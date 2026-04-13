<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('plans')) {
            return;
        }

        Schema::table('plans', function (Blueprint $table) {
            if (! Schema::hasColumn('plans', 'product_id')) {
                $table->string('product_id')->nullable();
            }

            if (! Schema::hasColumn('plans', 'price_id')) {
                $table->string('price_id')->nullable();
            }

            if (! Schema::hasColumn('plans', 'paystack_product_id')) {
                $table->string('paystack_product_id')->nullable();
            }

            if (! Schema::hasColumn('plans', 'stripe_product_id')) {
                $table->string('stripe_product_id')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('plans')) {
            return;
        }

        Schema::table('plans', function (Blueprint $table) {
            $columns = ['product_id', 'price_id', 'paystack_product_id', 'stripe_product_id'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('plans', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
