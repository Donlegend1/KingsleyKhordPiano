<?php

namespace Database\Seeders;

use App\Models\MusicalApplication;
use App\Models\Upload;
use Illuminate\Database\Seeder;

class UploadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pianoExercises = [
            [
                'title' => 'Five Finger Independence - C Major Warmup',
                'description' => 'Build finger control with a slow C major warmup designed for the virtual MIDI practice display.',
                'level' => 'independence',
                'skill_level' => 'Basic',
                'series' => 'Five Finger Independence',
                'thumbnail' => 'images/finger-exercise.jpeg',
                'video_url' => 'CD-E-LDc384',
            ],
            [
                'title' => 'Five Finger Independence - Left Hand Pulse',
                'description' => 'Keep a steady left-hand pulse while the right hand moves through simple chord tones.',
                'level' => 'independence',
                'skill_level' => 'Basic',
                'series' => 'Five Finger Independence',
                'thumbnail' => 'images/hand-independence.jpeg',
                'video_url' => 'iJCV_2H9xD0',
            ],
            [
                'title' => 'Chord Inversion Dexterity Drill',
                'description' => 'Practice smooth major-triad inversions with a clean hand shape and relaxed wrist.',
                'level' => 'dexterity',
                'skill_level' => 'Competent',
                'series' => 'Chord Inversion Dexterity',
                'thumbnail' => 'images/hands-dexterity.jpeg',
                'video_url' => 'ysz5S6PUM-U',
            ],
        ];

        foreach ($pianoExercises as $exercise) {
            Upload::updateOrCreate(
                ['title' => $exercise['title']],
                [
                    'category' => 'piano exercise',
                    'description' => $exercise['description'],
                    'video_type' => 'youtube',
                    'video_url' => $exercise['video_url'],
                    'level' => $exercise['level'],
                    'skill_level' => $exercise['skill_level'],
                    'status' => 'active',
                    'series' => $exercise['series'],
                    'thumbnail' => $exercise['thumbnail'],
                    'tags' => null,
                    'images' => null,
                ]
            );
        }

        $guidedPractices = [
            [
                'title' => 'Gospel Passing Chords Guided Practice',
                'description' => 'A guided practice loop for hearing and feeling gospel passing chords in time.',
                'skill_level' => 'Beginner',
                'series' => 'Guided Practice',
                'duration' => '04:30',
                'thumbnail' => 'images/technique.jpeg',
                'video_url' => 'CD-E-LDc384',
            ],
            [
                'title' => 'Slow Worship Voicing Guided Practice',
                'description' => 'Practice slow worship voicings with steady tempo control and relaxed transitions.',
                'skill_level' => 'Intermediate',
                'series' => 'Guided Practice',
                'duration' => '05:15',
                'thumbnail' => 'images/musical-application.jpeg',
                'video_url' => 'iJCV_2H9xD0',
            ],
        ];

        foreach ($guidedPractices as $practice) {
            MusicalApplication::updateOrCreate(
                ['title' => $practice['title']],
                [
                    'description' => $practice['description'],
                    'thumbnail' => $practice['thumbnail'],
                    'video_url' => $practice['video_url'],
                    'video_type' => 'youtube',
                    'skill_level' => $practice['skill_level'],
                    'series' => $practice['series'],
                    'duration' => $practice['duration'],
                    'status' => 'active',
                ]
            );
        }
    }
}
