<section class="bg-black text-white -mt-[72px]">

  {{-- ===== MOBILE LAYOUT (hidden md+) ===== --}}
  <div class="md:hidden flex flex-col">

    {{-- Image block: top portion, no overlay --}}
    <div class="relative w-full" style="height: 58vh;">
      <div class="absolute top-0 left-0 right-0 h-[72px] z-10"></div>
      <img
        src="/images/first-p.png"
        alt="Kingsley Khord at the piano"
        class="w-full h-full object-cover object-[center_20%]"
      >
    </div>

    {{-- Content block: solid black background --}}
    <div class="bg-black px-6 pt-8 pb-10">

      <p class="text-[#FFD736] text-[10px] font-semibold tracking-[0.22em] uppercase mb-4">
        Master Gospel Piano
      </p>

      <h1 class="font-bold leading-[1.05] mb-0">
        <span class="block text-[2.5rem] text-white">Play Gospel.</span>
        <span class="block text-[2.5rem] text-[#FFD736] italic font-playfair">Play With Purpose.</span>
      </h1>

      <div class="w-10 h-[3px] bg-[#FFD736] mt-5 mb-5"></div>

      <p class="text-sm text-gray-300 mb-8 leading-relaxed">
        Learn gospel piano step-by-step<br>and play with confidence.
      </p>

      <a href="/plans#pricing"
         class="flex items-center justify-center gap-2 bg-[#FFD736] text-black text-sm font-semibold px-7 py-4 rounded-lg w-full hover:bg-[#e6c22e] transition">
        Start Your Journey
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
        </svg>
      </a>

    </div>
  </div>

  {{-- ===== DESKTOP LAYOUT (hidden below md) ===== --}}
  <div class="hidden md:block relative overflow-hidden" style="min-height: 97vh;">

    {{-- Full-bleed background image --}}
    <img
      src="/images/first-p.png"
      alt="Kingsley Khord at the piano"
      class="absolute inset-0 w-full h-full object-cover"
      style="object-position: 62% 0%;"
      aria-hidden="true"
    >

    {{-- Left gradient: solid black → transparent (sharp hold then fade) --}}
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

    {{-- Content overlaid on left --}}
    <div class="relative z-10 flex items-center" style="min-height: 97vh;">
      <div class="px-16 lg:px-20 xl:px-28" style="max-width: 48%;">

        <p class="text-[#FFD736] text-xs font-semibold tracking-[0.22em] uppercase mb-5">
          Master Gospel Piano
        </p>

        <h1 class="font-bold leading-[1.05] mb-0">
          <span class="block text-6xl lg:text-[5.5rem] xl:text-[6rem] text-white">Play Gospel.</span>
          <span class="block text-6xl lg:text-[5.5rem] xl:text-[6rem] text-[#FFD736] italic font-playfair">Play With Purpose.</span>
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

</section>
