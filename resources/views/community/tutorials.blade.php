@extends('layouts.community')

@section('content')

<div 
  x-data="{ activeVideo: null, activeType: 'vimeo' }"
  class="min-h-screen bg-gray-50 dark:bg-gray-900 pb-12"
>
  
  {{-- Header --}}
  <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-6 mb-6">
    <div class="max-w-7xl mx-auto">
      <h1 class="text-3xl font-black text-gray-900 dark:text-white leading-tight">Tutorial</h1>
      <p class="text-sm text-gray-500 dark:text-gray-400 mt-1.5">Watch and learn from step-by-step video tutorials.</p>
    </div>
  </div>

  {{-- Main Content --}}
  <div class="max-w-7xl mx-auto px-6">
    
    {{-- Tutorials Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      
      @forelse($tutorials as $t)
        @php
          $thumbnail = $t->thumbnail ? asset($t->thumbnail) : ($t->thumbnail_url ?? asset('images/featured1.jpeg'));
          $avatar = $t->author_avatar ? asset($t->author_avatar) : '/images/featured1.jpeg';
        @endphp
        
        <div class="group bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition duration-300 overflow-hidden flex flex-col justify-between">
          
          {{-- Thumbnail Area --}}
          <div 
            @click="activeVideo = '{{ $t->video_url }}'; activeType = '{{ $t->video_type }}'"
            class="relative aspect-video bg-black cursor-pointer overflow-hidden group"
          >
            <img 
              src="{{ $thumbnail }}" 
              alt="{{ $t->title }}" 
              class="w-full h-full object-cover group-hover:scale-105 transition duration-500 opacity-90 group-hover:opacity-100"
            >
            
            {{-- Play Icon Overlay --}}
            <div class="absolute inset-0 flex items-center justify-center bg-black/30 group-hover:bg-black/45 transition-colors duration-300">
              <div class="w-14 h-14 rounded-full bg-white/90 group-hover:bg-indigo-600 flex items-center justify-center shadow-lg transition duration-300 transform group-hover:scale-110">
                <svg class="w-5 h-5 text-gray-900 group-hover:text-white fill-current ml-0.5" viewBox="0 0 24 24">
                  <path d="M8 5v14l11-7z"/>
                </svg>
              </div>
            </div>
            
            {{-- Duration Badge --}}
            @if($t->duration)
              <span class="absolute bottom-3 right-3 bg-black/80 text-white text-[10px] font-bold px-2 py-0.5 rounded tracking-wide shadow-sm">
                {{ $t->duration }}
              </span>
            @endif
          </div>

          {{-- Footer --}}
          <div class="p-5">
            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-3 line-clamp-2">
              {{ $t->title }}
            </h3>
            <button
              @click="activeVideo = '{{ $t->video_url }}'; activeType = '{{ $t->video_type }}'"
              class="w-full flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold py-2.5 rounded-xl transition"
            >
              <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                <path d="M8 5v14l11-7z"/>
              </svg>
              Watch Now
            </button>
          </div>

        </div>
      @empty
        <div class="col-span-full py-16 text-center">
          <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
            <i class="fa fa-video text-2xl"></i>
          </div>
          <h3 class="text-lg font-bold text-gray-800 dark:text-white">No Tutorials Available</h3>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Check back later or explore other sections in the space.</p>
        </div>
      @endforelse

    </div>
  </div>

  {{-- ===== Dynamic Video Player Modal ===== --}}
  <div 
    x-show="activeVideo" 
    x-transition 
    x-cloak 
    class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/85"
    @keydown.escape.window="activeVideo = null"
  >
    <div class="fixed inset-0" @click="activeVideo = null"></div>
    
    <div class="relative w-full max-w-4xl bg-black rounded-2xl overflow-hidden shadow-2xl border border-gray-800 transform transition-all z-10">
      
      <button 
        @click="activeVideo = null" 
        class="absolute top-4 right-4 z-50 w-10 h-10 flex items-center justify-center rounded-full bg-black/60 hover:bg-black text-white hover:text-red-500 transition text-2xl font-bold"
        aria-label="Close Player"
      >
        &times;
      </button>

      <div class="aspect-video w-full bg-black">
        <!-- Vimeo Player -->
        <template x-if="activeVideo && activeType === 'vimeo'">
          <iframe
            :src="'https://player.vimeo.com/video/' + activeVideo + '?autoplay=1'"
            class="w-full h-full border-0"
            allow="autoplay; fullscreen; picture-in-picture"
            allowfullscreen
          ></iframe>
        </template>
        
        <!-- YouTube Player -->
        <template x-if="activeVideo && activeType === 'youtube'">
          <iframe
            :src="'https://www.youtube.com/embed/' + activeVideo + '?autoplay=1'"
            class="w-full h-full border-0"
            allow="autoplay; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen
          ></iframe>
        </template>
        
        <!-- Google Drive Player -->
        <template x-if="activeVideo && activeType === 'google'">
          <iframe
            :src="'https://drive.google.com/file/d/' + activeVideo + '/preview?autoplay=1'"
            class="w-full h-full border-0"
            allow="autoplay"
            allowfullscreen
          ></iframe>
        </template>
        
        <!-- Local / Direct File Player -->
        <template x-if="activeVideo && activeType === 'local'">
          <video :src="activeVideo" class="w-full h-full" controls autoplay></video>
        </template>
      </div>

    </div>
  </div>

</div>

@endsection
