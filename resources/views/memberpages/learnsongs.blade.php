
@extends('layouts.member')

@section('content')
<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-6 px-4">
 <div class="max-w-7xl mx-auto space-y-3">
   
   <!-- Top Row -->
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

   <!-- Second Row -->
   <div>
     <h1 class="text-xl font-bold">Learn Songs</h1>
   </div>

 </div>
</section>
<section class="flex items-center justify-center bg-gray-100 p-6 min-h-screen">
 <div class="w-full max-w-6xl bg-white rounded-lg shadow-lg p-6" x-data="{ activeTab: 'all' }">
   <!-- Tabs -->
   <div class="mb-6">
     <div class="flex justify-between border-b">
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
   </div>

   <!-- Content Area -->
   <div class="space-y-4">
     <!-- All Content -->
     <div x-show="activeTab === 'all'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          @forelse ($all as $exercise)
      <div class="bg-white p-6 rounded-lg shadow flex flex-col items-center space-y-4">
        <img src="{{ $exercise->thumbnail_url }}" alt="{{ $exercise->title }}" class="w-full h-56 object-cover rounded-md">
        <h3 class="font-bold text-gray-800">{{ $exercise->title }}</h3>
        <a href="/member/lesson/{{ $exercise->id }}" class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition text-center">
          Watch Now
        </a>
      </div>
          @empty
            <div class="col-span-full text-center text-gray-500 py-12">
              No exercises found for this category.
            </div>
          @endforelse
          @if ($advanced->total() > 9)
            <div class="flex justify-center py-6 bg-gray-100 rounded-md mt-4">
              {{ $advanced->links() }}
            </div>
          @endif
     </div>

     <!-- Beginner Content -->
     <div x-show="activeTab === 'beginner'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          @forelse ($beginner as $exercise)
      <div class="bg-white p-6 rounded-lg shadow flex flex-col items-center space-y-4">
        <img src="{{ $exercise->thumbnail_url }}" alt="{{ $exercise->title }}" class="w-full h-56 object-cover rounded-md">
        <h3 class="font-bold text-gray-800">{{ $exercise->title }}</h3>
        <a href="/member/lesson/{{ $exercise->id }}" class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition text-center">
          Watch Now
        </a>
      </div>
          @empty
            <div class="col-span-full text-center text-gray-500 py-12">
              No exercises found for this category.
            </div>
          @endforelse
          @if ($advanced->total() > 9)
            <div class="flex justify-center py-6 bg-gray-100 rounded-md mt-4">
              {{ $advanced->links() }}
            </div>
          @endif
     </div>

     <!-- Intermediate Content -->
     <div x-show="activeTab === 'intermediate'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          @forelse ($intermediate as $exercise)
      <div class="bg-white p-6 rounded-lg shadow flex flex-col items-center space-y-4">
        <img src="{{ $exercise->thumbnail_url }}" alt="{{ $exercise->title }}" class="w-full h-56 object-cover rounded-md">
        <h3 class="font-bold text-gray-800">{{ $exercise->title }}</h3>
        <a href="/member/lesson/{{ $exercise->id }}" class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition text-center">
          Watch Now
        </a>
      </div>
          @empty
            <div class="col-span-full text-center text-gray-500 py-12">
              No exercises found for this category.
            </div>
          @endforelse
          @if ($advanced->total() > 9)
            <div class="flex justify-center py-6 bg-gray-100 rounded-md mt-4">
              {{ $advanced->links() }}
            </div>
          @endif
     </div>

     <!-- Advanced Content -->
     <div x-show="activeTab === 'advanced'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
         @forelse ($advanced as $exercise)
      <div class="bg-white p-6 rounded-lg shadow flex flex-col items-center space-y-4">
        <img src="{{ $exercise->thumbnail_url }}" alt="{{ $exercise->title }}" class="w-full h-56 object-cover rounded-md">
        <h3 class="font-bold text-gray-800">{{ $exercise->title }}</h3>
        <a href="/member/lesson/{{ $exercise->id }}" class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition text-center">
          Watch Now
        </a>
      </div>
          @empty
            <div class="col-span-full text-center text-gray-500 py-12">
              No exercises found for this category.
            </div>
          @endforelse
          @if ($advanced->total() > 9)
            <div class="flex justify-center py-6 bg-gray-100 rounded-md mt-4">
              {{ $advanced->links() }}
            </div>
          @endif
     </div>
   </div>
 </div>
</section>

@endsection