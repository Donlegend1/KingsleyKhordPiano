@extends('layouts.member')

@section('content')
<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-3 px-4">
  <div class="max-w-7xl mx-auto space-y-3">

    <!-- Breadcrumbs & User -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3">
      <div class="flex items-center flex-wrap text-sm text-gray-600 dark:text-gray-400 space-x-2">
        <a href="/home" class="hover:text-blue-600">Dashboard</a>
        <span>/</span>
        <a href="/member/extra-courses" class="hover:text-blue-600 font-semibold">Extra Courses</a>
      </div>
      <div>
        <i class="fa fa-user-circle text-xl"></i>
      </div>
    </div>

    <h1 class="text-xl font-bold mt-2">Extra Courses</h1>

  </div>
</section>

<section class="bg-gray-100 py-10 px-4 sm:px-6 lg:px-8 xl:px-10">
  <div class="w-full max-w-7xl mx-auto bg-white rounded-lg shadow-lg p-6" x-data="{ activeTab: 'all' }">

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

      <!-- All Tab -->
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
          <div class="col-span-full text-center text-gray-500 py-12">
            No exercises found for this category.
          </div>
        @endforelse

        @if ($all->total() > 9)
          <div class="flex justify-center py-6 col-span-full">
            {{ $all->links() }}
          </div>
        @endif
      </div>

      <!-- Beginner Tab -->
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
          <div class="col-span-full text-center text-gray-500 py-12">
            No exercises found for this category.
          </div>
        @endforelse

        @if ($beginner->total() > 9)
          <div class="flex justify-center py-6 col-span-full">
            {{ $beginner->links() }}
          </div>
        @endif
      </div>

      <!-- Intermediate Tab -->
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
          <div class="col-span-full text-center text-gray-500 py-12">
            No exercises found for this category.
          </div>
        @endforelse

        @if ($intermediate->total() > 9)
          <div class="flex justify-center py-6 col-span-full">
            {{ $intermediate->links() }}
          </div>
        @endif
      </div>

      <!-- Advanced Tab -->
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
          <div class="col-span-full text-center text-gray-500 py-12">
            No exercises found for this category.
          </div>
        @endforelse

        @if ($advanced->total() > 9)
          <div class="flex justify-center py-6 col-span-full">
            {{ $advanced->links() }}
          </div>
        @endif
      </div>

    </div>

  </div>
</section>

@endsection
