<footer class="bg-white shadow bottom-0 z-50 border-t border-gray-200 py-5">
    <div class="max-w-7xl mx-auto px-4 flex flex-col md:flex-row items-center justify-center text-sm text-gray-500 dark:text-gray-400 space-y-2 md:space-y-0 md:space-x-4">
        <div class="flex items-center space-x-2">
            <div>&copy; {{ date('Y') }} {{ config('app.name') }}</div>
            <div class="h-4 border-l border-gray-400 mx-2"></div>
            <div>All rights reserved.</div>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('privacy') }}" class="hover:underline">Privacy Policy</a>
            <span class="mx-1" aria-hidden="true">|</span>
            <a href="{{ route('terms') }}" class="hover:underline">Terms of Service</a>
        </div>
    </div>
</footer>
