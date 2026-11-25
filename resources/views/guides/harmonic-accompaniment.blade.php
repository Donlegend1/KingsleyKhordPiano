@extends('guides.layout')

@section('title', $guide['title'])
@section('description', 'Hear Yes Jesus Loves Me played four ways. Learn to harmonize a melody with diatonic thirds, fifths, sixths, and triads.')

@section('styles')
    <link rel="stylesheet" href="{{ asset('guide-assets/chord-extensions/guide.css') }}">
    <link rel="stylesheet" href="{{ asset('guide-assets/shared/performance-player.css') }}">
@endsection

@section('guide')
@php
    $base = 'guide-assets/harmonic-accompaniment';
    $whiteKeys = [47, 48, 50, 52, 53, 55, 57, 59, 60, 62, 64, 65, 67, 69, 71, 72, 74, 76, 77, 79, 81, 83, 84];
    $steps = [
        'melody' => [
            'title' => 'Start with the melody',
            'duration' => 41,
            'intro' => 'Play the tune by itself in your right hand. In your left hand, play the song’s chord progression using simple triads. Which chords you play depends on the song.',
            'cue' => 'Listen for the single-note tune above the left-hand chords. This is the version to compare with the next three takes.',
        ],
        'thirds' => [
            'title' => 'Add thirds below the melody',
            'duration' => 42,
            'intro' => 'Keep the melody on top and add a note a diatonic third below it. Count downward within the key, including the melody note as one. Use an occasional fourth when you want a different sound.',
            'cue' => 'Listen for the second right-hand note moving with the tune. Can you still sing the melody clearly over it?',
        ],
        'wide-intervals' => [
            'title' => 'Open up the sound with fifths and sixths',
            'duration' => 41,
            'intro' => 'Keep the tune in the highest voice and choose a note a fifth or sixth below it. Use notes from the same key. The wider interval gives the lower voice more room while the melody stays familiar.',
            'cue' => 'Listen to this take right after the thirds. Notice how much more space you hear between the two notes in the right hand.',
        ],
        'triads' => [
            'title' => 'Fill the space with a triad',
            'duration' => 41,
            'intro' => 'Keep the tune on top and add a note between the two notes you already have. Over C–G, add E for C major. Over B–G, add D for G/B or E for Em/B.',
            'cue' => 'Listen for the fuller right-hand voicings while the melody stays on top.',
        ],
    ];
@endphp

<section class="mx-auto max-w-7xl px-5 pb-9 pt-10 sm:px-8 sm:pb-12 sm:pt-16">
    <div class="max-w-3xl">
        <p class="text-xs font-bold uppercase tracking-widest text-[#4f5667]">Harmonic Accompaniment</p>
        <h1 class="mt-4 text-[clamp(2.75rem,5.5vw,5rem)] font-extrabold leading-[1.02] tracking-[-0.055em]">One melody, four ways to play it.</h1>
        <p class="mt-5 max-w-2xl text-lg leading-7 text-[#4f5667]">Hear Kingsley play the same passage from <strong>Yes Jesus Loves Me</strong> four ways. In each take, the left hand follows the song’s chord progression while the right hand adds harmony using notes from the key.</p>
    </div>
</section>

<div data-accompaniment-lesson data-note-source="{{ asset($base . '/notes.json') }}">
    @foreach($steps as $slug => $step)
        <section id="{{ $slug }}" aria-labelledby="{{ $slug }}-heading"
                 class="scroll-mt-5 border-t border-black/10 {{ $loop->odd ? 'bg-white' : '' }}">
            <div class="mx-auto grid max-w-7xl gap-7 px-5 py-10 sm:px-8 sm:py-14 lg:grid-cols-[0.95fr_1.05fr] lg:items-center lg:gap-12">
                <div>
                    <p class="mb-2 text-sm font-bold text-[#1447A6]">Step {{ $loop->iteration }} of {{ $loop->count }}</p>
                    <h2 id="{{ $slug }}-heading" class="text-2xl font-bold tracking-tight sm:text-3xl">{{ $step['title'] }}</h2>
                    <p class="mt-3 leading-7 text-[#4f5667]">{{ $step['intro'] }}</p>
                </div>

                @include('guides.performance-player', [
                    'recording' => $slug,
                    'playerTitle' => 'Listen to Step ' . $loop->iteration,
                    'playerLabel' => $step['title'],
                    'cue' => $step['cue'],
                    'audioPath' => $base . '/' . $slug . '.mp3',
                    'duration' => $step['duration'],
                ])
            </div>
        </section>
    @endforeach
</div>

@include('guides.membership-cta', [
    'heading' => 'Learn to harmonize your favorite gospel melodies.',
    'description' => 'You’ve heard four ways to play Yes Jesus Loves Me. Now learn when to use thirds, fifths, sixths, and triads in songs you already play. Inside the membership, guided practice and piano exercises help you build the harmony one phrase at a time, keep the melody clear, and choose a voicing that fits the song.',
])
@endsection

@section('scripts')
    <script type="module" src="{{ asset('guide-assets/shared/performance-player.js') }}"></script>
@endsection
