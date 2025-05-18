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

<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white pt-10 pb-5 px-12">

 <div class="flex flex-wrap justify-center gap-10">
  <div class="max-w-sm rounded overflow-hidden shadow-lg bg-[#F3F5F6]">
    <img class="w-full p-1" src="/images/featured1.png" alt="Course Image 1">
    <div class="p-1">
     <p class="font-bold font-[20px]">
      Beginner Course
     </p>
     <p class="text-[15px] text-[#717171]">
      Start your piano journey with a clear, step-by-step method designed to make learning both easy and practical.
     </p>
    </div>
    <div class="py-4 p-1">
     <a href="/member/course/beginner" class="px-5 py-2 bg-[#404348] text-white text-sm rounded-full hover:bg-yellow-400 hover:text-black transition inline-flex items-center">
      Enroll Now <i class="fa fa-angle-right ml-2" aria-hidden="true"></i>
    </a>
    </div>
  </div>
  <div class="max-w-sm rounded overflow-hidden shadow-lg bg-[#F3F5F6]">
    <img class="w-full p-1" src="/images/featured2.png" alt="Course Image 2">
    <div class="p-1">
     <p class="font-bold font-[20px]">
      Intermediate Course
     </p>
     <p class="text-[15px] text-[#717171]">
      Once you’ve mastered the basics, take your skills to the next level with this intermediate course.
     </p>
    </div>
    <div class="py-4 p-1">
     <a href="/member/course/intermediate" class="px-5 py-2 bg-[#404348] text-white text-sm rounded-full hover:bg-yellow-400 hover:text-black transition inline-flex items-center">
      Enroll Now <i class="fa fa-angle-right ml-2" aria-hidden="true"></i>
    </a>
    </div>
  </div>
  <div class="max-w-sm rounded overflow-hidden shadow-lg bg-[#F3F5F6]">
    <img class="w-full p-1" src="/images/featured3.png" alt="Course Image 3">
    <div class="p-1">
     <p class="font-bold font-[20px]">
      Advanced Course
     </p>
     <p class="text-[15px] text-[#717171]">
      After refining your intermediate skills, take the leap into advanced playing with this course.
     </p>
    </div>
    <div class="py-4 p-1">
     <a href="/member/course/advanced" class="px-5 py-2 bg-[#404348] text-white text-sm rounded-full hover:bg-yellow-400 hover:text-black transition inline-flex items-center">
      Enroll Now <i class="fa fa-angle-right ml-2" aria-hidden="true"></i>
    </a>
    </div>
  </div>
</div>
</section>
@endsection