<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#4B5563">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <title>@yield('title', 'Activity Feed') - {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>[x-cloak] { display: none !important; }</style>
</head>

<body class="bg-gray-50 dark:bg-gray-900" x-data="{ openMobileNav: false, showLogoutModal: false, showSupportModal: false }">

@php
    $hubNav = [
        [
            'key' => 'activity', 'label' => 'Activity', 'url' => '/member/community/activity-feed',
            'submenu' => [
                ['label' => 'Feed', 'url' => '/member/community/activity-feed'],
                ['label' => 'Leaderboard', 'url' => '/member/community/leaderboard'],
            ],
        ],
        [
            'key' => 'forum', 'label' => 'Forums', 'url' => '/member/community/forum',
            'submenu' => [],
        ],
        [
            'key' => 'files', 'label' => 'Files', 'url' => '/member/community/space/pdf-downloads',
            'no_redirect' => true,
            'submenu' => [
                ['label' => 'Midi Files', 'url' => '/member/community/space/midi-downloads'],
                ['label' => 'PDF Files', 'url' => '/member/community/space/pdf-downloads'],
                ['label' => 'Audio Files', 'url' => '/member/community/space/audio-downloads'],
            ],
        ],
        [
            'key' => 'tutorials', 'label' => 'Tutorials', 'url' => '/member/community/space/lessons',
            'submenu' => [],
            'extra_matches' => ['member/community/tutorials/*'],
        ],
        [
            'key' => 'member-area', 'label' => 'Member Area', 'url' => '/home',
            'submenu' => [
                ['label' => 'Dashboard', 'url' => '/home'],
                ['label' => 'Roadmap', 'url' => '/member/roadmap'],
                ['label' => 'Courses', 'url' => '/member/extra-courses'],
                ['label' => 'Songs', 'url' => '/member/learn-songs'],
            ],
        ],
    ];

    $activeNavKey = $forceActiveNavKey ?? null;
    foreach ($activeNavKey ? [] : $hubNav as $item) {
        $urls = array_merge([$item['url']], array_column($item['submenu'], 'url'));
        $patterns = array_map(fn ($url) => ltrim($url, '/'), $urls);
        foreach ($patterns as $path) {
            $patterns[] = $path . '/*';
        }
        $patterns = array_merge($patterns, $item['extra_matches'] ?? []);

        if (request()->is(...$patterns)) {
            $activeNavKey = $item['key'];
            break;
        }
    }
    $activeSubmenu = collect($hubNav)->firstWhere('key', $activeNavKey)['submenu'] ?? [];

    $user = auth()->user();
    $initial = $user ? strtoupper(substr($user->display_name ?? $user->email, 0, 1)) : '?';

    // Profile completion — used for the "Next Step" nudge banner below the header.
    $profileFields = [
        'passport' => 'Profile Photo',
        'biography' => 'Bio',
        'phone_number' => 'Phone Number',
        'country' => 'Country',
        'skill_level' => 'Skill Level',
    ];
    $profileNextStep = null;
    $profileFilledCount = 0;
    foreach ($profileFields as $field => $label) {
        if (!empty($user->$field)) {
            $profileFilledCount++;
        } elseif (!$profileNextStep) {
            $profileNextStep = $label;
        }
    }
    $profilePercent = (int) round(($profileFilledCount / count($profileFields)) * 100);
@endphp

<header class="relative z-40">

    {{-- Row 1: Logo + create/notifications/messages/account --}}
    <div class="bg-[#3A4B82]">
        <div class="max-w-7xl mx-auto px-6 h-24 flex items-center justify-between gap-4">
            <a href="/home" class="flex items-center flex-shrink-0">
                <img src="/logo/logo.png" alt="{{ config('app.name') }}" class="h-14 w-auto">
            </a>

            <div class="flex items-center gap-6">
                {{-- Notifications, Support, and Account move into the mobile
                    nav drawer below lg: — keep them out of the cramped top
                    bar on small screens. --}}
                <div class="hidden lg:flex items-center gap-6">
                {{-- Notifications --}}
                @php
                    $notifications = auth()->user()->notifications()
                        ->where('data->data->section', \App\Enums\Notification\NotificationSectionEnum::COMMUNITY->value)
                        ->latest()
                        ->get();
                    $unreadCount = $notifications->whereNull('read_at')->count();
                @endphp

                <div class="relative group" x-data="{
                        open: false,
                        openSettings: false,
                        notifPref: '{{ auth()->user()->notification_preference ?? 'email' }}',
                        saving: false,
                        pushError: '',
                        async savePreference(value) {
                            const previous = this.notifPref;
                            this.notifPref = value;
                            try {
                                const res = await fetch('{{ route('notifications.updatePreference') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                    },
                                    body: JSON.stringify({ notification_preference: value }),
                                });
                                if (!res.ok) { this.notifPref = previous; }
                            } catch (e) {
                                this.notifPref = previous;
                            }
                        },
                        async setPref(value) {
                            if (this.saving) return;
                            this.pushError = '';
                            if (value !== 'push') {
                                this.saving = true;
                                await this.savePreference(value);
                                this.saving = false;
                                return;
                            }

                            if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
                                this.pushError = 'Push notifications are not supported in this browser.';
                                this.notifPref = '{{ auth()->user()->notification_preference ?? 'email' }}';
                                return;
                            }

                            this.saving = true;
                            try {
                                const permission = await Notification.requestPermission();
                                if (permission !== 'granted') {
                                    this.pushError = 'Notification permission was denied.';
                                    this.notifPref = '{{ auth()->user()->notification_preference ?? 'email' }}';
                                    return;
                                }

                                const registration = await navigator.serviceWorker.register('/serviceworker.js');
                                await navigator.serviceWorker.ready;

                                const urlBase64ToUint8Array = (base64String) => {
                                    const padding = '='.repeat((4 - base64String.length % 4) % 4);
                                    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
                                    const rawData = atob(base64);
                                    return Uint8Array.from([...rawData].map((c) => c.charCodeAt(0)));
                                };

                                let subscription = await registration.pushManager.getSubscription();
                                if (!subscription) {
                                    subscription = await registration.pushManager.subscribe({
                                        userVisibleOnly: true,
                                        applicationServerKey: urlBase64ToUint8Array('{{ config('webpush.vapid.public_key') }}'),
                                    });
                                }

                                await fetch('{{ route('webpush.subscribe') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                    },
                                    body: JSON.stringify(subscription.toJSON()),
                                });

                                await this.savePreference('push');
                            } catch (e) {
                                this.pushError = 'Could not enable push notifications.';
                                this.notifPref = '{{ auth()->user()->notification_preference ?? 'email' }}';
                            } finally {
                                this.saving = false;
                            }
                        }
                     }" @click.outside="open = false">
                    <button type="button" @click="open = !open"
                        class="relative flex items-center justify-center w-6 h-6 text-white hover:text-white/80 transition-colors" aria-label="Notifications">
                        <svg class="w-6 h-6 block" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M5.85 3.5a.75.75 0 00-1.117-1 9.719 9.719 0 00-2.348 4.876.75.75 0 001.479.248A8.219 8.219 0 015.85 3.5zM19.267 2.5a.75.75 0 10-1.118 1 8.22 8.22 0 011.987 4.124.75.75 0 001.48-.248A9.72 9.72 0 0019.267 2.5zM12 2.25A6.75 6.75 0 005.25 9v.75a8.217 8.217 0 01-2.119 5.52.75.75 0 00.298 1.206c1.544.57 3.16.99 4.831 1.243a3.75 3.75 0 107.48 0 24.583 24.583 0 004.83-1.244.75.75 0 00.298-1.205 8.217 8.217 0 01-2.118-5.52V9A6.75 6.75 0 0012 2.25zM9.75 18c0-.034 0-.067.002-.1a25.05 25.05 0 004.496 0l.002.1a2.25 2.25 0 11-4.5 0z"/>
                        </svg>
                        <span class="absolute -top-1.5 -right-1.5 text-[10px] font-bold rounded-full min-w-[16px] h-[16px] flex items-center justify-center px-1 leading-none
                            {{ $unreadCount > 0 ? 'bg-amber-400 text-black' : 'bg-white/25 text-white' }}">
                            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                        </span>
                    </button>

                    {{-- Tooltip --}}
                    <span x-show="!open"
                        class="pointer-events-none absolute left-1/2 -translate-x-1/2 bottom-full mb-2.5 whitespace-nowrap bg-gray-900 text-white text-xs font-medium px-2.5 py-1.5 rounded-md opacity-0 group-hover:opacity-100 transition-opacity duration-150 z-50">
                        Notifications
                        <span class="absolute left-1/2 -translate-x-1/2 top-full -mt-1 w-2 h-2 rotate-45 bg-gray-900"></span>
                    </span>

                    <!-- Dropdown -->
                    <div
                        x-show="open"
                        x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="absolute right-0 mt-3 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden z-50"
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
                            <div class="flex items-center gap-3">
                                @if($unreadCount > 0)
                                    <form method="POST" action="{{ route('notifications.markAllAsRead') }}">
                                        @csrf
                                        <button type="submit" class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium">
                                            Mark all read
                                        </button>
                                    </form>
                                @endif
                                <div class="relative" @click.outside="openSettings = false">
                                    <button type="button" @click="openSettings = !openSettings" aria-label="Notification settings"
                                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </button>

                                    <div x-show="openSettings" x-cloak x-transition
                                        class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-lg p-3 z-50 text-left">
                                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2 px-1">Notify me via</p>
                                        <label class="flex items-center gap-2 px-2 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/60 cursor-pointer">
                                            <input type="radio" name="notifPref" value="push" x-model="notifPref" :disabled="saving" @change="setPref('push')" class="text-indigo-600 focus:ring-indigo-400">
                                            <span class="text-sm text-gray-700 dark:text-gray-200">Push notifications</span>
                                        </label>
                                        <label class="flex items-center gap-2 px-2 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/60 cursor-pointer">
                                            <input type="radio" name="notifPref" value="email" x-model="notifPref" :disabled="saving" @change="setPref('email')" class="text-indigo-600 focus:ring-indigo-400">
                                            <span class="text-sm text-gray-700 dark:text-gray-200">Email notifications</span>
                                        </label>
                                        <label class="flex items-center gap-2 px-2 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/60 cursor-pointer">
                                            <input type="radio" name="notifPref" value="disabled" x-model="notifPref" :disabled="saving" @change="setPref('disabled')" class="text-indigo-600 focus:ring-indigo-400">
                                            <span class="text-sm text-gray-700 dark:text-gray-200">Disabled</span>
                                        </label>
                                        <p x-show="pushError" x-cloak x-text="pushError" class="text-xs text-red-500 mt-1.5 px-1"></p>
                                    </div>
                                </div>
                            </div>
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
                                $initials2 = strtoupper(substr($firstName, 0, 1));
                                $notifUrl  = $data['data']['url'] ?? null;
                            @endphp

                                <a href="{{ $notifUrl }}"
                                   class="flex items-start gap-3 px-4 py-3 transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/60 {{ $isUnread ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">

                                    <!-- Avatar -->
                                    <div class="flex-shrink-0 mt-0.5">
                                        @if(!empty($avatar))
                                            <img src="{{ $avatar }}" alt="{{ $firstName }}"
                                                 class="w-9 h-9 rounded-full object-cover ring-2 ring-white dark:ring-gray-800">
                                        @else
                                            <div class="w-9 h-9 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-xs font-bold text-indigo-600 dark:text-indigo-400 ring-2 ring-white dark:ring-gray-800">
                                                {{ $initials2 }}
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

                        <a href="{{ route('community.notifications') }}"
                            class="block text-center text-sm font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 border-t border-gray-100 dark:border-gray-700 py-3 transition-colors">
                            View all notifications
                        </a>
                    </div>
                </div>

                {{-- Support --}}
                <div class="relative group">
                    <button type="button" @click="showSupportModal = true" class="flex items-center justify-center w-6 h-6 text-white hover:text-white/80 transition-colors" aria-label="Support">
                        <svg class="w-6 h-6 block" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M1.5 8.67v8.58a3 3 0 003 3h15a3 3 0 003-3V8.67l-8.928 5.493a3 3 0 01-3.144 0L1.5 8.67z"/>
                            <path d="M22.5 6.908V6.75a3 3 0 00-3-3h-15a3 3 0 00-3 3v.158l9.714 5.978a1.5 1.5 0 001.572 0L22.5 6.908z"/>
                        </svg>
                    </button>

                    {{-- Tooltip --}}
                    <span class="pointer-events-none absolute left-1/2 -translate-x-1/2 bottom-full mb-2.5 whitespace-nowrap bg-gray-900 text-white text-xs font-medium px-2.5 py-1.5 rounded-md opacity-0 group-hover:opacity-100 transition-opacity duration-150 z-50">
                        Support
                        <span class="absolute left-1/2 -translate-x-1/2 top-full -mt-1 w-2 h-2 rotate-45 bg-gray-900"></span>
                    </span>
                </div>

                {{-- Account --}}
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button type="button" @click="open = !open" class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-full bg-white/20 border-2 border-white/60 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                            {{ $initial }}
                        </span>
                        <span class="hidden sm:block text-sm font-semibold text-white uppercase tracking-wide">
                            {{ $user->display_name ?? 'Account' }}
                        </span>
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                        </svg>
                    </button>

                    <div x-show="open" x-cloak
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="absolute right-0 mt-3 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden z-50">
                        <a href="/member/community/profile" class="block px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/60 transition-colors">Profile</a>
                        <a href="/member/bookmark" class="block px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/60 transition-colors">Bookmark</a>
                        <a href="{{ route('community.account-settings') }}" class="block px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/60 transition-colors">Account Settings</a>
                        <button type="button" @click="showLogoutModal = true" class="w-full text-left px-4 py-2.5 text-sm text-red-600 border-t border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/60 transition-colors">Logout</button>
                    </div>
                </div>
                </div>

                {{-- Mobile menu --}}
                <button @click="openMobileNav = !openMobileNav" class="lg:hidden text-white p-1" aria-label="Open menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Rows 2 & 3: Nav tabs + hover-revealed sub-nav + search --}}
    <div class="hidden lg:block" x-data="{ hoveredKey: null, activeKey: '{{ $activeNavKey }}', navData: @js(collect($hubNav)->keyBy('key')) }"
        @mouseleave="hoveredKey = null">

        <div class="bg-[#262626]">
            <div class="max-w-7xl mx-auto px-6 h-14 flex items-center gap-6">
                <nav class="flex items-center gap-2 h-full">
                    @foreach ($hubNav as $item)
                        @php $isActive = $item['key'] === $activeNavKey; @endphp
                        @php
                            $navItemClasses = 'relative flex items-center h-8 px-3.5 rounded-md text-sm font-medium transition-all duration-150 ease-out hover:scale-110 '
                                . ($isActive ? 'bg-white/15 text-white' : 'text-white/70 hover:text-white');
                        @endphp
                        @if($item['no_redirect'] ?? false)
                            <button type="button"
                                @mouseenter="hoveredKey = '{{ $item['key'] }}'"
                                class="{{ $navItemClasses }}">
                                {{ $item['label'] }}
                                <span class="absolute left-1/2 -translate-x-1/2 -bottom-[18px] w-3 h-3 rotate-45 bg-[#AEB6DA] z-20 transition-opacity duration-100"
                                    :class="(hoveredKey ?? activeKey) === '{{ $item['key'] }}' ? 'opacity-100' : 'opacity-0'"></span>
                            </button>
                        @else
                            <a href="{{ $item['url'] }}"
                                @mouseenter="hoveredKey = '{{ $item['key'] }}'"
                                class="{{ $navItemClasses }}">
                                {{ $item['label'] }}
                                <span class="absolute left-1/2 -translate-x-1/2 -bottom-[18px] w-3 h-3 rotate-45 bg-[#AEB6DA] z-20 transition-opacity duration-100"
                                    :class="(hoveredKey ?? activeKey) === '{{ $item['key'] }}' ? 'opacity-100' : 'opacity-0'"></span>
                            </a>
                        @endif
                    @endforeach
                </nav>
            </div>
        </div>

        {{-- Sub-nav: shows the hovered tab's items, falling back to the active tab's --}}
        <template x-if="navData[hoveredKey ?? activeKey]">
            <div class="bg-[#AEB6DA]">
                <div class="max-w-7xl mx-auto px-6 h-11 flex items-center gap-8">
                    <template x-for="sub in navData[hoveredKey ?? activeKey].submenu" :key="sub.url">
                        <a :href="sub.url" x-text="sub.label"
                            class="inline-block text-sm font-medium transition-all duration-150 ease-out hover:scale-110"
                            :class="sub.url === window.location.pathname
                                ? 'text-[#262626] font-semibold'
                                : 'text-[#4B5563] hover:text-[#262626]'"></a>
                    </template>
                </div>
            </div>
        </template>
    </div>
</header>

{{-- Profile completion nudge --}}
@if($profilePercent < 100)
    <div x-data="{ dismissed: localStorage.getItem('profileNudgeDismissed') === '{{ $user->id }}' }"
        x-show="!dismissed" x-cloak>
        <div class="max-w-7xl mx-auto px-6 pt-3">
            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-lg shadow-sm overflow-hidden">
                <div class="flex items-center justify-between gap-3 px-4 py-2">
                    <h3 class="text-xs font-semibold text-gray-900 dark:text-white">Next Step: {{ $profileNextStep }}</h3>
                    <div class="flex items-center gap-2.5 flex-shrink-0">
                        <a href="/member/profile"
                            class="bg-[#4B5563] hover:bg-[#374151] text-white text-[11px] font-semibold px-2.5 py-1 rounded transition-colors">
                            Complete My Profile
                        </a>
                        <button type="button"
                            @click="dismissed = true; localStorage.setItem('profileNudgeDismissed', '{{ $user->id }}')"
                            class="text-blue-600 hover:underline text-[11px] font-medium">
                            Dismiss
                        </button>
                    </div>
                </div>
                <div class="relative h-4 bg-[#EFE1DE] dark:bg-gray-700 border-l-2 border-[#4B5563]">
                    <div class="absolute inset-y-0 left-0 bg-[#4B5563] transition-all duration-500" style="width: {{ $profilePercent }}%"></div>
                    <span class="relative z-10 flex items-center h-full px-3 text-[10px] text-white">
                        Your profile is {{ $profilePercent }}% complete!
                    </span>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- Mobile Nav Drawer --}}
<div x-show="openMobileNav" x-cloak class="fixed inset-0 z-50 flex justify-end lg:hidden">
    <div x-show="openMobileNav" x-transition:enter="transition-opacity ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/60" @click="openMobileNav = false"></div>
    <div x-show="openMobileNav"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
        class="relative bg-white text-gray-700 w-96 max-w-full h-full overflow-y-auto p-5 flex flex-col gap-1 shadow-xl">
        <div class="flex justify-end mb-4">
            <button @click="openMobileNav = false" class="text-gray-400 hover:text-gray-600">
                <i class="fa fa-times text-xl"></i>
            </button>
        </div>

        {{-- Profile summary + Notifications/Support (moved here from the top bar on mobile) --}}
        <div class="flex items-center justify-between gap-3 pb-4 mb-3 border-b border-gray-100">
            <div class="flex items-center gap-3 min-w-0">
                <span class="w-10 h-10 rounded-full bg-[#3A4B82]/10 border-2 border-[#3A4B82]/30 flex items-center justify-center text-[#3A4B82] text-sm font-bold flex-shrink-0">
                    {{ $initial }}
                </span>
                <div class="min-w-0">
                    <p class="text-[11px] text-gray-400 uppercase tracking-wide">Signed in as</p>
                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $user->display_name ?? 'Account' }}</p>
                </div>
            </div>

            <div class="flex items-center gap-4 flex-shrink-0">
                <a href="{{ route('community.notifications') }}" @click="openMobileNav = false"
                    class="relative flex items-center justify-center w-6 h-6 text-gray-500 hover:text-gray-800 transition-colors" aria-label="Notifications">
                    <svg class="w-6 h-6 block" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M5.85 3.5a.75.75 0 00-1.117-1 9.719 9.719 0 00-2.348 4.876.75.75 0 001.479.248A8.219 8.219 0 015.85 3.5zM19.267 2.5a.75.75 0 10-1.118 1 8.22 8.22 0 011.987 4.124.75.75 0 001.48-.248A9.72 9.72 0 0019.267 2.5zM12 2.25A6.75 6.75 0 005.25 9v.75a8.217 8.217 0 01-2.119 5.52.75.75 0 00.298 1.206c1.544.57 3.16.99 4.831 1.243a3.75 3.75 0 107.48 0 24.583 24.583 0 004.83-1.244.75.75 0 00.298-1.205 8.217 8.217 0 01-2.118-5.52V9A6.75 6.75 0 0012 2.25zM9.75 18c0-.034 0-.067.002-.1a25.05 25.05 0 004.496 0l.002.1a2.25 2.25 0 11-4.5 0z"/>
                    </svg>
                    @if($unreadCount > 0)
                        <span class="absolute -top-1.5 -right-1.5 text-[10px] font-bold rounded-full min-w-[16px] h-[16px] flex items-center justify-center px-1 leading-none bg-amber-400 text-black">
                            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                        </span>
                    @endif
                </a>

                <button type="button" @click="openMobileNav = false; showSupportModal = true"
                    class="flex items-center justify-center w-6 h-6 text-gray-500 hover:text-gray-800 transition-colors" aria-label="Support">
                    <svg class="w-6 h-6 block" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M1.5 8.67v8.58a3 3 0 003 3h15a3 3 0 003-3V8.67l-8.928 5.493a3 3 0 01-3.144 0L1.5 8.67z"/>
                        <path d="M22.5 6.908V6.75a3 3 0 00-3-3h-15a3 3 0 00-3 3v.158l9.714 5.978a1.5 1.5 0 001.572 0L22.5 6.908z"/>
                    </svg>
                </button>
            </div>
        </div>

        @foreach ($hubNav as $item)
            @php
                $isActive = $item['key'] === $activeNavKey;
                $hasSubmenu = !empty($item['submenu']);
            @endphp
            @if($hasSubmenu)
                <div x-data="{ open: {{ $isActive ? 'true' : 'false' }} }">
                    <button type="button" @click="open = !open"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition {{ $isActive ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <span>{{ $item['label'] }}</span>
                        <svg class="w-4 h-4 flex-shrink-0 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="flex flex-col gap-1 pl-6 mt-1">
                        @foreach($item['submenu'] as $sub)
                            <a href="{{ $sub['url'] }}" @click="openMobileNav = false"
                                class="px-3 py-2 rounded-lg text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-900 transition {{ request()->is(ltrim($sub['url'], '/')) ? 'text-gray-900 font-semibold' : '' }}">
                                {{ $sub['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <a href="{{ $item['url'] }}" @click="openMobileNav = false"
                    class="px-3 py-2.5 rounded-lg text-sm font-medium transition {{ $isActive ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    {{ $item['label'] }}
                </a>
            @endif
        @endforeach

        <div class="border-t border-gray-100 my-3"></div>

        <a href="/member/community/profile" class="px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition">Profile</a>
        <a href="/member/bookmark" class="px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition">Bookmark</a>
        <a href="{{ route('community.account-settings') }}" class="px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition">Account Settings</a>
        <button type="button" @click="openMobileNav = false; showLogoutModal = true"
            class="text-left px-3 py-2.5 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50 transition">
            Logout
        </button>
    </div>
</div>

@hasSection('breadcrumbs')
    <div class="max-w-7xl mx-auto px-4 pt-2 sm:px-6 sm:pt-4">
        @yield('breadcrumbs')
    </div>
@endif

{{-- Page Content --}}
<main class="max-w-7xl mx-auto">
    @yield('content')
</main>

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
                <button type="submit" class="w-full px-4 py-2 text-sm text-white bg-red-600 rounded-lg hover:bg-red-700 transition">
                    Logout
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Support Modal --}}
<div x-show="showSupportModal" x-cloak class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4"
    x-data="{
        subject: '',
        message: '',
        attachment: null,
        sending: false,
        sent: false,
        errors: {},
        pickFile(e) {
            this.attachment = e.target.files[0] || null;
        },
        removeFile() {
            this.attachment = null;
            this.$refs.fileInput.value = '';
        },
        async send() {
            this.sending = true;
            this.errors = {};
            try {
                const formData = new FormData();
                formData.append('subject', this.subject);
                formData.append('message', this.message);
                if (this.attachment) {
                    formData.append('attachment', this.attachment);
                }

                const res = await fetch('/support/send', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: formData,
                });

                if (res.status === 422) {
                    const data = await res.json();
                    this.errors = data.errors || {};
                } else {
                    this.sent = true;
                    this.subject = '';
                    this.message = '';
                    this.removeFile();
                }
            } catch (e) {
                this.errors = { message: ['Something went wrong. Please try again.'] };
            } finally {
                this.sending = false;
            }
        },
        close() {
            showSupportModal = false;
            this.sent = false;
            this.errors = {};
        },
    }">
    <div @click.away="close()" class="bg-white rounded-2xl shadow-xl w-full max-w-xl p-8">

        <div class="flex items-center justify-between mb-1">
            <h2 class="text-xl font-bold text-gray-900">Contact Support</h2>
            <button @click="close()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <p class="text-sm text-gray-500 mb-6">We usually reply within a day.</p>

        <template x-if="sent">
            <div class="flex items-start gap-2 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl p-4 mb-2">
                <i class="fa-solid fa-circle-check mt-0.5"></i>
                <span>Your message has been sent successfully.</span>
            </div>
        </template>

        <form @submit.prevent="send()" class="space-y-5" x-show="!sent" enctype="multipart/form-data">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Subject</label>
                <input type="text" x-model="subject" placeholder="What's this about?"
                    class="block w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 shadow-sm transition focus:border-gray-500 focus:ring-2 focus:ring-gray-100 focus:outline-none">
                <p class="text-red-600 text-xs mt-1.5" x-show="errors.subject" x-text="errors.subject?.[0]"></p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Message</label>
                <textarea x-model="message" rows="6" placeholder="Tell us how we can help..."
                    class="block w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 shadow-sm transition focus:border-gray-500 focus:ring-2 focus:ring-gray-100 focus:outline-none resize-none"></textarea>
                <p class="text-red-600 text-xs mt-1.5" x-show="errors.message" x-text="errors.message?.[0]"></p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Attachment <span class="font-normal text-gray-400">(optional)</span></label>

                <div x-show="!attachment">
                    <label class="flex items-center justify-center gap-2 w-full rounded-xl border-2 border-dashed border-gray-200 px-4 py-4 text-sm text-gray-500 cursor-pointer hover:border-gray-300 hover:bg-gray-50 transition">
                        <i class="fa-solid fa-paperclip"></i>
                        <span>Click to attach a file</span>
                        <input x-ref="fileInput" type="file" class="hidden" @change="pickFile($event)"
                            accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.txt">
                    </label>
                    <p class="text-xs text-gray-400 mt-1.5">Images, PDF, or Word docs, up to 10MB.</p>
                </div>

                <div x-show="attachment" class="flex items-center justify-between gap-3 rounded-xl border border-gray-200 px-4 py-3 bg-gray-50">
                    <div class="flex items-center gap-2 min-w-0">
                        <i class="fa-solid fa-file text-gray-400 flex-shrink-0"></i>
                        <span class="text-sm text-gray-700 truncate" x-text="attachment?.name"></span>
                    </div>
                    <button type="button" @click="removeFile()" class="text-gray-400 hover:text-red-500 flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <p class="text-red-600 text-xs mt-1.5" x-show="errors.attachment" x-text="errors.attachment?.[0]"></p>
            </div>

            <div class="flex justify-end gap-3 pt-1">
                <button type="button" @click="close()" class="px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 rounded-lg transition">
                    Cancel
                </button>
                <button type="submit" :disabled="sending"
                    class="inline-flex items-center gap-2 bg-[#4B5563] hover:bg-[#374151] disabled:opacity-60 text-white px-5 py-2.5 rounded-xl font-semibold text-sm transition-colors">
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                    <span x-text="sending ? 'Sending...' : 'Send Message'"></span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    window.authUser = @json(auth()->user());
</script>

</body>
</html>
