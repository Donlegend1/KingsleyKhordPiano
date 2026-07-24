<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Master Gospel Piano and Play Like the Pros | Achieve Pro-Level Gospel Piano Skills—Even If You&rsquo;re Starting from Scratch!">
    <meta name="keywords" content="Kingsley Khord Piano, gospel piano lessons, learn gospel piano online, piano chord progressions, piano technique drills, online piano course">
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
    <div id="app" class="min-h-screen flex flex-col">
    <header
      x-data="{ scrolled: false }"
      x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 10 })"
      :class="'bg-black'"
      class="fixed w-full top-0 z-50 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 grid grid-cols-3 items-center lg:flex lg:justify-between">
            <!-- Mobile: Cart Icon (left) -->
            <div class="lg:hidden justify-self-start">
                <a href="/cart" class="relative flex items-center justify-center w-10 h-10 rounded-xl bg-neutral-800 hover:bg-neutral-700 transition duration-200" aria-label="View cart">
                    <i class="fa-solid fa-cart-shopping text-white text-base"></i>
                    <span id="cart-badge-mobile" class="cart-badge absolute -top-1.5 -right-1.5 flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full bg-[#FFD736] text-black text-[10px] font-bold">
                        {{ $cartCount ?? 0 }}
                    </span>
                </a>
            </div>

            <!-- Left: Logo -->
            <div class="flex items-center flex-shrink-0 justify-self-center lg:justify-self-auto">
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
                   
                    <p class="text-xs text-gray-600" id="liveshow-time" data-utc="{{ \Carbon\Carbon::parse($liveshow->start_time)->utc()->toISOString() }}">
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
                    class="text-base font-semibold transition duration-200 {{ Request::is('/') ? 'text-[#FFD736] border-b-2 border-[#FFD736] pb-0.5' : 'text-gray-400 hover:text-[#FFD736]' }}">
                    Home
                </a>
                <a href="/about"
                    class="text-base font-semibold transition duration-200 {{ Request::is('about') ? 'text-[#FFD736] border-b-2 border-[#FFD736] pb-0.5' : 'text-gray-400 hover:text-[#FFD736]' }}">
                    About
                </a>
                <a href="/shop"
                    class="text-base font-semibold transition duration-200 {{ Request::is('shop') ? 'text-[#FFD736] border-b-2 border-[#FFD736] pb-0.5' : 'text-gray-400 hover:text-[#FFD736]' }}">
                    Shop
                </a>
                <a href="https://discord.gg/TKKtTSYVvx"
                    target="_blank" rel="noopener noreferrer"
                    class="text-base font-semibold transition duration-200 text-gray-400 hover:text-[#FFD736]">
                    Community
                </a>
                <a href="/contact"
                    class="text-base font-semibold transition duration-200 {{ Request::is('contact') ? 'text-[#FFD736] border-b-2 border-[#FFD736] pb-0.5' : 'text-gray-400 hover:text-[#FFD736]' }}">
                    Contact
                </a>
            </nav>

            <div class="hidden lg:flex items-center space-x-4">
            <a href="/register"
                class="text-lg font-semibold px-4 py-2 rounded-lg bg-transparent border border-gray-300 text-white hover:bg-[#FFD736] hover:text-black shadow transition duration-200">
                Sign Up
            </a>
            <a href="/login"
                class="text-lg font-semibold px-4 py-2 rounded-lg bg-[#FFD736] text-black hover:bg-[#e6c22e] shadow transition duration-200">
                Login
            </a>
            <div class="relative ml-14" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                <a href="/cart" class="relative flex items-center justify-center w-11 h-11 rounded-xl bg-neutral-800 hover:bg-neutral-700 transition duration-200" aria-label="View cart">
                    <i class="fa-solid fa-cart-shopping text-white text-lg"></i>
                    <span id="cart-badge-desktop" class="cart-badge absolute -top-1.5 -right-1.5 flex items-center justify-center min-w-[19px] h-[19px] px-1 rounded-full bg-[#FFD736] text-black text-[10px] font-bold">
                        {{ $cartCount ?? 0 }}
                    </span>
                </a>

                <!-- Hover Preview -->
                <div x-show="open" x-transition x-cloak
                    class="absolute right-0 top-full mt-3 w-80 bg-white rounded-2xl shadow-xl overflow-hidden z-50">
                    @if(count($cartPreviewItems ?? []) === 0)
                        <div class="text-center py-10 px-6">
                            <div class="relative inline-block mb-4">
                                <span class="absolute -top-3 left-1/2 -translate-x-1/2 w-0.5 h-2.5 bg-gray-300 rotate-[-15deg] -translate-x-3"></span>
                                <span class="absolute -top-4 left-1/2 -translate-x-1/2 w-0.5 h-3 bg-gray-300"></span>
                                <span class="absolute -top-3 left-1/2 -translate-x-1/2 w-0.5 h-2.5 bg-gray-300 rotate-[15deg] translate-x-3"></span>
                                <i class="fa-solid fa-cart-shopping text-4xl text-gray-900"></i>
                            </div>
                            <p class="font-bold text-gray-900">No products in the cart.</p>
                            <p class="text-sm text-gray-400 mt-1.5">Browse our MIDI files and plugins to find something for your next project.</p>
                        </div>
                    @else
                        <div class="max-h-80 overflow-y-auto divide-y divide-gray-100">
                            @foreach($cartPreviewItems as $item)
                                <div class="flex items-center gap-3 px-5 py-3.5">
                                    @if(!empty($item['thumbnail']))
                                        <img src="/{{ $item['thumbnail'] }}" alt="{{ $item['name'] }}" class="w-12 h-12 rounded-lg object-cover flex-shrink-0">
                                    @else
                                        <div class="w-12 h-12 rounded-lg overflow-hidden flex-shrink-0"
                                            style="background: linear-gradient(135deg, {{ $item['from'] }}, {{ $item['to'] }});">
                                        </div>
                                    @endif
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $item['name'] }}</p>
                                        <div class="flex items-center gap-2 mt-1 text-sm">
                                            <span class="text-gray-500">{{ $item['qty'] }} &times; ${{ number_format($item['price'], 2) }}</span>
                                            <button type="button" data-remove-slug="{{ $item['slug'] }}" class="cart-remove-btn flex items-center gap-1 text-red-500 hover:text-red-600 transition text-xs font-medium">
                                                <i class="fa-regular fa-trash-can"></i>
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-t border-gray-100 px-5 py-4 flex items-center justify-between">
                            <span class="font-bold text-gray-900">Subtotal:</span>
                            <span class="font-bold text-gray-900">${{ number_format(collect($cartPreviewItems)->sum(fn($i) => $i['price'] * $i['qty']), 2) }}</span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 px-5 pb-5">
                            <a href="/cart" class="flex items-center justify-center bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold py-2.5 rounded-lg transition">
                                View cart
                            </a>
                            <a href="/checkout" class="flex items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-semibold py-2.5 rounded-lg transition">
                                Checkout
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>



            <!-- Mobile Toggle -->
            <div class="lg:hidden justify-self-end">
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
                    <a href="/shop"
                        class="block text-sm font-semibold transition duration-200 py-3 px-2 {{ Request::is('shop') ? 'text-white' : 'text-gray-400 hover:text-[#FFD736]' }}">
                        Shop
                    </a>
                    <a href="https://discord.gg/TKKtTSYVvx"
                        target="_blank" rel="noopener noreferrer"
                        class="block text-sm font-semibold transition duration-200 py-3 px-2 text-gray-400 ">
                        Community
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
        <main class="flex-1">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white shadow bottom-0 z-50 border-t border-gray-200 py-5">
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

         document.addEventListener('DOMContentLoaded', () => {
        const el = document.getElementById('liveshow-time');
        if (!el) return;

        const utcTime = el.getAttribute('data-utc');
        if (!utcTime) return;

        const date = new Date(utcTime);
        const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

        const localDate = new Intl.DateTimeFormat('en-US', {
            timeZone: userTimezone,
            month: 'short',
            day: '2-digit',
            year: 'numeric',
        }).format(date);

        const localTime = new Intl.DateTimeFormat('en-US', {
            timeZone: userTimezone,
            hour: '2-digit',
            minute: '2-digit',
            hour12: true,
        }).format(date);

        const tzLabel = new Intl.DateTimeFormat('en-US', {
            timeZone: userTimezone,
            timeZoneName: 'short',
        }).formatToParts(date).find(p => p.type === 'timeZoneName')?.value;

        el.textContent = `${localDate} ${localTime} (${tzLabel})`;
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

<script>
    (function () {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function updateCartBadges(count) {
            document.querySelectorAll('.cart-badge').forEach(function (badge) {
                badge.textContent = count;
            });
        }

        function postJson(url, body) {
            return fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(body),
            }).then(function (res) { return res.json(); });
        }

        document.addEventListener('click', function (e) {
            const addBtn = e.target.closest('.add-to-cart-btn');
            if (addBtn) {
                e.preventDefault();
                if (addBtn.disabled) return;

                const label = addBtn.querySelector('span');
                const icon = addBtn.querySelector('i');
                const originalLabel = label ? label.textContent : '';
                const originalIconClass = icon ? icon.className : '';

                addBtn.disabled = true;

                postJson('/cart/add', { slug: addBtn.dataset.addSlug })
                    .then(function (data) {
                        if (!data.success) {
                            throw new Error(data.error || 'Failed to add to cart');
                        }

                        updateCartBadges(data.cartCount);

                        if (addBtn.dataset.redirect) {
                            window.location.href = addBtn.dataset.redirect;
                            return;
                        }

                        if (addBtn.dataset.reload) {
                            window.location.reload();
                            return;
                        }

                        if (label) label.textContent = 'Added!';
                        if (icon) icon.className = 'fa-solid fa-check text-xs';

                        setTimeout(function () {
                            if (label) label.textContent = originalLabel;
                            if (icon) icon.className = originalIconClass;
                            addBtn.disabled = false;
                        }, 1500);
                    })
                    .catch(function () {
                        if (label) label.textContent = 'Error';
                        setTimeout(function () {
                            if (label) label.textContent = originalLabel;
                            addBtn.disabled = false;
                        }, 1500);
                    });

                return;
            }

            const removeBtn = e.target.closest('.cart-remove-btn');
            if (removeBtn) {
                e.preventDefault();
                if (removeBtn.disabled) return;
                removeBtn.disabled = true;

                postJson('/cart/remove', { slug: removeBtn.dataset.removeSlug })
                    .then(function () {
                        window.location.reload();
                    });

                return;
            }

            const qtyBtn = e.target.closest('.cart-qty-btn');
            if (qtyBtn) {
                e.preventDefault();
                if (qtyBtn.disabled) return;
                qtyBtn.disabled = true;

                postJson('/cart/update', { slug: qtyBtn.dataset.slug, qty: parseInt(qtyBtn.dataset.qty, 10) })
                    .then(function () {
                        window.location.reload();
                    });

                return;
            }

            const clearBtn = e.target.closest('#clear-cart-btn');
            if (clearBtn) {
                e.preventDefault();
                if (clearBtn.disabled) return;
                clearBtn.disabled = true;

                postJson('/cart/clear', {})
                    .then(function () {
                        window.location.reload();
                    });
            }
        });
    })();
</script>

</body>
</html>