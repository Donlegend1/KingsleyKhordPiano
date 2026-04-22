<?php

namespace Database\Seeders;

use App\Models\Quiz;
use Illuminate\Database\Seeder;

class EarTrainingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sharedAudio = [
            '/uploads/audio/1769332639_pre1.mp3',
            '/uploads/audio/1769332691_pre1.mp3',
        ];

        $quizzes = [
            [
                'title' => 'Relative Pitch Foundations',
                'description' => 'A starter ear training set focused on identifying solfege tones by ear.',
                'category' => 'Relative Pitch',
                'video_url' => 'https://drive.google.com/file/d/1dQw4w9WgXcQExample/view',
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '0'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '2'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '4'],
                ],
            ],
            [
                'title' => 'Interval Recognition Drill',
                'description' => 'Practice hearing common intervals before moving into more advanced chord work.',
                'category' => 'Intervals',
                'video_url' => 'https://drive.google.com/file/d/1mN0pQrsTuVExample/view',
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[1],
                'questions' => [
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '1'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '4'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '7'],
                ],
            ],
            [
                'title' => 'Basic Triad Spotlight',
                'description' => 'A short quiz for identifying major, minor, diminished, and suspended triads.',
                'category' => 'Basic Triad',
                'video_url' => 'https://drive.google.com/file/d/1zYxWvuTsRqExample/view',
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '2'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '3'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '1'],
                ],
            ],
        ];

        foreach ($quizzes as $quizData) {
            $questions = $quizData['questions'];
            unset($quizData['questions']);

            $quiz = Quiz::updateOrCreate(
                ['title' => $quizData['title']],
                $quizData
            );

            $quiz->questions()->delete();
            $quiz->questions()->createMany($questions);
        }
    }
}
