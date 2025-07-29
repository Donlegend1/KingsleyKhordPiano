<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Notifications\FailedPaymentReminderNotification;

class SendFailedPaymentReminders extends Command
{
    protected $signature = 'email:failed-payment-reminders';
    protected $description = 'Send reminders to users with failed payments and no successful payment after that';

    public function handle()
    {
        $users = User::whereHas('payments', function ($q) {
            $q->where('status', 'failed');
        })->get()->filter(function ($user) {
            $latestFailed = $user->payments()
                ->where('status', 'failed')
                ->latest('created_at')
                ->first();

            if (! $latestFailed) return false;

            return ! $user->payments()
                ->where('status', 'successful')
                ->where('created_at', '>', $latestFailed->created_at)
                ->exists();
        });

        $this->info("Found {$users->count()} users with unresolved failed payments.");

        foreach ($users as $user) {
            Mail::to($user->email)->queue(new FailedPaymentReminderNotification($user));
        }

        $this->info('Reminder emails queued.');
        return Command::SUCCESS;
    }
}
