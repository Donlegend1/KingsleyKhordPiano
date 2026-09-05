<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = now();

        $quizzes = DB::table('quizzes')->whereNotNull('main_audio_path')->where('main_audio_path', '!=', '')->get(['id', 'main_audio_path']);

        foreach ($quizzes as $quiz) {
            DB::table('quiz_reference_audios')->insert([
                'quiz_id' => $quiz->id,
                'name' => null,
                'audio_path' => $quiz->main_audio_path,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('quiz_reference_audios')->whereNull('name')->delete();
    }
};
