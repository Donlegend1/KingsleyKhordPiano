<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Services\PayPalService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class PayPalPlanSeeder extends Seeder
{
    /**
     * Upserts local plans from the production dump, then creates any missing
     * PayPal product + USD/EUR billing plan IDs on those rows.
     *
     * Run with: php artisan db:seed --class=PayPalPlanSeeder
     */
    public function run(PayPalService $paypal): void
    {
        foreach ($this->plans() as $row) {
            $plan = Plan::updateOrCreate(
                ['id' => $row['id']],
                [
                    'tier' => $row['tier'],
                    'type' => $row['type'],
                    'price_ngn' => $row['price_ngn'],
                    'price_usd' => $row['price_usd'],
                    'price_eur' => $row['price_eur'],
                    'image' => $row['image'],
                    'background' => $row['background'],
                    'paystack_product_id' => $row['paystack_product_id'],
                    'stripe_product_id' => $row['stripe_product_id'],
                    'paypal_product_id' => $row['paypal_product_id'],
                    'paypal_plan_ids' => $row['paypal_plan_ids'],
                    'product_id' => $row['product_id'],
                    'price_id' => $row['price_id'],
                    'agent' => $row['agent'],
                ]
            );

            $this->command->info("Ensuring PayPal IDs for {$plan->tier} {$plan->type} (#{$plan->id})");

            try {
                $plan = $paypal->ensureBillingPlans($plan);
            } catch (\Throwable $e) {
                Log::error('PayPal plan seed failed', [
                    'plan_id' => $plan->id,
                    'error' => $e->getMessage(),
                ]);
                $this->command->error("Failed for plan #{$plan->id}: {$e->getMessage()}");
                continue;
            }

            $ids = is_array($plan->paypal_plan_ids) ? $plan->paypal_plan_ids : [];
            $this->command->info(
                "  product={$plan->paypal_product_id} USD=".($ids['USD'] ?? 'missing').' EUR='.($ids['EUR'] ?? 'missing')
            );
        }
    }

    /**
     * Source: database/plans (6).sql
     */
    protected function plans(): array
    {
        return [
            [
                'id' => 1,
                'tier' => 'standard',
                'type' => 'monthly',
                'price_ngn' => 38000.00,
                'price_usd' => 28.00,
                'price_eur' => 25.00,
                'image' => '/icons/icon.png',
                'background' => '',
                'paystack_product_id' => 'PLN_brzt8hexwspqu9p',
                'stripe_product_id' => 'price_1TosNAB0pqpbXiCi02nYWMfZ',
                'paypal_product_id' => 'PROD-3PK11385EB4507523',
                'paypal_plan_ids' => [
                    'EUR' => 'P-6DN4031633408434XNJ3DBAA',
                    'USD' => 'P-8ND1915612609040FNJ3C7NI',
                ],
                'product_id' => 'price_1TmEw0B0pqpbXiCi4WJifhGG',
                'price_id' => null,
                'agent' => null,
            ],
            [
                'id' => 2,
                'tier' => 'premium',
                'type' => 'monthly',
                'price_ngn' => 78000.00,
                'price_usd' => 57.00,
                'price_eur' => 50.00,
                'image' => '/icons/price2.png',
                'background' => '/images/Background.jpg',
                'paystack_product_id' => 'PLN_mb88lum57cm9dyy',
                'stripe_product_id' => 'price_1TmF19B0pqpbXiCiG7cPdcU6',
                'paypal_product_id' => 'PROD-9GY71630L44257120',
                'paypal_plan_ids' => [
                    'EUR' => 'P-4NU94981AR335624PNJ7ZI3Y',
                    'USD' => 'P-4PT51499LD5653846NJ7ZI3A',
                ],
                'product_id' => null,
                'price_id' => null,
                'agent' => null,
            ],
            [
                'id' => 3,
                'tier' => 'Standard 3-months',
                'type' => 'quarterly',
                'price_ngn' => 105000.00,
                'price_usd' => 75.00,
                'price_eur' => 68.00,
                'image' => '/icons/icon.png',
                'background' => '',
                'paystack_product_id' => 'PLN_zo5n17vwoeom6is',
                'stripe_product_id' => 'price_1TmFAaB0pqpbXiCikJDqSE8T',
                'paypal_product_id' => 'PROD-54R39287UT532922V',
                'paypal_plan_ids' => [
                    'EUR' => 'P-4DG0560861569861WNJ7ZI5Q',
                    'USD' => 'P-34A21887C3695471TNJ7ZI4Y',
                ],
                'product_id' => null,
                'price_id' => null,
                'agent' => null,
            ],
            [
                'id' => 4,
                'tier' => 'Premium 3-months',
                'type' => 'quarterly',
                'price_ngn' => 210000.00,
                'price_usd' => 154.00,
                'price_eur' => 135.00,
                'image' => '/icons/price2.png',
                'background' => '/images/Background.jpg',
                'paystack_product_id' => 'PLN_6x7ktvd81doif9t',
                'stripe_product_id' => 'price_1TmFHFB0pqpbXiCiKso7J8tY',
                'paypal_product_id' => 'PROD-7CB464156J138843V',
                'paypal_plan_ids' => [
                    'EUR' => 'P-2EP0740883223181DNJ7ZI7A',
                    'USD' => 'P-9UA919582U8783601NJ7ZI6I',
                ],
                'product_id' => null,
                'price_id' => null,
                'agent' => null,
            ],
            [
                'id' => 5,
                'tier' => 'standard',
                'type' => 'yearly',
                'price_ngn' => 330000.00,
                'price_usd' => 235.00,
                'price_eur' => 210.00,
                'image' => '/icons/icon.png',
                'background' => '',
                'paystack_product_id' => 'PLN_l4u5qel3amq3ukh',
                'stripe_product_id' => 'price_1TmFUaB0pqpbXiCiQS6SALlX',
                'paypal_product_id' => 'PROD-9KA514689H476474Y',
                'paypal_plan_ids' => [
                    'EUR' => 'P-6S718815GF0623509NJ7ZJAY',
                    'USD' => 'P-32629278E85476021NJ7ZJAA',
                ],
                'product_id' => null,
                'price_id' => null,
                'agent' => null,
            ],
            [
                'id' => 6,
                'tier' => 'premium',
                'type' => 'yearly',
                'price_ngn' => 650000.00,
                'price_usd' => 480.00,
                'price_eur' => 420.00,
                'image' => '/icons/price2.png',
                'background' => '/images/Background.jpg',
                'paystack_product_id' => 'PLN_x7f11lzl66061dg',
                'stripe_product_id' => 'price_1TmFapB0pqpbXiCiWwZpbUDc',
                'paypal_product_id' => 'PROD-37M234696R653872D',
                'paypal_plan_ids' => [
                    'EUR' => 'P-8DL36764PT466270CNJ7ZJCQ',
                    'USD' => 'P-4362833433754532DNJ7ZJBY',
                ],
                'product_id' => null,
                'price_id' => null,
                'agent' => null,
            ],
        ];
    }
}
