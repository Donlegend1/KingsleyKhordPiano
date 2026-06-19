<section class="bg-black text-white -mt-[72px]">

  {{-- ===== MOBILE LAYOUT (hidden md+) ===== --}}
  <div class="md:hidden relative">

    {{-- Image: absolute so its height never affects section height --}}
    <div class="absolute inset-x-0" style="top: 48px; height: 80vh; z-index: 0;">
      <img
        src="/images/first-p.png"
        alt="Kingsley Khord at the piano"
        class="w-full h-full object-cover"
        style="object-position: 65% 0%;"
      >
      {{-- Gradient fade into black --}}
      <div class="absolute inset-x-0 bottom-0" style="height: 65%; background: linear-gradient(to top, #000000 0%, #000000 25%, rgba(0,0,0,0.75) 60%, rgba(0,0,0,0) 100%);"></div>
    </div>

    {{-- Content: sits in flow, starts below the visible image area --}}
    <div class="relative px-6 pb-14 bg-transparent" style="padding-top: 54vh; z-index: 10;">

      <p class="text-[#FFD736] text-[10px] font-semibold tracking-[0.22em] uppercase mb-4">
        Master Gospel Piano
      </p>

      <h1 class="font-bold leading-[1.05] mb-0">
        <span class="block text-white whitespace-nowrap" style="font-size: 9.5vw;">Play Gospel.</span>
        <span class="block text-[#FFD736] italic font-playfair whitespace-nowrap" style="font-size: 9.5vw;">Play With Purpose.</span>
      </h1>

      <div class="w-10 h-[3px] bg-[#FFD736] mt-5 mb-5"></div>

      <p class="text-[1.1rem] text-gray-300 mb-8 leading-relaxed">
        Learn gospel piano step-by-step<br>and play with confidence.
      </p>

      <a href="/plans#pricing"
         class="flex items-center justify-center gap-2 bg-[#FFD736] text-black text-sm font-semibold px-7 py-4 rounded-full w-full hover:bg-[#e6c22e] transition">
        Start Your Journey
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
        </svg>
      </a>

    </div>

    {{-- Mobile wave transition --}}
    <div class="w-full" style="line-height: 0; margin-bottom: -2px;">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 414 60" preserveAspectRatio="none" class="w-full block">
        <path d="M0,0 L414,0 L414,10 C310,50 104,50 0,10 Z" fill="#000000"/>
        <path d="M0,10 C104,50 310,50 414,10 L414,60 L0,60 Z" fill="#FFD736" opacity="0.35"/>
        <path d="M0,20 C104,55 310,55 414,20 L414,60 L0,60 Z" fill="#f0f0ee"/>
      </svg>
    </div>

  </div>

  {{-- ===== DESKTOP LAYOUT (hidden below md) ===== --}}
  <div class="hidden md:flex relative overflow-hidden" style="height: 100vh;">

    {{-- Full-bleed background image --}}
    <img
      src="/images/first-p.png"
      alt="Kingsley Khord at the piano"
      class="absolute w-full object-cover"
      style="top: 72px; left: 0; right: 0; bottom: 0; height: calc(100% - 72px); object-position: 62% 0%;"
      aria-hidden="true"
    >

    {{-- Left gradient: solid black → transparent --}}
    <div class="absolute inset-0"
         style="background: linear-gradient(to right,
           #000000 0%,
           #000000 30%,
           rgba(0,0,0,0.92) 38%,
           rgba(0,0,0,0.60) 50%,
           rgba(0,0,0,0.15) 65%,
           rgba(0,0,0,0) 80%
         );"></div>

    {{-- Bottom fade to black --}}
    <div class="absolute inset-x-0 bottom-0 h-32"
         style="background: linear-gradient(to top, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0) 100%);"></div>

    {{-- Content: vertically centered below the fixed navbar --}}
    <div class="relative z-10 flex flex-col justify-center w-full" style="padding-top: 72px;">
      <div class="px-16 lg:px-20 xl:px-28" style="max-width: 48%;">

        <p class="text-[#FFD736] text-xs font-semibold tracking-[0.22em] uppercase mb-5">
          Master Gospel Piano
        </p>

        <h1 class="font-bold leading-[1.05] mb-0">
          <span class="block text-5xl lg:text-[4.5rem] xl:text-[5rem] text-white">Play Gospel.</span>
          <span class="block text-5xl lg:text-[4.5rem] xl:text-[5rem] text-[#FFD736] italic font-playfair">Play With Purpose.</span>
        </h1>

        <div class="w-12 h-[3px] bg-[#FFD736] mt-6 mb-6"></div>

        <p class="text-base lg:text-lg text-gray-300 mb-10 leading-relaxed">
          Learn gospel piano step-by-step<br>and play with confidence.
        </p>

        <a href="/plans#pricing"
           class="inline-flex items-center gap-2 bg-[#FFD736] text-black text-sm font-semibold px-8 py-4 rounded-md hover:bg-[#e6c22e] transition">
          Start Your Journey
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
          </svg>
        </a>

      </div>
    </div>

  </div>


  {{-- Wave transition to next section --}}
  <div class="w-full" style="line-height: 0; margin-top: -2px;">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 60" preserveAspectRatio="none" class="w-full block">
      {{-- Secondary colour layer (slightly offset) --}}
      <path d="M0,10 C360,50 1080,50 1440,10 L1440,60 L0,60 Z" fill="#FFD736" opacity="0.35"/>
      {{-- Primary wave --}}
      <path d="M0,20 C360,55 1080,55 1440,20 L1440,60 L0,60 Z" fill="#f0f0ee"/>
    </svg>
  </div>

</section>