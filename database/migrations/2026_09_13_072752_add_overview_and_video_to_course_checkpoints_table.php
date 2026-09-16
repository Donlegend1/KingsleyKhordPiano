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
        Schema::table('course_checkpoints', function (Blueprint $table) {
            $table->string('video_url')->nullable()->after('description');
            $table->text('overview')->nullable()->after('video_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_checkpoints', function (Blueprint $table) {
            $table->dropColumn(['video_url', 'overview']);
        });
    }
};
