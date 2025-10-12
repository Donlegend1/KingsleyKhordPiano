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
        $schedule->command('subscriptions:check-expired')->daily();
        $schedule->command('email:send-roadmap')->daily();
        $schedule->command('email:send-progress-check')->dailyAt('08:00');
        $schedule->command('email:encouragement')->dailyAt('10:00');
        $schedule->command('email:community-invite')->dailyAt('12:00');
        $schedule->command('email:reengagement')->weeklyOn(1, '08:00');
        $schedule->command('email:send-skill-assessment')->weeklyOn(2, '08:00');
        $schedule->command('email:send-milestone')->weeklyOn(3, '08:00');
        $schedule->command('email:send-song-breakdown')->weeklyOn(4, '08:00');
        $schedule->command('emails:send-quick-lessons')->daily();
        $schedule->command('emails:send-ear-training-quiz')->daily();
        $schedule->command('emails:send-failed-payment-reminder')->dailyAt('09:00');
        $schedule->command('community:sync-users')->dailyAt('01:00');
        $schedule->command('notifications:send-discovery-calls')->everyThreeHours();
        $schedule->command('stripe:sync-subscribers')->everyThreeMinutes();
        
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
