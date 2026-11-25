<div data-performance-panel="{{ $recording }}" class="min-w-0 rounded-2xl bg-[#151b29] p-4 text-white shadow-sm sm:p-5">
    <h3 class="font-bold">{{ $playerTitle }}</h3>
    <p class="mt-1 text-sm leading-6 text-[#d0d9e9]">{{ $cue }}</p>
    <div class="guide-keyboard mt-4" aria-hidden="true">
        <div class="guide-keyboard-octave">
            @foreach($whiteKeys as $index => $note)
                <span class="guide-white-key" data-performance-note="{{ $note }}"></span>
                @if(($whiteKeys[$index + 1] ?? $note) - $note === 2)
                    <span class="guide-black-key" data-performance-note="{{ $note + 1 }}"
                          style="left:{{ ($index + 1) / count($whiteKeys) * 100 }}%;width:2.7%"></span>
                @endif
            @endforeach
        </div>
    </div>
    <audio controls controlslist="nodownload" preload="none"
           aria-label="{{ $playerLabel }}"
           class="mt-4 block w-full" src="{{ asset($audioPath) }}"></audio>
    <div data-recording-controls hidden class="mt-4">
        <div class="flex items-center gap-2.5 rounded-full border border-white/15 bg-white/10 p-2 pr-3 sm:gap-3 sm:pr-4">
            <button type="button" data-recording-toggle
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#ffd736] text-sm font-bold text-[#151b29] hover:bg-[#ffe273] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white disabled:opacity-75">
                <span data-recording-icon aria-hidden="true">▶</span>
                <span data-recording-label class="sr-only">Play recording</span>
            </button>
            <div class="flex min-w-0 flex-1 items-center gap-2 sm:gap-3">
                <span data-recording-elapsed class="text-xs tabular-nums text-[#d0d9e9]">0:00</span>
                <input type="range" min="0" max="{{ $duration }}" value="0" step="0.1" data-recording-seek disabled
                       aria-label="Seek within {{ $playerLabel }}"
                       class="recording-timeline h-6 min-w-0 flex-1 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-white">
                <span data-recording-duration class="text-xs tabular-nums text-[#d0d9e9]">{{ sprintf('%d:%02d', intdiv($duration, 60), $duration % 60) }}</span>
            </div>
        </div>
        <p data-recording-status role="status" hidden class="mt-2 text-sm text-[#ffdf60]"></p>
    </div>
</div>
