@extends('layouts.admin')

@section('content')
<div class="p-4 sm:p-6 max-w-4xl mx-auto" x-data="personalizedPlanForm({{ \Illuminate\Support\Js::from($initialPlan) }})">

    <a href="{{ route('admin.personalized-guidance.show', $user) }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-500 hover:text-gray-700 mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        Back to {{ $user->full_name ?? 'student' }}
    </a>

    <div class="mb-6">
        <h1 class="text-lg font-bold text-gray-900">Personalized Plan</h1>
        <p class="text-sm text-gray-500">{{ $user->full_name ?? 'Unknown student' }} &middot; {{ $user->email ?? '' }}</p>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.personalized-guidance.plan.update', $user) }}">
        @csrf
        <input type="hidden" name="skill_level" :value="skillLevel">
        <input type="hidden" name="goal" :value="goal">
        <input type="hidden" name="start_date" :value="startDate">
        <input type="hidden" name="ninety_day_target" :value="JSON.stringify(ninetyDayTarget)">
        <input type="hidden" name="months" :value="JSON.stringify(months)">

        <!-- Skill Level -->
        <div class="bg-white rounded-xl shadow p-5 mb-6">
            <label class="block text-sm font-bold text-gray-900 mb-2">Skill Level</label>
            <select x-model="skillLevel" class="w-full sm:w-72 border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none">
                <option value="">Select a skill level</option>
                <option>Early Beginner</option>
                <option>Advanced Beginner</option>
                <option>Intermediate</option>
                <option>Upper Intermediate</option>
                <option>Advanced</option>
            </select>
        </div>

        <!-- Goal -->
        <div class="bg-white rounded-xl shadow p-5 mb-6">
            <label class="block text-sm font-bold text-gray-900 mb-2">Goal</label>
            <input type="text" x-model="goal" placeholder="e.g. Gospel Piano Improvisation"
                class="w-full sm:w-96 border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none">
        </div>

        <!-- Start Date -->
        <div class="bg-white rounded-xl shadow p-5 mb-6">
            <label class="block text-sm font-bold text-gray-900 mb-2">Start Date</label>
            <input type="date" x-model="startDate"
                class="w-full sm:w-56 border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none">
            <p class="text-xs text-gray-400 mt-1.5">This is a fixed 90-day program — the end date is calculated automatically.</p>
        </div>

        <!-- 90-Day Target -->
        <div class="bg-white rounded-xl shadow p-5 mb-6">
            <label class="block text-sm font-bold text-gray-900 mb-3">90-Day Target</label>

            <template x-for="(item, idx) in ninetyDayTarget" :key="idx">
                <div class="flex items-center gap-2 mb-2">
                    <input type="text" x-model="ninetyDayTarget[idx]" placeholder="e.g. Advanced chord voicings"
                        class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none">
                    <button type="button" @click="ninetyDayTarget.splice(idx, 1)"
                        class="flex-shrink-0 w-9 h-9 flex items-center justify-center text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition">
                        <i class="fa fa-trash text-xs"></i>
                    </button>
                </div>
            </template>

            <button type="button" @click="ninetyDayTarget.push('')"
                class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1.5 mt-1">
                <i class="fa fa-plus"></i> Add target item
            </button>
        </div>

        <!-- Months -->
        <div class="bg-white rounded-xl shadow p-5 mb-6" x-data="{ activeMonth: 0 }">
            <label class="block text-sm font-bold text-gray-900 mb-3">3-Month Plan</label>

            <div class="flex gap-2 mb-5">
                <template x-for="(month, mIdx) in months" :key="mIdx">
                    <button type="button" @click="activeMonth = mIdx"
                        :class="activeMonth === mIdx ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                        class="px-4 py-2 rounded-lg text-sm font-semibold transition-colors"
                        x-text="'Month ' + (mIdx + 1)"></button>
                </template>
            </div>

            <template x-for="(month, mIdx) in months" :key="mIdx">
                <div x-show="activeMonth === mIdx">

                    <template x-for="cat in categories" :key="cat.key">
                        <div class="mb-5">
                            <p class="text-xs font-bold text-gray-700 uppercase tracking-wide mb-2" x-text="cat.label"></p>

                            <template x-for="(lesson, lIdx) in month.lessons[cat.key]" :key="lIdx">
                                <div class="border border-gray-200 rounded-lg p-3 mb-2">
                                    <div class="flex items-center gap-2 mb-2">
                                        <input type="text" x-model="lesson.name" placeholder="Lesson name"
                                            class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none">
                                        <button type="button" @click="month.lessons[cat.key].splice(lIdx, 1)"
                                            class="flex-shrink-0 w-9 h-9 flex items-center justify-center text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition">
                                            <i class="fa fa-trash text-xs"></i>
                                        </button>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input type="text" x-model="lesson.url" placeholder="Lesson link"
                                            class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none">
                                        <div class="flex items-center w-32 border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                            <span class="text-gray-500 flex-shrink-0">Week</span>
                                            <input type="number" min="1" x-model="lesson.week"
                                                class="w-full ml-1.5 outline-none">
                                        </div>
                                        <div class="flex items-center w-32 border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                            <input type="number" min="1" x-model="lesson.duration"
                                                class="w-full outline-none">
                                            <span class="text-gray-500 flex-shrink-0 ml-1.5">min</span>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <button type="button" @click="month.lessons[cat.key].push({ id: genLessonId(), name: '', url: '', week: '', duration: '' })"
                                class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1.5">
                                <i class="fa fa-plus"></i> Add lesson
                            </button>
                        </div>
                    </template>

                </div>
            </template>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-gray-900 hover:bg-black text-white px-5 py-2.5 rounded-lg text-sm font-semibold">
                Save Plan
            </button>
        </div>
    </form>

</div>

<script>
    function personalizedPlanForm(initial) {
        const categories = [
            { key: 'finger_exercise', label: 'Finger Exercise' },
            { key: 'theory_and_application', label: 'Theory and Application' },
            { key: 'guided_practice', label: 'Guided Practice' },
            { key: 'repertoire', label: 'Repertoire' },
        ];

        return {
            categories,
            skillLevel: initial.skill_level || '',
            goal: initial.goal || '',
            startDate: initial.start_date || '',
            ninetyDayTarget: (initial.ninety_day_target && initial.ninety_day_target.length) ? initial.ninety_day_target : [''],
            months: initial.months,
            genLessonId() {
                return window.crypto?.randomUUID ? window.crypto.randomUUID() : 'l_' + Math.random().toString(36).slice(2);
            },
        };
    }
</script>
@endsection
