@extends("layouts.hub")

@section("title", "Say Hello")

@section("content")
<div class="p-6">
    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm p-10 text-center">
        <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75c0-.621.504-1.125 1.125-1.125h.375m3 0h.375c.621 0 1.125.504 1.125 1.125m-6 5.25a3.75 3.75 0 007.5 0"/>
            </svg>
        </div>
        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-1.5">No introductions yet</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">New here? This is the place to introduce yourself to the community.</p>
    </div>
</div>
@endsection
