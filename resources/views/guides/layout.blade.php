<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | Kingsley Khord Piano</title>
    <meta name="description" content="@yield('description')">
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    @vite('resources/css/app.css')
    @yield('styles')
</head>
<body class="bg-[#f9f7f0] text-[#151a22] antialiased">
    <header class="border-b border-black/10 bg-[#f9f7f0]">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-3 sm:px-8">
            <a href="{{ url('/') }}" aria-label="Kingsley Khord Piano home" class="rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4">
                <img src="{{ asset('logo/logoblack.png') }}" alt="Kingsley Khord Piano" class="h-10 w-auto object-contain">
            </a>
            <a href="{{ url('/plans') }}" class="rounded-full border border-black/20 px-4 py-2 text-sm font-semibold transition hover:border-black hover:bg-white">Membership <span aria-hidden="true">↗</span></a>
        </div>
    </header>
    <main>
        @yield('guide')
        @isset($discussion)
            @include('guides.discussion')
        @endisset
    </main>
    @include('partials.public-footer')
    @yield('scripts')
</body>
</html>
