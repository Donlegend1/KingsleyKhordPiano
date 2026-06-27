<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run()
    {
        // $plans = [
        //     [
        //         'tier' => 'standard',
        //         'type' => 'monthly',
        //         'price_ngn' => 38000,
        //         'price_usd' => 26,
        //         'price_eur' => 23,
        //         'image' => '/icons/icon.png',
        //         'background' => '',
        //         'paystack_product_id' => 'PLN_brzt8hexwspqu9p',
        //         'stripe_product_id' => 'price_1SF0RQB0pqpbXiCilRF4qMc6'
        //     ],
        //     [
        //         'tier' => 'premium',
        //         'type' => 'monthly',
        //         'price_ngn' => 70000,
        //         'price_usd' => 45,
        //         'price_eur' => 40,
        //         'image' => '/icons/price2.png',
        //         'background' => '/images/Background.jpg',
        //         'paystack_product_id' => 'PLN_mb88lum57cm9dyy',
        //         'stripe_product_id' => 'price_1SF0RQB0pqpbXiCilRF4qMc7'
        //     ],
        //     [
        //         'tier' => 'standard',
        //         'type' => 'yearly',
        //         'price_ngn' => 320000,
        //         'price_usd' => 215,
        //         'price_eur' => 189,
        //         'image' => '/icons/icon.png',
        //         'background' => '',
        //         'paystack_product_id' => 'PLN_l4u5qel3amq3ukh',
        //         'stripe_product_id' => 'price_1SF0RQB0pqpbXiCilRF4qMc8'
        //     ],
        //     [
        //         'tier' => 'premium',
        //         'type' => 'yearly',
        //         'price_ngn' => 650000,
        //         'price_usd' => 420,
        //         'price_eur' => 369,
        //         'image' => '/icons/price2.png',
        //         'background' => '/images/Background.jpg',
        //         'paystack_product_id' => 'PLN_x7f11lzl66061dg',
        //         'stripe_product_id' => 'price_1SF0RQB0pqpbXiCilRF4qMc9'
        //     ],
        // ];

        Plan::truncate(); // Clear existing records before seeding
         $plans = [
            [
                'tier' => 'standard',
                'type' => 'monthly',
                'price_ngn' => 40000,
                'price_usd' => 32,
                'price_eur' => 28,
                'image' => '/icons/icon.png',
                'background' => '',
                'paystack_product_id' => 'PLN_brzt8hexwspqu9p',
                'stripe_product_id' => 'price_1TmEw0B0pqpbXiCi4WJifhGG'
            ],
            [
                'tier' => 'premium',
                'type' => 'monthly',
                'price_ngn' => 78000,
                'price_usd' => 57,
                'price_eur' => 50,
                'image' => '/icons/price2.png',
                'background' => '/images/Background.jpg',
                'paystack_product_id' => 'PLN_mb88lum57cm9dyy',
                'stripe_product_id' => 'price_1TmF19B0pqpbXiCiG7cPdcU6'
            ],
           
            [
                'tier' => 'Standard 3-months',
                'type' => 'quarterly',
                'price_ngn' => 108000,
                'price_usd' => 85,
                'price_eur' => 75,
                'image' => '/icons/icon.png',
                'background' => '',
                'paystack_product_id' => 'PLN_l4u5qel3amq3ukh',
                'stripe_product_id' => 'price_1TmFAaB0pqpbXiCikJDqSE8T'
            ],
             [
                'tier' => 'Premium 3-months',
                'type' => 'quarterly',
                'price_ngn' => 210000,
                'price_usd' => 154,
                'price_eur' => 135,
                'image' => '/icons/price2.png',
                'background' => '/images/Background.jpg',
                'paystack_product_id' => 'PLN_mb88lum57cm9dyy',
                'stripe_product_id' => 'price_1TmFHFB0pqpbXiCiKso7J8tY'
            ],
             [
                'tier' => 'standard',
                'type' => 'yearly',
                'price_ngn' => 360000,
                'price_usd' => 268,
                'price_eur' => 235,
                'image' => '/icons/icon.png',
                'background' => '',
                'paystack_product_id' => 'PLN_l4u5qel3amq3ukh',
                'stripe_product_id' => 'price_1TmFUaB0pqpbXiCiQS6SALlX'
            ],
            [
                'tier' => 'premium',
                'type' => 'yearly',
                'price_ngn' => 650000,
                'price_usd' => 480,
                'price_eur' => 420,
                'image' => '/icons/price2.png',
                'background' => '/images/Background.jpg',
                'paystack_product_id' => 'PLN_x7f11lzl66061dg',
                'stripe_product_id' => 'price_1TmFapB0pqpbXiCiWwZpbUDc'
            ],
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}
