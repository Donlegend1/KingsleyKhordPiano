@extends('layouts.member')

@section('content')
<section x-data="bookingModal()" class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white py-12 px-4 min-h-screen">
    <div class="max-w-6xl mx-auto space-y-6">

        <!-- Page Header -->
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-[11px] font-bold uppercase tracking-wider mb-3">
                Group Coaching
            </span>
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                {{ $liveshow->title }}
            </h1>
            <p class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-gray-500 dark:text-gray-400 mt-1.5"
               data-utc-start="{{ \Carbon\Carbon::parse($liveshow->start_time, 'Africa/Lagos')->setTimezone('UTC')->toIso8601String() }}">
                <i class="fa-regular fa-calendar text-blue-500"></i>
                <span id="session-local-date">{{ \Carbon\Carbon::parse($liveshow->start_time)->format('D, M d') }}</span>
                <span>&middot;</span>
                <span id="session-local-time">{{ \Carbon\Carbon::parse($liveshow->start_time)->format('g:i A') }} (WAT)</span>
            </p>
        </div>

        <!-- Group Session Notice -->
        <div class="flex items-start gap-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-900/40 rounded-2xl p-4">
            <i class="fa-solid fa-circle-info text-amber-500 mt-0.5"></i>
            <p class="text-sm text-gray-700 dark:text-gray-300">
                <span class="font-bold text-gray-900 dark:text-white">This is a Group Live Session — not a 1-on-1 call.</span>
                You'll be joining other members in a shared coaching call. This is your monthly accountability and review session — a space to discuss your progress, ask questions, and learn alongside peers on the same journey.
            </p>
        </div>

        <!-- Main Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">

            <!-- Left Side: Rules -->
            <div class="flex flex-col">

                <!-- Rules Box -->
                <div class="flex-1 bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 space-y-4">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Live Session Rules</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            To ensure a productive session for everyone, please follow these guidelines:
                        </p>
                    </div>

                    <ul class="space-y-4">
                        <li class="flex gap-3">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-50 dark:bg-blue-900/30 rounded-lg flex items-center justify-center text-blue-500 text-sm">
                                <i class="fa-solid fa-clock"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Be On Time</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Join a few minutes early to get settled. The session starts promptly and important content is covered from the beginning.</p>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-50 dark:bg-blue-900/30 rounded-lg flex items-center justify-center text-blue-500 text-sm">
                                <i class="fa-solid fa-video"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Keep Your Camera On</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Having your camera on helps create a more personal and connected experience for the whole group.</p>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-50 dark:bg-blue-900/30 rounded-lg flex items-center justify-center text-blue-500 text-sm">
                                <i class="fa-solid fa-microphone-slash"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Mute When Not Speaking</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Please mute your microphone when you're not speaking to avoid background noise for other members.</p>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-50 dark:bg-blue-900/30 rounded-lg flex items-center justify-center text-blue-500 text-sm">
                                <i class="fa-solid fa-file-signature"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Come Prepared</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Review your roadmap progress and come with specific questions or updates you want to share with the group.</p>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-50 dark:bg-blue-900/30 rounded-lg flex items-center justify-center text-blue-500 text-sm">
                                <i class="fa-solid fa-handshake"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Be Respectful</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">This is a supportive learning space. Be respectful to the instructor and your fellow members at all times.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Right Side: Tutorial & Confirmation Form -->
            <div class="flex flex-col gap-6">

                <!-- Session Guide Video Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">Session Setup Guide</h3>
                    <div class="aspect-video rounded-xl overflow-hidden bg-black">
                        <iframe
                            src="https://www.youtube.com/embed/b0BQxVdg9Go?rel=0&modestbranding=1"
                            title="How to set up for your live session"
                            class="w-full h-full"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>

                <!-- Confirm & Book Card -->
                <div class="flex-1 flex flex-col justify-center bg-white dark:bg-gray-800 rounded-2xl p-7 shadow-sm border border-gray-100 dark:border-gray-700 space-y-3">
                    <form action="{{ route('member.live-session.book', $liveshow->id) }}" method="POST" class="space-y-3" @submit.prevent="submitBooking">
                        @csrf

                        <button type="submit"
                                :disabled="submitting"
                                class="w-full py-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm transition-all shadow-sm flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                            <span x-show="!submitting">Confirm &amp; Book My Slot</span>
                            <span x-show="submitting">Booking your slot…</span>
                            <i class="fa-solid fa-chevron-right text-xs" x-show="!submitting"></i>
                        </button>
                        <p x-show="errorMsg" x-text="errorMsg" x-cloak class="text-center text-sm text-red-500"></p>
                    </form>

                    <p class="flex items-center justify-center gap-1.5 text-center text-[11px] text-gray-400">
                        By confirming, you agree to follow the Live Session Rules.
                    </p>

                </div>

            </div>

        </div>

    </div>

    <!-- Booking Confirmed Modal -->
    <div x-show="showModal" x-cloak
         class="fixed inset-0 z-[100] flex items-center justify-center p-4"
         style="background: rgba(15, 23, 42, 0.55);">
        <div x-show="showModal"
             x-transition
             class="bg-white dark:bg-gray-800 rounded-2xl max-w-sm w-full p-7 text-center shadow-xl">

            <div class="mx-auto w-14 h-14 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center mb-4">
                <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </div>

            <h2 class="text-lg font-bold text-gray-900 dark:text-white">You're Booked!</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" x-text="bookedTitle"></p>

            <div class="mt-5 bg-gray-50 dark:bg-gray-700/40 rounded-xl p-4 space-y-2.5 text-left">
                <div class="flex items-center gap-2.5 text-sm text-gray-800 dark:text-gray-200">
                    <i class="fa-regular fa-calendar text-blue-500 w-4"></i>
                    <span x-text="bookedDate"></span>
                </div>
                <div class="flex items-center gap-2.5 text-sm text-gray-800 dark:text-gray-200">
                    <i class="fa-regular fa-clock text-blue-500 w-4"></i>
                    <span x-text="bookedTime"></span>
                </div>
            </div>

            <div class="mt-5 space-y-2">
                <a :href="googleCalUrl" target="_blank" rel="noopener noreferrer"
                   class="flex items-center justify-center gap-2 text-sm font-semibold text-blue-600 border border-blue-200 rounded-lg py-2.5 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">
                    <i class="fa-regular fa-calendar-plus"></i>
                    Add to Google Calendar
                </a>
                <a :href="outlookCalUrl" target="_blank" rel="noopener noreferrer"
                   class="flex items-center justify-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-600 rounded-lg py-2.5 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <i class="fa-regular fa-calendar-plus"></i>
                    Add to Outlook Calendar
                </a>
            </div>

            <a href="/member/live-session" class="block mt-5 text-sm font-semibold text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                Done
            </a>
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
                weekday: 'short',
                month: 'short',
                day: '2-digit',
            }).format(date);

            const localTime = new Intl.DateTimeFormat('en-US', {
                timeZone: userTimezone,
                hour: '2-digit',
                minute: '2-digit',
                hour12: true,
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

    function bookingModal() {
        return {
            submitting: false,
            showModal: false,
            errorMsg: '',
            bookedTitle: '',
            bookedDate: '',
            bookedTime: '',
            googleCalUrl: '#',
            outlookCalUrl: '#',

            async submitBooking(event) {
                this.submitting = true;
                this.errorMsg = '';
                const form = event.target;

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: new FormData(form),
                    });

                    const data = await response.json();

                    if (response.ok && data.status === 'success') {
                        const start = new Date(data.start_time_utc);
                        const end = new Date(start.getTime() + 60 * 60 * 1000);

                        this.bookedTitle = data.title;
                        this.bookedDate = start.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' });
                        this.bookedTime = start.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });

                        const toCalDate = (d) => d.toISOString().replace(/[-:]/g, '').split('.')[0] + 'Z';
                        const title = encodeURIComponent(data.title);
                        const details = encodeURIComponent('Your live group coaching session on Kingsley Khord Piano.');

                        this.googleCalUrl = 'https://calendar.google.com/calendar/render?action=TEMPLATE'
                            + '&text=' + title
                            + '&dates=' + toCalDate(start) + '/' + toCalDate(end)
                            + '&details=' + details;

                        this.outlookCalUrl = 'https://outlook.live.com/calendar/0/deeplink/compose'
                            + '?subject=' + title
                            + '&startdt=' + start.toISOString()
                            + '&enddt=' + end.toISOString()
                            + '&body=' + details
                            + '&path=/calendar/action/compose&rru=addevent';

                        this.showModal = true;
                    } else {
                        this.errorMsg = data.message || 'Something went wrong. Please try again.';
                    }
                } catch (e) {
                    this.errorMsg = 'Something went wrong. Please try again.';
                } finally {
                    this.submitting = false;
                }
            },
        };
    }
</script>
@endsection
