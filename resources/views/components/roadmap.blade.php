<section class="bg-[#FEF9E7] py-20 px-4">
  <div class="max-w-7xl mx-auto">

    {{-- Header --}}
    <div class="text-center mb-14">
      <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-4">
        The Pros Don't Just Play
      </h2>
      <div class="w-14 h-1 bg-orange-500 mx-auto mb-6 rounded-full"></div>
      <p class="text-gray-500 text-lg max-w-xl mx-auto leading-relaxed">
        They understand the fundamentals of music well enough<br>
        to teach others to follow in their footsteps.
      </p>
    </div>

    {{-- Feature Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

      @php
        $features = [
          ['icon' => 'fa-music', 'title' => 'Music Theory', 'tag' => 'Foundations', 'color' => '#1447A6', 'text' => 'Understand chords, harmony, scales, and the language behind modern gospel piano.'],
          ['icon' => 'fa-map-location-dot', 'title' => 'Road-map', 'tag' => 'Step-by-Step', 'color' => '#C85A5A', 'text' => 'Follow a clear step-by-step path designed to take you from beginner concepts to advanced gospel playing.'],
          ['icon' => 'fa-ear-listen', 'title' => 'Ear Training', 'tag' => 'Practical Drills', 'color' => '#B8860B', 'text' => 'Develop the ability to recognize chords, progressions, and melodies by ear with practical listening exercises.'],
          ['icon' => 'fa-sliders', 'title' => 'Piano Exercise', 'tag' => 'Daily Practice', 'color' => '#1447A6', 'text' => 'Build finger strength, speed, coordination, and accuracy with targeted daily practice routines.'],
        ];
      @endphp

      @foreach ($features as $feature)
        <div class="group bg-[#1447A6] rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 p-8 text-left">
          <div class="w-14 h-14 rounded-xl bg-white/15 flex items-center justify-center mb-6 transition-transform duration-300 group-hover:scale-105">
            <i class="fas {{ $feature['icon'] }} text-white text-xl"></i>
          </div>
          <span class="inline-block text-[11px] font-bold uppercase tracking-wider mb-2 text-white/70">
            {{ $feature['tag'] }}
          </span>
          <h3 class="text-white font-bold text-xl mb-3">{{ $feature['title'] }}</h3>
          <p class="text-white/80 text-base leading-relaxed">
            {{ $feature['text'] }}
          </p>
        </div>
      @endforeach

    </div>

  </div>
</section>

<section class="bg-white py-20 px-4">
  {{-- Academy Welcome Section --}}
  <div class="max-w-7xl mx-auto flex flex-col lg:flex-row justify-center items-center gap-12 lg:gap-16 px-4">
    <div class="w-full lg:w-5/12 flex justify-center lg:justify-start">
      <div class="bg-[#faf8f2] rounded-3xl p-10 sm:p-14 w-full max-w-sm flex items-center justify-center">
        <img src="/logo/logoblack.png" alt="logo" class="w-full h-auto">
      </div>
    </div>
    <div class="w-full lg:w-7/12">
      <span class="inline-block text-[#C85A5A] text-xs font-bold uppercase tracking-widest mb-3">
        About The Academy
      </span>
      <p class="font-bold text-2xl sm:text-3xl lg:text-[38px] leading-snug text-gray-900">
        Welcome to KingsleyKhord Music Academy
      </p>
      <p class="text-gray-500 mt-4 mb-8 text-base sm:text-lg leading-relaxed">
        Here, the fundamental concepts of the piano are broken down into digestible, easy-to-follow lessons.
      </p>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
        @foreach ([
            'Roadmap for all skill levels',
            'Personalized practice plan',
            'Songs and midi transcriptions',
            'Downloadable resources',
            'Monthly live sessions',
            'Supportive community',
        ] as $item)
          <div class="flex items-center gap-3">
            <span class="w-5 h-5 rounded-full bg-[#1447A6]/10 flex items-center justify-center flex-shrink-0">
              <i class="fa fa-check text-[#1447A6] text-[10px]"></i>
            </span>
            <span class="text-gray-700 text-sm sm:text-base">{{ $item }}</span>
          </div>
        @endforeach
      </div>

      {{-- CTA Button --}}
      <div class="mt-10">
        <a href="/plans" class="inline-flex items-center gap-3 bg-[#FFD736] text-black font-semibold text-base px-8 py-4 rounded-full hover:bg-[#e6c22e] transition-all duration-200">
          All Membership Features
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
          </svg>
        </a>
      </div>
    </div>
  </div>
</section>

@include("components.joinow")
