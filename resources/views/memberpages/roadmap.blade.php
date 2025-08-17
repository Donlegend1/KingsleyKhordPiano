@extends('layouts.member')

@section('content')
<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-6 px-4">
 <div class="max-w-7xl mx-auto space-y-3">
   
   <!-- Top Row -->
   <div class="flex justify-between items-center">
     <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
       <a href="/home" class="hover:text-blue-600">Dashboard</a>
       <span>/</span>
       <a href="member/roadmap" class="hover:text-blue-600 font-semibold">Roadmap</a>
     </div>
     <div class="flex items-center space-x-2">
       <i class="fa fa-user-circle text-xl"></i>
     </div>
   </div>

   <!-- Second Row -->
   <div>
     <h1 class="text-xl font-bold">Roadmap</h1>
   </div>

 </div>
</section>

<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white pt-10 pb-5 px-4 sm:px-6 lg:px-12">
  <div class="flex flex-wrap md:flex-nowrap justify-center gap-6 lg:gap-10">
    <!-- Card Template -->
    <div class="w-full md:w-1/3 max-w-sm rounded overflow-hidden shadow-lg bg-[#F3F5F6]">
      <a href="/member/course/beginner">
      <div class="px-4 pt-4">
        <img class="w-full object-cover h-48 rounded-md" src="/images/featured1.jpeg" alt="Course Image 1">
      </div>
      </a>
      <div class="px-4 py-3">
        <p class="text-xl font-semibold text-black text-center">Getting Started and Mastering the Fundamentals</p>
        <div class="pt-4 flex justify-center">
          <a href="/member/course/beginner" class="px-6 py-3 bg-[#404348] text-white text-base font-medium hover:bg-yellow-400 hover:text-black transition inline-flex items-center rounded-md">
            Start Course <i class="fa fa-angle-right ml-2" aria-hidden="true"></i>
          </a>
        </div>
      </div>
    </div>

    <!-- Card 2 -->
    <div class="w-full md:w-1/3 max-w-sm rounded overflow-hidden shadow-lg bg-[#F3F5F6]">
      <a href="/member/course/intermediate">
      <div class="px-4 pt-4">
        <img class="w-full object-cover h-48 rounded-md" src="/images/featured2.jpeg" alt="Course Image 2">
      </div>
      </a>
      <div class="px-4 py-3">
        <p class="text-xl font-semibold text-black text-center">Improve the quality of your Chords and Learn to Harmonize them</p>
        <div class="pt-4 flex justify-center">
          <a href="/member/course/intermediate" class="px-6 py-3 bg-[#404348] text-white text-base font-medium hover:bg-yellow-400 hover:text-black transition inline-flex items-center rounded-md">
            Start Course <i class="fa fa-angle-right ml-2" aria-hidden="true"></i>
          </a>
        </div>
      </div>
    </div>

    <!-- Card 3 -->
    <div class="w-full md:w-1/3 max-w-sm rounded overflow-hidden shadow-lg bg-[#F3F5F6]">
       <a href="/member/course/advanced" >
      <div class="px-4 pt-4">
        <img class="w-full object-cover h-48 rounded-md" src="/images/featured3.jpeg" alt="Course Image 3">
      </div>
       </a>
      <div class="px-4 py-3">
        <p class="text-xl font-semibold text-black text-center">Become a Musical Genius – Learn to Think of Music Abstractly</p>
        <div class="pt-4 flex justify-center">
          <a href="/member/course/advanced" class="px-6 py-3 bg-[#404348] text-white text-base font-medium hover:bg-yellow-400 hover:text-black transition inline-flex items-center rounded-md">
            Start Course <i class="fa fa-angle-right ml-2" aria-hidden="true"></i>
          </a>
        </div>
      </div>
    </div>
  </div>
</section>




@endsection