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
        Schema::table('personalized_guidance_requests', function (Blueprint $table) {
            $table->string('archetype')->nullable()->after('youtube_link');
            $table->string('practice_days_per_week')->nullable()->after('archetype');
            $table->string('practice_time_per_day')->nullable()->after('practice_days_per_week');
            $table->string('experience_level')->nullable()->after('practice_time_per_day');
            $table->text('primary_goal')->nullable()->after('experience_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personalized_guidance_requests', function (Blueprint $table) {
            $table->dropColumn([
                'archetype',
                'practice_days_per_week',
                'practice_time_per_day',
                'experience_level',
                'primary_goal',
            ]);
        });
    }
};
