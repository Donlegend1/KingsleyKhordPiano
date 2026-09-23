@php
    $palettes = [
        'leaderboard' => [
            0 => ['style' => 'background: linear-gradient(135deg, #d97706 0%, #78350f 100%);', 'ring' => 'ring-amber-400/40', 'icon' => 'fa-crown', 'tag' => 'Champion'],
            1 => ['style' => 'background: linear-gradient(135deg, #4338ca 0%, #1e1b4b 100%);', 'ring' => 'ring-indigo-400/40', 'icon' => 'fa-medal', 'tag' => 'Runner-up'],
            2 => ['style' => 'background: linear-gradient(135deg, #c2410c 0%, #7c2d12 100%);', 'ring' => 'ring-orange-400/40', 'icon' => 'fa-medal', 'tag' => 'Third Place'],
        ],
        'top-members' => [
            0 => ['style' => 'background: linear-gradient(135deg, #0f766e 0%, #042f2e 100%);', 'ring' => 'ring-teal-400/40', 'icon' => 'fa-crown', 'tag' => 'Champion'],
            1 => ['style' => 'background: linear-gradient(135deg, #7e22ce 0%, #3b0764 100%);', 'ring' => 'ring-purple-400/40', 'icon' => 'fa-medal', 'tag' => 'Runner-up'],
            2 => ['style' => 'background: linear-gradient(135deg, #334155 0%, #0f172a 100%);', 'ring' => 'ring-slate-400/40', 'icon' => 'fa-medal', 'tag' => 'Third Place'],
        ],
    ];

    $cardStyles = $palettes[$palette ?? 'leaderboard'];
@endphp

<div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mb-6">
    @foreach ($members as $index => $member)
        @php $style = $cardStyles[$index]; @endphp
        <div class="flex items-center gap-4 rounded-2xl p-5 text-white shadow-md" style="{{ $style['style'] }}">
            <div class="relative flex-shrink-0">
                <img
                    src="{{ $member->passport ? asset($member->passport) : '/avatar1.jpg' }}"
                    alt="{{ $displayName($member) }}"
                    class="h-14 w-14 rounded-full object-cover ring-4 {{ $style['ring'] }} shadow-sm"
                    onerror="this.onerror=null;this.src='/avatar1.jpg';"
                >
                <span class="absolute -bottom-1 -right-1 flex h-6 w-6 items-center justify-center rounded-full bg-white/25 backdrop-blur-sm">
                    <i class="fa-solid {{ $style['icon'] }} text-[11px] text-white"></i>
                </span>
            </div>

            <div class="min-w-0 flex-1">
                <p class="text-[10px] font-bold uppercase tracking-wide text-white/70">{{ $style['tag'] }}</p>
                <p class="mt-0.5 truncate text-base font-bold">{{ $displayName($member) }}</p>
                <div class="mt-2 flex items-center gap-4">
                    @foreach ($stats as $stat)
                        <div>
                            <span class="text-lg font-extrabold leading-none">{{ number_format($member->{$stat['key']}) }}</span>
                            <span class="ml-1 text-[10px] font-semibold uppercase tracking-wide text-white/70">{{ $stat['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
</div>
