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
        if (! Schema::hasTable('user_assessments')) {
            Schema::create('user_assessments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->integer('score');
                $table->string('skill_level');
                $table->integer('fundamentals_score');
                $table->integer('ear_training_score');
                $table->integer('chords_harmony_score');
                $table->integer('experience_score');
                $table->json('answers');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_assessments');
    }
};
