@extends('guides.layout')

@section('title', 'Free Piano Guides')
@section('description', 'Browse free piano guides from Kingsley Khord Piano and try new chords and techniques at the keyboard.')

@section('guide')
<section class="mx-auto max-w-7xl px-5 py-10 sm:px-8 sm:py-14" aria-labelledby="guide-library">
    <h1 id="guide-library" class="text-4xl font-extrabold tracking-tight sm:text-5xl">Free Piano Guides</h1>
    <p class="mt-3 max-w-xl text-base leading-7 text-gray-600">Pick a topic, try an example at the piano, and take the practice materials to the keyboard.</p>

    <div class="mt-8">
        @if($guides->isNotEmpty())
            <h2 class="sr-only">Available guides</h2>
            @include('components.piano-guide-cards', ['guides' => $guides])
        @else
            <p class="text-sm text-gray-600">No guides are available yet.</p>
        @endif
    </div>

    @if($guides->hasPages())
        <div class="mt-10">
            {{ $guides->links('pagination.community') }}
        </div>
    @endif
</section>
@endsection
