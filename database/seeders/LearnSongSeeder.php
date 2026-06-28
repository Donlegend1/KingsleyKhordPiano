<?php

namespace Database\Seeders;

use App\Models\LearnSong;
use App\Models\LearnSongCategory;
use Illuminate\Database\Seeder;

class LearnSongSeeder extends Seeder
{
    /**
     * Seed learn songs across all 12 keys.
     */
    public function run(): void
    {
        $songs = [
            ['Amazing Grace - Beginner Chord Breakdown', 'Gospel Essentials', 'beginner', 'G', 'A gentle song study focused on simple triads, steady timing, and clean transitions between common gospel movements.', 'CD-E-LDc384', 'images/featured1.jpeg'],
            ['Way Maker - Worship Piano Breakdown', 'Modern Worship', 'beginner', 'E', 'Learn the core worship progression, left-hand support patterns, passing chords, and tasteful fills for congregational playing.', 'iJCV_2H9xD0', 'images/music.png'],
            ['Blessed Assurance - Key of C Study', 'Hymn Foundations', 'beginner', 'C', 'Build confidence in C with simple inversions, melody support, and left-hand anchors.', 'ysz5S6PUM-U', 'images/featured2.jpeg'],
            ['10,000 Reasons - Key of D Warmup', 'Modern Worship', 'beginner', 'D', 'Practice open worship voicings and smooth transitions in D.', 'DXDGE_lRI0E', 'images/services1.jpeg'],
            ['You Are Good - Intermediate Groove Study', 'Groove Lab', 'intermediate', 'A', 'Build a stronger pocket with syncopated comping, dominant movement, gospel walk-ups, and rhythmic chord stabs.', 'dQw4w9WgXcQ', 'images/showcase.png'],
            ['Excess Love - Gospel Voicing Breakdown', 'African Gospel', 'intermediate', 'F#', 'Explore richer voicings, reharmonized turnarounds, and melodic fills that help the song feel full without overplaying.', 'YQHsXMglC9A', 'images/banner2.jpg'],
            ['Oceans - Key of Eb Worship Flow', 'Modern Worship', 'intermediate', 'Eb', 'Develop flowing worship textures, sus chords, and dynamic movement in Eb.', 'dy9nwe9_xzw', 'images/youtube.jpeg'],
            ['No Longer Slaves - Key of F Arrangement', 'Worship Arrangements', 'intermediate', 'F', 'Study arrangement shape, inversions, and supportive fills in F.', 'f8TkUMJtK5k', 'images/services2.jpeg'],
            ['Great Are You Lord - Advanced Worship Reharm', 'Advanced Reharm', 'advanced', 'D', 'A more advanced breakdown covering substitutions, inner-voice motion, quartal color, and dynamic arrangement choices.', 'kXw8CRapg7k', 'images/pro.jpeg'],
            ['Total Praise - Advanced Gospel Passing Chords', 'Advanced Gospel', 'advanced', 'Db', 'Study classic gospel passing chords, suspended resolutions, big voicings, and expressive movement between sections.', '3JWTaaS7LdU', 'images/kingsley.jpg'],
            ['Intentional - Key of Ab Gospel Movement', 'Advanced Gospel', 'advanced', 'Ab', 'Work through advanced gospel movement, altered dominants, and shout-style passing ideas in Ab.', '60ItHLz5WEA', 'images/services4.jpeg'],
            ['Goodness of God - Key of Bb Reharm', 'Advanced Reharm', 'advanced', 'Bb', 'Learn tasteful reharmonization, passing diminished color, and melodic top-note control in Bb.', 'n0FBb6hnwTo', 'images/live2.jpeg'],
            ['Firm Foundation - Key of B Modern Voicings', 'Advanced Reharm', 'advanced', 'B', 'Explore modern worship voicings, substitutions, and confident modulation ideas in B.', 'uOP4s8fOEm0', 'images/probanner.jpeg'],
        ];

        $categoryPositions = [];

        foreach ($songs as $index => [$title, $categoryName, $level, $songKey, $description, $videoUrl, $thumbnail]) {
            $categoryKey = "{$level}:{$categoryName}";

            if (! isset($categoryPositions[$categoryKey])) {
                $categoryPositions[$categoryKey] = (LearnSongCategory::where('level', $level)->max('position') ?: 0) + 1;
            }

            $category = LearnSongCategory::firstOrCreate(
                [
                    'category' => $categoryName,
                    'level' => $level,
                ],
                [
                    'position' => $categoryPositions[$categoryKey],
                ]
            );

            LearnSong::updateOrCreate(
                ['title' => $title],
                [
                    'learn_song_category_id' => $category->id,
                    'description' => $description,
                    'video_type' => 'youtube',
                    'video_url' => $videoUrl,
                    'thumbnail' => $thumbnail,
                    'level' => $level,
                    'song_key' => $songKey,
                    'status' => 'active',
                    'position' => $index + 1,
                    'related_songs' => null,
                ]
            );
        }
    }
}
