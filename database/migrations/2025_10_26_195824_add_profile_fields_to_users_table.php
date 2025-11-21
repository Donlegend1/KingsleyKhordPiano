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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number')->nullable()->after('country');
            $table->enum('skill_level', ['beginner', 'intermediate', 'advanced'])->nullable()->after('phone_number');
            $table->text('biography')->nullable()->after('skill_level');
            $table->string('instagram')->nullable()->after('biography');
            $table->string('youtube')->nullable()->after('instagram');
            $table->string('facebook')->nullable()->after('youtube');
            $table->string('tiktok')->nullable()->after('facebook');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone_number',
                'skill_level',
                'biography',
                'instagram',
                'youtube',
                'facebook',
                'tiktok'
            ]);
        });
    }
};
