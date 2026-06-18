<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="themeToggle()" x-init="init()">
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

<body class="bg-gray-100 dark:bg-gray-900 h-screen overflow-hidden">

<div class="flex h-screen">

    {{-- ===================== SIDEBAR ===================== --}}
    <aside
        x-cloak
        x-show="showSidebar || window.innerWidth >= 768"
        @click.away="if (window.innerWidth < 768) showSidebar = false"
        :class="{ '-translate-x-full': !showSidebar && window.innerWidth < 768 }"
        class="fixed inset-y-0 left-0 z-40 w-72 bg-gray-100 border-r border-gray-200 transform transition-transform duration-200 md:relative md:translate-x-0 flex flex-col" style="box-shadow: 4px 0 24px rgba(0,0,0,0.06);"
    >
        {{-- Logo --}}
        <div class="px-6 py-5 border-b border-gray-200 flex justify-center">
            <a href="/member/community">
                <img src="/logo/logoblack.png" alt="Kingsley Khord" class="h-10 w-auto">
            </a>
        </div>

        {{-- Nav --}}
        <div class="flex-1 overflow-y-auto scrollbar-hide px-4 py-5 space-y-0.5 mt-10 md:mt-0">

            @php
                $li = 'group flex items-center gap-3 px-3 py-2.5 rounded-xl text-base font-medium transition-all duration-150';
                $active = $li . ' bg-indigo-600 text-white shadow-sm shadow-indigo-200';
                $inactive = $li . ' text-gray-500 hover:bg-gray-200 hover:text-gray-900';
            @endphp

            <a href="/member/community" class="{{ Request::is('member/community') ? $active : $inactive }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                </svg>
                Overview
            </a>

            <a href="/member/community/space/lessons" class="{{ Request::is('member/community/space/lessons') ? $active : $inactive }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z"/>
                </svg>
                Tutorial
            </a>

            <a href="/member/community/space/progress-report" class="{{ Request::is('member/community/space/progress-report') ? $active : $inactive }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v5.25c0 .621-.504 1.125-1.125 1.125h-2.25A1.125 1.125 0 0 1 3 18.375v-5.25ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125v-9.75ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v14.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/>
                </svg>
                Progress Report
            </a>

            <a href="/member/bookmark" class="{{ Request::is('member/bookmark') ? $active : $inactive }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z"/>
                </svg>
                Bookmarks
            </a>

            <a href="/member/community/leaderboard" class="{{ Request::is('member/community/leaderboard') ? $active : $inactive }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3-3h.75a3 3 0 0 0 3-3v-1.5a3 3 0 0 0-3-3H18M16.5 18.75V21m-9 0V18.75m0 0a3 3 0 0 0-3-3H3.75a3 3 0 0 1-3-3v-1.5a3 3 0 0 1 3-3H6m10.5-3V3a.75.75 0 0 0-.75-.75h-7.5a.75.75 0 0 0-.75.75v3h9Z"/>
                </svg>
                Leaderboard
            </a>

            {{-- Divider --}}
            <div class="pt-4 pb-1">
                <div class="border-t border-gray-200"></div>
            </div>

            <a href="/member/community/space/pdf-downloads" class="{{ Request::is('member/community/space/pdf-downloads') ? $active : $inactive }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                </svg>
                PDF Files
            </a>

            <a href="/member/community/space/audio-downloads" class="{{ Request::is('member/community/space/audio-downloads') ? $active : $inactive }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 9l10.5-3m0 0v11.25m0-11.25L9 9m0 0v11.25m0-11.25L19.5 6m0 0v11.25M9 20.25a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm10.5-3a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                </svg>
                Audio Files
            </a>

            <a href="/member/community/space/midi-downloads" class="{{ Request::is('member/community/space/midi-downloads') ? $active : $inactive }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25"/>
                </svg>
                MIDI Files
            </a>

        </div>

        {{-- Logout --}}
        <div class="px-4 py-4 border-t border-gray-200">
            <form method="POST" action="{{ route('community.logout') }}">
                @csrf
                <button type="submit" class="group flex items-center gap-3 w-full px-3 py-2.5 rounded-xl text-base font-medium text-gray-500 hover:bg-red-50 hover:text-red-500 transition-all duration-150">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="m16 17 5-5-5-5"/>
                        <path d="M21 12H9"/>
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    </svg>
                    Logout
                </button>
            </form>
        </div>

    </aside>

    {{-- ===================== MAIN AREA ===================== --}}
    <div class="flex-1 flex flex-col min-w-0">

        <!-- Header -->
        <header class="bg-white border-b border-gray-100 sticky top-0 z-50">
            <div class="px-6 py-0 flex items-center justify-between h-20">

                <!-- Mobile: Hamburger -->
                <button @click="showSidebar = !showSidebar" class="md:hidden text-gray-500 p-1">
                    <i class="fa fa-bars text-xl"></i>
                </button>

                <!-- Mobile: Logo -->
                <a href="/member/community" class="md:hidden">
                    <img src="/logo/logoblack.png" alt="Logo" class="h-8 w-auto">
                </a>

                <!-- Desktop: Nav Links -->
                <nav class="hidden md:flex items-center gap-0.5">
                    @php
                        $navLink = 'flex items-center gap-2 px-4 py-2 rounded-lg text-[15px] font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-50 transition-all duration-150';
                        $navActive = 'flex items-center gap-2 px-4 py-2 rounded-lg text-[15px] font-semibold text-indigo-600 bg-indigo-50 transition-all duration-150';
                    @endphp
                    <a href="/home" class="{{ Request::is('home') ? $navActive : $navLink }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Dashboard
                    </a>
                    <a href="https://khordsounds.com/product-category/piano-best-sellers/" target="_blank" class="{{ $navLink }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        Shop Plugins
                    </a>
                    <a href="/member/live-session" class="{{ Request::is('member/live-session') ? $navActive : $navLink }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/></svg>
                        Live Session
                    </a>
                    <a href="/member/profile" class="{{ Request::is('member/profile') ? $navActive : $navLink }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Profile
                    </a>
                    <a href="/member/support" class="{{ Request::is('member/support') ? $navActive : $navLink }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Support
                    </a>
                </nav>

                <!-- Right: Actions -->
                <div class="flex items-center gap-1">

                    <a href="#" class="p-2.5 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-50 transition-all duration-150" aria-label="Search">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                        </svg>
                    </a>

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
                            class="relative p-2.5 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-50 transition-all duration-150"
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
                localStorage.setItem('color-theme', 'light');
                document.documentElement.classList.remove('dark');
            },
            toggle() {},
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
