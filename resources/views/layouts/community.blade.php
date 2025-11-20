<!DOCTYPE html>
<html lang="en">
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
<body x-data="{ isDark: false, showSidebar: false }" x-init class="bg-gray-100 dark:bg-gray-900 h-screen overflow-hidden">

<div class="flex h-screen">
    <!-- Sidebar -->
    <aside 
        x-cloak
        x-show="showSidebar || window.innerWidth >= 768"
        @click.away="if (window.innerWidth < 768) showSidebar = false"
        class="fixed inset-y-0 left-0 z-40 w-64 sm:w-[250px] bg-[#FAFAFA] dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform transition-transform duration-200 md:relative md:translate-x-0 md:block"
        :class="{ '-translate-x-full': !showSidebar && window.innerWidth < 768 }"
        >
       <div class="px-5 py-4 font-sans h-full overflow-y-auto scrollbar-hide" style="-webkit-overflow-scrolling: touch;">
           <style>
               .scrollbar-hide::-webkit-scrollbar {
                   display: none;
               }
               .scrollbar-hide {
                   -ms-overflow-style: none;
                   scrollbar-width: none;
               }
           </style>

            <!-- Logo Section -->
            <div class="hidden md:block mb-10 px-5">
                <a href="/member/community" class="flex items-center justify-center">
                    <img src="/logo/logoblack.png" alt="Logo" class="h-12 w-auto">
                </a>
            </div>

            <!-- Community Section -->
            <div class="mb-6 mt-16 md:mt-0">
                <h3 class="text-[#9CA3AF] dark:text-gray-400 text-[11px] font-bold tracking-[1px] mb-2 mt-3">COMMUNITY</h3>
                <div class="space-y-1">
                <a href="/member/community"
                       class="flex items-center gap-3 px-2 py-3 rounded-lg transition-colors duration-200 hover:bg-[#F3F4F6] dark:hover:bg-gray-700 {{ Request::is('member/community') ? 'bg-[#F3F4F6] dark:bg-gray-700' : '' }}">
                        <svg class="w-5 h-5 text-[#6B7280]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                        </svg>
                        <span class="text-[#6B7280] dark:text-gray-300 text-sm font-medium">Activity Feed</span>
                    </a>
                    <a href="/member/community/space/say-hello"
                       class="flex items-center gap-3 px-2 py-3 rounded-lg transition-colors duration-200 hover:bg-[#F3F4F6] dark:hover:bg-gray-700 {{ Request::is('member/community/space/say-hello') ? 'bg-[#F3F4F6] dark:bg-gray-700' : '' }}">
                        <svg class="w-5 h-5 text-[#6B7280] dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6v2.5l6.5-2.5-6.5-2.5z"></path>
                        </svg>
                        <span class="text-[#6B7280] dark:text-gray-300 text-sm font-medium">Say Hello</span>
                    </a>
                    <a href="/member/community/space/progress-report"
                       class="flex items-center gap-3 px-2 py-3 rounded-lg transition-colors duration-200 hover:bg-[#F3F4F6] dark:hover:bg-gray-700 {{ Request::is('member/community/space/progress-report') ? 'bg-[#F3F4F6] dark:bg-gray-700' : '' }}">
                        <svg class="w-5 h-5 text-[#6B7280] dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span class="text-[#6B7280] dark:text-gray-300 text-sm font-medium">Progress Report</span>
                    </a>
                    <a href="/member/community/space/lessons"
                       class="flex items-center gap-3 px-2 py-3 rounded-lg transition-colors duration-200 hover:bg-[#F3F4F6] dark:hover:bg-gray-700 {{ Request::is('member/community/space/lessons') ? 'bg-[#F3F4F6] dark:bg-gray-700' : '' }}">
                        <svg class="w-5 h-5 text-[#6B7280] dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <span class="text-[#6B7280] dark:text-gray-300 text-sm font-medium">Lessons</span>
                    </a>
                </div>
        </div>

            <!-- Forums Section -->
            <div class="mb-6">
                <h3 class="text-[#9CA3AF] dark:text-gray-400 text-[11px] font-bold tracking-[1px] mb-2">FORUMS</h3>
                <div class="space-y-1">
                    <a href="/member/community/space/beginner"
                       class="flex items-center gap-3 px-2 py-3 rounded-lg transition-colors duration-200 hover:bg-[#F3F4F6] dark:hover:bg-gray-700 {{ Request::is('member/community/space/beginner') ? 'bg-[#F3F4F6] dark:bg-gray-700' : '' }}">
                        <svg class="w-5 h-5 text-[#6B7280] dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                        <span class="text-[#6B7280] dark:text-gray-300 text-sm font-medium">Beginner</span>
                    </a>
                    <a href="/member/community/space/intermediate"
                       class="flex items-center gap-3 px-2 py-3 rounded-lg transition-colors duration-200 hover:bg-[#F3F4F6] dark:hover:bg-gray-700 {{ Request::is('member/community/space/intermediate') ? 'bg-[#F3F4F6] dark:bg-gray-700' : '' }}">
                        <svg class="w-5 h-5 text-[#6B7280] dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.5 9.5l3-3 3 3"></path>
                        </svg>
                        <span class="text-[#6B7280] dark:text-gray-300 text-sm font-medium">Intermediate</span>
                    </a>
                    <a href="/member/community/space/advanced"
                       class="flex items-center gap-3 px-2 py-3 rounded-lg transition-colors duration-200 hover:bg-[#F3F4F6] dark:hover:bg-gray-700 {{ Request::is('member/community/space/advanced') ? 'bg-[#F3F4F6] dark:bg-gray-700' : '' }}">
                        <svg class="w-5 h-5 text-[#6B7280] dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.5 9.5l3-3 3 3"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.5v6"></path>
                        </svg>
                        <span class="text-[#6B7280] dark:text-gray-300 text-sm font-medium">Advanced</span>
                    </a>
                </div>
            </div>

            <!-- Members Only Section -->
            <div class="mb-6">
                <h3 class="text-[#9CA3AF] dark:text-gray-400 text-[11px] font-bold tracking-[1px] mb-2">MEMBERS ONLY</h3>
                <div class="space-y-1">
                    <a href="/member/community/space/exclusive-feed"
                       class="flex items-center gap-3 px-2 py-3 rounded-lg transition-colors duration-200 hover:bg-[#F3F4F6] dark:hover:bg-gray-700 {{ Request::is('member/community/space/exclusive-feed') ? 'bg-[#F3F4F6] dark:bg-gray-700' : '' }}">
                        <svg class="w-5 h-5 text-[#6B7280] dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <span class="text-[#6B7280] dark:text-gray-300 text-sm font-medium">Exclusive Feed</span>
                    </a>
                    <a href="/member/community/space/pdf-downloads"
                       class="flex items-center gap-3 px-2 py-3 rounded-lg transition-colors duration-200 hover:bg-[#F3F4F6] dark:hover:bg-gray-700 {{ Request::is('member/community/space/pdf-downloads') ? 'bg-[#F3F4F6] dark:bg-gray-700' : '' }}">
                        <svg class="w-5 h-5 text-[#6B7280] dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="text-[#6B7280] dark:text-gray-300 text-sm font-medium">Pdf Downloads</span>
                    </a>
                    <a href="/member/community/space/audio-downloads"
                       class="flex items-center gap-3 px-2 py-3 rounded-lg transition-colors duration-200 hover:bg-[#F3F4F6] dark:hover:bg-gray-700 {{ Request::is('member/community/space/audio-downloads') ? 'bg-[#F3F4F6] dark:bg-gray-700' : '' }}">
                        <svg class="w-5 h-5 text-[#6B7280] dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                        </svg>
                        <span class="text-[#6B7280] dark:text-gray-300 text-sm font-medium">Audio Downloads</span>
                    </a>
                    <a href="/member/community/space/midi-downloads"
                       class="flex items-center gap-3 px-2 py-3 rounded-lg transition-colors duration-200 hover:bg-[#F3F4F6] dark:hover:bg-gray-700 {{ Request::is('member/community/space/midi-downloads') ? 'bg-[#F3F4F6] dark:bg-gray-700' : '' }}">
                        <svg class="w-5 h-5 text-[#6B7280] dark:text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14.23 12.004a2.236 2.236 0 0 1 2.235 2.236A2.236 2.236 0 0 1 14.23 16.476a2.236 2.236 0 0 1-2.235-2.236 2.236 2.236 0 0 1 2.235-2.236zm2.648-10.69c.366 0 .662.297.662.662v3.34c1.154.28 2.01 1.32 2.01 2.585a2.67 2.67 0 0 1-2.667 2.667h-4.666a2.67 2.67 0 0 1-2.667-2.667c0-1.265.856-2.305 2.01-2.585V1.976c0-.365.296-.662.662-.662h2.326zm-.662 4.666c-.735 0-1.333.598-1.333 1.333 0 .735.598 1.333 1.333 1.333h4.666c.735 0 1.333-.598 1.333-1.333 0-.735-.598-1.333-1.333-1.333h-4.666z"/>
                        </svg>
                        <span class="text-[#6B7280] dark:text-gray-300 text-sm font-medium">Midi Files</span>
                    </a>
                    <a href="/member/community/space/piano-breakdowns"
                       class="flex items-center gap-3 px-2 py-3 rounded-lg transition-colors duration-200 hover:bg-[#F3F4F6] dark:hover:bg-gray-700 {{ Request::is('member/community/space/piano-breakdowns') ? 'bg-[#F3F4F6] dark:bg-gray-700' : '' }}">
                        <span class="text-lg">🎹</span>
                        <span class="text-[#6B7280] dark:text-gray-300 text-sm font-medium">Piano Breakdowns</span>
                    </a>
                    </div>
            </div>

            <!-- Quick Links - Mobile Only -->
            <div class="block lg:hidden mt-6">
                <h3 class="text-[#9CA3AF] dark:text-gray-400 text-[11px] font-bold tracking-[1px] mb-2">QUICK LINKS</h3>
                <div class="space-y-1">
                    <a href="/home"
                       class="flex items-center gap-3 px-2 py-3 rounded-lg transition-colors duration-200 hover:bg-[#F3F4F6] dark:hover:bg-gray-700">
                        <svg class="w-5 h-5 text-[#6B7280] dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                        </svg>
                        <span class="text-[#6B7280] dark:text-gray-300 text-sm font-medium">Dashboard</span>
                    </a>
                    <a href="/member/shop"
                       class="flex items-center gap-3 px-2 py-3 rounded-lg transition-colors duration-200 hover:bg-[#F3F4F6] dark:hover:bg-gray-700">
                        <svg class="w-5 h-5 text-[#6B7280] dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <span class="text-[#6B7280] dark:text-gray-300 text-sm font-medium">Shop Plugins</span>
                    </a>
                    <a href="/member/live-session"
                       class="flex items-center gap-3 px-2 py-3 rounded-lg transition-colors duration-200 hover:bg-[#F3F4F6] dark:hover:bg-gray-700">
                        <svg class="w-5 h-5 text-[#6B7280] dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 10l2 2-2 2"></path>
                        </svg>
                        <span class="text-[#6B7280] dark:text-gray-300 text-sm font-medium">Live Sessions</span>
                    </a>
                    <a href="/member/support"
                       class="flex items-center gap-3 px-2 py-3 rounded-lg transition-colors duration-200 hover:bg-[#F3F4F6] dark:hover:bg-gray-700">
                        <svg class="w-5 h-5 text-[#6B7280] dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span class="text-[#6B7280] dark:text-gray-300 text-sm font-medium">Support</span>
                    </a>
                </div>
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

    <!-- Main area -->
    <div class="flex-1 flex flex-col">
        <!-- Header -->
        <header class="bg-white dark:bg-gray-800 shadow sticky top-0 z-50">
            <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
              <button @click="showSidebar = !showSidebar" class="md:hidden text-gray-500 dark:text-gray-300 hover:text-[#FFD736]">
                  <i class="fa fa-bars text-xl"></i>
              </button>
                          
                <!-- Left: Logo -->
                <a href="/member/community" class="flex items-center space-x-2 md:hidden">
                    <img src="/logo/logowhite.webp" alt="Logo" class="h-10 w-auto">
                </a>

                <!-- Center: Quick Links -->
                <nav class="hidden lg:flex items-center space-x-16">
                    <a href="/home" class="flex items-center space-x-1 text-gray-500 dark:text-gray-300 hover:text-[#FFD736] transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                        </svg>
                        <span class="text-sm font-medium">Dashboard</span>
                    </a>
                    <a href="/member/shop" class="flex items-center space-x-1 text-gray-500 dark:text-gray-300 hover:text-[#FFD736] transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <span class="text-sm font-medium">Shop Plugins</span>
                    </a>
                    <a href="/member/live-session" class="flex items-center space-x-1 text-gray-500 dark:text-gray-300 hover:text-[#FFD736] transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 10l2 2-2 2"></path>
                        </svg>
                        <span class="text-sm font-medium">Live Sessions</span>
                    </a>
                    <a href="/member/support" class="flex items-center space-x-1 text-gray-500 dark:text-gray-300 hover:text-[#FFD736] transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span class="text-sm font-medium">Support</span>
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

                    <button class="text-gray-500 dark:text-gray-300 hover:text-[#FFD736]" aria-label="Bookmarks">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"
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
        type="button"
        onclick="document.getElementById('notificationDropdown').classList.toggle('hidden')" 
        class="text-gray-500 dark:text-gray-300 hover:text-[#FFD736] relative"
        aria-label="Notifications"
    >
        <!-- Bell Icon -->
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path 
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 01-6 0v-1m6 0H9" 
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>

        <!-- Badge -->
        @if($notifications->whereNull('read_at')->count() > 0)
            <span 
                class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5"
            >
                {{ $notifications->whereNull('read_at')->count() }}
            </span>
        @endif
    </button>

    <!-- Dropdown -->
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

        <!-- Notifications list -->
        <div class="max-h-80 overflow-y-auto">
            @forelse($notifications as $notification)
                @php
                    $data = $notification->data;
                    $firstName = $data['first_name'] ?? '';
                    $lastName = $data['last_name'] ?? '';
                    $initials = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
                    $avatar = $data['by_user_avatar'] ?? null;
                    $isUnread = is_null($notification->read_at);
                    $postId = $data['post_id'] ?? null;
                @endphp

                <a 
                    href="/member/post/{{ $postId }}" 
                    class="block hover:no-underline"
                >
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
                </a>
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

        <!-- Scrollable content area -->
        <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900">
            @yield('content')
        </main>
    </div>
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
