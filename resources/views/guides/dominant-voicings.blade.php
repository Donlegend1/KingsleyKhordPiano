@extends('guides.layout')

@section('title', $guide['title'])
@section('description', 'Hear suspended, augmented, and dominant thirteenth voicings in a gospel piano 2–5–1 in C. Then apply them to secondary dominants.')

@section('styles')
    <link rel="stylesheet" href="{{ asset('guide-assets/chord-extensions/guide.css') }}">
    <link rel="stylesheet" href="{{ asset('guide-assets/shared/performance-player.css') }}">
@endsection

@section('guide')
@php
    $base = 'guide-assets/dominant-voicings';
    $whiteKeys = [43, 45, 47, 48, 50, 52, 53, 55, 57, 59, 60, 62, 64, 65, 67, 69];
    $voicings = [
        'suspended' => [
            'title' => 'Suspended dominant',
            'symbol' => 'G13sus4',
            'notes' => ['G', 'F', 'A', 'C', 'E'],
            'highlight' => 'C',
            'intro' => 'Use C, the fourth above G, in place of B, the third. In this take, F keeps the dominant seventh sound, while A and E add the 9th and 13th.',
            'cue' => 'Listen for the open sound on the 5 chord, then hear it settle into C.',
        ],
        'augmented' => [
            'title' => 'Augmented dominant',
            'symbol' => 'G7♯5',
            'notes' => ['G', 'B', 'D♯', 'F'],
            'highlight' => 'D♯',
            'intro' => 'Keep the third (B) and flat seventh (F) of G7. Raise D, the fifth, to D♯. That raised fifth gives the chord extra tension before it resolves.',
            'cue' => 'Listen for D♯ rising a half step to E when the progression lands on C.',
        ],
        'thirteenth' => [
            'title' => 'Dominant thirteenth',
            'symbol' => 'G13',
            'notes' => ['G', 'B', 'F', 'E'],
            'highlight' => 'E',
            'intro' => 'Start with G7 in the left hand. The sixth of G is E, so play an E minor triad in the right hand. Use the inversion that keeps the hands close. The E gives you the 13th.',
            'cue' => 'In this take, D moves up to E while B and G stay in place. Listen for the new color when E arrives.',
        ],
    ];
@endphp

<section class="mx-auto max-w-7xl px-5 pb-10 pt-10 sm:px-8 sm:py-16">
    <div class="max-w-3xl">
        <p class="text-xs font-bold uppercase tracking-widest text-[#4f5667]">Free Gospel Piano Guide</p>
        <h1 class="mt-4 text-[clamp(2.65rem,5.6vw,5rem)] font-extrabold leading-[1.02] tracking-[-0.055em]">Three ways to voice the 5 chord in a 2–5–1.</h1>
        <p class="mt-5 max-w-2xl text-lg leading-7 text-[#4f5667]">In C major, the <strong>2</strong> chord is D minor, the <strong>5</strong> chord is G dominant, and the <strong>1</strong> chord is C major. Hear Kingsley play the same progression three times and listen to how the middle chord changes the way you arrive home.</p>
        <ol class="mt-6 grid max-w-lg grid-cols-3 gap-2 text-center" aria-label="Two five one in C major">
            <li class="rounded-lg border border-black/10 bg-white p-3"><strong class="block text-lg">Dm</strong><span class="text-xs text-[#4f5667]">2 · Set up</span></li>
            <li class="rounded-lg bg-[#ffd736] p-3"><strong class="block text-lg">G7</strong><span class="text-xs">5 · Build tension</span></li>
            <li class="rounded-lg border border-black/10 bg-white p-3"><strong class="block text-lg">C</strong><span class="text-xs text-[#4f5667]">1 · Resolve</span></li>
        </ol>
    </div>
</section>

<div data-accompaniment-lesson data-note-source="{{ asset($base . '/notes.json') }}">
    @foreach($voicings as $slug => $voicing)
        <section id="{{ $slug }}" aria-labelledby="{{ $slug }}-heading" class="border-t border-black/10 {{ $loop->odd ? 'bg-white' : '' }}">
            <div class="mx-auto grid max-w-7xl gap-7 px-5 py-10 sm:px-8 sm:py-14 lg:grid-cols-[0.95fr_1.05fr] lg:items-center lg:gap-12">
                <div>
                    <p class="mb-2 text-sm font-bold text-[#1447A6]">{{ $voicing['symbol'] }}</p>
                    <h2 id="{{ $slug }}-heading" class="text-2xl font-bold tracking-tight sm:text-3xl">{{ $voicing['title'] }}</h2>
                    <p class="mt-3 leading-7 text-[#4f5667]">{{ $voicing['intro'] }}</p>
                    <div class="mt-4 flex flex-wrap gap-1.5" aria-label="Notes in the recorded G voicing">
                        @foreach($voicing['notes'] as $note)
                            <span class="rounded-md px-2.5 py-1.5 text-sm font-semibold {{ $note === $voicing['highlight'] ? 'bg-[#ffd736]' : 'bg-[#e8edf5]' }}">{{ $note }}</span>
                        @endforeach
                    </div>
                </div>
                @include('guides.performance-player', [
                    'recording' => $slug,
                    'playerTitle' => 'Hear ' . $voicing['symbol'] . ' in the progression',
                    'playerLabel' => $voicing['title'] . ' recording',
                    'cue' => $voicing['cue'],
                    'audioPath' => $base . '/' . $slug . '.mp3',
                    'duration' => 3,
                ])
            </div>
        </section>
    @endforeach
</div>

<section class="border-y border-black/10 bg-white" aria-labelledby="secondary-dominants">
    <div class="mx-auto max-w-7xl px-5 py-10 sm:px-8 sm:py-14">
        <h2 id="secondary-dominants" class="text-3xl font-bold tracking-tight sm:text-4xl">Use the same voicings to lead to other chords.</h2>
        <p class="mt-4 max-w-3xl leading-7 text-[#4f5667]">G7 → C is the main dominant resolving to the home chord. A <strong>secondary dominant</strong> leads to another chord in the key. For example, <strong>D7 → G</strong> makes G sound like a temporary destination. You can voice D7 as a suspended, augmented, or 13th chord and still aim for G.</p>
        <div class="mt-6 overflow-hidden rounded-xl border border-black/10">
            <table class="w-full text-left text-sm">
                <caption class="sr-only">Secondary dominant resolutions in C major</caption>
                <thead class="bg-[#f9f7f0] text-[#4f5667]">
                    <tr>
                        <th scope="col" class="px-3 py-3 sm:px-5">Root movement</th>
                        <th scope="col" class="px-3 py-3 sm:px-5">Play</th>
                        <th scope="col" class="px-3 py-3 sm:px-5">Function</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/10">
                    @foreach([
                        ['2 → 5', 'D7 → G major', 'V/V'],
                        ['3 → 6', 'E7 → A minor', 'V/vi'],
                        ['7 → 3', 'B7 → E minor', 'V/iii'],
                        ['1 → 4', 'C7 → F major', 'V/IV'],
                        ['6 → 2', 'A7 → D minor', 'V/ii'],
                    ] as [$movement, $chords, $function])
                        <tr>
                            <th scope="row" class="px-3 py-3 font-semibold sm:px-5">{{ $movement }}</th>
                            <td class="px-3 py-3 sm:px-5">{{ $chords }}</td>
                            <td class="px-3 py-3 text-[#4f5667] sm:px-5">{{ $function }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="mt-5 max-w-3xl text-sm leading-6 text-[#4f5667]">Try D7 → G first. For a D13, play D7 in the left hand and B minor in the right, because B is the sixth of D. Then try one of the other destinations in the chart.</p>
    </div>
</section>

@include('guides.membership-cta', [
    'heading' => 'Hear where each dominant voicing fits in a song.',
    'description' => 'Practise suspended, augmented, and 13th voicings with guided exercises and gospel song breakdowns. Work through the resolutions at the piano and learn to choose the sound that fits the melody.',
])
@endsection

@section('scripts')
    <script type="module" src="{{ asset('guide-assets/shared/performance-player.js') }}"></script>
@endsection
