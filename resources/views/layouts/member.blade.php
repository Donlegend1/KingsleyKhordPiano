<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-token" content="{{ auth()->user()?->api_token }}">
    <meta name="description" content="KingsleyKhord Member Area">
    <meta name="theme-color" content="#FFD736">
    <title>{{ config('app.name', 'Laravel') }}</title>

    {{-- Favicons --}}
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">

    {{-- Icons & UI CSS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet">

    {{-- App --}}
    @vite(['resources/css/app.css'])
    @viteReactRefresh
    @vite(['resources/js/app.js'])

    {{-- Deferred JS --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js" defer></script>
    <script src="https://js.paystack.co/v2/inline.js" defer></script>

    <style>[x-cloak] { display: none !important; }</style>
</head>

<body class="bg-white text-gray-800 dark:bg-gray-900 dark:text-gray-100 min-h-screen flex flex-col">
<div id="app" class="flex flex-col flex-grow">

    {{-- ==================== HEADER ==================== --}}
    <header
        x-data="{ openMobileNav: false, showLogoutModal: false }"
        class="bg-black dark:bg-gray-800 shadow sticky top-0 z-50"
    >
        {{-- Top Bar --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between gap-4">

            {{-- Left: Logo + Member Area + Live Badge --}}
            <div class="flex items-center gap-3 flex-shrink-0">
                <a href="/">
                    <img src="/logo/logo.png" alt="KingsleyKhord" class="h-8 sm:h-9 w-auto">
                </a>
                <span class="text-[#FFD736] text-sm flex items-center gap-1">
                    <i class="fa fa-user"></i>
                    <span class="hidden sm:inline">Member Area</span>
                </span>

                {{-- Live Show Badge --}}
                @php
                    use App\Models\Liveshow;
                    $liveshow = Liveshow::where('start_time', '>=', now())->orderBy('start_time')->first();
                @endphp

                @if($liveshow)
                    <div x-data="{ open: false }" class="relative flex-shrink-0">
                        <button @click="open = !open"
                            class="flex items-center gap-1 px-2 py-1 bg-red-600 text-white text-xs font-bold uppercase rounded-md animate-pulse hover:scale-110 transition-transform">
                            <i class="fa fa-circle text-[8px] animate-ping"></i> Live
                        </button>
                        <div x-show="open" x-transition x-cloak @click.away="open = false"
                            class="absolute top-10 left-0 w-64 bg-white text-black rounded-lg shadow-2xl p-4 z-[9999]">
                            <button @click="open = false" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">
                                <i class="fa fa-times text-sm"></i>
                            </button>
                            <h3 class="text-sm font-semibold text-gray-800">{{ $liveshow->title }}</h3>
                            <p class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($liveshow->start_time)->format('M d, Y h:i A') }}</p>
                            <a href="/member/live-session"
                                class="mt-3 block text-center bg-red-600 text-white text-xs py-1.5 rounded hover:bg-red-700 transition">
                                Join Live Show
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Right: Desktop Links + Notifications + Logout --}}
            <div class="flex items-center gap-4">

                {{-- Desktop Quick Links --}}
                <nav class="hidden lg:flex items-center gap-4">
                    <a href="/member/getstarted" class="text-white hover:text-[#FFD736] text-sm flex items-center gap-1.5 transition">
                        <i class="fa fa-play-circle"></i> Get Started
                    </a>
                    <a href="/member/community" class="text-white hover:text-[#FFD736] text-sm flex items-center gap-1.5 transition">
                        <i class="fa fa-users"></i> Community
                    </a>
                    <a href="https://khordsounds.com/" target="_blank" class="text-white hover:text-[#FFD736] text-sm flex items-center gap-1.5 transition">
                        <i class="fa fa-shopping-bag"></i> Shop
                    </a>
                    <a href="/member/support" class="text-white hover:text-[#FFD736] text-sm flex items-center gap-1.5 transition">
                        <i class="fa fa-life-ring"></i> Support
                    </a>
                    <a href="/member/profile" class="text-white hover:text-[#FFD736] text-sm flex items-center gap-1.5 transition">
                        <i class="fa fa-user-circle text-base"></i> Account
                    </a>
                </nav>

                {{-- Notifications --}}
                @php
                    $unreadNotifications = auth()->user()?->unreadNotifications()->count() ?? 0;
                    $recentNotifications = auth()->user()?->notifications()->latest()->get() ?? collect();
                @endphp

                <div x-data="{ openNotif: false }" class="relative">
                    <button @click="openNotif = !openNotif"
                        class="relative text-white hover:text-[#FFD736] transition p-1"
                        aria-label="Notifications">
                        <i class="fa fa-bell text-lg"></i>
                        @if($unreadNotifications > 0)
                            <span class="absolute -top-1 -right-1.5 min-w-[18px] h-[18px] flex items-center justify-center px-1 text-[10px] font-bold text-white bg-red-500 rounded-full leading-none">
                                {{ $unreadNotifications > 99 ? '99+' : $unreadNotifications }}
                            </span>
                        @endif
                    </button>

                    <div x-show="openNotif" x-transition x-cloak @click.away="openNotif = false"
                        class="absolute right-0 top-10 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden z-[9999]">

                        {{-- Notif Header --}}
                        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">Notifications</span>
                                @if($unreadNotifications > 0)
                                    <span class="text-[10px] font-bold bg-red-100 text-red-600 dark:bg-red-900/40 dark:text-red-400 px-1.5 py-0.5 rounded-full">
                                        {{ $unreadNotifications }} new
                                    </span>
                                @endif
                            </div>
                            @if($unreadNotifications > 0)
                                <form method="POST" action="{{ route('notifications.markAllAsRead') }}">
                                    @csrf
                                    <button type="submit" class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium">
                                        Mark all read
                                    </button>
                                </form>
                            @endif
                        </div>

                        {{-- Notif List --}}
                        <div class="max-h-72 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($recentNotifications as $note)
                                @php
                                    $nd       = $note->data['data'] ?? $note->data;
                                    $url      = $nd['url'] ?? '#';
                                    $message  = $nd['title'] ?? $note->data['message'] ?? 'New notification';
                                    $section  = $nd['section'] ?? null;
                                    $isUnread = is_null($note->read_at);
                                @endphp
                                <div class="flex items-start gap-3 px-4 py-3 transition hover:bg-gray-50 dark:hover:bg-gray-700/60 {{ $isUnread ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                                    <span class="flex-shrink-0 mt-2 w-2 h-2 rounded-full {{ $isUnread ? 'bg-blue-500' : 'bg-gray-300 dark:bg-gray-600' }}"></span>
                                    <a href="{{ $url }}" class="flex-1 min-w-0">
                                        @if($section)
                                            <span class="text-[10px] font-semibold uppercase tracking-wide text-pink-500">{{ $section }}</span>
                                        @endif
                                        <p class="text-sm text-gray-800 dark:text-gray-100 leading-snug">{{ $message }}</p>
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $note->created_at->diffForHumans() }}</p>
                                    </a>
                                    @if($isUnread)
                                        <form method="POST" action="{{ route('notifications.markRead', $note->id) }}">
                                            @csrf
                                            <button type="submit" class="text-xs text-blue-600 dark:text-blue-400 hover:underline whitespace-nowrap mt-1">
                                                Mark read
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @empty
                                <div class="py-8 text-center">
                                    <i class="fa fa-bell-slash text-2xl text-gray-300 dark:text-gray-600 mb-2"></i>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">You're all caught up 🎉</p>
                                </div>
                            @endforelse
                        </div>

                        @if($recentNotifications->count() > 0)
                            <div class="px-4 py-2.5 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-center">
                                <a href="/member/notifications" class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                    View all notifications
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Desktop Logout --}}
                <button @click="showLogoutModal = true"
                    class="hidden lg:block text-red-500 hover:text-red-400 transition p-1"
                    title="Logout">
                    <i class="fa fa-sign-out text-lg"></i>
                </button>

                {{-- Mobile Menu Button --}}
                <button @click="openMobileNav = !openMobileNav"
                    class="lg:hidden text-white p-1 focus:outline-none"
                    aria-label="Open menu">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Desktop Sub-Nav --}}
        <div class="hidden lg:flex bg-[#28303C] dark:bg-gray-800 overflow-x-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center gap-2 flex-nowrap">
                @php
                    $subNav = [
                        ['url' => 'home',                   'label' => 'Dashboard',     'icon' => 'dashboard.svg'],
                        ['url' => 'member/roadmap',         'label' => 'Roadmap',       'icon' => 'roadmap2.png'],
                        ['url' => 'member/piano-exercise',  'label' => 'Piano Exercise','icon' => 'piano2.png'],
                        ['url' => 'member/ear-training',    'label' => 'Ear Training',  'icon' => 'eartraning.svg'],
                        ['url' => 'member/extra-courses',   'label' => 'Extra Courses', 'icon' => 'extracourse.svg'],
                        ['url' => 'member/quick-lessons',   'label' => 'Quick Lesson',  'icon' => 'quick lession.svg'],
                        ['url' => 'member/learn-songs',     'label' => 'Learn Songs',   'icon' => 'songs.svg'],
                        ['url' => 'member/live-session',    'label' => 'Live Session',  'icon' => 'livesession.svg'],
                    ];
                @endphp
                @foreach($subNav as $item)
                    <a href="/{{ $item['url'] }}"
                        class="flex items-center gap-2 text-sm px-3 py-1.5 rounded whitespace-nowrap transition text-white
                            {{ Request::is($item['url']) ? 'bg-gray-700' : 'hover:text-[#FFD736]' }}">
                        <img src="/icons/{{ $item['icon'] }}" class="h-4 w-auto" alt="">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Mobile Sidebar --}}
        <div x-show="openMobileNav" x-cloak class="fixed inset-0 z-50">
            <div
                x-show="openMobileNav"
                x-transition:enter="transition-opacity duration-300 ease-out"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity duration-200 ease-in"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute inset-0 bg-slate-950/72 backdrop-blur-[2px]"
                @click="openMobileNav = false"
            ></div>

            <div class="absolute inset-y-0 right-0 flex max-w-full">
                <div
                    x-show="openMobileNav"
                    x-transition:enter="transform transition duration-300 ease-[cubic-bezier(.22,1,.36,1)]"
                    x-transition:enter-start="translate-x-full opacity-0"
                    x-transition:enter-end="translate-x-0 opacity-100"
                    x-transition:leave="transform transition duration-200 ease-in"
                    x-transition:leave-start="translate-x-0 opacity-100"
                    x-transition:leave-end="translate-x-full opacity-0"
                    class="relative ml-auto flex h-full w-72 max-w-full overflow-y-auto bg-black p-5 text-white flex-col gap-6"
                >
                <div class="flex justify-end">
                    <button @click="openMobileNav = false" class="text-white">
                        <i class="fa fa-times text-lg"></i>
                    </button>
                </div>

                {{-- Sub-nav items --}}
                <div class="flex flex-col divide-y divide-gray-700 border border-gray-700 rounded-md overflow-hidden">
                    @foreach($subNav as $item)
                        <a href="/{{ $item['url'] }}"
                            class="flex items-center gap-3 text-sm px-3 py-3 transition
                                {{ Request::is($item['url']) ? 'bg-gray-700 text-white' : 'hover:text-[#FFD736]' }}">
                            <img src="/icons/{{ $item['icon'] }}" class="h-5 w-auto" alt="">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>

                {{-- Quick links --}}
                <div class="flex flex-col gap-1">
                    <a href="/member/getstarted" class="flex items-center gap-2 text-sm px-3 py-2 text-gray-300 hover:text-[#FFD736] transition">
                        <i class="fa fa-play-circle w-4"></i> Get Started
                    </a>
                    <a href="/member/community" class="flex items-center gap-2 text-sm px-3 py-2 text-gray-300 hover:text-[#FFD736] transition">
                        <i class="fa fa-users w-4"></i> Community
                    </a>
                    <a href="/member/profile" class="flex items-center gap-2 text-sm px-3 py-2 text-gray-300 hover:text-[#FFD736] transition">
                        <i class="fa fa-user-circle w-4"></i> My Account
                    </a>
                    <a href="https://khordsounds.com/" target="_blank" class="flex items-center gap-2 text-sm px-3 py-2 text-gray-300 hover:text-[#FFD736] transition">
                        <i class="fa fa-shopping-bag w-4"></i> Shop
                    </a>
                    <a href="/member/support" class="flex items-center gap-2 text-sm px-3 py-2 text-gray-300 hover:text-[#FFD736] transition">
                        <i class="fa fa-life-ring w-4"></i> Support
                    </a>
                </div>

                <button @click="showLogoutModal = true"
                    class="flex items-center gap-2 text-sm font-semibold px-4 py-2 rounded-md bg-red-500 text-white hover:bg-red-600 transition w-fit">
                    <i class="fa fa-sign-out"></i> Logout
                </button>
            </div>
            </div>
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

    </header>

    {{-- ==================== BANNERS ==================== --}}
    @include('partials.wip-banner')

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- ==================== MAIN ==================== --}}
    <main class="flex-grow">
        @yield('content')
    </main>

    {{-- ==================== FOOTER ==================== --}}
    <footer class="bg-gray-100 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-5">
        <div class="max-w-7xl mx-auto px-4 flex items-center justify-center gap-2 text-sm text-gray-500 dark:text-gray-400">
            <span>&copy; {{ date('Y') }} {{ config('app.name') }}</span>
            <span class="border-l border-gray-400 h-4"></span>
            <span>All rights reserved.</span>
        </div>
    </footer>

</div>

<script>
    window.authUser = @json(auth()->user());
</script>

</body>
</html>
