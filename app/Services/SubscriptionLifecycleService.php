<?php

namespace App\Services;

use App\Mail\SubscriptionLifecycleMail;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SubscriptionLifecycleService
{
    /**
     * Grant piano coaching only when the plan includes it.
     * Existing access is never revoked (legacy Premium members stay entitled).
     */
    public function syncCoachingAccess(User $user, ?Plan $plan = null): void
    {
        if ($user->can_access_coaching) {
            return;
        }

        $plan ??= $this->resolvePlan($user);

        if ($plan && $plan->includesCoaching()) {
            $user->forceFill(['can_access_coaching' => true])->save();
        }
    }

    public function notifyActivated(User $user, string $reference, ?Plan $plan = null): void
    {
        $plan ??= $this->resolvePlan($user);
        $this->syncCoachingAccess($user, $plan);
        $this->sendOnce($user, 'activated', $reference, $plan, false);
    }

    public function notifyRenewed(User $user, string $reference, ?Plan $plan = null): void
    {
        $plan ??= $this->resolvePlan($user);
        $this->syncCoachingAccess($user, $plan);
        $this->sendOnce($user, 'renewed', $reference, $plan, true);
    }

    public function resolvePlan(User $user): ?Plan
    {
        $planId = data_get($user->metadata, 'plan_id') ?: $user->getAttribute('plan');

        return $planId ? Plan::find($planId) : null;
    }

    protected function sendOnce(User $user, string $type, string $reference, ?Plan $plan, bool $isRenewal): void
    {
        if (! $user->email || ! $reference) {
            return;
        }

        $key = "subscription-mail:{$type}:{$user->id}:{$reference}";

        if (! Cache::add($key, 1, now()->addDays(45))) {
            return;
        }

        try {
            Mail::to($user->email)->send(
                new SubscriptionLifecycleMail($user->fresh(), $type, $plan, $isRenewal)
            );
        } catch (\Throwable $e) {
            Cache::forget($key);
            Log::error('Failed to send subscription lifecycle email', [
                'user_id' => $user->id,
                'type' => $type,
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
