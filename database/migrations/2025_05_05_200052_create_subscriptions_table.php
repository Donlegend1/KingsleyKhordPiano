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
        if (! Schema::hasTable('subscriptions')) {
            Schema::create('subscriptions', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->integer('amount_naria')->nullable();
                $table->integer('amount_dollar')->nullable();
                $table->string('duration')->nullable();
                $table->timestamps();
            });

            return;
        }

        Schema::table('subscriptions', function (Blueprint $table) {
            if (! Schema::hasColumn('subscriptions', 'name')) {
                $table->string('name')->nullable()->after('id');
            }

            if (! Schema::hasColumn('subscriptions', 'amount_naria')) {
                $table->integer('amount_naria')->nullable()->after('name');
            }

            if (! Schema::hasColumn('subscriptions', 'amount_dollar')) {
                $table->integer('amount_dollar')->nullable()->after('amount_naria');
            }

            if (! Schema::hasColumn('subscriptions', 'duration')) {
                $table->string('duration')->nullable()->after('amount_dollar');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('subscriptions')) {
            return;
        }

        Schema::table('subscriptions', function (Blueprint $table) {
            $columns = ['name', 'amount_naria', 'amount_dollar', 'duration'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('subscriptions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
