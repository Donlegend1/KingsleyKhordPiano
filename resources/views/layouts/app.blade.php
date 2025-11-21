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
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://js.paystack.co/v2/inline.js"></script>
    <script src="https://js.stripe.com/v3/"></script>

    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.js']) 
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
</head>
<body class="bg-white text-gray-800 dark:bg-gray-900 dark:text-gray-100">
    <div id="app">
    <header class="bg-black dark:bg-gray-800 shadow top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
            <!-- Left: Logo -->
            <div class="flex items-center flex-shrink-0">
                 <div class="flex items-center space-x-3 flex-shrink-0 relative">
            <a href="/" class="text-2xl font-bold">
                <img src="/logo/logo.png" alt="KingsleyKhord logo" class="h-8 w-auto">
            </a>
            @php
            use App\Models\Liveshow;
            use Carbon\Carbon;
            
            $liveshow = Liveshow::where('start_time', '>=', Carbon::now())
                ->orderBy('start_time', 'asc')
                ->first();
            @endphp

           @if(isset($liveshow) && $liveshow)
            <div x-data="{ open: false }" class="relative">
                <!-- Live icon -->
               <button
                    @click="open = !open"
                    class="flex items-center justify-center gap-1 relative rounded-md
                        px-2 sm:px-3 py-1 sm:py-1.5 bg-red-600 text-white text-xs sm:text-sm 
                        font-bold uppercase tracking-wider 
                        hover:scale-110 transition-transform animate-pulse shadow-md"
                    title="Live show now"
                >
                    <i class="fa fa-circle text-[8px] sm:text-[10px] text-white animate-ping"></i>
                    <span>Live</span>
                </button>

                <!-- Tooltip -->
                <div
                    x-show="open"
                    x-transition
                    @mouseenter="open = true"
                    @mouseleave="open = false"
                    x-cloak
                    class="absolute top-10 left-full ml-3 w-60 bg-white text-black rounded-lg shadow-lg p-3 z-50"
                >
                    <!-- Arrow -->
                    <div class="absolute -left-1 top-3 w-3 h-3 bg-white rotate-45 shadow-sm"></div>

                    <h3 class="text-sm font-semibold text-gray-800">{{ $liveshow->title }}</h3>
                    <p class="text-xs text-gray-600">
                        {{ \Carbon\Carbon::parse($liveshow->start_time)->format('M d, Y h:i A') }}
                    </p>
                    @if($liveshow->title)
                    <p class="text-xs text-gray-500 mt-1">{{ Str::limit($liveshow->title, 80) }}</p>
                    @endif
                </div>
            </div>
            @endif

            </div>
            </div>

            <!-- Center: Desktop Nav -->
            <nav class="hidden lg:grid grid-flow-col auto-cols-max gap-x-10 items-center justify-center flex-1">
                <a href="/"
                    class="text-lg font-semibold transition duration-200 {{ Request::is('/') ? 'text-white' : 'text-gray-400 hover:text-[#FFD736]' }}">
                    Home
                </a>
                <a href="/about"
                    class="text-lg font-semibold transition duration-200 {{ Request::is('about') ? 'text-white' : 'text-gray-400 hover:text-[#FFD736]' }}">
                    About
                </a>
                <a href="/contact"
                    class="text-lg font-semibold transition duration-200 {{ Request::is('contact') ? 'text-white' : 'text-gray-400 hover:text-[#FFD736]' }}">
                    Contact
                </a>
                <a href="https://khordsounds.com/"
                    target="blank"
                    class="text-lg font-semibold transition duration-200 text-gray-400 hover:text-[#FFD736]">
                    Shop
                </a>
            </nav>

            <div class="hidden lg:flex items-center space-x-4">
            <a href="/register"
                class="text-lg font-semibold px-4 py-2 rounded-lg bg-transparent border border-gray-300 text-white hover:bg-[#FFD736] hover:text-black shadow transition duration-200">
                Sign Up
            </a>
            <a href="/login"
                class="text-lg font-semibold px-4 py-2 rounded-lg bg-gray-500 text-white hover:bg-[#FFD736] hover:text-black shadow transition duration-200">
                Login
            </a>
        </div>



            <!-- Mobile Toggle -->
            <div class="lg:hidden">
                <button class="navbar-burger" aria-label="Open Menu">
                    <svg class="h-6 w-6 text-white dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Nav -->
            <div id="mobile-nav" class="lg:hidden hidden px-4 pb-4">
                <div class="flex flex-col divide-y divide-gray-600 border border-gray-700 rounded-md overflow-hidden">
                    <a href="/"
                        class="block text-sm font-semibold transition duration-200 py-3 px-2 {{ Request::is('/') ? 'text-white' : 'text-gray-400 hover:text-[#FFD736]' }}">
                        Home
                    </a>
                    <a href="/about"
                        class="block text-sm font-semibold transition duration-200 py-3 px-2 {{ Request::is('about') ? 'text-white' : 'text-gray-400 hover:text-[#FFD736]' }}">
                        About
                    </a>
                    <a href="/contact"
                        class="block text-sm font-semibold transition duration-200 py-3 px-2 {{ Request::is('contact') ? 'text-white' : 'text-gray-400 hover:text-[#FFD736]' }}">
                        Contact
                    </a>
                    <a href="https://khordsounds.com/"
                    target="blank"
                        class="block text-sm font-semibold transition duration-200 py-3 px-2  'text-white' ">
                        Shop
                    </a>
                </div>

                <div class="flex flex-col space-y-2 mt-4">
                    <a href="/register"
                        class="text-sm font-semibold px-4 py-2 rounded-md border border-[#FFD736] text-[#FFD736] hover:bg-[#FFD736] hover:text-white transition text-center">
                        Sign Up
                    </a>
                    <a href="/login"
                        class="text-sm font-semibold px-4 py-2 rounded-md border border-[#FFD736] text-[#FFD736] hover:bg-[#FFD736] hover:text-white transition text-center">
                        Login
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

        <!-- Main Content -->
        <main >
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-gray-100 shadow bottom-0 z-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-5">
            <div class="max-w-7xl mx-auto px-4 flex flex-col md:flex-row items-center justify-center text-sm text-gray-500 dark:text-gray-400 space-y-2 md:space-y-0 md:space-x-4">
                <div class="flex items-center space-x-2">
                    <div>&copy; {{ date('Y') }} {{ config('app.name') }}</div>
                    <div class="h-4 border-l border-gray-400 mx-2"></div>
                    <div>All rights reserved.</div>
                </div>
                <div class="flex items-center space-x-2">
                    <a href="/privacy-policy" class="hover:underline">Privacy Policy</a>
                    <span class="mx-1">|</span>
                    <a href="/terms-of-service" class="hover:underline">Terms of Service</a>
                </div>
            </div>
        </footer>

    </div>

    {!! NoCaptcha::renderJs() !!}

    <script>
        // Toggle mobile nav
        document.addEventListener('DOMContentLoaded', () => {
            const burger = document.querySelector('.navbar-burger');
            const nav = document.getElementById('mobile-nav');

            burger?.addEventListener('click', () => {
                nav.classList.toggle('hidden');
            });
        });
    </script>
 <script>
    const slider = document.getElementById('slider');
    const slides = slider.children;
    const totalSlides = slides.length;
    let currentIndex = 0;

    document.getElementById('nextBtn').addEventListener('click', () => {
      if (currentIndex < totalSlides - 1) currentIndex++;
      updateSlider();
    });

    document.getElementById('prevBtn').addEventListener('click', () => {
      if (currentIndex > 0) currentIndex--;
      updateSlider();
    });

    function updateSlider() {
      slider.style.transform = `translateX(-${currentIndex * 100}%)`;
    }
  </script>

<script>
function registerForm() {
    const params = new URLSearchParams(window.location.search);
    return {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        plan: '',
        showPaymentModal: false,
        registerUser() {
            fetch('{{ route('register') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({
                    name: this.name,
                    email: this.email,
                    password: this.password,
                    password_confirmation: this.password_confirmation,
                    plan: params.get('plan') ,
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success || data.user) {
                    this.showPaymentModal = true;
                } else if (data.errors) {
                    alert("Error: " + JSON.stringify(data.errors));
                } else {
                    alert("Something went wrong.");
                }
            })
            .catch(error => {
                console.error(error);
                alert("Something went wrong.");
            });
        }
    }
}


</script>
<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

</body>
</html>
