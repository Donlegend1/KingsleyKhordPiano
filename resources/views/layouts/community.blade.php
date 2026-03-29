<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="themeToggle()" :class="{ 'dark': isDark }" x-init="init()">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="KingsleyKhord community platform for musicians.">
    <meta name="theme-color" content="#302f2c">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Preconnects -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Icons / CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet"/>

    <!-- Deferred JS -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js" defer></script>

    <!-- App -->
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>

<body x-data="{ isDark: false, showSidebar: false }" x-init class="bg-gray-100 dark:bg-gray-900 h-screen overflow-hidden">

<div class="flex h-screen">

    {{-- ===================== SIDEBAR ===================== --}}
    <aside
        x-cloak
        x-show="showSidebar || window.innerWidth >= 768"
        @click.away="if (window.innerWidth < 768) showSidebar = false"
        :class="{ '-translate-x-full': !showSidebar && window.innerWidth < 768 }"
        class="fixed inset-y-0 left-0 z-40 w-64 bg-[#FAFAFA] dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform transition-transform duration-200 md:relative md:translate-x-0"
    >
        <div class="px-5 py-4 font-sans h-full overflow-y-auto scrollbar-hide">

            <!-- Logo -->
            <div class="hidden md:flex justify-center mb-10 mt-2">
                <a href="/member/community">
                    <img src="/logo/logoblack.png" alt="Logo" class="h-12 w-auto dark:hidden">
                    <img src="/logo/logowhite.webp" alt="Logo" class="h-12 w-auto hidden dark:block">
                </a>
            </div>

            <!-- COMMUNITY -->
            <div class="mb-6 mt-16 md:mt-0">
                <h3 class="text-[#9CA3AF] dark:text-gray-400 text-[11px] font-bold tracking-widest mb-2">COMMUNITY</h3>
                <div class="space-y-1">
                    <a href="/member/community"
                       class="flex items-center gap-3 px-2 py-2.5 rounded-lg text-sm font-medium text-[#6B7280] dark:text-gray-300 hover:bg-[#F3F4F6] dark:hover:bg-gray-700 transition-colors {{ Request::is('member/community') ? 'bg-[#F3F4F6] dark:bg-gray-700' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"/>
                        </svg>
                        Activity Feed
                    </a>
                    <a href="/member/community/space/progress-report"
                       class="flex items-center gap-3 px-2 py-2.5 rounded-lg text-sm font-medium text-[#6B7280] dark:text-gray-300 hover:bg-[#F3F4F6] dark:hover:bg-gray-700 transition-colors {{ Request::is('member/community/space/progress-report') ? 'bg-[#F3F4F6] dark:bg-gray-700' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Progress Report
                    </a>
                    <a href="/member/community/space/lessons"
                       class="flex items-center gap-3 px-2 py-2.5 rounded-lg text-sm font-medium text-[#6B7280] dark:text-gray-300 hover:bg-[#F3F4F6] dark:hover:bg-gray-700 transition-colors {{ Request::is('member/community/space/lessons') ? 'bg-[#F3F4F6] dark:bg-gray-700' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        Lessons
                    </a>
                </div>
            </div>

            <!-- MEMBERS ONLY -->
            <div class="mb-6">
                <h3 class="text-[#9CA3AF] dark:text-gray-400 text-[11px] font-bold tracking-widest mb-2">MEMBERS ONLY</h3>
                <div class="space-y-1">
                    <a href="/member/community/space/exclusive-feed"
                       class="flex items-center gap-3 px-2 py-2.5 rounded-lg text-sm font-medium text-[#6B7280] dark:text-gray-300 hover:bg-[#F3F4F6] dark:hover:bg-gray-700 transition-colors {{ Request::is('member/community/space/exclusive-feed') ? 'bg-[#F3F4F6] dark:bg-gray-700' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Exclusive Feed
                    </a>
                    <a href="/member/community/space/pdf-downloads"
                       class="flex items-center gap-3 px-2 py-2.5 rounded-lg text-sm font-medium text-[#6B7280] dark:text-gray-300 hover:bg-[#F3F4F6] dark:hover:bg-gray-700 transition-colors {{ Request::is('member/community/space/pdf-downloads') ? 'bg-[#F3F4F6] dark:bg-gray-700' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        PDF Downloads
                    </a>
                    <a href="/member/community/space/audio-downloads"
                       class="flex items-center gap-3 px-2 py-2.5 rounded-lg text-sm font-medium text-[#6B7280] dark:text-gray-300 hover:bg-[#F3F4F6] dark:hover:bg-gray-700 transition-colors {{ Request::is('member/community/space/audio-downloads') ? 'bg-[#F3F4F6] dark:bg-gray-700' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                        </svg>
                        Audio Downloads
                    </a>
                    <a href="/member/community/space/midi-downloads"
                       class="flex items-center gap-3 px-2 py-2.5 rounded-lg text-sm font-medium text-[#6B7280] dark:text-gray-300 hover:bg-[#F3F4F6] dark:hover:bg-gray-700 transition-colors {{ Request::is('member/community/space/midi-downloads') ? 'bg-[#F3F4F6] dark:bg-gray-700' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14.23 12.004a2.236 2.236 0 0 1 2.235 2.236A2.236 2.236 0 0 1 14.23 16.476a2.236 2.236 0 0 1-2.235-2.236 2.236 2.236 0 0 1 2.235-2.236zm2.648-10.69c.366 0 .662.297.662.662v3.34c1.154.28 2.01 1.32 2.01 2.585a2.67 2.67 0 0 1-2.667 2.667h-4.666a2.67 2.67 0 0 1-2.667-2.667c0-1.265.856-2.305 2.01-2.585V1.976c0-.365.296-.662.662-.662h2.326zm-.662 4.666c-.735 0-1.333.598-1.333 1.333 0 .735.598 1.333 1.333 1.333h4.666c.735 0 1.333-.598 1.333-1.333 0-.735-.598-1.333-1.333-1.333h-4.666z"/>
                        </svg>
                        Midi Files
                    </a>
                </div>
            </div>

            <!-- QUICK LINKS — Mobile Only -->
            <div class="block md:hidden mb-6">
                <h3 class="text-[#9CA3AF] dark:text-gray-400 text-[11px] font-bold tracking-widest mb-2">QUICK LINKS</h3>
                <div class="space-y-1">
                    <a href="/home" class="flex items-center gap-3 px-2 py-2.5 rounded-lg text-sm font-medium text-[#6B7280] dark:text-gray-300 hover:bg-[#F3F4F6] dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"/>
                        </svg>
                        Dashboard
                    </a>
                    <a href="https://khordsounds.com/" target="_blank" class="flex items-center gap-3 px-2 py-2.5 rounded-lg text-sm font-medium text-[#6B7280] dark:text-gray-300 hover:bg-[#F3F4F6] dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        Shop Plugins
                    </a>
                    <a href="/member/live-session" class="flex items-center gap-3 px-2 py-2.5 rounded-lg text-sm font-medium text-[#6B7280] dark:text-gray-300 hover:bg-[#F3F4F6] dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        Live Sessions
                    </a>
                    <a href="/member/support" class="flex items-center gap-3 px-2 py-2.5 rounded-lg text-sm font-medium text-[#6B7280] dark:text-gray-300 hover:bg-[#F3F4F6] dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Support
                    </a>
                </div>
            </div>

            <!-- Logout -->
            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                <form method="POST" action="{{ route('community.logout') }}">
                    @csrf
                    <button type="submit" class="group flex items-center gap-3 w-full px-2 py-2.5 rounded-lg text-sm font-medium text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="m16 17 5-5-5-5"/>
                            <path d="M21 12H9"/>
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>

        </div>
    </aside>

    {{-- ===================== MAIN AREA ===================== --}}
    <div class="flex-1 flex flex-col min-w-0">

        <!-- Header -->
        <header class="bg-white dark:bg-gray-800 shadow sticky top-0 z-50">
            <div class="px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between">

                <!-- Mobile: Hamburger -->
                <button @click="showSidebar = !showSidebar" class="md:hidden text-gray-500 dark:text-gray-300 p-1">
                    <i class="fa fa-bars text-xl"></i>
                </button>

                <!-- Mobile: Logo -->
                <a href="/member/community" class="md:hidden">
                    <img src="/logo/logowhite.webp" alt="Logo" class="h-9 w-auto">
                </a>

                <!-- Desktop: Nav Links -->
                <nav class="hidden md:flex items-center gap-8">
                    <a href="/home" class="flex items-center gap-1.5 text-sm font-medium text-gray-500 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"/>
                        </svg>
                        Dashboard
                    </a>
                    <a href="https://khordsounds.com/" target="_blank" class="flex items-center gap-1.5 text-sm font-medium text-gray-500 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        Shop Plugins
                    </a>
                    <a href="/member/live-session" class="flex items-center gap-1.5 text-sm font-medium text-gray-500 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        Live Sessions
                    </a>
                    <a href="/member/support" class="flex items-center gap-1.5 text-sm font-medium text-gray-500 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Support
                    </a>
                </nav>

                <!-- Right: Actions -->
                <div class="flex items-center gap-2">

                    <!-- Theme Toggle -->
                    <button @click="toggle"
                        class="p-2 rounded-full text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                        aria-label="Toggle Dark Mode">
                        <svg x-show="!isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M12 3v1m0 16v1m8.66-12.66l-.71.71M4.05 19.95l-.71.71M21 12h-1M4 12H3m16.66 4.95l-.71-.71M4.05 4.05l-.71-.71M12 5a7 7 0 100 14 7 7 0 000-14z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <svg x-show="isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>

                    <!-- Notifications -->
                    @php
                        $notifications = auth()->user()->notifications()
                            ->where('data->data->section', \App\Enums\Notification\NotificationSectionEnum::COMMUNITY->value)
                            ->latest()
                            ->get();
                        $unreadCount = $notifications->whereNull('read_at')->count();
                    @endphp

                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button type="button" @click="open = !open"
                            class="relative p-2 rounded-full text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                            aria-label="Notifications">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 01-6 0v-1m6 0H9"
                                      stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            @if($unreadCount > 0)
                                <span class="absolute -top-0.5 -right-0.5 bg-red-500 text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1 leading-none">
                                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                                </span>
                            @endif
                        </button>

                        <!-- Dropdown -->
                        <div
                            x-show="open"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1"
                            style="display:none;"
                            class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden z-50"
                        >
                            <!-- Dropdown Header -->
                            <div class="flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">Notifications</span>
                                    @if($unreadCount > 0)
                                        <span class="text-xs font-semibold bg-red-100 text-red-600 dark:bg-red-900/40 dark:text-red-400 px-2 py-0.5 rounded-full">
                                            {{ $unreadCount }} new
                                        </span>
                                    @endif
                                </div>
                                @if($unreadCount > 0)
                                    <form method="POST" action="{{ route('notifications.markAllAsRead') }}">
                                        @csrf
                                        <button type="submit" class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium">
                                            Mark all read
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <!-- Notification Items -->
                            <div class="max-h-80 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($notifications as $notification)
                                @php
                                    $data      = $notification->data;
                                    $firstName = $data['data']['user'] ?? 'Someone';  // adjust to your actual key
                                    $postId    = null;
                                    // extract post ID from the URL
                                    if (!empty($data['data']['url'])) {
                                        preg_match('/\/post\/(\d+)/', $data['data']['url'], $m);
                                        $postId = $m[1] ?? null;
                                    }
                                    $type      = $data['data']['type'] ?? '';
                                    $avatar    = $data['data']['by_user_avatar'] ?? null;
                                    $isUnread  = is_null($notification->read_at);
                                    $initials  = strtoupper(substr($firstName, 0, 1));
                                    $url       = $data['data']['url'] ?? null;
                                @endphp

                                    <a href="{{ $url }}"
                                       class="flex items-start gap-3 px-4 py-3 transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/60 {{ $isUnread ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">

                                        <!-- Avatar -->
                                        <div class="flex-shrink-0 mt-0.5">
                                            @if(!empty($avatar))
                                                <img src="{{ $avatar }}" alt="{{ trim("$firstName $lastName") }}"
                                                     class="w-9 h-9 rounded-full object-cover ring-2 ring-white dark:ring-gray-800">
                                            @else
                                                <div class="w-9 h-9 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-xs font-bold text-indigo-600 dark:text-indigo-400 ring-2 ring-white dark:ring-gray-800">
                                                    {{ $initials }}
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Text -->
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-gray-800 dark:text-gray-200 leading-snug">
                                                <span class="font-semibold">{{ $firstName }}</span>
                                                @if($type === 'comment') commented on your post
                                                @elseif($type === 'reply') replied to your comment
                                                @elseif($type === 'like') liked your post
                                                @else interacted with your post
                                                @endif
                                            </p>
                                            <span class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 block">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </span>
                                        </div>

                                        <!-- Unread dot -->
                                        @if($isUnread)
                                            <span class="flex-shrink-0 mt-2 w-2 h-2 rounded-full bg-blue-500"></span>
                                        @endif
                                    </a>
                                @empty
                                    <div class="flex flex-col items-center justify-center py-10 px-4 text-center">
                                        <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 01-6 0v-1m6 0H9"/>
                                        </svg>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No notifications yet</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">You'll be notified when something happens</p>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Footer -->
                            @if($notifications->count() > 0)
                                <div class="px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border-t border-gray-100 dark:border-gray-700 text-center">
                                    <a href="/member/notifications" class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                        View all notifications
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900">
            @yield('content')
        </main>

    </div>
</div>

<script>
    function themeToggle() {
        return {
            isDark: false,
            showSidebar: false,
            init() {
                this.isDark = localStorage.getItem('theme') === 'dark'
                    || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);
                this.applyTheme();
            },
            toggle() {
                this.isDark = !this.isDark;
                localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
                this.applyTheme();
            },
            applyTheme() {
                document.documentElement.classList.toggle('dark', this.isDark);
            }
        }
    }
</script>

<script>
    window.authUser = @json(Auth::user());
      let currentAudio = null;
    let currentButton = null;

    function toggleAudio(audioId, btn) {
        const audio = document.getElementById(audioId);

        // If a different audio is playing, stop it first
        if (currentAudio && currentAudio !== audio) {
            currentAudio.pause();
            currentAudio.currentTime = 0;
            resetButton(currentButton);
        }

        if (audio.paused) {
            audio.play();
            currentAudio = audio;
            currentButton = btn;
            btn.querySelector('.play-icon').classList.add('hidden');
            btn.querySelector('.pause-icon').classList.remove('hidden');
            btn.querySelector('.btn-label').textContent = 'PAUSE';

            // Reset button when audio ends naturally
            audio.onended = () => resetButton(btn);
        } else {
            audio.pause();
            resetButton(btn);
        }
    }

    function resetButton(btn) {
        if (!btn) return;
        btn.querySelector('.play-icon').classList.remove('hidden');
        btn.querySelector('.pause-icon').classList.add('hidden');
        btn.querySelector('.btn-label').textContent = 'PLAY';
        currentAudio = null;
        currentButton = null;
    }
</script>

</body>
</html>