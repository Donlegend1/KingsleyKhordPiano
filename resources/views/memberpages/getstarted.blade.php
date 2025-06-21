@extends('layouts.member')

@section('content')
<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-6 px-4 ">
 <div class="max-w-7xl mx-auto space-y-3">
     
     <!-- Top Row -->
     <div class="flex justify-between items-center">
         <h1 class="text-xl font-bold">Get Started</h1>
         <div class="flex items-center space-x-2">
             <i class="fa fa-user-circle text-xl "></i>
             <span class="text-sm font-medium">{{ auth()->user()->name }}</span>
         </div>
     </div>

     <!-- Second Row -->
     <div class="flex justify-left items-center">
         <p class="text-sm lg:text-base font-medium text-[#404348]">
            Your journey begins here, <span class="font-semibold text-black">{{ auth()->user()->name }}</span>
         </p>
        

     </div>

 </div>
</section>

<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-2 px-12">
 <div class="font-bold mb-5 text-[22px]">
  <p>
   Step 1 - Commence by accessing various Quick Links
  </p>
 </div>
 <div class="flex flex-col space-y-4 md:flex-row md:space-x-4 md:space-y-0">
  <!-- Card 1 -->
  <div class="flex flex-col items-center justify-center p-4 bg-gray-100 border border-gray-300 rounded-lg w-full md:w-1/2 min-h-[200px]">
   <img src="/icons/quicklink1.svg" alt="quick link">
   <div class="text-center my-5 font-sf">
    <p class="text-gray-800 font-semibold mb-2">Take a Tour of the Website</p>
    <p>This site is big!! Kingsley has created everything here from scratch. You need to know where things are</p>
  
   </div>
   <a href="/member/roadmap" class="px-5 py-2 bg-[#404348] text-white text-sm rounded-full hover:bg-yellow-400 hover:text-black transition inline-flex items-center">
    View Details <i class="fa fa-angle-right ml-2" aria-hidden="true"></i>
  </a>
  </div>

  <!-- Card 2 -->
  <div class="flex flex-col items-center justify-center p-4 bg-gray-100 border border-gray-300 rounded-lg w-full md:w-1/2 min-h-[200px]">
   <img src="/icons/piano.svg" alt="quick link">
   <div class="text-center my-5 font-sf">
    <p class="text-gray-800 font-semibold mb-2">Structure a good Practice Session</p>
    <p>Step by step breakdown of learning methods. Every lesson follows a clear and practical approach that deepens your understanding</p>
  
   </div>
   <a href="/member/roadmap" class="px-5 py-2 bg-[#404348] text-white text-sm rounded-full hover:bg-yellow-400 hover:text-black transition inline-flex items-center">
    View Details <i class="fa fa-angle-right ml-2" aria-hidden="true"></i>
  </a>
  </div>

</div>
<div class="flex flex-col space-y-4 md:flex-row md:space-x-4 md:space-y-0 my-5">
 <!-- Card 1 -->
 <div class="flex flex-col items-center justify-center p-4 bg-gray-100 border border-gray-300 rounded-lg w-full md:w-1/2 min-h-[200px]">
  <img src="/icons/quicklink2.svg" alt="quick link">
  <div class="text-center my-5 font-sf">
   <p class="text-gray-800 font-semibold mb-2">How I Arrange My Set-up</p>
   <p>A system that gives you access to tools, resources and mastermind meetings. To give you a seamless learning experience</p>
 
  </div>
  <a href="/member/roadmap" class="px-5 py-2 bg-[#404348] text-white text-sm rounded-full hover:bg-yellow-400 hover:text-black transition inline-flex items-center">
   View Details <i class="fa fa-angle-right ml-2" aria-hidden="true"></i>
 </a>
 </div>

 <!-- Card 2 -->
 <div class="flex flex-col items-center justify-center p-4 bg-gray-100 border border-gray-300 rounded-lg w-full md:w-1/2 min-h-[200px]">
  <img src="/icons/trophy.png" alt="quick link">
  <div class="text-center my-5 font-sf">
   <p class="text-gray-800 font-semibold mb-2">How To Gain Quality Experience</p>
   <p>Get exposed to the right opportunities, stay consistent in your learning, and transform your knowledge into life-changing results</p>
 
  </div>
  <a href="/member/roadmap" class="px-5 py-2 bg-[#404348] text-white text-sm rounded-full hover:bg-yellow-400 hover:text-black transition inline-flex items-center">
   View Details <i class="fa fa-angle-right ml-2" aria-hidden="true"></i>
 </a>
 </div>

</div>
</section>

<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white pt-10 pb-5 px-12">
 <div class="font-bold mb-5 text-[22px]">
  <p>
   Step 2 - Find your Best Path & Choose your Skill Level
  </p>
 </div>
 <div class="flex flex-col space-y-4 md:flex-row md:space-x-4 md:space-y-0 items-center justify-center">
  <!-- Card 1 -->
 <div id="getStartedQuiz">

 </div>

  <!-- Center "OR" -->
  <div class="flex items-center justify-center h-full">
    <p class="text-gray-500 font-semibold mx-4">OR</p>
  </div>

  <!-- Card 2 -->
<div id="free-call"></div>
<!-- Calendly inline widget end -->
</div>
</section>

<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white pt-10 pb-5 px-12">
 <div class="font-bold mb-5 text-[22px]">
  <p>
   Step 3 - Start a Course Base on your Skill Level
  </p>
 </div>
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
     <a href="/member/roadmap" class="px-5 py-2 bg-[#404348] text-white text-sm rounded-full hover:bg-yellow-400 hover:text-black transition inline-flex items-center">
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
     <a href="/member/roadmap" class="px-5 py-2 bg-[#404348] text-white text-sm rounded-full hover:bg-yellow-400 hover:text-black transition inline-flex items-center">
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
     <a href="/member/roadmap" class="px-5 py-2 bg-[#404348] text-white text-sm rounded-full hover:bg-yellow-400 hover:text-black transition inline-flex items-center">
      Enroll Now <i class="fa fa-angle-right ml-2" aria-hidden="true"></i>
    </a>
    </div>
  </div>
</div>
</section>
<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white pt-10 pb-5 px-12 ">
 <div class="font-bold mb-5 text-[22px] text-left w-9/12">
  <p>
    Step 4 - Enhance your vocabulary by Learning these songs 
  </p>
  <p class="text-[16px] text-[#5E6779] font-sf mt-2">
    Mastering and memorizing songs is the fastest method to advance rapidly on the piano! You can visit the Song Section of the website to sort by difficulty level or utilize the Search Bar to locate a song you’d like to learn.
  </p>
</div>

<div class="border border-gray-300 p-5 rounded-lg">
 <div class="flex space-x-4 mb-4 justify-center">
   <button class="px-12 py-2 text-lg text-gray-700 bg-gray-300 rounded-full w-[300px] hover:bg-gray-400 focus:bg-gray-500 active:bg-gray-300" data-tab="beginners" onclick="changeTab('beginners')">
     <p class="text-[#435065] font-sf font-bold">Beginners</p>
   </button>
   <button class="px-12 py-2 text-lg text-gray-700 bg-transparent rounded-full w-[300px] hover:bg-gray-400 hover:text-white focus:bg-gray-300 focus:text-white" data-tab="intermediate" onclick="changeTab('intermediate')">
     <p class="text-[#435065] font-sf font-bold">Intermediate</p>
   </button>
   <button class="px-12 py-2 text-lg text-gray-700 bg-transparent rounded-full w-[300px] hover:bg-gray-400 hover:text-white focus:bg-gray-300 focus:text-white" data-tab="advanced" onclick="changeTab('advanced')">
     <p class="text-[#435065] font-sf font-bold">Advanced</p>
   </button>
 </div>

 <!-- Content Area -->
 <div class="p-4  rounded-lg">
   <div class="tab-content hidden" data-content="beginners">
     <ul class="list-disc pl-6 text-gray-700">
       <li>Start with the basics of piano keys</li>
       <li>Learn how to read musical notes</li>
       <li>Practice simple songs for beginners</li>
       <li>Get familiar with basic music theory concepts</li>
     </ul>
   </div>
   <div class="tab-content hidden" data-content="intermediate">
     <ul class="list-disc pl-6 text-gray-700">
       <li>Start with the basics of piano keys</li>
       <li>Learn how to read musical notes</li>
       <li>Practice simple songs for beginners</li>
       <li>Get familiar with basic music theory concepts</li>
     </ul>
   </div>
   <div class="tab-content hidden" data-content="advanced">
     <ul class="list-disc pl-6 text-gray-700">
       <li>Start with the basics of piano keys</li>
       <li>Learn how to read musical notes</li>
       <li>Practice simple songs for beginners</li>
       <li>Get familiar with basic music theory concepts</li>
     </ul>
   </div>
 </div>
</div>
</section><section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white pt-10 pb-5 px-12">
 <div class="font-bold mb-5 text-[22px] text-left w-9/12">
   <p>Step 5 - Participate in the Community</p>
   <p class="text-[16px] text-[#5E6779] font-sf mt-2">
     Introduce yourself, ask a question, post your progress, see member challenges and engage with other members
   </p>
 </div>

 <!-- Community Area -->
 <div class="bg-gray-200 rounded-lg p-4 w-full  mx-auto flex justify-between items-center">
   <!-- Left side: Icon and text -->
   <div class="flex items-center space-x-4">
    <img src="/icons/community.png" alt="community">
    <div class="flex flex-col text-left">
      <span class="text-gray-700 text-lg font-bold font-sf">Community</span>
      <span class="text-gray-700 text-[16px] font-sf">View the latest community activities</span>
    </div>
  </div>
  

   <!-- Right side: Forward Icon -->
   <i class="fa fa-angle-right ml-2" aria-hidden="true"></i>
 </div>
</section>


@endsection