@if (config('app.wip_mode'))
    <div class="w-full bg-yellow-500 text-black text-sm text-center px-4 py-2 fixed top-0 left-0 z-50 shadow">
        🚧 Some features are currently being worked on and perfected.  
        You may experience minor issues. Thank you for your patience.
    </div>

    {{-- Spacer to prevent content overlap --}}
    <div class="h-10"></div>
@endif
