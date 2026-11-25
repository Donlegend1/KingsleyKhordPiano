@php
    $extensions = [
        ['7th', '#80dbb3'],
        ['9th', '#88caff'],
        ['♯11th', '#c5adfa'],
        ['13th', '#ffac91'],
    ];
    $whiteColors = [6 => $extensions[0][1], 8 => $extensions[1][1], 12 => $extensions[3][1]];
@endphp

<div class="flex h-full flex-col items-center justify-center gap-2 rounded-lg border border-white/20 bg-[#202b40] px-3 text-center text-white sm:gap-3" aria-hidden="true">
    <span class="text-lg font-extrabold leading-none text-[#ffd736] sm:text-2xl">Chord Extensions</span>
    <div class="flex flex-wrap justify-center gap-1.5">
        @foreach($extensions as [$name, $color])
            <span class="rounded px-2 py-0.5 text-xs font-bold text-[#151b29]" style="background-color: {{ $color }}">{{ $name }}</span>
        @endforeach
    </div>
    <div class="relative flex h-10 w-full max-w-[15rem] overflow-hidden rounded border border-black bg-[#151b29] sm:h-12">
        @for($key = 0; $key < 14; $key++)
            <span class="flex-1 border-r border-[#9aa4b5]" style="background-color: {{ $whiteColors[$key] ?? '#f5f7fa' }}"></span>
        @endfor
        @foreach([1, 2, 4, 5, 6, 8, 9, 11, 12, 13] as $edge)
            <span class="absolute top-0 h-3/5 w-[4.5%] -translate-x-1/2 rounded-b border border-[#151b29]"
                  style="left: {{ $edge / 14 * 100 }}%; background-color: {{ $edge === 11 ? $extensions[2][1] : '#151b29' }}"></span>
        @endforeach
    </div>
</div>
