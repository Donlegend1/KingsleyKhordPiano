<?php

namespace Database\Seeders;

use App\Models\Quiz;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

/**
 * Gives the "Single tone Pitch" quiz 25 real questions made from the generated piano notes
 * (tools/quiz-audio/generate.py --repeat 3). Key of C only. Each clip strikes one note three times
 * with silence between, so the listener can hear it again before answering. No tonic is played
 * first - the quiz's reference audio is what tells the listener where Doh is.
 *
 * Only this one quiz is touched, and its previous questions are saved to storage/app/backups
 * before they are replaced.
 */
class SingleTonePitchPianoSeeder extends Seeder
{
    // Same key EarTrainingSeeder uses for this quiz.
    private const QUIZ_DESCRIPTION = 'A starter ear training set focused on identifying solfege tones by ear.';

    // Solfege position (the stored correct_option) => piano clips that sound that degree in C.
    private const NOTES = [
        0 => ['c3', 'c4'],   // Doh
        1 => ['d3'],         // Reh
        2 => ['e3'],         // Mi
        3 => ['f3'],         // Fah
        4 => ['g3'],         // Sol
        5 => ['a3'],         // Lah
        6 => ['b3'],         // Ti
    ];

    // 25 questions: every degree appears 3 times, plus one extra each for Doh, Mi, Sol and Ti.
    private const HOW_MANY = [0 => 4, 1 => 3, 2 => 4, 3 => 3, 4 => 4, 5 => 3, 6 => 4];

    public function run(): void
    {
        $quiz = Quiz::where('description', self::QUIZ_DESCRIPTION)->first();
        if (! $quiz) {
            $this->command?->error('Quiz not found - run EarTrainingSeeder first.');
            return;
        }

        $questions = $this->buildQuestions();

        foreach ($questions as $question) {
            if (! File::exists(public_path($question['audio_path']))) {
                $this->command?->error("Missing audio file {$question['audio_path']} - run tools/quiz-audio/generate.py --repeat 3 first.");
                return;
            }
        }

        $backupDir = storage_path('app/backups');
        File::ensureDirectoryExists($backupDir);
        $backup = $backupDir . '/quiz-' . $quiz->id . '-questions-' . date('Ymd-His') . '.json';
        File::put($backup, $quiz->questions()->get(['audio_path', 'correct_option'])->toJson(JSON_PRETTY_PRINT));

        $quiz->questions()->delete();
        $quiz->questions()->createMany($questions);

        $this->command?->info("Quiz #{$quiz->id}: " . count($questions) . " questions saved. Previous questions backed up to $backup");
    }

    private function buildQuestions(): array
    {
        $pool = [];
        foreach (self::HOW_MANY as $degree => $count) {
            for ($i = 0; $i < $count; $i++) {
                $files = self::NOTES[$degree];
                $pool[] = ['degree' => $degree, 'file' => $files[$i % count($files)]];
            }
        }

        // Fixed seed so a re-run gives the same order. Shuffle, then break up back-to-back repeats.
        mt_srand(2026);
        for ($i = count($pool) - 1; $i > 0; $i--) {
            $j = mt_rand(0, $i);
            [$pool[$i], $pool[$j]] = [$pool[$j], $pool[$i]];
        }
        for ($i = 1; $i < count($pool); $i++) {
            if ($pool[$i]['degree'] === $pool[$i - 1]['degree']) {
                for ($j = $i + 1; $j < count($pool); $j++) {
                    if ($pool[$j]['degree'] !== $pool[$i - 1]['degree'] && ($pool[$j]['degree'] !== ($pool[$i + 1]['degree'] ?? null))) {
                        [$pool[$i], $pool[$j]] = [$pool[$j], $pool[$i]];
                        break;
                    }
                }
            }
        }

        return array_map(fn ($item) => [
            'audio_path' => '/uploads/audio/generated/piano-x3/' . $item['file'] . '.mp3',
            'correct_option' => (string) $item['degree'],
        ], $pool);
    }
}
