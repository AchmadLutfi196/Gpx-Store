<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

class CheckoutController extends Controller
{
    public function index()
    {
        $cartItems = $this->getCartItems();
        
        if ($cartItems->count() === 0) {
            return redirect()->route('shop')->with('error', 'Your cart is empty.');
        }
        
        // Calculate cart totals
        $subtotal = 0;
        $discount = 0;
        
        foreach ($cartItems as $item) {
            $price = $item->product->discount_price ?? $item->product->price;
            $subtotal += $price * $item->quantity;
            
            // Calculate discount if applicable
            if ($item->product->discount_price && $item->product->discount_price < $item->product->price) {
                $discount += ($item->product->price - $item->product->discount_price) * $item->quantity;
            }
        }
        
        $shipping = 10000; // Default shipping cost
        $tax = round($subtotal * 0.11); // 11% tax rate
        $total = $subtotal + $shipping + $tax - $discount;
        
        return view('checkout', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'shipping' => $shipping,
            'tax' => $tax,
            'total' => $total,
            'user' => Auth::user(),
            'addresses' => Auth::user() ? Auth::user()->addresses()->orderBy('is_default', 'desc')->get() : [],
        ]);
    }
    
    public function process(Request $request)
    {
        // Add debug logging
        Log::info('Checkout process started', ['request' => $request->all()]);
        
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'address' => 'required|string|max:255',
                'city' => 'required|string|max:100',
                'state' => 'required|string|max:100',
                'zipcode' => 'required|string|max:20',
                'notes' => 'nullable|string|max:500',
            ]);
            
            $cartItems = $this->getCartItems();
            
            if ($cartItems->count() === 0) {
                Log::warning('Checkout attempted with empty cart');
                return redirect()->route('shop')->with('error', 'Your cart is empty.');
            }
            
            // Calculate cart totals
            $subtotal = 0;
            $discount = 0;
            
            foreach ($cartItems as $item) {
                $price = $item->product->discount_price ?? $item->product->price;
                $subtotal += $price * $item->quantity;
                
                if ($item->product->discount_price && $item->product->discount_price < $item->product->price) {
                    $discount += ($item->product->price - $item->product->discount_price) * $item->quantity;
                }
            }
            
            $shipping = 10000;
            $tax = round($subtotal * 0.11);
            $total = $subtotal + $shipping + $tax - $discount;
            
            // Generate order number
            $orderNumber = 'ORD-' . strtoupper(Str::random(10));
            
            Log::info('Creating order', [
                'order_number' => $orderNumber,
                'total' => $total,
            ]);
            
            // Create order with try/catch to catch any database errors
            try {
                $order = Order::create([
                    'user_id' => Auth::id(),
                    'order_number' => $orderNumber,
                    'status' => 'pending',
                    'total_amount' => $total,
                    'shipping_address' => $request->address,
                    'shipping_city' => $request->city,
                    'shipping_state' => $request->state,
                    'shipping_zipcode' => $request->zipcode,
                    'shipping_phone' => $request->phone,
                    'notes' => $request->notes,
                    'payment_status' => 'pending',
                    'shipping_cost' => $shipping,
                    'tax_amount' => $tax,
                    'discount_amount' => $discount,
                ]);
                
                Log::info('Order created', ['order_id' => $order->id]);
            } catch (\Exception $e) {
                Log::error('Error creating order', ['error' => $e->getMessage()]);
                return redirect()->back()->with('error', 'There was an error creating your order: ' . $e->getMessage());
            }
            
            // Create order items
            try {
                foreach ($cartItems as $item) {
                    $price = $item->product->discount_price ?? $item->product->price;
                    
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'name' => $item->product->name,
                        'price' => $price,
                        'quantity' => $item->quantity,
                        'subtotal' => $price * $item->quantity,
                    ]);
                }
                
                Log::info('Order items created for order', ['order_id' => $order->id]);
            } catch (\Exception $e) {
                Log::error('Error creating order items', ['error' => $e->getMessage()]);
                return redirect()->back()->with('error', 'There was an error creating your order items: ' . $e->getMessage());
            }
            
            // Pastikan paket Midtrans tersedia
            if (!class_exists('\\Midtrans\\Config')) {
                Log::error('Midtrans package not installed');
                return redirect()->back()->with('error', 'Payment gateway integration is not available. Please try again later or contact support.');
            }
            
            // Setup Midtrans configuration
            try {
                Config::$serverKey = config('midtrans.server_key');
                Config::$isProduction = config('midtrans.is_production');
                Config::$isSanitized = true;
                Config::$is3ds = true;
                
                Log::info('Midtrans configuration set', [
                    'is_production' => config('midtrans.is_production'),
                    'server_key_exists' => !empty(config('midtrans.server_key')),
                ]);
                
                // Prepare Midtrans transaction data
                $transactionDetails = [
                    'order_id' => $order->order_number,
                    'gross_amount' => (int) $total,
                ];
                
                // Customer details
                $customerDetails = [
                    'first_name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'billing_address' => [
                        'address' => $request->address,
                        'city' => $request->city,
                        'postal_code' => $request->zipcode,
                        'country_code' => 'IDN',
                    ],
                    'shipping_address' => [
                        'address' => $request->address,
                        'city' => $request->city,
                        'postal_code' => $request->zipcode,
                        'country_code' => 'IDN',
                    ],
                ];
                
                // Item details for Midtrans
                $itemDetails = [];
                
                foreach ($cartItems as $item) {
                    $price = $item->product->discount_price ?? $item->product->price;
                    
                    $itemDetails[] = [
                        'id' => $item->product_id,
                        'price' => (int) $price,
                        'quantity' => $item->quantity,
                        'name' => substr($item->product->name, 0, 50), // Midtrans has 50 chars limit
                    ];
                }
                
                // Add shipping, tax, and discount as separate items
                if ($shipping > 0) {
                    $itemDetails[] = [
                        'id' => 'SHIPPING',
                        'price' => (int) $shipping,
                        'quantity' => 1,
                        'name' => 'Shipping Cost',
                    ];
                }
                
                if ($tax > 0) {
                    $itemDetails[] = [
                        'id' => 'TAX',
                        'price' => (int) $tax,
                        'quantity' => 1,
                        'name' => 'Tax (11%)',
                    ];
                }
                
                if ($discount > 0) {
                    $itemDetails[] = [
                        'id' => 'DISCOUNT',
                        'price' => -(int) $discount,
                        'quantity' => 1,
                        'name' => 'Discount',
                    ];
                }
                
                // Create Midtrans transaction parameters
                $params = [
                    'transaction_details' => $transactionDetails,
                    'customer_details' => $customerDetails,
                    'item_details' => $itemDetails,
                ];
                
                Log::info('Preparing to get Snap Token', ['params' => $params]);
                
                // Get Snap Token
                $snapToken = Snap::getSnapToken($params);
                Log::info('Snap token obtained', ['snap_token' => $snapToken]);
                
                // Update order with the Snap Token
                $order->update(['snap_token' => $snapToken]);
                
                // Clear cart after successful order creation
                $this->clearCart();
                
                Log::info('Redirecting to payment page', ['order_id' => $order->id]);
                
                return view('payment', [
                    'snapToken' => $snapToken,
                    'order' => $order,
                    'clientKey' => config('midtrans.client_key'),
                ]);
                
            } catch (\Exception $e) {
                Log::error('Error processing Midtrans payment', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                return redirect()->back()->with('error', 'Error processing your payment: ' . $e->getMessage());
            }
            
        } catch (\Exception $e) {
            Log::error('Unexpected error in checkout process', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }
    
    // Other methods remain the same
    public function callback(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        $hashed = hash("sha512", $request->order_id.$request->status_code.$request->gross_amount.$serverKey);
        
        if ($hashed == $request->signature_key) {
            $order = Order::where('order_number', $request->order_id)->first();
            
            if ($order) {
                // Update the order based on the transaction status
                switch ($request->transaction_status) {
                    case 'capture':
                    case 'settlement':
                        $order->payment_status = 'paid';
                        $order->status = 'processing';
                        $order->transaction_id = $request->transaction_id;
                        break;
                    case 'deny':
                    case 'cancel':
                    case 'failure':
                        $order->payment_status = 'failed';
                        break;
                    case 'expire':
                        $order->payment_status = 'expired';
                        break;
                    case 'pending':
                        $order->payment_status = 'pending';
                        break;
                }
                
                $order->save();
                
                return response()->json(['success' => true]);
            }
        }
        
        return response()->json(['success' => false], 401);
    }
    
    public function finish(Request $request)
    {
        if ($request->transaction_status == 'settlement' || $request->transaction_status == 'capture') {
            $status = 'success';
            $message = 'Your payment has been successfully processed!';
        } elseif ($request->transaction_status == 'pending') {
            $status = 'pending';
            $message = 'Your payment is being processed. Please complete your payment according to the instructions provided.';
        } else {
            $status = 'failed';
            $message = 'Payment failed or was cancelled. Please try again.';
        }
        
        return view('payment-finish', [
            'status' => $status,
            'message' => $message,
            'order_id' => $request->order_id,
            'transaction_status' => $request->transaction_status,
        ]);
    }
    
    public function unfinish(Request $request)
    {
        return view('payment-unfinish', [
            'order_id' => $request->order_id,
            'transaction_status' => $request->transaction_status,
        ]);
    }
    
    public function error(Request $request)
    {
        return view('payment-error', [
            'order_id' => $request->order_id,
            'transaction_status' => $request->transaction_status,
        ]);
    }
    
    private function getCartItems()
    {
        $sessionId = Session::getId();
        $userId = Auth::id();
        
        return CartItem::with('product')
            ->where(function ($query) use ($sessionId, $userId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })
            ->latest()
            ->get();
    }
    
    private function clearCart()
    {
        $sessionId = Session::getId();
        $userId = Auth::id();
        
        if ($userId) {
            CartItem::where('user_id', $userId)->delete();
        } else {
            CartItem::where('session_id', $sessionId)->delete();
        }
    }

}