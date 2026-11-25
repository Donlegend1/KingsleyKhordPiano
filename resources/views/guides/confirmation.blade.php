@extends('guides.layout')

@section('title', 'Check your email')
@section('description', 'Your Kingsley Khord Piano guide is on its way.')

@section('guide')
<section class="mx-auto grid max-w-6xl gap-6 px-5 py-10 sm:px-8 sm:py-16 lg:grid-cols-[0.9fr_1.1fr]">
    <div class="rounded-2xl border border-black/10 bg-white p-6 sm:p-8">
        <span class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-emerald-100 font-bold text-emerald-800" aria-hidden="true">✓</span>
        <h1 class="mt-4 text-3xl font-extrabold tracking-tight sm:text-4xl">Check your email.</h1>
        <p class="mt-3 leading-7 text-[#4f5667]">We sent a link to your <strong>{{ $guide['pdf_title'] ?? $guide['title'] }}</strong> PDF. Open the link in your inbox to read or save the guide.</p>
        <p class="mt-3 text-sm leading-6 text-[#4f5667]">If it doesn’t appear, check your spam or promotions folder.</p>
        <a href="{{ route('guides.show', $slug) }}#guide-pdf" class="mt-5 inline-block text-sm font-semibold text-[#1447A6] underline underline-offset-4">Back to the guide</a>
    </div>

    <div class="rounded-2xl bg-[#151b29] p-6 text-white sm:p-8">
        <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">Turn the voicings you heard into chords you can use.</h2>
        <p class="mt-3 leading-7 text-[#d0d9e9]">The chart gives you the shapes. Inside Kingsley Khord Piano, you’ll learn which voicings fit a progression and practise using them in the songs you play.</p>
        <ul class="mt-5 space-y-3 text-sm leading-6 text-[#d0d9e9]">
            <li class="flex gap-2"><span class="font-bold text-[#ffd736]" aria-hidden="true">✓</span><span><strong class="text-white">Guided practice</strong> to work through chord shapes and progressions at the piano.</span></li>
            <li class="flex gap-2"><span class="font-bold text-[#ffd736]" aria-hidden="true">✓</span><span><strong class="text-white">Piano exercises and MIDI practice</strong> to work on tricky passages at your own pace.</span></li>
            <li class="flex gap-2"><span class="font-bold text-[#ffd736]" aria-hidden="true">✓</span><span><strong class="text-white">Gospel song breakdowns</strong> that show where to use the voicings in real music.</span></li>
        </ul>
        <a href="https://kingsleykhordpiano.com/plans" class="mt-6 inline-flex items-center justify-center gap-2.5 rounded-lg bg-[#ffd736] px-5 py-3 text-sm font-bold text-[#161a22] transition hover:bg-[#ffe273] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4">
            Explore Membership <span aria-hidden="true">→</span>
        </a>
    </div>
</section>
@endsection
