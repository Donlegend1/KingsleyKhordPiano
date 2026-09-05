<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run()
    {

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
                'stripe_product_id' => 'price_1U6bmA4msUcQGPMTmFTHw6xQ'
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
                'stripe_product_id' => 'price_1U6eaA4msUcQGPMTQdughSSf'
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
                'stripe_product_id' => 'price_1U6boy4msUcQGPMT5lcwSnYn'
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
                'stripe_product_id' => 'price_1U6eby4msUcQGPMTjxi05CVa'
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
                'stripe_product_id' => 'price_1U6bpd4msUcQGPMTMuQYaKoK'
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
                'stripe_product_id' => 'price_1U6egB4msUcQGPMT0A6d1fhC'
            ],
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}
