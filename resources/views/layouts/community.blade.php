<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="themeToggle()" :class="{ 'dark': isDark }" x-init="init()">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="KingsleyKhord is a platform that connects you with the best service providers in your area. Whether you're looking for a plumber, electrician, or any other service, we've got you covered.">
    <meta name="keywords" content="KingsleyKhord, service providers, local services, find services, connect with service providers">
    <meta name="author" content="LengendOSA Consultants">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#FFD736">
    <meta name="google-site-verification" content="your-google-site-verification-code" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
   
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://js.paystack.co/v2/inline.js"></script>
     <script src="https://js.stripe.com/v3/"></script>
       <script src="/build/manifest.json"></script>

    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.js']) 
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
</head>
<body x-data="{ isDark: false, showSidebar: false }" x-init class="bg-white text-gray-800 dark:bg-gray-900 dark:text-gray-100">
    <div id="app" class="min-h-screen flex flex-col">

        <!-- Header -->
        <header class="bg-white dark:bg-gray-800 shadow sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
              <button @click="showSidebar = !showSidebar" class="md:hidden text-gray-500 dark:text-gray-300 hover:text-[#FFD736]">
                  <i class="fa fa-bars text-xl"></i>
              </button>
                          
                <!-- Left: Logo -->
                <a href="/member/community" class="flex items-center space-x-2">
                    <img src="/logo/logowhite.webp" alt="Logo" class="h-10 w-auto">
                </a>

                <!-- Center: Nav Links -->
                <nav class="hidden md:flex space-x-6">
                    <a href="/member/community" class="flex items-center space-x-1 text-gray-500 dark:text-gray-300 hover:text-[#FFD736] transition">
                        <i class="fa fa-home" aria-hidden="true"></i>
                        <span class="text-sm font-medium">Feeds</span>
                    </a>
                    <a href="/member/community/space" class="flex items-center space-x-1 text-gray-500 dark:text-gray-300 hover:text-[#FFD736] transition">
                        <i class="fa fa-users" aria-hidden="true"></i>
                        <span class="text-sm font-medium">Spaces</span>
                    </a>
                    <a href="/member/community/members" class="flex items-center space-x-1 text-gray-500 dark:text-gray-300 hover:text-[#FFD736] transition">
                        <i class="fa fa-users" aria-hidden="true"></i>
                        <span class="text-sm font-medium">Members</span>
                    </a>
                </nav>

                <!-- Right: Icons -->
                <div class="flex items-center space-x-4">
                    <!-- Theme Toggle (Moon) -->
                    <button @click="toggle" class="text-gray-500 dark:text-gray-300 hover:text-[#FFD736]" aria-label="Toggle Dark Mode">
                        <svg x-show="!isDark" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path d="M12 3v1m0 16v1m8.66-12.66l-.71.71M4.05 19.95l-.71.71M21 12h-1M4 12H3m16.66 4.95l-.71-.71M4.05 4.05l-.71-.71M12 5a7 7 0 100 14 7 7 0 000-14z"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <svg x-show="isDark" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>

                    <button class="text-gray-500 dark:text-gray-300 hover:text-[#FFD736]" aria-label="Search">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z" 
                                  stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                     @php
                    // Get latest 10 activities (global)
                   $notifications =  auth()->user()->notifications()
                        ->latest()
                        ->take(5)
                        ->get(); 
                    @endphp


                    <!-- Notifications -->
                    <div class="relative">
                        <!-- Bell Button -->
                        <button 
                            onclick="document.getElementById('notificationDropdown').classList.toggle('hidden')" 
                            class="text-gray-500 dark:text-gray-300 hover:text-[#FFD736] relative"
                            aria-label="Notifications"
                        >
                            <!-- Bell Icon -->
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 01-6 0v-1m6 0H9" 
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>

                            <!-- Badge -->
                            @if($notifications->whereNull('read_at')->count() > 0)
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5">
                                    {{ $notifications->whereNull('read_at')->count() }}
                                </span>
                            @endif
                        </button>

                        <div 
                            id="notificationDropdown" 
                            class="hidden absolute right-0 mt-2 w-72 bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden z-50"
                        >
                            <div class="flex justify-between items-center px-3 py-2 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                                <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                    Notifications
                                </span>
                                @if($notifications->whereNull('read_at')->count() > 0)
                                    <form method="POST" action="{{ route('notifications.markAllAsRead') }}">
                                        @csrf
                                        <button 
                                            type="submit" 
                                            class="text-xs text-blue-600 hover:underline dark:text-blue-400"
                                        >
                                            Mark all as read
                                        </button>
                                    </form>
                                @endif
                            </div>
                        <div class="max-h-80 overflow-y-auto">
                            @forelse($notifications as $notification)
                                @php
                                    $data = $notification->data;
                                    $firstName = $data['first_name'] ?? '';
                                    $lastName = $data['last_name'] ?? '';
                                    $initials = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
                                    $avatar = $data['by_user_avatar'] ?? null;
                                    $isUnread = is_null($notification->read_at);
                                @endphp

                                <div 
                                    class="flex items-center gap-3 p-3 border-b border-gray-200 dark:border-gray-700 
                                        hover:bg-gray-50 dark:hover:bg-gray-700
                                        {{ $isUnread ? 'bg-yellow-50 dark:bg-yellow-900/30' : 'bg-transparent' }}"
                                >
                                    @if(!empty($avatar))
                                        <img 
                                            src="{{ $avatar }}" 
                                            alt="{{ trim("$firstName $lastName") ?: 'User' }}"
                                            class="w-8 h-8 rounded-full object-cover"
                                        >
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-xs font-bold text-gray-700">
                                            {{ $initials ?: '?' }}
                                        </div>
                                    @endif

                                    <div class="flex flex-col">
                                        <p class="text-sm text-gray-800 dark:text-gray-200">
                                            @if($data['type'] === 'comment')
                                                {{ $firstName }} commented on your post
                                            @elseif($data['type'] === 'reply')
                                                {{ $firstName }} replied to your comment
                                            @elseif($data['type'] === 'like')
                                                {{ $firstName }} liked your post
                                            @else
                                                New activity
                                            @endif
                                        </p>
                                        <span class="text-xs text-gray-500">
                                            {{ $notification->created_at->diffForHumans() }}
                                            @if($isUnread)
                                                <span class="ml-2 inline-block w-2 h-2 bg-blue-500 rounded-full"></span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <div class="p-3 text-sm text-gray-500 dark:text-gray-300 text-center">
                                    No new notifications
                                </div>
                            @endforelse
                        </div>


                        </div>

                    </div>

                    <!-- Profile Avatar -->
                    <div class="relative">
                        <button class="rounded-full border-2 border-gray-300 dark:text-gray-500 overflow-hidden w-8 h-8">
                            <img src="/avatar1.jpg" alt="Profile" class="w-full h-full object-cover">
                        </button>
                    </div>
                </div>
            </div>
        </header>

      <main class="flex-1 flex">
    <!-- Sidebar -->
    <aside 
        x-cloak
        x-show="showSidebar || window.innerWidth >= 768"
        @click.away="if (window.innerWidth < 768) showSidebar = false"
        class="fixed inset-y-0 left-0 z-40 w-64 bg-white dark:bg-gray-800 border-r dark:border-gray-700 transform transition-transform duration-200 md:relative md:translate-x-0 md:block"
        :class="{ '-translate-x-full': !showSidebar && window.innerWidth < 768 }"
        >
       <div class="p-4 text-sm font-medium text-gray-700 dark:text-gray-300">

            <!-- Feeds -->
            <div class="mb-6">
                <a href="/member/community"
                   class="flex items-center space-x-2 px-4 py-2 rounded-md transition
                   {{ Request::is('/') ? 'bg-[#F0F3F5] text-[#545861] dark:text-[#E4E7EB] dark:bg-[#42464D] font-semibold' : 'hover:text-[#FFD736]' }}">
                    <i class="fa fa-home"></i>
                    <span>Feeds</span>
                </a>
            </div>

            <!-- Get Started -->
            <div x-data="{ open: true }" class="group relative mt-10 mb-6">
            <div @click="open = !open"
                class="flex items-center justify-between px-4 py-2 cursor-pointer hover:text-[#FFD736]">
                <div class="flex items-center space-x-2">
                    <span>Get Started</span>
                </div>
                <i class="fa fa-chevron-down text-xs text-gray-400 group-hover:inline hidden transition-transform"
                :class="{ 'rotate-180': !open }"></i>
            </div>

            <template x-if="open">
                <div class="pl-8 space-y-1 text-gray-500 dark:text-gray-400">
                    <a href="/member/say-hello"
                    class="flex items-center space-x-2 py-1 rounded-md transition 
                    {{ Request::is('say-hello') ? 'text-[#FFD736] font-semibold' : 'hover:text-[#FFD736]' }}">
                        <span>👋</span><span>Say Hello</span>
                    </a>
                    <a href="/ask-question"
                    class="flex items-center space-x-2 py-1 rounded-md transition 
                    {{ Request::is('ask-question') ? 'text-[#FFD736] font-semibold' : 'hover:text-[#FFD736]' }}">
                        <span>🙏</span><span>Ask Question</span>
                    </a>
                </div>
            </template>
        </div>


            <!-- Others -->
            <div x-data="{ open: true }" class="group relative mt-10 mb-6">
                <div @click="open = !open"
                    class="flex items-center justify-between px-4 py-2 cursor-pointer hover:text-[#FFD736]">
                    <div class="flex items-center space-x-2">
                        <span>Others</span>
                    </div>
                    <i class="fa fa-chevron-down text-xs text-gray-400 group-hover:inline hidden transition-transform"
                    :class="{ 'rotate-180': !open }"></i>
                </div>
                <template x-if="open">
                    <div class="pl-8 space-y-1 text-gray-500 dark:text-gray-400">
                        <a href="/post-progress"
                        class="flex items-center space-x-2 py-1 rounded-md transition 
                        {{ Request::is('post-progress') ? 'text-[#FFD736] font-semibold' : 'hover:text-[#FFD736]' }}">
                            <span>🎬</span><span>Post Progress</span>
                        </a>
                        <a href="/lessons"
                        class="flex items-center space-x-2 py-1 rounded-md transition 
                        {{ Request::is('lessons') ? 'text-[#FFD736] font-semibold' : 'hover:text-[#FFD736]' }}">
                            <span>🎹</span><span>Lessons</span>
                        </a>
                    </div>
                </template>
            </div>


            <!-- Forum -->
            <div x-data="{ open: true }" class="group relative mt-10">
                <div @click="open = !open"
                     class="flex items-center justify-between px-4 py-2 cursor-pointer hover:text-[#FFD736]">
                    <div class="flex items-center space-x-2">
                        <i class="fa fa-comments"></i>
                        <span>Forum</span>
                    </div>
                    <i class="fa fa-chevron-down text-xs text-gray-400 group-hover:inline hidden transition-transform"
                       :class="{ 'rotate-180': !open }"></i>
                </div>
                <template x-if="open">
                    <div class="pl-8 space-y-1 text-gray-500 dark:text-gray-400">
                        <a href="/forum/beginner"
                           class="flex items-center space-x-2 py-1 rounded-md transition 
                           {{ Request::is('forum/beginner') ? 'text-[#FFD736] font-semibold' : 'hover:text-[#FFD736]' }}">
                            <img src="/images/featured1.jpeg" class="h-5 " alt="Beginners forum"></i><span>Beginner</span>
                        </a>
                        <a href="/forum/intermediate"
                           class="flex items-center space-x-2 py-1 rounded-md transition 
                           {{ Request::is('forum/intermediate') ? 'text-[#FFD736] font-semibold' : 'hover:text-[#FFD736]' }}">
                           <img src="/images/featured2.jpeg" class="h-5 " alt="Intermediate forum"><span>Intermediate</span>
                        </a>
                        <a href="/forum/advance"
                           class="flex items-center space-x-2 py-1 rounded-md transition 
                           {{ Request::is('forum/advance') ? 'text-[#FFD736] font-semibold' : 'hover:text-[#FFD736]' }}">
                            <img src="/images/featured3.jpeg" class="h-5 " alt="Advance forum"><span>Advance</span>
                        </a>
                    </div>
                </template>
            </div>

            {{-- <div class="mt-8 fixed bottom-1 bg-gray-100 dark:bg-gray-700 rounded-md">
                <a href="/kingsleykhord.com/home"
                   class="flex items-center space-x-2 px-4 py-2 rounded-md transition 
                   {{ Request::is('kingsleykhord.com/home') ? 'bg-[#F0F3F5] text-[#545861] dark:text-[#E4E7EB] dark:bg-[#42464D] font-semibold' : 'hover:text-[#FFD736]' }}">
                    <i class="fa fa-sign-out" aria-hidden="true"></i>
                    <span>Back</span>
                    {{auth()->user()->first_name?? ""}} {{auth()->user()->last_name  ?? ''}}
                </a>
            </div> --}}

        </div>
    </aside>




    <!-- Page Content -->
    <section class="flex-1">
        @yield('content')
    </section>
</main>



    </div>

    <script>
      function themeToggle() {
    return {
        isDark: false,
        showSidebar: false, // <-- add this
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

    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
</body>

</html>
