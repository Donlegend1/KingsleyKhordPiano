@extends('layouts.member')

@section('content')
<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-6 px-4">
 <div class="max-w-7xl mx-auto space-y-3">
   
   <!-- Top Row -->
   <div class="flex justify-between items-center">
     <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
       <a href="/home" class="hover:text-blue-600">Dashboard</a>
       <span>/</span>
       <a href="/member/piano-exercise" class="hover:text-blue-600 font-semibold">Piano Exercise</a>
     </div>
     <div class="flex items-center space-x-2">
       <i class="fa fa-user-circle text-xl"></i>
     </div>
   </div>

   <!-- Second Row -->
   <div>
     <h1 class="text-xl font-bold">Piano Exercise</h1>
   </div>

 </div>
</section>

<section class="flex justify-center items-center my-5">
 <div class="w-full max-w-3xl  bg-[#F3F5F6] p-8 rounded-lg shadow-lg text-center space-y-4">
   <p class="text-gray-700 ">
     Hands-on daily exercises to strengthen your fingers. What’s learning to play the piano without consistent, hands-on practice? 
     Your fingers need a lot of practical training to produce flawless rhythms. These exercises are designed to help you develop 
     strong, flexible fingers.
   </p>
   <div class="flex justify-center mt-4">
     <i class="fa fa-angle-down text-gray-400 text-lg"></i>
   </div>
 </div>
</section>


<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white p-6 rounded-lg shadow-lg max-w-3xl mx-auto" x-data="{ openTab: null }">
 <h2 class="text-2xl font-bold mb-4">Video Tutorials</h2>

 <!-- Video 1 -->
 <div class="mb-4">
   <button 
     @click="openTab = openTab === 1 ? null : 1"
     class="w-full text-left py-3 px-4 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 flex justify-between items-center rounded-lg"
   >
     <span>Introduction to the Platform</span>
     <span x-show="openTab !== 1">+</span>
     <span x-show="openTab === 1">-</span>
   </button>
   <div x-show="openTab === 1" class="mt-2 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
     <video controls class="w-full rounded-lg">
       <source src="video1.mp4" type="video/mp4">
       Your browser does not support the video tag.
     </video>
   </div>
 </div>

 <!-- Video 2 -->
 <div class="mb-4">
   <button 
     @click="openTab = openTab === 2 ? null : 2"
     class="w-full text-left py-3 px-4 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 flex justify-between items-center rounded-lg"
   >
     <span>Getting Started with Features</span>
     <span x-show="openTab !== 2">+</span>
     <span x-show="openTab === 2">-</span>
   </button>
   <div x-show="openTab === 2" class="mt-2 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
     <video controls class="w-full rounded-lg">
       <source src="video2.mp4" type="video/mp4">
       Your browser does not support the video tag.
     </video>
   </div>
 </div>

 <!-- Video 3 -->
 <div>
   <button 
     @click="openTab = openTab === 3 ? null : 3"
     class="w-full text-left py-3 px-4 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 flex justify-between items-center rounded-lg"
   >
     <span>Advanced Techniques</span>
     <span x-show="openTab !== 3">+</span>
     <span x-show="openTab === 3">-</span>
   </button>
   <div x-show="openTab === 3" class="mt-2 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
     <video controls class="w-full rounded-lg">
       <source src="video3.mp4" type="video/mp4">
       Your browser does not support the video tag.
     </video>
   </div>
 </div>

</section>
@endsection