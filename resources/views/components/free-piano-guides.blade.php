@php $guides = array_slice(config('piano-guides'), 0, 3, true); @endphp

@if($guides)
    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8" aria-labelledby="free-piano-guides">
        <div class="mb-5 flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2">
            <h2 id="free-piano-guides" class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-3xl">Free Piano Guides</h2>
            <a href="{{ route('guides.index') }}" class="text-sm font-semibold text-[#1447A6] underline-offset-4 hover:underline dark:text-blue-300">
                View All Guides <span aria-hidden="true">→</span>
            </a>
        </div>
        @include('components.piano-guide-cards', ['guides' => $guides])
    </section>
@endif
