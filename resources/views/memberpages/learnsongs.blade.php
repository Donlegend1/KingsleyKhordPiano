@extends('layouts.member')

@section('content')

<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-6 px-4">
  <div class="max-w-7xl mx-auto space-y-3">

    <!-- Breadcrumb & User -->
    <div class="flex justify-between items-center">
      <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
        <a href="/home" class="hover:text-blue-600">Dashboard</a>
        <span>/</span>
        <a href="/member/learn-songs" class="hover:text-blue-600 font-semibold">Learn Songs</a>
      </div>
      <div class="flex items-center space-x-2">
        <i class="fa fa-user-circle text-xl"></i>
      </div>
    </div>

    <div>
      <h1 class="text-xl font-bold">Learn Songs</h1>
    </div>
  </div>
</section>

<section class="flex items-start justify-center bg-gray-100 py-10 px-4 min-h-[70vh]">
  <div class="w-full max-w-6xl bg-white rounded-xl shadow-lg p-6 md:p-10" x-data="{ activeTab: 'all' }">

    <!-- Mobile Dropdown -->
    <div class="block lg:hidden mb-6">
      <select x-model="activeTab" class="w-full p-3 rounded-lg border border-gray-300 text-gray-700">
        <option value="all">All</option>
        <option value="beginner">Beginner</option>
        <option value="intermediate">Intermediate</option>
        <option value="advanced">Advanced</option>
      </select>
    </div>

    <!-- Desktop Tabs -->
    <div class="hidden lg:flex justify-between border-b mb-6">
      <button 
        class="py-2 px-4 text-gray-600 hover:text-blue-500 border-b-2"
        :class="{ 'border-blue-500 text-blue-500': activeTab === 'all' }"
        @click="activeTab = 'all'">All</button>

      <button 
        class="py-2 px-4 text-gray-600 hover:text-blue-500 border-b-2"
        :class="{ 'border-blue-500 text-blue-500': activeTab === 'beginner' }"
        @click="activeTab = 'beginner'">Beginner</button>

      <button 
        class="py-2 px-4 text-gray-600 hover:text-blue-500 border-b-2"
        :class="{ 'border-blue-500 text-blue-500': activeTab === 'intermediate' }"
        @click="activeTab = 'intermediate'">Intermediate</button>

      <button 
        class="py-2 px-4 text-gray-600 hover:text-blue-500 border-b-2"
        :class="{ 'border-blue-500 text-blue-500': activeTab === 'advanced' }"
        @click="activeTab = 'advanced'">Advanced</button>
    </div>

    <!-- Content Area -->
    <div class="space-y-4 mt-4">

      <!-- All -->
      <div x-show="activeTab === 'all'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($all as $exercise)
          <div class="bg-white p-6 rounded-lg shadow flex flex-col items-center space-y-4">
            <img src="{{ $exercise->thumbnail_url }}" alt="{{ $exercise->title }}" class="w-full h-56 object-cover rounded-md">
            <h3 class="font-bold text-gray-800">{{ $exercise->title }}</h3>
            <a href="/member/lesson/{{ $exercise->id }}" class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition text-center w-full">
              Watch Now
            </a>
          </div>
        @empty
          <div class="col-span-full text-center text-gray-500 py-12">No exercises found for this category.</div>
        @endforelse
        @if ($all->total() > 9)
          <div class="flex justify-center py-6 col-span-full">{{ $all->links() }}</div>
        @endif
      </div>

      <!-- Beginner -->
      <div x-show="activeTab === 'beginner'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($beginner as $exercise)
          <div class="bg-white p-6 rounded-lg shadow flex flex-col items-center space-y-4">
            <img src="{{ $exercise->thumbnail_url }}" alt="{{ $exercise->title }}" class="w-full h-56 object-cover rounded-md">
            <h3 class="font-bold text-gray-800">{{ $exercise->title }}</h3>
            <a href="/member/lesson/{{ $exercise->id }}" class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition text-center w-full">
              Watch Now
            </a>
          </div>
        @empty
          <div class="col-span-full text-center text-gray-500 py-12">No exercises found for this category.</div>
        @endforelse
        @if ($beginner->total() > 9)
          <div class="flex justify-center py-6 col-span-full">{{ $beginner->links() }}</div>
        @endif
      </div>

      <!-- Intermediate -->
      <div x-show="activeTab === 'intermediate'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($intermediate as $exercise)
          <div class="bg-white p-6 rounded-lg shadow flex flex-col items-center space-y-4">
            <img src="{{ $exercise->thumbnail_url }}" alt="{{ $exercise->title }}" class="w-full h-56 object-cover rounded-md">
            <h3 class="font-bold text-gray-800">{{ $exercise->title }}</h3>
            <a href="/member/lesson/{{ $exercise->id }}" class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition text-center w-full">
              Watch Now
            </a>
          </div>
        @empty
          <div class="col-span-full text-center text-gray-500 py-12">No exercises found for this category.</div>
        @endforelse
        @if ($intermediate->total() > 9)
          <div class="flex justify-center py-6 col-span-full">{{ $intermediate->links() }}</div>
        @endif
      </div>

      <!-- Advanced -->
      <div x-show="activeTab === 'advanced'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($advanced as $exercise)
          <div class="bg-white p-6 rounded-lg shadow flex flex-col items-center space-y-4">
            <img src="{{ $exercise->thumbnail_url }}" alt="{{ $exercise->title }}" class="w-full h-56 object-cover rounded-md">
            <h3 class="font-bold text-gray-800">{{ $exercise->title }}</h3>
            <a href="/member/lesson/{{ $exercise->id }}" class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition text-center w-full">
              Watch Now
            </a>
          </div>
        @empty
          <div class="col-span-full text-center text-gray-500 py-12">No exercises found for this category.</div>
        @endforelse
        @if ($advanced->total() > 9)
          <div class="flex justify-center py-6 col-span-full">{{ $advanced->links() }}</div>
        @endif
      </div>

    </div>

  </div>
</section>
@endsection
