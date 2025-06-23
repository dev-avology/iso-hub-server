<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class QueueWorkerCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:queue-worker';

    /**
     * The console command description.
     */
    protected $description = 'Start the Laravel queue worker process';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Laravel queue worker...');

        $process = Process::fromShellCommandline(
            'php artisan queue:work --sleep=3 --tries=3 --timeout=90'
        );

        // Optional: increase memory limit
        $process->setTimeout(null); // No timeout for long-running jobs

        try {
            $process->run(function ($type, $buffer) {
                echo $buffer;
            });
        } catch (ProcessFailedException $e) {
            $this->error('Queue worker failed: ' . $e->getMessage());
        }

        return Command::SUCCESS;
    }
}
