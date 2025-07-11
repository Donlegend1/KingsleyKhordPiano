<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment;
use App\Mail\SubscriptionExpiredMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class CheckExpiredSubscriptions extends Command
{
    protected $signature = 'subscriptions:check-expired';
    protected $description = 'Check for expired subscriptions and send email notifications';

    public function handle(): int
    {
        $this->info('Checking for expired subscriptions...');

        $expiredSubs = Payment::where('ends_at', '<', now())
            ->whereNull('notified_at')
            ->where('status', 'successful')
            ->with('user') 
            ->get();

        foreach ($expiredSubs as $subscription) {
            if (!$subscription->user) {
                $this->warn("Subscription ID {$subscription->id} has no user.");
                continue;
            }

            Mail::to($subscription->user->email)->send(
                new SubscriptionExpiredMail($subscription->user, $subscription)
            );

            $subscription->update(['notified_at' => now()]);
            $subscription->user->update([
                'premium' => false,
                'payment_status' => 'expired',
            ]);

            $this->info("Notified: {$subscription->user->email}");
        }

        $this->info('Subscription check completed.');

        return Command::SUCCESS;
    }
}
