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
        Schema::create('audio_downloads', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('category', ['tracks_loops', 'piano_plays']);
            $table->string('audio_file');
            $table->string('duration')->nullable();
            $table->string('file_size')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audio_downloads');
    }
};
