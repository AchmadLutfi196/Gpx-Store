<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SendEmails extends Command
{
    protected $signature = 'mail:send {--queue=default} {--timeout=60} {--sleep=3}';
    protected $description = 'Send emails from the queue with detailed processing information';

    public function handle()
    {
        $queue = $this->option('queue');
        $timeout = $this->option('timeout');
        $sleep = $this->option('sleep');
        
        $this->info('Starting email processing from ['.$queue.'] queue...');
        $this->info('Press Ctrl+C to stop the worker');
        
        try {
            $this->info('Running queue worker with timeout: '.$timeout.'s, sleep: '.$sleep.'s');
            
            // Execute queue:work with verbose output to see what's happening
            $exitCode = Artisan::call('queue:work', [
                '--queue' => $queue,
                '--tries' => 3,
                '--timeout' => $timeout,
                '--sleep' => $sleep,
                '--verbose' => true
            ], $this->output);
            
            if ($exitCode === 0) {
                $this->info('Queue worker completed successfully.');
                // Check if any emails are still pending in the queue
                $pendingCount = DB::table('jobs')
                    ->where('queue', $queue)
                    ->count();                    
                if ($pendingCount > 0) {
                    $this->warn("There are still {$pendingCount} jobs pending in the [{$queue}] queue.");
                    $this->info("Run 'php artisan queue:work --queue={$queue}' to process them.");
                } else {
                    $this->info("All emails in the [{$queue}] queue have been processed.");
                }
            } else {
                $this->error("Queue worker exited with code {$exitCode}");
            }
            
        } catch (\Exception $e) {
            $this->error('Error processing emails: ' . $e->getMessage());
            Log::error('Email queue error: ' . $e->getMessage(), [
                'exception' => $e,
                'queue' => $queue
            ]);
            
            return 1;
        }
        
        return 0;
    }
}
