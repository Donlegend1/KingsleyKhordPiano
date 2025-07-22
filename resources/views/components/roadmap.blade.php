<div class="container mx-auto px-4 py-10">
  <!-- Header Text -->
  <div class="text-center mb-10">
    <p class="text-black font-bold text-2xl sm:text-3xl lg:text-4xl">
      The Pros Don’t Just Play
    </p>
    <p class="mt-4 text-base sm:text-lg mx-auto max-w-xl">
      They understand the fundamentals of music well enough to teach others to follow in their footsteps.​
    </p>
  </div>

  <!-- Feature Grid -->
@php
  $features = [
    ['image' => 'musictheory', 'title' => 'Music Theory'],
    ['image' => 'roadmap', 'title' => 'Road Map'],
    ['image' => 'eartraining', 'title' => 'Ear Training'],
    ['image' => 'pianoexercise', 'title' => 'Piano Exercise'],
  ];
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
  @foreach ($features as $feature)
    <div class="relative rounded-xl overflow-hidden shadow-lg group">
      <!-- Background Image -->
      <div 
        class="absolute inset-0 bg-cover bg-center" 
        style="background-image: url('/images/{{ $feature['image'] }}.png');">
      </div>

      <!-- Overlay -->
      <div class="absolute inset-0 bg-black/60 transition-opacity group-hover:bg-black/70"></div>

      <!-- Content -->
      <div class="relative z-10 p-6 text-white text-center flex flex-col h-full justify-between">
        <div>
          <div class="flex justify-center mb-4">
            <img src="/icons/lessonwhite.png" alt="{{ $feature['title'] }} Icon" class="h-10 w-10">
          </div>
          <h3 class="text-lg font-semibold mb-2">{{ $feature['title'] }}</h3>
          <p class="text-sm text-gray-300">
            Enhance your piano skills effortlessly. Whether you're starting out or experienced, our lessons guide you through musical excellence at your own pace.
          </p>
        </div>
      </div>
    </div>
  @endforeach
</div>




  <!-- Academy Welcome Section -->
  <div class="flex flex-col lg:flex-row justify-center items-center gap-8 mt-16 px-4">
    <!-- Logo -->
    <div class="w-full lg:w-5/12 text-center lg:text-left">
      <img src="/logo/logoblack.png" alt="logo" class="w-[80%] mx-auto lg:mx-0 h-auto">
    </div>

    <!-- Text Content -->
    <div class="w-full lg:w-7/12">
      <p class="font-bold text-2xl sm:text-3xl lg:text-[38px] leading-snug">
        Welcome to KingsleyKhord Music Academy
      </p>
      <p class="font-medium mt-4 mb-6 text-base sm:text-lg">
        Here, the fundamental concepts of the piano are broken down into digestible, easy-to-follow lessons.
      </p>
      <ul class="list-disc pl-5 space-y-2 text-base">
        <li>Roadmap for all skill levels​</li>
        <li>Premium midi files​</li>
        <li>Ear Training Quiz​</li>
        <li>Zoom Live Lessons​</li>
        <li>Supportive Community​</li>
      </ul>
    </div>
  </div>

  <!-- CTA Button -->
  <div class="flex justify-center mt-12">
    <a href="/plans" class="flex items-center bg-[#FFD736] uppercase px-6 py-3 hover:bg-[#c2ab39] text-black rounded-md font-semibold text-base sm:text-lg transition">
      All Membership Features
      <i class="fa fa-angle-right ml-2" aria-hidden="true"></i>
    </a>
  </div>
</div>
