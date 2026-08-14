<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Throwable;

class PayPalService
{
    /**
     * Create a PayPal Billing subscription and return the approval URL.
     *
     * @throws Throwable
     */
    public function createSubscription(User $user, Plan $plan, string $currency, string $tier, string $duration): array
    {
        $currency = strtoupper($currency);
        $amount = $this->amountFor($plan, $currency);

        if ($amount === null) {
            throw new \InvalidArgumentException("Plan has no price for currency {$currency}.");
        }

        $provider = $this->provider($currency);
        $paypalPlanId = $this->resolvePayPalPlanId($provider, $plan, $currency, $duration, $amount);

        $productId = $plan->fresh()->paypal_product_id;
        if (! $productId) {
            throw new \RuntimeException('PayPal product could not be resolved for this plan.');
        }

        $givenName = trim((string) $user->first_name) ?: 'Customer';
        $surname = trim((string) $user->last_name) ?: 'Member';

        // Build the payload explicitly — the package helper omits shipping_preference /
        // user_action, which commonly makes PayPal's approval page fail with the
        // generic /webapps/billing/error screen.
        $subscription = $provider->createSubscription([
            'plan_id' => $paypalPlanId,
            'quantity' => '1',
            'custom_id' => (string) $user->id,
            'subscriber' => [
                'name' => [
                    'given_name' => substr($givenName, 0, 140),
                    'surname' => substr($surname, 0, 140),
                ],
                'email_address' => $user->email,
            ],
            'application_context' => [
                'brand_name' => substr((string) config('app.name', 'Kingsley Khord Piano'), 0, 127),
                'locale' => 'en-US',
                'shipping_preference' => 'NO_SHIPPING',
                'user_action' => 'SUBSCRIBE_NOW',
                'payment_method' => [
                    'payer_selected' => 'PAYPAL',
                    'payee_preferred' => 'IMMEDIATE_PAYMENT_REQUIRED',
                ],
                'return_url' => route('paypal.success'),
                'cancel_url' => route('paypal.cancel'),
            ],
        ]);

        if ($error = data_get($subscription, 'error')) {
            Log::error('PayPal create subscription failed', ['response' => $subscription]);
            throw new \RuntimeException(
                data_get($error, 'details.0.description')
                    ?? data_get($error, 'message')
                    ?? 'Unable to create PayPal subscription.'
            );
        }

        $subscriptionId = data_get($subscription, 'id');
        if (! $subscriptionId) {
            throw new \RuntimeException('PayPal did not return a subscription id.');
        }

        Cache::put($this->pendingCacheKey($subscriptionId), [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'tier' => $tier,
            'duration' => $duration,
            'currency' => $currency,
            'amount' => $amount,
        ], now()->addDay());

        Subscription::updateOrCreate(
            ['stripe_id' => $subscriptionId],
            [
                'user_id' => $user->id,
                'type' => 'default',
                'stripe_status' => 'incomplete',
                'stripe_price' => $paypalPlanId,
                'quantity' => 1,
                'payment_method' => 'paypal',
                'plan_code' => $plan->id,
                'subscription_code' => $subscriptionId,
            ]
        );

        $approveUrl = collect(data_get($subscription, 'links', []))
            ->firstWhere('rel', 'approve')['href'] ?? null;

        if (! $approveUrl) {
            throw new \RuntimeException('PayPal did not return an approval link.');
        }

        return [
            'subscription_id' => $subscriptionId,
            'approve_url' => $approveUrl,
            'raw' => $subscription,
        ];
    }

    /**
     * Activate a subscription after the user returns from PayPal approval.
     *
     * @throws Throwable
     */
    public function activateFromReturn(string $subscriptionId): User
    {
        $pending = Cache::get($this->pendingCacheKey($subscriptionId), []);
        $currency = strtoupper($pending['currency'] ?? config('paypal.currency', 'USD'));
        $provider = $this->provider($currency);

        $details = $provider->showSubscriptionDetails($subscriptionId);
        if ($error = data_get($details, 'error')) {
            throw new \RuntimeException(
                data_get($error, 'details.0.description')
                    ?? data_get($error, 'message')
                    ?? 'Unable to verify PayPal subscription.'
            );
        }

        $status = strtoupper((string) data_get($details, 'status', ''));

        if ($status === 'APPROVED') {
            $provider->activateSubscription($subscriptionId, 'User returned from PayPal approval');
            $details = $provider->showSubscriptionDetails($subscriptionId);
            $status = strtoupper((string) data_get($details, 'status', $status));
        }

        if (! in_array($status, ['ACTIVE', 'APPROVED'], true)) {
            throw new \RuntimeException("PayPal subscription is not active yet (status: {$status}).");
        }

        $user = $this->activateLocalSubscription($details, $pending);

        Cache::forget($this->pendingCacheKey($subscriptionId));

        return $user;
    }

    /**
     * Cancel an active PayPal subscription at period end (stop renewals).
     *
     * @throws Throwable
     */
    public function cancelSubscription(User $user, string $reason = 'User requested cancellation'): void
    {
        $subscription = Subscription::where('user_id', $user->id)
            ->where('payment_method', 'paypal')
            ->whereIn('stripe_status', ['active', 'trialing'])
            ->latest()
            ->first();

        if (! $subscription || ! $subscription->stripe_id) {
            throw new \RuntimeException('No active PayPal subscription found.');
        }

        $currency = strtoupper(data_get($user->metadata, 'currency', config('paypal.currency', 'USD')));
        $provider = $this->provider($currency);

        $details = $provider->showSubscriptionDetails($subscription->stripe_id);
        $endsAt = $this->resolvePeriodEnd(is_array($details) ? $details : [], $user);

        $response = $provider->cancelSubscription($subscription->stripe_id, $reason);
        if ($error = data_get($response, 'error')) {
            $errorName = strtoupper((string) (
                data_get($error, 'name')
                ?? data_get($response, 'name')
                ?? ''
            ));

            if (! in_array($errorName, ['RESOURCE_NOT_FOUND', 'SUBSCRIPTION_STATUS_INVALID'], true)) {
                throw new \RuntimeException(
                    data_get($error, 'details.0.description')
                        ?? data_get($error, 'message')
                        ?? 'Unable to cancel PayPal subscription.'
                );
            }
        }

        $subscription->update([
            'stripe_status' => 'canceled',
            'ends_at' => $endsAt,
        ]);

        $user->update([
            'subscription_status' => 'canceled',
            'subscription_expires_at' => $endsAt,
        ]);
    }

    public function getSubscription(string $subscriptionId): array
    {
        $details = $this->provider((string) config('paypal.currency', 'USD'))
            ->showSubscriptionDetails($subscriptionId);

        return is_array($details) ? $details : [];
    }

    public function getPlan(string $planId): array
    {
        $details = $this->provider((string) config('paypal.currency', 'USD'))
            ->showPlanDetails($planId);

        return is_array($details) ? $details : [];
    }

    public function listTransactions(array $query): array
    {
        $provider = $this->provider((string) config('paypal.currency', 'USD'));

        $page = (int) ($query['page'] ?? 1);
        $pageSize = (int) ($query['page_size'] ?? 100);
        $fields = (string) ($query['fields'] ?? 'all');
        $filters = array_diff_key($query, array_flip(['page', 'page_size', 'fields']));

        if (method_exists($provider, 'setCurrentPage')) {
            $provider->setCurrentPage($page);
        }
        if (method_exists($provider, 'setPageSize')) {
            $provider->setPageSize($pageSize);
        }

        if (! method_exists($provider, 'listTransactions')) {
            return [];
        }

        $firstType = (string) ((new \ReflectionMethod($provider, 'listTransactions'))->getParameters()[0]?->getType() ?: '');
        $response = $firstType === 'string'
            ? $provider->listTransactions($fields, $filters)
            : $provider->listTransactions($filters, $fields);

        return is_array($response) ? $response : [];
    }

    public function createProduct(array $data): array
    {
        $result = $this->provider((string) config('paypal.currency', 'USD'))->createProduct($data);

        return is_array($result) ? $result : [];
    }

    public function createPlan(array $data): array
    {
        $result = $this->provider((string) config('paypal.currency', 'USD'))->createPlan($data);

        return is_array($result) ? $result : [];
    }

    /**
     * Handle PayPal webhook events for subscriptions and sales.
     */
    public function handleWebhook(array $payload): void
    {
        $eventType = data_get($payload, 'event_type');
        $resource = data_get($payload, 'resource', []);

        Log::info('PayPal webhook received', ['event_type' => $eventType]);

        match ($eventType) {
            'BILLING.SUBSCRIPTION.ACTIVATED' => $this->handleSubscriptionActivated($resource),
            'BILLING.SUBSCRIPTION.UPDATED' => $this->handleSubscriptionUpdated($resource),
            'BILLING.SUBSCRIPTION.CANCELLED',
            'BILLING.SUBSCRIPTION.EXPIRED' => $this->handleSubscriptionCancelled($resource),
            'BILLING.SUBSCRIPTION.SUSPENDED' => $this->handleSubscriptionSuspended($resource),
            'BILLING.SUBSCRIPTION.PAYMENT.FAILED' => $this->handleSubscriptionPaymentFailed($resource),
            'PAYMENT.SALE.COMPLETED',
            'PAYMENT.CAPTURE.COMPLETED' => $this->handleSaleCompleted($resource),
            default => null,
        };
    }

    protected function handleSubscriptionActivated(array $resource): void
    {
        $subscriptionId = data_get($resource, 'id');
        if (! $subscriptionId) {
            return;
        }

        try {
            $details = $this->getSubscription($subscriptionId);
            if (data_get($details, 'id')) {
                $resource = array_merge($resource, $details);
            }

            $pending = Cache::get($this->pendingCacheKey($subscriptionId), []);
            $this->activateLocalSubscription($resource, $pending);
            Cache::forget($this->pendingCacheKey($subscriptionId));
        } catch (Throwable $e) {
            Log::error('PayPal activation from webhook failed', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function handleSubscriptionUpdated(array $resource): void
    {
        $status = strtoupper((string) data_get($resource, 'status', ''));

        match ($status) {
            'CANCELLED', 'EXPIRED' => $this->handleSubscriptionCancelled($resource),
            'SUSPENDED' => $this->handleSubscriptionSuspended($resource),
            default => $this->handleSubscriptionActivated($resource),
        };
    }

    protected function handleSubscriptionCancelled(array $resource): void
    {
        $subscriptionId = data_get($resource, 'id');
        if (! $subscriptionId) {
            return;
        }

        $subscription = $this->findLocalSubscription($subscriptionId);
        if (! $subscription) {
            return;
        }

        $endsAt = $this->resolvePeriodEnd($resource, $subscription->user)
            ?? $subscription->ends_at
            ?? now();

        $subscription->update([
            'stripe_status' => 'canceled',
            'ends_at' => $endsAt,
        ]);

        optional($subscription->user)->update([
            'subscription_status' => 'canceled',
            'subscription_expires_at' => $endsAt,
        ]);
    }

    protected function handleSubscriptionSuspended(array $resource): void
    {
        $subscriptionId = data_get($resource, 'id');
        if (! $subscriptionId) {
            return;
        }

        $subscription = $this->findLocalSubscription($subscriptionId);
        if (! $subscription) {
            return;
        }

        $subscription->update([
            'stripe_status' => 'past_due',
        ]);

        optional($subscription->user)->update([
            'subscription_status' => 'past_due',
            'payment_status' => 'pending',
        ]);
    }

    protected function handleSubscriptionPaymentFailed(array $resource): void
    {
        $subscriptionId = data_get($resource, 'id')
            ?? data_get($resource, 'billing_agreement_id');

        if (! $subscriptionId) {
            return;
        }

        $subscription = $this->findLocalSubscription($subscriptionId);
        if (! $subscription) {
            return;
        }

        $subscription->update([
            'stripe_status' => 'past_due',
        ]);

        optional($subscription->user)->update([
            'subscription_status' => 'past_due',
            'payment_status' => 'failed',
        ]);
    }

    protected function handleSaleCompleted(array $resource): void
    {
        $subscriptionId = data_get($resource, 'billing_agreement_id')
            ?? data_get($resource, 'supplementary_data.related_ids.billing_agreement_id');

        $subscription = $subscriptionId
            ? $this->findLocalSubscription($subscriptionId)
            : null;

        if (! $subscription) {
            $userId = data_get($resource, 'custom_id');
            if ($userId) {
                $subscription = Subscription::where('user_id', $userId)
                    ->where('payment_method', 'paypal')
                    ->latest()
                    ->first();
            }
        }

        if (! $subscription || ! $subscription->user) {
            return;
        }

        $user = $subscription->user;
        $amount = (float) (
            data_get($resource, 'amount.total')
            ?? data_get($resource, 'amount.value')
            ?? 0
        );
        $currency = strtoupper((string) (
            data_get($resource, 'amount.currency')
            ?? data_get($resource, 'amount.currency_code')
            ?? 'USD'
        ));
        $reference = (string) data_get($resource, 'id', Str::uuid());
        $duration = data_get($user->metadata, 'duration', 'monthly');
        $endsAt = $this->periodEndFromDuration($duration);

        $subscription->update([
            'stripe_status' => 'active',
            'ends_at' => null,
        ]);

        $user->update([
            'payment_status' => 'successful',
            'payment_method' => 'paypal',
            'subscription_status' => 'active',
            'subscription_expires_at' => $endsAt,
            'last_payment_reference' => $reference,
            'last_payment_amount' => $amount,
            'last_payment_at' => now(),
            'premium' => $this->isPremiumTier(data_get($user->metadata, 'tier')),
        ]);

        $this->recordPayment($user, $amount, $currency, $reference, $endsAt, [
            'paypal_sale_id' => $reference,
            'subscription_id' => $subscription->stripe_id,
            'event' => 'PAYMENT.SALE.COMPLETED',
        ]);
    }

    /**
     * Idempotently activate the local subscription + user entitlement fields.
     */
    protected function activateLocalSubscription(array $details, array $pending = []): User
    {
        $subscriptionId = data_get($details, 'id');
        $user = $this->resolveUserFromPayPal($details, $pending);

        if (! $user) {
            throw new \RuntimeException('Unable to match PayPal subscription to a user.');
        }

        $plan = isset($pending['plan_id'])
            ? Plan::find($pending['plan_id'])
            : null;

        $paypalPlanId = data_get($details, 'plan_id');
        if (! $plan && $paypalPlanId) {
            $plan = Plan::all()->first(
                fn (Plan $candidate) => collect($candidate->paypal_plan_ids)->contains($paypalPlanId)
            );
        }

        $tier = $pending['tier']
            ?? data_get($user->metadata, 'tier')
            ?? ($plan?->tier ?? 'standard');

        $duration = $pending['duration']
            ?? data_get($user->metadata, 'duration')
            ?? ($plan?->type ?? 'monthly');

        $currency = strtoupper(
            $pending['currency']
                ?? data_get($user->metadata, 'currency')
                ?? config('paypal.currency', 'USD')
        );

        $amount = $pending['amount']
            ?? ($plan ? $this->amountFor($plan, $currency) : null)
            ?? $user->last_payment_amount
            ?? 0;

        $startedAt = now();
        $expiresAt = $this->resolvePeriodEnd($details, $user)
            ?? $this->periodEndFromDuration($duration);
        $status = $this->mapStatus((string) data_get($details, 'status', 'ACTIVE'));

        return DB::transaction(function () use (
            $user,
            $subscriptionId,
            $paypalPlanId,
            $plan,
            $tier,
            $duration,
            $currency,
            $amount,
            $startedAt,
            $expiresAt,
            $status,
            $details
        ) {
            Subscription::where('user_id', $user->id)
                ->where('payment_method', 'paypal')
                ->where('stripe_status', 'incomplete')
                ->where('stripe_id', '!=', $subscriptionId)
                ->delete();

            Subscription::updateOrCreate(
                ['stripe_id' => $subscriptionId],
                [
                    'user_id' => $user->id,
                    'type' => 'default',
                    'stripe_status' => $status,
                    'stripe_price' => $paypalPlanId ?? $plan?->id,
                    'quantity' => 1,
                    'trial_ends_at' => null,
                    'ends_at' => null,
                    'payment_method' => 'paypal',
                    'plan_code' => $plan?->id,
                    'subscription_code' => $subscriptionId,
                ]
            );

            $user->update([
                'payment_method' => 'paypal',
                'payment_status' => $status === 'active' ? 'successful' : 'failed',
                'premium' => $this->isPremiumTier($tier),
                'plan' => $plan?->id,
                'amount' => $amount,
                'subscription_type' => $duration,
                'subscription_status' => $status,
                'subscription_started_at' => $startedAt,
                'subscription_expires_at' => $expiresAt,
                'last_payment_reference' => $subscriptionId,
                'last_payment_amount' => $amount,
                'last_payment_at' => $startedAt,
                'metadata' => array_merge($user->metadata ?? [], [
                    'tier' => $tier,
                    'duration' => $duration,
                    'currency' => $currency,
                    'plan_id' => $plan?->id,
                    'paypal_subscription_id' => $subscriptionId,
                ]),
            ]);

            $this->recordPayment($user, (float) $amount, $currency, $subscriptionId, $expiresAt, [
                'paypal_subscription' => data_get($details, 'id'),
                'tier' => $tier,
                'duration' => $duration,
            ]);

            return $user->fresh();
        });
    }

    /**
     * Create any missing PayPal product + USD/EUR billing plans for a local plan.
     *
     * @throws Throwable
     */
    public function ensureBillingPlans(Plan $plan): Plan
    {
        $duration = (string) $plan->type;

        foreach (['USD', 'EUR'] as $currency) {
            $amount = $this->amountFor($plan, $currency);
            if ($amount === null || $amount <= 0) {
                continue;
            }

            $plan = $plan->fresh();
            $this->resolvePayPalPlanId(
                $this->provider($currency),
                $plan,
                $currency,
                $duration,
                $amount
            );
        }

        return $plan->fresh();
    }

    /**
     * Create or reuse a PayPal product + billing plan for this local plan/currency.
     *
     * @throws Throwable
     */
    protected function resolvePayPalPlanId(
        PayPalClient $provider,
        Plan $plan,
        string $currency,
        string $duration,
        float $amount
    ): string {
        $planIds = is_array($plan->paypal_plan_ids) ? $plan->paypal_plan_ids : [];
        if (! empty($planIds[$currency])) {
            return $planIds[$currency];
        }

        if (! $plan->paypal_product_id) {
            $product = $provider->createProduct([
                'name' => substr($this->planDisplayName($plan), 0, 127),
                'description' => substr($this->planDisplayName($plan).' membership', 0, 256),
                'type' => 'SERVICE',
                'category' => 'SOFTWARE',
            ]);

            if ($error = data_get($product, 'error')) {
                throw new \RuntimeException(
                    data_get($error, 'details.0.description')
                        ?? data_get($error, 'message')
                        ?? 'Failed to create PayPal product.'
                );
            }

            $productId = data_get($product, 'id');
            if (! $productId) {
                throw new \RuntimeException('PayPal product id missing.');
            }

            $plan->paypal_product_id = $productId;
            $plan->save();
        }

        $interval = match ($duration) {
            'monthly' => ['unit' => 'MONTH', 'count' => 1],
            'quarterly' => ['unit' => 'MONTH', 'count' => 3],
            'yearly' => ['unit' => 'YEAR', 'count' => 1],
            default => throw new \InvalidArgumentException("Unsupported duration: {$duration}"),
        };

        $created = $provider->createPlan([
            'product_id' => $plan->paypal_product_id,
            'name' => substr($this->planDisplayName($plan)." ({$currency})", 0, 127),
            'description' => substr("Recurring {$duration} subscription", 0, 127),
            'status' => 'ACTIVE',
            'billing_cycles' => [[
                'frequency' => [
                    'interval_unit' => $interval['unit'],
                    'interval_count' => $interval['count'],
                ],
                'tenure_type' => 'REGULAR',
                'sequence' => 1,
                'total_cycles' => 0,
                'pricing_scheme' => [
                    'fixed_price' => [
                        'value' => number_format($amount, 2, '.', ''),
                        'currency_code' => $currency,
                    ],
                ],
            ]],
            'payment_preferences' => [
                'auto_bill_outstanding' => true,
                'setup_fee' => [
                    'value' => '0',
                    'currency_code' => $currency,
                ],
                'setup_fee_failure_action' => 'CONTINUE',
                'payment_failure_threshold' => 3,
            ],
            'taxes' => [
                'percentage' => '0',
                'inclusive' => false,
            ],
        ]);

        if ($error = data_get($created, 'error')) {
            throw new \RuntimeException(
                data_get($error, 'details.0.description')
                    ?? data_get($error, 'message')
                    ?? 'Failed to create PayPal billing plan.'
            );
        }

        $billingPlanId = data_get($created, 'id');
        if (! $billingPlanId) {
            throw new \RuntimeException('PayPal billing plan id missing.');
        }

        $planIds[$currency] = $billingPlanId;
        $plan->paypal_plan_ids = $planIds;
        $plan->save();

        return $billingPlanId;
    }

    protected function provider(string $currency): PayPalClient
    {
        $credentials = config('paypal');
        $mode = $credentials['mode'] ?? 'sandbox';
        $clientId = data_get($credentials, "{$mode}.client_id");
        $clientSecret = data_get($credentials, "{$mode}.client_secret");

        if (! $clientId || ! $clientSecret) {
            throw new \RuntimeException("PayPal {$mode} credentials are not configured.");
        }

        $provider = new PayPalClient;
        $provider->setApiCredentials($credentials);
        $provider->getAccessToken();
        $provider->setCurrency(strtoupper($currency));

        return $provider;
    }

    public function amountFor(Plan $plan, string $currency): ?float
    {
        return match (strtoupper($currency)) {
            'USD' => $plan->price_usd !== null ? (float) $plan->price_usd : null,
            'EUR' => $plan->price_eur !== null ? (float) $plan->price_eur : null,
            'NGN' => $plan->price_ngn !== null ? (float) $plan->price_ngn : null,
            default => null,
        };
    }

    public function isPremiumTier($tier): bool
    {
        return stripos((string) $tier, 'premium') !== false;
    }

    protected function planDisplayName(Plan $plan): string
    {
        $tier = ucwords(str_replace('-', ' ', (string) $plan->tier));
        $type = ucfirst((string) $plan->type);

        return trim("{$tier} {$type}");
    }

    protected function periodEndFromDuration(string $duration): Carbon
    {
        return match ($duration) {
            'monthly' => now()->addMonth(),
            'quarterly' => now()->addMonths(3),
            'yearly' => now()->addYear(),
            default => now()->addMonth(),
        };
    }

    protected function resolvePeriodEnd(array $details, ?User $user = null): ?Carbon
    {
        $nextBilling = data_get($details, 'billing_info.next_billing_time');
        if ($nextBilling) {
            return Carbon::parse($nextBilling);
        }

        $duration = data_get($user?->metadata, 'duration', 'monthly');

        return $this->periodEndFromDuration($duration);
    }

    protected function resolveUserFromPayPal(array $details, array $pending = []): ?User
    {
        $userId = data_get($pending, 'user_id') ?: data_get($details, 'custom_id');
        if ($userId) {
            $user = User::find($userId);
            if ($user) {
                return $user;
            }
        }

        $subscriptionId = data_get($details, 'id');
        if ($subscriptionId) {
            $subscription = $this->findLocalSubscription($subscriptionId);
            if ($subscription?->user) {
                return $subscription->user;
            }
        }

        $email = data_get($details, 'subscriber.email_address');
        if ($email) {
            return User::where('email', $email)->first();
        }

        return null;
    }

    protected function findLocalSubscription(string $subscriptionId): ?Subscription
    {
        return Subscription::where('payment_method', 'paypal')
            ->where(function ($query) use ($subscriptionId) {
                $query->where('stripe_id', $subscriptionId)
                    ->orWhere('subscription_code', $subscriptionId);
            })
            ->first();
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

    protected function recordPayment(
        User $user,
        float $amount,
        string $currency,
        string $reference,
        Carbon $endsAt,
        array $metadata = []
    ): void {
        $existing = Payment::where('reference', $reference)->first();
        if ($existing) {
            return;
        }

        Payment::create([
            'user_id' => $user->id,
            'reference' => $reference,
            'amount' => $amount,
            'status' => 'successful',
            'payment_method' => 'paypal',
            'starts_at' => now()->toDateString(),
            'ends_at' => $endsAt->toDateString(),
            'metadata' => array_merge($metadata, [
                'currency' => $currency,
            ]),
        ]);
    }

    protected function pendingCacheKey(string $subscriptionId): string
    {
        return "paypal_subscription:{$subscriptionId}";
    }
}
