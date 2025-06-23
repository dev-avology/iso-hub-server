<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        // $schedule->command('files:cleanup')->daily();
        $schedule->command('files:cleanup')->everyMinute();
        $schedule->command('queue:work --sleep=3 --tries=3 --timeout=90')
        ->everyMinute()
        ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        // \App\Console\Commands\QueueWorkerCommand::class;
        require base_path('routes/console.php');
    }
}
