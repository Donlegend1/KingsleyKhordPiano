<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('plans')) {
            return;
        }

        Schema::table('plans', function (Blueprint $table) {
            if (! Schema::hasColumn('plans', 'paypal_product_id')) {
                $table->string('paypal_product_id')->nullable()->after('stripe_product_id');
            }

            if (! Schema::hasColumn('plans', 'paypal_plan_ids')) {
                $table->json('paypal_plan_ids')->nullable()->after('paypal_product_id');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('plans')) {
            return;
        }

        Schema::table('plans', function (Blueprint $table) {
            foreach (['paypal_product_id', 'paypal_plan_ids'] as $column) {
                if (Schema::hasColumn('plans', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
