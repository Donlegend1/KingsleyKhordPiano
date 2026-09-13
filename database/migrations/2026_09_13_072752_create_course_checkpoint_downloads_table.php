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
        Schema::create('course_checkpoint_downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_checkpoint_id')->constrained('course_checkpoints')->cascadeOnDelete();
            $table->string('title');
            $table->string('file_path');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_checkpoint_downloads');
    }
};
