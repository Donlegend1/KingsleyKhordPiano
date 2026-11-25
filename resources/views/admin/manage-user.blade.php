@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-6xl space-y-5 px-4 py-6 sm:px-7 sm:py-8">
    <header>
        <a href="{{ url('/admin/users') }}" class="text-sm font-semibold text-[#1447a6] hover:underline">← All users</a>
        <div class="mt-3 flex items-center gap-3">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-blue-100 font-bold text-blue-800" aria-hidden="true">{{ Str::substr((string) $user->first_name, 0, 1) }}{{ Str::substr((string) $user->last_name, 0, 1) }}</span>
            <div class="min-w-0">
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">{{ trim($user->full_name) ?: 'Manage user' }}</h1>
                <p class="break-all text-sm text-slate-600">{{ $user->email }}</p>
            </div>
        </div>
    </header>

    <dl class="grid gap-3 sm:grid-cols-3">
        @foreach(['Joined' => $user->created_at?->format('M j, Y') ?? 'Unavailable', 'Country' => $country ?? 'Unavailable', 'Assessment skill level' => $assessment?->skill_level ?: 'NIL'] as $label => $value)
            <div class="rounded-xl border border-slate-200 bg-white px-4 py-3">
                <dt class="text-xs font-semibold text-slate-600">{{ $label }}</dt>
                <dd class="mt-1 text-sm font-bold text-slate-900">{{ $value }}</dd>
            </div>
        @endforeach
    </dl>

    <nav aria-label="Manage user sections" class="flex flex-wrap gap-1 rounded-xl border border-slate-200 bg-white p-1.5">
        @foreach($tabs as $tab => $label)
            <a href="{{ route('admin.users.manage', ['user' => $user, 'tab' => $tab]) }}"
               @if($activeTab === $tab) aria-current="page" @endif
               class="rounded-lg px-3 py-2 text-sm font-semibold {{ $activeTab === $tab ? 'bg-[#1447a6] text-white' : 'text-slate-700 hover:bg-slate-100' }}">{{ $label }}</a>
        @endforeach
    </nav>

    @if(session('success'))
        <p role="status" class="rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</p>
    @endif
    @if($errors->any())
        <ul role="alert" class="list-disc rounded-lg bg-red-50 py-3 pl-8 pr-4 text-sm text-red-800">
            @foreach($errors->all() as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    @endif

    @if($activeTab === 'profile')
        <section class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-6">
            <h2 class="text-lg font-bold text-slate-900">Profile details</h2>
            <form method="POST" action="{{ route('admin.users.manage.update', $user) }}" class="mt-4 space-y-4">
                @csrf
                @method('PUT')
                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach(['first_name' => 'First name', 'last_name' => 'Last name', 'email' => 'Email address'] as $field => $label)
                        <label class="block text-sm font-semibold text-slate-700 {{ $field === 'email' ? 'sm:col-span-2' : '' }}">{{ $label }}
                            <input name="{{ $field }}" type="{{ $field === 'email' ? 'email' : 'text' }}" value="{{ old($field, $user->$field) }}" maxlength="255" class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        </label>
                    @endforeach
                </div>
                <button class="rounded-lg bg-[#1447a6] px-4 py-2 text-sm font-semibold text-white">Save profile</button>
            </form>
        </section>
        <dl class="grid gap-3 sm:grid-cols-3">
            @foreach([
                'Last recorded sign-in' => $lastSignIn ? \Carbon\Carbon::parse($lastSignIn)->format('M j, Y') : 'Unavailable',
                'Last seen' => $user->last_login_at?->format('M j, Y · H:i T') ?? 'Unavailable',
                'Recorded completions' => number_format($completedCount),
            ] as $label => $value)
                <div class="rounded-xl border border-slate-200 bg-white px-4 py-3">
                    <dt class="text-xs font-semibold text-slate-600">{{ $label }}</dt>
                    <dd class="mt-1 text-sm font-bold text-slate-900">{{ $value }}</dd>
                </div>
            @endforeach
        </dl>
        <p class="text-xs text-slate-600">Last seen includes activity in remembered sessions.</p>
        <div class="grid gap-4 lg:grid-cols-2">
            <section class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5">
                <h2 class="text-lg font-bold text-slate-900">Recently opened lessons</h2>
                <ul class="mt-2 divide-y divide-slate-100 text-sm">
                    @forelse($recentViews as $entry)
                        <li class="flex justify-between gap-3 py-2.5"><span>{{ $entry->viewable?->title ?? 'Lesson unavailable' }}</span><span class="shrink-0 text-slate-600">{{ $entry->updated_at?->format('M j, Y') }}</span></li>
                    @empty
                        <li class="py-2.5 text-slate-600">No lesson openings recorded.</li>
                    @endforelse
                </ul>
            </section>
            <section class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5">
                <h2 class="text-lg font-bold text-slate-900">Recently completed lessons</h2>
                <ul class="mt-2 divide-y divide-slate-100 text-sm">
                    @forelse($recentCompletions as $entry)
                        <li class="flex justify-between gap-3 py-2.5"><span>{{ $entry['title'] ?? 'Lesson unavailable' }}</span><span class="shrink-0 text-slate-600">{{ $entry['date']?->format('M j, Y') }}</span></li>
                    @empty
                        <li class="py-2.5 text-slate-600">No completions recorded.</li>
                    @endforelse
                </ul>
            </section>
        </div>
        <section class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5">
            <div class="flex flex-wrap items-baseline justify-between gap-2">
                <h2 class="text-lg font-bold text-slate-900">Login history</h2>
                <span class="text-xs text-slate-600">{{ number_format($loginDayCount) }} {{ Str::plural('day', $loginDayCount) }} signed in · showing latest {{ $loginHistory->count() }}</span>
            </div>
            @if($loginHistory->isEmpty())
                <p class="mt-2 text-sm text-slate-600">No sign-ins recorded.</p>
            @else
                <ul class="mt-3 grid gap-x-6 text-sm sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($loginHistory as $day)
                        <li class="flex justify-between gap-3 border-b border-slate-100 py-2"><span>{{ \Carbon\Carbon::parse($day)->format('D, M j, Y') }}</span><span class="shrink-0 text-slate-500">{{ \Carbon\Carbon::parse($day)->diffForHumans() }}</span></li>
                    @endforeach
                </ul>
            @endif
        </section>
    @elseif($activeTab === 'assessment')
        <section class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-6">
            <h2 class="text-lg font-bold text-slate-900">Latest assessment results</h2>
            @if($assessment)
                <p class="mt-1 text-sm text-slate-600">{{ $assessment->created_at?->format('M j, Y') }} · {{ $assessment->skill_level }}</p>
                <dl class="mt-4 grid grid-cols-2 gap-2 md:grid-cols-5">
                    @foreach(['Overall' => $assessment->score, 'Fundamentals' => $assessment->fundamentals_score, 'Ear training' => $assessment->ear_training_score, 'Chords & harmony' => $assessment->chords_harmony_score, 'Experience' => $assessment->experience_score] as $label => $score)
                        <div class="rounded-lg bg-slate-50 px-3 py-2">
                            <dt class="text-xs text-slate-600">{{ $label }}</dt>
                            <dd class="mt-1 text-lg font-bold text-[#1447a6]">{{ $score === null ? '—' : $score . '%' }}</dd>
                        </div>
                    @endforeach
                </dl>
                <h3 class="mt-5 text-sm font-bold text-slate-900">Selected answers</h3>
                <ol class="mt-2 divide-y divide-slate-100">
                    @forelse($assessment->answers ?? [] as $answer)
                        <li class="py-2.5 text-sm">
                            <p class="font-semibold text-slate-900">{{ $loop->iteration }}. {{ $answer['question'] ?? 'Question unavailable' }}</p>
                            <p class="mt-0.5 text-slate-600">{{ $answer['selected'] ?? 'No answer recorded' }}</p>
                        </li>
                    @empty
                        <li class="py-2.5 text-sm text-slate-600">No answers recorded for this assessment.</li>
                    @endforelse
                </ol>
            @else
                <p class="mt-2 text-sm text-slate-600">This user hasn’t taken the assessment quiz. Skill level is NIL.</p>
            @endif
        </section>
    @else
        <div class="grid gap-4 lg:grid-cols-2">
            <section class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 class="text-lg font-bold text-slate-900">Subscription dates and access</h2>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $accessStatus }}</span>
                </div>
                <form method="POST" action="{{ route('admin.users.manage.access', $user) }}" class="mt-4 space-y-3">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="paused" value="{{ $accessStatus === 'Paused' ? '1' : '0' }}">
                    <div class="grid gap-3 sm:grid-cols-2">
                        <label class="block text-sm font-semibold text-slate-700">Start date
                            <input type="date" name="starts_at" value="{{ old('starts_at', $startsAt?->toDateString()) }}" required class="mt-1 block w-full rounded-lg border border-slate-300 px-2 py-2 text-sm">
                        </label>
                        <label class="block text-sm font-semibold text-slate-700">Expiry date
                            <input type="date" name="ends_at" value="{{ old('ends_at', $endsAt?->toDateString()) }}" required class="mt-1 block w-full rounded-lg border border-slate-300 px-2 py-2 text-sm">
                        </label>
                    </div>
                    <button class="rounded-lg bg-[#1447a6] px-4 py-2 text-sm font-semibold text-white">Save dates</button>
                </form>
                <form method="POST" action="{{ route('admin.users.manage.access', $user) }}" class="mt-4 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="paused" value="{{ $accessStatus === 'Paused' ? '0' : '1' }}">
                    <button class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-semibold text-amber-800">{{ $accessStatus === 'Paused' ? 'Resume subscription' : 'Pause subscription' }}</button>
                    <p class="text-xs text-slate-600">Pausing blocks access until it is resumed.</p>
                </form>
            </section>
            <section class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5">
                <h2 class="text-lg font-bold text-slate-900">Account permissions</h2>
                <form method="POST" action="{{ route('admin.users.manage.update', $user) }}" class="mt-4 space-y-3">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="section" value="subscription">
                    @foreach(['premium' => 'Premium account', 'verified' => 'Verified user'] as $field => $label)
                        <label class="flex cursor-pointer items-center justify-between gap-3 text-sm font-medium text-slate-700">
                            <span>{{ $label }}</span>
                            <input type="hidden" name="{{ $field }}" value="0">
                            <input type="checkbox" role="switch" name="{{ $field }}" value="1" @checked(old($field, $user->$field)) class="peer sr-only">
                            <span aria-hidden="true" class="relative h-6 w-11 shrink-0 rounded-full bg-slate-300 transition-colors after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow after:transition-transform peer-checked:bg-[#1447a6] peer-checked:after:translate-x-5 peer-focus-visible:ring-2 peer-focus-visible:ring-blue-400 peer-focus-visible:ring-offset-2"></span>
                        </label>
                    @endforeach
                    <button class="rounded-lg bg-[#1447a6] px-4 py-2 text-sm font-semibold text-white">Save permissions</button>
                </form>
            </section>
        </div>
        @unless(auth()->user()->is($user))
            <details class="rounded-xl border border-slate-200 bg-white p-4">
                <summary class="cursor-pointer text-sm font-semibold text-red-700">Delete user</summary>
                <form method="POST" action="{{ route('admin.users.manage.destroy', $user) }}" class="mt-3 flex flex-wrap items-center gap-3">
                    @csrf
                    @method('DELETE')
                    <p class="text-sm text-slate-600">Confirm deletion of {{ $user->email }}.</p>
                    <button class="rounded-lg bg-red-700 px-3 py-2 text-sm font-semibold text-white">Confirm deletion</button>
                </form>
            </details>
        @endunless
    @endif
</div>
@endsection
