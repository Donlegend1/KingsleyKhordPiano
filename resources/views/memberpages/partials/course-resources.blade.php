@if ($lesson->audio_resource_url || $lesson->pdf_resource_url)
    <div class="mt-8">
        <h2 class="text-[18px] font-bold text-gray-900 mb-4">Course Resources</h2>
        <div class="flex flex-col sm:flex-row gap-3">
            @if ($lesson->audio_resource_url)
                <a href="{{ $lesson->audio_resource_url }}" download
                   class="flex-1 flex items-center justify-center gap-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-800 font-semibold px-5 py-3 rounded-xl transition-all">
                    <i class="fa-solid fa-music text-[15px]"></i>
                    Download Audio Track
                </a>
            @endif

            @if ($lesson->pdf_resource_url)
                <a href="{{ $lesson->pdf_resource_url }}" download
                   class="flex-1 flex items-center justify-center gap-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-800 font-semibold px-5 py-3 rounded-xl transition-all">
                    <i class="fa-solid fa-file-pdf text-[15px]"></i>
                    Download PDF File
                </a>
            @endif
        </div>
    </div>
@endif
