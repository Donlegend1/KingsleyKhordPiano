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
            ],
            [
                'tier' => 'premium',
                'type' => 'monthly',
                'price_ngn' => 70000,
                'price_usd' => 45,
                'price_eur' => 40,
                'image' => '/icons/price2.png',
                'background' => '/images/Background.jpg',
            ],
            [
                'tier' => 'standard',
                'type' => 'yearly',
                'price_ngn' => 320000,
                'price_usd' => 215,
                'price_eur' => 189,
                'image' => '/icons/icon.png',
                'background' => '',
            ],
            [
                'tier' => 'premium',
                'type' => 'yearly',
                'price_ngn' => 650000,
                'price_usd' => 420,
                'price_eur' => 369,
                'image' => '/icons/price2.png',
                'background' => '/images/Background.jpg',
            ],
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}

