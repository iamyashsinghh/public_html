<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Hourly task: Clean activity log
        $schedule->command('activitylog:clean --force')->hourly()->before(function () {
            Log::info('Running the activitylog:clean command');
        });

        // Daily task: Precompute dashboard data
        $schedule->command('dashboard:precompute')->dailyAt('00:30')->before(function () {
            Log::info('Starting the dashboard:precompute command');
        })->after(function () {
            Log::info('Finished the dashboard:precompute command');
        });
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
