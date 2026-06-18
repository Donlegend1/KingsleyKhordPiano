@extends("layouts.community")

@section("content")

@php
    $milestonesList = [
        ['name' => 'Starter', 'lessons' => 1],
        ['name' => 'Player', 'lessons' => 4],
        ['name' => 'Performer', 'lessons' => 9],
        ['name' => 'Artist', 'lessons' => 14],
        ['name' => 'Maestro', 'lessons' => 21],
        ['name' => 'Master', 'lessons' => 31],
        ['name' => 'Grand Master', 'lessons' => 43],
        ['name' => 'Composer', 'lessons' => 58],
        ['name' => 'Conductor', 'lessons' => 76],
        ['name' => 'Virtuoso', 'lessons' => 96],
        ['name' => 'Prodigy', 'lessons' => 121],
        ['name' => 'Piano Legend', 'lessons' => 151],
    ];

    $currentMilestoneName = 'None';
    foreach ($milestonesList as $ms) {
        if ($totalCompleted >= $ms['lessons']) {
            $currentMilestoneName = $ms['name'];
        }
    }

    // Colors mapping for skill level
    $levelColor = 'indigo-600';
    $levelBg = 'indigo-50';
    $levelBorder = 'indigo-100';
    $levelText = 'indigo-700';
    $strokeColor = '#4F46E5';

    if ($assessment) {
        $levelLower = strtolower($assessment->skill_level);
        if ($levelLower === 'beginner') {
            $levelColor = 'blue-600';
            $levelBg = 'blue-50';
            $levelBorder = 'blue-100';
            $levelText = 'blue-700';
            $strokeColor = '#3B82F6';
        } elseif ($levelLower === 'intermediate') {
            $levelColor = 'violet-600';
            $levelBg = 'violet-50';
            $levelBorder = 'violet-100';
            $levelText = 'violet-700';
            $strokeColor = '#8B5CF6';
        } elseif ($levelLower === 'advanced') {
            $levelColor = 'rose-600';
            $levelBg = 'rose-50';
            $levelBorder = 'rose-100';
            $levelText = 'rose-700';
            $strokeColor = '#F43F5E';
        }
    }
@endphp

<!-- Header Section -->
<div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 mb-6">
    <div class="px-6 py-5">
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white leading-tight">Your Growth at a Glance</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Track your skills assessment, progress metrics, and continue your music learning journey.</p>
    </div>
</div>

<!-- Main Overview Content -->
<div class="px-6 pb-12">
    <div class="space-y-8">

            {{-- Profile + Stats: side by side --}}
            <div class="flex gap-6">

                {{-- Profile Card --}}
                <div class="flex-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-6 shadow-sm flex flex-col items-center text-center">
                    @if($user->passport)
                        <img src="{{ asset($user->passport) }}" alt="Avatar" class="w-20 h-20 rounded-full object-cover ring-4 ring-indigo-100 dark:ring-indigo-900/30">
                    @else
                        <div class="w-20 h-20 rounded-full bg-indigo-50 dark:bg-indigo-900/20 ring-4 ring-indigo-100 dark:ring-indigo-900/30 flex items-center justify-center text-indigo-300 text-xs font-semibold">
                            Avatar
                        </div>
                    @endif

                    <h2 class="mt-4 text-lg font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                        {{ $user->first_name }} {{ $user->last_name }}
                        @if($user->verified)
                            <svg class="w-5 h-5 text-blue-500 fill-current flex-shrink-0" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                        @endif
                    </h2>

                    <p class="text-sm font-semibold text-indigo-500 dark:text-indigo-400 mt-0.5">
                        {{ !empty($user->biography) ? $user->biography : 'Gospel Piano Enthusiast' }}
                    </p>

                    <div class="mt-3 inline-flex items-center gap-1.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600 px-3 py-1.5 rounded-full">
                        <svg class="w-3.5 h-3.5 text-red-500 fill-current" viewBox="0 0 24 24">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                        </svg>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $user->country ?? 'Global' }}</span>
                    </div>

                    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-3">
                        Joined {{ $user->created_at ? $user->created_at->format('F Y') : 'August 2025' }}
                    </p>
                </div>

                {{-- Academy Stats Card --}}
                <div class="flex-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-6 shadow-sm">
                    <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-4">Academy Stats</p>
                    <div class="flex flex-col gap-4">
                        <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-xl p-4 text-center">
                            <span class="text-2xl font-black text-indigo-600 dark:text-indigo-400">{{ $totalCompleted }}</span>
                            <p class="text-[10px] font-semibold text-indigo-400 dark:text-indigo-500 mt-1">Lessons</p>
                        </div>
                        <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl p-4 text-center">
                            <span class="text-2xl font-black text-amber-500 dark:text-amber-400">{{ $achievedCount }}</span>
                            <p class="text-[10px] font-semibold text-amber-400 dark:text-amber-500 mt-1">Milestones</p>
                        </div>
                    </div>
                </div>

            </div>

            {{-- 3. Feedback Widget --}}
            <div
                x-data="{ rating: 0, hoverRating: 0, comment: '', submitted: false }"
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 shadow-sm"
            >
                <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-2">Help Us Improve</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">How has your academy experience been? Share your thoughts below.</p>

                <!-- Interactive Stars -->
                <div class="flex items-center gap-2 mb-5">
                    <template x-for="i in 5">
                        <button
                            type="button"
                            @click="rating = i"
                            @mouseenter="hoverRating = i"
                            @mouseleave="hoverRating = 0"
                            class="focus:outline-none transition-transform active:scale-95 p-0.5"
                        >
                            <svg
                                class="w-6 h-6 transition-colors"
                                :class="{
                                    'fill-amber-400 stroke-amber-500': (hoverRating || rating) >= i,
                                    'fill-white stroke-gray-900': (hoverRating || rating) < i
                                }"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/>
                            </svg>
                        </button>
                    </template>
                </div>

                <!-- Comment Input -->
                <textarea
                    x-model="comment"
                    placeholder="Suggest courses, report bugs, or share positive feedback..."
                    class="w-full text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-4 text-gray-800 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 mb-4 resize-none h-24"
                ></textarea>

                <button
                    type="button"
                    @click="if(rating > 0 || comment.trim() !== '') { submitted = true; rating = 0; comment = ''; setTimeout(() => submitted = false, 5000) }"
                    class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold transition shadow-sm"
                >
                    Submit Feedback
                </button>

                <!-- Success Alert -->
                <div 
                    x-show="submitted" 
                    x-transition 
                    x-cloak 
                    class="mt-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs p-3 rounded-xl text-center font-medium"
                >
                    Thank you! Your feedback has been received. ✨
                </div>
            </div>

            {{-- Assigned Level / Retake Quiz --}}
            @if($assessment)
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl px-10 py-12 shadow-sm flex items-center gap-10 min-h-[300px]">
                    <!-- Circular Gauge -->
                    <div class="relative w-44 h-44 flex-shrink-0">
                        <svg class="w-44 h-44 transform -rotate-90" viewBox="0 0 120 120">
                            <circle cx="60" cy="60" r="52" stroke="#E5E7EB" class="dark:stroke-gray-700" stroke-width="7" fill="none"/>
                            <circle cx="60" cy="60" r="52" stroke="{{ $strokeColor }}" stroke-width="7" fill="none"
                                stroke-dasharray="326.725"
                                stroke-dashoffset="{{ 326.725 - (326.725 * $assessment->score / 100) }}"
                                stroke-linecap="round" class="transition-all duration-500"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-4xl font-extrabold text-gray-900 dark:text-white">{{ $assessment->score }}%</span>
                            <span class="text-xs text-gray-400 dark:text-gray-500 uppercase font-bold tracking-wider mt-1">Score</span>
                        </div>
                    </div>

                    <!-- Level & Breakdown -->
                    <div class="flex-grow min-w-0">
                        <div class="flex items-center justify-between gap-2 mb-6">
                            <span class="text-sm font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Assigned Level</span>
                            <span class="text-sm font-black uppercase bg-{{ $levelBg }} text-{{ $levelText }} dark:bg-{{ $levelColor }}/10 dark:text-{{ $levelColor }} border border-{{ $levelBorder }} dark:border-{{ $levelColor }}/20 px-4 py-1.5 rounded-full">
                                {{ $assessment->skill_level }}
                            </span>
                        </div>

                        <!-- Breakdown bars: 2 per row -->
                        <div class="flex gap-8 mb-6">
                            <div class="flex-1">
                                <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400 mb-2">
                                    <span>Fundamentals</span><span class="font-bold">{{ $assessment->fundamentals_score }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                                    <div class="h-2 rounded-full" style="width:{{ $assessment->fundamentals_score }}%;background-color:#3B82F6"></div>
                                </div>
                                <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400 mb-2 mt-5">
                                    <span>Chords & Harmony</span><span class="font-bold">{{ $assessment->chords_harmony_score }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                                    <div class="h-2 rounded-full" style="width:{{ $assessment->chords_harmony_score }}%;background-color:#8B5CF6"></div>
                                </div>
                            </div>
                            <div class="w-px bg-gray-100 dark:bg-gray-700 self-stretch"></div>
                            <div class="flex-1">
                                <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400 mb-2">
                                    <span>Ear Training</span><span class="font-bold">{{ $assessment->ear_training_score }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                                    <div class="h-2 rounded-full" style="width:{{ $assessment->ear_training_score }}%;background-color:#10B981"></div>
                                </div>
                                <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400 mb-2 mt-5">
                                    <span>Experience</span><span class="font-bold">{{ $assessment->experience_score }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                                    <div class="h-2 rounded-full" style="width:{{ $assessment->experience_score }}%;background-color:#F59E0B"></div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <a href="/member/quiz" class="text-sm text-indigo-500 hover:text-indigo-600 dark:text-indigo-400 font-bold inline-flex items-center gap-1.5 transition">
                                Retake Assessment <i class="fa fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-gradient-to-r from-indigo-900 to-violet-800 text-white rounded-2xl p-6 shadow-sm flex flex-col md:flex-row items-center gap-6 border border-indigo-700/20">
                    <div class="w-16 h-16 rounded-2xl bg-white/10 flex items-center justify-center text-3xl flex-shrink-0">🏆</div>
                    <div class="flex-grow">
                        <h3 class="text-lg font-bold text-white mb-1.5">Evaluate Your Piano Skill Level</h3>
                        <p class="text-xs text-indigo-200 leading-relaxed mb-4">Take the 2-minute diagnostic quiz to unlock custom goals and find the perfect courses for your journey.</p>
                        <a href="/member/quiz" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-yellow-400 hover:bg-yellow-300 text-black font-extrabold rounded-xl text-xs transition shadow-sm">
                            Take Assessment Quiz
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/></svg>
                        </a>
                    </div>
                </div>
            @endif


            {{-- 1. Quick Links Row --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                <!-- Get Started Card -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-5 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 bg-indigo-50 dark:bg-indigo-950/40 rounded-xl flex items-center justify-center text-xl mb-4">
                            🧭
                        </div>
                        <h4 class="text-base font-bold text-gray-900 dark:text-white mb-1">Get Started</h4>
                        <p class="text-xs text-gray-400 dark:text-gray-500 leading-relaxed mb-4">
                            Get familiar with the platform and follow our step-by-step setup guides to start smoothly.
                        </p>
                    </div>
                    <a href="/member/getstarted" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 border border-gray-300 dark:border-gray-600 hover:border-indigo-500 dark:hover:border-indigo-400 text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-xl text-xs font-bold transition">
                        <span>Continue Setup</span>
                    </a>
                </div>

                <!-- Community Space Card -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-5 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl flex items-center justify-center text-xl mb-4">
                            👥
                        </div>
                        <h4 class="text-base font-bold text-gray-900 dark:text-white mb-1">Community Space</h4>
                        <p class="text-xs text-gray-400 dark:text-gray-500 leading-relaxed mb-4">
                            Explore shared files, PDF resources, backing tracks, MIDI downloads, and connect with other students.
                        </p>
                    </div>
                    <a href="/member/community/space/lessons" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 border border-gray-300 dark:border-gray-600 hover:border-emerald-500 dark:hover:border-emerald-400 text-gray-700 dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-400 rounded-xl text-xs font-bold transition">
                        <span>Visit Space</span>
                    </a>
                </div>
            </div>

            {{-- 3. Continue Learning Widget --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-full bg-violet-100 dark:bg-violet-950/40 flex items-center justify-center text-violet-600 dark:text-violet-400">
                        <svg class="w-4 h-4 fill-current ml-0.5" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-bold text-gray-900 dark:text-white">Continue Learning</span>
                </div>
                
                @if($resumeLesson)
                    @php
                        $thumbnail = $resumeLesson->thumbnail ? asset($resumeLesson->thumbnail) : ($resumeLesson->thumbnail_url ?? asset('images/featured1.jpeg'));
                    @endphp
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <img src="{{ $thumbnail }}" alt="{{ $resumeLesson->title }}" class="w-24 h-24 object-cover rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex-shrink-0">
                        <div class="flex-grow min-w-0 w-full">
                            <h4 class="text-base font-bold text-gray-900 dark:text-white truncate mb-0.5">{{ $resumeLesson->title }}</h4>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-3">{{ ucfirst($resumeLesson->level ?? 'Beginner') }} Lesson</p>
                            
                            {{-- Progress bar --}}
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5 mb-1.5">
                                <div class="bg-violet-600 dark:bg-violet-500 h-1.5 rounded-full" style="width: 45%"></div>
                            </div>
                            <div class="flex justify-between items-center text-[10px] text-gray-400 dark:text-gray-500 font-semibold">
                                <span>45% Completed</span>
                                <a href="{{ $resumeUrl }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-violet-600 hover:bg-violet-700 dark:bg-violet-500 dark:hover:bg-violet-600 text-white rounded-lg text-[10px] font-bold transition shadow-sm">
                                    <span>Resume</span>
                                    <svg class="w-2.5 h-2.5 fill-current" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <p class="text-xs text-gray-400 py-6 text-center">No active lessons found.</p>
                @endif
            </div>

            {{-- 4. Testimonials Slider --}}
            <div 
                x-data="{ active: 0, reviews: [
                    { text: 'Kingsley\'s academy is the best investment I have made in my music. The lessons are exceptionally detailed and easy to follow.', author: 'Marcus T.', role: 'Gospel Keyboardist' },
                    { text: 'The diagnostic assessment placed me perfectly at the Intermediate level. I\'ve already completed 14 lessons this month!', author: 'Sarah P.', role: 'Academy Student' },
                    { text: 'The community spacing is awesome. Downloading PDF sheets and MIDI files directly to practice on has helped me save hours.', author: 'David E.', role: 'Worship Director' }
                ] }"
                x-init="setInterval(() => active = (active + 1) % reviews.length, 5000)"
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-6 shadow-sm"
            >
                <h4 class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">Student Spotlights</h4>
                <div class="min-h-[100px] flex flex-col justify-between">
                    <p class="text-sm italic text-gray-600 dark:text-gray-300 leading-relaxed" x-text="'“' + reviews[active].text + '”'"></p>
                    <div class="mt-4 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-900 dark:text-white" x-text="reviews[active].author"></p>
                            <p class="text-[10px] text-indigo-500 dark:text-indigo-400" x-text="reviews[active].role"></p>
                        </div>
                        <div class="flex gap-1.5">
                            <template x-for="(rev, idx) in reviews">
                                <button 
                                    @click="active = idx" 
                                    class="w-2 h-2 rounded-full transition-all focus:outline-none"
                                    :class="active === idx ? 'bg-indigo-600 dark:bg-indigo-400 w-4' : 'bg-gray-200 dark:bg-gray-700'"
                                ></button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

    </div>
</div>

@endsection
