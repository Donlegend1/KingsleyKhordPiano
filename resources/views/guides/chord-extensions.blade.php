@extends('guides.layout')

@section('title', $guide['title'])
@section('description', 'Use left-hand bass shapes and right-hand chords to build richer gospel piano voicings. Try the interactive keyboard and download the free charts for all 12 major keys.')

@section('guide')
@php
    $primaryButton = 'inline-flex min-h-12 items-center justify-center gap-2.5 rounded-lg bg-[#ffd736] px-5 py-3 text-sm font-bold text-[#161a22] transition hover:bg-[#ffe273] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4';
@endphp

<section class="mx-auto grid max-w-7xl gap-8 px-5 pb-12 pt-10 sm:px-8 sm:py-16 lg:grid-cols-[0.82fr_1.18fr] lg:items-center lg:gap-9 lg:py-20">
    <div>
        <p class="mb-4 flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-[#4f5667]">
            <span class="h-2 w-2 rounded-full bg-[#e3aa03]" aria-hidden="true"></span>
            Free Gospel Piano Guide
        </p>
        <h1 class="max-w-xl text-[clamp(2.75rem,5.8vw,5.35rem)] font-extrabold leading-[0.99] tracking-[-0.06em]">Make simple chords sound richer.</h1>
        <p class="mt-5 max-w-lg text-base leading-7 text-[#4f5667] sm:text-lg">
            Build chord extensions with a bass shape in your left hand and a familiar chord in your right. Compare C major, D minor, and G dominant in the key of C, and hear how each changes as you add extensions.
        </p>
        <form id="guide-pdf" action="{{ route('guides.pdf.request', 'chord-extensions') }}" method="POST" class="mt-7 max-w-sm scroll-mt-6">
            @csrf
            <label for="guide-email" class="sr-only">Email address</label>
            <input id="guide-email" name="email" type="email" autocomplete="email" inputmode="email" maxlength="254" required
                   placeholder="Enter your email" value="{{ old('email') }}"
                   aria-describedby="guide-pdf-details"
                   class="block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-[#151a22] placeholder:text-gray-500 focus:border-[#1447A6] focus:outline focus:outline-1 focus:outline-[#1447A6]">
            <button type="submit" class="{{ $primaryButton }} mt-2 w-full">Email Me the Free PDF</button>
            @error('email')
                <p class="mt-2 text-sm font-medium text-red-700" role="alert">{{ $message }}</p>
            @enderror
            <p id="guide-pdf-details" class="mt-2 text-xs leading-5 text-[#545a67]">73 pages · All 12 major keys</p>
        </form>
    </div>

    <div id="voicing-lab" data-voicing-lab class="scroll-mt-4 rounded-2xl bg-[#151b29] p-4 text-white shadow-xl sm:p-6">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-[#ffdf60]">Interactive Keyboard</p>
                <h2 class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl" data-chord-name>Cmaj7</h2>
            </div>
            <button type="button" data-play-chord hidden class="min-h-11 rounded-lg border border-white/30 px-3.5 text-sm font-semibold hover:bg-white/10 focus-visible:outline focus-visible:outline-2 focus-visible:outline-white">
                <span aria-hidden="true">▶</span> <span data-play-label>Hear chord</span>
            </button>
        </div>
        <div data-quality-field hidden class="mt-5">
            <label for="guide-quality" class="text-xs font-semibold text-[#d0d9e9]">Chord quality · Key of C major</label>
            <select id="guide-quality" data-chord-quality class="mt-1 block w-full max-w-xs rounded-md border border-white/25 bg-[#202a3c] px-2 py-2 text-sm text-white">
                <option value="major">Major · C (I)</option>
                <option value="minor">Minor · Dm (ii)</option>
                <option value="dominant">Dominant · G7 (V7)</option>
            </select>
        </div>
        <div class="mt-5 flex flex-wrap gap-1.5" data-chord-options hidden aria-label="Choose a chord">
            <button type="button" data-voicing="seventh" aria-pressed="true">7th</button>
            <button type="button" data-voicing="ninth" aria-pressed="false">9th</button>
            <button type="button" data-voicing="eleventh" aria-pressed="false">♯11th</button>
            <button type="button" data-voicing="thirteenth" aria-pressed="false">13th</button>
        </div>
        <div class="mt-5 grid grid-cols-2 gap-2.5">
            <div class="min-w-0 rounded-lg bg-white/10 p-3">
                <label for="guide-bass" class="flex items-center gap-2 text-xs font-semibold text-[#ffdf60]"><span class="h-2 w-2 rounded-full bg-[#ffd736]" aria-hidden="true"></span>Left hand</label>
                <select id="guide-bass" data-bass hidden class="mt-2 w-full rounded-md border border-white/25 bg-[#202a3c] px-2 py-1.5 text-xs text-white">
                    <option value="root">Root only</option><option value="fifth">Root + fifth</option><option value="octave">Octave</option>
                    <option value="octaveFifth">Octave + fifth</option><option value="tenth">Spread tenth</option><option id="guide-bass-seventh" value="seventh">Root + major seventh</option><option id="guide-bass-triad" value="triad">Major triad</option><option id="guide-bass-shell" value="seventhShell">Root + third + major seventh</option>
                </select>
                <p data-left-notes class="mt-2 text-sm font-bold">C3</p>
            </div>
            <div class="min-w-0 rounded-lg bg-white/10 p-3">
                <p class="flex items-center gap-2 text-xs font-semibold text-[#a5d5ff]"><span class="h-2 w-2 rounded-full bg-[#88caff]" aria-hidden="true"></span>Right hand</p>
                <select id="guide-right-shape" data-shape-options aria-label="Right-hand shape" hidden
                        class="mt-2 w-full rounded-md border border-white/25 bg-[#202a3c] px-2 py-1.5 text-xs text-white"></select>
                <select id="guide-inversion" data-inversion aria-label="Right-hand inversion" hidden class="mt-2 w-full rounded-md border border-white/25 bg-[#202a3c] px-2 py-1.5 text-xs text-white">
                    <option value="0">Root position</option><option value="1">1st inversion</option><option value="2">2nd inversion</option><option value="3" hidden>3rd inversion</option>
                </select>
                <p data-right-shape class="mt-2 truncate text-xs text-[#c6d6e9]">E minor</p>
                <p data-right-notes class="mt-0.5 text-sm font-bold">E3 · G3 · B3</p>
            </div>
        </div>
        <div class="guide-keyboard mt-4" aria-hidden="true">
            @php
                $whiteNotes = [0, 2, 4, 5, 7, 9, 11];
                $blackNotes = [1 => 1, 3 => 2, 6 => 4, 8 => 5, 10 => 6];
            @endphp
            @foreach(range(2, 6) as $octave)
                @php $keyCount = $octave === 6 ? 3 : 7; @endphp
                <div class="guide-keyboard-octave" style="flex: {{ $keyCount }}; --octave-keys: {{ $keyCount }}">
                    @foreach(array_slice($whiteNotes, 0, $keyCount) as $note)
                        @php $midi = 12 * ($octave + 1) + $note; @endphp
                        <span class="guide-white-key" data-midi="{{ $midi }}" @if($midi === 48) data-hand="left" @elseif(in_array($midi, [52, 55, 59], true)) data-hand="right" @endif></span>
                    @endforeach
                    @foreach(array_slice($blackNotes, 0, $octave === 6 ? 2 : 5, true) as $note => $edge)
                        <span class="guide-black-key" style="--key-edge: {{ $edge }}" data-midi="{{ 12 * ($octave + 1) + $note }}"></span>
                    @endforeach
                </div>
            @endforeach
        </div>
        <p data-voicing-note aria-live="polite" class="mt-4 min-h-10 text-sm leading-relaxed text-[#d0d9e9]">Hold C in your left hand. Play E–G–B, an E minor triad, in your right. Together they give you Cmaj7.</p>
    </div>
</section>

<section aria-labelledby="build-extensions" class="border-y border-black/10 bg-white">
    <div class="mx-auto max-w-7xl px-5 py-11 sm:px-8 sm:py-16">
        <h2 id="build-extensions" class="text-3xl font-bold tracking-tight sm:text-4xl">Where do 7, 9, 11, and 13 come from?</h2>
        <p class="mt-4 max-w-3xl leading-7 text-[#4f5667]">Start with the C major triad <strong>C–E–G</strong>. Count C as 1 and take every other note of the C major scale. The triad uses 1, 3, and 5. Keep skipping a note and you get 7, then 9, 11, and 13. The count keeps going when you reach the next octave.</p>

        <figure class="mt-7 rounded-xl border border-black/10 bg-[#f9f7f0] p-3 sm:p-5">
            <figcaption class="mb-3 text-sm font-semibold">Count from C across two octaves</figcaption>
            <p class="sr-only">The first C is 1, E is 3, G is 5, and B is 7. In the next octave C is 8, D is 9, F is 11, F sharp is the raised 11th, and A is 13.</p>
            <div class="guide-keyboard guide-degree-keyboard" aria-hidden="true">
                @for($octave = 0; $octave < 2; $octave++)
                    <div class="guide-keyboard-octave">
                        @foreach(['C', 'D', 'E', 'F', 'G', 'A', 'B'] as $index => $note)
                            @php $degree = $octave * 7 + $index + 1; @endphp
                            <span class="guide-white-key guide-degree-key" @if(in_array($degree, [1, 3, 5, 7, 9, 13], true)) data-degree="{{ $degree }}" @endif>
                                <span>{{ $note }}</span>
                                <strong>{{ $degree }}</strong>
                            </span>
                        @endforeach
                        @foreach([1, 2, 4, 5, 6] as $edge)
                            <span class="guide-black-key" style="--key-edge: {{ $edge }}" @if($octave === 1 && $edge === 4) data-degree="sharp11" @endif>
                                @if($octave === 1 && $edge === 4)
                                    <span class="guide-raised-degree">♯11</span>
                                @endif
                            </span>
                        @endforeach
                    </div>
                @endfor
            </div>
            <p class="mt-3 text-sm leading-6 text-[#4f5667]">The yellow keys make up the C major triad. Each extension has a matching colour on the keyboard and in the cards below. The second C is <strong>8</strong>, so the next D is <strong>9</strong>. The key immediately to the right of F is <strong>F♯ (♯11)</strong>.</p>
        </figure>

        @php $degreesByNote = ['C' => 1, 'E' => 3, 'G' => 5, 'B' => 7, 'D' => 9, 'F♯' => 'sharp11', 'A' => 13]; @endphp
        <ol class="mt-7 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            @foreach([
                ['degree' => '7th', 'notes' => ['C', 'E', 'G'], 'added' => 'B', 'cue' => 'Add B to make Cmaj7.'],
                ['degree' => '9th', 'notes' => ['C', 'E', 'G', 'B'], 'added' => 'D', 'cue' => 'Add D above the octave to make Cmaj9.'],
                ['degree' => '♯11th', 'notes' => ['C', 'E', 'G', 'B', 'D'], 'added' => 'F♯', 'cue' => 'Add F♯, the raised 11th, to make Cmaj9♯11.'],
                ['degree' => '13th', 'notes' => ['C', 'E', 'G', 'B', 'D', 'F♯'], 'added' => 'A', 'cue' => 'Keep F♯ and add A, the 13th, to make Cmaj13♯11.'],
            ] as $step)
                <li class="rounded-xl border border-black/10 bg-[#f9f7f0] p-4">
                    <h3 class="text-lg font-bold">{{ $step['degree'] }} <span class="ml-1 text-sm font-semibold text-[#4f5667]">+ {{ $step['added'] }}</span></h3>
                    <div class="mt-3 flex flex-wrap gap-1">
                        @foreach($step['notes'] as $note)
                            <span class="guide-degree-chip inline-flex h-7 min-w-7 items-center justify-center rounded px-1 text-xs font-semibold" data-degree="{{ $degreesByNote[$note] }}">{{ $note }}</span>
                        @endforeach
                        <span class="guide-degree-chip inline-flex h-7 min-w-7 items-center justify-center rounded px-1 text-xs font-bold ring-1 ring-inset ring-black/30" data-degree="{{ $degreesByNote[$step['added']] }}">{{ $step['added'] }}</span>
                    </div>
                    <p class="mt-3 text-sm leading-6 text-[#4f5667]">{{ $step['cue'] }}</p>
                </li>
            @endforeach
        </ol>

        <p class="mt-5 max-w-3xl text-sm leading-6 text-[#4f5667]">These are the full note stacks. When you play, you can leave out some notes to make the chord easier to voice. For the major examples here, E gives you the major 3rd, B gives you the major 7th, and the extension gives the chord its extra colour.</p>
    </div>
</section>

<section aria-labelledby="combine-hands" class="border-b border-black/10">
    <div class="mx-auto max-w-7xl px-5 py-11 sm:px-8 sm:py-16">
        <h2 id="combine-hands" class="text-3xl font-bold tracking-tight sm:text-4xl">Build the chord with two familiar shapes.</h2>
        <div class="mt-6 grid gap-4 md:grid-cols-2">
            <div class="rounded-xl border border-black/10 bg-white p-5">
                <h3 class="flex items-center gap-2 text-lg font-bold"><span class="h-3 w-3 rounded-full bg-[#ffd736]" aria-hidden="true"></span>Left hand · Choose a bass shape</h3>
                <p class="mt-2 leading-7 text-[#4f5667]">Start with C alone. For a fuller sound, try C with its fifth, a C octave, or a spread tenth such as C–G–E. You can also use the left hand to supply a chord tone the right hand leaves out. For example, C–E–B gives you the root, major 3rd, and major 7th.</p>
            </div>
            <div class="rounded-xl border border-black/10 bg-white p-5">
                <h3 class="flex items-center gap-2 text-lg font-bold"><span class="h-3 w-3 rounded-full bg-[#88caff]" aria-hidden="true"></span>Right hand · Find a chord you know</h3>
                <p class="mt-2 leading-7 text-[#4f5667]">Take C out of C–E–G–B and you’re left with E–G–B, an E minor triad. Play E minor over C and you get Cmaj7. Invert E minor to change which note sits on top. The bass and the chord tones stay the same.</p>
            </div>
        </div>

        <div class="mt-6 overflow-hidden rounded-xl border border-black/10 bg-white">
            <div class="border-b border-black/10 px-4 py-3 sm:px-5">
                <h3 class="font-bold">Try these combinations on the keyboard</h3>
            </div>
            <table class="w-full text-left text-sm">
                <thead class="bg-[#f9f7f0] text-xs text-[#4f5667] sm:text-sm">
                    <tr>
                        <th scope="col" class="px-3 py-3 sm:px-5">Chord</th>
                        <th scope="col" class="px-3 py-3 sm:px-5">Left hand</th>
                        <th scope="col" class="px-3 py-3 sm:px-5">Right hand</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/10">
                    @foreach([
                        ['Cmaj7', 'C', 'E minor'],
                        ['Cmaj9', 'C', 'E minor 7'],
                        ['Cmaj9', 'C–G–E', 'G major'],
                        ['Cmaj9♯11', 'C–G–E', 'G major 7 or B minor'],
                        ['Cmaj13♯11', 'C–G–E', 'B minor 7'],
                        ['Cmaj13♯11', 'C–E–B', 'D major'],
                    ] as [$name, $bass, $shape])
                        <tr>
                            <th scope="row" class="px-3 py-3 font-semibold sm:px-5">{{ $name }}</th>
                            <td class="px-3 py-3 text-[#4f5667] sm:px-5">{{ $bass }}</td>
                            <td class="px-3 py-3 text-[#4f5667] sm:px-5">{{ $shape }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="mt-4 text-sm leading-6 text-[#4f5667]">Try both Cmaj9 combinations. They use the same note names, but dividing those notes between the hands changes the voicing. Then keep the same bass shape and try a different right-hand inversion.</p>
    </div>
</section>

<section aria-labelledby="full-charts" class="border-b border-black/10 bg-white">
    <div class="mx-auto grid max-w-7xl gap-8 px-5 py-11 sm:px-8 sm:py-16 lg:grid-cols-2 lg:items-center">
        <div>
            <h2 id="full-charts" class="text-3xl font-bold leading-tight tracking-tight sm:text-4xl">Use the same idea on other chords.</h2>
            <p class="mt-4 max-w-prose leading-7 text-[#4f5667]">Try degree 2 in the key of C. Put D in the bass and play an F major triad (F–A–C) in the right hand. Together they give you Dm7. Keep D in the bass and use Fmaj7 (F–A–C–E) to add the 9th and get Dm9.</p>
            <p class="mt-3 max-w-prose leading-7 text-[#4f5667]">The full PDF walks through these combinations for six chords in each of the 12 major keys. Keep it beside the piano as you practise.</p>
        </div>
        <figure class="rounded-xl border border-black/10 bg-[#f9f7f0] p-3 sm:p-4">
            <img src="{{ asset('guide-assets/chord-extensions/chart-preview.webp') }}" alt="D minor extension chart in the key of C major, showing F major for Dm7 and Fmaj7 for Dm9." width="1000" height="708" loading="lazy" class="block h-auto w-full rounded-md">
            <figcaption class="mt-3 flex flex-wrap items-center justify-between gap-2 px-1 text-sm text-[#525866]">
                <span>From the full chart: D minor extensions in C major</span>
                <a href="#guide-pdf" class="font-bold text-[#151a22] underline underline-offset-4">Get all 12 keys ↓</a>
            </figcaption>
        </figure>
    </div>
</section>

@include('guides.membership-cta', [
    'heading' => 'Put these voicings into a real gospel progression.',
    'description' => 'Join Kingsley Khord Piano for step-by-step lessons, guided practice, and song breakdowns that show you where to use these chords.',
])
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('guide-assets/chord-extensions/guide.css') }}">
@endsection

@section('scripts')
    <script type="module" src="{{ asset('guide-assets/chord-extensions/guide.js') }}"></script>
@endsection
