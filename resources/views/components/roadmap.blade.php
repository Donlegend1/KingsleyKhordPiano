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
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    @foreach (['component2', 'component1', 'component3', 'component4'] as $image)
    <div class="h-80 sm:h-96 bg-cover bg-center rounded-lg relative"
         style="background-image: url('/images/{{ $image }}.png')">
      <div class="absolute bottom-0 left-0 w-full p-3 bg-black bg-opacity-50 text-white opacity-0 hover:opacity-100 transition-opacity duration-300 rounded-b-lg">
        <p class="text-sm sm:text-base">
          @switch($image)
            @case('component2') Replace your nervousness with still-like confidence. @break
            @case('component1') Gain a clear, structured plan of the curriculum, designed to guide you through every skill level. @break
            @case('component3') Learn to expand your musical vocabulary and repertoire. @break
            @case('component4') Hands-on daily piano exercises to strengthen your fingers. @break
          @endswitch
        </p>
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
      <img src="/icons/forward.png" alt="forward" class="ml-3 h-5 w-5 order-last">
    </a>
  </div>
</div>
