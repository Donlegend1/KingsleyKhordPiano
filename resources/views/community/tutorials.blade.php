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
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-10">

      @forelse($tutorials as $t)
        @php
          $thumbnail = $t->thumbnail ? asset($t->thumbnail) : ($t->thumbnail_url ?? asset('images/featured1.jpeg'));
          $avatar = $t->author_avatar ? asset($t->author_avatar) : '/images/featured1.jpeg';
        @endphp

        <div class="group flex flex-col transition-transform duration-300 hover:-translate-y-1">

          {{-- Thumbnail Area --}}
          <a
            href="{{ route('community.tutorials.show', $t->id) }}"
            class="relative aspect-video rounded-2xl overflow-hidden bg-gray-100 dark:bg-white/5 ring-1 ring-black/5 dark:ring-white/10 shadow-sm group-hover:shadow-lg transition-shadow duration-300 block"
          >
            <img
              src="{{ $thumbnail }}"
              alt="{{ $t->title }}"
              class="w-full h-full object-cover transition duration-500 group-hover:scale-[1.03]"
            >

            {{-- Play Icon Overlay --}}
            <div class="absolute inset-0 flex items-center justify-center bg-black/0 group-hover:bg-black/10 transition-colors duration-300">
              <div class="w-12 h-12 rounded-full bg-white/95 flex items-center justify-center shadow-md transition duration-300 group-hover:scale-110">
                <svg class="w-4 h-4 text-gray-900 fill-current ml-0.5" viewBox="0 0 24 24">
                  <path d="M8 5v14l11-7z"/>
                </svg>
              </div>
            </div>

            {{-- Duration Badge --}}
            @if($t->duration)
              <span class="absolute bottom-3 right-3 bg-black/60 backdrop-blur-sm text-white text-[10px] font-medium px-2 py-0.5 rounded-md">
                {{ $t->duration }}
              </span>
            @endif
          </a>

          {{-- Footer --}}
          <div class="pt-4">
            <h3 class="text-[15px] font-semibold text-gray-900 dark:text-white mb-2 leading-snug tracking-tight text-center line-clamp-2">
              {{ $t->title }}
            </h3>
            <a
              href="{{ route('community.tutorials.show', $t->id) }}"
              class="inline-flex items-center justify-center gap-1.5 w-full px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold transition-colors"
            >
              Watch Now
              <svg class="w-3.5 h-3.5 fill-current transition-transform duration-200 group-hover:translate-x-0.5" viewBox="0 0 24 24">
                <path d="M8 5v14l11-7z"/>
              </svg>
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
