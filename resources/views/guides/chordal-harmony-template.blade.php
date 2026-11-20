@extends('guides.layout')

@section('title', $guide['title'])
@section('description', $guide['description'])

@section('styles')
    <link rel="stylesheet" href="{{ asset('guide-assets/chord-extensions/guide.css') }}">
    <link rel="stylesheet" href="{{ asset('guide-assets/shared/performance-player.css') }}">
@endsection

@section('guide')
@php
    $base = 'guide-assets/chordal-harmony-template';
    $whiteKeys = [36, 38, 40, 41, 43, 45, 47, 48, 50, 52, 53, 55, 57, 59, 60, 62, 64, 65, 67, 69, 71, 72];
    $template = [
        ['Do', 'C', 'Fadd9/A', ['A', 'C', 'F', 'G', 'C']],
        ['Re', 'D', 'Gadd9/B', ['B', 'D', 'G', 'A', 'D']],
        ['Mi', 'E', 'C', ['C', 'E', 'G', 'C', 'E']],
        ['Fa', 'F', 'Dm7', ['D', 'F', 'A', 'C', 'F']],
        ['Sol', 'G', 'Cadd9/E', ['E', 'G', 'C', 'D', 'G']],
        ['La', 'A', 'F', ['F', 'A', 'C', 'F', 'A']],
        ['Ti', 'B', 'G', ['G', 'B', 'D', 'G', 'B']],
    ];
    $songs = [
        'twinkle' => [
            'name' => 'Twinkle, Twinkle, Little Star',
            'intro' => 'Start with C–C–G–G–A–A–G. The original take uses the same shape whenever a melody note repeats. In the second take, listen to what changes under those repeated notes.',
            'template_duration' => 33,
            'variation_duration' => 34,
            'template_cue' => 'Both As in the opening phrase use the same F-major shape.',
            'variation_cue' => 'Listen for the second A near 0:04. The melody holds its place while the harmony moves to F♯ diminished seventh.',
            'comparison' => 'In the original take, the first and second A both have F–A–C–F underneath. In the variation, the second A sits above F♯–A–C–E♭. Try singing the melody through both versions.',
        ],
        'jesus' => [
            'name' => 'Jesus Loves Me',
            'intro' => 'The opening G–E–E–D–E–G–G shows the template in action. To hear how a single choice can change an ending, compare the D just before the final C in both recordings.',
            'template_duration' => 40,
            'variation_duration' => 39,
            'template_cue' => 'Just before the final C, D is on top of B–D–G–A (Gadd9/B).',
            'variation_cue' => 'At the same spot, D is now on top of B–F–A (Bm7♭5).',
            'comparison' => 'The bass B and the melody D stay in place. Only the notes between them change, giving the last move to C a different sound.',
        ],
    ];
@endphp

<section class="mx-auto grid max-w-7xl gap-8 px-5 py-10 sm:px-8 sm:py-16 lg:grid-cols-[1.15fr_0.85fr] lg:items-center lg:gap-12">
    <div>
        <p class="text-xs font-bold uppercase tracking-widest text-[#4f5667]">Free Piano Guide · Chordal Harmony Template</p>
        <h1 class="mt-4 text-[clamp(2.65rem,5.8vw,4.8rem)] font-extrabold leading-[1.04] tracking-[-0.055em]">Turn a familiar melody into a fuller piano arrangement</h1>
        <p class="mt-5 max-w-xl text-lg leading-7 text-[#4f5667]">Start with a tune you can sing. For each melody note, use the five-note chord shape assigned to it in the C major template. The melody stays on top, and the other four notes give it a fuller sound.</p>
        <div class="mt-6 flex flex-wrap items-center gap-x-5 gap-y-3">
            <a href="#guide-pdf" class="inline-flex min-h-12 items-center gap-2 rounded-lg bg-[#ffd736] px-5 py-3 text-sm font-bold hover:bg-[#ffe273]">Get the Free 12-Key PDF <span aria-hidden="true">↓</span></a>
            <a href="#chordal-harmony-chart" class="text-sm font-bold text-[#1447A6] underline-offset-4 hover:underline">See the seven shapes</a>
        </div>
    </div>
    <figure class="rounded-2xl bg-[#151b29] p-5 text-white sm:p-7">
        <figcaption class="text-xl font-bold">Try it with C</figcaption>
        <p class="mt-2 text-sm leading-6 text-[#d0d9e9]">When the tune lands on C, play A–C–F–G underneath. Play all five notes together and listen for the higher C as the melody.</p>
        <div class="mt-5 grid grid-cols-5 gap-1.5 text-center">
            @foreach(['A', 'C', 'F', 'G', 'C'] as $note)
                <div class="rounded-lg px-1 py-3 {{ $loop->last ? 'bg-[#ffd736] text-[#151b29]' : 'bg-white/10' }}">
                    <strong class="block text-2xl">{{ $note }}</strong>
                    
                </div>
            @endforeach
        </div>
        <p class="mt-4 text-sm leading-6 text-[#d0d9e9]">Read the notes from low to high. Yellow marks the melody. When you want a different sound, try changing the notes below it and keep that top note in place.</p>
    </figure>
</section>

<div data-accompaniment-lesson data-note-source="{{ asset($base . '/notes.json') }}">
    <section id="chordal-harmony-chart" class="scroll-mt-5 border-y border-black/10 bg-white">
        <div class="mx-auto max-w-7xl px-5 py-10 sm:px-8 sm:py-14">
            <h2 class="text-2xl font-bold sm:text-3xl">The seven-note template in C</h2>
            <p class="mt-2 max-w-2xl leading-7 text-[#4f5667]">Read each shape from its lowest note up to the melody. When you reach the next Do, use the same shape an octave higher.</p>
            <ul class="mt-5 grid grid-cols-2 gap-2 sm:grid-cols-4 xl:grid-cols-7">
                @foreach($template as [$degree, $melody, $chord, $notes])
                    <li class="rounded-xl border border-black/10 bg-[#f9f7f0] p-3">
                        <p class="text-sm font-bold text-[#1447A6]">{{ $degree }} · {{ $melody }}</p>
                        <p class="mt-1 font-bold">{{ $chord }}</p>
                        <p class="mt-2 flex flex-wrap items-center gap-1 text-sm">
                            @foreach($notes as $note)
                                <span class="rounded px-1 py-0.5 {{ $loop->last ? 'bg-[#ffd736] font-bold' : 'bg-white' }}">{{ $note }}</span>
                            @endforeach
                        </p>
                    </li>
                @endforeach
            </ul>
            <div class="mt-6 max-w-2xl">
                @include('guides.performance-player', [
                    'recording' => 'scale-template',
                    'playerTitle' => 'Hear the template up and down the scale',
                    'playerLabel' => 'Chordal harmony template in C',
                    'cue' => 'Follow the top note from Do to the upper Do and back. The keyboard shows the notes as Kingsley plays them.',
                    'audioPath' => $base . '/scale-template.mp3',
                    'duration' => 13,
                ])
            </div>
        </div>
    </section>

    @foreach($songs as $slug => $song)
        <section class="border-b border-black/10 {{ $loop->last ? 'bg-white' : '' }}" id="chordal-harmony-{{ $slug }}" aria-labelledby="chordal-harmony-{{ $slug }}-heading">
            <div class="mx-auto max-w-7xl px-5 py-10 sm:px-8 sm:py-14">
                <h2 id="chordal-harmony-{{ $slug }}-heading" class="text-2xl font-bold sm:text-3xl">{{ $song['name'] }}</h2>
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

<section id="guide-pdf" aria-labelledby="chordal-harmony-pdf-heading" class="mx-auto max-w-7xl scroll-mt-6 px-5 pt-10 sm:px-8 sm:pt-14">
    <div class="grid gap-5 rounded-2xl border border-black/10 bg-white p-5 sm:p-7 md:grid-cols-[1fr_22rem] md:items-center">
        <div>
            <h2 id="chordal-harmony-pdf-heading" class="text-2xl font-bold tracking-tight sm:text-3xl">Take the template to all 12 keys</h2>
            <p id="chordal-harmony-pdf-details" class="mt-2 max-w-xl text-sm leading-6 text-[#4f5667]">Get one illustrated chart per major key. Each chart shows your five-note chord shapes from low to high, with the melody highlighted in yellow.</p>
        </div>
        <form action="{{ route('guides.pdf.request', $guideSlug) }}" method="POST">
            @csrf
            <label for="chordal-harmony-email" class="sr-only">Email address</label>
            <input id="chordal-harmony-email" name="email" type="email" autocomplete="email" inputmode="email" maxlength="254" required
                   placeholder="Enter your email" value="{{ old('email') }}" aria-describedby="chordal-harmony-pdf-details"
                   class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm">
            <button type="submit" class="mt-2 w-full rounded-xl bg-[#ffd736] px-4 py-3 text-sm font-bold hover:bg-[#ffe273]">Email Me the Free PDF</button>
            @error('email')
                <p role="alert" class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>
            @enderror
        </form>
    </div>
</section>

@include('guides.membership-cta', [
    'heading' => 'Learn to change the harmony while keeping the tune clear.',
    'description' => 'Use guided piano exercises to get these shapes under your fingers. Then follow gospel song breakdowns to practise changing a repeated note’s harmony, choosing a different ending, and finding voicings that fit the songs you play.',
])
@endsection

@section('scripts')
    <script type="module" src="{{ asset('guide-assets/shared/performance-player.js') }}"></script>
@endsection
