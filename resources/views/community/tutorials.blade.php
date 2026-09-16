@extends('layouts.hub')

@section('title', 'Tutorials')

@section('breadcrumbs')
    @include('community.partials.breadcrumbs', ['items' => [['label' => 'Tutorials']]])
@endsection

@section('content')

<div class="pb-12">

  {{-- Main Content --}}
  <div class="max-w-7xl mx-auto px-6 pt-6">

    {{-- Page header --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm p-6 mb-6">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tutorials</h1>
      <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Get the most out of the platform</p>
    </div>

    {{-- Tutorials Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

      @forelse($tutorials as $t)
        @php
          $thumbnail = $t->thumbnail ? asset($t->thumbnail) : ($t->thumbnail_url ?? asset('images/featured1.jpeg'));
        @endphp

        <div class="group flex flex-col bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-all duration-300">

          {{-- Thumbnail Area --}}
          <a
            href="{{ route('community.tutorials.show', $t->id) }}"
            class="relative aspect-video bg-gray-900 block overflow-hidden"
          >
            <img
              src="{{ $thumbnail }}"
              alt="{{ $t->title }}"
              class="w-full h-full object-cover transition duration-500 group-hover:scale-105"
            >
          </a>

          {{-- Footer --}}
          <div class="flex flex-col flex-1 gap-4 p-5 bg-gray-50 dark:bg-gray-900/40">
            <h3 class="text-base font-bold text-gray-900 dark:text-white leading-snug line-clamp-2">
              {{ $t->title }}
            </h3>

            <a
              href="{{ route('community.tutorials.show', $t->id) }}"
              class="inline-flex items-center justify-center px-5 py-2.5 rounded-md text-white text-xs font-bold uppercase tracking-wide transition-colors duration-200 self-start"
              style="background-color: #C85A5A;"
              onmouseover="this.style.backgroundColor='#B84D4D'"
              onmouseout="this.style.backgroundColor='#C85A5A'"
            >
              Watch Now
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
