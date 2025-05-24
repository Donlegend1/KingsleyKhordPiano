<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
    <script src="https://js.paystack.co/v2/inline.js" ></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />

    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body class="bg-white text-gray-800 dark:bg-gray-900 dark:text-gray-100">
    <div id="app">
     
     <header class="bg-black dark:bg-gray-800 shadow sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between space-x-6">
        
        <div class="flex items-center space-x-4 flex-shrink-0">
            <a href="/home" class="text-2xl font-bold">
                <img src="/logo/logo.png" alt="KingsleyKhord logo" class="h-8">
            </a>
            <span class="text-[#FFD736] flex items-center space-x-1">
                <i class="fa fa-user" aria-hidden="true"></i>
                <span>Member Area</span>
            </span>
        </div>

        <!-- Center: Search Bar -->
        <div class="hidden lg:flex flex-grow justify-center">
    <div class="relative w-full max-w-sm">
        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="fa fa-search text-gray-400"></i>
        </span>
        <input
            type="text"
            placeholder="Search..."
            class="w-full pl-10 pr-4 py-1.5 rounded-full bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#FFD736] focus:border-transparent text-sm"
        />
    </div>
</div>


        <!-- Right: Navigation & Mobile Menu Button -->
        <div class="flex items-center space-x-4">
            <nav class="hidden lg:flex items-center space-x-6">
                <a href="/member/getstarted"
                    class="text-sm font-semibold transition duration-200 {{ Request::is('/') ? 'text-white' : 'text-gray-400 hover:text-[#FFD736]' }}">
                    Get Started
                </a>
                <a href="#"
                    class="text-sm font-semibold transition duration-200 {{ Request::is('plans') ? 'text-white' : 'text-gray-400 hover:text-[#FFD736]' }}">
                    Community
                </a>
                  <a href="/member/shop"
                        class="block text-sm font-semibold transition duration-200 {{ Request::is('about') ? 'text-white' : 'text-gray-400 hover:text-[#FFD736]' }}">
                        Shop
                    </a>
                <a href="/member/profile"
                    class="text-sm font-semibold transition duration-200 {{ Request::is('about') ? 'text-white' : 'text-gray-400 hover:text-[#FFD736]' }}">
                    My Account
                </a>
                <a href="#"
                    class="text-sm font-semibold transition duration-200 {{ Request::is('contact') ? 'text-white' : 'text-gray-400 hover:text-[#FFD736]' }}">
                    Support
                </a>
               <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="text-sm font-semibold px-4 py-2 rounded-md   hover:bg-[#FFD736] text-gray-400 transition text-center" type="submit" >
                                {{ __('Logout') }}
                            </button>
                        </form>
            </nav>

            <!-- Mobile menu button -->
            <button class="lg:hidden navbar-burger" aria-label="Open Menu">
                <svg class="h-6 w-6 text-white dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Nav -->
    <div id="mobile-nav" class="lg:hidden hidden px-4 pb-4 space-y-4">
        <div class="flex flex-col space-y-2">
            <a href="/member/getstarted"
                class="block text-sm font-semibold transition duration-200 {{ Request::is('/') ? 'text-white' : 'text-gray-400 hover:text-[#FFD736]' }}">
                Get Started
            </a>
            <a href="#"
                class="block text-sm font-semibold transition duration-200 {{ Request::is('plans') ? 'text-white' : 'text-gray-400 hover:text-[#FFD736]' }}">
                Community
            </a>
            <a href="/member/profile"
                class="block text-sm font-semibold transition duration-200 {{ Request::is('about') ? 'text-white' : 'text-gray-400 hover:text-[#FFD736]' }}">
                My Account
            </a>
             <a href="/member/shop"
                class="block text-sm font-semibold transition duration-200 {{ Request::is('about') ? 'text-white' : 'text-gray-400 hover:text-[#FFD736]' }}">
               Shop
            </a>
            <a href="#"
                class="block text-sm font-semibold transition duration-200 {{ Request::is('contact') ? 'text-white' : 'text-gray-400 hover:text-[#FFD736]' }}">
                Support
            </a>
        </div>
        <div class="flex flex-col space-y-2 mt-4">
          
                 <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="text-sm font-semibold px-4 py-2 rounded-md border border-[#FFD736] text-[#FFD736] hover:bg-[#FFD736] hover:text-black transition text-center" type="submit" >
                                {{ __('Logout') }}
                            </button>
                        </form>
        
        </div>
    </div>
   
</header>
<section class="bg-gray-900 text-white py-7 shadow sticky top-0 ">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-wrap justify-between items-center space-x-4 overflow-x-auto">

        <a href="/home" class="flex items-center space-x-2 text-sm hover:text-[#FFD736] transition">
           <img src="/icons/dashboard.svg">
            <span>Dashboard</span>
        </a>

        <a href="/member/roadmap" class="flex items-center space-x-2 text-sm hover:text-[#FFD736] transition">
            <img src="/icons/roadmap2.png">
            <span>Roadmap</span>
        </a>

        <a href="/member/piano-exercise" class="flex items-center space-x-2 text-sm hover:text-[#FFD736] transition">
            <img src="/icons/piano2.png">
            <span>Piano Exercise</span>
        </a>

        <a href="/member/ear-training" class="flex items-center space-x-2 text-sm hover:text-[#FFD736] transition">
            <img src="/icons/eartraning.svg">
            <span>Ear Training</span>
        </a>

        <a href="/member/extra-courses" class="flex items-center space-x-2 text-sm hover:text-[#FFD736] transition">
            <img src="/icons/extracourse.svg">
            <span>Extra Courses</span>
        </a>

        <a href="/member/quick-lessons" class="flex items-center space-x-2 text-sm hover:text-[#FFD736] transition">
            <img src="/icons/quick lession.svg">
            <span>Quick Lesson</span>
        </a>

        <a href="/member/learn-songs" class="flex items-center space-x-2 text-sm hover:text-[#FFD736] transition">
            <img src="/icons/songs.svg">
            <span>Learn Songs</span>
        </a>

        <a href="/member/live-session" class="flex items-center space-x-2 text-sm hover:text-[#FFD736] transition">
            <img src="/icons/livesession.svg">
            <span>Live Session</span>
        </a>

    </div>
</section>

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
        <main >
            @yield('content')
        </main>

        <footer class="bg-gray-100 shadow sticky bottom-0 z-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-5">
        <div class="max-w-7xl mx-auto  px-4 flex flex-col md:flex-row items-center justify-center text-sm text-gray-500 dark:text-gray-400">
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

</body>
</html>
