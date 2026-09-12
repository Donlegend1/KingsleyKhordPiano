<section class="relative bg-black overflow-hidden py-24 px-4">

    {{-- Subtle ambient glow — restrained, no busy decoration --}}
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[900px] h-[500px] rounded-full opacity-[0.10] blur-[140px] pointer-events-none"
         style="background: radial-gradient(circle, #FFD736 0%, transparent 70%);"></div>

    {{-- ── Section Heading ── --}}
    <div class="text-center mb-16 relative z-10">
        <p class="inline-block relative text-white text-[13px] font-semibold tracking-[0.24em] uppercase mb-6">
            Student Stories
            <svg class="absolute left-0 -bottom-2 w-full" height="6" viewBox="0 0 160 6" preserveAspectRatio="none" fill="none" aria-hidden="true">
                <path d="M1 4.5C30 1.5 90 1 159 4" stroke="#FFD736" stroke-width="2.4" stroke-linecap="round"/>
            </svg>
        </p>
        <h2 class="font-playfair italic font-medium text-4xl md:text-5xl lg:text-[3.25rem] text-white leading-tight">
            A Glimpse Into the Student Journey
        </h2>
        <p class="text-gray-400 mt-5 text-base md:text-lg">
            Real stories. Real growth. Real results.
        </p>
    </div>

    {{-- ── 2×2 Card Grid ── --}}
    <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">

        {{-- Card 1 — Danacky Miak --}}
        <div class="relative bg-white/[0.035] rounded-2xl p-8 border border-white/10 flex flex-col gap-5 hover:border-[#FFD736]/30 transition-colors duration-300">
            <div class="flex items-start justify-between">
                <span class="font-playfair text-[#FFD736] text-6xl leading-none select-none">&ldquo;</span>
                <div class="flex gap-0.5 mt-2">
                    @for($i=0;$i<5;$i++)
                    <svg class="w-4 h-4 text-[#FFD736]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
            </div>
            <h3 class="font-playfair italic text-xl text-white leading-snug -mt-3">
                Kingsley's Classes Transformed my Piano Game
            </h3>
            <p class="text-gray-400 text-sm leading-relaxed flex-1">
                Kingsley's classes transformed my game. I quickly reached new levels after being stuck for a while. His courses are precise, direct, simple, and methodical, perfect for those with limited time. You learn a lot in a short period. Besides his excellent teaching, I was impressed by his availability and patience.
            </p>
            <div class="flex items-center gap-3 pt-5 border-t border-white/10">
                <img src="/logo/testimonial.png" alt="Danacky Miak" class="w-10 h-10 rounded-full object-cover flex-shrink-0 ring-1 ring-white/10">
                <div>
                    <span class="block font-semibold text-white text-sm">Danacky Miak</span>
                    <span class="block text-gray-500 text-xs tracking-wide">Student</span>
                </div>
            </div>
        </div>

        {{-- Card 2 — Josien Kuipers --}}
        <div class="relative bg-white/[0.035] rounded-2xl p-8 border border-white/10 flex flex-col gap-5 hover:border-[#FFD736]/30 transition-colors duration-300">
            <div class="flex items-start justify-between">
                <span class="font-playfair text-[#FFD736] text-6xl leading-none select-none">&ldquo;</span>
                <div class="flex gap-0.5 mt-2">
                    @for($i=0;$i<5;$i++)
                    <svg class="w-4 h-4 text-[#FFD736]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
            </div>
            <h3 class="font-playfair italic text-xl text-white leading-snug -mt-3">
                He has a Clear and Solid Curriculum
            </h3>
            <p class="text-gray-400 text-sm leading-relaxed flex-1">
                When I met Kingsley Khord, his beginner piano classes on WhatsApp and YouTube transformed my musical understanding. His clear teaching and solid curriculum helped me move from basic guitar to piano chords, keys, and scales. Kingsley's personalized approach and deep music knowledge encouraged my steady growth.
            </p>
            <div class="flex items-center gap-3 pt-5 border-t border-white/10">
                <img src="/images/france.png" alt="Josien Kuipers" class="w-10 h-10 rounded-full object-cover flex-shrink-0 ring-1 ring-white/10">
                <div>
                    <span class="block font-semibold text-white text-sm">Josien Kuipers</span>
                    <span class="block text-gray-500 text-xs tracking-wide">Student</span>
                </div>
            </div>
        </div>

        {{-- Card 3 — Joseph Joseph --}}
        <div class="relative bg-white/[0.035] rounded-2xl p-8 border border-white/10 flex flex-col gap-5 hover:border-[#FFD736]/30 transition-colors duration-300">
            <div class="flex items-start justify-between">
                <span class="font-playfair text-[#FFD736] text-6xl leading-none select-none">&ldquo;</span>
                <div class="flex gap-0.5 mt-2">
                    @for($i=0;$i<5;$i++)
                    <svg class="w-4 h-4 text-[#FFD736]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
            </div>
            <h3 class="font-playfair italic text-xl text-white leading-snug -mt-3">
                Everything Changed When I Started Learning
            </h3>
            <p class="text-gray-400 text-sm leading-relaxed flex-1">
                For years, I struggled with the piano, feeling frustrated and stuck. Everything changed when I started learning from Kingsley Khords. With his expert guidance and patient teaching, my piano skills have greatly improved. His unique approach and ability to simplify complex concepts have been invaluable.
            </p>
            <div class="flex items-center gap-3 pt-5 border-t border-white/10">
                <img src="/images/nigeria.png" alt="Joseph Joseph" class="w-10 h-10 rounded-full object-cover flex-shrink-0 ring-1 ring-white/10">
                <div>
                    <span class="block font-semibold text-white text-sm">Joseph Joseph</span>
                    <span class="block text-gray-500 text-xs tracking-wide">Student</span>
                </div>
            </div>
        </div>

        {{-- Card 4 — Dionysius Harmon --}}
        <div class="relative bg-white/[0.035] rounded-2xl p-8 border border-white/10 flex flex-col gap-5 hover:border-[#FFD736]/30 transition-colors duration-300">
            <div class="flex items-start justify-between">
                <span class="font-playfair text-[#FFD736] text-6xl leading-none select-none">&ldquo;</span>
                <div class="flex gap-0.5 mt-2">
                    @for($i=0;$i<5;$i++)
                    <svg class="w-4 h-4 text-[#FFD736]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
            </div>
            <h3 class="font-playfair italic text-xl text-white leading-snug -mt-3">
                Your Teachings Improved My Playing
            </h3>
            <p class="text-gray-400 text-sm leading-relaxed flex-1">
                It was truly a blessing meeting Kingsley. His teachings have improved my playing and my overall knowledge of what I'm actually playing, dramatically. Thank you so much for your time and patience. Truly grateful. May God continue to increase your wisdom.
            </p>
            <div class="flex items-center gap-3 pt-5 border-t border-white/10">
                <img src="/images/fran.png" alt="Dionysius Harmon" class="w-10 h-10 rounded-full object-cover flex-shrink-0 ring-1 ring-white/10">
                <div>
                    <span class="block font-semibold text-white text-sm">Dionysius Harmon</span>
                    <span class="block text-gray-500 text-xs tracking-wide">Student</span>
                </div>
            </div>
        </div>

    </div>

</section>
