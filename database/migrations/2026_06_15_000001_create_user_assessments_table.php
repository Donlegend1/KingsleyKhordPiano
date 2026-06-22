<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('learn_song_categories')){
        Schema::create('user_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('score'); // 0-100 percentage
            $table->string('skill_level'); // 'Beginner', 'Intermediate', 'Advanced'
            $table->integer('fundamentals_score');
            $table->integer('ear_training_score');
            $table->integer('chords_harmony_score');
            $table->integer('experience_score');
            $table->json('answers'); // selected option ids/points for each question
            $table->timestamps();
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_assessments');
    }
};
