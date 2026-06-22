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
         if (! Schema::hasTable('lesson_completions')){
        Schema::create('lesson_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Polymorphic
            $table->unsignedBigInteger('completable_id');
            $table->string('completable_type'); // ExtraCourse::class, LearnSong::class, Quiz::class

            $table->timestamps();

            $table->unique(['user_id', 'completable_id', 'completable_type'], 'unique_user_completion');
        });
         }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_completions');
    }
};
