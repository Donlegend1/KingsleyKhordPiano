@extends('layouts.member')

@section('content')

<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-2 px-4">
  <div class="max-w-7xl mx-auto space-y-1">

    <!-- Breadcrumb & User -->
    <div class="flex justify-between items-center">
      <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="/home" class="hover:text-gray-700">Dashboard</a>
        <span>/</span>
        <span class="text-[#6366F1] font-medium">Quick Lesson</span>
      </div>
      <div class="flex items-center space-x-2">
          <form method="GET" action="{{ route('quick.lession') }}" class="mb-2 flex justify-end">
          <div class="relative w-full max-w-xs">
              <!-- Input Field -->
              <input 
                type="text" 
                name="search" 
                id="name" 
                value="{{ request('search') }}" 
                class="w-full border border-gray-300 rounded-full pl-4 pr-12 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                placeholder="Search..."
              >

              <!-- Search Button with Icon -->
              <button 
                type="submit" 
                class="absolute my-4 right-1 top-1/2 transform -translate-y-1/2 bg-blue-500 hover:bg-blue-600 text-white rounded-full p-2 flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
              >
                <i class="fa fa-search"></i>
              </button>
            </div> 
        </form>
        {{-- <i class="fa fa-user-circle text-xl"></i> --}}
      </div>
    </div>

    @if ($series)
    <div class="mb-4">
        <a href="{{ route('quick.lession') }}" class="text-blue-500 hover:underline flex items-center gap-2">
            <i class="fa fa-arrow-left text-sm"></i> 
            <span class="font-medium">Back to All Quick Lessons</span>
        </a>
        <h2 class="text-2xl font-bold mt-4 text-gray-800">Series: {{ $series }}</h2>
    </div>
    @endif
  </div>
</section>

<section class="bg-gray-100 py-10 px-4 sm:px-6 lg:px-8 xl:px-10">
  <div class="w-full max-w-7xl mx-auto bg-white rounded-lg shadow-lg p-6" x-data="{ activeTab: '{{ request('tab', 'all') }}' }"">

    <!-- Mobile Dropdown -->
   <div class="block lg:hidden mb-6">
      <div class="rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-50 to-white p-4 shadow-sm">
        <label for="quicklesson-mobile-filter" class="mb-2 block text-xs font-bold uppercase tracking-[0.22em] text-slate-500">
          Filter Lessons
        </label>
        <div class="relative">
          <select
            id="quicklesson-mobile-filter"
            x-model="activeTab"
            class="w-full appearance-none rounded-xl border border-slate-300 bg-white py-3.5 pl-4 pr-12 text-base font-bold text-slate-800 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
            style="-webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: none;"
          >
            <option value="all" class="font-bold">All</option>
            <option value="beginner" class="font-bold">Beginner</option>
            <option value="intermediate" class="font-bold">Intermediate</option>
            <option value="advanced" class="font-bold">Advanced</option>
          </select>
          <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 text-slate-500">
            <svg class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Desktop Tabs -->
    <div class="hidden lg:flex justify-between border-b mb-6">
      <button 
        class="py-2 px-4 text-gray-600 hover:text-blue-500 border-b-2" 
        :class="{ 'border-blue-500 text-blue-500': activeTab === 'all' }"
        @click="activeTab = 'all'">All</button>

      <a href="?tab=beginner" 
        class="py-2 px-4 text-gray-600 hover:text-blue-500 border-b-2"
        :class="{ 'border-blue-500 text-blue-500': activeTab === 'beginner' }">
        Beginner
      </a>
      <a href="?tab=intermediate" 
        class="py-2 px-4 text-gray-600 hover:text-blue-500 border-b-2"
        :class="{ 'border-blue-500 text-blue-500': activeTab === 'intermediate' }">
        Intermediate
      </a>
      <a href="?tab=advanced" 
        class="py-2 px-4 text-gray-600 hover:text-blue-500 border-b-2"
        :class="{ 'border-blue-500 text-blue-500': activeTab === 'advanced' }">
        Advanced
      </a>

    </div>

    <!-- Content Area -->
    <div class="space-y-4 mt-4">

      <!-- All Tab -->
      <div x-show="activeTab === 'all'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($all as $exercise)
          @if (!$series && $exercise->series)
            <div class="bg-white p-6 rounded-lg shadow flex flex-col items-center space-y-4 relative group">
              <a href="{{ request()->fullUrlWithQuery(['series' => $exercise->series]) }}" class="w-full relative">
                <div class="relative z-10">
                   <img src="{{ $exercise->thumbnail_url }}" alt="{{ $exercise->title }}" class="w-full h-48 object-cover rounded-md border-2 border-white shadow-sm">
                </div>
                <!-- Stack visual effect -->
                <div class="absolute top-1 left-1 w-full h-48 bg-gray-200 rounded-md border border-gray-300 -z-0 translate-x-1 translate-y-1"></div>
                <div class="absolute top-2 left-2 w-full h-48 bg-gray-100 rounded-md border border-gray-200 -z-10 translate-x-2 translate-y-2"></div>
              </a>
              <div class="text-center w-full pt-2">
                <h3 class="font-bold text-gray-800 text-lg">{{ $exercise->series }}</h3>
                <p class="text-sm text-blue-600 font-medium">{{ $exercise->item_count }} Lessons in this series</p>
              </div>
              <a href="{{ request()->fullUrlWithQuery(['series' => $exercise->series]) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-center w-full font-semibold">
                View Series
              </a>
            </div>
          @else
            <div class="bg-white p-6 rounded-lg shadow flex flex-col items-center space-y-4">
              <a href="/member/lesson/{{ $exercise->id }}">
              <img src="{{ $exercise->thumbnail_url }}" alt="{{ $exercise->title }}" class="w-full h-48 object-cover rounded-md">
              </a>
              <h3 class="font-bold text-gray-800">{{ $exercise->title }}</h3>
              <p class="text-xs text-gray-700">{{ $exercise->level }}</p>
              <a href="/member/lesson/{{ $exercise->id }}" class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition text-center w-full">
                Watch Now
              </a>
            </div>
          @endif
        @empty
          <div class="col-span-full text-center text-gray-500 py-12">
            No exercises found for this category.
          </div>
        @endforelse
        @if ($all->hasPages())
          <div class="flex justify-center py-6 col-span-full">
            {{ $all->appends(['tab' => 'all', 'all_page' => $all->currentPage()])->links('components.pagination') }}
          </div>
        @endif
      </div>

      <!-- Beginner Tab -->
      <div x-show="activeTab === 'beginner'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($beginner as $exercise)
          @if (!$series && $exercise->series)
            <div class="bg-white p-6 rounded-lg shadow flex flex-col items-center space-y-4 relative group">
              <a href="{{ request()->fullUrlWithQuery(['series' => $exercise->series]) }}" class="w-full relative">
                <div class="relative z-10">
                   <img src="{{ $exercise->thumbnail_url }}" alt="{{ $exercise->title }}" class="w-full h-48 object-cover rounded-md border-2 border-white shadow-sm">
                </div>
                <div class="absolute top-1 left-1 w-full h-48 bg-gray-200 rounded-md border border-gray-300 -z-0 translate-x-1 translate-y-1"></div>
                <div class="absolute top-2 left-2 w-full h-48 bg-gray-100 rounded-md border border-gray-200 -z-10 translate-x-2 translate-y-2"></div>
              </a>
              <div class="text-center w-full pt-2">
                <h3 class="font-bold text-gray-800 text-lg">{{ $exercise->series }}</h3>
                <p class="text-sm text-blue-600 font-medium">{{ $exercise->item_count }} Lessons in this series</p>
              </div>
              <a href="{{ request()->fullUrlWithQuery(['series' => $exercise->series]) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-center w-full font-semibold">
                View Series
              </a>
            </div>
          @else
            <div class="bg-white p-6 rounded-lg shadow flex flex-col items-center space-y-4">
              <a href="/member/lesson/{{ $exercise->id }}">
              <img src="{{ $exercise->thumbnail_url }}" alt="{{ $exercise->title }}" class="w-full h-48 object-cover rounded-md">
              </a>
              <h3 class="font-bold text-gray-800">{{ $exercise->title }}</h3>
              <p class="text-xs text-gray-700">{{ $exercise->level }}</p>
              <a href="/member/lesson/{{ $exercise->id }}" class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition text-center w-full">
                Watch Now
              </a>
            </div>
          @endif
        @empty
          <div class="col-span-full text-center text-gray-500 py-12">
            No exercises found for this category.
          </div>
        @endforelse

        @if( $beginner->hasPages())
          <div class="flex justify-center py-6 col-span-full">
            {{ $beginner->appends(['tab' => 'beginner', 'beginner_page' => $beginner->currentPage()])->links('components.pagination') }}
          </div>
        @endif
      </div>

      <!-- Intermediate Tab -->
      <div x-show="activeTab === 'intermediate'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($intermediate as $exercise)
          @if (!$series && $exercise->series)
            <div class="bg-white p-6 rounded-lg shadow flex flex-col items-center space-y-4 relative group">
              <a href="{{ request()->fullUrlWithQuery(['series' => $exercise->series]) }}" class="w-full relative">
                <div class="relative z-10">
                   <img src="{{ $exercise->thumbnail_url }}" alt="{{ $exercise->title }}" class="w-full h-48 object-cover rounded-md border-2 border-white shadow-sm">
                </div>
                <div class="absolute top-1 left-1 w-full h-48 bg-gray-200 rounded-md border border-gray-300 -z-0 translate-x-1 translate-y-1"></div>
                <div class="absolute top-2 left-2 w-full h-48 bg-gray-100 rounded-md border border-gray-200 -z-10 translate-x-2 translate-y-2"></div>
              </a>
              <div class="text-center w-full pt-2">
                <h3 class="font-bold text-gray-800 text-lg">{{ $exercise->series }}</h3>
                <p class="text-sm text-blue-600 font-medium">{{ $exercise->item_count }} Lessons in this series</p>
              </div>
              <a href="{{ request()->fullUrlWithQuery(['series' => $exercise->series]) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-center w-full font-semibold">
                View Series
              </a>
            </div>
          @else
            <div class="bg-white p-6 rounded-lg shadow flex flex-col items-center space-y-4">
              <a href="/member/lesson/{{ $exercise->id }}">
                <img src="{{ $exercise->thumbnail_url }}" alt="{{ $exercise->title }}" class="w-full h-48 object-cover rounded-md">
              </a>
              <h3 class="font-bold text-gray-800">{{ $exercise->title }}</h3>
              <p class="text-xs text-gray-700">{{ $exercise->level }}</p>
              <a href="/member/lesson/{{ $exercise->id }}" class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition text-center w-full">
                Watch Now
              </a>
            </div>
          @endif
        @empty
          <div class="col-span-full text-center text-gray-500 py-12">
            No exercises found for this category.
          </div>
        @endforelse

        @if ($intermediate->hasPages())
          <div class="flex justify-center py-6 col-span-full">
            {{ $intermediate->appends(['tab' => 'intermediate', 'intermediate_page' => $intermediate->currentPage()])->links('components.pagination') }}
          </div>
        @endif
      </div>

      <!-- Advanced Tab -->
      <div x-show="activeTab === 'advanced'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($advanced as $exercise)
          @if (!$series && $exercise->series)
            <div class="bg-white p-6 rounded-lg shadow flex flex-col items-center space-y-4 relative group">
              <a href="{{ request()->fullUrlWithQuery(['series' => $exercise->series]) }}" class="w-full relative">
                <div class="relative z-10">
                   <img src="{{ $exercise->thumbnail_url }}" alt="{{ $exercise->title }}" class="w-full h-48 object-cover rounded-md border-2 border-white shadow-sm">
                </div>
                <div class="absolute top-1 left-1 w-full h-48 bg-gray-200 rounded-md border border-gray-300 -z-0 translate-x-1 translate-y-1"></div>
                <div class="absolute top-2 left-2 w-full h-48 bg-gray-100 rounded-md border border-gray-200 -z-10 translate-x-2 translate-y-2"></div>
              </a>
              <div class="text-center w-full pt-2">
                <h3 class="font-bold text-gray-800 text-lg">{{ $exercise->series }}</h3>
                <p class="text-sm text-blue-600 font-medium">{{ $exercise->item_count }} Lessons in this series</p>
              </div>
              <a href="{{ request()->fullUrlWithQuery(['series' => $exercise->series]) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-center w-full font-semibold">
                View Series
              </a>
            </div>
          @else
            <div class="bg-white p-6 rounded-lg shadow flex flex-col items-center space-y-4">
              <a href="/member/lesson/{{ $exercise->id }}">
              <img src="{{ $exercise->thumbnail_url }}" alt="{{ $exercise->title }}" class="w-full h-48 object-cover rounded-md">
              </a>
              <h3 class="font-bold text-gray-800">{{ $exercise->title }}</h3>
              <p class="text-xs text-gray-700">{{ $exercise->level }}</p>
              <a href="/member/lesson/{{ $exercise->id }}" class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition text-center w-full">
                Watch Now
              </a>
            </div>
          @endif
        @empty
          <div class="col-span-full text-center text-gray-500 py-12">
            No exercises found for this category.
          </div>
        @endforelse

        @if ($advanced->hasPages())
          <div class="flex justify-center py-6 col-span-full">
            {{ $advanced->appends(['tab' => 'advanced', 'advanced_page' => $advanced->currentPage()])->links('components.pagination') }}
          </div>
        @endif
      </div>

    </div>

  </div>
</section>

@endsection
