<div class="flex h-full flex-col items-center justify-center gap-2 rounded-lg border border-white/20 bg-[#202b40] px-3 text-center text-white sm:gap-3" aria-hidden="true">
    <span class="text-lg font-extrabold leading-tight text-[#ffd736] sm:text-xl">Chordal Harmony Template</span>
    @include('guides.template-cover-keyboard', ['highlights' => [
        5 => ['A', '#91cef7'], 7 => ['C', '#91cef7'], 10 => ['F', '#91cef7'],
        11 => ['G', '#91cef7'], 14 => ['C', '#ffd736'],
    ]])
    <div class="flex flex-wrap justify-center gap-x-3 gap-y-1 text-xs font-semibold">
        <span class="text-[#91cef7]">A–C–F–G harmony</span>
        <span class="text-[#ffd736]">C melody</span>
    </div>
</div>
