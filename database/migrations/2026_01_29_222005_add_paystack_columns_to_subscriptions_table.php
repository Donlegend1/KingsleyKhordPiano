<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('subscriptions')) {
            return;
        }

        Schema::table('subscriptions', function (Blueprint $table) {
            if (! Schema::hasColumn('subscriptions', 'plan_code')) {
                $table->string('plan_code')->after('user_id')->nullable();
            }

            if (! Schema::hasColumn('subscriptions', 'subscription_code')) {
                $table->string('subscription_code')->nullable()->unique()->after('plan_code');
            }

            if (! Schema::hasColumn('subscriptions', 'email_token')) {
                $table->string('email_token')->nullable()->after('subscription_code');
            }

            if (! Schema::hasColumn('subscriptions', 'authorization_code')) {
                $table->string('authorization_code')->nullable()->after('email_token');
            }

            if (! Schema::hasColumn('subscriptions', 'payment_method')) {
                $table->string('payment_method')->default('stripe')->after('authorization_code');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('subscriptions')) {
            return;
        }

        Schema::table('subscriptions', function (Blueprint $table) {
            $columns = ['plan_code', 'subscription_code', 'email_token', 'authorization_code', 'payment_method'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('subscriptions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
