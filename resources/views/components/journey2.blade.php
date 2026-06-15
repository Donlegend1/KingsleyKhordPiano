<section class="relative bg-white overflow-hidden py-20 px-4">

    {{-- ── Decorative blobs ── --}}
    <div class="absolute top-0 left-0 w-48 h-48 bg-indigo-200 rounded-full opacity-40 blur-3xl -translate-x-1/2 -translate-y-1/2 pointer-events-none"></div>
    <div class="absolute top-0 right-0 w-48 h-48 bg-cyan-200 rounded-full opacity-40 blur-3xl translate-x-1/2 -translate-y-1/2 pointer-events-none"></div>

    {{-- Dot grid top-left --}}
    <div class="absolute top-8 left-8 grid grid-cols-5 gap-1.5 opacity-25 pointer-events-none">
        @for($i = 0; $i < 25; $i++)
            <div class="w-1.5 h-1.5 bg-indigo-400 rounded-full"></div>
        @endfor
    </div>

    {{-- Curly line top-right --}}
    <div class="absolute top-6 right-12 opacity-50 pointer-events-none">
        <svg width="80" height="70" viewBox="0 0 80 70" fill="none">
            <path d="M70 10 C60 5, 40 20, 50 35 C60 50, 20 55, 10 65" stroke="#0d9488" stroke-width="2.5" stroke-linecap="round" fill="none"/>
        </svg>
    </div>

    {{-- Green plant bottom-center-left --}}
    <div class="absolute bottom-6 left-[30%] opacity-40 pointer-events-none">
        <svg width="80" height="80" viewBox="0 0 80 80" fill="none">
            <path d="M40 72 C40 52, 18 38, 8 14" stroke="#16a34a" stroke-width="2" stroke-linecap="round"/>
            <ellipse cx="20" cy="30" rx="18" ry="12" fill="#4ade80" opacity="0.8" transform="rotate(-30 20 30)"/>
            <ellipse cx="13" cy="48" rx="14" ry="9"  fill="#86efac" opacity="0.7" transform="rotate(20 13 48)"/>
            <ellipse cx="32" cy="24" rx="13" ry="8"  fill="#4ade80" opacity="0.6" transform="rotate(-50 32 24)"/>
        </svg>
    </div>

    {{-- Music notes bottom-right --}}
    <div class="absolute bottom-8 right-10 opacity-25 pointer-events-none">
        <svg width="80" height="55" viewBox="0 0 80 55" fill="#6366f1">
            <text x="0"  y="24" font-size="22">♩</text>
            <text x="28" y="14" font-size="16">♪</text>
            <text x="52" y="34" font-size="20">♫</text>
        </svg>
    </div>

    {{-- ── Section Heading ── --}}
    <div class="text-center mb-12 relative z-10">
        <h2 class="text-3xl md:text-4xl lg:text-[42px] font-extrabold text-gray-900 leading-tight tracking-tight">
            A Glimpse Into Student's Journey
        </h2>
        <p class="text-gray-500 mt-3 text-base md:text-lg font-medium">
            Real stories. Real growth. Real results.
        </p>
        <div class="mt-4 mx-auto w-16 h-[3px] rounded-full"
             style="background: linear-gradient(90deg, #7c3aed, #ec4899)"></div>
    </div>

    {{-- ── 2×2 Card Grid ── --}}
    <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">

        {{-- Card 1 — Danacky Miak (white) --}}
        <div class="relative bg-white rounded-2xl p-7 shadow-[0_4px_24px_rgba(0,0,0,0.07)] border border-gray-100 flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <div class="w-11 h-11 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                    </svg>
                </div>
                <div class="flex gap-0.5">
                    @for($i=0;$i<5;$i++)
                    <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
            </div>
            <h3 class="text-[16px] font-bold text-gray-900 leading-snug">
                ❝ Kingsley's Classes Transformed my Piano Game! ❞
            </h3>
            <p class="text-gray-500 text-sm leading-relaxed flex-1">
                Kingsley's classes transformed my game. I quickly reached new levels after being stuck for a while. His courses are precise, direct, simple, and methodical, perfect for those with limited time. You learn a lot in a short period. Besides his excellent teaching, I was impressed by his availability and patience.
            </p>
            <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                <img src="/logo/testimonial.png" alt="Flag" class="w-9 h-9 rounded-full object-cover flex-shrink-0">
                <span class="font-bold text-gray-800 text-sm">Danacky Miak</span>
            </div>
        </div>

        {{-- Card 2 — Josien Kuipers (cream) --}}
        <div class="relative bg-amber-50 rounded-2xl p-7 shadow-[0_4px_24px_rgba(0,0,0,0.06)] border border-amber-100 flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <div class="w-11 h-11 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                    </svg>
                </div>
                <div class="flex gap-0.5">
                    @for($i=0;$i<5;$i++)
                    <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
            </div>
            <h3 class="text-[16px] font-bold text-gray-900 leading-snug">
                ❝ He has a Clear and Solid Curriculum ❞
            </h3>
            <p class="text-gray-500 text-sm leading-relaxed flex-1">
                When I met Kingsley Khord, his beginner piano classes on WhatsApp and YouTube transformed my musical understanding. His clear teaching and solid curriculum helped me move from basic guitar to piano chords, keys, and scales. Kingsley's personalized approach and deep music knowledge encouraged my steady growth. He helped me achieve my dream of playing piano, progressing from beginner to intermediate.
            </p>
            <div class="flex items-center gap-3 pt-2 border-t border-amber-100">
                <img src="/images/france.png" alt="Flag" class="w-9 h-9 rounded-full object-cover flex-shrink-0">
                <span class="font-bold text-gray-800 text-sm">Josien Kuipers</span>
            </div>
        </div>

        {{-- Card 3 — Joseph Joseph (mint green) --}}
        <div class="relative bg-green-50 rounded-2xl p-7 shadow-[0_4px_24px_rgba(0,0,0,0.06)] border border-green-100 flex flex-col gap-4 overflow-hidden">
            <div class="flex items-center justify-between">
                <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                    </svg>
                </div>
                <div class="flex gap-0.5">
                    @for($i=0;$i<5;$i++)
                    <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
            </div>
            <h3 class="text-[16px] font-bold text-gray-900 leading-snug">
                ❝ Everything changed when I started learning from Kingsley Khords ❞
            </h3>
            <p class="text-gray-500 text-sm leading-relaxed flex-1">
                For years, I struggled with the piano, feeling frustrated and stuck. Everything changed when I started learning from Kingsley Khords. With his expert guidance and patient teaching, my piano skills have greatly improved. His unique approach and ability to simplify complex concepts have been invaluable. Now, I am feeling confident and inspired.
            </p>
            <div class="flex items-center gap-3 pt-2 border-t border-green-100">
                <img src="/images/nigeria.png" alt="Flag" class="w-9 h-9 rounded-full object-cover flex-shrink-0">
                <span class="font-bold text-gray-800 text-sm">Joseph Joseph</span>
            </div>
            {{-- Leaf decoration --}}
            <div class="absolute bottom-3 right-3 opacity-30 pointer-events-none">
                <svg width="60" height="60" viewBox="0 0 60 60" fill="none">
                    <ellipse cx="32" cy="30" rx="22" ry="14" fill="#4ade80" transform="rotate(-35 32 30)"/>
                    <ellipse cx="20" cy="42" rx="16" ry="10" fill="#86efac" transform="rotate(20 20 42)"/>
                    <path d="M30 52 C30 36, 14 24, 8 8" stroke="#16a34a" stroke-width="1.5" stroke-linecap="round" fill="none"/>
                </svg>
            </div>
        </div>

        {{-- Card 4 — Dionysius Harmon (lavender) --}}
        <div class="relative bg-violet-50 rounded-2xl p-7 shadow-[0_4px_24px_rgba(0,0,0,0.06)] border border-violet-100 flex flex-col gap-4 overflow-hidden">
            <div class="flex items-center justify-between">
                <div class="w-11 h-11 rounded-xl bg-violet-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-violet-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                    </svg>
                </div>
                <div class="flex gap-0.5">
                    @for($i=0;$i<5;$i++)
                    <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
            </div>
            <h3 class="text-[16px] font-bold text-gray-900 leading-snug">
                ❝ Your Teachings have improved my playing and Knowledge ❞
            </h3>
            <p class="text-gray-500 text-sm leading-relaxed flex-1">
                It was truly a blessing meeting Kingsley. His teachings have improved my playing and my overall knowledge of what I'm actually playing, dramatically. Thank you so much for your time and patience bro. Truly grateful. May God continue to increase your wisdom.
            </p>
            <div class="flex items-center gap-3 pt-2 border-t border-violet-100">
                <img src="/images/fran.png" alt="Flag" class="w-9 h-9 rounded-full object-cover flex-shrink-0">
                <span class="font-bold text-gray-800 text-sm">Dionysius Harmon</span>
            </div>
            {{-- Music note decoration --}}
            <div class="absolute bottom-4 right-4 opacity-20 pointer-events-none text-violet-500">
                <svg width="70" height="50" viewBox="0 0 70 50" fill="currentColor">
                    <text x="0"  y="22" font-size="20">♩</text>
                    <text x="24" y="14" font-size="14">♪</text>
                    <text x="44" y="30" font-size="18">♫</text>
                </svg>
            </div>
        </div>

    </div>

</section>
