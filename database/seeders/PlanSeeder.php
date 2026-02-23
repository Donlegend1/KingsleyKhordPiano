<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run()
    {
        $plans = [
            [
                'tier' => 'standard',
                'type' => 'monthly',
                'price_ngn' => 38000,
                'price_usd' => 26,
                'price_eur' => 23,
                'image' => '/icons/icon.png',
                'background' => '',
                'paystack_product_id' => 'PLN_brzt8hexwspqu9p',
                'stripe_product_id' => 'price_1SF0RQB0pqpbXiCilRF4qMc6'
            ],
            [
                'tier' => 'premium',
                'type' => 'monthly',
                'price_ngn' => 70000,
                'price_usd' => 45,
                'price_eur' => 40,
                'image' => '/icons/price2.png',
                'background' => '/images/Background.jpg',
                'paystack_product_id' => 'PLN_mb88lum57cm9dyy',
                'stripe_product_id' => 'price_1SF0RQB0pqpbXiCilRF4qMc7'
            ],
            [
                'tier' => 'standard',
                'type' => 'yearly',
                'price_ngn' => 320000,
                'price_usd' => 215,
                'price_eur' => 189,
                'image' => '/icons/icon.png',
                'background' => '',
                'paystack_product_id' => 'PLN_l4u5qel3amq3ukh',
                'stripe_product_id' => 'price_1SF0RQB0pqpbXiCilRF4qMc8'
            ],
            [
                'tier' => 'premium',
                'type' => 'yearly',
                'price_ngn' => 650000,
                'price_usd' => 420,
                'price_eur' => 369,
                'image' => '/icons/price2.png',
                'background' => '/images/Background.jpg',
                'paystack_product_id' => 'PLN_x7f11lzl66061dg',
                'stripe_product_id' => 'price_1SF0RQB0pqpbXiCilRF4qMc9'
            ],
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}
