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
        Schema::create('liveshows', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->datetime('start_time');
            $table->enum('access_type', ['all', 'premium']);
            $table->string('zoom_link');
            $table->string('recording_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('liveshows');
    }
};
