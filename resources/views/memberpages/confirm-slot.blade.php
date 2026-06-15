@extends('layouts.member')

@section('content')
<section class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white py-12 px-4 min-h-screen">
    <div class="max-w-6xl mx-auto space-y-8">
        
        <!-- Breadcrumb -->
        <nav class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
            <a href="/home" class="hover:text-blue-600">Dashboard</a>
            <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
            <a href="/member/live-session" class="hover:text-blue-600">Live Session</a>
            <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
            <span class="font-semibold text-gray-800 dark:text-white">Confirm Booking</span>
        </nav>

        <!-- Page Header -->
        <div class="text-center max-w-2xl mx-auto space-y-2">
            <div class="inline-flex p-3 bg-blue-100 dark:bg-blue-900/30 rounded-2xl text-blue-600 dark:text-blue-400 text-2xl">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <h1 class="text-3xl font-black tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                Confirm Your Live Session Slot
            </h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm sm:text-base">
                Please review the session details, rules, and guidelines before confirming your booking.
            </p>
        </div>

        <!-- Main Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 items-start">
            
            <!-- Left Side: Session Details & Rules (2/5 Width) -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Session Details Card -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-xl border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
                    <div class="absolute top-0 left-0 w-2 h-full bg-blue-600"></div>
                    <div class="space-y-6 pl-2">
                        
                        <!-- Badge -->
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-black uppercase tracking-wider border border-blue-100 dark:border-blue-800">
                                <i class="fa-solid fa-users text-[10px]"></i>
                                Live Session
                            </span>
                            
                            <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">
                                Max 5 Slots
                            </span>
                        </div>

                        <!-- Title -->
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white leading-tight">
                            {{ $liveshow->title }}
                        </h2>

                        <!-- Date & Time Row -->
                        <div class="grid grid-cols-2 gap-4 py-4 border-y border-gray-100 dark:border-gray-700">
                            <div class="flex items-center gap-2">
                                <div class="p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg text-blue-600 dark:text-blue-400">
                                    <i class="fa-regular fa-calendar"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] uppercase font-bold text-gray-400">Date</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ \Carbon\Carbon::parse($liveshow->start_time)->format('M d, Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg text-blue-600 dark:text-blue-400">
                                    <i class="fa-regular fa-clock"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] uppercase font-bold text-gray-400">Time</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ \Carbon\Carbon::parse($liveshow->start_time)->format('H:i') }} (WAT)
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Seats Count and Avatars -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-green-500"></i>
                                <span class="text-sm font-bold text-gray-700 dark:text-gray-300">
                                    {{ $bookingsCount }} / {{ $liveshow->max_slots }} Seats Filled
                                </span>
                            </div>
                            
                            <!-- Avatars overlapping -->
                            <div class="flex -space-x-2 overflow-hidden">
                                @foreach($bookedUsers as $u)
                                    @php
                                        $avatarUrl = $u->passport 
                                            ? (str_starts_with($u->passport, 'http') ? $u->passport : asset($u->passport)) 
                                            : '/images/default-avatar.png';
                                    @endphp
                                    <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white dark:ring-gray-800 object-cover" 
                                         src="{{ $avatarUrl }}" 
                                         alt="{{ $u->first_name }}"
                                         title="{{ $u->first_name }} {{ $u->last_name }}"
                                         onerror="this.src='/images/default-avatar.png';">
                                @endforeach
                                @if($liveshow->max_slots > $bookingsCount)
                                    <div class="inline-flex items-center justify-center h-8 w-8 rounded-full ring-2 ring-white dark:ring-gray-800 bg-blue-100 text-blue-600 text-xs font-black">
                                        +{{ $liveshow->max_slots - $bookingsCount }}
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Rules Box -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-xl border border-gray-100 dark:border-gray-700 space-y-4">
                    <div class="flex items-center gap-2 border-b border-gray-100 dark:border-gray-700 pb-3">
                        <i class="fa-solid fa-shield-halved text-blue-600"></i>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Live Session Rules</h3>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        To ensure a productive and respectful session for everyone, please follow these rules:
                    </p>
                    
                    <ul class="space-y-4">
                        <li class="flex gap-3">
                            <div class="flex-shrink-0 w-6 h-6 bg-blue-50 dark:bg-blue-900/30 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-400 text-xs">
                                <i class="fa-solid fa-clock"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Be On Time</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Join the session a few minutes early to set up and avoid missing important time.</p>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <div class="flex-shrink-0 w-6 h-6 bg-blue-50 dark:bg-blue-900/30 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-400 text-xs">
                                <i class="fa-solid fa-video"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Keep Your Camera On</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Having your camera on helps create a more personal and engaging experience.</p>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <div class="flex-shrink-0 w-6 h-6 bg-blue-50 dark:bg-blue-900/30 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-400 text-xs">
                                <i class="fa-solid fa-microphone-slash"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Mute When Not Speaking</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Please mute your microphone when you're not speaking to avoid background noise.</p>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <div class="flex-shrink-0 w-6 h-6 bg-blue-50 dark:bg-blue-900/30 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-400 text-xs">
                                <i class="fa-solid fa-file-signature"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Come Prepared</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Review your progress and come with any questions you'd like to ask.</p>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <div class="flex-shrink-0 w-6 h-6 bg-blue-50 dark:bg-blue-900/30 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-400 text-xs">
                                <i class="fa-solid fa-heart"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Be Respectful</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Be respectful to the instructor and other members. Share a positive learning space.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Right Side: Checklists, Tutorials & Confirmation Form (3/5 Width) -->
            <div class="lg:col-span-3 space-y-6">
                
                <!-- Watch Tutorial Section -->
                <div class="bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-gray-800 dark:to-gray-800 rounded-3xl p-6 border border-indigo-100/50 dark:border-gray-700 shadow-xl flex flex-col md:flex-row items-center gap-6">
                    <div class="w-16 h-16 bg-blue-600 text-white rounded-2xl flex items-center justify-center text-3xl shadow-lg shadow-blue-500/20 flex-shrink-0">
                        <i class="fa-solid fa-circle-play"></i>
                    </div>
                    <div class="flex-grow text-center md:text-left space-y-2">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Watch the tutorial before your session</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                            We highly recommend watching our tutorial on how to set up for a live session, to actively prepare for a smooth and successful experience.
                        </p>
                    </div>
                    <a href="/member/community/space/lessons" 
                       class="px-5 py-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs font-black uppercase tracking-wider rounded-xl shadow-md border border-gray-100 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition flex items-center gap-2 flex-shrink-0">
                        Watch Tutorial
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                    </a>
                </div>

                <!-- Guidelines Checklist Card -->
                <div x-data="{ check1: false, check2: false, check3: false }" 
                     class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-xl border border-gray-100 dark:border-gray-700 space-y-8">
                    
                    <div class="space-y-4">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-list-check text-blue-600"></i>
                            Preparation Checklist
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Check all boxes below to confirm you are ready for this live session.
                        </p>
                    </div>

                    <!-- Checklist Options -->
                    <div class="space-y-4">
                        <!-- Box 1 -->
                        <label class="flex items-start gap-4 p-4 rounded-2xl border transition-all cursor-pointer select-none"
                               :class="check1 ? 'bg-blue-50/50 border-blue-200 dark:bg-blue-900/10 dark:border-blue-800' : 'bg-gray-50/50 border-gray-100 dark:bg-gray-700/20 dark:border-gray-700'">
                            <input type="checkbox" x-model="check1" class="mt-1 w-5 h-5 rounded text-blue-600 focus:ring-blue-500 border-gray-300">
                            <div class="ml-3">
                                <span class="text-sm font-bold text-gray-900 dark:text-white">Review Your Session Details</span>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Please confirm the date, time, and session name are correct.</p>
                            </div>
                        </label>

                        <!-- Box 2 -->
                        <label class="flex items-start gap-4 p-4 rounded-2xl border transition-all cursor-pointer select-none"
                               :class="check2 ? 'bg-blue-50/50 border-blue-200 dark:bg-blue-900/10 dark:border-blue-800' : 'bg-gray-50/50 border-gray-100 dark:bg-gray-700/20 dark:border-gray-700'">
                            <input type="checkbox" x-model="check2" class="mt-1 w-5 h-5 rounded text-blue-600 focus:ring-blue-500 border-gray-300">
                            <div class="ml-3">
                                <span class="text-sm font-bold text-gray-900 dark:text-white">Read and Understand the Rules</span>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Make sure you agree to follow the live session rules for a productive experience.</p>
                            </div>
                        </label>

                        <!-- Box 3 -->
                        <label class="flex items-start gap-4 p-4 rounded-2xl border transition-all cursor-pointer select-none"
                               :class="check3 ? 'bg-blue-50/50 border-blue-200 dark:bg-blue-900/10 dark:border-blue-800' : 'bg-gray-50/50 border-gray-100 dark:bg-gray-700/20 dark:border-gray-700'">
                            <input type="checkbox" x-model="check3" class="mt-1 w-5 h-5 rounded text-blue-600 focus:ring-blue-500 border-gray-300">
                            <div class="ml-3">
                                <span class="text-sm font-bold text-gray-900 dark:text-white">Prepare for a Great Session</span>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Ensure you have a stable internet connection, a quiet space, and any materials you need.</p>
                            </div>
                        </label>
                    </div>

                    <!-- Submit Booking Form -->
                    <form action="{{ route('member.live-session.book', $liveshow->id) }}" method="POST" class="space-y-4">
                        @csrf
                        
                        <button type="submit" 
                                :disabled="!check1 || !check2 || !check3"
                                class="w-full py-4 rounded-2xl text-white font-black text-sm uppercase tracking-widest transition-all shadow-xl flex items-center justify-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed"
                                :class="(check1 && check2 && check3) ? 'bg-blue-600 hover:bg-blue-700 shadow-blue-500/20' : 'bg-gray-300 dark:bg-gray-700 dark:text-gray-400 shadow-none'">
                            <i class="fa-solid fa-lock-open text-base" x-show="check1 && check2 && check3"></i>
                            <i class="fa-solid fa-lock text-base" x-show="!check1 || !check2 || !check3"></i>
                            Confirm & Book My Slot
                        </button>
                    </form>

                    <p class="text-center text-[10px] text-gray-400">
                        By confirming, you agree to follow the Live Session Rules.
                    </p>

                </div>

            </div>
            
        </div>

    </div>
</section>
@endsection
