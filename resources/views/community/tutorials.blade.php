@extends('layouts.community')

@section('page-title')
    <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white leading-tight truncate">Tutorial</h1>
@endsection

@section('content')

<div
  class="min-h-screen bg-gray-50 dark:bg-black pb-12"
>

  {{-- Main Content --}}
  <div class="max-w-7xl mx-auto px-6 pt-6">
    
    {{-- Tutorials Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

      @forelse($tutorials as $t)
        @php
          $thumbnail = $t->thumbnail ? asset($t->thumbnail) : ($t->thumbnail_url ?? asset('images/featured1.jpeg'));
        @endphp

        <div class="group flex flex-col bg-white dark:bg-[#161617] border border-gray-100/80 dark:border-white/10 rounded-2xl overflow-hidden shadow-[0_1px_3px_rgba(0,0,0,0.06)] hover:shadow-xl hover:-translate-y-1 transition-all duration-300">

          {{-- Thumbnail Area --}}
          <a
            href="{{ route('community.tutorials.show', $t->id) }}"
            class="relative aspect-video bg-gray-100 dark:bg-white/5 block overflow-hidden"
          >
            <img
              src="{{ $thumbnail }}"
              alt="{{ $t->title }}"
              class="w-full h-full object-cover transition duration-500 group-hover:scale-105"
            >
            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-black/0 to-black/0 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

            @if($t->duration)
              <span class="absolute bottom-3 right-3 bg-black/70 backdrop-blur-sm text-white text-[10px] font-semibold px-2 py-1 rounded-md tracking-wide">
                {{ $t->duration }}
              </span>
            @endif
          </a>

          {{-- Footer --}}
          <div class="flex flex-col flex-1 items-center p-5">
            <h3 class="text-[15px] font-bold text-gray-900 dark:text-white mb-4 leading-snug tracking-tight text-center line-clamp-2">
              {{ $t->title }}
            </h3>

            <a
              href="{{ route('community.tutorials.show', $t->id) }}"
              class="mt-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-gray-900 dark:bg-indigo-600 hover:bg-indigo-600 dark:hover:bg-indigo-500 text-white text-sm font-semibold transition-colors duration-200"
            >
              Watch Now
              <span class="w-6 h-6 rounded-full bg-white/15 flex items-center justify-center transition-transform duration-200 group-hover:translate-x-0.5">
                <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24">
                  <path d="M8 5v14l11-7z"/>
                </svg>
              </span>
            </a>
          </div>

        </div>
      @empty
        <div class="col-span-full py-16 text-center">
          <div class="w-16 h-16 bg-gray-100 dark:bg-[#161617] rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
            <i class="fa fa-video text-2xl"></i>
          </div>
          <h3 class="text-lg font-bold text-gray-800 dark:text-white">No Tutorials Available</h3>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Check back later or explore other sections in the space.</p>
        </div>
      @endforelse

    </div>
  </div>

</div>

@endsection
