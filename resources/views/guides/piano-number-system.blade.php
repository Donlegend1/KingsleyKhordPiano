@extends('guides.layout')

@section('title', $guide['title'])
@section('description', $guide['description'])

@section('styles')
    <link rel="stylesheet" href="{{ asset('guide-assets/chord-extensions/guide.css') }}">
@endsection

@section('guide')
@php
    $scales = [
        'C' => ['C', 'D', 'E', 'F', 'G', 'A', 'B'],
        'D♭' => ['D♭', 'E♭', 'F', 'G♭', 'A♭', 'B♭', 'C'],
        'D' => ['D', 'E', 'F♯', 'G', 'A', 'B', 'C♯'],
        'E♭' => ['E♭', 'F', 'G', 'A♭', 'B♭', 'C', 'D'],
        'E' => ['E', 'F♯', 'G♯', 'A', 'B', 'C♯', 'D♯'],
        'F' => ['F', 'G', 'A', 'B♭', 'C', 'D', 'E'],
        'F♯' => ['F♯', 'G♯', 'A♯', 'B', 'C♯', 'D♯', 'E♯'],
        'G' => ['G', 'A', 'B', 'C', 'D', 'E', 'F♯'],
        'A♭' => ['A♭', 'B♭', 'C', 'D♭', 'E♭', 'F', 'G'],
        'A' => ['A', 'B', 'C♯', 'D', 'E', 'F♯', 'G♯'],
        'B♭' => ['B♭', 'C', 'D', 'E♭', 'F', 'G', 'A'],
        'B' => ['B', 'C♯', 'D♯', 'E', 'F♯', 'G♯', 'A♯'],
    ];
    $qualities = ['Major', 'Minor', 'Minor', 'Major', 'Major', 'Minor', 'Diminished'];
    $progression = [4, 1, 5, 6];
@endphp

<section class="mx-auto grid max-w-7xl gap-8 px-5 py-10 sm:px-8 sm:py-16 lg:grid-cols-2 lg:items-center lg:gap-12">
    <div>
        <p class="text-xs font-bold uppercase tracking-widest text-[#4f5667]">Free Piano Guide</p>
        <h1 class="mt-4 text-[clamp(2.55rem,5.6vw,4.65rem)] font-extrabold leading-[1.04] tracking-[-0.055em]">Play the same song in all 12 keys</h1>
        <p class="mt-5 max-w-xl text-lg leading-7 text-[#4f5667]">A singer asks you to move a song from C to E. Where do your hands go? With the piano number system, you remember the order of the chords as numbers. Change the key and the numbers tell you which chords to play.</p>
        <a href="#number-system-demo" class="mt-6 inline-flex min-h-12 items-center gap-2 rounded-lg bg-[#ffd736] px-5 py-3 text-sm font-bold hover:bg-[#ffe273]">Try changing the key <span aria-hidden="true">↓</span></a>
        <a href="#guide-pdf" class="mt-3 block w-fit text-sm font-bold text-[#1447A6] underline-offset-4 hover:underline">Get the free PDF</a>
    </div>
    <figure class="rounded-2xl bg-[#151b29] p-5 text-white sm:p-7">
        <figcaption class="text-xl font-bold">Same numbers, new chords</figcaption>
        <div class="mt-4 grid grid-cols-[auto_repeat(4,minmax(0,1fr))] items-center gap-1.5 text-center text-sm sm:gap-2">
            <span class="text-xs text-[#d0d9e9]">Number</span>
            @foreach($progression as $number)
                <strong class="rounded-md bg-[#ffd736] py-2 text-[#151b29]">{{ $number }}</strong>
            @endforeach
            @foreach(['C' => ['F', 'C', 'G', 'Am'], 'E' => ['A', 'E', 'B', 'C♯m']] as $key => $chords)
                <strong class="text-xs text-[#d0d9e9]">In {{ $key }}</strong>
                @foreach($chords as $chord)
                    <span class="rounded-md bg-white/10 py-2 font-bold">{{ $chord }}</span>
                @endforeach
            @endforeach
        </div>
        <p class="mt-4 text-sm leading-6 text-[#d0d9e9]">Read each row from left to right. A little <strong>m</strong> beside a chord means minor.</p>
    </figure>
</section>

<section class="border-y border-black/10 bg-white">
    <div class="mx-auto max-w-7xl px-5 py-10 sm:px-8 sm:py-14">
        <h2 class="text-2xl font-bold sm:text-3xl">Start with the notes, then build the chords</h2>
        <div class="mt-5 grid gap-4 lg:grid-cols-2">
            <div class="min-w-0 rounded-xl border border-black/10 bg-[#f9f7f0] p-4 sm:p-5">
                <h3 class="text-lg font-bold">1. Give each scale note a number</h3>
                <p class="mt-2 text-sm leading-6 text-[#4f5667]">The first note of a major scale is always 1. In C major, C is 1. Follow the white keys up through B, which is 7. The next C starts the pattern again.</p>
                <div class="guide-keyboard guide-degree-keyboard mt-4" aria-hidden="true">
                    <div class="guide-keyboard-octave" style="--octave-keys: 8">
                        @foreach([...$scales['C'], 'C'] as $index => $note)
                            <span class="guide-white-key guide-degree-key" @if($loop->first || $loop->last) data-hand="left" @endif>
                                {{ $note }} <strong>{{ $index % 7 + 1 }}</strong>
                            </span>
                            @if(in_array($index, [0, 1, 3, 4, 5], true))
                                <span class="guide-black-key" style="--key-edge: {{ $index + 1 }}"></span>
                            @endif
                        @endforeach
                    </div>
                </div>
                <p class="mt-3 text-sm leading-6 text-[#4f5667]">Other major scales use black keys too. Count the seven notes <strong>in the scale</strong>.</p>
            </div>
            <div class="rounded-xl border border-black/10 bg-[#f9f7f0] p-4 sm:p-5">
                <h3 class="text-lg font-bold">2. Learn the chord type for each number</h3>
                <p class="mt-2 text-sm leading-6 text-[#4f5667]">A <strong>triad</strong> is a three-note chord. Start on the numbered note, then take every other note from the scale until you have three. In C, the 4 starts on F, so play <strong>F–A–C</strong>. That’s F major.</p>
                <dl class="mt-4 grid gap-2 text-sm">
                    <div class="flex flex-wrap items-center justify-between gap-2 rounded-md bg-blue-100 px-3 py-2"><dt class="font-bold">Major</dt><dd>1 · 4 · 5</dd></div>
                    <div class="flex flex-wrap items-center justify-between gap-2 rounded-md bg-emerald-100 px-3 py-2"><dt class="font-bold">Minor</dt><dd>2 · 3 · 6</dd></div>
                    <div class="flex flex-wrap items-center justify-between gap-2 rounded-md bg-amber-100 px-3 py-2"><dt class="font-bold">Diminished</dt><dd>7</dd></div>
                </dl>
                <p class="mt-3 text-sm leading-6 text-[#4f5667]">This is the basic chord pattern for every major key. To build a triad from any starting key on the piano, count half steps between its notes. Move to the very next piano key for each half step. Major is <strong>4 then 3</strong>, minor is <strong>3 then 4</strong>, and diminished is <strong>3 then 3</strong>.</p>
            </div>
        </div>
    </div>
</section>

<section id="number-system-demo" class="mx-auto max-w-7xl scroll-mt-6 px-5 py-10 sm:px-8 sm:py-14" data-number-system>
    <div class="rounded-2xl bg-[#151b29] p-5 text-white sm:p-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold sm:text-3xl">Take 4–1–5–6 to another key</h2>
                <p class="mt-2 text-sm leading-6 text-[#d0d9e9]">Find each number in the scale, then use the chord type printed under it.</p>
            </div>
            <label for="number-system-key" class="text-sm font-bold" hidden data-key-picker>Choose a major key
                <select id="number-system-key" class="mt-1 block min-w-36 rounded-lg border border-white/25 bg-[#242e42] px-3 py-2 text-sm text-white">
                    @foreach($scales as $key => $scale)
                        <option value="{{ $key }}" @selected($key === 'E')>{{ $key }} major</option>
                    @endforeach
                </select>
            </label>
        </div>
        <div class="mt-5" aria-live="polite">
            @foreach($scales as $key => $scale)
                <div data-key-panel="{{ $key }}" @if($key !== 'E') hidden @endif>
                    <h3 class="text-sm font-bold text-[#d0d9e9]">{{ $key }} major scale and chords</h3>
                    <ol class="mt-2 grid grid-cols-4 gap-2 sm:grid-cols-7">
                        @foreach($scale as $index => $note)
                            <li class="rounded-lg p-2 text-center {{ in_array($index + 1, $progression, true) ? 'bg-[#ffd736]/20 ring-1 ring-inset ring-[#ffd736]/40' : 'bg-white/5' }}">
                                <span class="block text-xs text-[#ffd736]">{{ $index + 1 }}</span>
                                <strong class="block">{{ $note }}</strong>
                                <span class="block text-xs text-[#d0d9e9]">{{ $qualities[$index] }}</span>
                            </li>
                        @endforeach
                    </ol>
                    <h3 class="mt-5 text-sm font-bold text-[#d0d9e9]">Play these chords from left to right</h3>
                    <ol class="mt-2 grid grid-cols-2 gap-2 sm:grid-cols-4">
                        @foreach($progression as $number)
                            @php($position = $number - 1)
                            <li class="rounded-lg bg-[#ffd736] p-3 text-[#151b29]">
                                <span class="block text-xs font-bold">{{ $number }} chord</span>
                                <strong class="block">{{ $scale[$position] }} {{ strtolower($qualities[$position]) }}</strong>
                                <span class="block text-sm">{{ $scale[$position] }}–{{ $scale[($position + 2) % 7] }}–{{ $scale[($position + 4) % 7] }}</span>
                            </li>
                        @endforeach
                    </ol>
                    @if($key === 'E')
                        <p class="mt-4 text-sm leading-6 text-[#d0d9e9]">In E major, the 6 is C♯. You might also know that piano key as D♭. In this scale, we spell the chord C♯ minor.</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    <div class="mt-7 max-w-3xl">
        <h2 class="text-xl font-bold">Make it a short daily practice</h2>
        <p class="mt-2 leading-7 text-[#4f5667]">Pick one key. Say its scale notes and numbers out loud, then play 4–1–5–6 slowly. Try another key and say the same numbers as you play. As you practise, learn the major, minor, and diminished triads from all 12 starting notes so you can find each shape more quickly.</p>
    </div>
</section>

<section id="guide-pdf" aria-labelledby="number-system-pdf-heading" class="mx-auto max-w-7xl scroll-mt-6 px-5 pt-10 sm:px-8 sm:pt-14">
    <div class="grid gap-5 rounded-2xl border border-black/10 bg-white p-5 sm:p-7 md:grid-cols-[1fr_22rem] md:items-center">
        <div>
            <h2 id="number-system-pdf-heading" class="text-2xl font-bold tracking-tight sm:text-3xl">Get the 12-key chord chart</h2>
            <p id="number-system-pdf-details" class="mt-2 max-w-xl text-sm leading-6 text-[#4f5667]">Keep all 12 major keys beside your piano. The printable chart shows the chord quality for each number and the exact three notes to play.</p>
        </div>
        <form action="{{ route('guides.pdf.request', $guideSlug) }}" method="POST">
            @csrf
            <label for="number-system-email" class="sr-only">Email address</label>
            <input id="number-system-email" name="email" type="email" autocomplete="email" inputmode="email" maxlength="254" required
                   placeholder="Enter your email" value="{{ old('email') }}" aria-describedby="number-system-pdf-details"
                   class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm">
            <button type="submit" class="mt-2 w-full rounded-xl bg-[#ffd736] px-4 py-3 text-sm font-bold hover:bg-[#ffe273]">Email Me the Free PDF</button>
            @error('email')
                <p role="alert" class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>
            @enderror
        </form>
    </div>
</section>

@include('guides.membership-cta', [
    'heading' => 'Play a song you know in a key you usually avoid.',
    'description' => 'Use guided piano practice and gospel song breakdowns to connect scale numbers to real chords. Work on the triad shapes, then move a progression to a new key one phrase at a time.',
])
@endsection

@section('scripts')
    <script type="module" src="{{ asset('guide-assets/piano-number-system/key-selector.js') }}"></script>
@endsection
