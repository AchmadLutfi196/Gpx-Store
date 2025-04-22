<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckGitHubEnv extends Command
{
    protected $signature = 'github:env';
    protected $description = 'Check GitHub OAuth environment variables';

    public function handle()
    {
        $this->info('Checking GitHub OAuth Environment Variables:');
        
        $clientId = env('GITHUB_CLIENT_ID');
        $clientSecret = env('GITHUB_CLIENT_SECRET');
        $redirect = env('GITHUB_REDIRECT');
        
        $this->line('GITHUB_CLIENT_ID: ' . ($clientId ? '[SET]' : '[MISSING]'));
        $this->line('GITHUB_CLIENT_SECRET: ' . ($clientSecret ? '[SET]' : '[MISSING]'));
        $this->line('GITHUB_REDIRECT: ' . ($redirect ?: '[MISSING]'));
        
        $this->newLine();
        $this->line('Checking GitHub Services Config:');
        
        $configId = config('services.github.client_id');
        $configSecret = config('services.github.client_secret');
        $configRedirect = config('services.github.redirect');
        
        $this->line('services.github.client_id: ' . ($configId ? '[SET]' : '[MISSING]'));
        $this->line('services.github.client_secret: ' . ($configSecret ? '[SET]' : '[MISSING]'));
        $this->line('services.github.redirect: ' . ($configRedirect ?: '[MISSING]'));

        return Command::SUCCESS;
    }
}