<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Menampilkan daftar pesanan user
     */
    public function index()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)
                      ->orderBy('created_at', 'desc')
                      ->paginate(10);
                      
        return view('orders.index', compact('orders'));
    }

    /**
     * Menampilkan detail pesanan
     */
    public function show(Order $order)
    {
        // Pastikan user hanya bisa melihat pesanannya sendiri
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Load relations untuk tampilan detail
        $order->load('items.product');
        
        return view('orders.show', compact('order'));
    }
    /**
 * Regenerate payment token for order
 */
public function regeneratePayment(Order $order)
{
    // Pastikan user hanya bisa me-regenerate token pembayaran untuk pesanannya sendiri
    if ($order->user_id !== Auth::id()) {
        abort(403, 'Unauthorized action.');
    }
    
    // Pastikan order dalam status yang tepat untuk pembayaran ulang
    if (!in_array($order->status, ['pending', 'failed']) && 
        !in_array($order->payment_status, ['pending', 'failed', null])) {
        return redirect()->route('orders.show', $order->id)
            ->with('error', 'Pembayaran tidak dapat diproses ulang untuk pesanan ini.');
    }
    
    try {
        // Konfigurasi Midtrans
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');
        
        // Load items dari order
        $order->load('items.product');
        
        // Set up parameter pembayaran
        $params = [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => (int) $order->total_amount,
            ],
            'customer_details' => [
                'first_name' => $order->user->name,
                'email' => $order->user->email,
                'phone' => $order->user->phone,
            ],
            'item_details' => [],
            'callbacks' => [
                'finish' => route('payment.finish', $order->id),
            ]
        ];
        
        // Menambahkan detail item dari order
        foreach ($order->items as $item) {
            $params['item_details'][] = [
                'id' => $item->product_id,
                'price' => (int) $item->price,
                'quantity' => $item->quantity,
                'name' => substr($item->name, 0, 50),
            ];
        }
        
        // Tambahkan biaya pengiriman sebagai item
        $params['item_details'][] = [
            'id' => 'SHIPPING-' . $order->shipping_method,
            'price' => (int) $order->shipping_amount,
            'quantity' => 1,
            'name' => ucfirst($order->shipping_method) . ' Shipping',
        ];
        
        // Tambahkan pajak sebagai item
        $params['item_details'][] = [
            'id' => 'TAX-11%',
            'price' => (int) $order->tax_amount,
            'quantity' => 1,
            'name' => 'Tax 11%',
        ];
        
        // Jika ada diskon, tambahkan sebagai item negatif
        if ($order->discount_amount > 0) {
            $params['item_details'][] = [
                'id' => 'DISCOUNT',
                'price' => (int) -$order->discount_amount,
                'quantity' => 1,
                'name' => 'Discount',
            ];
        }
        
        // Dapatkan Snap Token dari Midtrans
        $snapToken = \Midtrans\Snap::getSnapToken($params);
        
        // Update order dengan Snap Token baru
        $order->payment_token = $snapToken;
        $order->save();
        
        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Siap melakukan pembayaran. Silakan klik tombol "Selesaikan Pembayaran".');
        
    } catch (\Exception $e) {
        return redirect()->route('orders.show', $order->id)
            ->with('error', 'Error saat memproses pembayaran: ' . $e->getMessage());
    }
}

public function completeOrder(Order $order)
{
    // Pastikan user hanya bisa menyelesaikan pesanannya sendiri
    if ($order->user_id !== Auth::id()) {
        abort(403, 'Unauthorized action.');
    }
    
    // Pastikan order dalam status yang tepat
    if ($order->status !== 'processing') {
        return redirect()->route('orders.show', $order->id)
            ->with('error', 'Status pesanan tidak dapat diubah.');
    }
    
    $order->status = 'completed';
    $order->save();
    
    return redirect()->route('orders.show', $order->id)
        ->with('success', 'Pesanan telah diselesaikan. Terima kasih telah berbelanja!');
}

public function cancel($id)
{
    // Cari order berdasarkan ID
    $order = Order::findOrFail($id);
    
    // Pastikan user hanya bisa membatalkan pesanannya sendiri
    if ($order->user_id !== Auth::id()) {
        abort(403, 'Unauthorized action.');
    }
    
    // Pastikan pesanan masih dapat dibatalkan (hanya pending atau processing)
    if (!in_array($order->status, ['pending', 'processing'])) {
        return redirect()->route('orders.show', $order->id)
            ->with('error', 'Pesanan dengan status ' . $order->status . ' tidak dapat dibatalkan.');
    }
    
    // Ubah status pesanan menjadi cancelled
    $order->status = 'cancelled';
    // Juga ubah payment_status menjadi cancelled
    $order->payment_status = 'cancelled';
    
    // Jika kolom cancelled_at sudah ada di database
    try {
        $order->cancelled_at = now();
    } catch (\Exception $e) {
        // Jika kolom tidak ada, abaikan error
    }
    
    $order->save();
    
    // Kembalikan stok produk
    foreach ($order->items as $item) {
        $product = $item->product;
        $product->stock += $item->quantity;
        $product->save();
    }
    
    return redirect()->route('orders.show', $order->id)
        ->with('success', 'Pesanan berhasil dibatalkan.');
}
}