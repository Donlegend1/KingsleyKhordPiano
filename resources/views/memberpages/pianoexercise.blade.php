
@extends('layouts.member')

@section('content')
<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-6 px-4">
 <div class="max-w-7xl mx-auto space-y-3">
   
   <!-- Top Row -->
   <div class="flex justify-between items-center">
     <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
       <a href="/home" class="hover:text-blue-600">Dashboard</a>
       <span>/</span>
       <a href="/member/piano-exercise" class="hover:text-blue-600 font-semibold">Piano Exercises</a>
     </div>
     <div class="flex items-center space-x-2">
       <i class="fa fa-user-circle text-xl"></i>
     </div>
   </div>

   <!-- Second Row -->
   <div>
     <h1 class="text-xl font-bold">Piano Exercises</h1>
   </div>

 </div>
</section>
<section class="flex items-center justify-center bg-gray-100 p-6 min-h-screen">
  <div class="w-full max-w-6xl bg-white rounded-lg shadow-lg " x-data="{ activeTab: 'all' }">

    <!-- Tabs -->
    <div class="mb-6">
      <div class="flex flex-wrap gap-2 border-b">
        <button 
          class="py-2 px-4 text-gray-600 hover:text-blue-500 border-b-2"
          :class="{ 'border-blue-500 text-blue-500': activeTab === 'all' }"
          @click="activeTab = 'all'">All</button>

        <button 
          class="py-2 px-4 text-gray-600 hover:text-blue-500 border-b-2"
          :class="{ 'border-blue-500 text-blue-500': activeTab === 'independence' }"
          @click="activeTab = 'independence'">Independence</button>

        <button 
          class="py-2 px-4 text-gray-600 hover:text-blue-500 border-b-2"
          :class="{ 'border-blue-500 text-blue-500': activeTab === 'coordination' }"
          @click="activeTab = 'coordination'">Coordination</button>

        <button 
          class="py-2 px-4 text-gray-600 hover:text-blue-500 border-b-2"
          :class="{ 'border-blue-500 text-blue-500': activeTab === 'flexibility' }"
          @click="activeTab = 'flexibility'">Flexibility</button>

        <button 
          class="py-2 px-4 text-gray-600 hover:text-blue-500 border-b-2"
          :class="{ 'border-blue-500 text-blue-500': activeTab === 'strength' }"
          @click="activeTab = 'strength'">Strength</button>

        <button 
          class="py-2 px-4 text-gray-600 hover:text-blue-500 border-b-2"
          :class="{ 'border-blue-500 text-blue-500': activeTab === 'dexterity' }"
          @click="activeTab = 'dexterity'">Dexterity</button>
      </div>
    </div>

    <!-- Content Area -->
    <div class="space-y-6">

      <!-- All Content -->
      <div x-show="activeTab === 'all'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        @foreach ($all as $exercise) 
          <div class="bg-gray-100 p-6  rounded-lg shadow-lg flex flex-col items-center space-y-4" >
          <img src="{{$exercise->thumbnail_url }}" alt="{{$exercise->title}}" class="w-full h-56 object-cover rounded-md">
          <h3 class="font-bold text-gray-800">{{$exercise->title}}</h3>
          {{-- <p class="text-gray-600 text-center">{{$exercise->description}}</p> --}}
          <a href="/member/extra-courses/{{$exercise->id}}" class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition text-center">Watch Now</a>
        </div>
        @endforeach
      
      </div>

      <!-- Beginner Content -->
      <div x-show="activeTab === 'independence'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
         @foreach ($independence as $exercise) 
          <div class="bg-gray-100 p-6  rounded-lg shadow-lg flex flex-col items-center space-y-4" >
          <img src="{{$exercise->thumbnail_url }}" alt="{{$exercise->title}}" class="w-full h-56 object-cover rounded-md">
          <h3 class="font-bold text-gray-800">{{$exercise->title}}</h3>
          {{-- <p class="text-gray-600 text-center">{{$exercise->description}}</p> --}}
          <a href="/member/extra-courses/{{$exercise->id}}" class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition text-center">Watch Now</a>
        </div>
        @endforeach
      </div>

      <!-- Intermediate Content -->
      <div x-show="activeTab === 'coordination'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($coordination as $exercise) 
          <div class="bg-gray-100 p-6  rounded-lg shadow-lg flex flex-col items-center space-y-4" >
          <img src="{{$exercise->thumbnail_url }}" alt="{{$exercise->title}}" class="w-full h-56 object-cover rounded-md">
          <h3 class="font-bold text-gray-800">{{$exercise->title}}</h3>
          {{-- <p class="text-gray-600 text-center">{{$exercise->description}}</p> --}}
          <a href="/member/extra-courses/{{$exercise->id}}" class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition text-center">Watch Now</a>
        </div>
        @endforeach
      </div>

      <!-- Advanced Content -->
      <div x-show="activeTab === 'flexibility'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
         @foreach ($flexibility as $exercise) 
          <div class="bg-gray-100 p-6  rounded-lg shadow-lg flex flex-col items-center space-y-4" >
          <img src="{{$exercise->thumbnail_url }}" alt="{{$exercise->title}}" class="w-full h-56 object-cover rounded-md">
          <h3 class="font-bold text-gray-800">{{$exercise->title}}</h3>
          {{-- <p class="text-gray-600 text-center">{{$exercise->description}}</p> --}}
          <a href="/member/extra-courses/{{$exercise->id}}" class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition text-center">Watch Now</a>
        </div>
        @endforeach
      </div>

      <div x-show="activeTab === 'strength'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($strength as $exercise) 
          <div class="bg-gray-100 p-6  rounded-lg shadow-lg flex flex-col items-center space-y-4" >
          <img src="{{$exercise->thumbnail_url }}" alt="{{$exercise->title}}" class="w-full h-56 object-cover rounded-md">
          <h3 class="font-bold text-gray-800">{{$exercise->title}}</h3>
          {{-- <p class="text-gray-600 text-center">{{$exercise->description}}</p> --}}
          <a href="/member/extra-courses/{{$exercise->id}}" class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition text-center">Watch Now</a>
        </div>
        @endforeach
      </div>
      <div x-show="activeTab === 'dexterity'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($dexterity as $exercise) 
          <div class="bg-gray-100 p-6  rounded-lg shadow-lg flex flex-col items-center space-y-4" >
          <img src="{{$exercise->thumbnail_url }}" alt="{{$exercise->title}}" class="w-full h-56 object-cover rounded-md">
          <h3 class="font-bold text-gray-800">{{$exercise->title}}</h3>
          {{-- <p class="text-gray-600 text-center">{{$exercise->description}}</p> --}}
          <a href="/member/extra-courses/{{$exercise->id}}" class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition text-center">Watch Now</a>
        </div>
        @endforeach
      </div>

    </div>
  </div>
</section>



<section class="flex items-center justify-center py-6 bg-gray-100">
 <div class="flex items-center space-x-2">
   <!-- Previous Button -->
   <button 
     class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-200"
     :disabled="currentPage === 1"
     @click="currentPage--"
   >
     Previous
   </button>

   <!-- Numbered Pagination Links -->
   <template x-for="page in totalPages" :key="page">
     <button 
       class="px-4 py-2 border rounded-lg"
       :class="{
         'bg-blue-500 text-white': page === currentPage,
         'bg-white text-gray-700 border-gray-300': page !== currentPage
       }"
       @click="currentPage = page"
     >
       <span x-text="page"></span>
     </button>
   </template>

   <!-- Next Button -->
   <button 
     class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-200"
     :disabled="currentPage === totalPages"
     @click="currentPage++"
   >
     Next
   </button>
 </div>
</section>

<script>
 document.addEventListener('alpine:init', () => {
   Alpine.data('pagination', () => ({
     currentPage: 1,
     totalPages: 5,
   }));
 });
</script>

@endsection