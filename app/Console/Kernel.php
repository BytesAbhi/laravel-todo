<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        // Run the command every minute (for testing; in production change to hourly or as needed)
        $schedule->command('send:task-reminders')->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        // Automatically load all commands in the Commands directory
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
