@php
    $noHeader = true;
@endphp
@extends('layouts.member')

@section('content')

<div
    x-data="{
        step: 1,
        totalSteps: 2,
        submitting: false,
        submitted: {{ $existingRequest && $existingRequest->status === 'pending' ? 'true' : 'false' }},
        showCalendly: false,
        youtubeLink: '',
        chordVocabulary: '',
        keyFluency: '',
        inspirationPianist: '',
        inspirationPianistOther: '',
        archetype: '',
        daysPerWeek: '',
        practiceTime: '',
        playingByEar: '',
        experience: '',
        styleFocus: '',
        primaryGoal: '',
        details: '',
        errors: {},
        flashMessage: null,
        flashType: null,
        async submit() {
            this.submitting = true;
            this.errors = {};
            this.flashMessage = null;
            try {
                const res = await fetch('{{ route('member.personalized-guidance.submit') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({
                        youtube_link: this.youtubeLink,
                        chord_vocabulary: this.chordVocabulary,
                        key_fluency: this.keyFluency,
                        inspiration_pianist: this.inspirationPianist === 'Other' ? this.inspirationPianistOther : this.inspirationPianist,
                        archetype: this.archetype,
                        practice_days_per_week: this.daysPerWeek,
                        practice_time_per_day: this.practiceTime,
                        playing_by_ear: this.playingByEar,
                        experience_level: this.experience,
                        style_focus: this.styleFocus,
                        primary_goal: this.primaryGoal,
                    }),
                });
                const data = await res.json();
                if (res.status === 422) {
                    this.errors = data.errors || {};
                } else if (!res.ok) {
                    this.flashType = 'error';
                    this.flashMessage = data.message || 'Something went wrong. Please try again.';
                } else {
                    this.submitted = true;
                }
            } catch (e) {
                this.flashType = 'error';
                this.flashMessage = 'Something went wrong. Please try again.';
            } finally {
                this.submitting = false;
            }
        }
    }"
    class="min-h-screen bg-white flex flex-col"
>

    {{-- ── Page Header ── --}}
    <header class="bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between sticky top-0 z-50">
        <div class="flex items-center gap-3">
            <a href="{{ route('home') }}" class="flex items-center">
                <img src="/logo/logoblack.png" alt="Kingsley Khord" class="h-9 w-auto">
            </a>
        </div>

        <div class="flex items-center gap-4">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('home') }}" class="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-200 hover:bg-gray-50 text-gray-600 rounded-xl text-xs font-bold transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Exit
            </a>
            @if(Auth::user()->passport)
                <img src="{{ Auth::user()->passport }}" alt="Avatar" class="w-9 h-9 rounded-full object-cover ring-2 ring-gray-100">
            @else
                <div class="w-9 h-9 rounded-full bg-[#1447A6]/10 text-[#1447A6] flex items-center justify-center font-bold text-sm">
                    {{ strtoupper(substr(Auth::user()->first_name ?? Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
            @endif
        </div>
    </header>

    {{-- ── Content ── --}}
    <div class="flex-1 flex flex-col items-center px-4 py-10">
        <h1 class="flex items-center gap-2.5 text-2xl font-bold text-gray-900 mb-6 text-center">
            <span class="w-9 h-9 bg-[#1447A6]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 text-[#1447A6]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 3h12l4 6-10 12L2 9l4-6z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2 9h20M9 3l3 6 3-6M12 9v12"/>
                </svg>
            </span>
            Personalized Guidance
        </h1>
        <div class="w-full max-w-4xl border border-gray-200 rounded-2xl shadow-sm bg-white p-6 sm:p-8">

            <template x-if="submitted">
                <div class="relative flex flex-col items-center text-center py-8 px-2 overflow-hidden">
                    {{-- Decorative background accents --}}
                    <div class="absolute -top-20 -right-16 w-56 h-56 rounded-full bg-emerald-500/5"></div>
                    <div class="absolute -bottom-24 -left-16 w-56 h-56 rounded-full bg-[#1447A6]/5"></div>

                    <div class="relative w-20 h-20 rounded-full bg-emerald-50 flex items-center justify-center mb-6">
                        <div class="w-14 h-14 bg-emerald-500 rounded-full flex items-center justify-center shadow-lg shadow-emerald-500/30">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </div>
                    </div>

                    <h2 class="relative text-2xl font-extrabold text-gray-900 mb-2.5">Submitted Successfully!</h2>
                    <p class="relative text-sm text-gray-500 max-w-md mb-8 leading-relaxed">Your information has been submitted successfully. Kingsley will review it and email you your personalized roadmap shortly — please keep an eye on your inbox.</p>

                    <a href="{{ route('home') }}" class="relative inline-flex items-center justify-center gap-2 bg-[#1447A6] hover:bg-[#0F3A8A] text-white font-bold py-3.5 px-10 rounded-xl transition-all text-sm w-full sm:w-auto shadow-lg shadow-[#1447A6]/25 hover:shadow-xl hover:-translate-y-0.5">
                        Continue to Dashboard
                    </a>

                    <div class="relative flex items-center gap-3 w-full max-w-xs mt-8">
                        <div class="flex-1 h-px bg-gray-100"></div>
                        <span class="text-xs text-gray-400 font-semibold uppercase tracking-wide">or</span>
                        <div class="flex-1 h-px bg-gray-100"></div>
                    </div>

                    <button
                        type="button"
                        @click="showCalendly = true"
                        class="relative mt-8 inline-flex items-center justify-center gap-2 border border-gray-200 hover:border-gray-300 hover:bg-gray-50 text-gray-700 font-bold py-3 px-7 rounded-xl transition text-sm"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Book a Call Instead
                    </button>
                    <p class="relative text-xs text-gray-400 mt-3 max-w-sm">Prefer to talk it through? Book a live session and Kingsley will assess you in real time.</p>
                </div>
            </template>

            <template x-if="!submitted">
                <div>

                    @if (!auth()->user()->premium)
                        <div class="flex items-start gap-3 bg-amber-50 border border-amber-100 rounded-lg p-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-amber-800">This feature is for Premium members only.</p>
                                <p class="text-xs text-amber-700 mt-0.5">Upgrade your membership to request a personalized roadmap from Kingsley.</p>
                            </div>
                        </div>
                    @else

                        {{-- STEP 1: How it works --}}
                        <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">

                            <div class="flex items-start justify-between mb-1.5">
                                <h2 class="text-lg font-extrabold text-gray-900">How it works</h2>
                                <span class="text-[11px] font-bold text-[#1447A6] bg-[#1447A6]/10 px-2.5 py-1 rounded-full mt-0.5">Step 1 of 2</span>
                            </div>
                            <p class="text-sm text-gray-500 mb-3">Watch the video and follow the guide to upload your video and information.</p>
                            <div class="w-full bg-gray-100 rounded-full h-1.5 mb-6">
                                <div class="bg-[#1447A6] h-1.5 rounded-full w-1/2"></div>
                            </div>

                            {{-- Video embed --}}
                            <div class="relative w-full rounded-2xl overflow-hidden ring-1 ring-black/5 shadow-sm mb-6" style="padding-top: 56.25%;">
                                <iframe
                                    src="https://www.youtube.com/embed/WQ2U6zV-S1M"
                                    title="How it works"
                                    allow="autoplay; fullscreen; encrypted-media; picture-in-picture"
                                    allowfullscreen
                                    frameborder="0"
                                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
                                ></iframe>
                            </div>

                            <div class="flex justify-center">
                                <button
                                    type="button"
                                    @click="step = 2"
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-[#1447A6] hover:bg-[#0F3A8A] text-white font-bold py-3 px-10 rounded-xl transition text-sm shadow-lg shadow-[#1447A6]/20 hover:shadow-xl hover:-translate-y-0.5"
                                >
                                    Proceed
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>

                        </div>

                        {{-- STEP 2: Submission form --}}
                        <div x-show="step === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">

                            <div class="flex items-start justify-between mb-1.5">
                                <h2 class="text-lg font-extrabold text-gray-900">Submit information</h2>
                                <span class="text-[11px] font-bold text-[#1447A6] bg-[#1447A6]/10 px-2.5 py-1 rounded-full mt-0.5">Step 2 of 2</span>
                            </div>
                            <p class="text-sm text-gray-500 mb-3">Provide your information then we will take it from there</p>
                            <div class="w-full bg-gray-100 rounded-full h-1.5 mb-6">
                                <div class="bg-[#1447A6] h-1.5 rounded-full w-full"></div>
                            </div>

                            <div
                                x-show="flashMessage"
                                x-cloak
                                class="text-sm rounded-lg p-3 mb-4"
                                :class="flashType === 'error' ? 'bg-red-50 text-red-700 border border-red-100' : 'bg-green-50 text-green-700 border border-green-100'"
                                x-text="flashMessage"
                            ></div>

                            <form @submit.prevent="submit()" class="flex flex-col gap-5">

                                <div class="bg-gray-50/70 border border-gray-100 rounded-2xl p-5">
                                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Paste Assessment Video Link</label>
                                    <input
                                        type="text"
                                        required
                                        x-model="youtubeLink"
                                        class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#1447A6] focus:border-transparent"
                                    />
                                    <p class="text-xs text-red-500 mt-1" x-show="errors.youtube_link" x-text="errors.youtube_link && errors.youtube_link[0]"></p>
                                </div>

                                {{-- ── Section: Your Playing ── --}}
                                <div class="bg-gray-50/70 border border-gray-100 rounded-2xl p-5">
                                    <div class="flex items-center gap-2 mb-4">
                                        <span class="w-6 h-6 rounded-lg bg-[#1447A6]/10 text-[#1447A6] flex items-center justify-center flex-shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                                            </svg>
                                        </span>
                                        <p class="text-xs font-bold text-gray-700 uppercase tracking-wide">Your Playing</p>
                                    </div>

                                    <div class="flex flex-col gap-5">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                            <div>
                                                <label class="flex items-center gap-1.5 text-xs font-semibold text-gray-700 mb-1.5">
                                                    Archetype
                                                    <span class="relative group inline-flex">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-gray-400 cursor-help" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                                        </svg>
                                                        <span class="pointer-events-none absolute left-1/2 -translate-x-1/2 bottom-full mb-2 w-56 rounded-lg bg-gray-900 text-white text-[11px] leading-snug px-2.5 py-2 opacity-0 group-hover:opacity-100 transition-opacity duration-150 z-10 normal-case font-normal">
                                                            Whether you learned by ear on your own or through formal lessons — helps us tailor how we explain things.
                                                        </span>
                                                    </span>
                                                </label>
                                                <select x-model="archetype" required class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1447A6] focus:border-transparent bg-white">
                                                    <option value="">Select archetype</option>
                                                    <option>Self-taught</option>
                                                    <option>Formally Trained</option>
                                                </select>
                                            </div>

                                            <div>
                                                <label class="flex items-center gap-1.5 text-xs font-semibold text-gray-700 mb-1.5">
                                                    Experience
                                                    <span class="relative group inline-flex">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-gray-400 cursor-help" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                                        </svg>
                                                        <span class="pointer-events-none absolute left-1/2 -translate-x-1/2 bottom-full mb-2 w-56 rounded-lg bg-gray-900 text-white text-[11px] leading-snug px-2.5 py-2 opacity-0 group-hover:opacity-100 transition-opacity duration-150 z-10 normal-case font-normal">
                                                            How long you've been playing piano overall — helps us set the right starting point.
                                                        </span>
                                                    </span>
                                                </label>
                                                <select x-model="experience" required class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1447A6] focus:border-transparent bg-white">
                                                    <option value="">Select experience</option>
                                                    <option>Less than 6 months</option>
                                                    <option>6 months - 1 year</option>
                                                    <option>1 - 3 years</option>
                                                    <option>3+ years</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                            <div>
                                                <label class="flex items-center gap-1.5 text-xs font-semibold text-gray-700 mb-1.5">
                                                    Chord Vocabulary
                                                    <span class="relative group inline-flex">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-gray-400 cursor-help" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                                        </svg>
                                                        <span class="pointer-events-none absolute left-1/2 -translate-x-1/2 bottom-full mb-2 w-56 rounded-lg bg-gray-900 text-white text-[11px] leading-snug px-2.5 py-2 opacity-0 group-hover:opacity-100 transition-opacity duration-150 z-10 normal-case font-normal">
                                                            The most advanced type of chords you're already comfortable playing.
                                                        </span>
                                                    </span>
                                                </label>
                                                <select x-model="chordVocabulary" required class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1447A6] focus:border-transparent bg-white">
                                                    <option value="">Select chord vocabulary</option>
                                                    <option>Just triads (major/minor)</option>
                                                    <option>7th chords</option>
                                                    <option>9ths, 11ths &amp; extended chords</option>
                                                    <option>Passing &amp; reharmonized chords</option>
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Playing by Ear</label>
                                                <select x-model="playingByEar" required class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1447A6] focus:border-transparent bg-white">
                                                    <option value="">Select ability</option>
                                                    <option>I can't yet — I need it shown to me</option>
                                                    <option>I can figure out simple melodies</option>
                                                    <option>I can figure out full chord progressions</option>
                                                    <option>I can transcribe &amp; reharmonize by ear</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Can you play on all 12 keys?</label>
                                            <select x-model="keyFluency" required class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1447A6] focus:border-transparent bg-white">
                                                <option value="">Select ability</option>
                                                <option value="Yes I Can">Yes I Can — I can comfortably play in any of the 12 keys</option>
                                                <option value="I Know Some Keys">I Know Some Keys — I can play in a few but not all of them yet</option>
                                                <option value="No I Can't">No I Can't — I mostly stick to one or two familiar keys</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Which pianist's style do you want to play like?</label>
                                            <select x-model="inspirationPianist" required class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1447A6] focus:border-transparent bg-white">
                                                <option value="">Select a pianist</option>
                                                <option>Kelvin Bond</option>
                                                <option>Cory Henry</option>
                                                <option>Alain Merville</option>
                                                <option>Jason Tyson</option>
                                                <option>Mike Bereal</option>
                                                <option>Joshua Domfeh</option>
                                                <option>Jason White</option>
                                                <option>Glen Gibson Jr</option>
                                                <option>Joe Pryor</option>
                                                <option>Braxton Moore</option>
                                                <option>Kell Bailey</option>
                                                <option value="None">None</option>
                                                <option value="Other">Other (type your own)</option>
                                            </select>
                                            <input
                                                x-show="inspirationPianist === 'Other'"
                                                x-cloak
                                                type="text"
                                                :required="inspirationPianist === 'Other'"
                                                x-model="inspirationPianistOther"
                                                placeholder="Enter their name"
                                                class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm bg-white mt-2 focus:outline-none focus:ring-2 focus:ring-[#1447A6] focus:border-transparent"
                                            />
                                        </div>
                                    </div>
                                </div>

                                {{-- ── Section: Practice & Goals ── --}}
                                <div class="bg-gray-50/70 border border-gray-100 rounded-2xl p-5">
                                    <div class="flex items-center gap-2 mb-4">
                                        <span class="w-6 h-6 rounded-lg bg-[#1447A6]/10 text-[#1447A6] flex items-center justify-center flex-shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </span>
                                        <p class="text-xs font-bold text-gray-700 uppercase tracking-wide">Practice &amp; Goals</p>
                                    </div>

                                    <div class="flex flex-col gap-5">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">How many days in a week do you have for practice?</label>
                                                <select x-model="daysPerWeek" required class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1447A6] focus:border-transparent bg-white">
                                                    <option value="">Select days</option>
                                                    @for ($i = 1; $i <= 7; $i++)
                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">How much time do you have for practice each day?</label>
                                                <select x-model="practiceTime" required class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1447A6] focus:border-transparent bg-white">
                                                    <option value="">Select time</option>
                                                    <option>15 minutes</option>
                                                    <option>30 minutes</option>
                                                    <option>1 hour</option>
                                                    <option>2 hours</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="flex items-center gap-1.5 text-xs font-semibold text-gray-700 mb-1.5">
                                                Style Focus
                                                <span class="relative group inline-flex">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-gray-400 cursor-help" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                                    </svg>
                                                    <span class="pointer-events-none absolute left-1/2 -translate-x-1/2 bottom-full mb-2 w-56 rounded-lg bg-gray-900 text-white text-[11px] leading-snug px-2.5 py-2 opacity-0 group-hover:opacity-100 transition-opacity duration-150 z-10 normal-case font-normal">
                                                        The gospel style you want your roadmap and lesson picks built around.
                                                    </span>
                                                </span>
                                            </label>
                                            <select x-model="styleFocus" required class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1447A6] focus:border-transparent bg-white">
                                                <option value="">Select style</option>
                                                <option>Hymns &amp; Traditional</option>
                                                <option>Contemporary Gospel</option>
                                                <option>Praise &amp; Worship</option>
                                                <option>Neo-Soul Gospel / Runs &amp; Riffs</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Describe your Primary Goal</label>
                                            <textarea
                                                rows="7"
                                                required
                                                x-model="primaryGoal"
                                                placeholder="Type here..."
                                                class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm bg-white resize-none focus:outline-none focus:ring-2 focus:ring-[#1447A6] focus:border-transparent"
                                            ></textarea>
                                            <p class="text-xs text-red-500 mt-1" x-show="errors.primary_goal" x-text="errors.primary_goal && errors.primary_goal[0]"></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-center pt-1">
                                    <button
                                        type="submit"
                                        :disabled="submitting"
                                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-[#1447A6] hover:bg-[#0F3A8A] disabled:opacity-60 disabled:cursor-not-allowed text-white font-bold py-3 px-10 rounded-xl transition text-sm shadow-lg shadow-[#1447A6]/20 hover:shadow-xl hover:-translate-y-0.5"
                                    >
                                        <span x-text="submitting ? 'Submitting...' : 'Submit Now'"></span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="flex items-center gap-3">
                                    <div class="flex-1 h-px bg-gray-100"></div>
                                    <span class="text-xs text-gray-400 font-medium">or</span>
                                    <div class="flex-1 h-px bg-gray-100"></div>
                                </div>

                                <div class="flex justify-center">
                                    <button
                                        type="button"
                                        @click="showCalendly = true"
                                        class="inline-flex items-center justify-center gap-2 border border-gray-200 hover:bg-gray-50 text-gray-700 font-semibold py-2.5 px-6 rounded-xl transition text-sm"
                                    >
                                        Book a Call Instead
                                    </button>
                                </div>
                                <p class="text-xs text-gray-400 text-center -mt-2">Prefer to talk it through? Book a live session and Kingsley will assess you in real time.</p>

                                <div class="text-center">
                                    <button type="button" @click="step = 1" class="text-sm font-semibold text-gray-500 hover:text-gray-700">
                                        Back
                                    </button>
                                </div>
                            </form>

                        </div>

                    @endif

                </div>
            </template>

        </div>
    </div>

    {{-- ── Calendly Popup ── --}}
    <div
        x-show="showCalendly"
        x-cloak
        x-transition
        class="fixed inset-0 bg-black/50 z-[60] flex items-center justify-center p-4"
        @click.self="showCalendly = false"
    >
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-4xl overflow-hidden max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 flex-shrink-0">
                <p class="flex items-center gap-2 text-sm font-semibold text-gray-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#1447A6]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Schedule a Call
                </p>
                <button
                    type="button"
                    @click="showCalendly = false"
                    class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="overflow-y-auto">
                <iframe
                    :src="showCalendly ? '{{ route('member.live-coaching') }}?embed=1' : ''"
                    title="Book a live coaching session"
                    style="width:100%; height:80vh; border:0; display:block;"
                ></iframe>
            </div>
        </div>
    </div>

</div>

@endsection
