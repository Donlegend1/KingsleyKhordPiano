<?php

namespace Database\Seeders;

use App\Models\MidiFile;
use Illuminate\Database\Seeder;

class MidiFileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $midiFiles = [
            [
                'name' => 'Beginner Gospel Walkups',
                'video_path' => 'M7lc1UVf-VE',
                'video_type' => 'youtube',
                'midi_file_path' => null,
                'lmv_file_path' => null,
                'thumbnail_path' => 'images/featured1.jpeg',
                'description' => 'Simple walkups and progressions for beginner practice.',
            ],
            [
                'name' => 'Sunday Service Chord Movements',
                'video_path' => 'dQw4w9WgXcQ',
                'video_type' => 'youtube',
                'midi_file_path' => null,
                'lmv_file_path' => null,
                'thumbnail_path' => 'images/services1.jpeg',
                'description' => 'Chord movement ideas for live worship transitions.',
            ],
            [
                'name' => 'Praise Break Layering',
                'video_path' => '3JZ_D3ELwOQ',
                'video_type' => 'youtube',
                'midi_file_path' => null,
                'lmv_file_path' => null,
                'thumbnail_path' => 'images/services3.jpeg',
                'description' => 'Layered voicings for energetic praise sections.',
            ],
            [
                'name' => 'Neo Soul Reharm Starter',
                'video_path' => 'kXYiU_JCYtU',
                'video_type' => 'youtube',
                'midi_file_path' => null,
                'lmv_file_path' => null,
                'thumbnail_path' => 'images/pro.jpeg',
                'description' => 'Intro reharm shapes and passing tones for practice.',
            ],
            [
                'name' => 'Advanced Worship Intro',
                'video_path' => 'ScMzIvxBSi4',
                'video_type' => 'youtube',
                'midi_file_path' => null,
                'lmv_file_path' => null,
                'thumbnail_path' => 'images/featured2.jpeg',
                'description' => 'Long-form intro idea for more expressive worship sets.',
            ],
            [
                'name' => 'Modulation Drill Pack',
                'video_path' => 'ysz5S6PUM-U',
                'video_type' => 'youtube',
                'midi_file_path' => null,
                'lmv_file_path' => null,
                'thumbnail_path' => 'images/featured3.jpeg',
                'description' => 'Practice quick key changes and setup chords.',
            ],
            [
                'name' => 'Choir Backup Progressions',
                'video_path' => 'aqz-KE-bpKQ',
                'video_type' => 'youtube',
                'midi_file_path' => null,
                'lmv_file_path' => null,
                'thumbnail_path' => null,
                'description' => 'Solid support progressions to sit under choir vocals.',
            ],
            [
                'name' => 'Tension and Release Voicings',
                'video_path' => 'e-ORhEE9VVg',
                'video_type' => 'youtube',
                'midi_file_path' => null,
                'lmv_file_path' => null,
                'thumbnail_path' => null,
                'description' => 'Suspended voicings and tasteful releases for dynamics.',
            ],
        ];

        foreach ($midiFiles as $midiFile) {
            MidiFile::updateOrCreate(
                ['name' => $midiFile['name']],
                $midiFile
            );
        }
    }
}
