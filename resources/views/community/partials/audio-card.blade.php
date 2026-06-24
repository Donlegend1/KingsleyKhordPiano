@php
    $isPianoPlay = $audio->category === 'piano_plays';
    $gradient = $isPianoPlay ? 'from-orange-400 to-orange-600' : 'from-blue-400 to-blue-600';
    $overlay = $isPianoPlay ? 'bg-orange-500' : 'bg-blue-500';
@endphp
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300 h-full flex flex-col">
    <!-- Waveform Visual -->
    <div class="relative h-32 bg-gradient-to-br {{ $gradient }} flex items-center justify-center p-4">
        <svg class="w-full h-full" viewBox="0 0 200 60" preserveAspectRatio="none">
            <path d="M0,30 L5,25 L10,35 L15,20 L20,40 L25,15 L30,45 L35,10 L40,50 L45,5 L50,55 L55,8 L60,52 L65,12 L70,48 L75,18 L80,42 L85,22 L90,38 L95,28 L100,32 L105,26 L110,36 L115,24 L120,34 L125,30 L130,28 L135,32 L140,26 L145,34 L150,30 L155,28 L160,32 L165,30 L170,28 L175,32 L180,30 L185,28 L190,32 L195,30 L200,30" fill="none" stroke="rgba(255,255,255,0.6)" stroke-width="2"/>
        </svg>
        <div class="absolute inset-0 {{ $overlay }} opacity-10"></div>
    </div>

    <div class="p-5 flex flex-col flex-grow">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2 line-clamp-2 min-h-[3.5rem]">{{ $audio->title }}</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Duration: {{ $audio->duration ?? 'N/A' }}</p>

        @php
            $audioMime = match (strtolower(pathinfo($audio->audio_file, PATHINFO_EXTENSION))) {
                'wav' => 'audio/wav',
                'ogg' => 'audio/ogg',
                'm4a' => 'audio/mp4',
                default => 'audio/mpeg',
            };
        @endphp
        <div class="grid grid-cols-2 gap-2 mt-auto">
            <audio class="hidden" id="audio-{{ $audio->id }}" controls>
                <source src="/{{ $audio->audio_file }}" type="{{ $audioMime }}">
                Your browser does not support the audio element.
            </audio>

            <button
                onclick="toggleAudio('audio-{{ $audio->id }}', this)"
                class="w-full bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2"
            >
                <!-- Play Icon -->
                <svg class="w-5 h-5 play-icon" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z"/>
                </svg>
                <!-- Pause Icon (hidden by default) -->
                <svg class="w-5 h-5 pause-icon hidden" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                </svg>
                <span class="btn-label">PLAY</span>
            </button>
            <a href="/member/community/space/audio/downloads/{{ $audio->id }}" class="w-full bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 border border-gray-300 dark:border-gray-600 flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-cloud-download-icon lucide-cloud-download"><path d="M12 13v8l-4-4"/><path d="m12 21 4-4"/><path d="M4.393 15.269A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.436 8.284"/></svg>
            </a>
        </div>
    </div>
</div>
