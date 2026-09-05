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
        // System / sync jobs
        $schedule->command('subscriptions:check-expired')->dailyAt('00:00');
        $schedule->command('booking:generate-availability')->dailyAt('00:30');
        $schedule->command('community:sync-users')->dailyAt('01:00');
        $schedule->command('stripe:sync-subscribers')->everyThreeMinutes();
        $schedule->command('paypal:sync-subscribers')->everyThreeMinutes();

        // Application Emails
        $schedule->command('email:welcome')->dailyAt('07:00');
        // $schedule->command('email:reengagement')->dailyAt('08:00');
        $schedule->command('email:send-skill-assessment')->dailyAt('09:00');
        $schedule->command('email:send-song-breakdowns')->dailyAt('10:00');

        // Frequent jobs
        $schedule->command('notifications:send-discovery-calls')->everyThreeHours();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
