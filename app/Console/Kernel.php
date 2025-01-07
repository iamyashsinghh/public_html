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
            // Log::info('Running the activitylog:clean command');
        });

        // eveymin task: Precompute dashboard data
        $schedule->command('dashboard:precompute')->everyMinute()->before(function () {
            // Log::info('Starting the dashboard:precompute command');
        })->after(function () {
            // Log::info('Finished the dashboard:precompute command');
        });

        $schedule->command('dashboard:save-historical-data')->monthlyOn(1, '00:00');

        // eveymin task: Precompute dashboard data
        $schedule->command('seomanagerdashboard:precompute')->everyMinute()->before(function () {
            // Log::info('Starting the seomanagerdashboard:precompute command');
        })->after(function () {
            // Log::info('Finished the seomanagerdashboard:precompute command');
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
