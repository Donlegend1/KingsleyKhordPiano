<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Cashier\Subscription;
use App\Mail\SubscriptionExpiredMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CheckExpiredSubscriptions extends Command
{
    protected $signature = 'subscriptions:check-expired';
    protected $description = 'Check for expired Cashier subscriptions and send notifications';

    public function handle(): int
    {
        $this->info('Checking for expired Cashier subscriptions...');

        $expiredSubs = Subscription::whereNotNull('ends_at')
            ->where('ends_at', '<', now())
            ->whereNull('notified_at')
            ->where('stripe_status', '!=', 'canceled')
            ->with('user')
            ->get();

        if ($expiredSubs->isEmpty()) {
            $this->info('No expired subscriptions found.');
            return Command::SUCCESS;
        }

        foreach ($expiredSubs as $subscription) {
            $user = $subscription->user;

            if (!$user) {
                $this->warn("Subscription ID {$subscription->id} has no user.");
                continue;
            }

            try {
                // Send notification email
                Mail::to($user->email)->send(
                    new SubscriptionExpiredMail($user, $subscription)
                );

                // Update subscription and user
                $subscription->update(['notified_at' => now()]);
                $user->update([
                    'premium' => false,
                    'payment_status' => 'expired',
                ]);

                $this->info("Notified: {$user->email}");
            } catch (\Throwable $e) {
                Log::error('Failed to notify user about expired subscription', [
                    'subscription_id' => $subscription->id,
                    'user_id' => $user->id ?? null,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info('Cashier subscription check completed.');
        return Command::SUCCESS;
    }
}
