<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Subscription;
use App\Models\SubscriptionItem;
use League\Csv\Reader;
use Carbon\Carbon;

class ImportStripeDataCommand extends Command
{
    protected $signature = 'stripe:import';
    protected $description = 'Import and update users, subscriptions, and subscription_items from storage/app/subscriptions.csv';

    public function handle()
    {
        $filePath = storage_path('app/subscriptions.csv');

        if (!file_exists($filePath)) {
            $this->error("❌ File not found at: {$filePath}");
            return 1;
        }

        $this->info("📂 Reading file from: {$filePath}");

        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);
        $records = $csv->getRecords();

        $updated = 0;
        $skipped = 0;

        foreach ($records as $row) {
            try {
                $email = $row['Customer Email'] ?? null;
                $stripeCustomerId = $row['Customer ID'] ?? null;
                $subscriptionId = $row['id'] ?? null;
                $status = $row['Status'] ?? null;
                $priceId = $row['Plan'] ?? null;
                $quantity = $row['quantity'] ?? 1;
                $endsAt = $row['Current Period End (UTC)'] ?? null;

                if (!$email || !$stripeCustomerId) {
                    $this->warn("⚠️ Missing required fields (email or customer_id). Skipping...");
                    $skipped++;
                    continue;
                }

                $user = User::where('email', $email)->first();

                if (!$user) {
                    $this->warn("⚠️ No user found for {$email}. Skipping...");
                    $skipped++;
                    continue;
                }

                // 🔹 Update user's Stripe customer ID
                $user->update([
                    'stripe_id' => $stripeCustomerId,
                ]);

                // 🔹 Find or create subscription
                $subscription = Subscription::firstOrNew([
                    'user_id' => $user->id,
                    'stripe_id' => $subscriptionId,
                ]);

                $subscription->fill([
                    'type' => 'default',
                    'stripe_status' => $status ?? 'active',
                    'stripe_price' => $priceId,
                    'quantity' => $quantity,
                    'ends_at' => $endsAt ? Carbon::parse($endsAt) : null,
                ]);

                $subscription->save();

                // 🔹 Update subscription item
                if ($priceId) {
                    SubscriptionItem::updateOrCreate(
                        ['subscription_id' => $subscription->id, 'stripe_price' => $priceId],
                        [
                            'stripe_id' => $subscriptionId ?? null,
                            'quantity' => $quantity,
                            'stripe_price' => $priceId,
                            'stripe_product' => $row['Product']
                        ]
                    );
                }

                $this->info("✅ Synced subscription for {$email}");
                $updated++;

            } catch (\Throwable $e) {
                $this->error("❌ Error on : {$e->getMessage()}");
                $skipped++;
            }
        }

        $this->line('');
        $this->info("🎯 Import completed. Updated/Created: {$updated}, Skipped: {$skipped}");
        return 0;
    }
}
