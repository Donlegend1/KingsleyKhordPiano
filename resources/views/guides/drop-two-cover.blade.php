<div class="flex h-full flex-col items-center justify-center gap-2 rounded-lg border border-white/20 bg-[#202b40] px-3 text-center text-white sm:gap-3" aria-hidden="true">
    <span class="text-lg font-extrabold leading-tight text-[#ffd736] sm:text-xl">Drop Two Templates</span>
    <div class="flex items-center gap-2 text-xs font-bold text-[#151b29]">
        <span class="rounded bg-[#91cef7] px-2 py-1">Left · A</span>
        <span class="rounded bg-[#99e6ad] px-2 py-1">Right · F <span class="rounded bg-[#ffd736] px-1">C</span></span>
    </div>
    @include('guides.template-cover-keyboard', ['highlights' => [5 => ['A', '#91cef7'], 10 => ['F', '#99e6ad'], 14 => ['C', '#ffd736']]])
</div>
