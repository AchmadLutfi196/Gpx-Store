<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class FixMailConfigCommand extends Command
{
    protected $signature = 'mail:fix-config {provider=gmail}';
    protected $description = 'Fix mail configuration and generate .env entries';

    public function handle()
    {
        $provider = $this->argument('provider');
        
        $this->info("🛠 Mail Configuration Fixer");
        $this->line("----------------------------------------");
        
        if (!in_array($provider, ['gmail', 'mailgun', 'sendgrid', 'mailtrap'])) {
            $this->error("Provider '$provider' not supported. Available providers: gmail, mailgun, sendgrid, mailtrap");
            return 1;
        }
        
        $this->info("Configuring mail for: $provider");
        
        switch ($provider) {
            case 'gmail':
                $this->configureGmail();
                break;
            case 'mailgun':
                $this->configureMailgun();
                break;
            case 'sendgrid':
                $this->configureSendgrid();
                break;
            case 'mailtrap':
                $this->configureMailtrap();
                break;
        }
        
        $this->info("\n✅ Configuration completed!");
        $this->line("Please restart your application and test emails again.");
        
        if ($this->confirm('Would you like to test the new configuration now?', true)) {
            $email = $this->ask('Enter test email address');
            Artisan::call('email:debug', [
                'to' => $email,
                '--test' => true
            ], $this->output);
        }
        
        return 0;
    }
    
    protected function configureGmail()
    {
        $this->info("To use Gmail SMTP, you should create an App Password:");
        $this->line("1. Go to your Google Account > Security");
        $this->line("2. Enable 2-Step Verification if not already enabled");
        $this->line("3. Go to App passwords");
        $this->line("4. Select 'Mail' and 'Other' (custom name, e.g., 'Laravel GPX Store')");
        $this->line("5. Use the generated password below");
        
        $email = $this->ask('Enter Gmail address');
        $password = $this->secret('Enter Gmail App Password');
        $fromName = $this->ask('Enter from name', 'GPX Store');
        
        $config = [
            'MAIL_MAILER' => 'smtp',
            'MAIL_HOST' => 'smtp.gmail.com',
            'MAIL_PORT' => '587',
            'MAIL_USERNAME' => $email,
            'MAIL_PASSWORD' => $password,
            'MAIL_ENCRYPTION' => 'tls',
            'MAIL_FROM_ADDRESS' => $email,
            'MAIL_FROM_NAME' => "\"$fromName\""
        ];
        
        $this->updateEnv($config);
    }
    
    protected function configureMailgun()
    {
        $this->info("To use Mailgun, you need an account at https://www.mailgun.com/");
        
        $domain = $this->ask('Enter Mailgun domain');
        $secret = $this->secret('Enter Mailgun API key');
        $fromEmail = $this->ask('Enter from email address');
        $fromName = $this->ask('Enter from name', 'GPX Store');
        
        $config = [
            'MAIL_MAILER' => 'mailgun',
            'MAILGUN_DOMAIN' => $domain,
            'MAILGUN_SECRET' => $secret,
            'MAIL_FROM_ADDRESS' => $fromEmail,
            'MAIL_FROM_NAME' => "\"$fromName\""
        ];
        
        $this->updateEnv($config);
        
        $this->info("\nInstalling Mailgun SDK...");
        shell_exec('composer require symfony/http-client symfony/mailgun-mailer');
    }
    
    protected function configureSendgrid()
    {
        $this->info("To use SendGrid, you need an account at https://sendgrid.com/");
        
        $key = $this->secret('Enter SendGrid API key');
        $fromEmail = $this->ask('Enter from email address');
        $fromName = $this->ask('Enter from name', 'GPX Store');
        
        $config = [
            'MAIL_MAILER' => 'sendgrid',
            'SENDGRID_API_KEY' => $key,
            'MAIL_FROM_ADDRESS' => $fromEmail,
            'MAIL_FROM_NAME' => "\"$fromName\""
        ];
        
        $this->updateEnv($config);
        
        $this->info("\nInstalling SendGrid SDK...");
        shell_exec('composer require s-ichikawa/laravel-sendgrid-driver');
    }
    
    protected function configureMailtrap()
    {
        $this->info("To use Mailtrap, you need an account at https://mailtrap.io/");
        
        $username = $this->ask('Enter Mailtrap username/inbox ID');
        $password = $this->secret('Enter Mailtrap password/API key');
        $fromEmail = $this->ask('Enter from email address', 'gpxstore@example.com');
        $fromName = $this->ask('Enter from name', 'GPX Store');
        
        $config = [
            'MAIL_MAILER' => 'smtp',
            'MAIL_HOST' => 'smtp.mailtrap.io',
            'MAIL_PORT' => '2525',
            'MAIL_USERNAME' => $username,
            'MAIL_PASSWORD' => $password,
            'MAIL_ENCRYPTION' => 'tls',
            'MAIL_FROM_ADDRESS' => $fromEmail,
            'MAIL_FROM_NAME' => "\"$fromName\""
        ];
        
        $this->updateEnv($config);
    }
    
    protected function updateEnv($configs)
    {
        $envFile = base_path('.env');
        $envContent = File::get($envFile);
        
        foreach ($configs as $key => $value) {
            if (strpos($envContent, $key . '=') !== false) {
                $envContent = preg_replace("/$key=(.*)/", "$key=$value", $envContent);
            } else {
                $envContent .= "\n$key=$value";
            }
        }
        
        File::put($envFile, $envContent);
        
        $this->info("\nUpdated .env with new mail configuration");
    }
}
