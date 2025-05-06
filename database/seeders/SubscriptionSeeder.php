<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subscription;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subscriptions = [
            [
                'name' => 'Monthly Membership',
                'amount_naria' => 37000,
                'amount_dollar' => 25,
                'duration' => '1 month',
            ],
            [
                'name' => 'Yearly Membership',
                'amount_naria' => 13000,
                'amount_dollar' => 200 ,
                'duration' => '1 year',
            ],
           
        ];

        foreach ($subscriptions as $plan) {
            Subscription::create($plan);
        }
    }
}
