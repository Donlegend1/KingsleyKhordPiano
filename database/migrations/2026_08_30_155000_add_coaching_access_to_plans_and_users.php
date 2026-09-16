<?php

use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * New Premium (no piano coaching): ₦78,000 / $45 / €39 monthly,
     * plus the matching quarterly and yearly prices.
     */
    public function up(): void
    {
        if (Schema::hasTable('plans') && ! Schema::hasColumn('plans', 'includes_coaching')) {
            Schema::table('plans', function (Blueprint $table) {
                $table->boolean('includes_coaching')->default(true);
            });
        }

        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'can_access_coaching')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('can_access_coaching')->default(false)->after('premium');
            });
        }

        if (Schema::hasTable('plans') && Schema::hasColumn('plans', 'includes_coaching')) {
            Plan::query()
                ->whereRaw('LOWER(tier) LIKE ?', ['%premium%'])
                ->get()
                ->each(function (Plan $plan) {
                    if ($this->isNewPremiumWithoutCoaching($plan)) {
                        $plan->update(['includes_coaching' => false]);
                    }
                });
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'can_access_coaching')) {
            User::query()
                ->where('premium', true)
                ->update(['can_access_coaching' => true]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'can_access_coaching')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('can_access_coaching');
            });
        }

        if (Schema::hasTable('plans') && Schema::hasColumn('plans', 'includes_coaching')) {
            Schema::table('plans', function (Blueprint $table) {
                $table->dropColumn('includes_coaching');
            });
        }
    }

    protected function isNewPremiumWithoutCoaching(Plan $plan): bool
    {
        $usd = (float) $plan->price_usd;
        $eur = (float) $plan->price_eur;

        $isNewMonthly = abs($usd - 45.00) < 0.05 && abs($eur - 39.00) < 0.05;
        $isNewQuarterly = abs($usd - 120.00) < 0.05 && abs($eur - 105.00) < 0.05;
        $isNewYearly = abs($usd - 380.00) < 0.05 && abs($eur - 327.00) < 0.05;

        return $isNewMonthly || $isNewQuarterly || $isNewYearly;
    }
};
