<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class PaymentConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $order;
    public $debug;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, $debug = false)
    {
        $this->order = $order;
        $this->debug = $debug;
        
        // Log when email object is created
        Log::info('PaymentConfirmation email created', [
            'order' => $order->order_number,
            'to' => $order->email
        ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Confirmation - Order #' . $this->order->order_number,
            metadata: [
                // Adding headers to improve deliverability
                'X-Priority' => '1',
                'X-MSMail-Priority' => 'High',
            ]
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Ensure the order items are available
        try {
            if (!isset($this->order->items) || $this->order->items->isEmpty()) {
                Log::warning('PaymentConfirmation: Order has no items', [
                    'order_id' => $this->order->id,
                    'order_number' => $this->order->order_number
                ]);
                
                // Make sure we have at least an empty collection
                $this->order->items = $this->order->items ?? collect();
            }
            
            return new Content(
                view: 'emails.payment-confirmation',
                with: [
                    'debug' => $this->debug,
                ]
            );
        } catch (Throwable $e) {
            Log::error('PaymentConfirmation content error', [
                'exception' => $e->getMessage(),
                'order_id' => $this->order->id ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            
            // Fallback to a simple view
            return new Content(
                view: 'emails.payment-confirmation-simple',
                with: [
                    'orderNumber' => $this->order->order_number ?? 'N/A',
                    'customerName' => $this->order->name ?? 'Customer',
                    'amount' => $this->order->total_amount ?? 0,
                ]
            );
        }
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Handle a failed sending attempt.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(Throwable $exception)
    {
        Log::error('Failed to send payment confirmation email', [
            'order_id' => $this->order->id ?? 'unknown',
            'order_number' => $this->order->order_number ?? 'unknown',
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
