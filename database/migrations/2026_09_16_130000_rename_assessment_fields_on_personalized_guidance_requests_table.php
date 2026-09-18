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
            $table->renameColumn('archetype', 'chord_vocabulary');
            $table->renameColumn('experience_level', 'playing_by_ear');
        });

        Schema::table('personalized_guidance_requests', function (Blueprint $table) {
            $table->string('style_focus')->nullable()->after('playing_by_ear');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personalized_guidance_requests', function (Blueprint $table) {
            $table->dropColumn('style_focus');
            $table->renameColumn('chord_vocabulary', 'archetype');
            $table->renameColumn('playing_by_ear', 'experience_level');
        });
    }
};
