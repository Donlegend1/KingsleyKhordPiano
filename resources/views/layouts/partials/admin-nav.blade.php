@php
    // One menu definition shared by the desktop sidebar and the mobile drawer.
    // 'match' are request()->is() patterns used to highlight the current page.
    $navGroups = [
        ['key' => 'overview', 'label' => 'Overview', 'items' => [
            ['Dashboard', '/admin/dashboard', 'fa-house', ['admin/dashboard']],
            ['Lesson Analytics', route('admin.lesson-analytics'), 'fa-chart-bar', ['admin/lesson-analytics*']],
        ]],
        ['key' => 'members', 'label' => 'Members', 'items' => [
            ['Users', '/admin/users', 'fa-user', ['admin/users*']],
            ['Guidance Requests', route('admin.personalized-guidance.index'), 'fa-graduation-cap', ['admin/personalized-guidance*']],
            ['Guest Bookings', route('admin.guest-bookings.index'), 'fa-calendar-check', ['admin/guest-bookings*']],
            ['Email Campaign', '/admin/email-campaign', 'fa-envelope', ['admin/email-campaign*']],
        ]],
        ['key' => 'content', 'label' => 'Lessons & Content', 'items' => [
            ['Roadmap', '/admin/courses', 'fa-map', ['admin/courses*', 'admin/course/*']],
            ['Finger Exercise', '/admin/uploads/piano-exercise', 'fa-hand-pointer', ['admin/uploads/piano-exercise*']],
            ['Guided Practice', '/admin/musical-application', 'fa-music', ['admin/musical-application*']],
            ['Etudes & Pieces', '/admin/uploads/etudes', 'fa-feather-pointed', ['admin/uploads/etudes*']],
            ['Extra Courses', '/admin/uploads/extra-courses', 'fa-school', ['admin/uploads/extra-courses*']],
            ['Learn Songs', '/admin/uploads/learn-songs', 'fa-compact-disc', ['admin/uploads/learn-songs*']],
        ]],
        ['key' => 'live', 'label' => 'Live & Community', 'items' => [
            ['Live Show', '/admin/live-shows', 'fa-tv', ['admin/live-show*']],
            ['Community Tutorials', route('admin.tutorials.index'), 'fa-video', ['admin/tutorials*']],
            ['Student Challenges', route('admin.student-challenges.index'), 'fa-trophy', ['admin/student-challenges*']],
        ]],
        ['key' => 'shop', 'label' => 'Shop & Downloads', 'items' => [
            ['Shop', route('admin.shop.index', ['type' => 'midi']), 'fa-store', ['admin/shop', 'admin/shop/*']],
            ['Shop Orders', route('admin.shop-orders.index'), 'fa-receipt', ['admin/shop-orders*']],
            ['MIDI Files', '/admin/midi-file', 'fa-file-audio', ['admin/midi-file*']],
            ['PDF Downloads', '/admin/pdf-download', 'fa-file-pdf', ['admin/pdf-download*']],
            ['Audio Downloads', '/admin/audio-download', 'fa-headphones', ['admin/audio-download*']],
        ]],
        ['key' => 'account', 'label' => 'Account', 'items' => [
            ['Payment Settings', route('admin.payment-settings.index'), 'fa-credit-card', ['admin/payment-settings*']],
            ['Settings', '/member/profile', 'fa-gear', ['member/profile*']],
        ]],
    ];

    // "+ Add content" shortcuts. Where a section has no standalone create page,
    // it links to the section's page, which is where uploads are added.
    $quickAdd = [
        ['Roadmap course', '/admin/course/create', 'fa-map'],
        ['Finger exercise', '/admin/uploads/piano-exercise', 'fa-hand-pointer'],
        ['Guided practice', '/admin/musical-application', 'fa-music'],
        ['Etude or piece', '/admin/uploads/etudes', 'fa-feather-pointed'],
        ['Extra course', '/admin/uploads/extra-courses', 'fa-school'],
        ['Song (MIDI)', '/admin/uploads/learn-songs', 'fa-compact-disc'],
        ['Live show', '/admin/live-show/create', 'fa-tv'],
    ];

    $isActive = fn (array $patterns) => request()->is(...$patterns);
    $activeGroup = collect($navGroups)->first(fn ($g) => collect($g['items'])->contains(fn ($i) => $isActive($i[3])))['key'] ?? 'overview';
@endphp

<nav aria-label="Admin"
     x-data="{
        active: @js($activeGroup),
        open: {},
        init() {
            let saved = {};
            try { saved = JSON.parse(localStorage.getItem('adminNavOpen') || '{}'); } catch (e) {}
            this.open = saved;
            this.open[this.active] = true;
        },
        toggle(key) {
            this.open[key] = !this.open[key];
            try { localStorage.setItem('adminNavOpen', JSON.stringify(this.open)); } catch (e) {}
        }
     }"
     class="space-y-3 text-sm">

    {{-- Quick add --}}
    <div x-data="{ add: false }" class="relative" @keydown.escape.window="add = false" @click.outside="add = false">
        <button type="button" @click="add = !add" :aria-expanded="add"
                class="flex w-full items-center justify-between gap-2 rounded-lg bg-yellow-400 px-3 py-2 font-semibold text-black hover:bg-yellow-300">
            <span class="flex items-center gap-2"><i class="fa-solid fa-plus text-xs" aria-hidden="true"></i> Add content</span>
            <i class="fa-solid fa-chevron-down text-[10px] transition-transform" :class="add && 'rotate-180'" aria-hidden="true"></i>
        </button>
        <ul x-show="add" x-cloak x-transition.opacity
            class="absolute left-0 right-0 z-30 mt-1 overflow-hidden rounded-lg border border-gray-700 bg-gray-900 py-1 shadow-xl">
            @foreach($quickAdd as [$label, $url, $icon])
                <li>
                    <a href="{{ $url }}" class="flex items-center gap-3 px-3 py-2 text-gray-200 hover:bg-gray-800 hover:text-white">
                        <i class="fa-solid {{ $icon }} w-4 text-center text-xs text-gray-400" aria-hidden="true"></i> {{ $label }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Groups --}}
    @foreach($navGroups as $group)
        <div>
            <button type="button" @click="toggle('{{ $group['key'] }}')" :aria-expanded="!!open['{{ $group['key'] }}']"
                    class="flex w-full items-center justify-between rounded-md px-2 py-1.5 text-[11px] font-bold uppercase tracking-wider text-gray-400 hover:text-white">
                {{ $group['label'] }}
                <i class="fa-solid fa-chevron-down text-[9px] transition-transform" :class="!open['{{ $group['key'] }}'] && '-rotate-90'" aria-hidden="true"></i>
            </button>
            <ul x-show="open['{{ $group['key'] }}']" @if($group['key'] !== $activeGroup) x-cloak @endif class="mt-0.5 space-y-0.5">
                @foreach($group['items'] as [$label, $url, $icon, $patterns])
                    @php($current = $isActive($patterns))
                    <li>
                        <a href="{{ $url }}" @if($current) aria-current="page" @endif
                           class="flex items-center gap-3 rounded-lg px-2.5 py-2 {{ $current ? 'bg-white font-semibold text-black' : 'text-gray-200 hover:bg-gray-800 hover:text-white' }}">
                            <i class="fa-solid {{ $icon }} w-4 text-center text-[13px] {{ $current ? '' : 'text-gray-400' }}" aria-hidden="true"></i>
                            <span>{{ $label }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endforeach

    <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-800 pt-3">
        @csrf
        <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-2.5 py-2 text-gray-300 hover:bg-yellow-400 hover:text-black">
            <i class="fa-solid fa-right-from-bracket w-4 text-center text-[13px]" aria-hidden="true"></i> Log out
        </button>
    </form>
</nav>
