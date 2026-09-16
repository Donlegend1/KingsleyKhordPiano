@extends("layouts.hub")

@section("title", "My Profile")

@section("breadcrumbs")
    @include('community.partials.breadcrumbs', ['items' => [['label' => 'My Profile']]])
@endsection

@section("content")
<div class="p-6">

    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">My Profile</h1>

    {{-- Cover / profile header --}}
    <div class="relative rounded-2xl overflow-hidden shadow-sm">

        {{-- Cover photo --}}
        <div class="relative h-44 sm:h-52"
            style="background: linear-gradient(135deg, #6b5b73 0%, #a48a9e 35%, #d8c6d6 70%, #ece3ea 100%);">
            <svg class="absolute inset-0 w-full h-full" preserveAspectRatio="none" viewBox="0 0 100 40" xmlns="http://www.w3.org/2000/svg">
                <polygon points="0,0 30,0 15,20" fill="#ffffff" opacity="0.06"/>
                <polygon points="20,0 55,0 40,25 10,25" fill="#ffffff" opacity="0.05"/>
                <polygon points="50,0 80,0 65,18" fill="#000000" opacity="0.06"/>
                <polygon points="70,0 100,0 100,15 85,25" fill="#ffffff" opacity="0.08"/>
                <polygon points="0,20 20,25 5,40 0,40" fill="#000000" opacity="0.05"/>
                <polygon points="30,25 60,20 55,40 25,40" fill="#ffffff" opacity="0.05"/>
                <polygon points="60,15 90,22 100,40 55,40" fill="#000000" opacity="0.05"/>
            </svg>

            {{-- Action buttons --}}
            <div class="absolute top-4 right-4 flex items-center gap-0.5 rounded-lg overflow-hidden shadow-sm">
                <a href="{{ route('community.account-settings') }}"
                    class="flex items-center gap-1.5 px-4 py-2.5 text-sm font-medium text-white bg-black/30 hover:bg-black/40 backdrop-blur-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Account Settings
                </a>
                <a href="/member/profile"
                    class="flex items-center gap-1.5 px-4 py-2.5 text-sm font-medium text-white bg-black/30 hover:bg-black/40 backdrop-blur-sm transition-colors border-l border-white/10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487z"/>
                    </svg>
                    Edit Profile
                </a>
            </div>

            {{-- Avatar + name, arranged together so they always line up --}}
            <div class="absolute left-6 sm:left-8 bottom-6 sm:bottom-8 flex items-center gap-4">
                <div class="relative w-24 h-24 sm:w-28 sm:h-28 flex-shrink-0">
                    <form action="/profile/update" method="POST" enctype="multipart/form-data"
                        class="w-full h-full rounded-full ring-4 ring-white overflow-hidden shadow-md">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="first_name" value="{{ $user->first_name }}">
                        <input type="hidden" name="last_name" value="{{ $user->last_name }}">
                        <input type="hidden" name="email" value="{{ $user->email }}">
                        <input type="hidden" name="country" value="{{ $user->country }}">

                        @if($user->passport)
                            <img src="{{ $user->passport }}" alt="{{ $user->display_name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-white text-4xl font-bold"
                                style="background-color: #7fb069;">
                                {{ strtoupper(substr($user->first_name ?? $user->email, 0, 1)) }}
                            </div>
                        @endif

                        <input id="avatarUpload" name="passport" type="file" accept="image/*" class="hidden" onchange="this.form.submit()">
                    </form>

                    <label for="avatarUpload"
                        class="absolute -bottom-0.5 -right-0.5 w-8 h-8 rounded-full bg-gray-900 hover:bg-black ring-2 ring-white flex items-center justify-center cursor-pointer shadow-md transition-colors z-10">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/>
                        </svg>
                    </label>
                </div>

                <div class="flex flex-col items-start pb-1">
                    <div class="bg-black/45 backdrop-blur-sm px-4 py-2 rounded-t-md">
                        <h1 class="text-xl sm:text-2xl font-bold text-white uppercase tracking-wide leading-none whitespace-nowrap">{{ $user->display_name }}</h1>
                    </div>
                    <div class="bg-black/30 backdrop-blur-sm px-4 py-1 text-sm text-gray-200">
                        {{ $user->premium ? 'Premium Member' : 'Member' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Stats bar --}}
        <div class="bg-[#262626] px-6 sm:px-8 py-4 flex items-center flex-wrap gap-3">
            <div class="flex items-center gap-6 sm:gap-10 pl-24 sm:pl-28">
                <div>
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Posts</p>
                    <p class="text-sm font-bold text-white mt-0.5">{{ $postsCount }}</p>
                </div>
                <div class="h-8 w-px bg-white/10 hidden sm:block"></div>
                <div>
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Joined</p>
                    <p class="text-sm font-bold text-white mt-0.5">{{ $user->created_at->diffForHumans() }}</p>
                </div>
                <div class="h-8 w-px bg-white/10 hidden sm:block"></div>
                <div>
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Skill Level</p>
                    <p class="text-sm font-bold mt-0.5 {{ $skillLevel ? 'text-white' : 'text-gray-500' }}">{{ $skillLevel ?: 'Not Set' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Academy Stats --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm p-6 mt-6">
        <p class="text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-4">Academy Stats</p>
        <div class="flex flex-col gap-4">
            <div class="bg-gray-50 dark:bg-gray-700/40 rounded-xl p-4 flex items-center gap-4">
                <div class="w-11 h-11 rounded-full bg-white dark:bg-gray-700 flex items-center justify-center flex-shrink-0 shadow-sm">
                    <svg class="w-5 h-5 text-gray-900 dark:text-gray-200" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                    </svg>
                </div>
                <div>
                    <span class="text-2xl font-black text-gray-900 dark:text-white">{{ $totalCompleted }}</span>
                    <p class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 -mt-0.5">Lessons</p>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700/40 rounded-xl p-4 flex items-center gap-4">
                <div class="w-11 h-11 rounded-full bg-white dark:bg-gray-700 flex items-center justify-center flex-shrink-0 shadow-sm">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013-3h.75a3 3 0 003-3v-1.5a3 3 0 00-3-3H18M16.5 18.75V21m-9 0V18.75m0 0a3 3 0 00-3-3H3.75a3 3 0 01-3-3v-1.5a3 3 0 013-3H6m10.5-3V3a.75.75 0 00-.75-.75h-7.5a.75.75 0 00-.75.75v3h9Z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-2xl font-black text-gray-900 dark:text-white">{{ $achievedCount }}</span>
                    <p class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 -mt-0.5">Milestones</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Skill Assessment --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm p-6 mt-6">
        @if($assessment)
            @php
                $levelColors = [
                    'Advanced' => ['text' => 'text-red-600', 'bg' => 'bg-red-50', 'ring' => 'text-red-500'],
                    'Intermediate' => ['text' => 'text-violet-600', 'bg' => 'bg-violet-50', 'ring' => 'text-violet-500'],
                    'Beginner' => ['text' => 'text-blue-600', 'bg' => 'bg-blue-50', 'ring' => 'text-blue-500'],
                ];
                $lc = $levelColors[$assessment->skill_level] ?? $levelColors['Beginner'];
                $circumference = 251.2;
            @endphp

            <div class="flex flex-col md:flex-row items-center gap-8">
                {{-- Score gauge --}}
                <div class="relative w-32 h-32 flex-shrink-0 flex items-center justify-center">
                    <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100">
                        <circle class="text-gray-100 dark:text-gray-700" stroke-width="8" stroke="currentColor" fill="transparent" r="40" cx="50" cy="50"/>
                        <circle class="{{ $lc['ring'] }}" stroke-width="8"
                            stroke-dasharray="{{ $circumference }}"
                            stroke-dashoffset="{{ $circumference - ($circumference * $assessment->score) / 100 }}"
                            stroke-linecap="round" stroke="currentColor" fill="transparent" r="40" cx="50" cy="50"/>
                    </svg>
                    <div class="absolute flex flex-col items-center">
                        <span class="text-2xl font-black text-gray-900 dark:text-white">{{ $assessment->score }}%</span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Score</span>
                    </div>
                </div>

                {{-- Metrics --}}
                <div class="flex-1 w-full grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">
                    <div class="sm:col-span-2 flex items-center justify-between -mb-1">
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Assigned Level</p>
                        <span class="text-xs font-bold px-3 py-1 rounded-full {{ $lc['bg'] }} {{ $lc['text'] }}">{{ strtoupper($assessment->skill_level) }}</span>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Fundamentals</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $assessment->fundamentals_score }}%</span>
                        </div>
                        <div class="w-full h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-600 rounded-full" style="width: {{ $assessment->fundamentals_score }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Ear Training</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $assessment->ear_training_score }}%</span>
                        </div>
                        <div class="w-full h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $assessment->ear_training_score }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Chords &amp; Harmony</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $assessment->chords_harmony_score }}%</span>
                        </div>
                        <div class="w-full h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full bg-violet-600 rounded-full" style="width: {{ $assessment->chords_harmony_score }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Experience</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $assessment->experience_score }}%</span>
                        </div>
                        <div class="w-full h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full bg-amber-500 rounded-full" style="width: {{ $assessment->experience_score }}%"></div>
                        </div>
                    </div>

                    <div class="sm:col-span-2 flex justify-end">
                        <a href="{{ route('member.quiz', ['retake' => 1]) }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-indigo-600 hover:text-indigo-700 transition-colors">
                            Retake Assessment
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="flex flex-col items-center text-center py-6">
                <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1">No assessment taken yet</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4 max-w-sm">Take the skills assessment so Kingsley can tailor your roadmap to your level.</p>
                <a href="{{ route('member.quiz') }}" class="inline-flex items-center gap-1.5 bg-gray-900 hover:bg-black text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                    Take Assessment
                </a>
            </div>
        @endif
    </div>

</div>
@endsection
