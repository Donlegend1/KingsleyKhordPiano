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
            $table->string('goal')->nullable()->after('skill_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personalized_plans', function (Blueprint $table) {
            $table->dropColumn('goal');
        });
    }
};
