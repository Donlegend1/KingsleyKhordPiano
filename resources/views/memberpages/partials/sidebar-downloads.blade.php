@if ($lesson && ($lesson->audio_resource_url || $lesson->pdf_resource_url))
    <div class="border-t border-gray-100">
        <div class="bg-gray-900 px-5 py-2.5">
            <h3 class="text-white text-[13px] font-bold tracking-wide uppercase">
                Downloads for this lesson
            </h3>
        </div>

        <div class="divide-y divide-gray-100">
            @if ($lesson->pdf_resource_url)
                <div class="bg-gray-50 px-5 py-2.5 flex items-center gap-3">
                    <div class="w-11 h-7 rounded bg-gray-400 flex items-center justify-center text-white text-[10px] font-bold flex-shrink-0">
                        PDF
                    </div>
                    <a href="{{ $lesson->pdf_resource_url }}" target="_blank" rel="noopener noreferrer"
                       class="text-red-500 hover:text-red-600 font-bold text-[13px]">
                        Download the Chart
                    </a>
                </div>
            @endif

            @if ($lesson->audio_resource_url)
                <div class="bg-gray-50 px-5 py-2.5 flex items-center gap-3">
                    <div class="w-11 h-7 rounded bg-gray-400 flex items-center justify-center text-white text-[10px] font-bold flex-shrink-0">
                        MP3
                    </div>
                    <a href="{{ $lesson->audio_resource_url }}" download
                       class="text-red-500 hover:text-red-600 font-bold text-[13px]">
                        Download the audio
                    </a>
                </div>
            @endif
        </div>
    </div>
@endif
