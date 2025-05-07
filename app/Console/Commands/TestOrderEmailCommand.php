<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Mail\PaymentConfirmation;
use Illuminate\Support\Facades\Mail;

class TestOrderEmailCommand extends Command
{
    protected $signature = 'order:test-email {order_id?} {--email=}';
    protected $description = 'Test order confirmation email for a specific order';

    public function handle()
    {
        $orderId = $this->argument('order_id');
        $customEmail = $this->option('email');
        
        if (!$orderId) {
            // If no order ID provided, list the most recent orders
            $orders = Order::latest()->take(10)->get();
            
            if ($orders->isEmpty()) {
                $this->error('No orders found in the database.');
                return 1;
            }
            
            $this->info('Recent orders:');
            
            $headers = ['ID', 'Order #', 'Customer', 'Total', 'Status', 'Date'];
            $rows = [];
            
            foreach ($orders as $order) {
                $rows[] = [
                    $order->id,
                    $order->order_number,
                    $order->name,
                    'Rp ' . number_format($order->total, 0, ',', '.'),
                    $order->status,
                    $order->created_at->format('Y-m-d H:i')
                ];
            }
            
            $this->table($headers, $rows);
            
            $orderId = $this->ask('Enter the ID of the order to send a test email for:');
        }
        
        try {
            $order = Order::findOrFail($orderId);
            
            // Use custom email if provided
            $emailTo = $customEmail ?: $order->email;
            
            $this->info("Sending test email for Order #{$order->order_number} to: {$emailTo}");
            
            Mail::to($emailTo)->send(new PaymentConfirmation($order));
            
            $this->info('Email sent successfully!');
            $this->info('Please check your inbox (and spam folder).');
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Error sending email: ' . $e->getMessage());
            return 1;
        }
    }
}
