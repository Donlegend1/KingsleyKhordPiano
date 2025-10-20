@extends('layouts.member')

@section('content')

<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-6 px-4">
  <div class="max-w-7xl mx-auto space-y-3">

    <!-- Breadcrumb & User -->
    <div class="flex justify-between items-center">
      <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
        <a href="/home" class="hover:text-blue-600">Dashboard</a>
        <span>/</span>
        <a href="/member/extra-courses" class="hover:text-blue-600 font-semibold">Extra Courses</a>
      </div>
      <div class="flex items-center space-x-2">
        <i class="fa fa-user-circle text-xl"></i>
      </div>
    </div>

    <div>
      <h1 class="text-xl font-bold">Extra Courses</h1>
    </div>
       <form method="GET" action="{{ route('extra.courses') }}" class="mb-8 flex justify-end">
     <div class="relative w-full max-w-xs">
       <input type="text" name="name" id="name" value="{{ request('name') }}" class="w-full border border-gray-300 rounded-full p-2 pr-10 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" placeholder="Search...">
     </div>
     <button type="submit" class="ml-2 px-4 py-1 bg-gray-300 text-white rounded-2xl font-semibold shadow hover:bg-gray-700 transition"><span class="fa fa-search text-black hover:text-white"></span></button>
   </form>
  </div>
</section>

<section class="bg-gray-100 py-10 px-4 sm:px-6 lg:px-8 xl:px-10">
  <div 
    class="w-full max-w-7xl mx-auto bg-white rounded-lg shadow-lg p-6" 
    x-data="{ 
      activeTab: '{{ request('tab', 'all') }}', 
      allPage: {{ request('all_page', 1) }}, 
      beginnerPage: {{ request('beginner_page', 1) }}, 
      intermediatePage: {{ request('intermediate_page', 1) }}, 
      advancedPage: {{ request('advanced_page', 1) }} 
    }"
  >

    <!-- Mobile Dropdown -->
    <div class="block lg:hidden mb-6">
      <select 
        x-model="activeTab" 
        class="w-full p-3 rounded-lg border border-gray-300 text-gray-700"
      >
        <option value="all">All</option>
        <option value="beginner">Beginner</option>
        <option value="intermediate">Intermediate</option>
        <option value="advanced">Advanced</option>
      </select>
    </div>

    <!-- Desktop Tabs -->
    <div class="hidden lg:flex border-b mb-6 space-x-6">
      <button 
        class="py-2 px-4 text-gray-600 hover:text-blue-500 border-b-2 transition" 
        :class="{ 'border-blue-500 text-blue-500': activeTab === 'all' }"
        @click="activeTab = 'all'">
        All
      </button>
      <button 
        class="py-2 px-4 text-gray-600 hover:text-blue-500 border-b-2 transition" 
        :class="{ 'border-blue-500 text-blue-500': activeTab === 'beginner' }"
        @click="activeTab = 'beginner'">
        Beginner
      </button>
      <button 
        class="py-2 px-4 text-gray-600 hover:text-blue-500 border-b-2 transition" 
        :class="{ 'border-blue-500 text-blue-500': activeTab === 'intermediate' }"
        @click="activeTab = 'intermediate'">
        Intermediate
      </button>
      <button 
        class="py-2 px-4 text-gray-600 hover:text-blue-500 border-b-2 transition" 
        :class="{ 'border-blue-500 text-blue-500': activeTab === 'advanced' }"
        @click="activeTab = 'advanced'">
        Advanced
      </button>
    </div>

    <!-- Content Area -->
    <div class="space-y-4 mt-4">

      <!-- All Tab -->
      <div x-show="activeTab === 'all'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($all as $exercise)
          <div class="bg-white p-6 rounded-lg shadow flex flex-col items-center space-y-4">
            <a href="/member/lesson/{{ $exercise->id }}">
              <img src="{{ $exercise->thumbnail_url }}" alt="{{ $exercise->title }}" class="w-full object-cover rounded-md">
            </a>
            <h3 class="font-bold text-gray-800">{{ $exercise->title }}</h3>
            <p class="text-xs text-gray-700">{{ $exercise->level }}</p>
            <a href="/member/lesson/{{ $exercise->id }}" class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition text-center w-full">
              Watch Now
            </a>
          </div>
        @empty
          <div class="col-span-full text-center text-gray-500 py-12 text-lg font-semibold">
            <i class="fa fa-exclamation-circle fa-2x mb-2"></i>
            <p>No result found.</p>
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
          <div class="bg-white p-6 rounded-lg shadow flex flex-col items-center space-y-4">
            <a href="/member/lesson/{{ $exercise->id }}">
              <img src="{{ $exercise->thumbnail_url }}" alt="{{ $exercise->title }}" class="w-full object-cover rounded-md">
            </a>
            <h3 class="font-bold text-gray-800">{{ $exercise->title }}</h3>
            <p class="text-xs text-gray-700">{{ $exercise->level }}</p>
            <a href="/member/lesson/{{ $exercise->id }}" class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition text-center w-full">
              Watch Now
            </a>
          </div>
        @empty
          <div class="col-span-full text-center text-gray-500 py-12 text-lg font-semibold">
            <i class="fa fa-exclamation-circle fa-2x mb-2"></i>
            <p>No result found.</p>
          </div>
        @endforelse
        @if ($beginner->hasPages())
          <div class="flex justify-center py-6 col-span-full">
            {{ $beginner->appends(['tab' => 'beginner', 'beginner_page' => $beginner->currentPage()])->links('components.pagination') }}
          </div>
        @endif
      </div>

      <!-- Intermediate Tab -->
      <div x-show="activeTab === 'intermediate'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($intermediate as $exercise)
          <div class="bg-white p-6 rounded-lg shadow flex flex-col items-center space-y-4">
            <a href="/member/lesson/{{ $exercise->id }}">
              <img src="{{ $exercise->thumbnail_url }}" alt="{{ $exercise->title }}" class="w-full object-cover rounded-md">
            </a>
            <h3 class="font-bold text-gray-800">{{ $exercise->title }}</h3>
            <p class="text-xs text-gray-700">{{ $exercise->level }}</p>
            <a href="/member/lesson/{{ $exercise->id }}" class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition text-center w-full">
              Watch Now
            </a>
          </div>
        @empty
          <div class="col-span-full text-center text-gray-500 py-12 text-lg font-semibold">
            <i class="fa fa-exclamation-circle fa-2x mb-2"></i>
            <p>No result found.</p>
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
          <div class="bg-white p-6 rounded-lg shadow flex flex-col items-center space-y-4">
            <a href="/member/lesson/{{ $exercise->id }}">
              <img src="{{ $exercise->thumbnail_url }}" alt="{{ $exercise->title }}" class="w-full object-cover rounded-md">
            </a>
            <h3 class="font-bold text-gray-800">{{ $exercise->title }}</h3>
            <p class="text-xs text-gray-700">{{ $exercise->level }}</p>
            <a href="/member/lesson/{{ $exercise->id }}" class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition text-center w-full">
              Watch Now
            </a>
          </div>
        @empty
          <div class="col-span-full text-center text-gray-500 py-12 text-lg font-semibold">
            <i class="fa fa-exclamation-circle fa-2x mb-2"></i>
            <p>No result found.</p>
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
