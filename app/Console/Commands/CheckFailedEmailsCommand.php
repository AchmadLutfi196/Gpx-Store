<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CheckFailedEmailsCommand extends Command
{
    protected $signature = 'mail:check-failed';
    protected $description = 'Check for failed emails and provide debugging information';

    public function handle()
    {
        $this->info('Checking for failed emails...');
        
        // Check failed_jobs table
        $failedJobs = DB::table('failed_jobs')
            ->orderBy('failed_at', 'desc')
            ->limit(10)
            ->get();
            
        if ($failedJobs->count() > 0) {
            $this->info('Found ' . $failedJobs->count() . ' failed email jobs:');
            
            $headers = ['ID', 'Connection', 'Queue', 'Failed At', 'Exception'];
            $rows = [];
            
            foreach ($failedJobs as $job) {
                $rows[] = [
                    $job->id,
                    $job->connection,
                    $job->queue,
                    Carbon::parse($job->failed_at)->format('Y-m-d H:i:s'),
                    substr($job->exception, 0, 100) . '...'
                ];
            }
            
            $this->table($headers, $rows);
            
            if ($this->confirm('Would you like to see full details of the latest failed job?')) {
                $latestFailed = $failedJobs->first();
                $this->line("\nFull Exception Details:");
                $this->error($latestFailed->exception);
                
                $this->line("\nPayload:");
                $this->info(json_encode(json_decode($latestFailed->payload), JSON_PRETTY_PRINT));
                
                if ($this->confirm('Would you like to retry this failed job?')) {
                    DB::table('failed_jobs')
                        ->where('id', $latestFailed->id)
                        ->delete();
                        
                    $this->info('Job deleted from failed_jobs table. It will be retried.');
                }
                
                if ($this->confirm('Would you like to clear all failed jobs?')) {
                    DB::table('failed_jobs')->delete();
                    $this->info('All failed jobs have been cleared.');
                }
            }
        } else {
            $this->info('No failed jobs found in the failed_jobs table.');
        }
        
        // Check for dead or stuck jobs
        $oldJobs = DB::table('jobs')
            ->where('created_at', '<', Carbon::now()->subHours(1))
            ->orderBy('created_at', 'asc')
            ->limit(10)
            ->get();
            
        if ($oldJobs->count() > 0) {
            $this->warn('Found ' . $oldJobs->count() . ' jobs older than 1 hour that might be stuck:');
            
            $headers = ['ID', 'Queue', 'Attempts', 'Created At'];
            $rows = [];
            
            foreach ($oldJobs as $job) {
                $rows[] = [
                    $job->id,
                    $job->queue,
                    $job->attempts,
                    Carbon::parse($job->created_at)->format('Y-m-d H:i:s')
                ];
            }
            
            $this->table($headers, $rows);
            
            if ($this->confirm('Would you like to delete these potentially stuck jobs?')) {
                $deletedCount = DB::table('jobs')
                    ->where('created_at', '<', Carbon::now()->subHours(1))
                    ->delete();
                $this->info("Deleted {$deletedCount} stuck jobs.");
            }
        } else {
            $this->info('No stuck jobs found.');
        }
        
        return 0;
    }
}
