@if ($lesson && ($lesson->audio_resource_url || $lesson->pdf_resource_url))
    @php
        $resourceCount = ($lesson->audio_resource_url ? 1 : 0) + ($lesson->pdf_resource_url ? 1 : 0);
    @endphp
    <div class="bg-gradient-to-br from-indigo-50 via-white to-white">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-indigo-100">
            <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-indigo-600 text-white shadow-md shadow-indigo-600/30 flex-shrink-0">
                <i class="fa-solid fa-gift text-[15px]"></i>
            </span>
            <div class="flex-1 min-w-0">
                <h3 class="text-gray-900 text-[14px] font-extrabold">
                    Resources for this lesson
                </h3>
                <p class="text-indigo-500 text-[11px] font-semibold">{{ $resourceCount }} {{ Str::plural('file', $resourceCount) }} included</p>
            </div>
        </div>

        <div class="divide-y divide-indigo-100/70">
            @if ($lesson->audio_resource_url)
                <div class="lesson-audio-player px-5 py-4">
                    <div class="flex items-center gap-3">
                        <button type="button"
                            class="lesson-audio-toggle flex items-center justify-center w-10 h-10 rounded-full bg-gray-900 hover:bg-black text-white flex-shrink-0 shadow-md transition-colors">
                            <i class="fa-solid fa-play text-[12px] ml-0.5"></i>
                        </button>
                        <div class="flex-1 min-w-0">
                            <p class="text-gray-900 font-bold text-[13px] truncate">Lesson Audio</p>
                            <p class="text-gray-400 text-[11px]">MP3 &middot; Streamable</p>
                        </div>
                        <span class="lesson-audio-time text-gray-400 font-semibold text-[11px] tabular-nums flex-shrink-0">0:00 / 0:00</span>
                    </div>

                    <div class="lesson-audio-seek relative w-full h-1.5 rounded-full bg-gray-100 mt-3 cursor-pointer">
                        <div class="lesson-audio-progress absolute inset-y-0 left-0 w-0 rounded-full bg-red-500"></div>
                    </div>

                    <audio class="lesson-audio-element hidden" src="{{ $lesson->audio_resource_url }}" preload="metadata"></audio>
                </div>
            @endif

            @if ($lesson->pdf_resource_url)
                <a href="{{ $lesson->pdf_resource_url }}" target="_blank" rel="noopener noreferrer"
                   class="flex items-center gap-3 px-5 py-4 hover:bg-white/70 transition-colors group">
                    <span class="flex items-center justify-center w-10 h-10 rounded-full bg-rose-500 text-white shadow-md shadow-rose-500/30 flex-shrink-0">
                        <i class="fa-solid fa-file-pdf text-[15px]"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-gray-900 font-bold text-[13px] truncate">Chord Chart</p>
                        <p class="text-gray-400 text-[11px]">PDF &middot; Tap to download</p>
                    </div>
                    <span class="flex items-center justify-center w-8 h-8 rounded-full bg-white border border-gray-200 text-gray-400 group-hover:text-gray-900 group-hover:border-gray-300 flex-shrink-0 transition-colors">
                        <i class="fa-solid fa-arrow-down text-xs"></i>
                    </span>
                </a>
            @endif
        </div>
    </div>

    <script>
        if (!window.__lessonAudioPlayerBound) {
            window.__lessonAudioPlayerBound = true;

            const formatTime = (seconds) => {
                if (!seconds || !isFinite(seconds)) return '0:00';
                const mins = Math.floor(seconds / 60);
                const secs = Math.floor(seconds % 60);
                return mins + ':' + String(secs).padStart(2, '0');
            };

            const resetPlayer = (wrapper) => {
                const icon = wrapper.querySelector('.lesson-audio-toggle i');
                icon.classList.remove('fa-pause', 'ml-0');
                icon.classList.add('fa-play', 'ml-0.5');
                wrapper.querySelector('.lesson-audio-progress').style.width = '0%';
            };

            const updateTimeLabel = (wrapper, audio) => {
                const label = wrapper.querySelector('.lesson-audio-time');
                label.textContent = formatTime(audio.currentTime) + ' / ' + formatTime(audio.duration);
            };

            document.addEventListener('click', function (e) {
                const toggle = e.target.closest('.lesson-audio-toggle');
                const seek = e.target.closest('.lesson-audio-seek');
                if (!toggle && !seek) return;

                const wrapper = (toggle || seek).closest('.lesson-audio-player');
                const audio = wrapper.querySelector('.lesson-audio-element');

                if (seek) {
                    if (!audio.duration) return;
                    const rect = seek.getBoundingClientRect();
                    const ratio = Math.min(Math.max((e.clientX - rect.left) / rect.width, 0), 1);
                    audio.currentTime = ratio * audio.duration;
                    return;
                }

                document.querySelectorAll('.lesson-audio-element').forEach((el) => {
                    if (el !== audio && !el.paused) {
                        el.pause();
                        el.currentTime = 0;
                        resetPlayer(el.closest('.lesson-audio-player'));
                    }
                });

                const icon = toggle.querySelector('i');
                if (audio.paused) {
                    audio.play();
                    icon.classList.remove('fa-play', 'ml-0.5');
                    icon.classList.add('fa-pause', 'ml-0');
                } else {
                    audio.pause();
                    icon.classList.remove('fa-pause', 'ml-0');
                    icon.classList.add('fa-play', 'ml-0.5');
                }
            });

            document.addEventListener('timeupdate', function (e) {
                if (!e.target.classList || !e.target.classList.contains('lesson-audio-element')) return;
                const audio = e.target;
                const wrapper = audio.closest('.lesson-audio-player');
                if (audio.duration) {
                    wrapper.querySelector('.lesson-audio-progress').style.width =
                        ((audio.currentTime / audio.duration) * 100) + '%';
                }
                updateTimeLabel(wrapper, audio);
            }, true);

            document.addEventListener('loadedmetadata', function (e) {
                if (!e.target.classList || !e.target.classList.contains('lesson-audio-element')) return;
                updateTimeLabel(e.target.closest('.lesson-audio-player'), e.target);
            }, true);

            document.addEventListener('ended', function (e) {
                if (!e.target.classList || !e.target.classList.contains('lesson-audio-element')) return;
                resetPlayer(e.target.closest('.lesson-audio-player'));
            }, true);
        }
    </script>
@endif
