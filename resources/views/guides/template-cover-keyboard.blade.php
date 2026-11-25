<div class="relative flex h-9 w-full max-w-[13rem] overflow-hidden rounded border border-[#111827] bg-[#151b29] sm:h-11">
    @for($key = 0; $key < 15; $key++)
        <span class="flex flex-1 items-end justify-center border-r border-[#9aa4b5] pb-0.5 text-[9px] font-bold text-[#151b29]"
              style="background-color: {{ $highlights[$key][1] ?? '#f5f7fa' }}">{{ $highlights[$key][0] ?? '' }}</span>
    @endfor
    @foreach([1, 2, 4, 5, 6, 8, 9, 11, 12, 13] as $edge)
        <span class="absolute top-0 h-3/5 w-[4%] -translate-x-1/2 rounded-b bg-[#151b29]"
              style="left: {{ $edge / 15 * 100 }}%"></span>
    @endforeach
</div>
