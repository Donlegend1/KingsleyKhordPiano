
@extends('layouts.member')

@section('content')
<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-6 px-4">
 <div class="max-w-7xl mx-auto space-y-3">
   
   <!-- Top Row -->
   <div class="flex justify-between items-center">
     <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
       <a href="/home" class="hover:text-blue-600">Dashboard</a>
       <span>/</span>
       <a href="/member/quick-lessons" class="hover:text-blue-600 font-semibold">Quick Lesson</a>
     </div>
     <div class="flex items-center space-x-2">
       <i class="fa fa-user-circle text-xl"></i>
     </div>
   </div>

   <!-- Second Row -->
   <div>
     <h1 class="text-xl font-bold">Quick Lesson</h1>
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
       <div class="bg-gray-100 p-6 rounded-lg shadow-lg flex flex-col items-center space-y-4">
         <img src="https://via.placeholder.com/300x150" alt="All 1" class="w-full h-36 object-cover rounded-md">
         <h3 class="font-bold text-gray-800">All Exercise 1</h3>
         <p class="text-gray-600 text-center">Comprehensive exercises for all levels.</p>
          <a href="/member/extra-courses/1" class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition text-center">Watch Now</a>
       </div>

       <div class="bg-gray-100 p-6 rounded-lg shadow-lg flex flex-col items-center space-y-4">
         <img src="https://via.placeholder.com/300x150" alt="All 2" class="w-full h-36 object-cover rounded-md">
         <h3 class="font-bold text-gray-800">All Exercise 2</h3>
         <p class="text-gray-600 text-center">Another exercise for all levels.</p>
          <a href="/member/extra-courses/1" class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition text-center">Watch Now</a>
       </div>
     </div>

     <!-- Beginner Content -->
     <div x-show="activeTab === 'beginner'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
       <div class="bg-gray-100 p-6 rounded-lg shadow-lg flex flex-col items-center space-y-4">
         <img src="https://via.placeholder.com/300x150" alt="Beginner 1" class="w-full h-36 object-cover rounded-md">
         <h3 class="font-bold text-gray-800">Beginner Exercise 1</h3>
         <p class="text-gray-600 text-center">Basic finger movements.</p>
          <a href="/member/extra-courses/1" class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition text-center">Watch Now</a>
       </div>
     </div>

     <!-- Intermediate Content -->
     <div x-show="activeTab === 'intermediate'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
       <div class="bg-gray-100 p-6 rounded-lg shadow-lg flex flex-col items-center space-y-4">
         <img src="https://via.placeholder.com/300x150" alt="Intermediate 1" class="w-full h-36 object-cover rounded-md">
         <h3 class="font-bold text-gray-800">Intermediate Exercise 1</h3>
         <p class="text-gray-600 text-center">Chord progressions and inversions.</p>
          <a href="/member/extra-courses/1" class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition text-center">Watch Now</a>
       </div>
     </div>

     <!-- Advanced Content -->
     <div x-show="activeTab === 'advanced'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
       <div class="bg-gray-100 p-6 rounded-lg shadow-lg flex flex-col items-center space-y-4">
         <img src="https://via.placeholder.com/300x150" alt="Advanced 1" class="w-full h-36 object-cover rounded-md">
         <h3 class="font-bold text-gray-800">Advanced Exercise 1</h3>
         <p class="text-gray-600 text-center">Jazz improvisation basics.</p>
          <a href="/member/extra-courses/1" class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition text-center">Watch Now</a>
       </div>
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