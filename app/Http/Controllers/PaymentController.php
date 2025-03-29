<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Midtrans\Notification;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function notification(Request $request)
    {
        $notification = new Notification();
        
        $transaction = $notification->transaction_status;
        $type = $notification->payment_type;
        $order_id = $notification->order_id;
        $fraud = $notification->fraud_status;
        
        $order = Order::where('order_number', $order_id)->first();
        
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        
        // Handle berbagai status transaksi
        if ($transaction == 'capture') {
            if ($fraud == 'challenge') {
                $order->status = 'challenge';
                $order->payment_status = 'challenge';
            } else if ($fraud == 'accept') {
                $order->status = 'processing';
                $order->payment_status = 'completed';
            }
        } else if ($transaction == 'settlement') {
            $order->status = 'processing';
            $order->payment_status = 'completed';
        } else if ($transaction == 'pending') {
            $order->status = 'pending';
            $order->payment_status = 'pending';
        } else if ($transaction == 'deny') {
            $order->status = 'failed';
            $order->payment_status = 'failed';
        } else if ($transaction == 'expire') {
            $order->status = 'expired';
            $order->payment_status = 'expired';
        } else if ($transaction == 'cancel') {
            $order->status = 'cancelled';
            $order->payment_status = 'cancelled';
        }
        
        $order->payment_details = json_encode($notification->getResponse());
        $order->save();
        
        // Bisa menambahkan logika bisnis lainnya di sini, seperti mengirim email notifikasi
        
        return response()->json(['message' => 'Payment notification processed']);
    }
    public function show(Order $order)
{
    // Pastikan user hanya bisa melihat ordernya sendiri
    if ($order->user_id !== Auth::id()) {
        abort(403, 'Unauthorized action');
    }
    
    // Jika order sudah dibayar, redirect ke halaman detail order
    if ($order->payment_status === 'completed') {
        return redirect()->route('orders.show', $order->id)
            ->with('info', 'This order has already been paid.');
    }
    
    // Jika order sudah memiliki token pembayaran, gunakan token yang ada
    if ($order->payment_token) {
        return view('payment', [
            'snapToken' => $order->payment_token,
            'order' => $order,
            'client_key' => Config::$clientKey
        ]);
    }
    
    // Jika tidak ada token, buat token pembayaran baru
    try {
        $order->load('items.product');
        
        $params = [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => (int) $order->total_amount,
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'phone' => Auth::user()->phone,
            ],
            'item_details' => [],
            'callbacks' => [
                'finish' => route('payment.finish', $order->id),
            ]
        ];
        
        // Menambahkan detail item untuk Midtrans dari order items
        foreach ($order->items as $item) {
            $params['item_details'][] = [
                'id' => $item->product_id,
                'price' => (int) $item->price,
                'quantity' => $item->quantity,
                'name' => substr($item->product->name, 0, 50),
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
        $snapToken = Snap::getSnapToken($params);
        
        // Update order dengan Snap Token
        $order->payment_token = $snapToken;
        $order->save();
        
        return view('payment', [
            'snapToken' => $snapToken,
            'order' => $order,
            'client_key' => Config::$clientKey
        ]);
        
    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Error processing payment: ' . $e->getMessage());
    }
}
}