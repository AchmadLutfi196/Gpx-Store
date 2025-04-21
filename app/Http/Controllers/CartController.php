<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\CartItem;
use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * Display the cart page
     */
    public function index()
    {
        $cartItems = $this->getCartItems();
        
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
        
        $shipping = $cartItems->count() > 0 ? 10000 : 0; // Default shipping cost
        $tax = round($subtotal * 0.11); // 11% tax rate
        $total = $subtotal + $shipping + $tax - $discount;
        
        return view('cart', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'shipping' => $shipping,
            'tax' => $tax,
            'total' => $total,
        ]);
    }
    
    /**
     * Add an item to the cart.
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
        
        $productId = $request->product_id;
        $quantity = $request->quantity;
        
        // Check if product exists and has stock
        $product = Product::findOrFail($productId);
        
        if (isset($product->stock) && $product->stock < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough stock available',
                'available_stock' => $product->stock
            ], 422);
        }
        
        $sessionId = Session::getId();
        $userId = Auth::id();
        
        // Find existing cart item
        $cartItem = CartItem::where(function ($query) use ($sessionId, $userId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId);
            }
        })->where('product_id', $productId)->first();
        
        if ($cartItem) {
            // Update quantity if item already exists
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            // Create new cart item
            CartItem::create([
                'user_id' => $userId,
                'session_id' => $userId ? null : $sessionId,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }
        
        // Get updated cart count for response
        $cartCount = $this->getCartItems()->sum('quantity');
        
        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully',
            'cart_count' => $cartCount
        ]);
    }
    
    /**
     * Update cart item quantity
     */
        public function updateCart(Request $request)
        {
            try {
                // Validate request for cart_item_id
                $validator = Validator::make($request->all(), [
                    'cart_item_id' => 'required|exists:cart_items,id',
                    'quantity' => 'required|integer|min:1',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validasi gagal',
                        'errors' => $validator->errors()
                    ], 422);
                }

                $cartItemId = $request->input('cart_item_id');
                $quantity = (int) $request->input('quantity');
                
                // Find the cart item
                $cartItem = CartItem::with('product')->findOrFail($cartItemId);
                
                // Verify the cart item belongs to the current user/session
                if (!$this->verifyCartItemOwnership($cartItem)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized'
                    ], 403);
                }
                
                // Get product to check stock
                $product = $cartItem->product;
                
                // Validate against stock
                if ($product->stock && $quantity > $product->stock) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stok tidak cukup. Maksimal {$product->stock} item",
                        'available_stock' => $product->stock
                    ], 422);
                }
                
                // Update quantity
                $cartItem->quantity = $quantity;
                $cartItem->save();
                
                // Get updated cart totals
                $cartItems = $this->getCartItems();
                $cartCount = $cartItems->sum('quantity');
                $cartTotal = 0;
                
                foreach ($cartItems as $item) {
                    $price = $item->product->discount_price ?? $item->product->price;
                    $cartTotal += $price * $item->quantity;
                }
                
                return response()->json([
                    'success' => true,
                    'message' => 'Keranjang berhasil diperbarui',
                    'cart_count' => $cartCount,
                    'cart_total' => $cartTotal,
                    'cart_total_formatted' => 'Rp ' . number_format($cartTotal, 0, ',', '.')
                ]);
            } catch (\Exception $e) {
                Log::error('Cart Update Error', ['error' => $e->getMessage()]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ], 500);
            }
        }
    
    /**
     * Remove an item from the cart
     */
    public function removeFromCart(Request $request)
    {
        $request->validate([
            'cart_item_id' => 'required|exists:cart_items,id',
        ]);
        
        $cartItemId = $request->cart_item_id;
        
        $cartItem = CartItem::findOrFail($cartItemId);
        
        // Verify the cart item belongs to the current user/session
        if (!$this->verifyCartItemOwnership($cartItem)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $cartItem->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'cart_count' => $this->getCartItems()->sum('quantity')
        ]);
    }
    
    /**
     * Clear all items from the cart
     */
    public function clearCart()
    {
        $sessionId = Session::getId();
        $userId = Auth::id();
        
        if ($userId) {
            CartItem::where('user_id', $userId)->delete();
        } else {
            CartItem::where('session_id', $sessionId)->delete();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully'
        ]);
    }
    
    /**
     * Get all cart items for the current user/session
     */
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
    
    /**
     * Verify a cart item belongs to the current user/session
     */
    private function verifyCartItemOwnership($cartItem)
    {
        $sessionId = Session::getId();
        $userId = Auth::id();
        
        if ($userId && $cartItem->user_id == $userId) {
            return true;
        }
        
        if (!$userId && $cartItem->session_id == $sessionId) {
            return true;
        }
        
        return false;
    }
    
    
    public function add(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $productId = $request->input('product_id');
        $quantity = (int) $request->input('quantity', 1);
        $product = \App\Models\Product::findOrFail($productId);

        // Ambil keranjang saat ini
        $cart = session('cart', []);
        
        // Hitung jumlah produk yang sudah ada di keranjang (jika ada)
        $cartQuantity = isset($cart[$productId]) ? $cart[$productId]['quantity'] : 0;
        
        // Hitung total yang akan ada di keranjang
        $totalQuantity = $cartQuantity + $quantity;
        
        // Check stock - pastikan total tidak melebihi stok
        if ($product->stock < $totalQuantity) {
            // Jika stok tidak cukup, beri pesan error yang jelas
            $availableToAdd = $product->stock - $cartQuantity;
            
            if ($availableToAdd <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk ini sudah ada di keranjang dengan jumlah maksimal yang tersedia',
                    'remaining_stock' => 0
                ], 422);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Stok tidak cukup. Anda hanya dapat menambahkan {$availableToAdd} lagi ke keranjang",
                    'remaining_stock' => $availableToAdd
                ], 422);
            }
        }

        // Initialize cart if not exists
        if (!session()->has('cart')) {
            session(['cart' => []]);
        }

        // Check if product already in cart
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $totalQuantity;
        } else {
            $cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
                'image' => $product->featured_image ?? '',
                'stock' => $product->stock,
            ];
        }

        // Save cart back to session
        session(['cart' => $cart]);

        // Calculate cart total items
        $cartCount = array_sum(array_column($cart, 'quantity'));
        
        // Calculate remaining stock available to add
        $remainingStock = $product->stock - $cart[$productId]['quantity'];

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan ke keranjang',
            'cart_count' => $cartCount,
            'remaining_stock' => $remainingStock
        ]);
        
    } catch (\Exception $e) {
        Log::error('Cart Add Error', ['error' => $e->getMessage()]);
        
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}
 
}