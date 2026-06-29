@extends("layouts.community")

@section("page-title")
    <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white leading-tight truncate">Your Growth at a Glance</h1>
@endsection

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
    $levelColor = 'blue-600';
    $levelBg = 'blue-50';
    $levelBorder = 'blue-100';
    $levelText = 'blue-700';
    $strokeColor = '#2563EB';

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

<!-- Main Overview Content -->
<div class="px-6 pt-6 pb-12">
    <div class="space-y-8">

            {{-- Profile + Stats: side by side --}}
            <div class="flex flex-col sm:flex-row sm:items-start gap-6">

                {{-- Profile Card --}}
                <div class="flex-1 bg-white dark:bg-[#161617] border border-gray-200 dark:border-white/10 rounded-2xl p-6 shadow-sm flex flex-col items-center text-center">
                    @if($user->passport)
                        <img src="{{ asset($user->passport) }}" alt="Avatar" class="w-20 h-20 rounded-full object-cover ring-4 ring-indigo-100 dark:ring-blue-500/20 flex-shrink-0 mb-4">
                    @else
                        <div class="w-20 h-20 rounded-full bg-indigo-50 dark:bg-blue-500/10 ring-4 ring-indigo-100 dark:ring-blue-500/20 flex items-center justify-center text-indigo-300 dark:text-blue-400 text-xs font-semibold flex-shrink-0 mb-4">
                            Avatar
                        </div>
                    @endif

                    <div class="min-w-0">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center justify-center gap-1.5">
                            {{ $user->first_name }} {{ $user->last_name }}
                            @if($user->verified)
                                <svg class="w-5 h-5 text-blue-500 fill-current flex-shrink-0" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                            @endif
                        </h2>

                        <p class="text-sm font-semibold text-indigo-500 dark:text-blue-400 mt-0.5">
                            {{ !empty($user->biography) ? $user->biography : 'Gospel Piano Enthusiast' }}
                        </p>

                        <div class="mt-3 inline-flex items-center gap-1.5 bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/10 px-3 py-1.5 rounded-full">
                            <svg class="w-3.5 h-3.5 text-red-500 fill-current" viewBox="0 0 24 24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                            </svg>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $user->country ?? 'Global' }}</span>
                        </div>

                        <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-3">
                            Joined {{ $user->created_at ? $user->created_at->format('F Y') : 'August 2025' }}
                        </p>
                    </div>
                </div>

                {{-- Academy Stats Card --}}
                <div class="flex-1 bg-white dark:bg-[#161617] border border-gray-200 dark:border-white/10 rounded-2xl p-6 shadow-sm">
                    <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-4">Academy Stats</p>
                    <div class="flex flex-col gap-4">
                        <div class="bg-gray-50 dark:bg-blue-500/10 rounded-xl p-4 flex items-center gap-4">
                            <div class="w-11 h-11 rounded-full bg-white dark:bg-blue-500/20 flex items-center justify-center flex-shrink-0 shadow-sm">
                                <svg class="w-5 h-5 text-gray-900 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                                </svg>
                            </div>
                            <div>
                                <span class="text-2xl font-black text-gray-900 dark:text-blue-400">{{ $totalCompleted }}</span>
                                <p class="text-[11px] font-semibold text-gray-500 dark:text-blue-400 -mt-0.5">Lessons</p>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-amber-900/20 rounded-xl p-4 flex items-center gap-4">
                            <div class="w-11 h-11 rounded-full bg-white dark:bg-amber-500/20 flex items-center justify-center flex-shrink-0 shadow-sm">
                                <svg class="w-5 h-5 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3-3h.75a3 3 0 0 0 3-3v-1.5a3 3 0 0 0-3-3H18M16.5 18.75V21m-9 0V18.75m0 0a3 3 0 0 0-3-3H3.75a3 3 0 0 1-3-3v-1.5a3 3 0 0 1 3-3H6m10.5-3V3a.75.75 0 0 0-.75-.75h-7.5a.75.75 0 0 0-.75.75v3h9Z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="text-2xl font-black text-gray-900 dark:text-amber-400">{{ $achievedCount }}</span>
                                <p class="text-[11px] font-semibold text-gray-500 dark:text-amber-500 -mt-0.5">Milestones</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Assigned Level / Retake Quiz --}}
            @if($assessment)
                <div class="bg-white dark:bg-[#161617] border border-gray-200 dark:border-white/10 rounded-2xl px-6 py-8 sm:px-10 sm:py-12 shadow-sm flex flex-col md:flex-row items-center gap-8 md:gap-10">
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
                    <div class="flex-grow min-w-0 w-full">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-6">
                            <span class="text-sm font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Assigned Level</span>
                            <span class="text-sm font-black uppercase bg-{{ $levelBg }} text-{{ $levelText }} dark:bg-{{ $levelColor }}/10 dark:text-{{ $levelColor }} border border-{{ $levelBorder }} dark:border-{{ $levelColor }}/20 px-4 py-1.5 rounded-full">
                                {{ $assessment->skill_level }}
                            </span>
                        </div>

                        <!-- Breakdown bars: 2 per row -->
                        <div class="flex flex-col sm:flex-row gap-6 sm:gap-8 mb-6">
                            <div class="flex-1">
                                <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400 mb-2">
                                    <span>Fundamentals</span><span class="font-bold">{{ $assessment->fundamentals_score }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 dark:bg-white/10 rounded-full h-2">
                                    <div class="h-2 rounded-full" style="width:{{ $assessment->fundamentals_score }}%;background-color:#3B82F6"></div>
                                </div>
                                <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400 mb-2 mt-5">
                                    <span>Chords & Harmony</span><span class="font-bold">{{ $assessment->chords_harmony_score }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 dark:bg-white/10 rounded-full h-2">
                                    <div class="h-2 rounded-full" style="width:{{ $assessment->chords_harmony_score }}%;background-color:#8B5CF6"></div>
                                </div>
                            </div>
                            <div class="hidden sm:block w-px bg-gray-100 dark:bg-white/10 self-stretch"></div>
                            <div class="flex-1">
                                <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400 mb-2">
                                    <span>Ear Training</span><span class="font-bold">{{ $assessment->ear_training_score }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 dark:bg-white/10 rounded-full h-2">
                                    <div class="h-2 rounded-full" style="width:{{ $assessment->ear_training_score }}%;background-color:#10B981"></div>
                                </div>
                                <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400 mb-2 mt-5">
                                    <span>Experience</span><span class="font-bold">{{ $assessment->experience_score }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 dark:bg-white/10 rounded-full h-2">
                                    <div class="h-2 rounded-full" style="width:{{ $assessment->experience_score }}%;background-color:#F59E0B"></div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <a href="/member/quiz" class="text-sm text-indigo-500 hover:text-indigo-600 dark:text-blue-400 font-bold inline-flex items-center gap-1.5 transition">
                                Retake Assessment <i class="fa fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-gradient-to-r from-blue-900 to-blue-700 text-white rounded-2xl p-6 shadow-sm flex flex-col md:flex-row items-center gap-6 border border-blue-700/20">
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

            {{-- 3. Feedback Widget --}}
            <div
                x-data="{ rating: 0, hoverRating: 0, comment: '', submitted: false }"
                class="bg-white dark:bg-[#161617] border border-gray-200 dark:border-white/10 rounded-2xl p-8 shadow-sm"
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
                    class="w-full text-sm bg-gray-50 dark:bg-[#0F0F10] border border-gray-200 dark:border-white/10 rounded-xl p-4 text-gray-800 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 mb-4 resize-none h-24"
                ></textarea>

                <button
                    type="button"
                    @click="if(rating > 0 || comment.trim() !== '') { submitted = true; rating = 0; comment = ''; setTimeout(() => submitted = false, 5000) }"
                    class="inline-flex px-8 py-3 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-sm font-bold transition shadow-sm"
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

            {{-- 1. Quick Links Row --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                <!-- Get Started Card -->
                <div class="bg-white dark:bg-[#161617] border border-gray-200 dark:border-white/10 rounded-2xl p-5 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 bg-indigo-50 dark:bg-blue-500/10 rounded-xl flex items-center justify-center text-xl mb-4">
                            🧭
                        </div>
                        <h4 class="text-base font-bold text-gray-900 dark:text-white mb-1">Get Started</h4>
                        <p class="text-xs text-gray-400 dark:text-gray-500 leading-relaxed mb-4">
                            Get familiar with the platform and follow our step-by-step setup guides to start smoothly.
                        </p>
                    </div>
                    <a href="/member/getstarted" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 border border-gray-300 dark:border-white/10 hover:border-indigo-500 dark:hover:border-blue-400 text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-blue-400 rounded-xl text-xs font-bold transition">
                        <span>Continue Setup</span>
                    </a>
                </div>

                <!-- Community Space Card -->
                <div class="bg-white dark:bg-[#161617] border border-gray-200 dark:border-white/10 rounded-2xl p-5 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl flex items-center justify-center text-xl mb-4">
                            👥
                        </div>
                        <h4 class="text-base font-bold text-gray-900 dark:text-white mb-1">Community Space</h4>
                        <p class="text-xs text-gray-400 dark:text-gray-500 leading-relaxed mb-4">
                            Explore shared files, PDF resources, backing tracks, MIDI downloads, and connect with other students.
                        </p>
                    </div>
                    <a href="/member/community/space/lessons" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 border border-gray-300 dark:border-white/10 hover:border-emerald-500 dark:hover:border-emerald-400 text-gray-700 dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-400 rounded-xl text-xs font-bold transition">
                        <span>Visit Space</span>
                    </a>
                </div>
            </div>

    </div>
</div>

@endsection
