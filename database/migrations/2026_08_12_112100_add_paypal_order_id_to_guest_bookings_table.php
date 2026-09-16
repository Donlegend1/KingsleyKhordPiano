<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guest_bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('guest_bookings', 'paypal_order_id')) {
                $table->string('paypal_order_id')->nullable()->after('paystack_reference');
            }
        });
    }

    public function down(): void
    {
        Schema::table('guest_bookings', function (Blueprint $table) {
            if (Schema::hasColumn('guest_bookings', 'paypal_order_id')) {
                $table->dropColumn('paypal_order_id');
            }
        });
    }
};
