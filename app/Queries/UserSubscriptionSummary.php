<?php

namespace App\Queries;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Every user row plus two derived columns, worked out from the user record,
 * their latest subscription and their payments:
 *   display_subscription_status  Active | Inactive | Canceled
 *   display_plan                 standard-monthly … premium-yearly | manual | none
 *
 * Used by the admin users list and the admin dashboard so both agree.
 */
class UserSubscriptionSummary
{
    public const PLAN_LABELS = [
        'standard-monthly' => 'Standard Monthly',
        'standard-quarterly' => 'Standard Quarterly',
        'standard-yearly' => 'Standard Yearly',
        'premium-monthly' => 'Premium Monthly',
        'premium-quarterly' => 'Premium Quarterly',
        'premium-yearly' => 'Premium Yearly',
        'manual' => 'Manual access',
        'none' => 'No subscription',
    ];

    public static function query(): Builder
    {
        $latest = Subscription::query()->whereColumn('subscriptions.user_id', 'users.id')
            ->orderByDesc('subscriptions.created_at')->orderByDesc('subscriptions.id')->limit(1);
        $source = User::query()->leftJoin('plans', 'plans.id', '=', 'users.plan')->select('users.*');
        $grammar = $source->getQuery()->getGrammar();
        $durationMeta = $grammar->wrap('users.metadata->duration');
        $tierMeta = $grammar->wrap('users.metadata->tier');
        $status = (clone $latest)->select('stripe_status');
        $method = (clone $latest)->select('payment_method');
        // Older databases have a subscriptions.duration column; newer ones don't.
        $subscriptionDuration = Schema::hasColumn('subscriptions', 'duration')
            ? '(' . (clone $latest)->select('duration')->toSql() . '), '
            : '';

        $source->selectRaw("LOWER(COALESCE(users.subscription_status, ({$status->toSql()}), '')) as effective_status")
            ->selectRaw("LOWER(COALESCE({$durationMeta}, {$subscriptionDuration}users.subscription_type, plans.type, '')) as plan_interval")
            ->selectRaw("LOWER(COALESCE({$tierMeta}, plans.tier, CASE WHEN users.premium THEN 'premium' ELSE 'standard' END)) as plan_tier")
            ->selectRaw("LOWER(COALESCE(users.payment_method, ({$method->toSql()}), '')) as plan_method");
        $now = now();

        return DB::query()->fromSub($source, 'accounts')->select('accounts.*')->selectRaw("
            CASE
                WHEN effective_status IN ('canceled', 'cancelled') THEN 'Canceled'
                WHEN effective_status = 'paused' OR subscription_started_at > ? THEN 'Inactive'
                WHEN EXISTS (
                    SELECT 1 FROM subscriptions WHERE subscriptions.user_id = accounts.id AND (
                        (stripe_status IN ('active', 'trialing') AND (ends_at IS NULL OR ends_at > ?))
                        OR (stripe_status IN ('canceled', 'cancelled') AND ends_at > ?)
                    )
                ) OR (
                    subscription_expires_at > ?
                    AND effective_status NOT IN ('past_due', 'expired', 'failed', 'incomplete', 'pending')
                    AND (effective_status IN ('active', 'trialing') OR payment_status = 'successful')
                ) OR EXISTS (
                    SELECT 1 FROM payments WHERE payments.user_id = CAST(accounts.id AS CHAR)
                    AND payments.status = 'successful' AND payments.ends_at > ?
                ) THEN 'Active'
                ELSE 'Inactive'
            END as display_subscription_status
        ", [$now, $now, $now, $now, $now])->selectRaw("
            CASE
                WHEN plan_interval IN ('month', 'monthly') THEN CASE WHEN plan_tier LIKE '%premium%' THEN 'premium-monthly' ELSE 'standard-monthly' END
                WHEN plan_interval IN ('quarter', 'quarterly') THEN CASE WHEN plan_tier LIKE '%premium%' THEN 'premium-quarterly' ELSE 'standard-quarterly' END
                WHEN plan_interval IN ('year', 'yearly', 'annual') THEN CASE WHEN plan_tier LIKE '%premium%' THEN 'premium-yearly' ELSE 'standard-yearly' END
                WHEN plan_interval = 'manual' OR plan_method = 'manual' THEN 'manual'
                ELSE 'none'
            END as display_plan
        ");
    }
}
