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
    private const ALWAYS_PREMIUM_EMAILS = [
        'henagyeman1@icloud.com',
    ];

    public function handle()
    {
        $this->info('Fetching Stripe subscribers...');
        $stripe = Cashier::stripe();

        // autoPagingIterator walks through every page, not just the first 100
        $subscriptions = $stripe->subscriptions->all(['limit' => 100]);

        foreach ($subscriptions->autoPagingIterator() as $stripeSub) {
            try {
                $stripeCustomerId = $stripeSub->customer;

                // Find the local user by Stripe customer ID, falling back to email
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

                $price         = $stripeItem->price;
                $interval      = $price->recurring->interval ?? null;
                $intervalCount = $price->recurring->interval_count ?? 1;
                $amount        = $price->unit_amount / 100;
                $status        = $stripeSub->status;

                $startDate = Carbon::createFromTimestamp($stripeSub->current_period_start);
                $endDate = match ($interval) {
                    'month' => $startDate->copy()->addMonths($intervalCount),
                    'year'  => $startDate->copy()->addYears($intervalCount),
                    'week'  => $startDate->copy()->addWeeks($intervalCount),
                    'day'   => $startDate->copy()->addDays($intervalCount),
                    default => null,
                };

                // Matches on either the product ID (prod_...) or the price ID (price_...),
                // so it works whichever one your plans table stores in stripe_product_id.
                $plan = Plan::where('stripe_product_id', $price->product)
                    ->orWhere('stripe_product_id', $price->id)
                    ->first();

                Subscription::updateOrCreate(
                    ['stripe_id' => $stripeSub->id],
                    [
                        'user_id'       => $user->id,
                        'stripe_status' => $status,
                        'stripe_price'  => $price->id,
                        'type'          => 'default',
                        'quantity'      => $stripeItem->quantity ?? 1,
                        'trial_ends_at' => isset($stripeSub->trial_end)
                            ? Carbon::createFromTimestamp($stripeSub->trial_end)
                            : null,
                        'ends_at'       => $endDate,
                    ]
                );

                // premium is an integer flag: 1 for an active premium tier, otherwise 0
                $tier = strtolower(trim($plan?->tier ?? ''));
                $isPremium = in_array($status, ['active', 'trialing'], true) && $tier === 'premium';

                // Override: certain accounts are always premium
                if (in_array(strtolower($user->email), self::ALWAYS_PREMIUM_EMAILS, true)) {
                    $isPremium = true;
                }

                $user->update([
                    'payment_status' => $status === 'active' ? 'successful' : 'failed',
                    'amount'         => $amount,
                    'plan'           => $plan?->id,
                    'premium'        => $isPremium ? 1 : 0,
                ]);

                if ($status === 'active' && $plan) {
                    app(\App\Services\SubscriptionLifecycleService::class)
                        ->syncCoachingAccess($user->fresh(), $plan);
                }

                $this->info(
                    "Updated {$user->email}: {$status}, plan={$plan?->id}, premium=" . ($isPremium ? 1 : 0) .
                    ", amount={$amount}, ends_at={$endDate}"
                );
            } catch (\Throwable $e) {
                // One bad record no longer aborts the whole sync
                Log::error("Stripe sync failed for subscription {$stripeSub->id}: {$e->getMessage()}");
                $this->error("Failed for subscription {$stripeSub->id}: {$e->getMessage()}");
                continue;
            }
        }

        // Make sure always-premium accounts stay premium even if they have no Stripe subscription
        User::whereIn('email', self::ALWAYS_PREMIUM_EMAILS)->update(['premium' => 1]);

        $this->info('Sync complete.');
    }
}
