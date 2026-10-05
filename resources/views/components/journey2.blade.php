<section class="bg-black py-24 px-4">

    {{-- ── Section Heading ── --}}
    <div class="text-center mb-14">
        <span class="inline-block text-[11px] font-bold uppercase tracking-widest text-[#FFD736] bg-[#FFD736]/10 rounded-full px-3 py-1 mb-4">
            Student Stories
        </span>
        <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-3">
            A Glimpse Into the Student Journey
        </h2>
        <p class="text-gray-400 text-base md:text-lg">
            Real stories. Real growth. Real results.
        </p>
    </div>

    {{-- ── 2×2 Card Grid ── --}}
    <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-5">

        @php
            $testimonials = [
                [
                    'quote' => "Kingsley's Classes Transformed my Piano Game",
                    'body' => "Kingsley's classes transformed my game. I quickly reached new levels after being stuck for a while. His courses are precise, direct, simple, and methodical, perfect for those with limited time. You learn a lot in a short period. Besides his excellent teaching, I was impressed by his availability and patience.",
                    'avatar' => '/logo/testimonial.png',
                    'name' => 'Danacky Miak',
                ],
                [
                    'quote' => 'He has a Clear and Solid Curriculum',
                    'body' => "When I met Kingsley Khord, his beginner piano classes on WhatsApp and YouTube transformed my musical understanding. His clear teaching and solid curriculum helped me move from basic guitar to piano chords, keys, and scales. Kingsley's personalized approach and deep music knowledge encouraged my steady growth.",
                    'avatar' => '/images/france.png',
                    'name' => 'Josien Kuipers',
                ],
                [
                    'quote' => 'Everything Changed When I Started Learning',
                    'body' => 'For years, I struggled with the piano, feeling frustrated and stuck. Everything changed when I started learning from Kingsley Khords. With his expert guidance and patient teaching, my piano skills have greatly improved. His unique approach and ability to simplify complex concepts have been invaluable.',
                    'avatar' => '/images/nigeria.png',
                    'name' => 'Joseph Joseph',
                ],
                [
                    'quote' => 'Your Teachings Improved My Playing',
                    'body' => "It was truly a blessing meeting Kingsley. His teachings have improved my playing and my overall knowledge of what I'm actually playing, dramatically. Thank you so much for your time and patience. Truly grateful. May God continue to increase your wisdom.",
                    'avatar' => '/images/fran.png',
                    'name' => 'Dionysius Harmon',
                ],
            ];
        @endphp

        @foreach ($testimonials as $t)
        <div class="bg-white/[0.04] rounded-2xl p-7 border border-white/10 flex flex-col gap-4 hover:border-[#FFD736]/30 transition-colors duration-300">
            <div class="flex items-center justify-between">
                <div class="w-9 h-9 rounded-full bg-[#FFD736]/10 flex items-center justify-center">
                    <i class="fas fa-quote-left text-[#FFD736] text-xs"></i>
                </div>
                <div class="flex gap-0.5">
                    @for($i=0;$i<5;$i++)
                        <i class="fas fa-star text-[#FFD736] text-[11px]"></i>
                    @endfor
                </div>
            </div>
            <h3 class="font-bold text-lg text-white leading-snug">
                {{ $t['quote'] }}
            </h3>
            <p class="text-gray-400 text-sm leading-relaxed flex-1">
                {{ $t['body'] }}
            </p>
            <div class="flex items-center gap-3 pt-4 border-t border-white/10">
                <img src="{{ $t['avatar'] }}" alt="{{ $t['name'] }}" class="w-9 h-9 rounded-full object-cover flex-shrink-0 ring-1 ring-white/10">
                <div>
                    <span class="block font-semibold text-white text-sm">{{ $t['name'] }}</span>
                    <span class="block text-gray-500 text-xs">Student</span>
                </div>
            </div>
        </div>
        @endforeach

    </div>

</section>
