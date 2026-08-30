<?php

namespace App\Listeners;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\SubscriptionLifecycleService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Events\WebhookReceived;

class HandleStripeSubscriptionLifecycle
{
    public function __construct(protected SubscriptionLifecycleService $lifecycle)
    {
    }

    public function handle(WebhookReceived $event): void
    {
        try {
            match ($event->payload['type'] ?? '') {
                'checkout.session.completed' => $this->onCheckoutCompleted($event->payload),
                'invoice.payment_succeeded' => $this->onInvoicePaid($event->payload),
                default => null,
            };
        } catch (\Throwable $e) {
            Log::error('Stripe subscription lifecycle handler failed', [
                'type' => $event->payload['type'] ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function onCheckoutCompleted(array $payload): void
    {
        $session = $payload['data']['object'] ?? [];
        if (($session['mode'] ?? '') !== 'subscription') {
            return;
        }

        $user = $this->resolveUserFromSession($session);
        if (! $user) {
            return;
        }

        $metadata = $session['metadata'] ?? [];
        $tier = $metadata['tier'] ?? data_get($user->metadata, 'tier', 'standard');
        $duration = $metadata['duration'] ?? data_get($user->metadata, 'duration', 'monthly');
        $plan = $this->resolvePlan($metadata, $session);
        $expiresAt = $this->expiresAtFromDuration($duration);

        $user->forceFill([
            'plan' => $plan?->id ?? $user->plan,
            'premium' => stripos((string) $tier, 'premium') !== false,
            'subscription_status' => 'active',
            'subscription_type' => $duration,
            'subscription_started_at' => $user->subscription_started_at ?? now(),
            'subscription_expires_at' => $expiresAt,
            'payment_status' => 'successful',
            'payment_method' => 'stripe',
            'last_payment_at' => now(),
            'last_payment_amount' => isset($session['amount_total']) ? $session['amount_total'] / 100 : $user->last_payment_amount,
            'metadata' => array_merge($user->metadata ?? [], [
                'tier' => $tier,
                'duration' => $duration,
                'plan_id' => $plan?->id,
                'currency' => strtoupper((string) ($session['currency'] ?? data_get($user->metadata, 'currency', 'USD'))),
            ]),
        ])->save();

        if (! empty($session['subscription'])) {
            Subscription::updateOrCreate(
                ['stripe_id' => $session['subscription']],
                [
                    'user_id' => $user->id,
                    'type' => 'default',
                    'stripe_status' => 'active',
                    'payment_method' => 'stripe',
                    'ends_at' => $expiresAt,
                ]
            );
        }

        $reference = (string) ($session['subscription'] ?? $session['id'] ?? '');
        $this->lifecycle->notifyActivated($user->fresh(), $reference, $plan);
    }

    protected function onInvoicePaid(array $payload): void
    {
        $invoice = $payload['data']['object'] ?? [];
        $reason = $invoice['billing_reason'] ?? '';
        $customerId = $invoice['customer'] ?? null;

        if (! $customerId) {
            return;
        }

        $user = User::where('stripe_id', $customerId)->first();
        if (! $user) {
            return;
        }

        $plan = $this->resolvePlanFromInvoice($invoice, $user);
        $periodEnd = data_get($invoice, 'lines.data.0.period.end')
            ?? $invoice['period_end']
            ?? null;

        if ($periodEnd) {
            $user->forceFill([
                'subscription_status' => 'active',
                'subscription_expires_at' => Carbon::createFromTimestamp((int) $periodEnd),
                'payment_status' => 'successful',
                'last_payment_at' => now(),
                'last_payment_amount' => isset($invoice['amount_paid']) ? $invoice['amount_paid'] / 100 : $user->last_payment_amount,
            ])->save();
        }

        $stripeSubscriptionId = (string) ($invoice['subscription'] ?? '');
        $invoiceId = (string) ($invoice['id'] ?? $stripeSubscriptionId);

        if ($reason === 'subscription_cycle') {
            $this->lifecycle->notifyRenewed($user->fresh(), $invoiceId, $plan);
            return;
        }

        if ($reason === 'subscription_create' && $stripeSubscriptionId) {
            $this->lifecycle->notifyActivated($user->fresh(), $stripeSubscriptionId, $plan);
        }
    }

    protected function resolveUserFromSession(array $session): ?User
    {
        $userId = data_get($session, 'metadata.user_id');
        if ($userId) {
            return User::find($userId);
        }

        $customerId = $session['customer'] ?? null;
        if ($customerId) {
            return User::where('stripe_id', $customerId)->first();
        }

        return null;
    }

    protected function resolvePlan(array $metadata, array $session): ?Plan
    {
        if (! empty($metadata['plan_id'])) {
            return Plan::find($metadata['plan_id']);
        }

        $priceId = data_get($session, 'line_items.data.0.price.id')
            ?: data_get($session, 'display_items.0.price.id');

        if ($priceId) {
            $plan = Plan::where('stripe_product_id', $priceId)->first();
            if ($plan) {
                return $plan;
            }
        }

        $tier = $metadata['tier'] ?? null;
        $duration = $metadata['duration'] ?? null;
        if ($tier && $duration) {
            return Plan::where('type', $duration)
                ->where('tier', $tier)
                ->first();
        }

        return null;
    }

    protected function resolvePlanFromInvoice(array $invoice, User $user): ?Plan
    {
        $priceId = data_get($invoice, 'lines.data.0.price.id')
            ?: data_get($invoice, 'lines.data.0.pricing.price_details.price');

        if ($priceId) {
            $plan = Plan::where('stripe_product_id', $priceId)->first();
            if ($plan) {
                return $plan;
            }
        }

        return $this->lifecycle->resolvePlan($user);
    }

    protected function expiresAtFromDuration(string $duration): Carbon
    {
        return match ($duration) {
            'quarterly', 'quarter' => now()->addMonths(3),
            'yearly', 'year' => now()->addYear(),
            default => now()->addMonth(),
        };
    }
}
