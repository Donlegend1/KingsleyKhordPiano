<div class="flex h-full flex-col items-center justify-center gap-2 rounded-lg border border-white/20 bg-[#202b40] px-3 text-center text-white sm:gap-3" aria-hidden="true">
    <span class="text-lg font-extrabold leading-tight text-[#ffd736] sm:text-xl">30 Ways to Play a 2–5–1</span>
    <div class="grid w-full max-w-[15rem] grid-cols-3 gap-1.5 text-xs font-bold sm:text-sm">
        @foreach(['D', 'G', 'C'] as $bass)
            <span class="rounded bg-white/10 py-1 text-[#d0d9e9]">{{ $bass }}</span>
        @endforeach
        @foreach(['Dm7', 'G7', 'Cmaj7', 'Dm7', 'G7', 'Cm6'] as $chord)
            <span class="rounded py-1 {{ $chord === 'Cm6' ? 'bg-[#ffd736] text-[#151b29]' : 'bg-white/10' }}">{{ $chord }}</span>
        @endforeach
    </div>
</div>
