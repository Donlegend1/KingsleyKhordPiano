@extends('layouts.member')

@section('content')

<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-4 px-4 border-b border-gray-150 dark:border-gray-800">
  <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-4">
    <!-- Breadcrumb -->
    <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400 w-full sm:w-auto">
      <a href="/home" class="hover:text-blue-600 transition">Dashboard</a>
      <span>/</span>
      <span class="text-gray-900 dark:text-white font-semibold">Extra Courses</span>
    </div>
    
    <!-- Search Bar -->
    <div class="w-full sm:w-auto">
      <form method="GET" action="{{ route('extra.courses') }}" class="flex">
        <input type="hidden" name="tab" value="{{ request('tab', 'beginner') }}">
        <div class="relative w-full sm:w-72">
          <input 
            type="text" 
            name="search" 
            value="{{ request('search') }}" 
            class="w-full border border-gray-300 dark:border-gray-750 dark:bg-gray-800 rounded-full pl-4 pr-10 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
            placeholder="Search courses..."
          >
          <button 
            type="submit" 
            class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-white p-1"
          >
            <i class="fa fa-search"></i>
          </button>
        </div> 
      </form>
    </div>
  </div>
</section>

<section class="bg-gray-50 dark:bg-gray-950 py-8 px-4 sm:px-6 lg:px-8">
  <div class="w-full max-w-7xl mx-auto" x-data="{ activeTab: '{{ request('tab', 'beginner') }}' }">

    <!-- Mobile Dropdown Navigation -->
    <div class="block lg:hidden mb-6">
      <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-4 shadow-sm">
        <label for="extracourses-mobile-filter" class="mb-2 block text-xs font-bold uppercase tracking-wider text-gray-500">
          Select Difficulty Level
        </label>
        <div class="relative">
          <select
            id="extracourses-mobile-filter"
            x-model="activeTab"
            onChange="window.location.href = '?tab=' + this.value + '{{ request('search') ? '&search=' . urlencode(request('search')) : '' }}'"
            class="w-full appearance-none rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 py-3 pl-4 pr-12 text-base font-semibold text-gray-800 dark:text-gray-200 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
          >
            <option value="beginner">Beginner</option>
            <option value="intermediate">Intermediate</option>
            <option value="advanced">Advanced</option>
          </select>
          <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 text-gray-500">
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Desktop Tab Navigation -->
    <div class="hidden lg:flex justify-center border-b border-gray-200 dark:border-gray-800 mb-8 gap-4">
      <a href="?tab=beginner{{ request('search') ? '&search=' . urlencode(request('search')) : '' }}" 
        class="py-3 px-6 font-bold text-gray-500 hover:text-gray-900 dark:hover:text-white border-b-2 transition-all duration-200"
        :class="activeTab === 'beginner' ? 'border-yellow-400 text-gray-900 dark:text-white' : 'border-transparent'">
        Beginner
      </a>
      <a href="?tab=intermediate{{ request('search') ? '&search=' . urlencode(request('search')) : '' }}" 
        class="py-3 px-6 font-bold text-gray-500 hover:text-gray-900 dark:hover:text-white border-b-2 transition-all duration-200"
        :class="activeTab === 'intermediate' ? 'border-yellow-400 text-gray-900 dark:text-white' : 'border-transparent'">
        Intermediate
      </a>
      <a href="?tab=advanced{{ request('search') ? '&search=' . urlencode(request('search')) : '' }}" 
        class="py-3 px-6 font-bold text-gray-500 hover:text-gray-900 dark:hover:text-white border-b-2 transition-all duration-200"
        :class="activeTab === 'advanced' ? 'border-yellow-400 text-gray-900 dark:text-white' : 'border-transparent'">
        Advanced
      </a>
    </div>

    <!-- Content List -->
    <!-- Content List -->
    @php
      $beginnerGroups = $beginnerCategories->filter(fn($cat) => $cat->courses->isNotEmpty());
      $intermediateGroups = $intermediateCategories->filter(fn($cat) => $cat->courses->isNotEmpty());
      $advancedGroups = $advancedCategories->filter(fn($cat) => $cat->courses->isNotEmpty());
    @endphp

    <div class="mt-8">
      <!-- Beginner tab view -->
      <div x-show="activeTab === 'beginner'">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
          @forelse($beginnerGroups as $cat)
            @php
              $firstCourse = $cat->courses->first();
              $lessonCount = $cat->courses->count();
            @endphp
            <div class="bg-white dark:bg-gray-900 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-gray-150 dark:border-gray-805 flex flex-col justify-between group">
              <a href="/member/lesson/{{ $firstCourse->id }}" class="block relative overflow-hidden aspect-video bg-gray-100 dark:bg-gray-850">
                @if($firstCourse->thumbnail_url)
                  <img src="{{ $firstCourse->thumbnail_url }}" alt="{{ $cat->category }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                @else
                  <div class="w-full h-full flex items-center justify-center text-gray-400 dark:text-gray-650 bg-gray-50 dark:bg-gray-800">
                    <i class="fa fa-music text-4xl"></i>
                  </div>
                @endif
                
                <!-- Play Button Hover Overlay -->
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center">
                  <div class="w-14 h-14 bg-[#0FA9A0] text-white rounded-full flex items-center justify-center shadow-lg transform scale-90 group-hover:scale-100 transition duration-300">
                    <i class="fa fa-play text-xl ml-1"></i>
                  </div>
                </div>
                
                <!-- Lessons count badge -->
                <div class="absolute bottom-3 right-3 bg-black/75 backdrop-blur-sm text-white text-xs font-bold px-2.5 py-1 rounded-md">
                  {{ $lessonCount }} {{ Str::plural('Lesson', $lessonCount) }}
                </div>
              </a>
              
              <div class="p-6 flex-grow flex flex-col justify-between">
                <div>
                  <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 leading-snug group-hover:text-[#0FA9A0] transition-colors">
                    {{ $cat->category }}
                  </h3>
                  <p class="text-sm text-gray-500 dark:text-gray-405 line-clamp-2 leading-relaxed mb-5">
                    {{ $firstCourse->description ?? 'Explore this categorized course and learn chords, song patterns, and advanced techniques.' }}
                  </p>
                </div>
                
                <a href="/member/lesson/{{ $firstCourse->id }}" class="flex items-center justify-center gap-2 w-full py-3 bg-gray-55 dark:bg-gray-800 hover:bg-[#0FA9A0] hover:text-white text-gray-700 dark:text-gray-300 rounded-xl text-sm font-semibold transition-all duration-300">
                  <i class="fa fa-play text-[10px] ml-0.5"></i> View Course
                </a>
              </div>
            </div>
          @empty
            <div class="col-span-full text-center text-gray-500 py-16 bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-850">
              <i class="fa fa-graduation-cap text-4xl text-gray-300 mb-3 block"></i>
              No courses found for Beginner level.
            </div>
          @endforelse
        </div>
      </div>

      <!-- Intermediate tab view -->
      <div x-show="activeTab === 'intermediate'">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
          @forelse($intermediateGroups as $cat)
            @php
              $firstCourse = $cat->courses->first();
              $lessonCount = $cat->courses->count();
            @endphp
            <div class="bg-white dark:bg-gray-900 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-gray-150 dark:border-gray-855 flex flex-col justify-between group">
              <a href="/member/lesson/{{ $firstCourse->id }}" class="block relative overflow-hidden aspect-video bg-gray-100 dark:bg-gray-850">
                @if($firstCourse->thumbnail_url)
                  <img src="{{ $firstCourse->thumbnail_url }}" alt="{{ $cat->category }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                @else
                  <div class="w-full h-full flex items-center justify-center text-gray-400 dark:text-gray-655 bg-gray-50 dark:bg-gray-800">
                    <i class="fa fa-music text-4xl"></i>
                  </div>
                @endif
                
                <!-- Play Button Hover Overlay -->
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center">
                  <div class="w-14 h-14 bg-[#0FA9A0] text-white rounded-full flex items-center justify-center shadow-lg transform scale-90 group-hover:scale-100 transition duration-300">
                    <i class="fa fa-play text-xl ml-1"></i>
                  </div>
                </div>
                
                <!-- Lessons count badge -->
                <div class="absolute bottom-3 right-3 bg-black/75 backdrop-blur-sm text-white text-xs font-bold px-2.5 py-1 rounded-md">
                  {{ $lessonCount }} {{ Str::plural('Lesson', $lessonCount) }}
                </div>
              </a>
              
              <div class="p-6 flex-grow flex flex-col justify-between">
                <div>
                  <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 leading-snug group-hover:text-[#0FA9A0] transition-colors">
                    {{ $cat->category }}
                  </h3>
                  <p class="text-sm text-gray-500 dark:text-gray-405 line-clamp-2 leading-relaxed mb-5">
                    {{ $firstCourse->description ?? 'Explore this categorized course and learn chords, song patterns, and advanced techniques.' }}
                  </p>
                </div>
                
                <a href="/member/lesson/{{ $firstCourse->id }}" class="flex items-center justify-center gap-2 w-full py-3 bg-gray-55 dark:bg-gray-800 hover:bg-[#0FA9A0] hover:text-white text-gray-700 dark:text-gray-300 rounded-xl text-sm font-semibold transition-all duration-300">
                  <i class="fa fa-play text-[10px] ml-0.5"></i> View Course
                </a>
              </div>
            </div>
          @empty
            <div class="col-span-full text-center text-gray-500 py-16 bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-850">
              <i class="fa fa-graduation-cap text-4xl text-gray-300 mb-3 block"></i>
              No courses found for Intermediate level.
            </div>
          @endforelse
        </div>
      </div>

      <!-- Advanced tab view -->
      <div x-show="activeTab === 'advanced'">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
          @forelse($advancedGroups as $cat)
            @php
              $firstCourse = $cat->courses->first();
              $lessonCount = $cat->courses->count();
            @endphp
            <div class="bg-white dark:bg-gray-900 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-gray-150 dark:border-gray-855 flex flex-col justify-between group">
              <a href="/member/lesson/{{ $firstCourse->id }}" class="block relative overflow-hidden aspect-video bg-gray-100 dark:bg-gray-850">
                @if($firstCourse->thumbnail_url)
                  <img src="{{ $firstCourse->thumbnail_url }}" alt="{{ $cat->category }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                @else
                  <div class="w-full h-full flex items-center justify-center text-gray-400 dark:text-gray-655 bg-gray-50 dark:bg-gray-800">
                    <i class="fa fa-music text-4xl"></i>
                  </div>
                @endif
                
                <!-- Play Button Hover Overlay -->
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center">
                  <div class="w-14 h-14 bg-[#0FA9A0] text-white rounded-full flex items-center justify-center shadow-lg transform scale-90 group-hover:scale-100 transition duration-300">
                    <i class="fa fa-play text-xl ml-1"></i>
                  </div>
                </div>
                
                <!-- Lessons count badge -->
                <div class="absolute bottom-3 right-3 bg-black/75 backdrop-blur-sm text-white text-xs font-bold px-2.5 py-1 rounded-md">
                  {{ $lessonCount }} {{ Str::plural('Lesson', $lessonCount) }}
                </div>
              </a>
              
              <div class="p-6 flex-grow flex flex-col justify-between">
                <div>
                  <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 leading-snug group-hover:text-[#0FA9A0] transition-colors">
                    {{ $cat->category }}
                  </h3>
                  <p class="text-sm text-gray-500 dark:text-gray-405 line-clamp-2 leading-relaxed mb-5">
                    {{ $firstCourse->description ?? 'Explore this categorized course and learn chords, song patterns, and advanced techniques.' }}
                  </p>
                </div>
                
                <a href="/member/lesson/{{ $firstCourse->id }}" class="flex items-center justify-center gap-2 w-full py-3 bg-gray-55 dark:bg-gray-800 hover:bg-[#0FA9A0] hover:text-white text-gray-700 dark:text-gray-300 rounded-xl text-sm font-semibold transition-all duration-300">
                  <i class="fa fa-play text-[10px] ml-0.5"></i> View Course
                </a>
              </div>
            </div>
          @empty
            <div class="col-span-full text-center text-gray-500 py-16 bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-850">
              <i class="fa fa-graduation-cap text-4xl text-gray-300 mb-3 block"></i>
              No courses found for Advanced level.
            </div>
          @endforelse
        </div>
      </div>
    </div>

  </div>
</section>

@endsection
