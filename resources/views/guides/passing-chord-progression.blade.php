@extends('guides.layout')

@section('title', $guide['title'])
@section('description', $guide['description'])

@section('guide')
@php
    $degrees = [
        ['degree' => 2, 'bass' => 'D', 'midi' => 38, 'chords' => [
            ['Dm7', 'Minor seventh', 'F · A · C', [53, 57, 60]],
            ['D7', 'Dominant seventh', 'F♯ · A · C', [54, 57, 60]],
            ['Dm7♭5', 'Half-diminished seventh', 'F · A♭ · C', [53, 56, 60]],
        ]],
        ['degree' => 5, 'bass' => 'G', 'midi' => 43, 'chords' => [
            ['G7', 'Dominant seventh', 'F · B · D', [53, 59, 62]],
            ['Gm7♭5', 'Half-diminished seventh', 'F · B♭ · D♭', [53, 58, 61]],
        ]],
        ['degree' => 1, 'bass' => 'C', 'midi' => 36, 'chords' => [
            ['Cmaj7', 'Major seventh', 'E · G · B', [52, 55, 59]],
            ['Cm7', 'Minor seventh', 'E♭ · G · B♭', [51, 55, 58]],
            ['C6', 'Major sixth', 'E · G · A', [52, 55, 57]],
            ['Cm6', 'Minor sixth', 'E♭ · G · A', [51, 55, 57]],
            ['C7', 'Dominant seventh', 'E · G · B♭', [52, 55, 58]],
        ]],
    ];
@endphp

<section class="mx-auto max-w-7xl px-5 py-10 sm:px-8 sm:py-16">
    <div class="grid items-center gap-8 lg:grid-cols-[1.1fr_0.9fr] lg:gap-12">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-[#4f5667]">Free Gospel Piano Guide</p>
            <h1 class="mt-4 text-[clamp(2.5rem,5.6vw,5rem)] font-extrabold leading-[1.04] tracking-[-0.05em]">{{ $guide['title'] }}</h1>
            <p class="mt-5 max-w-2xl text-lg leading-7 text-[#4f5667]">Those last three chords in a gospel progression can land with a bright major sound or a darker minor sound, even when the bass plays exactly the same notes. Try it in C and hear what one chord change can do.</p>
            <a href="#progression-heading" class="mt-5 inline-block font-bold text-[#1447A6] underline underline-offset-4">Hear the difference <span aria-hidden="true">↓</span></a>
        </div>
        <figure class="rounded-2xl border border-black/10 bg-white p-4 shadow-sm sm:p-6">
            <figcaption class="text-sm font-bold text-[#1447A6]">Same bass, two ways to land</figcaption>
            <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                @foreach($degrees as $step)
                    <div class="rounded-lg bg-[#151b29] px-1 py-3 text-white">
                        <span class="block text-xs text-[#d0d9e9]">Degree {{ $step['degree'] }}</span>
                        <strong class="text-2xl">{{ $step['bass'] }}</strong>
                    </div>
                @endforeach
                @foreach(['Dm7', 'G7', 'Cmaj7', 'Dm7', 'G7', 'Cm6'] as $chord)
                    <span class="rounded-lg px-1 py-2 text-sm font-bold {{ $chord === 'Cm6' ? 'bg-[#ffd736]' : 'bg-[#eff2f6]' }}">{{ $chord }}</span>
                @endforeach
            </div>
            <p class="mt-4 text-sm leading-6 text-[#4f5667]">Both versions land on C in the left hand. Changing the right-hand notes gives that last chord a different sound.</p>
        </figure>
    </div>

    <section class="mt-10 border-t border-black/10 pt-8 sm:mt-14" aria-labelledby="progression-explained">
        <h2 id="progression-explained" class="text-2xl font-bold sm:text-3xl">What does 2–5–1 mean?</h2>
        <div class="mt-4 grid gap-3 sm:grid-cols-3">
            <div class="rounded-xl border border-black/10 bg-white p-4">
                <h3 class="font-bold">Count from C</h3>
                <p class="mt-2 text-sm leading-6 text-[#4f5667]"><strong>C, D, E, F, G, A, B</strong> is the C major scale. C is 1, D is 2, and G is 5. So a 2–5–1 moves from D to G and back to C.</p>
            </div>
            <div class="rounded-xl border border-black/10 bg-white p-4">
                <h3 class="font-bold">Keep the bass in your left hand</h3>
                <p class="mt-2 text-sm leading-6 text-[#4f5667]">Play <strong>D → G → C</strong>. The D chord sets up the move, the G chord leads toward C, and the C chord is where you land.</p>
            </div>
            <div class="rounded-xl border border-black/10 bg-white p-4">
                <h3 class="font-bold">Change the chord with your right hand</h3>
                <p class="mt-2 text-sm leading-6 text-[#4f5667]">Play <strong>E–G–B</strong> above a low C for Cmaj7. Try <strong>E♭–G–A</strong> over the same C for Cm6. The chord quality changes because the notes above the bass change.</p>
            </div>
        </div>
    </section>

    <div class="mt-8 rounded-2xl bg-[#151b29] p-5 text-white sm:p-8" data-progression data-playing-classes="ring-2 ring-[#ffd736]" aria-labelledby="progression-heading">
        <h2 id="progression-heading" class="scroll-mt-6 text-2xl font-bold">Build and hear your 2–5–1</h2>
        <p class="mt-2 max-w-2xl text-sm leading-6 text-[#d0d9e9]">Press <strong class="text-white">Play Progression</strong> to hear Dm7 → G7 → Cmaj7 first. Then choose Cm6 on the 1 and play it again. Once you hear the difference, explore the three choices on D, two on G, and five on C.</p>
        <div class="mt-6 grid gap-3 sm:grid-cols-3">
            @foreach($degrees as $step)
                <div data-chord-card data-bass="{{ $step['midi'] }}" class="rounded-xl border border-white/15 bg-white/10 p-4">
                    <label for="degree-{{ $step['degree'] }}" class="block text-sm font-bold text-[#ffd736]">{{ $step['degree'] }} chord · {{ $step['bass'] }} in the bass</label>
                    <select id="degree-{{ $step['degree'] }}" class="mt-3 w-full rounded-lg border border-white/25 bg-[#242e42] px-3 py-2.5 text-sm font-semibold text-white" aria-describedby="notes-{{ $step['degree'] }}">
                        @foreach($step['chords'] as [$symbol, $quality, $notes, $midi])
                            <option data-midi="{{ implode(',', $midi) }}" data-notes="{{ $notes }}">{{ $symbol }} · {{ $quality }}</option>
                        @endforeach
                    </select>
                    <p id="notes-{{ $step['degree'] }}" class="mt-3 text-sm text-[#d0d9e9]">Right hand <strong data-chord-notes class="ml-1 text-white">{{ $step['chords'][0][2] }}</strong></p>
                </div>
            @endforeach
        </div>
        <div class="mt-5 flex flex-wrap items-center gap-3">
            <button type="button" hidden data-play class="min-h-12 rounded-lg bg-[#ffd736] px-5 py-3 text-sm font-bold text-[#151b29] hover:bg-[#ffe273]">
                Play Progression
            </button>
            <button type="button" hidden data-reset class="min-h-12 rounded-lg border border-white/25 px-4 text-sm font-semibold hover:bg-white/10">Reset to Standard</button>
            <p data-audio-status role="status" class="text-sm text-[#d0d9e9]"></p>
        </div>
    </div>
</section>

<section class="border-y border-black/10 bg-white">
    <div class="mx-auto max-w-7xl px-5 py-10 sm:px-8 sm:py-14">
        <h2 class="text-2xl font-bold sm:text-3xl">Change one chord and listen again</h2>
        <ol class="mt-5 grid gap-4 md:grid-cols-3">
            <li class="rounded-xl border border-black/10 p-5">
                <h3 class="font-bold">Try D7 on the 2</h3>
                <p class="mt-2 text-sm leading-6 text-[#4f5667]">Keep G7 and Cmaj7. D7 changes F to F♯, which pulls toward G.</p>
            </li>
            <li class="rounded-xl border border-black/10 p-5">
                <h3 class="font-bold">Try Gm7♭5 on the 5</h3>
                <p class="mt-2 text-sm leading-6 text-[#4f5667]">Start from the standard progression again. Hear how B♭ and D♭ change the sound just before you reach Cmaj7.</p>
            </li>
            <li class="rounded-xl border border-black/10 p-5">
                <h3 class="font-bold">Land on Cm6</h3>
                <p class="mt-2 text-sm leading-6 text-[#4f5667]">Keep Dm7 and G7. Listen for E♭ in the final chord and notice how the same bass line takes you to a minor sound.</p>
            </li>
        </ol>
        <p class="mt-5 max-w-3xl text-sm leading-6 text-[#4f5667]">When you find a combination you like, try it as a turnaround in a song. Listen to the melody over the chord you changed to see whether that sound fits.</p>
    </div>
</section>

@include('guides.membership-cta', [
    'heading' => 'Know which passing chord to play in your next song.',
    'description' => 'Work through guided 2–5–1 practice, use piano exercises to move smoothly between chord shapes, and follow gospel song breakdowns to hear where each sound fits. Then sit down at the piano and try the same ideas in songs you already play.',
])
@endsection

@section('scripts')
    <script type="module" src="{{ asset('guide-assets/passing-chord-progression/player.js') }}"></script>
@endsection
