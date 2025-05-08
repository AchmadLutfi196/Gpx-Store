<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Mail\ContactFormSubmission;
use App\Mail\PaymentConfirmation;
use App\Models\Order;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Artisan;

class TestEmailTypes extends Command
{
    protected $signature = 'mail:test {email} {--type=all}';
    protected $description = 'Test different email types';

    public function handle()
    {
        $email = $this->argument('email');
        $type = $this->option('type');
        
        $this->info("Sending test email(s) to: {$email}");
        
        try {
            if ($type === 'all' || $type === 'simple') {
                $this->sendSimpleEmail($email);
            }
            
            if ($type === 'all' || $type === 'contact') {
                $this->sendContactEmail($email);
            }
            
            if ($type === 'all' || $type === 'payment') {
                $this->sendPaymentEmail($email);
            }
            
            $this->info("Test emails have been queued. Run 'php artisan queue:work' to process them.");
            
            if ($this->confirm('Would you like to process queued emails now?', true)) {
                $this->processQueue();
            }
            
            $this->info("\nPlease check your inbox (including spam folder) at: {$email}");
            $this->info("If no emails are received, please review these troubleshooting steps:");
            $this->line("1. Check Laravel logs at: storage/logs/laravel.log");
            $this->line("2. Verify your mail configuration in .env file");
            $this->line("3. Make sure your mail service provider allows sending from your application");
            $this->line("4. If using Gmail, confirm you've enabled 'Less secure apps' or created an App Password");
            
        } catch (\Exception $e) {
            $this->error('Error sending test emails: ' . $e->getMessage());
            Log::error('Test email error: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
    
    protected function sendSimpleEmail($email)
    {
        $this->info("Queuing simple test email...");
        Mail::raw("This is a simple test email from GPX Store sent at ".now(), function($message) use ($email) {
            $message->to($email)
                    ->subject('GPX Store Simple Test Email');
        });
        $this->info("✓ Simple email queued");
    }
    
    protected function sendContactEmail($email)
    {
        $this->info("Queuing contact form test email...");
        
        // Try to find an existing contact message or create a new one
        $contactMessage = ContactMessage::first();
        
        if (!$contactMessage) {
            $this->warn("No existing contact messages found, creating a new one...");
            
            // Create a new ContactMessage model instance
            $contactMessage = new ContactMessage();
            $contactMessage->name = 'Test User';
            $contactMessage->email = $email;
            $contactMessage->subject = 'Test Contact Form';
            $contactMessage->message = 'This is a test message from the contact form.';
            $contactMessage->created_at = now();
            
            try {
                $contactMessage->save();
                $this->info("Created new contact message record in database.");
            } catch (\Exception $e) {
                $this->warn("Could not save contact message to database: " . $e->getMessage());
                $this->warn("Using in-memory model instance instead.");
            }
        } else {
            $this->info("Using existing contact message from database.");
            // Override the email to send the test to the specified address
            $contactMessage->email = $email;
        }
        
        Mail::to($email)->queue(new ContactFormSubmission($contactMessage));
        $this->info("✓ Contact form email queued");
    }
    
    protected function sendPaymentEmail($email)
    {
        $this->info("Queuing payment confirmation test email...");
        // Try to find a real order or create a mock one
        $order = Order::first();
        
        if (!$order) {
            $this->warn("No real orders found, creating a mock order...");
            $order = new Order();
            $order->id = 999;
            $order->order_number = 'TEST123456';
            $order->name = 'Test Customer';
            $order->email = $email;
            $order->phone = '123456789';
            $order->address = '123 Test Street, Test City';
            $order->subtotal = 100000;
            $order->tax = 11000;
            $order->shipping_cost = 10000;
            $order->discount = 0;
            $order->total = 121000;
            $order->status = 'processing';
            $order->payment_status = 'paid';
            $order->created_at = now();
            
            // Mock order items
            $order->items = collect([
                (object)[
                    'product_name' => 'Test Product 1',
                    'price' => 50000,
                    'quantity' => 1
                ],
                (object)[
                    'product_name' => 'Test Product 2',
                    'price' => 50000,
                    'quantity' => 1
                ]
            ]);
        }
        
        // Override email for testing
        $order->email = $email;
        
        if (class_exists('App\Mail\PaymentConfirmation')) {
            Mail::to($email)->queue(new PaymentConfirmation($order));
            $this->info("✓ Payment confirmation email queued");
        } else {
            $this->error("PaymentConfirmation mail class not found");
        }
    }
    
    protected function processQueue()
    {
        $this->info("\nProcessing email queue...");
        $this->info("Press Ctrl+C to stop when finished (after the queue is empty).\n");
        
        try {
            Artisan::call('queue:work', [
                '--once' => true,
                '--verbose' => true
            ], $this->output);
            
            // Small pause to allow for output to be readable
            sleep(1);
            
            $remainingJobs = DB::table('jobs')->count();
            
            if ($remainingJobs > 0) {
                if ($this->confirm("\n{$remainingJobs} jobs remaining. Process more?", true)) {
                    $this->processQueue();
                }
            } else {
                $this->info("\nAll queued emails have been processed!");
            }
            
        } catch (\Exception $e) {
            $this->error('Error processing queue: ' . $e->getMessage());
        }
    }
    
    /**
     * Add diagnostic option to test total price calculation
     */
    protected function debugCalculation($items, $shippingCost = 10000)
    {
        $this->info("\nDiagnosing price calculation issue...");
        
        $subtotal = 0;
        $this->line("\nItems:");
        
        foreach ($items as $index => $item) {
            $price = $item->price * $item->quantity;
            $subtotal += $price;
            $this->line(" - Item " . ($index + 1) . ": " . $item->product_name . 
                         " x " . $item->quantity . 
                         " = Rp " . number_format($price, 0, ',', '.'));
        }
        
        $tax = ceil($subtotal * 0.11);
        $total = $subtotal + $shippingCost + $tax;
        
        $this->line("\nCalculation steps:");
        $this->line(" - Raw Subtotal: " . $subtotal);
        $this->line(" - Formatted Subtotal: Rp " . number_format($subtotal, 0, ',', '.'));
        $this->line(" - Shipping Cost: Rp " . number_format($shippingCost, 0, ',', '.'));
        $this->line(" - Tax (11%): Rp " . number_format($tax, 0, ',', '.'));
        $this->line(" - Total: Rp " . number_format($total, 0, ',', '.'));
        
        return [
            'subtotal' => $subtotal,
            'shipping' => $shippingCost,
            'tax' => $tax,
            'total' => $total
        ];
    }
    
    /**
     * Add direct email test without queue to diagnose delivery issues
     */
    protected function sendDirectEmail($email)
    {
        $this->info("\nSending direct email without queue...");
        
        try {
            $result = Mail::raw("This is a DIRECT test email from GPX Store sent at " . now() . 
                             "\n\nThis email bypasses the queue to test direct delivery.", 
                function($message) use ($email) {
                    $message->to($email)
                            ->subject('GPX Store DIRECT Mail Test');
                });
                
            $this->info("✓ Direct email sent successfully!");
            return true;
        } catch (\Exception $e) {
            $this->error("✗ Direct email failed: " . $e->getMessage());
            Log::error('Direct email test failed: ' . $e->getMessage(), [
                'exception' => $e,
                'to' => $email
            ]);
            return false;
        }
    }
}
