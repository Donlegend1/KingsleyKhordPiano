<?php

namespace Database\Seeders;

use App\Models\Upload;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class QuickLessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lessons = [
            [
                'title' => 'Major Chord Shape Drill',
                'description' => 'A focused warm-up on clean major chord shapes and smooth transitions across nearby keys.',
                'video_url' => 'dQw4w9WgXcQ',
                'video_type' => 'youtube',
                'level' => 'Beginner',
                'thumbnail' => 'images/featured1.jpeg',
                'tags' => ['chords', 'warmup', 'major'],
            ],
            [
                'title' => 'Left Hand Timing Builder',
                'description' => 'Develop steadier rhythm and left-hand confidence with a short timing exercise.',
                'video_url' => 'M7lc1UVf-VE',
                'video_type' => 'youtube',
                'level' => 'Beginner',
                'thumbnail' => 'images/featured2.jpeg',
                'tags' => ['timing', 'left hand', 'groove'],
            ],
            [
                'title' => 'Easy Gospel Walkups',
                'description' => 'Learn a few simple walkups you can drop into worship progressions right away.',
                'video_url' => 'ysz5S6PUM-U',
                'video_type' => 'youtube',
                'level' => 'Beginner',
                'thumbnail' => 'images/featured3.jpeg',
                'tags' => ['gospel', 'walkups', 'worship'],
            ],
            [
                'title' => 'Minor Alternation',
                'description' => 'Practice switching between related minor colors to make your progressions feel more musical.',
                'video_url' => 'aqz-KE-bpKQ',
                'video_type' => 'youtube',
                'level' => 'Intermediate',
                'thumbnail' => 'images/music.png',
                'tags' => ['minor', 'progressions', 'color'],
            ],
            [
                'title' => 'Modal Interchange Basics',
                'description' => 'An introduction to borrowed chords and how to use them tastefully in real playing.',
                'video_url' => 'ScMzIvxBSi4',
                'video_type' => 'youtube',
                'level' => 'Intermediate',
                'thumbnail' => 'images/musictheory.png',
                'tags' => ['modal interchange', 'harmony', 'theory'],
            ],
            [
                'title' => 'Passing Chords in Context',
                'description' => 'Use practical passing chords that connect your main harmony without sounding forced.',
                'video_url' => '5qap5aO4i9A',
                'video_type' => 'youtube',
                'level' => 'Intermediate',
                'thumbnail' => 'images/services1.jpeg',
                'tags' => ['passing chords', 'voice leading', 'arrangement'],
            ],
            [
                'title' => 'Upper Structure Color Tones',
                'description' => 'Open up richer voicings using upper structure tones and controlled tension.',
                'video_url' => 'C0DPdy98e4c',
                'video_type' => 'youtube',
                'level' => 'Advanced',
                'thumbnail' => 'images/services2.jpeg',
                'tags' => ['voicings', 'upper structure', 'tension'],
            ],
            [
                'title' => 'Reharm a 2-5-1 Progression',
                'description' => 'Take a standard 2-5-1 and rebuild it with tasteful substitutions and movement.',
                'video_url' => 'ktvTqknDobU',
                'video_type' => 'youtube',
                'level' => 'Advanced',
                'thumbnail' => 'images/services3.jpeg',
                'tags' => ['reharm', '2-5-1', 'substitutions'],
            ],
            [
                'title' => 'Advanced Runs Breakdown',
                'description' => 'Slow down a fast melodic run and understand the fingering and harmonic targeting behind it.',
                'video_url' => 'hHW1oY26kxQ',
                'video_type' => 'youtube',
                'level' => 'Advanced',
                'thumbnail' => 'images/services4.jpeg',
                'tags' => ['runs', 'melody', 'technique'],
            ],
        ];

        foreach ($lessons as $index => $lesson) {
            Upload::updateOrCreate(
                [
                    'title' => $lesson['title'],
                    'category' => 'quick lessons',
                ],
                [
                    'description' => $lesson['description'],
                    'video_url' => $lesson['video_url'],
                    'video_type' => $lesson['video_type'],
                    'level' => $lesson['level'],
                    'status' => 'active',
                    'skill_level' => $lesson['level'],
                    'thumbnail' => $lesson['thumbnail'],
                    'tags' => $lesson['tags'],
                    'created_at' => Carbon::now()->subDays(20 - ($index * 2)),
                    'updated_at' => Carbon::now()->subDays(20 - ($index * 2)),
                ]
            );
        }
    }
}
