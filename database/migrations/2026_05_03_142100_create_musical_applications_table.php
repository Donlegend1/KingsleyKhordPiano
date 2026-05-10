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
        Schema::create('musical_applications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->text('video_url');
            $table->string('video_type')->default('vimeo');
            $table->enum('skill_level', ['Beginner', 'Intermediate', 'Advanced'])->default('Beginner');
            $table->string('series')->nullable();
            $table->string('duration')->nullable();
            $table->enum('status', ['active', 'inactive', 'draft'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('musical_applications');
    }
};
