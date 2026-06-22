@extends('layouts.member')

@section('content')
<section class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white py-12 px-4 min-h-screen">
    <div class="max-w-6xl mx-auto space-y-8">

        <!-- Page Header -->
        <div class="text-center max-w-2xl mx-auto space-y-3">
            <div class="inline-flex p-3.5 bg-indigo-50 dark:bg-indigo-900/30 rounded-2xl text-indigo-600 dark:text-indigo-400 text-2xl">
                <i class="fa-solid fa-user-check"></i>
            </div>
            <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                Confirm Your Live Session Slot
            </h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm sm:text-base">
                Please review the session details, rules, and guidelines before confirming your booking.
            </p>
        </div>

        <!-- Main Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">

            <!-- Left Side: Session Details & Rules (2/5 Width) -->
            <div class="flex flex-col gap-6">

                <!-- Session Details Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="flex items-stretch gap-5">
                        <div class="flex-1 min-w-0">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-[11px] font-bold uppercase tracking-wider mb-4">
                                Live Session
                            </span>

                            <h2 class="text-xl font-bold text-gray-900 dark:text-white leading-tight mb-3">
                                {{ $liveshow->title }}
                            </h2>

                            <div class="flex flex-col gap-1.5 text-sm text-gray-500 dark:text-gray-400"
                                 data-utc-start="{{ \Carbon\Carbon::parse($liveshow->start_time, 'Africa/Lagos')->setTimezone('UTC')->toIso8601String() }}">
                                <div class="flex items-center gap-2">
                                    <i class="fa-regular fa-calendar text-indigo-500"></i>
                                    <span id="session-local-date">{{ \Carbon\Carbon::parse($liveshow->start_time)->format('M d, Y') }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fa-regular fa-clock text-indigo-500"></i>
                                    <span id="session-local-time">{{ \Carbon\Carbon::parse($liveshow->start_time)->format('H:i') }} (WAT)</span>
                                </div>
                            </div>
                        </div>

                        <div class="w-px bg-gray-100 dark:bg-gray-700"></div>

                        <div class="flex-shrink-0 flex flex-col items-center justify-center text-center px-2">
                            <i class="fa-solid fa-users text-indigo-400 mb-1.5"></i>
                            <p class="text-base font-bold text-gray-900 dark:text-white leading-none">
                                {{ $bookingsCount }} / {{ $liveshow->max_slots }}
                            </p>
                            <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-3">Seats Filled</p>

                            <div class="flex -space-x-2 overflow-hidden">
                                @foreach($bookedUsers as $u)
                                    @php
                                        $avatarUrl = $u->passport
                                            ? (str_starts_with($u->passport, 'http') ? $u->passport : asset($u->passport))
                                            : '/images/default-avatar.png';
                                    @endphp
                                    <img class="inline-block h-7 w-7 rounded-full ring-2 ring-white dark:ring-gray-800 object-cover"
                                         src="{{ $avatarUrl }}"
                                         alt="{{ $u->first_name }}"
                                         title="{{ $u->first_name }} {{ $u->last_name }}"
                                         onerror="this.src='/images/default-avatar.png';">
                                @endforeach
                                @if($liveshow->max_slots > $bookingsCount)
                                    <div class="inline-flex items-center justify-center h-7 w-7 rounded-full ring-2 ring-white dark:ring-gray-800 bg-indigo-100 text-indigo-600 text-[10px] font-bold">
                                        +{{ $liveshow->max_slots - $bookingsCount }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rules Box -->
                <div class="flex-1 bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 space-y-4">
                    <div class="flex items-center gap-2 border-b border-gray-100 dark:border-gray-700 pb-3">
                        <i class="fa-solid fa-shield-halved text-indigo-500"></i>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Live Session Rules</h3>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        To ensure a productive and respectful session for everyone, please follow these rules:
                    </p>

                    <ul class="space-y-4">
                        <li class="flex gap-3">
                            <div class="flex-shrink-0 w-7 h-7 bg-indigo-50 dark:bg-indigo-900/30 rounded-full flex items-center justify-center text-indigo-500 text-xs">
                                <i class="fa-solid fa-clock"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Be On Time</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Join the session a few minutes early to set up and avoid missing important time.</p>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <div class="flex-shrink-0 w-7 h-7 bg-indigo-50 dark:bg-indigo-900/30 rounded-full flex items-center justify-center text-indigo-500 text-xs">
                                <i class="fa-solid fa-video"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Keep Your Camera On</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Having your camera on helps create a more personal and engaging experience.</p>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <div class="flex-shrink-0 w-7 h-7 bg-indigo-50 dark:bg-indigo-900/30 rounded-full flex items-center justify-center text-indigo-500 text-xs">
                                <i class="fa-solid fa-microphone-slash"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Mute When Not Speaking</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Please mute your microphone when you're not speaking to avoid background noise.</p>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <div class="flex-shrink-0 w-7 h-7 bg-indigo-50 dark:bg-indigo-900/30 rounded-full flex items-center justify-center text-indigo-500 text-xs">
                                <i class="fa-solid fa-file-signature"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Come Prepared</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Review your progress and come with any questions you'd like to ask.</p>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <div class="flex-shrink-0 w-7 h-7 bg-indigo-50 dark:bg-indigo-900/30 rounded-full flex items-center justify-center text-indigo-500 text-xs">
                                <i class="fa-solid fa-heart"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Be Respectful</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Be respectful to the instructor and other members. This is a positive learning space.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Right Side: Checklists, Tutorials & Confirmation Form (3/5 Width) -->
            <div class="flex flex-col gap-6">

                <!-- Watch Tutorial Section -->
                <div class="bg-indigo-50/60 dark:bg-gray-800 rounded-2xl p-6 border border-indigo-100 dark:border-gray-700 shadow-sm flex flex-col md:flex-row items-center gap-5">
                    <div class="w-14 h-14 bg-white text-indigo-600 rounded-xl border-2 border-indigo-200 flex items-center justify-center text-2xl flex-shrink-0">
                        <i class="fa-solid fa-circle-play"></i>
                    </div>
                    <div class="flex-grow text-center md:text-left">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Watch the tutorial before your session</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                            We highly recommend watching our tutorial on how to set up for a live session.
                            It will help you have a smooth and successful experience.
                        </p>
                    </div>
                    <a href="/member/community/space/lessons"
                       class="px-5 py-2.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs font-bold rounded-xl shadow-sm border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition flex items-center gap-2 flex-shrink-0 whitespace-nowrap">
                        Watch Tutorial
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                    </a>
                </div>

                <!-- Guidelines Checklist Card -->
                <div x-data="{ check1: true, check2: true, check3: true }"
                     class="flex-1 flex flex-col justify-between bg-white dark:bg-gray-800 rounded-2xl p-7 shadow-sm border border-gray-100 dark:border-gray-700 space-y-6">

                    <!-- Checklist Options -->
                    <div class="space-y-3">
                        <!-- Box 1 -->
                        <div class="flex items-start gap-3.5">
                            <input type="checkbox" x-model="check1" disabled class="sr-only">
                            <span class="mt-0.5 flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center transition-colors"
                                  :class="check1 ? 'bg-indigo-600' : 'bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600'">
                                <i class="fa-solid fa-check text-white text-[10px]" x-show="check1"></i>
                            </span>
                            <div>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">Review Your Session Details</span>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Please confirm the date, time, and session name are correct.</p>
                            </div>
                        </div>

                        <!-- Box 2 -->
                        <div class="flex items-start gap-3.5">
                            <input type="checkbox" x-model="check2" disabled class="sr-only">
                            <span class="mt-0.5 flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center transition-colors"
                                  :class="check2 ? 'bg-indigo-600' : 'bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600'">
                                <i class="fa-solid fa-check text-white text-[10px]" x-show="check2"></i>
                            </span>
                            <div>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">Read and Understand the Rules</span>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Make sure you agree to follow the live session rules for a productive experience.</p>
                            </div>
                        </div>

                        <!-- Box 3 -->
                        <div class="flex items-start gap-3.5">
                            <input type="checkbox" x-model="check3" disabled class="sr-only">
                            <span class="mt-0.5 flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center transition-colors"
                                  :class="check3 ? 'bg-indigo-600' : 'bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600'">
                                <i class="fa-solid fa-check text-white text-[10px]" x-show="check3"></i>
                            </span>
                            <div>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">Prepare for a Great Session</span>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Ensure you have a stable internet connection, a quiet space, and any materials you need.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Booking Form -->
                    <form action="{{ route('member.live-session.book', $liveshow->id) }}" method="POST" class="space-y-3">
                        @csrf

                        <button type="submit"
                                :disabled="!check1 || !check2 || !check3"
                                class="w-full py-4 rounded-xl text-white font-bold text-sm uppercase tracking-widest transition-all shadow-sm flex items-center justify-center gap-2.5 disabled:opacity-50 disabled:cursor-not-allowed"
                                :class="(check1 && check2 && check3) ? 'bg-indigo-600 hover:bg-indigo-700 shadow-indigo-200' : 'bg-gray-300 dark:bg-gray-700 dark:text-gray-400 shadow-none'">
                            <i class="fa-solid fa-calendar-check"></i>
                            Confirm &amp; Book My Slot
                        </button>
                    </form>

                    <p class="flex items-center justify-center gap-1.5 text-center text-[11px] text-gray-400">
                        <i class="fa-solid fa-lock text-[10px]"></i>
                        By confirming, you agree to follow the Live Session Rules.
                    </p>

                </div>

            </div>

        </div>

    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const wrapper = document.querySelector('[data-utc-start]');
        const dateEl = document.getElementById('session-local-date');
        const timeEl = document.getElementById('session-local-time');
        if (!wrapper || !dateEl || !timeEl) return;

        try {
            const utcStart = wrapper.getAttribute('data-utc-start');
            const date = new Date(utcStart);
            const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

            const localDate = new Intl.DateTimeFormat('en-US', {
                timeZone: userTimezone,
                month: 'short',
                day: '2-digit',
                year: 'numeric',
            }).format(date);

            const localTime = new Intl.DateTimeFormat('en-US', {
                timeZone: userTimezone,
                hour: '2-digit',
                minute: '2-digit',
                hour12: false,
            }).format(date);

            const tzLabel = new Intl.DateTimeFormat('en-US', {
                timeZone: userTimezone,
                timeZoneName: 'short',
            }).formatToParts(date).find(p => p.type === 'timeZoneName')?.value;

            dateEl.textContent = localDate;
            timeEl.textContent = `${localTime} (${tzLabel || userTimezone})`;
        } catch (e) {
            // Leave the server-rendered WAT fallback in place if anything goes wrong.
        }
    });
</script>
@endsection
