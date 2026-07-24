<?php

namespace App\Services;

use App\Models\Subscription;
use Carbon\Carbon;

class SubscriptionService
{
    /**
     * Get the start and end dates of the user's current subscription billing cycle.
     *
     * @param \App\Models\User $user
     * @return array
     */
    public function getBillingCycleRange($user)
    {
        $now = now();

        // Find the active subscription from the subscriptions table
        $subscription = Subscription::where('user_id', $user->id)
        ->Where('stripe_status', 'active')
        ->latest()
        ->first();

        $endsAt = null;
        if ($subscription && $subscription->ends_at) {
            $endsAt = Carbon::parse($subscription->ends_at);
        }

        // Fallback to calendar month if no active subscription renewal date is found
        if (!$endsAt) {
            return [
                'start' => $now->startOfMonth()->toDateString(),
                'end' => $now->endOfMonth()->toDateString(),
                'reset_label' => $now->startOfMonth()->addMonthNoOverflow()->format('F j, Y'),
            ];
        }

        $day = $endsAt->day;

        // Construct cycle start in the current calendar month
        $currentMonthDays = $now->daysInMonth;
        $cycleStart = clone $now;
        $cycleStart->day(min($day, $currentMonthDays))->startOfDay();

        if ($now->lt($cycleStart)) {
            // Cycle started in the previous calendar month
            $prevMonth = clone $now;
            $prevMonth->subMonthNoOverflow();
            $prevMonthDays = $prevMonth->daysInMonth;

            $cycleStart = $prevMonth->day(min($day, $prevMonthDays))->startOfDay();
            $cycleEnd = (clone $cycleStart)->addMonthNoOverflow()->endOfDay();
        } else {
            // Cycle started in the current calendar month
            $cycleEnd = (clone $cycleStart)->addMonthNoOverflow()->endOfDay();
        }

        return [
            'start' => $cycleStart->toDateString(),
            'end' => $cycleEnd->toDateString(),
            'reset_label' => $cycleEnd->format('F j, Y'),
        ];
    }
}
