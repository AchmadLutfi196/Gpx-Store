<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\Address;
use App\Models\PromoCode;
use App\Models\Product;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct()
    {
        // Konfigurasi Midtrans
        MidtransConfig::$serverKey = config('midtrans.server_key');
        MidtransConfig::$isProduction = config('midtrans.is_production');
        MidtransConfig::$isSanitized = config('midtrans.is_sanitized');
        MidtransConfig::$is3ds = config('midtrans.is_3ds');
    }

    public function index()
    {
        $user = Auth::user();
        $addresses = Address::where('user_id', $user->id)->get();
        $cartItems = CartItem::where('user_id', $user->id)->with('product')->get();
        
        if($cartItems->count() == 0) {
            return redirect()->route('cart')->with('error', 'Keranjang Anda kosong');
        }
        
        // Hitung subtotal
        $subtotal = $cartItems->sum(function ($item) {
            $price = $item->product->discount_price && $item->product->discount_price < $item->product->price 
                ? $item->product->discount_price 
                : $item->product->price;
            return $price * $item->quantity;
        });
        
        $shipping = 10000; // Default shipping (Regular)
        $tax = $subtotal * 0.11; // 11% tax
        $discount = 0; // Default discount
        $appliedPromo = null;
        
        // Jika ada kode kupon yang diterapkan, hitung diskon
        if (Session::has('applied_promo')) {
            $appliedPromo = Session::get('applied_promo');
            $discount = $appliedPromo['discount_amount'];
        }
        
        $total = $subtotal + $shipping + $tax - $discount;
        
        return view('checkout', compact(
            'user', 
            'addresses', 
            'cartItems', 
            'subtotal', 
            'shipping', 
            'tax', 
            'discount', 
            'total',
            'appliedPromo'
        ));
    }
    
    public function process(Request $request)
    {
        // Validasi input form checkout
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:15',
        ]);
        
        $user = Auth::user();
        $cartItems = CartItem::where('user_id', $user->id)->with('product')->get();
        
        if($cartItems->count() == 0) {
            return redirect()->route('cart')->with('error', 'Keranjang Anda kosong');
        }
        
        // Periksa ketersediaan stok
        foreach ($cartItems as $item) {
            if ($item->product->stock < $item->quantity) {
                return redirect()->route('cart')->with('error', "Stok tidak cukup untuk produk {$item->product->name}. Tersedia: {$item->product->stock}, Dibutuhkan: {$item->quantity}");
            }
        }
        
        // Hitung total seperti di method index
        $subtotal = $cartItems->sum(function ($item) {
            $price = $item->product->discount_price && $item->product->discount_price < $item->product->price 
                ? $item->product->discount_price 
                : $item->product->price;
            return $price * $item->quantity;
        });
        
        // Ambil metode pengiriman yang dipilih
        $shipping_method = $request->shipping_method;
        
        // Set biaya pengiriman berdasarkan metode
        $shipping_cost = 10000; // Default: Regular
        if ($shipping_method === 'express') {
            $shipping_cost = 25000;
        } elseif ($shipping_method === 'same_day') {
            $shipping_cost = 50000;
        }
        
        $tax = $subtotal * 0.11;
        $discount = 0;
        $promo_id = null;
        
        // Jika ada kode kupon yang diterapkan
        if (Session::has('applied_promo')) {
            $appliedPromo = Session::get('applied_promo');
            $discount = $appliedPromo['discount_amount'];
            $promo_id = $appliedPromo['id'];
            
            // Increment usage pada promo code
            $promoCode = PromoCode::find($promo_id);
            if ($promoCode) {
                $promoCode->incrementUsage();
            }
        }
        
        $total = $subtotal + $shipping_cost + $tax - $discount;
        
        // Buat order baru
        $order = new Order();
        $order->user_id = $user->id;
        $order->order_number = 'ORD-' . strtoupper(Str::random(10));
        $order->status = 'pending';
        $order->total_amount = $total;
        $order->shipping_amount = $shipping_cost;
        $order->tax_amount = $tax;
        $order->discount_amount = $discount;
        $order->promo_code_id = $promo_id;
        $order->shipping_method = $shipping_method;
        
        // Atur alamat pengiriman
        if ($request->has('address_id')) {
            // Jika memilih alamat yang sudah ada
            $address = Address::findOrFail($request->address_id);
            $order->shipping_address = json_encode([
                'recipient_name' => $address->recipient_name,
                'phone' => $address->phone,
                'address_line1' => $address->address_line1,
                'address_line2' => $address->address_line2 ?? '',
                'city' => $address->city,
                'province' => $address->province,
                'postal_code' => $address->postal_code,
            ]);
            $order->shipping_postal_code = $address->postal_code;
            $order->shipping_phone = $address->phone;
        } else {
            // Jika menggunakan alamat baru
            $order->shipping_address = json_encode([
                'recipient_name' => $request->recipient_name,
                'phone' => $request->recipient_phone,
                'address_line1' => $request->address_line1,
                'address_line2' => $request->address_line2 ?? '',
                'city' => $request->city,
                'province' => $request->province,
                'postal_code' => $request->postal_code,
            ]);
            $order->shipping_postal_code = $request->postal_code;
            $order->shipping_phone = $request->recipient_phone;

            // Jika user ingin menyimpan alamat
            if ($request->has('save_address') && $request->save_address) {
                $newAddress = new Address();
                $newAddress->user_id = $user->id;
                $newAddress->recipient_name = $request->recipient_name;
                $newAddress->phone = $request->recipient_phone;
                $newAddress->address_line1 = $request->address_line1;
                $newAddress->address_line2 = $request->address_line2 ?? null;
                $newAddress->city = $request->city;
                $newAddress->province = $request->province;
                $newAddress->postal_code = $request->postal_code;
                $newAddress->is_default = $request->has('set_as_default') ? true : false;
                $newAddress->save();
                
                // Jika set sebagai default, update alamat lain
                if ($request->has('set_as_default')) {
                    Address::where('user_id', $user->id)
                        ->where('id', '!=', $newAddress->id)
                        ->update(['is_default' => false]);
                }
            }
        }
        
        // Tambahkan catatan pesanan jika ada
        $order->notes = $request->notes;
        $order->save();
        
        // Simpan item pesanan
        foreach ($cartItems as $item) {
            $price = $item->product->discount_price && $item->product->discount_price < $item->product->price 
                ? $item->product->discount_price 
                : $item->product->price;
            
            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $item->product_id;
            $orderItem->name = $item->product->name; // Tambahkan nama produk
            $orderItem->quantity = $item->quantity;
            $orderItem->price = $price;
            $orderItem->subtotal = $price * $item->quantity; // Tambahkan subtotal
            $orderItem->save();
        }
        
        // Set up Midtrans parameter
        $params = [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => (int) $total,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
            'item_details' => [],
            'callbacks' => [
                'finish' => route('payment.finish', $order->id),
            ]
        ];
        
        // Menambahkan detail item untuk Midtrans
        foreach ($cartItems as $item) {
            $price = $item->product->discount_price && $item->product->discount_price < $item->product->price 
                ? $item->product->discount_price 
                : $item->product->price;
            
            $params['item_details'][] = [
                'id' => $item->product_id,
                'price' => (int) $price,
                'quantity' => $item->quantity,
                'name' => substr($item->product->name, 0, 50),
            ];
        }
        
        // Tambahkan biaya pengiriman sebagai item
        $params['item_details'][] = [
            'id' => 'SHIPPING-' . $shipping_method,
            'price' => (int) $shipping_cost,
            'quantity' => 1,
            'name' => ucfirst($shipping_method) . ' Shipping',
        ];
        
        // Tambahkan pajak sebagai item
        $params['item_details'][] = [
            'id' => 'TAX-11%',
            'price' => (int) $tax,
            'quantity' => 1,
            'name' => 'Tax 11%',
        ];
        
        // Jika ada diskon, tambahkan sebagai item negatif
        if ($discount > 0) {
            $params['item_details'][] = [
                'id' => 'DISCOUNT',
                'price' => (int) -$discount,
                'quantity' => 1,
                'name' => 'Discount',
            ];
        }
        
        try {
            // Dapatkan Snap Token dari Midtrans
            $snapToken = Snap::getSnapToken($params);
            
            // Update order dengan Snap Token
            $order->payment_token = $snapToken;
            $order->save();
            
            // Hapus item dari keranjang dan clear promo code
            CartItem::where('user_id', $user->id)->delete();
            Session::forget('applied_promo');
            
            // Response dengan token untuk frontend
            return view('payment', [
                'snapToken' => $snapToken,
                'order' => $order,
                'client_key' => config('midtrans.client_key')
            ]);
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error processing payment: ' . $e->getMessage());
        }
    }
    
    public function finish(Request $request, $orderId)
    {
        // Handle callback setelah pembayaran
        $order = Order::with('items.product')->findOrFail($orderId);
        
        // Update status order berdasarkan status transaksi
        if ($request->transaction_status === 'capture' || $request->transaction_status === 'settlement') {
            $order->status = 'processing';
            $order->payment_status = 'completed';
            
            // Kurangi stok produk setelah pembayaran berhasil
            $this->reduceProductStock($order);
        } elseif ($request->transaction_status === 'pending') {
            $order->status = 'pending';
            $order->payment_status = 'pending';
        } else {
            $order->status = 'failed';
            $order->payment_status = 'failed';
        }
        
        $order->payment_details = json_encode($request->all());
        $order->save();
        
        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Order placed successfully! Order ID: ' . $order->order_number);
    }
    
    /**
     * Reduce product stock based on order items
     */
    private function reduceProductStock(Order $order)
    {
        foreach ($order->items as $item) {
            // Gunakan transaksi database untuk menghindari race condition
            DB::transaction(function() use ($item) {
                $product = Product::find($item->product_id);
                
                if ($product) {
                    // Kurangi stok dan simpan
                    $product->stock = max(0, $product->stock - $item->quantity);
                    $product->save();
                    
                    // Opsional: log perubahan stok
                    Log::info("Stok dikurangi untuk produk ID {$product->id} ({$product->name}). Jumlah: -{$item->quantity}. Stok baru: {$product->stock}");
                }
            });
        }
    }
}