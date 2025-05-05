<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;

class TestEmailCommand extends Command
{
    protected $signature = 'mail:test {email : The email address to send the test to}';
    protected $description = 'Send a test email to verify mail configuration';

    public function handle()
    {
        $email = $this->argument('email');
        $this->info("Sending test email to {$email}...");

        try {
            Mail::to($email)->send(new TestMail());
            $this->info('Test email sent successfully!');
        } catch (\Exception $e) {
            $this->error("Failed to send email: {$e->getMessage()}");
            $this->error("File: {$e->getFile()}");
            $this->error("Line: {$e->getLine()}");
        }

        return 0;
    }
}
