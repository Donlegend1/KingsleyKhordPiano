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
        Schema::create('midi_files', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('video_path');
            $table->enum('video_type', ['youtube', 'google', 'local', 'iframe']);
            $table->string('midi_file_path')->nullable();
            $table->string('lmv_file_path')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('midi_files');
    }
};
