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

<body class="bg-gray-50 dark:bg-black" x-data="{ openMobileNav: false, showLogoutModal: false }">

@php
    $li = 'flex items-center gap-2 px-3 py-2 my-2 rounded-lg text-sm font-semibold whitespace-nowrap transition-colors duration-150';
    $activeSub = $li . ' bg-white/10 text-white';
    $inactiveSub = $li . ' text-white hover:bg-white/10';
    $iconActive = 'w-5 h-5 flex-shrink-0 text-white';
    $iconInactive = 'w-5 h-5 flex-shrink-0 text-white';

    $subNav = [
        ['url' => 'member/my-library', 'label' => 'Overview', 'icon' => 'm2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25'],
        ['url' => 'member/community/space/lessons', 'label' => 'Tutorial', 'icon' => 'm15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z'],
        ['url' => 'member/community/space/progress-report', 'label' => 'Progress Report', 'icon' => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v5.25c0 .621-.504 1.125-1.125 1.125h-2.25A1.125 1.125 0 0 1 3 18.375v-5.25ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125v-9.75ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v14.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z'],
        ['url' => 'member/bookmark', 'label' => 'Bookmark', 'icon' => 'M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z'],
        ['url' => 'member/community/space/pdf-downloads', 'label' => 'PDF Files', 'icon' => 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z'],
        ['url' => 'member/community/space/audio-downloads', 'label' => 'Audio Files', 'icon' => 'M9 9l10.5-3m0 0v11.25m0-11.25L9 9m0 0v11.25m0-11.25L19.5 6m0 0v11.25M9 20.25a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm10.5-3a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z'],
        ['url' => 'member/community/space/midi-downloads', 'label' => 'MIDI Files', 'icon' => 'M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25'],
        ['url' => 'member/application', 'label' => 'Application', 'icon' => 'M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5 7.5 12M12 16.5V3'],
    ];

    $topLinks = [
        ['url' => 'home', 'label' => 'Dashboard', 'icon' => 'M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z'],
        ['url' => 'https://discord.com/invite/gFXnRnaf5N', 'label' => 'Community', 'external' => true, 'icon' => 'M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z'],
        ['url' => 'https://khordsounds.com/product-category/piano-best-sellers/', 'label' => 'Shop', 'external' => true, 'icon' => 'M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z M3 6h18 M16 10a4 4 0 0 1-8 0'],
    ];
@endphp

<header class="sticky top-0 z-40">

    {{-- Row 1: Logo + top-right links + notifications --}}
    <div class="bg-[#4F72E0]">
    <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between gap-4">
        <a href="/member/my-library" class="flex items-center flex-shrink-0">
            <img src="/logo/logo.png" alt="KingsleyKhord" class="h-8 sm:h-9 w-auto">
        </a>

        <div class="flex items-center gap-7 ml-auto">
            <nav class="hidden lg:flex items-center gap-2">
                @foreach ($topLinks as $link)
                    @php
                        $isActiveTop = !empty($link['url']) && !str_starts_with($link['url'], 'http') && Request::is($link['url']);
                    @endphp
                    <a href="{{ Str::startsWith($link['url'], 'http') ? $link['url'] : '/' . $link['url'] }}"
                        @if(!empty($link['external'])) target="_blank" @endif
                        class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-150
                            {{ $isActiveTop
                                ? 'bg-white/10 text-white'
                                : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}"/>
                        </svg>
                        {{ $link['label'] }}
                    </a>
                @endforeach

                {{-- Profile --}}
                <a href="/member/profile"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium text-gray-300 hover:bg-white/10 hover:text-white transition-colors duration-150">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Profile
                </a>
            </nav>

            <div class="flex items-center gap-1">

            {{-- Notifications --}}
            @php
                $notifications = auth()->user()->notifications()
                    ->where('data->data->section', \App\Enums\Notification\NotificationSectionEnum::COMMUNITY->value)
                    ->latest()
                    ->get();
                $unreadCount = $notifications->whereNull('read_at')->count();
            @endphp

            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                <button type="button" @click="open = !open"
                    class="relative p-2.5 rounded-xl text-white hover:text-gray-100 hover:bg-white/10 transition-all duration-150"
                    aria-label="Notifications">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M5.85 3.5a.75.75 0 00-1.117-1 9.719 9.719 0 00-2.348 4.876.75.75 0 001.479.248A8.219 8.219 0 015.85 3.5zM19.267 2.5a.75.75 0 10-1.118 1 8.22 8.22 0 011.987 4.124.75.75 0 001.48-.248A9.72 9.72 0 0019.267 2.5zM12 2.25A6.75 6.75 0 005.25 9v.75a8.217 8.217 0 01-2.119 5.52.75.75 0 00.298 1.206c1.544.57 3.16.99 4.831 1.243a3.75 3.75 0 107.48 0 24.583 24.583 0 004.83-1.244.75.75 0 00.298-1.205 8.217 8.217 0 01-2.118-5.52V9A6.75 6.75 0 0012 2.25zM9.75 18c0-.034 0-.067.002-.1a25.05 25.05 0 004.496 0l.002.1a2.25 2.25 0 11-4.5 0z"/>
                    </svg>
                    @if($unreadCount > 0)
                        <span class="absolute -top-0.5 -right-0.5 bg-amber-500 dark:bg-amber-400 text-white dark:text-black text-[10px] font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1 leading-none">
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
                            $firstName = $data['data']['user'] ?? 'Someone';
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
                                        <img src="{{ $avatar }}" alt="{{ $firstName }}"
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
                </div>
            </div>

            {{-- Mobile Menu Button --}}
            <button @click="openMobileNav = !openMobileNav" class="lg:hidden text-gray-300 hover:text-white p-2" aria-label="Open menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            </div>
        </div>
    </div>
    </div>

    {{-- Row 2: Sub-nav (desktop) --}}
    <div class="hidden lg:block bg-[#DB6B35]">
        <div class="max-w-7xl mx-auto px-6 flex items-center gap-7 overflow-x-auto scrollbar-hide">
            @foreach ($subNav as $item)
                <a href="/{{ $item['url'] }}" class="{{ Request::is($item['url']) ? $activeSub : $inactiveSub }}">
                    <svg class="{{ Request::is($item['url']) ? $iconActive : $iconInactive }}" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                    </svg>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>
    </div>
</header>

{{-- Mobile Nav Drawer --}}
<div x-show="openMobileNav" x-cloak class="fixed inset-0 z-50 flex lg:hidden" x-transition>
    <div class="fixed inset-0 bg-black/60" @click="openMobileNav = false"></div>
    <div class="relative bg-[#4F72E0] text-white w-72 max-w-full h-full overflow-y-auto p-5 flex flex-col gap-6">
        <div class="flex justify-end">
            <button @click="openMobileNav = false" class="text-white">
                <i class="fa fa-times text-xl"></i>
            </button>
        </div>

        <div class="flex flex-col gap-1">
            @foreach ($subNav as $item)
                <a href="/{{ $item['url'] }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                        {{ Request::is($item['url']) ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                    </svg>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>

        <div class="border-t border-white/10 pt-4 flex flex-col gap-1">
            @foreach ($topLinks as $link)
                <a href="{{ Str::startsWith($link['url'], 'http') ? $link['url'] : '/' . $link['url'] }}"
                    @if(!empty($link['external'])) target="_blank" @endif
                    class="px-3 py-2.5 rounded-lg text-sm font-medium transition {{ !str_starts_with($link['url'], 'http') && Request::is($link['url']) ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
            <a href="/member/profile" class="px-3 py-2.5 rounded-lg text-sm font-medium text-gray-300 hover:bg-white/5 hover:text-white transition">My Profile</a>
            <button type="button" @click="showLogoutModal = true" class="text-left px-3 py-2.5 rounded-lg text-sm font-medium text-red-400 hover:bg-white/5 transition">Logout</button>
        </div>
    </div>
</div>

{{-- Page Content --}}
<div class="min-h-screen bg-gray-50 dark:bg-black" x-data="{ search: '' }">
    <div class="max-w-7xl mx-auto px-6 py-4 flex flex-wrap items-center justify-between gap-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">
            <a href="@yield('breadcrumb-parent-url', '/home')" class="hover:text-gray-700 dark:hover:text-gray-200">@yield('breadcrumb-parent', 'Dashboard')</a>
            <span class="mx-1">/</span>
            <span class="text-gray-800 dark:text-gray-200 font-medium">@yield('breadcrumb', 'Overview')</span>
        </div>

        @hasSection('page-search')
            <div class="w-full sm:w-auto sm:min-w-[280px]">
                @yield('page-search')
            </div>
        @endif
    </div>

    <div class="max-w-7xl mx-auto px-6 pb-4">
        @yield('page-title')
    </div>

    <main class="max-w-6xl mx-auto">
        @yield('content')
    </main>
</div>

{{-- Logout Modal --}}
<div x-show="showLogoutModal" x-cloak class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div @click.away="showLogoutModal = false" class="bg-white rounded-xl shadow-xl w-full max-w-sm p-6 mx-4">
        <h2 class="text-lg font-semibold text-gray-800 text-center mb-1">Confirm Logout</h2>
        <p class="text-sm text-gray-500 text-center mb-6">Are you sure you want to log out?</p>
        <div class="flex gap-3">
            <button @click="showLogoutModal = false"
                class="flex-1 px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                Cancel
            </button>
            <form method="POST" action="{{ route('logout') }}" class="flex-1">
                @csrf
                <button type="submit"
                    class="w-full px-4 py-2 text-sm text-white bg-red-600 rounded-lg hover:bg-red-700 transition">
                    Logout
                </button>
            </form>
        </div>
    </div>
</div>
<script src="https://fast.wistia.com/player.js" async></script>

<script>
    function themeToggle() {
        return {
            isDark: false,
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
