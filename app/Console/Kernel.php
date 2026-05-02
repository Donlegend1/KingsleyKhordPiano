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
        // System / sync jobs — run early before emails
        $schedule->command('subscriptions:check-expired')->dailyAt('00:00');
        $schedule->command('community:sync-users')->dailyAt('01:00');
        $schedule->command('stripe:sync-subscribers')->everyThreeMinutes();

        // Daily emails — spaced 30 mins apart
        $schedule->command('email:send-roadmap')->dailyAt('07:00');
        $schedule->command('email:send-progress-check')->dailyAt('07:30');
        $schedule->command('email:failed-payment-reminders')->dailyAt('08:00');
        $schedule->command('emails:send-quick-lessons')->dailyAt('08:30');
        $schedule->command('emails:send-ear-training-quiz')->dailyAt('09:00');
        $schedule->command('email:encouragement')->dailyAt('09:30');
        $schedule->command('email:community-invite')->dailyAt('10:00');

        // Weekly emails — each on a different day, spaced from daily emails
        $schedule->command('email:reengagement')->weeklyOn(1, '11:00');         // Monday
        $schedule->command('email:send-skill-assessment')->weeklyOn(2, '11:00'); // Tuesday
        $schedule->command('email:send-milestone')->weeklyOn(3, '11:00');        // Wednesday
        $schedule->command('email:send-song-breakdown')->weeklyOn(4, '11:00');   // Thursday

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
