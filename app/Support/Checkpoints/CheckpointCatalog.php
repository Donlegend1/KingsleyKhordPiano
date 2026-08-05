<?php

namespace App\Support\Checkpoints;

class CheckpointCatalog
{
    /**
     * Premade checkpoint templates. Each key corresponds to a matching
     * React content component in
     * resources/js/components/courses/checkpoints/.
     */
    protected static array $templates = [
        'major-scale' => [
            'title' => 'Major Scale',
            'label' => 'Practice Checkpoint',
            'description' => "Reinforce what you've learned so far.",
        ],
        'major-triads' => [
            'title' => 'Major Triads',
            'label' => 'Practice Checkpoint',
            'description' => 'Learn how to build and play Major Triads in all 12 keys.',
        ],
        'minor-triads' => [
            'title' => 'Minor Triads',
            'label' => 'Practice Checkpoint',
            'description' => 'Learn how to build and play Minor Triads in all 12 keys.',
        ],
        'relative-pitch-ear-training' => [
            'title' => 'Relative Pitch Ear Training',
            'label' => 'Practice Checkpoint',
            'description' => 'Learn how to identify solfa (do, re, mi, fa, so, la, ti) from any tonal center.',
        ],
        'diatonic-triads' => [
            'title' => 'Diatonic Triads',
            'label' => 'Practice Checkpoint',
            'description' => 'Learn how to build the seven diatonic triads in any major key.',
        ],
        '7th-chords' => [
            'title' => '7th Chords',
            'label' => 'Practice Checkpoint',
            'description' => 'Learn how to build Major 7, Minor 7, Dominant 7, Minor 7b5, and Diminished 7 chords.',
        ],
        'chord-extensions' => [
            'title' => 'Chord Extensions',
            'label' => 'Practice Checkpoint',
            'description' => 'Learn how to build 9th, 11th, and 13th chord extensions beyond the basic 7th chord.',
        ],
        'drop-2-chords' => [
            'title' => 'Drop 2 Chords',
            'label' => 'Practice Checkpoint',
            'description' => 'Learn how to build Drop 2 voicings for triads and 7th chords.',
        ],
        'melodic-minor-scale' => [
            'title' => 'Melodic Minor Scale',
            'label' => 'Practice Checkpoint',
            'description' => 'Learn how to play the Melodic Minor Scale in all 12 keys.',
        ],
        'major-add9-drop2' => [
            'title' => 'Major add9 Drop 2',
            'label' => 'Practice Checkpoint',
            'description' => 'Learn how to build Drop 2 voicings for Major add9 chords.',
        ],
        'secondary-dominant-passing' => [
            'title' => 'Secondary Dominant Passing',
            'label' => 'Practice Checkpoint',
            'description' => 'Learn how to use secondary dominants to smoothly connect diatonic chords.',
        ],
        '7th-chord-inversions' => [
            'title' => '7th Chord Inversions',
            'label' => 'Practice Checkpoint',
            'description' => 'Learn how to build all four block chord inversions of a 7th chord.',
        ],
    ];

    public static function all(): array
    {
        return collect(self::$templates)
            ->map(fn ($template, $key) => array_merge(['key' => $key], $template))
            ->values()
            ->all();
    }

    public static function get(string $key): ?array
    {
        return self::$templates[$key] ?? null;
    }

    public static function exists(string $key): bool
    {
        return isset(self::$templates[$key]);
    }
}
