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
        Schema::table('personalized_plans', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('goal');
            $table->json('completed_lessons')->nullable()->after('months');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personalized_plans', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'completed_lessons']);
        });
    }
};
