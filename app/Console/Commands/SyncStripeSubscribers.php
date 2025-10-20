<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Cashier\Cashier;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Plan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SyncStripeSubscribers extends Command
{
    protected $signature = 'stripe:sync-subscribers';
    protected $description = 'Sync Stripe subscribers, update user payment and plan info';

    public function handle()
    {
        $this->info('Fetching Stripe subscribers...');
        $stripe = Cashier::stripe();

        $subscriptions = $stripe->subscriptions->all(['limit' => 100]);

        foreach ($subscriptions->data as $stripeSub) {
            $stripeCustomerId = $stripeSub->customer;

            $user = User::where('stripe_id', $stripeCustomerId)->first();

        if (! $user) {
            $stripeCustomer = $stripe->customers->retrieve($stripeCustomerId);
            $email = $stripeCustomer?->email ?? null;

            if ($email) {
                $user = User::where('email', $email)->first();
            }
        }

        if (! $user) {
            Log::warning("No local user found for Stripe customer {$stripeCustomerId}");
            continue;
        }

            $this->info("Processing {$user->email}");

            $stripeItem = $stripeSub->items->data[0] ?? null;
            if (! $stripeItem) {
                Log::warning("No subscription item found for {$stripeSub->id}");
                continue;
            }

            $price = $stripeItem->price;
            $interval = $price->recurring->interval ?? null;
            $amount = $price->unit_amount / 100;
            $status = $stripeSub->status;

            $startDate = Carbon::createFromTimestamp($stripeSub->current_period_start);
            $endDate = match ($interval) {
                'month' => $startDate->copy()->addMonth(),
                'year'  => $startDate->copy()->addYear(),
                default => null,
            };

            $plan = Plan::where('price_id', $price->id)
                ->orWhere('product_id', $price->product)
                ->first();

            $subscription = Subscription::updateOrCreate(
                ['stripe_id' => $stripeSub->id],
                [
                    'user_id' => $user->id,
                    'stripe_status' => $status,
                    'stripe_price' => $price->id,
                    'type' => 'default',
                    'quantity' => $stripeItem->quantity ?? 1,
                    'trial_ends_at' => isset($stripeSub->trial_end) ? Carbon::createFromTimestamp($stripeSub->trial_end) : null,
                    'ends_at' => $endDate,
                ]
            );

            $user->update([
                'payment_status' => $status === 'active' ? 'successful' : 'failed',
                'amount' => $amount,
                'premium' => $plan?->tier === 'premium' ? 1 : 0,
                'plan_id' => $plan?->id,
            ]);

            $this->info("Updated {$user->email}: {$status}, plan={$plan?->id}, amount={$amount}, ends_at={$endDate}");
        }

        $this->info('Sync complete.');
    }
}
