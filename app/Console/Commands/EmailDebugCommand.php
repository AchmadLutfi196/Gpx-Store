<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class EmailDebugCommand extends Command
{
    protected $signature = 'email:debug {to} {--provider=} {--test}';
    protected $description = 'Debug email configuration and test multiple providers';

    // Email provider configurations
    protected $providers = [
        'gmail' => [
            'driver' => 'smtp',
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'encryption' => 'tls',
            'username' => '', // Set in command
            'password' => '', // Set in command
            'from' => [
                'address' => '', // Set in command
                'name' => 'GPX Store'
            ]
        ],
        'mailtrap' => [
            'driver' => 'smtp',
            'host' => 'smtp.mailtrap.io',
            'port' => 2525,
            'encryption' => 'tls',
            'username' => '', // Ask in command 
            'password' => '', // Ask in command
            'from' => [
                'address' => 'gpxstore@example.com',
                'name' => 'GPX Store'
            ]
        ],
        'mailgun' => [
            'driver' => 'mailgun',
            'domain' => '',  // Ask in command
            'secret' => '',  // Ask in command
            'endpoint' => 'api.mailgun.net',
            'from' => [
                'address' => '', // Ask in command
                'name' => 'GPX Store'
            ]
        ]
    ];

    public function handle()
    {
        $to = $this->argument('to');
        $providerName = $this->option('provider');
        $runTest = $this->option('test');

        $this->info("🔍 Email Configuration Diagnostic Tool");
        $this->line("----------------------------------------");

        // Show current configuration
        $this->showCurrentConfig();

        // Test email delivery if requested
        if ($runTest) {
            if ($providerName) {
                $this->testWithProvider($to, $providerName);
            } else {
                $this->testWithCurrentConfig($to);
            }
        }

        return 0;
    }

    protected function showCurrentConfig()
    {
        $this->info("📧 Current Email Configuration:");
        $this->line("Driver: " . config('mail.default'));
        $this->line("Host: " . config('mail.mailers.smtp.host'));
        $this->line("Port: " . config('mail.mailers.smtp.port'));
        $this->line("Username: " . config('mail.mailers.smtp.username'));
        $this->line("Password: " . (config('mail.mailers.smtp.password') ? '********' : 'Not set'));
        $this->line("Encryption: " . config('mail.mailers.smtp.encryption'));
        $this->line("From Address: " . config('mail.from.address'));
        $this->line("From Name: " . config('mail.from.name'));
        $this->line("----------------------------------------");
    }

    protected function testWithCurrentConfig($to)
    {
        $this->info("🚀 Testing email with current configuration...");
        
        try {
            $this->line("\n📝 Sending simple test email...");
            $result = Mail::raw('This is a test email from GPX Store sent at ' . now() . 
                               "\n\nEmail Configuration:" .
                               "\nDriver: " . config('mail.default') .
                               "\nHost: " . config('mail.mailers.smtp.host') .
                               "\nPort: " . config('mail.mailers.smtp.port') .
                               "\nEncryption: " . config('mail.mailers.smtp.encryption') .
                               "\nFrom: " . config('mail.from.address'),
                function($message) use ($to) {
                    $message->to($to)
                            ->subject('GPX Store Email Test');
                });
            
            $this->info("✓ Email sent successfully with current configuration!");
            $this->line("Please check your inbox at: " . $to);
            $this->line("Also check your spam/junk folder.");
            
        } catch (\Exception $e) {
            $this->error("❌ Error sending email: " . $e->getMessage());
            Log::error("Email debug test failed: " . $e->getMessage(), [
                'exception' => $e,
                'to' => $to
            ]);
        }
    }

    protected function testWithProvider($to, $providerName)
    {
        if (!isset($this->providers[$providerName])) {
            $this->error("Provider '$providerName' not found. Available providers: " . implode(', ', array_keys($this->providers)));
            return;
        }

        $this->info("🔄 Testing with $providerName provider...");
        
        // Get provider config
        $config = $this->providers[$providerName];
        
        // Ask for necessary credentials
        switch ($providerName) {
            case 'gmail':
                $config['username'] = $this->ask("Enter Gmail address");
                $config['password'] = $this->secret("Enter Gmail App Password");
                $config['from']['address'] = $config['username'];
                break;
                
            case 'mailtrap':
                $config['username'] = $this->ask("Enter Mailtrap username/inbox ID");
                $config['password'] = $this->secret("Enter Mailtrap password/API key");
                break;
                
            case 'mailgun':
                $config['domain'] = $this->ask("Enter Mailgun domain");
                $config['secret'] = $this->secret("Enter Mailgun API key");
                $config['from']['address'] = $this->ask("Enter from email address");
                break;
        }

        // Temporarily override mail config
        $originalMailer = config('mail.default');
        $originalConfig = [];

        foreach (array_keys($config) as $key) {
            if ($key === 'from') continue;
            if ($key === 'driver') {
                $originalConfig['mail.default'] = config('mail.default');
                Config::set('mail.default', $config[$key]);
            } else {
                $configKey = "mail.mailers.{$config['driver']}.$key";
                $originalConfig[$configKey] = config($configKey);
                Config::set($configKey, $config[$key]);
            }
        }

        // Override from address
        $originalFromAddress = config('mail.from.address');
        $originalFromName = config('mail.from.name');
        Config::set('mail.from.address', $config['from']['address']);
        Config::set('mail.from.name', $config['from']['name']);

        // Test sending
        try {
            $this->line("\n📝 Sending test email via $providerName...");
            Mail::raw("This is a test email from GPX Store via $providerName sent at " . now() . 
                      "\n\nEmail Configuration:" .
                      "\nDriver: " . config('mail.default') .
                      "\nHost: " . config('mail.mailers.' . config('mail.default') . '.host') .
                      "\nPort: " . config('mail.mailers.' . config('mail.default') . '.port') .
                      "\nFrom: " . config('mail.from.address'),
                function($message) use ($to) {
                    $message->to($to)
                            ->subject("GPX Store Test via " . config('mail.default'));
                });
            
            $this->info("✓ Email sent successfully with $providerName!");
            $this->line("Please check your inbox at: " . $to);
            
        } catch (\Exception $e) {
            $this->error("❌ Error sending email via $providerName: " . $e->getMessage());
            Log::error("Email test failed with $providerName: " . $e->getMessage(), [
                'exception' => $e,
                'provider' => $providerName,
                'to' => $to
            ]);
        }

        // Restore original config
        foreach ($originalConfig as $key => $value) {
            Config::set($key, $value);
        }
        Config::set('mail.from.address', $originalFromAddress);
        Config::set('mail.from.name', $originalFromName);
    }
}
