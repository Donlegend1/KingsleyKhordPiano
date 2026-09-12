<section class="bg-black text-white -mt-[72px]">

  {{-- ===== MOBILE LAYOUT (hidden md+) ===== --}}
  <div class="md:hidden relative overflow-hidden">

    {{-- Image: absolute so its height never affects section height --}}
    <div class="absolute inset-x-0" style="top: 48px; height: 95vh; z-index: 0;">
      <img
        src="/images/first-p.png"
        alt="Kingsley Khord at the piano"
        class="w-full h-full object-cover"
        style="object-position: 65% 0%; filter: contrast(1.08) saturate(1.1); image-rendering: -webkit-optimize-contrast;"
      >
      {{-- Gradient fade into black --}}
      <div class="absolute inset-x-0 bottom-0" style="height: 65%; background: linear-gradient(to top, #000000 0%, #000000 25%, rgba(0,0,0,0.75) 60%, rgba(0,0,0,0) 100%);"></div>
    </div>

    {{-- Content: sits in flow, starts below the visible image area --}}
    <div class="relative px-6 pb-14 bg-transparent" style="padding-top: 54vh; z-index: 10;">

      <p class="inline-block relative text-white text-[11px] font-semibold tracking-[0.22em] uppercase mb-6">
        Master The Piano
        <svg class="absolute left-0 -bottom-1.5 w-full" height="5" viewBox="0 0 120 5" preserveAspectRatio="none" fill="none" aria-hidden="true">
          <path d="M1 3.5C22 1.2 68 0.8 119 3.2" stroke="#FFD736" stroke-width="2" stroke-linecap="round"/>
        </svg>
      </p>

      <h1 class="font-playfair italic font-medium leading-[1.08] mb-0 text-white">
        <span class="block whitespace-nowrap" style="font-size: 9.5vw;">Play Piano.</span>
        <span class="block text-[#FFD736] whitespace-nowrap" style="font-size: 9.5vw;">Play With Purpose.</span>
      </h1>

      <p class="text-[1.05rem] text-gray-300 mt-6 mb-8 leading-relaxed">
        Start wherever you are — we'll guide you one note at a time, until playing feels like second nature.
      </p>

      <a href="/plans#pricing"
         class="flex items-center justify-center gap-2 text-black text-sm font-semibold px-7 py-4 rounded-full w-full transition shadow-[0_10px_30px_-8px_rgba(255,215,54,0.55)]"
         style="background: linear-gradient(100deg, #FFE07A 0%, #FFD736 45%, #F2A93C 100%);">
        Start Your Journey
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
        </svg>
      </a>

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
      <div class="px-16 lg:px-20 xl:px-28" style="max-width: 58%;">

        <p class="inline-block relative text-white text-[13px] font-semibold tracking-[0.24em] uppercase mb-7">
          Master The Piano
          <svg class="absolute left-0 -bottom-2 w-full" height="6" viewBox="0 0 160 6" preserveAspectRatio="none" fill="none" aria-hidden="true">
            <path d="M1 4.5C30 1.5 90 1 159 4" stroke="#FFD736" stroke-width="2.4" stroke-linecap="round"/>
          </svg>
        </p>

        <h1 class="font-playfair italic font-medium leading-[1.08] mb-0 text-white">
          <span class="block text-5xl lg:text-[4.25rem] xl:text-[5rem]">Play Piano.</span>
          <span class="block text-5xl lg:text-[4.25rem] xl:text-[5rem] text-[#FFD736]">Play With Purpose.</span>
        </h1>

        <p class="text-base lg:text-lg text-gray-300 mt-8 mb-10 leading-relaxed max-w-md">
          Start wherever you are — we'll guide you one note at a time, until playing feels like second nature.
        </p>

        <a href="/plans#pricing"
           class="inline-flex items-center gap-2 text-black text-sm font-semibold px-8 py-4 rounded-full transition hover:brightness-105 hover:-translate-y-0.5 duration-200 shadow-[0_10px_30px_-8px_rgba(255,215,54,0.55)]"
           style="background: linear-gradient(100deg, #FFE07A 0%, #FFD736 45%, #F2A93C 100%);">
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