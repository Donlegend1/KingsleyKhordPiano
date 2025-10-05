@extends('layouts.member')

@section('content')


<section class="bg-white md:mx-auto md:max-w-6xl dark:bg-gray-900 text-gray-900 dark:text-white py-6 px-4 ">
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

<section x-data="{ video: null }" class="bg-white md:mx-auto md:max-w-6xl dark:bg-gray-900 text-gray-900 dark:text-white py-2 px-5 md:px-12">
  <div class="font-bold mb-5 text-[22px]">
    <p>Step 1 - Commence by accessing various Quick Links</p>
  </div>

  <!-- First Row -->
  <div class="flex flex-col space-y-4 md:flex-row md:space-x-4 md:space-y-0">
    <!-- Card 1 -->
    <div class="flex flex-col items-center justify-center p-4 bg-gray-100 border border-gray-300 rounded-lg w-full md:w-1/2 min-h-[200px]">
      <img src="/icons/quicklink1.svg" alt="quick link">
      <div class="text-center my-5 font-sf">
        <p class="text-gray-800 font-semibold mb-2">Take a Tour of the Website</p>
        <p>This site is big!! Kingsley has created everything here from scratch. You need to know where things are</p>
      </div>
      <!-- Use json_encode and single quotes for safe embedding -->
      <button
        @click='video = {{ json_encode($tour ? "https://drive.google.com/file/d/{$tour}/preview" : "") }}'
        class="px-5 py-2 bg-[#404348] text-white text-sm rounded-full hover:bg-yellow-400 hover:text-black transition inline-flex items-center"
      >
        Watch Video <i class="fa fa-play ml-2" aria-hidden="true"></i>
      </button>
    </div>

    <!-- Card 2 -->
    <div class="flex flex-col items-center justify-center p-4 bg-gray-100 border border-gray-300 rounded-lg w-full md:w-1/2 min-h-[200px]">
      <img src="/icons/piano.svg" alt="quick link">
      <div class="text-center my-5 font-sf">
        <p class="text-gray-800 font-semibold mb-2">Structure a good Practice Session</p>
        <p>Step by step breakdown of learning methods...</p>
      </div>
      <button
        @click='video = {{ json_encode($session ? "https://drive.google.com/file/d/{$session}/preview" : "") }}'
        class="px-5 py-2 bg-[#404348] text-white text-sm rounded-full hover:bg-yellow-400 hover:text-black transition inline-flex items-center"
      >
        Watch Video <i class="fa fa-play ml-2" aria-hidden="true"></i>
      </button>
    </div>
  </div>

  <!-- Second Row -->
  <div class="flex flex-col space-y-4 md:flex-row md:space-x-4 md:space-y-0 my-5">
    <!-- Card 3 -->
    <div class="flex flex-col items-center justify-center p-4 bg-gray-100 border border-gray-300 rounded-lg w-full md:w-1/2 min-h-[200px]">
      <img src="/icons/quicklink2.svg" alt="quick link">
      <div class="text-center my-5 font-sf">
        <p class="text-gray-800 font-semibold mb-2">How I Arrange My Set-up</p>
        <p>A system that gives you access to tools and mastermind meetings...</p>
      </div>
      <button
        @click='video = {{ json_encode($setUp ? "https://drive.google.com/file/d/{$setUp}/preview" : "") }}'
        class="px-5 py-2 bg-[#404348] text-white text-sm rounded-full hover:bg-yellow-400 hover:text-black transition inline-flex items-center"
      >
        Watch Video <i class="fa fa-play ml-2" aria-hidden="true"></i>
      </button>
    </div>

    <!-- Card 4 -->
    <div class="flex flex-col items-center justify-center p-4 bg-gray-100 border border-gray-300 rounded-lg w-full md:w-1/2 min-h-[200px]">
      <img src="/icons/trophy.png" alt="quick link">
      <div class="text-center my-5 font-sf">
        <p class="text-gray-800 font-semibold mb-2">How To Gain Quality Experience</p>
        <p>Get exposed to the right opportunities and transform your knowledge...</p>
      </div>
      <button
        @click='video = {{ json_encode($exper ? "https://drive.google.com/file/d/{$exper}/preview" : "") }}'
        class="px-5 py-2 bg-[#404348] text-white text-sm rounded-full hover:bg-yellow-400 hover:text-black transition inline-flex items-center"
      >
        Watch Video <i class="fa fa-play ml-2" aria-hidden="true"></i>
      </button>
    </div>
  </div>

  <!-- Modal / Overlay -->
  <div 
    x-show="video !== null" 
    x-cloak 
    x-transition.opacity
    @click.self="video = null"
    class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50"
  >
    <div class="relative bg-white rounded-lg overflow-hidden shadow-lg max-w-3xl w-full">

      <!-- Close Button -->
      <button 
        @click="video = null" 
        aria-label="Close video"
        class="absolute -top-3 -right-3 bg-white text-black border border-gray-300 
               hover:bg-red-600 hover:text-white rounded-full w-9 h-9 flex items-center 
               justify-center shadow-lg z-60"
      >
        &times;
      </button>

      <div class="aspect-w-16 aspect-h-9 flex items-center justify-center bg-black">
        <!-- iframe shown only when video is non-empty string -->
        <div x-show="video !== '' && video !== null" class="w-full h-[500px]">
          <iframe 
            x-bind:src="video" 
            class="w-full h-full" 
            frameborder="0" 
            allow="autoplay; encrypted-media" 
            allowfullscreen
          ></iframe>
        </div>

        <!-- Coming soon when video is empty string -->
        <div x-show="video === ''" class="px-6 py-4">
          <p class="text-2xl font-bold text-white animate-pulse">
            Coming Soon
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="bg-white md:mx-auto md:max-w-6xl dark:bg-gray-900 text-gray-900 dark:text-white pt-10 pb-5 px-5 md:px-12">
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

  <section class="bg-white md:mx-auto md:max-w-6xl dark:bg-gray-900 text-gray-900 dark:text-white pt-10 pb-5 px-5 md:px-12">
    <div class="font-bold mb-5 text-[22px]">
      <p>
      Step 3 - Start a Course Base on your Skill Level
      </p>
    </div>
    <div class="flex flex-wrap md:flex-nowrap justify-center gap-6 lg:gap-4">
    <!-- Card Template -->
    <div class="w-full md:w-1/3 max-w-sm rounded overflow-hidden shadow-lg bg-[#F3F5F6]">
      <a href="/member/course/beginner">
      <div class="px-4 pt-4">
        <img class="w-full object-cover h-48 rounded-md" src="/images/featured1.jpeg" alt="Course Image 1">
      </div>
      </a>
      <div class="px-4 py-3">
        <p class="text-[18px] font-semibold text-black text-center">Getting Started and Mastering the Fundamentals</p>
        <div class="pt-4 flex justify-center">
          <a href="/member/course/beginner" class="px-6 py-3 bg-[#404348] text-white text-base font-medium hover:bg-yellow-400 hover:text-black transition inline-flex items-center rounded-md">
            Start Course <i class="fa fa-angle-right ml-2" aria-hidden="true"></i>
          </a>
        </div>
      </div>
    </div>

    <!-- Card 2 -->
    <div class="w-full md:w-1/3 max-w-sm rounded overflow-hidden shadow-lg bg-[#F3F5F6]">
      <a href="/member/course/intermediate" >
      <div class="px-4 pt-4">
        <img class="w-full object-cover h-48 rounded-md" src="/images/featured2.jpeg" alt="Course Image 2">
      </div>
      </a>
      <div class="px-4 py-3">
        <p class="text-[18px] font-semibold text-black text-center">Improve the quality of your Chords and Learn to Harmonize them</p>
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
        <p class="text-[18px] font-semibold text-black text-center">Become a Musical Genius – Learn to Think of Music Abstractly</p>
        <div class="pt-4 flex justify-center">
          <a href="/member/course/advanced" class="px-6 py-3 bg-[#404348] text-white text-base font-medium hover:bg-yellow-400 hover:text-black transition inline-flex items-center rounded-md">
            Start Course <i class="fa fa-angle-right ml-2" aria-hidden="true"></i>
          </a>
        </div>
      </div>
    </div>
   </div>
  </section>
  <section class="bg-white md:mx-auto md:max-w-6xl dark:bg-gray-900 text-gray-900 dark:text-white pt-8 pb-5 px-4 sm:px-8 md:px-12">
    <div class="font-bold mb-5 text-lg sm:text-xl md:text-[22px] text-left w-full md:w-9/12">
      <p>
        Step 4 - Enhance your vocabulary by Learning these songs
      </p>
      <p class="text-sm sm:text-base text-[#5E6779] font-sf mt-2 leading-relaxed">
        Mastering and memorizing songs is the fastest method to advance rapidly on the piano! You can visit the Song Section of the website to sort by difficulty level or utilize the Search Bar to locate a song you’d like to learn.
      </p>
    </div>

    <div class="border border-gray-300 p-4 sm:p-5 rounded-lg">
      <div class="flex flex-col sm:flex-row sm:space-x-4 mb-4 space-y-3 sm:space-y-0 justify-center">
        <button class="px-6 sm:px-8 py-2 text-base sm:text-lg text-gray-700 bg-gray-300 rounded-full w-full sm:w-[220px] hover:bg-gray-400 focus:bg-gray-500 active:bg-gray-300" data-tab="beginners" onclick="changeTab('beginners')">
          <p class="text-black font-sf font-bold">Beginners</p>
        </button>
        <button class="px-6 sm:px-8 py-2 text-base sm:text-lg text-gray-700 bg-transparent rounded-full w-full sm:w-[220px] hover:bg-gray-400 hover:text-white focus:bg-gray-300 focus:text-white" data-tab="intermediate" onclick="changeTab('intermediate')">
          <p class="text-black font-sf font-bold">Intermediate</p>
        </button>
        <button class="px-6 sm:px-8 py-2 text-base sm:text-lg text-gray-700 bg-transparent rounded-full w-full sm:w-[220px] hover:bg-gray-400 hover:text-white focus:bg-gray-300 focus:text-white" data-tab="advanced" onclick="changeTab('advanced')">
          <p class="text-black font-sf font-bold">Advanced</p>
        </button>
      </div>

      <div class="p-3 sm:p-4 rounded-lg">
        <div class="tab-content hidden" data-content="beginners">
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            @foreach($beginnerCourses as $course)
              <div class="bg-white rounded-lg shadow p-4 flex flex-col items-center">
                <img src="{{ $course->thumbnail_url ?? '/images/featured1.jpeg' }}" alt="{{ $course->title }}" class="w-full h-32 object-cover rounded-md mb-2">
                <h4 class="font-bold text-gray-800 mb-2 text-center">{{ $course->title }}</h4>
                <a href="/member/course/beginner" class="text-blue-600 hover:underline">View Course</a>
              </div>
            @endforeach
          </div>
        </div>

        <div class="tab-content hidden" data-content="intermediate">
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            @foreach($intermediateCourses as $course)
              <div class="bg-white rounded-lg shadow p-4 flex flex-col items-center">
                <img src="{{ $course->thumbnail_url ?? '/images/featured2.jpeg' }}" alt="{{ $course->title }}" class="w-full h-32 object-cover rounded-md mb-2">
                <h4 class="font-bold text-gray-800 mb-2 text-center">{{ $course->title }}</h4>
                <a href="/member/course/intermediate" class="text-blue-600 hover:underline">View Course</a>
              </div>
            @endforeach
          </div>
        </div>

        <div class="tab-content hidden" data-content="advanced">
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            @foreach($advancedCourses as $course)
              <div class="bg-white rounded-lg shadow p-4 flex flex-col items-center">
                <img src="{{ $course->thumbnail_url ?? '/images/featured3.jpeg' }}" alt="{{ $course->title }}" class="w-full h-32 object-cover rounded-md mb-2">
                <h4 class="font-bold text-gray-800 mb-2 text-center">{{ $course->title }}</h4>
                <a href="/member/course/advanced" class="text-blue-600 hover:underline">View Course</a>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="bg-white md:mx-auto md:max-w-6xl dark:bg-gray-900 text-gray-900 dark:text-white pt-8 pb-5 px-4 sm:px-8 md:px-12">
    <div class="font-bold mb-5 text-lg sm:text-xl md:text-[22px] text-left w-full md:w-9/12">
    
    <p>Step 5 - Participate in the Community</p>
    <p class="text-sm sm:text-base text-[#5E6779] font-sf mt-2 leading-relaxed">
        Introduce yourself, ask a question, post your progress, see member challenges and engage with other members
    </p>
  </div>

  <!-- Community Area -->
  <div class="bg-gray-200 rounded-lg p-4 w-full  mx-auto flex justify-between items-center">
    <!-- Left side: Icon and text -->
    <a href="/member/community">
    <div class="flex items-center space-x-4">
      <img src="/icons/community.png" alt="community">
      <div class="flex flex-col text-left">
        <span class="text-gray-700 text-lg font-bold font-sf">Community</span>
        <span class="text-gray-700 text-[16px] font-sf">View the latest community activities</span>
      </div>
    </div>
    </a>
    

    <!-- Right side: Forward Icon -->
    <i class="fa fa-angle-right ml-2" aria-hidden="true"></i>
  </div>
  </section>

@endsection