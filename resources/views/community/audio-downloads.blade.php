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
                
                <!-- Beginners Content -->
                <div x-show="activeTab === 'beginners'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        
                        <!-- Audio Card 1 -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <!-- Waveform Visual -->
                            <div class="relative h-32 bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center p-4">
                                <svg class="w-full h-full" viewBox="0 0 200 60" preserveAspectRatio="none">
                                    <path d="M0,30 L5,25 L10,35 L15,20 L20,40 L25,15 L30,45 L35,10 L40,50 L45,5 L50,55 L55,8 L60,52 L65,12 L70,48 L75,18 L80,42 L85,22 L90,38 L95,28 L100,32 L105,26 L110,36 L115,24 L120,34 L125,30 L130,28 L135,32 L140,26 L145,34 L150,30 L155,28 L160,32 L165,30 L170,28 L175,32 L180,30 L185,28 L190,32 L195,30 L200,30" fill="none" stroke="rgba(255,255,255,0.6)" stroke-width="2"/>
                                </svg>
                                <div class="absolute inset-0 bg-blue-500 opacity-10"></div>
                            </div>
                            
                            <div class="p-5">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Piano Scales Practice</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Duration: 5:30</p>
                                
                                <div class="flex gap-2">
                                    <button class="flex-1 bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                        PLAY
                                    </button>
                                    <button class="flex-1 bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 border border-gray-300 dark:border-gray-600 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        DOWNLOAD
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Audio Card 2 -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <!-- Waveform Visual -->
                            <div class="relative h-32 bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center p-4">
                                <svg class="w-full h-full" viewBox="0 0 200 60" preserveAspectRatio="none">
                                    <path d="M0,30 L5,28 L10,32 L15,25 L20,35 L25,22 L30,38 L35,20 L40,40 L45,18 L50,42 L55,16 L60,44 L65,14 L70,46 L75,12 L80,48 L85,10 L90,50 L95,8 L100,30 L105,32 L110,28 L115,34 L120,26 L125,36 L130,24 L135,38 L140,22 L145,40 L150,20 L155,42 L160,30 L165,28 L170,32 L175,30 L180,28 L185,32 L190,30 L195,28 L200,30" fill="none" stroke="rgba(255,255,255,0.6)" stroke-width="2"/>
                                </svg>
                                <div class="absolute inset-0 bg-green-500 opacity-10"></div>
                            </div>
                            
                            <div class="p-5">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Basic Chord Progressions</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Duration: 4:15</p>
                                
                                <div class="flex gap-2">
                                    <button class="flex-1 bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                        PLAY
                                    </button>
                                    <button class="flex-1 bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 border border-gray-300 dark:border-gray-600 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        DOWNLOAD
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Audio Card 3 -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <!-- Waveform Visual -->
                            <div class="relative h-32 bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center p-4">
                                <svg class="w-full h-full" viewBox="0 0 200 60" preserveAspectRatio="none">
                                    <path d="M0,30 L5,26 L10,34 L15,24 L20,36 L25,20 L30,40 L35,16 L40,44 L45,14 L50,46 L55,12 L60,48 L65,10 L70,50 L75,15 L80,45 L85,18 L90,42 L95,22 L100,38 L105,30 L110,28 L115,32 L120,26 L125,34 L130,30 L135,28 L140,32 L145,30 L150,28 L155,32 L160,26 L165,34 L170,30 L175,28 L180,32 L185,30 L190,28 L195,32 L200,30" fill="none" stroke="rgba(255,255,255,0.6)" stroke-width="2"/>
                                </svg>
                                <div class="absolute inset-0 bg-purple-500 opacity-10"></div>
                            </div>
                            
                            <div class="p-5">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Rhythm Training Exercises</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Duration: 3:45</p>
                                
                                <div class="flex gap-2">
                                    <button class="flex-1 bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                        PLAY
                                    </button>
                                    <button class="flex-1 bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 border border-gray-300 dark:border-gray-600 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        DOWNLOAD
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Audio Card 4 -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <!-- Waveform Visual -->
                            <div class="relative h-32 bg-gradient-to-br from-yellow-400 to-yellow-600 flex items-center justify-center p-4">
                                <svg class="w-full h-full" viewBox="0 0 200 60" preserveAspectRatio="none">
                                    <path d="M0,30 L5,32 L10,28 L15,34 L20,26 L25,36 L30,24 L35,38 L40,22 L45,40 L50,20 L55,42 L60,18 L65,44 L70,16 L75,46 L80,14 L85,48 L90,12 L95,50 L100,30 L105,28 L110,32 L115,26 L120,34 L125,30 L130,28 L135,32 L140,30 L145,28 L150,32 L155,26 L160,34 L165,30 L170,28 L175,32 L180,30 L185,28 L190,32 L195,30 L200,30" fill="none" stroke="rgba(255,255,255,0.6)" stroke-width="2"/>
                                </svg>
                                <div class="absolute inset-0 bg-yellow-500 opacity-10"></div>
                            </div>
                            
                            <div class="p-5">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Hand Position Techniques</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Duration: 6:00</p>
                                
                                <div class="flex gap-2">
                                    <button class="flex-1 bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                        PLAY
                                    </button>
                                    <button class="flex-1 bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 border border-gray-300 dark:border-gray-600 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        DOWNLOAD
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Intermediate Content -->
                <div x-show="activeTab === 'intermediate'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        
                        <!-- Audio Card 1 -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <!-- Waveform Visual -->
                            <div class="relative h-32 bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center p-4">
                                <svg class="w-full h-full" viewBox="0 0 200 60" preserveAspectRatio="none">
                                    <path d="M0,30 L5,20 L10,40 L15,15 L20,45 L25,10 L30,50 L35,5 L40,55 L45,8 L50,52 L55,12 L60,48 L65,16 L70,44 L75,20 L80,40 L85,25 L90,35 L95,28 L100,32 L105,26 L110,34 L115,30 L120,28 L125,32 L130,26 L135,34 L140,30 L145,28 L150,32 L155,30 L160,28 L165,32 L170,30 L175,28 L180,32 L185,30 L190,28 L195,32 L200,30" fill="none" stroke="rgba(255,255,255,0.6)" stroke-width="2"/>
                                </svg>
                                <div class="absolute inset-0 bg-orange-500 opacity-10"></div>
                            </div>
                            
                            <div class="p-5">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Jazz Improvisation Guide</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Duration: 8:20</p>
                                
                                <div class="flex gap-2">
                                    <button class="flex-1 bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                        PLAY
                                    </button>
                                    <button class="flex-1 bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 border border-gray-300 dark:border-gray-600 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        DOWNLOAD
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Audio Card 2 -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <!-- Waveform Visual -->
                            <div class="relative h-32 bg-gradient-to-br from-pink-400 to-pink-600 flex items-center justify-center p-4">
                                <svg class="w-full h-full" viewBox="0 0 200 60" preserveAspectRatio="none">
                                    <path d="M0,30 L5,24 L10,36 L15,20 L20,40 L25,18 L30,42 L35,16 L40,44 L45,14 L50,46 L55,12 L60,48 L65,10 L70,50 L75,15 L80,45 L85,20 L90,40 L95,25 L100,35 L105,30 L110,28 L115,32 L120,26 L125,34 L130,30 L135,28 L140,32 L145,30 L150,28 L155,32 L160,30 L165,28 L170,32 L175,30 L180,28 L185,32 L190,30 L195,28 L200,30" fill="none" stroke="rgba(255,255,255,0.6)" stroke-width="2"/>
                                </svg>
                                <div class="absolute inset-0 bg-pink-500 opacity-10"></div>
                            </div>
                            
                            <div class="p-5">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Complex Scale Patterns</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Duration: 7:10</p>
                                
                                <div class="flex gap-2">
                                    <button class="flex-1 bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                        PLAY
                                    </button>
                                    <button class="flex-1 bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 border border-gray-300 dark:border-gray-600 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        DOWNLOAD
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Audio Card 3 -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <!-- Waveform Visual -->
                            <div class="relative h-32 bg-gradient-to-br from-teal-400 to-teal-600 flex items-center justify-center p-4">
                                <svg class="w-full h-full" viewBox="0 0 200 60" preserveAspectRatio="none">
                                    <path d="M0,30 L5,22 L10,38 L15,18 L20,42 L25,14 L30,46 L35,12 L40,48 L45,10 L50,50 L55,15 L60,45 L65,20 L70,40 L75,25 L80,35 L85,28 L90,32 L95,30 L100,28 L105,32 L110,26 L115,34 L120,30 L125,28 L130,32 L135,26 L140,34 L145,30 L150,28 L155,32 L160,30 L165,28 L170,32 L175,30 L180,28 L185,32 L190,30 L195,28 L200,30" fill="none" stroke="rgba(255,255,255,0.6)" stroke-width="2"/>
                                </svg>
                                <div class="absolute inset-0 bg-teal-500 opacity-10"></div>
                            </div>
                            
                            <div class="p-5">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Arpeggios Mastery</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Duration: 6:45</p>
                                
                                <div class="flex gap-2">
                                    <button class="flex-1 bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                        PLAY
                                    </button>
                                    <button class="flex-1 bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 border border-gray-300 dark:border-gray-600 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        DOWNLOAD
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Audio Card 4 -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <!-- Waveform Visual -->
                            <div class="relative h-32 bg-gradient-to-br from-cyan-400 to-cyan-600 flex items-center justify-center p-4">
                                <svg class="w-full h-full" viewBox="0 0 200 60" preserveAspectRatio="none">
                                    <path d="M0,30 L5,26 L10,34 L15,22 L20,38 L25,18 L30,42 L35,14 L40,46 L45,12 L50,48 L55,10 L60,50 L65,16 L70,44 L75,20 L80,40 L85,24 L90,36 L95,28 L100,32 L105,30 L110,28 L115,32 L120,26 L125,34 L130,30 L135,28 L140,32 L145,30 L150,28 L155,32 L160,26 L165,34 L170,30 L175,28 L180,32 L185,30 L190,28 L195,32 L200,30" fill="none" stroke="rgba(255,255,255,0.6)" stroke-width="2"/>
                                </svg>
                                <div class="absolute inset-0 bg-cyan-500 opacity-10"></div>
                            </div>
                            
                            <div class="p-5">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Blues Progressions</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Duration: 5:55</p>
                                
                                <div class="flex gap-2">
                                    <button class="flex-1 bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                        PLAY
                                    </button>
                                    <button class="flex-1 bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 border border-gray-300 dark:border-gray-600 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        DOWNLOAD
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Advanced Content -->
                <div x-show="activeTab === 'advanced'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        
                        <!-- Audio Card 1 -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <!-- Waveform Visual -->
                            <div class="relative h-32 bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center p-4">
                                <svg class="w-full h-full" viewBox="0 0 200 60" preserveAspectRatio="none">
                                    <path d="M0,30 L5,15 L10,45 L15,10 L20,50 L25,5 L30,55 L35,8 L40,52 L45,12 L50,48 L55,16 L60,44 L65,20 L70,40 L75,24 L80,36 L85,28 L90,32 L95,30 L100,28 L105,32 L110,26 L115,34 L120,30 L125,28 L130,32 L135,26 L140,34 L145,30 L150,28 L155,32 L160,30 L165,28 L170,32 L175,30 L180,28 L185,32 L190,30 L195,28 L200,30" fill="none" stroke="rgba(255,255,255,0.6)" stroke-width="2"/>
                                </svg>
                                <div class="absolute inset-0 bg-red-500 opacity-10"></div>
                            </div>
                            
                            <div class="p-5">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Professional Recording Tips</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Duration: 10:30</p>
                                
                                <div class="flex gap-2">
                                    <button class="flex-1 bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                        PLAY
                                    </button>
                                    <button class="flex-1 bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 border border-gray-300 dark:border-gray-600 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        DOWNLOAD
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Audio Card 2 -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <!-- Waveform Visual -->
                            <div class="relative h-32 bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center p-4">
                                <svg class="w-full h-full" viewBox="0 0 200 60" preserveAspectRatio="none">
                                    <path d="M0,30 L5,18 L10,42 L15,14 L20,46 L25,10 L30,50 L35,12 L40,48 L45,16 L50,44 L55,20 L60,40 L65,24 L70,36 L75,28 L80,32 L85,30 L90,28 L95,32 L100,26 L105,34 L110,30 L115,28 L120,32 L125,26 L130,34 L135,30 L140,28 L145,32 L150,30 L155,28 L160,32 L165,26 L170,34 L175,30 L180,28 L185,32 L190,30 L195,28 L200,30" fill="none" stroke="rgba(255,255,255,0.6)" stroke-width="2"/>
                                </svg>
                                <div class="absolute inset-0 bg-indigo-500 opacity-10"></div>
                            </div>
                            
                            <div class="p-5">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Advanced Theory Explained</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Duration: 12:15</p>
                                
                                <div class="flex gap-2">
                                    <button class="flex-1 bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                        PLAY
                                    </button>
                                    <button class="flex-1 bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 border border-gray-300 dark:border-gray-600 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        DOWNLOAD
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Audio Card 3 -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <!-- Waveform Visual -->
                            <div class="relative h-32 bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center p-4">
                                <svg class="w-full h-full" viewBox="0 0 200 60" preserveAspectRatio="none">
                                    <path d="M0,30 L5,20 L10,40 L15,16 L20,44 L25,12 L30,48 L35,14 L40,46 L45,18 L50,42 L55,22 L60,38 L65,26 L70,34 L75,28 L80,32 L85,30 L90,28 L95,32 L100,26 L105,34 L110,30 L115,28 L120,32 L125,26 L130,34 L135,30 L140,28 L145,32 L150,30 L155,28 L160,32 L165,26 L170,34 L175,30 L180,28 L185,32 L190,30 L195,28 L200,30" fill="none" stroke="rgba(255,255,255,0.6)" stroke-width="2"/>
                                </svg>
                                <div class="absolute inset-0 bg-amber-500 opacity-10"></div>
                            </div>
                            
                            <div class="p-5">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Concert Performance Guide</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Duration: 9:40</p>
                                
                                <div class="flex gap-2">
                                    <button class="flex-1 bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                        PLAY
                                    </button>
                                    <button class="flex-1 bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 border border-gray-300 dark:border-gray-600 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        DOWNLOAD
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Audio Card 4 -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <!-- Waveform Visual -->
                            <div class="relative h-32 bg-gradient-to-br from-rose-400 to-rose-600 flex items-center justify-center p-4">
                                <svg class="w-full h-full" viewBox="0 0 200 60" preserveAspectRatio="none">
                                    <path d="M0,30 L5,22 L10,38 L15,18 L20,42 L25,14 L30,46 L35,16 L40,44 L45,20 L50,40 L55,24 L60,36 L65,28 L70,32 L75,30 L80,28 L85,32 L90,26 L95,34 L100,30 L105,28 L110,32 L115,26 L120,34 L125,30 L130,28 L135,32 L140,26 L145,34 L150,30 L155,28 L160,32 L165,30 L170,28 L175,32 L180,26 L185,34 L190,30 L195,28 L200,30" fill="none" stroke="rgba(255,255,255,0.6)" stroke-width="2"/>
                                </svg>
                                <div class="absolute inset-0 bg-rose-500 opacity-10"></div>
                            </div>
                            
                            <div class="p-5">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Master Class Session</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Duration: 15:00</p>
                                
                                <div class="flex gap-2">
                                    <button class="flex-1 bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                        PLAY
                                    </button>
                                    <button class="flex-1 bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 border border-gray-300 dark:border-gray-600 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        DOWNLOAD
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>
</section>
@endsection

