<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('plan_code')->after('user_id')->nullable();
            $table->string('subscription_code')->unique()->after('plan_code')->nullable();
            $table->string('email_token')->nullable()->after('subscription_code');
            $table->string('authorization_code')->nullable()->after('email_token');
            $table->string('payment_method')->default('stripe')->after('authorization_code');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropUnique(['subscription_code']);
            $table->dropColumn([
                'plan_code',
                'subscription_code',
                'email_token',
                'authorization_code',
            ]);
        });
    }
};
