<?php

namespace App\Http\Controllers;

use App\Events\OrderPlaced;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Mail\PaymentConfirmation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    public function callback(Request $request)
    {
        Log::info('Payment callback received', $request->all());
        
        $serverKey = config('midtrans.server_key');
        $hashed = hash("sha512", $request->order_id.$request->status_code.$request->gross_amount.$serverKey);
        
        if ($hashed == $request->signature_key) {
            if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                $order = Order::where('midtrans_order_id', $request->order_id)->first();
                
                if ($order) {
                    // Update order status
                    $order->payment_status = 'paid';
                    $order->status = 'processing';
                    $order->save();
                    
                    Log::info('Payment successful for order: ' . $order->order_number);
                    
                    // Send payment confirmation email
                    try {
                        Log::info('Sending payment confirmation email to: ' . $order->email);
                        Mail::to($order->email)->send(new PaymentConfirmation($order));
                        Log::info('Payment confirmation email sent successfully');
                    } catch (\Exception $e) {
                        Log::error('Failed to send payment confirmation email: ' . $e->getMessage(), [
                            'exception' => $e,
                            'order_id' => $order->id,
                            'email' => $order->email
                        ]);
                    }

                    // Fire event to send email
                    event(new OrderPlaced($order));
                } else {
                    Log::warning('Order not found for midtrans_order_id: ' . $request->order_id);
                }
            } else {
                Log::info('Payment status not completed: ' . $request->transaction_status);
            }
        } else {
            Log::warning('Invalid signature key for payment callback');
        }
        
        return response()->json(['success' => true]);
    }
}