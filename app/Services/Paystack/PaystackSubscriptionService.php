<?php

namespace App\Services\Paystack;

use App\Models\Subscription;
use App\Models\SubscriptionItem;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Throwable;

class PaystackSubscriptionService
{
    /**
     * Store or update a Paystack subscription and its items
     *
     * @throws Throwable
     */
    public function store(User $user, array $data): Subscription
    {
        try {
            return DB::transaction(function () use ($user, $data) {

                $subscription = Subscription::updateOrCreate(
                    [
                        'user_id' => $user->id,
                    ],
                    [
                        'plan_code'          => Arr::get($data, 'plan'),
                        'subscription_code'  => Arr::get($data, 'subscription.subscription_code'),
                        'email_token'        => Arr::get($data, 'subscription.email_token'),
                        'authorization_code' => Arr::get($data, 'authorization.authorization_code'),
                        'end_date'           => Arr::get($data, 'period_end'),
                        'type'               => 'default',
                        'status'             => 'active',
                    ]
                );
      
                    SubscriptionItem::updateOrCreate(
                        [
                          'subscription_id' => $subscription->id,
                        ],
                      ['subscription_id' => $subscription->id,]
                    );

                return $subscription;
            });
        } catch (Throwable $e) {
            logger()->error('Paystack subscription storage failed', [
                'user_id' => $user->id,
                'payload' => $data,
                'error'   => $e->getMessage(),
            ]);

            throw $e; 
        }
    }
}
