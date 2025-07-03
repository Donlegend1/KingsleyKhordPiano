<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
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
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script type="text/javascript" src="https://assets.calendly.com/assets/external/widget.js" async></script>
    <script src="https://js.paystack.co/v2/inline.js" ></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />

    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.js']) 

</head>
<body class="bg-white text-gray-800 dark:bg-gray-900 dark:text-gray-100 min-h-screen flex flex-col">
    <div id="app" class="flex flex-col flex-grow">
     
    <header x-data="{ openMobileNav: false, showLogoutModal: false }" class="bg-black dark:bg-gray-800 shadow sticky top-0 z-50">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between space-x-6">

        <!-- Logo & Member Area -->
        <div class="flex items-center space-x-4 flex-shrink-0">
            <a href="/" class="text-2xl font-bold">
                <img src="/logo/logo.png" alt="KingsleyKhord logo" class="h-8">
            </a>
            <span class="text-[#FFD736] flex items-center space-x-1">
                <i class="fa fa-user" aria-hidden="true"></i>
                <span>Member Area</span>
            </span>
        </div>

        <!-- Search Bar (desktop only) -->
        <div class="hidden lg:flex flex-grow justify-center">
            <div class="relative w-full max-w-sm">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa fa-search text-gray-400"></i>
                </span>
                <input type="text" placeholder="Search..." class="w-full pl-10 pr-4 py-1.5 rounded-full bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#FFD736] focus:border-transparent text-sm">
            </div>
        </div>

        <!-- Right-side nav & mobile button -->
        <div class="flex items-center space-x-4">

            <nav class="hidden lg:flex items-center space-x-3">
                <a href="/member/getstarted" class="text-sm font-semibold {{ Request::is('member/getstarted') ? 'text-white' : 'text-gray-400 hover:text-[#FFD736]' }}">Get Started</a>
                <a href="/member/community" class="text-sm font-semibold {{ Request::is('member/community') ? 'text-white' : 'text-gray-400 hover:text-[#FFD736]' }}">Community</a>
                <a href="/member/shop" class="text-sm font-semibold {{ Request::is('member/shop') ? 'text-white' : 'text-gray-400 hover:text-[#FFD736]' }}">Shop</a>
                <a href="/member/profile" class="text-sm font-semibold {{ Request::is('member/profile') ? 'text-white' : 'text-gray-400 hover:text-[#FFD736]' }}">My Account</a>
                <a href="/member/support" class="text-sm font-semibold {{ Request::is('member/support') ? 'text-white' : 'text-gray-400 hover:text-[#FFD736]' }}">Support</a>

                <button @click="showLogoutModal = true" class="text-sm font-semibold px-4 py-2 rounded-md hover:bg-[#FFD736] text-gray-700 bg-gray-100 transition">
                    {{ __('Logout') }}
                </button>
            </nav>

            <!-- Mobile Menu Button -->
            <button @click="openMobileNav = true" class="lg:hidden navbar-burger" aria-label="Open Menu">
                <svg class="h-6 w-6 text-white dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

        </div>

    </div>

    <!-- Mobile Nav Sliding Sidebar -->
    <div x-show="openMobileNav" class="fixed inset-0 z-50 flex" x-transition>
        <div class="fixed inset-0 bg-black bg-opacity-60" @click="openMobileNav = false"></div>

        <div class="relative bg-black text-white w-72 max-w-full h-full overflow-y-auto p-4 transform transition-transform duration-300" :class="openMobileNav ? 'translate-x-0' : '-translate-x-full'">

            <div class="flex justify-end mb-4">
                <button @click="openMobileNav = false">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="space-y-6">
                <div class="flex flex-col divide-y divide-gray-700 border border-gray-700 rounded-md overflow-hidden">
                   @php
                      $navItems = [
                        ['url' => 'home', 'label' => 'Dashboard', 'icon' => 'dashboard.svg'],
                        ['url' => 'member/roadmap', 'label' => 'Roadmap', 'icon' => 'roadmap2.png'],
                        ['url' => 'member/piano-exercise', 'label' => 'Piano Exercise', 'icon' => 'piano2.png'],
                        ['url' => 'member/ear-training', 'label' => 'Ear Training', 'icon' => 'eartraning.svg'],
                        ['url' => 'member/extra-courses', 'label' => 'Extra Courses', 'icon' => 'extracourse.svg'],
                        ['url' => 'member/quick-lessons', 'label' => 'Quick Lesson', 'icon' => 'quick lession.svg'],
                        ['url' => 'member/learn-songs', 'label' => 'Learn Songs', 'icon' => 'songs.svg'],
                        ['url' => 'member/live-session', 'label' => 'Live Session', 'icon' => 'livesession.svg'],
                      ];
                  @endphp
                    @foreach ($navItems as $item)
                        <a href="/{{ $item['url'] }}" class="flex items-center gap-3 text-sm px-3 py-3 transition {{ Request::is($item['url']) ? 'bg-gray-700 text-white' : 'hover:text-[#FFD736]' }}">
                            <img src="/icons/{{ $item['icon'] }}" class="h-5 w-auto" alt="{{ $item['label'] }}" />
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>

                <div class="flex flex-col gap-2">
                <a href="/member/getstarted" class=" text-sm font-semibold px-3 py-1 text-gray-400 hover:text-[#FFD736]">Get Started</a>
                <a href="/member/community" class=" text-sm font-semibold px-3 py-1 text-gray-400 hover:text-[#FFD736]">Community</a>
                <a href="/member/profile" class=" text-sm font-semibold px-3 py-1 text-gray-400 hover:text-[#FFD736]">My Account</a>
                <a href="/member/shop" class=" text-sm font-semibold px-3 py-1 text-gray-400 hover:text-[#FFD736]">Shop</a>
                <a href="/member/support" class=" text-sm font-semibold px-3 py-1 text-gray-400 hover:text-[#FFD736]">Support</a>
                </div>
                <button @click="showLogoutModal = true" class="mt-4 text-sm font-semibold px-4 py-2 rounded-md hover:bg-[#FFD736] text-white bg-red-500">Logout <i class="fa fa-sign-out" aria-hidden="true"></i></button>

            </div>
        </div>
    </div>

    <!-- Logout Modal -->
    <div x-show="showLogoutModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div @click.away="showLogoutModal = false" class="bg-white rounded-lg shadow-xl w-full max-w-sm p-6">
            <div class="text-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800 mb-2">Confirm Logout</h2>
                <p class="text-gray-600">Are you sure you want to log out?</p>
            </div>
            <div class="flex justify-between space-x-3">
                <button @click="showLogoutModal = false" class="px-4 py-2 text-sm text-gray-600 bg-gray-200 rounded hover:bg-gray-300">Cancel</button>
                <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">@csrf</form>
                <button @click="document.getElementById('logout-form').submit()" class="px-4 py-2 text-sm text-white bg-red-600 rounded hover:bg-red-700">Logout</button>
            </div>
        </div>
    </div>
    <div class="hidden md:flex bg-black dark:bg-gray-800 shadow sticky top-0 z-50 text-white overflow-x-auto">

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between gap-x-4 flex-nowrap">

                <a href="/home"
                  class="flex items-center gap-x-2 text-sm px-3 py-2 rounded transition whitespace-nowrap
                  {{ Request::is('home') ? 'bg-gray-700 text-white' : 'hover:text-[#FFD736]' }}">
                    <img src="/icons/dashboard.svg" class="h-5 w-auto">
                    <span>Dashboard</span>
                </a>

                <a href="/member/roadmap"
                  class="flex items-center gap-x-2 text-sm px-3 py-2 rounded transition whitespace-nowrap
                  {{ Request::is('member/roadmap') ? 'bg-gray-700 text-white' : 'hover:text-[#FFD736]' }}">
                    <img src="/icons/roadmap2.png" class="h-5 w-auto">
                    <span>Roadmap</span>
                </a>

                <a href="/member/piano-exercise"
                  class="flex items-center gap-x-2 text-sm px-3 py-2 rounded transition whitespace-nowrap
                  {{ Request::is('member/piano-exercise') ? 'bg-gray-700 text-white' : 'hover:text-[#FFD736]' }}">
                    <img src="/icons/piano2.png" class="h-5 w-auto">
                    <span>Piano Exercise</span>
                </a>

                <a href="/member/ear-training"
                  class="flex items-center gap-x-2 text-sm px-3 py-2 rounded transition whitespace-nowrap
                  {{ Request::is('member/ear-training') ? 'bg-gray-700 text-white' : 'hover:text-[#FFD736]' }}">
                    <img src="/icons/eartraning.svg" class="h-5 w-auto">
                    <span>Ear Training</span>
                </a>

                <a href="/member/extra-courses"
                  class="flex items-center gap-x-2 text-sm px-3 py-2 rounded transition whitespace-nowrap
                  {{ Request::is('member/extra-courses') ? 'bg-gray-700 text-white' : 'hover:text-[#FFD736]' }}">
                    <img src="/icons/extracourse.svg" class="h-5 w-auto">
                    <span>Extra Courses</span>
                </a>

                <a href="/member/quick-lessons"
                  class="flex items-center gap-x-2 text-sm px-3 py-2 rounded transition whitespace-nowrap
                  {{ Request::is('member/quick-lessons') ? 'bg-gray-700 text-white' : 'hover:text-[#FFD736]' }}">
                    <img src="/icons/quick lession.svg" class="h-5 w-auto">
                    <span>Quick Lesson</span>
                </a>

                <a href="/member/learn-songs"
                  class="flex items-center gap-x-2 text-sm px-3 py-2 rounded transition whitespace-nowrap
                  {{ Request::is('member/learn-songs') ? 'bg-gray-700 text-white' : 'hover:text-[#FFD736]' }}">
                    <img src="/icons/songs.svg" class="h-5 w-auto">
                    <span>Learn Songs</span>
                </a>

                <a href="/member/live-session"
                  class="flex items-center gap-x-2 text-sm px-3 py-2 rounded transition whitespace-nowrap
                  {{ Request::is('member/live-session') ? 'bg-gray-700 text-white' : 'hover:text-[#FFD736]' }}">
                    <img src="/icons/livesession.svg" class="h-5 w-auto">
                    <span>Live Session</span>
                </a>

            </div>
        </div>

</header>


    @if(session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session()->get('success') }}
        </div>
    @endif

  @if(session()->has('error'))
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
          {{ session()->get('error') }}
      </div>
  @endif
        <main  class="flex-grow">
            @yield('content')
        </main>

         <footer class="bg-gray-100 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-5">
            <div class="max-w-7xl mx-auto px-4 flex flex-col md:flex-row items-center justify-center text-sm text-gray-500 dark:text-gray-400">
                <div class="flex items-center">
                    <div>&copy; {{ date('Y') }} {{ config('app.name') }}</div>
                    <div class="h-4 border-l border-gray-400 mx-2"></div>
                    <div>All rights reserved.</div>
                </div>
            </div>
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const burger = document.querySelector('.navbar-burger');
            const nav = document.getElementById('mobile-nav');

            burger?.addEventListener('click', () => {
                nav.classList.toggle('hidden');
            });
        });
    </script>

    <script>
  function changeTab(tab) {
    // Hide all tab contents
    const contents = document.querySelectorAll('.tab-content');
    contents.forEach(content => {
      content.classList.add('hidden');
    });

    // Remove active state from all buttons
    const buttons = document.querySelectorAll('[data-tab]');
    buttons.forEach(button => {
      button.classList.remove('bg-gray-300');
      button.classList.add('bg-transparent');
    });

    // Show content of the selected tab
    const activeContent = document.querySelector(`[data-content="${tab}"]`);
    activeContent.classList.remove('hidden');

    // Add active state to the selected button
    const activeButton = document.querySelector(`[data-tab="${tab}"]`);
    activeButton.classList.remove('bg-transparent');
    activeButton.classList.add('bg-gray-300');
  }

  // Initially set the first tab as active
  document.addEventListener("DOMContentLoaded", function() {
    changeTab('beginners');
  });
</script>

 <script>
    window.authUser = @json(auth()->user());
</script>
</body>
</html>
