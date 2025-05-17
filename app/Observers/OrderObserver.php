<?php

namespace App\Observers;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order)
{
    // Debug log untuk melihat perubahan status
    Log::info('Order updated', [
        'order_id' => $order->id,
        'old_status' => $order->getOriginal('status'),
        'new_status' => $order->status,
        'old_payment_status' => $order->getOriginal('payment_status'),
        'new_payment_status' => $order->payment_status
    ]);

    // Cek apakah status berubah menjadi cancelled atau payment_status berubah menjadi cancelled
    $statusChangedToCancelled = $order->isDirty('status') && 
                               $order->status === 'cancelled' && 
                               $order->getOriginal('status') !== 'cancelled';
                               
    $paymentStatusChangedToCancelled = $order->isDirty('payment_status') && 
                                      $order->payment_status === 'cancelled' && 
                                      $order->getOriginal('payment_status') !== 'cancelled';

    // Jika salah satu kondisi terpenuhi, cek status pembayaran SEBELUM diubah
    if (($statusChangedToCancelled || $paymentStatusChangedToCancelled)) {
        // PENTING: Periksa apakah order sebelumnya sudah dibayar (completed) atau belum
        $previouslyPaid = $order->getOriginal('payment_status') === 'completed';
        
        if ($previouslyPaid) {
            Log::info('Mengembalikan stok untuk order #' . $order->id . ' (sudah dibayar sebelumnya)');
            
            // Atur cancelled_at jika belum diset
            if (empty($order->cancelled_at)) {
                $order->cancelled_at = now();
                // Gunakan saveQuietly untuk menghindari trigger observer lagi
                $order->saveQuietly(); 
            }
            
            // Kembalikan stok untuk setiap item pesanan
            foreach ($order->items as $item) {
                $product = $item->product;
                if ($product) {
                    Log::info('Mengembalikan stok produk', [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'old_stock' => $product->stock,
                        'quantity_to_return' => $item->quantity,
                        'new_stock' => $product->stock + $item->quantity
                    ]);
                    
                    // Tambah stok produk
                    $product->stock += $item->quantity;
                    $product->save();
                }
            }
            
            Log::info('Stok berhasil dikembalikan untuk order #' . $order->id);
        } else {
            // Jika order belum dibayar, tidak perlu mengembalikan stok
            Log::info('Order #' . $order->id . ' dibatalkan tetapi belum dibayar, tidak perlu mengembalikan stok');
        }
    }
}
}