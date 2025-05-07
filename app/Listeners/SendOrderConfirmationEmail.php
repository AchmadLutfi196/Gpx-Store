<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Mail\PaymentConfirmation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendOrderConfirmationEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(OrderPlaced $event): void
    {
        $order = $event->order;
        
        Log::info('Attempting to send payment confirmation email', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'email' => $order->email
        ]);
        
        try {
            Mail::to($order->email)->send(new PaymentConfirmation($order));
            
            Log::info('Payment confirmation email sent successfully', [
                'order_id' => $order->id
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send payment confirmation email', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Retry up to 3 times with increasing delay
            if ($this->attempts() < 3) {
                $this->release(30 * $this->attempts());
                return;
            }
        }
    }
}
