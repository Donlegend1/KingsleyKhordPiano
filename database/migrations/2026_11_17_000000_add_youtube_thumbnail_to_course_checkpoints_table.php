<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_checkpoints', function (Blueprint $table) {
            $table->string('thumbnail_youtube_id', 11)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('course_checkpoints', function (Blueprint $table) {
            $table->dropColumn('thumbnail_youtube_id');
        });
    }
};
