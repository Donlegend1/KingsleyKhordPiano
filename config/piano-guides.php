<?php

return [
    // Put new guides first. Each slug has a matching view under resources/views/guides.
    'piano-number-system' => [
        'title' => 'The Piano Number System for All 12 Keys',
        'description' => 'Turn scale notes into numbers, learn the chord pattern, and play the same progression in every major key.',
        'cover_view' => 'guides.number-system-cover',
        'pdf' => 'guide-downloads/piano-number-system-all-12-keys.pdf',
        'pdf_title' => 'The Piano Number System for All 12 Keys',
    ],
    'chordal-harmony-template' => [
        'title' => 'Chordal Harmony Template for Piano',
        'description' => 'Learn seven five-note shapes for harmonizing a melody, then compare original and varied takes of two familiar songs.',
        'cover_view' => 'guides.chordal-harmony-cover',
        'pdf' => 'guide-downloads/chordal-harmony-template-all-12-keys.pdf',
        'pdf_title' => 'Chordal Harmony Template for All 12 Keys',
        'email_tip' => 'Pick a short melody in C. Find each melody note in the chart, play its five-note shape, and keep the yellow melody note on top.',
    ],
    'drop-two-templates' => [
        'title' => 'Drop Two Templates for Piano',
        'description' => 'Give each melody note a ready-made chord shape, then hear the template and variations in two familiar songs.',
        'cover_view' => 'guides.drop-two-cover',
        'pdf' => 'guide-downloads/drop-two-templates-all-12-keys.pdf',
        'pdf_title' => 'Drop Two Templates for All 12 Keys',
        'email_tip' => 'Start with a short melody in C. For each melody note, find its card and play the blue key with your left hand and the green and yellow keys with your right. Keep the yellow melody note on top.',
    ],
    'chromatic-passing-chords' => [
        'title' => 'Chromatic Passing Chords for Seventh Chords',
        'description' => 'Learn how to approach seventh-chord voicings by half steps and get the free dominant seventh chart for all 12 keys.',
        'cover_view' => 'guides.chromatic-passing-cover',
        'pdf' => 'guide-downloads/chromatic-passing-chords-dominant-7.pdf',
        'pdf_title' => 'Chromatic Passing Chords for Dominant 7ths',
        'email_tip' => 'Try the C7 example: play G major, A♭ major, then A major in second inversion. Use your right hand alone until A major, when C7 joins in the left hand.',
    ],
    'passing-chord-progression' => [
        'title' => 'One Bass Line, 30 Ways to Play a 2–5–1',
        'description' => 'Keep D → G → C in the bass, change the chord qualities, and hear 30 ways to play the same progression.',
        'cover_view' => 'guides.passing-chord-cover',
    ],
    'dominant-voicings' => [
        'title' => 'Three Ways to Voice the 5 Chord in a 2–5–1',
        'description' => 'Hear suspended, augmented, and dominant thirteenth voicings resolve in C, then try them on secondary dominants.',
        'cover' => 'guide-assets/dominant-voicings/cover.webp',
    ],
    'harmonic-accompaniment' => [
        'title' => 'Harmonic Accompaniment for Gospel Piano',
        'description' => 'Hear four ways to harmonize Yes Jesus Loves Me, from melody and chords to full triads.',
        'cover' => 'guide-assets/harmonic-accompaniment/cover.webp',
    ],
    'chord-extensions' => [
        'title' => 'Chord Extensions for Gospel Piano',
        'description' => 'Try left-hand bass shapes and right-hand chords, then open the free charts for all 12 major keys.',
        'cover_view' => 'guides.chord-extensions-cover',
        'pdf' => 'guide-downloads/chord-extension-charts.pdf',
        'email_tip' => 'Try this first: play C in your left hand and E–G–B in your right. You’ve just made Cmaj7. Keep C in the bass and add D to your right hand to hear how the 9th changes the sound.',
    ],
];
