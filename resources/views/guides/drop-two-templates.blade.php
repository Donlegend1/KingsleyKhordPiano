@extends('guides.layout')

@section('title', $guide['title'])
@section('description', $guide['description'])

@section('styles')
    <link rel="stylesheet" href="{{ asset('guide-assets/chord-extensions/guide.css') }}">
    <link rel="stylesheet" href="{{ asset('guide-assets/shared/performance-player.css') }}">
@endsection

@section('guide')
@php
    $base = 'guide-assets/drop-two-templates';
    $whiteKeys = [36, 38, 40, 41, 43, 45, 47, 48, 50, 52, 53, 55, 57, 59, 60, 62, 64, 65, 67, 69, 71, 72];
    $template = [
        ['Do', 'C', 'F/A', ['A', 'F', 'C']],
        ['Re', 'D', 'G/B', ['B', 'G', 'D']],
        ['Mi', 'E', 'C', ['C', 'G', 'E']],
        ['Fa', 'F', 'Dm', ['D', 'A', 'F']],
        ['Sol', 'G', 'C/E', ['E', 'C', 'G']],
        ['La', 'A', 'F', ['F', 'C', 'A']],
        ['Ti', 'B', 'G', ['G', 'D', 'B']],
    ];
    $songs = [
        'birthday' => [
            'name' => 'Happy Birthday',
            'intro' => 'Hear the whole C major scale put to work in a familiar tune. Both takes finish on C/G, so listen for the changes before that ending.',
            'template_duration' => 23,
            'variation_duration' => 23,
            'template_cue' => 'Listen for the F/A shape underneath the first C in the melody.',
            'variation_cue' => 'At the end of the second phrase, listen to the D in the melody. It now sits above B–F–A instead of B–G.',
            'comparison' => 'Later, the two Fs that open the last phrase keep F on top. Underneath, the A moves to A♭, turning D minor into D diminished.',
        ],
        'saints' => [
            'name' => 'When the Saints Go Marching In',
            'intro' => 'The opening C–E–F–G is easy to sing. Follow those four notes through both versions and notice how much the harmony changes around the tune.',
            'template_duration' => 28,
            'variation_duration' => 32,
            'template_cue' => 'The first four chords follow the chart: F/A → C → Dm → C/E.',
            'variation_cue' => 'The voicings under C, E, and F fill out. On G, the original E–C–G shape returns.',
            'comparison' => 'Compare the F in the opening phrase. D–A–F becomes D–A♭–C–F. The top note stays F, so the melody still sounds like the same song.',
        ],
    ];
@endphp

<section class="mx-auto grid max-w-7xl gap-8 px-5 py-10 sm:px-8 sm:py-16 lg:grid-cols-[1.15fr_0.85fr] lg:items-center lg:gap-12">
    <div>
        <p class="text-xs font-bold uppercase tracking-widest text-[#4f5667]">Free Piano Guide · Drop Two Templates</p>
        <h1 class="mt-4 text-[clamp(2.65rem,5.8vw,4.8rem)] font-extrabold leading-[1.04] tracking-[-0.055em]">Give every melody note a chord shape</h1>
        <p class="mt-5 max-w-xl text-lg leading-7 text-[#4f5667]">You can play the tune, but what goes underneath it? Start with one ready-made shape for each note in the C major scale. Find the melody note in the chart, play its shape, and keep the tune as the highest note.</p>
        <div class="mt-6 flex flex-wrap items-center gap-x-5 gap-y-3">
            <a href="#guide-pdf" class="inline-flex min-h-12 items-center gap-2 rounded-lg bg-[#ffd736] px-5 py-3 text-sm font-bold hover:bg-[#ffe273]">Get the Free 12-Key PDF <span aria-hidden="true">↓</span></a>
            <a href="#drop-two-chart" class="text-sm font-bold text-[#1447A6] underline-offset-4 hover:underline">See the seven shapes</a>
        </div>
    </div>
    <figure class="rounded-2xl bg-[#151b29] p-5 text-white sm:p-7">
        <figcaption class="text-xl font-bold">Try it with C</figcaption>
        <p class="mt-2 text-sm leading-6 text-[#d0d9e9]">When the tune lands on C, put A and F below it. Play all three notes together and listen for C on top.</p>
        <div class="mt-5 grid grid-cols-3 gap-2 text-center">
            @foreach([['A', 'Lowest note'], ['F', 'Middle note'], ['C', 'Melody']] as [$note, $role])
                <div class="rounded-lg p-3 {{ $loop->last ? 'bg-[#ffd736] text-[#151b29]' : 'bg-white/10' }}">
                    <strong class="block text-2xl">{{ $note }}</strong>
                    <span class="mt-1 block text-xs">{{ $role }}</span>
                </div>
            @endforeach
        </div>
        <p class="mt-4 text-sm leading-6 text-[#d0d9e9]">A drop-two voicing opens up a chord by moving its second-highest note down an octave. These templates give you the shapes up front so you can concentrate on the melody.</p>
    </figure>
</section>

<div data-accompaniment-lesson data-note-source="{{ asset($base . '/notes.json') }}">
    <section id="drop-two-chart" class="scroll-mt-5 border-y border-black/10 bg-white">
        <div class="mx-auto max-w-7xl px-5 py-10 sm:px-8 sm:py-14">
            <h2 class="text-2xl font-bold sm:text-3xl">The seven-note template in C</h2>
            <p class="mt-2 max-w-2xl leading-7 text-[#4f5667]">Read each shape from its lowest note up to the melody. When you reach the next Do, use the same shape an octave higher.</p>
            <ul class="mt-5 grid grid-cols-2 gap-2 sm:grid-cols-4 lg:grid-cols-7">
                @foreach($template as [$degree, $melody, $chord, $notes])
                    <li class="rounded-xl border border-black/10 bg-[#f9f7f0] p-3">
                        <p class="text-sm font-bold text-[#1447A6]">{{ $degree }} · {{ $melody }}</p>
                        <p class="mt-1 font-bold">{{ $chord }}</p>
                        <p class="mt-2 flex flex-wrap items-center gap-1 text-sm">
                            @foreach($notes as $note)
                                <span class="rounded px-1.5 py-0.5 {{ $loop->last ? 'bg-[#ffd736] font-bold' : 'bg-white' }}">{{ $note }}</span>
                            @endforeach
                        </p>
                    </li>
                @endforeach
            </ul>
            <div class="mt-6 max-w-2xl">
                @include('guides.performance-player', [
                    'recording' => 'scale-template',
                    'playerTitle' => 'Hear the template up and down the scale',
                    'playerLabel' => 'Drop two template in C',
                    'cue' => 'Follow the top note from Do to the upper Do and back. The keyboard shows the notes as Kingsley plays them.',
                    'audioPath' => $base . '/scale-template.mp3',
                    'duration' => 16,
                ])
            </div>
        </div>
    </section>

    @foreach($songs as $slug => $song)
        <section class="border-b border-black/10 {{ $loop->last ? 'bg-white' : '' }}" aria-labelledby="drop-two-{{ $slug }}">
            <div class="mx-auto max-w-7xl px-5 py-10 sm:px-8 sm:py-14">
                <h2 id="drop-two-{{ $slug }}" class="text-2xl font-bold sm:text-3xl">{{ $song['name'] }}</h2>
                <p class="mt-2 max-w-3xl leading-7 text-[#4f5667]">{{ $song['intro'] }}</p>
                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    @foreach(['template' => 'Using the template', 'variation' => 'Changing the voicings'] as $take => $title)
                        @include('guides.performance-player', [
                            'recording' => $slug . '-' . $take,
                            'playerTitle' => $title,
                            'playerLabel' => $song['name'] . ' · ' . $title,
                            'cue' => $song[$take . '_cue'],
                            'audioPath' => $base . '/' . $slug . '-' . $take . '.mp3',
                            'duration' => $song[$take . '_duration'],
                        ])
                    @endforeach
                </div>
                <p class="mt-4 max-w-3xl rounded-xl border border-black/10 bg-[#f9f7f0] px-4 py-3 text-sm leading-6 text-[#4f5667]">{{ $song['comparison'] }}</p>
            </div>
        </section>
    @endforeach
</div>

<section id="guide-pdf" aria-labelledby="drop-two-pdf-heading" class="mx-auto max-w-7xl scroll-mt-6 px-5 pt-10 sm:px-8 sm:pt-14">
    <div class="grid gap-5 rounded-2xl border border-black/10 bg-white p-5 sm:p-7 md:grid-cols-[1fr_22rem] md:items-center">
        <div>
            <h2 id="drop-two-pdf-heading" class="text-2xl font-bold tracking-tight sm:text-3xl">Take the template to all 12 keys</h2>
            <p id="drop-two-pdf-details" class="mt-2 max-w-xl text-sm leading-6 text-[#4f5667]">Keep the chart beside your piano. Each page gives you the original shapes for one major key, with separate hand labels and color-coded keyboards to show exactly where each note sits.</p>
        </div>
        <form action="{{ route('guides.pdf.request', $guideSlug) }}" method="POST">
            @csrf
            <label for="drop-two-email" class="sr-only">Email address</label>
            <input id="drop-two-email" name="email" type="email" autocomplete="email" inputmode="email" maxlength="254" required
                   placeholder="Enter your email" value="{{ old('email') }}" aria-describedby="drop-two-pdf-details"
                   class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm">
            <button type="submit" class="mt-2 w-full rounded-xl bg-[#ffd736] px-4 py-3 text-sm font-bold hover:bg-[#ffe273]">Email Me the Free PDF</button>
            @error('email')
                <p role="alert" class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>
            @enderror
        </form>
    </div>
</section>

@include('guides.membership-cta', [
    'heading' => 'Take one line from a song you love and make it your own.',
    'description' => 'Start with the template, then learn how to swap a voicing, keep the melody clear, and choose a chord that sounds right at the end of a phrase. Inside the membership, guided piano exercises and gospel song breakdowns give you a place to practise each choice.',
])
@endsection

@section('scripts')
    <script type="module" src="{{ asset('guide-assets/shared/performance-player.js') }}"></script>
@endsection
