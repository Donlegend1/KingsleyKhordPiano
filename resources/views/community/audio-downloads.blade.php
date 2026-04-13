@extends('layouts.community')

@section('content')
<!-- Header Section -->
<div class="bg-white dark:bg-gray-800 mb-6">
    <div class="px-4 sm:px-6 py-6">
        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-2">
            <a href="/member/community" class="hover:text-[#FFD736]">Community</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <span>Audio Downloads</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold text-[#1F2937] dark:text-white">Audio Downloads</h1>
        <p class="text-gray-600 dark:text-gray-300 mt-2">Listen and download exclusive audio tracks organized by skill level</p>
    </div>
</div>

<!-- Main Content Section -->
<section class="px-4 sm:px-6 pb-6 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto">
        
        <!-- Tabs Navigation -->
        <div class="mb-8" x-data="{ activeTab: 'beginners' }">
            
            <!-- Tab Buttons - Spread out on Desktop, Stacked on Mobile -->
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 mb-6">
                <button 
                    @click="activeTab = 'beginners'"
                    :class="activeTab === 'beginners' ? 'bg-[#FF6B35] text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                    class="flex-1 px-8 py-3 rounded-lg font-semibold text-sm sm:text-base transition-all duration-200 shadow-sm"
                >
                    Track & Loops
                </button>
                
                <button 
                    @click="activeTab = 'intermediate'"
                    :class="activeTab === 'intermediate' ? 'bg-[#FF6B35] text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                    class="flex-1 px-8 py-3 rounded-lg font-semibold text-sm sm:text-base transition-all duration-200 shadow-sm"
                >
                    Piano Plays
                </button>
                
               
            </div>

            <!-- Tab Content -->
            <div class="min-h-[500px]">
                
                <!-- Track & Loops Content -->
                <div x-show="activeTab === 'beginners'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                    @if($tracksAndLoops->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            @foreach($tracksAndLoops as $audio)
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                                <!-- Waveform Visual -->
                                <div class="relative h-44 bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center p-4">
                                    <svg class="w-full h-full" viewBox="0 0 200 60" preserveAspectRatio="none">
                                        <path d="M0,30 L5,25 L10,35 L15,20 L20,40 L25,15 L30,45 L35,10 L40,50 L45,5 L50,55 L55,8 L60,52 L65,12 L70,48 L75,18 L80,42 L85,22 L90,38 L95,28 L100,32 L105,26 L110,36 L115,24 L120,34 L125,30 L130,28 L135,32 L140,26 L145,34 L150,30 L155,28 L160,32 L165,30 L170,28 L175,32 L180,30 L185,28 L190,32 L195,30 L200,30" fill="none" stroke="rgba(255,255,255,0.6)" stroke-width="2"/>
                                    </svg>
                                    <div class="absolute inset-0 bg-blue-500 opacity-10"></div>
                                </div>
                                
                                <div class="p-5">
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2 line-clamp-2">{{ $audio->title }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Duration: {{ $audio->duration ?? 'N/A' }}</p>
                                    
                                    <div class="flex gap-2">
                                        <audio class="hidden" id="audio-{{ $audio->id }}" controls>
                                            <source src="/{{ $audio->audio_file }}" type="audio/mpeg">
                                            Your browser does not support the audio element.
                                        </audio>
                                        
                                       <button 
                                            onclick="toggleAudio('audio-{{ $audio->id }}', this)" 
                                            class="flex-1 bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2"
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
                                        <a href="/member/community/space/audio/downloads/{{ $audio->id }}" class="flex-1 bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 border border-gray-300 dark:border-gray-600 flex items-center justify-center gap-2">
                                           <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-cloud-download-icon lucide-cloud-download"><path d="M12 13v8l-4-4"/><path d="m12 21 4-4"/><path d="M4.393 15.269A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.436 8.284"/></svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-gray-400 dark:text-gray-600 mb-4"><path d="M9 19V5m0 0a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2z"/></svg>
                            <h3 class="text-lg font-semibold text-gray-600 dark:text-gray-300">No Audio Available</h3>
                            <p class="text-gray-500 dark:text-gray-400 mt-2">Check back soon for track and loop resources</p>
                        </div>
                    @endif
                </div>

                <!-- Piano Plays Content -->
                <div x-show="activeTab === 'intermediate'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                    @if($pianoPlays->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            @foreach($pianoPlays as $audio)
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                                <!-- Waveform Visual -->
                                <div class="relative h-44 bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center p-4">
                                    <svg class="w-full h-full" viewBox="0 0 200 60" preserveAspectRatio="none">
                                        <path d="M0,30 L5,20 L10,40 L15,15 L20,45 L25,10 L30,50 L35,5 L40,55 L45,8 L50,52 L55,12 L60,48 L65,16 L70,44 L75,20 L80,40 L85,25 L90,35 L95,28 L100,32 L105,26 L110,34 L115,30 L120,28 L125,32 L130,26 L135,34 L140,30 L145,28 L150,32 L155,30 L160,28 L165,32 L170,30 L175,28 L180,32 L185,30 L190,28 L195,32 L200,30" fill="none" stroke="rgba(255,255,255,0.6)" stroke-width="2"/>
                                    </svg>
                                    <div class="absolute inset-0 bg-orange-500 opacity-10"></div>
                                </div>
                                
                                <div class="p-5">
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2 line-clamp-2">{{ $audio->title }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Duration: {{ $audio->duration ?? 'N/A' }}</p>
                                    
                                    <div class="flex gap-2">
                                        <audio class="hidden" id="audio-{{ $audio->id }}" controls>
                                            <source src="/{{ $audio->audio_file }}" type="audio/mpeg">
                                            Your browser does not support the audio element.
                                        </audio>
                                        
                                        <button 
                                            onclick="toggleAudio('audio-{{ $audio->id }}', this)" 
                                            class="flex-1 bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2"
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
                                        <a href="/member/community/space/audio/downloads/{{ $audio->id }}" class="flex-1 bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 border border-gray-300 dark:border-gray-600 flex items-center justify-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-cloud-download-icon lucide-cloud-download"><path d="M12 13v8l-4-4"/><path d="m12 21 4-4"/><path d="M4.393 15.269A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.436 8.284"/></svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-gray-400 dark:text-gray-600 mb-4"><path d="M9 19V5m0 0a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2z"/></svg>
                            <h3 class="text-lg font-semibold text-gray-600 dark:text-gray-300">No Audio Available</h3>
                            <p class="text-gray-500 dark:text-gray-400 mt-2">Check back soon for piano play resources</p>
                        </div>
                    @endif
                </div>


            </div>
        </div>

    </div>
</section>

@endsection
