@extends('guides.layout')

@section('title', $guide['title'])
@section('description', $guide['description'])

@section('guide')
<section class="mx-auto grid max-w-7xl gap-8 px-5 py-10 sm:px-8 sm:py-16 lg:grid-cols-2 lg:items-center lg:gap-12">
    <div>
        <p class="text-xs font-bold uppercase tracking-widest text-[#4f5667]">Free Gospel Piano Guide</p>
        <h1 class="mt-4 max-w-2xl text-[clamp(2.6rem,5.5vw,4.6rem)] font-extrabold leading-[1.04] tracking-[-0.055em]">Give the next chord a little build-up</h1>
        <p class="mt-5 max-w-xl text-lg leading-7 text-[#4f5667]">Think of a chord change in a song you already play. You can add a little movement just before that next chord arrives. A <strong>chromatic passing chord</strong> does this by moving a chord shape one piano key at a time.</p>
        <a href="#chord-example" class="mt-6 inline-flex min-h-12 items-center rounded-lg bg-[#ffd736] px-5 py-3 text-sm font-bold hover:bg-[#ffe273]">See how it works <span class="ml-2" aria-hidden="true">↓</span></a>
    </div>
    <figure class="rounded-2xl bg-[#151b29] p-5 text-white shadow-sm sm:p-7">
        <figcaption class="text-xl font-bold sm:text-2xl">What is a half step?</figcaption>
        <p class="mt-2 text-sm leading-6 text-[#d0d9e9]">It’s the move to the very next piano key, black or white. Here, D moves to the black key E♭, then to the white key E.</p>
        <div class="relative mx-auto mt-5 flex h-28 max-w-xs overflow-hidden rounded-lg border-2 border-[#0b1020]" aria-hidden="true">
            <span class="flex flex-1 items-end justify-center border-r border-[#afb8c9] bg-[#ffd736] pb-2 font-bold text-[#151b29]">D</span>
            <span class="flex flex-1 items-end justify-center border-r border-[#afb8c9] bg-[#ffd736] pb-2 font-bold text-[#151b29]">E</span>
            <span class="flex flex-1 items-end justify-center bg-white pb-2 font-bold text-[#151b29]">F</span>
            <span class="absolute left-1/3 top-0 flex h-16 w-1/5 -translate-x-1/2 items-end justify-center rounded-b-md border border-white/30 bg-[#27354d] pb-2 text-sm font-bold text-[#ffd736]">E♭</span>
        </div>
        <p class="mt-3 text-center text-sm font-bold text-[#ffd736]">D <span aria-hidden="true">→</span> E♭ <span aria-hidden="true">→</span> E</p>
    </figure>
</section>

<section id="chord-example" class="scroll-mt-6 border-t border-black/10 bg-white">
    <div class="mx-auto grid max-w-7xl gap-6 px-5 py-10 sm:px-8 sm:py-14 lg:grid-cols-[0.8fr_1.2fr] lg:items-center lg:gap-10">
        <div>
            <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">Now move a whole shape</h2>
            <p class="mt-3 leading-7 text-[#4f5667]">Pick where you want to land first. In this example, the right hand lands on <strong>A major</strong> while the left hand plays <strong>C7</strong>.</p>
            <p class="mt-3 leading-7 text-[#4f5667]">To build up to that landing, start the right-hand shape two keys lower. Move each finger up one key at a time. The left hand waits and joins on the beat where the chord belongs.</p>
        </div>
        <figure class="rounded-2xl bg-[#151b29] p-5 text-white shadow-sm sm:p-7">
            <figcaption class="text-2xl font-bold">From G major to A major over C7</figcaption>
            <ol class="mt-5 grid grid-cols-3 gap-2">
                @foreach([
                    ['Two half steps below', 'G major', 'D · G · B', 'Rests'],
                    ['One half step below', 'A♭ major', 'E♭ · A♭ · C', 'Rests'],
                    ['Target', 'A major', 'E · A · C♯', 'C · E · G · B♭'],
                ] as [$position, $triad, $right, $left])
                    <li class="min-w-0 rounded-xl border {{ $loop->last ? 'border-[#ffd736] bg-white/10' : 'border-white/15 bg-white/5' }} p-2 sm:p-3">
                        <span class="block min-h-8 text-xs leading-4 {{ $loop->last ? 'font-bold text-[#ffd736]' : 'text-[#d0d9e9]' }}">{{ $position }}</span>
                        <strong class="mt-1 block text-sm">{{ $triad }}</strong>
                        <p class="mt-2 text-xs leading-5 text-[#ffd736]">Right hand <strong class="block">{{ $right }}</strong></p>
                        <p class="mt-2 text-xs leading-5 text-[#a8d8ff]">Left hand <strong class="block">{{ $left }}</strong></p>
                    </li>
                @endforeach
            </ol>
            <p class="mt-4 text-sm leading-6 text-[#d0d9e9]">Follow D → E♭ → E at the bottom of each right-hand shape. The other fingers move by the same distance, so the order of the notes stays the same.</p>
        </figure>
    </div>
</section>

<section class="border-y border-black/10">
    <div class="mx-auto grid max-w-7xl gap-8 px-5 py-10 sm:px-8 sm:py-14 lg:grid-cols-2">
        <div>
            <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">Where can you use it?</h2>
            <p class="mt-3 leading-7 text-[#4f5667]">You can use chromatic movement with suitable shapes for <strong>major 7ths, minor 7ths, minor 7♭5 chords, and dominant 7ths</strong>. Two ways to start:</p>
            <dl class="mt-5 space-y-4 text-sm leading-6 text-[#4f5667]">
                <div>
                    <dt class="font-bold text-[#151a22]">Lead with the right hand</dt>
                    <dd>Use an approved target with a three-note right-hand chord in first or second inversion. Keep the notes in that same order as you move from above or below. Bring in the left hand on the target.</dd>
                </div>
                <div>
                    <dt class="font-bold text-[#151a22]">Move both hands together</dt>
                    <dd>In <strong>Am7 → A♭m7 → Gm7</strong>, every note in both hands moves down one key at each step. Keep the spacing between notes and move any doubled notes too.</dd>
                </div>
            </dl>
            <p class="mt-4 text-sm leading-6 text-[#4f5667]">Try it where the melody leaves space. One passing chord is plenty if you have room for just one. Keep the original landing beat.</p>
        </div>
        <div class="scroll-mt-6 rounded-2xl border border-black/10 bg-[#f9f7f0] p-5 sm:p-7" id="guide-pdf">
            <h2 class="text-2xl font-bold">Get the dominant seventh chart</h2>
            <p id="guide-pdf-details" class="mt-2 text-sm leading-6 text-[#4f5667]">Pick a dominant 7th chord you already play and find a right-hand shape to pair with it. The free PDF has one chart per key, so you can keep it beside the piano.</p>
            <form action="{{ route('guides.pdf.request', $guideSlug) }}" method="POST" class="mt-4">
                @csrf
                <label for="guide-email" class="sr-only">Email address</label>
                <input id="guide-email" name="email" type="email" autocomplete="email" inputmode="email" maxlength="254" required placeholder="Enter your email" value="{{ old('email') }}" aria-describedby="guide-pdf-details" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm">
                <button type="submit" class="mt-2 w-full rounded-xl bg-[#ffd736] px-4 py-3 text-sm font-bold hover:bg-[#ffe273]">Email Me the Free PDF</button>
                @error('email')
                    <p role="alert" class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>
                @enderror
            </form>
        </div>
    </div>
</section>

@include('guides.membership-cta', [
    'heading' => 'Try this on a gospel song you already play.',
    'description' => 'Start with guided practice and piano exercises to get the movement under your fingers. Then follow gospel song breakdowns to hear where a passing chord adds something, which shape leaves room for the melody, and how to land on time.',
])
@endsection

