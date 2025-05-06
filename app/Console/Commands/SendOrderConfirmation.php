<?php

namespace App\Console\Commands;

use App\Mail\PaymentConfirmation;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendOrderConfirmation extends Command
{
    protected $signature = 'order:send-confirmation {order_number}';
    protected $description = 'Send an order confirmation email for a specific order';

    public function handle()
    {
        $orderNumber = $this->argument('order_number');
        
        $order = Order::where('order_number', $orderNumber)->first();
        
        if (!$order) {
            $this->error("Order with number {$orderNumber} not found!");
            return 1;
        }
        
        $this->info("Sending order confirmation email for Order #{$orderNumber}");
        $this->info("Email will be sent to: {$order->email}");
        
        if ($this->confirm("Do you want to proceed?", true)) {
            try {
                Mail::to($order->email)->send(new PaymentConfirmation($order));
                $this->info("Email sent successfully!");
                Log::info("Order confirmation email sent manually via command", [
                    'order_number' => $orderNumber,
                    'email' => $order->email
                ]);
                return 0;
            } catch (\Exception $e) {
                $this->error("Failed to send email: {$e->getMessage()}");
                Log::error("Failed to send order confirmation email via command", [
                    'order_number' => $orderNumber,
                    'error' => $e->getMessage()
                ]);
                return 1;
            }
        }
        
        $this->info("Operation cancelled.");
        return 0;
    }
}
