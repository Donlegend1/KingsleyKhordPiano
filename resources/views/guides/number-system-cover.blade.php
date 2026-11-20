<div class="flex h-full flex-col items-center justify-center gap-2 rounded-lg border border-white/20 bg-[#202b40] px-3 text-center text-white sm:gap-3" aria-hidden="true">
    <span class="text-lg font-extrabold leading-tight text-[#ffd736] sm:text-xl">The Piano Number System</span>
    <div class="grid w-full max-w-[15rem] grid-cols-[auto_repeat(4,minmax(0,1fr))] items-center gap-1 text-xs font-bold sm:gap-1.5">
        <span></span>
        @foreach([4, 1, 5, 6] as $number)
            <span class="rounded bg-[#ffd736] py-1 text-[#151b29]">{{ $number }}</span>
        @endforeach
        @foreach(['C' => ['F', 'C', 'G', 'Am'], 'E' => ['A', 'E', 'B', 'C♯m']] as $key => $chords)
            <span class="pr-1 text-[#d0d9e9]">In {{ $key }}</span>
            @foreach($chords as $chord)
                <span class="rounded bg-white/10 py-1">{{ $chord }}</span>
            @endforeach
        @endforeach
    </div>
</div>
