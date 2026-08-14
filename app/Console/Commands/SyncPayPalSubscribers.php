<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PayPalService;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Plan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SyncPayPalSubscribers extends Command
{
    protected $signature = 'paypal:sync-subscribers';
    protected $description = 'Sync PayPal subscribers, update user payment and plan info';

    protected array $paypalPlanCache = [];

    public function handle(PayPalService $paypal)
    {
        $this->info('Fetching PayPal subscribers...');

        $subscriptionIds = $this->collectSubscriptionIds($paypal);

        if ($subscriptionIds->isEmpty()) {
            $this->info('No PayPal subscriptions found to sync.');
            return Command::SUCCESS;
        }

        $plans = Plan::all();

        foreach ($subscriptionIds as $subscriptionId) {
            $paypalSub = $paypal->getSubscription($subscriptionId);

            if (! data_get($paypalSub, 'id')) {
                Log::warning("PayPal subscription {$subscriptionId} could not be retrieved", [
                    'response' => $paypalSub,
                ]);
                continue;
            }

            $user = $this->resolveUser($paypalSub);

            if (! $user) {
                Log::warning("No local user found for PayPal subscription {$subscriptionId}");
                continue;
            }

            $this->info("Processing {$user->email}");

            $paypalPlanId = data_get($paypalSub, 'plan_id');
            $planDetails = $this->paypalPlanDetails($paypal, $paypalPlanId);
            $cycle = $this->regularBillingCycle($planDetails);

            $interval = strtolower((string) data_get($cycle, 'frequency.interval_unit'));
            $intervalCount = (int) (data_get($cycle, 'frequency.interval_count') ?? 1);
            $amount = (float) (
                data_get($paypalSub, 'billing_info.last_payment.amount.value')
                ?? data_get($cycle, 'pricing_scheme.fixed_price.value')
                ?? 0
            );
            $status = $this->mapStatus((string) data_get($paypalSub, 'status', ''));

            $startDate = $this->parseTime(
                data_get($paypalSub, 'billing_info.last_payment.time')
                    ?? data_get($paypalSub, 'start_time')
            );
            $endDate = $this->parseTime(data_get($paypalSub, 'billing_info.next_billing_time'))
                ?? match ($interval) {
                    'month' => $startDate?->copy()->addMonths($intervalCount),
                    'year'  => $startDate?->copy()->addYears($intervalCount),
                    'week'  => $startDate?->copy()->addWeeks($intervalCount),
                    'day'   => $startDate?->copy()->addDays($intervalCount),
                    default => null,
                };

            $plan = $plans->first(function (Plan $plan) use ($paypalPlanId) {
                return collect($plan->paypal_plan_ids)->contains($paypalPlanId);
            });

            Subscription::updateOrCreate(
                ['stripe_id' => $paypalSub['id']],
                [
                    'user_id' => $user->id,
                    'stripe_status' => $status,
                    'stripe_price' => $paypalPlanId,
                    'type' => 'default',
                    'quantity' => (int) (data_get($paypalSub, 'quantity') ?? 1),
                    'trial_ends_at' => data_get($paypalSub, 'billing_info.cycle_executions.0.tenure_type') === 'TRIAL'
                        ? $this->parseTime(data_get($paypalSub, 'billing_info.next_billing_time'))
                        : null,
                    'ends_at' => in_array($status, ['canceled', 'expired'], true) ? ($endDate ?? now()) : $endDate,
                    'payment_method' => 'paypal',
                    'plan_code' => $plan?->id,
                    'subscription_code' => $paypalSub['id'],
                ]
            );

            $user->update([
                'payment_status' => $status === 'active' ? 'successful' : 'failed',
                'payment_method' => 'paypal',
                'amount' => $amount,
                'premium' => (isset($plan->tier) && stripos($plan->tier, 'premium') !== false) ? 1 : 0,
                'plan' => $plan?->id,
                'subscription_status' => $status,
                'subscription_type' => $this->durationFromInterval($interval, $intervalCount, $plan),
                'subscription_expires_at' => $endDate,
                'metadata' => array_merge($user->metadata ?? [], [
                    'paypal_subscription_id' => $paypalSub['id'],
                    'plan_id' => $plan?->id,
                    'currency' => data_get($paypalSub, 'billing_info.last_payment.amount.currency_code')
                        ?? data_get($cycle, 'pricing_scheme.fixed_price.currency_code'),
                ]),
            ]);

            $this->info("Updated {$user->email}: {$status}, plan={$plan?->id}, amount={$amount}, ends_at={$endDate}");
        }

        $this->info('Sync complete.');

        return Command::SUCCESS;
    }

    protected function collectSubscriptionIds(PayPalService $paypal)
    {
        $ids = collect()
            ->merge(
                Subscription::where('payment_method', 'paypal')
                    ->whereNotNull('stripe_id')
                    ->pluck('stripe_id')
            )
            ->merge(
                Subscription::where('payment_method', 'paypal')
                    ->whereNotNull('subscription_code')
                    ->pluck('subscription_code')
            )
            ->merge(
                User::whereNotNull('metadata->paypal_subscription_id')
                    ->pluck('metadata')
                    ->map(fn ($metadata) => data_get($metadata, 'paypal_subscription_id'))
            );

        try {
            $ids = $ids->merge($this->discoverFromTransactions($paypal));
        } catch (\Throwable $e) {
            Log::warning('PayPal transaction search skipped', ['error' => $e->getMessage()]);
        }

        return $ids->filter()->unique()->values();
    }

    protected function discoverFromTransactions(PayPalService $paypal): array
    {
        $ids = [];
        $page = 1;
        $end = now()->utc();
        $start = now()->utc()->subDays(31);

        do {
            $response = $paypal->listTransactions([
                'start_date' => $start->toIso8601String(),
                'end_date' => $end->toIso8601String(),
                'fields' => 'all',
                'page_size' => 100,
                'page' => $page,
            ]);

            if (! isset($response['transaction_details']) && isset($response['name'])) {
                Log::warning('PayPal transaction search failed', ['response' => $response]);
                break;
            }

            foreach ($response['transaction_details'] ?? [] as $detail) {
                $type = data_get($detail, 'transaction_info.paypal_reference_id_type');
                $ref = data_get($detail, 'transaction_info.paypal_reference_id');

                if (($type === 'SUB' || str_starts_with((string) $ref, 'I-')) && $ref) {
                    $ids[] = $ref;
                }
            }

            $totalPages = (int) ($response['total_pages'] ?? 1);
            $page++;
        } while ($page <= $totalPages);

        return $ids;
    }

    protected function resolveUser(array $paypalSub): ?User
    {
        $customId = data_get($paypalSub, 'custom_id');
        if ($customId) {
            $user = User::find($customId);
            if ($user) {
                return $user;
            }
        }

        $paypalSubId = data_get($paypalSub, 'id');
        $subscription = Subscription::where(function ($query) use ($paypalSubId) {
            $query->where('stripe_id', $paypalSubId)
                ->orWhere('subscription_code', $paypalSubId);
        })->first();

        if ($subscription?->user) {
            return $subscription->user;
        }

        $email = data_get($paypalSub, 'subscriber.email_address');
        if ($email) {
            return User::where('email', $email)->first();
        }

        return null;
    }

    protected function paypalPlanDetails(PayPalService $paypal, ?string $planId): array
    {
        if (! $planId) {
            return [];
        }

        if (! array_key_exists($planId, $this->paypalPlanCache)) {
            $this->paypalPlanCache[$planId] = $paypal->getPlan($planId) ?? [];
        }

        return is_array($this->paypalPlanCache[$planId]) ? $this->paypalPlanCache[$planId] : [];
    }

    protected function regularBillingCycle(array $planDetails): array
    {
        $cycles = data_get($planDetails, 'billing_cycles', []);

        return collect($cycles)->firstWhere('tenure_type', 'REGULAR')
            ?? ($cycles[0] ?? []);
    }

    protected function mapStatus(string $status): string
    {
        return match (strtoupper($status)) {
            'ACTIVE', 'APPROVED' => 'active',
            'APPROVAL_PENDING' => 'incomplete',
            'SUSPENDED' => 'past_due',
            'CANCELLED', 'CANCELED' => 'canceled',
            'EXPIRED' => 'expired',
            default => strtolower($status) ?: 'incomplete',
        };
    }

    protected function durationFromInterval(?string $interval, int $intervalCount, ?Plan $plan): ?string
    {
        if ($plan?->type) {
            return $plan->type;
        }

        return match ($interval) {
            'year' => 'yearly',
            'month' => $intervalCount === 3 ? 'quarterly' : 'monthly',
            'week' => 'weekly',
            'day' => 'daily',
            default => null,
        };
    }

    protected function parseTime(mixed $value): ?Carbon
    {
        if (! $value || ! is_string($value)) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
