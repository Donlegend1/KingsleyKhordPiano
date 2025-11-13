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
            <span>PDF Downloads</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold text-[#1F2937] dark:text-white">PDF Downloads</h1>
        <p class="text-gray-600 dark:text-gray-300 mt-2">Access exclusive PDF resources organized by skill level</p>
    </div>
</div>

<!-- Main Content Section -->
<section class="px-4 sm:px-6 pb-6 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto">
        
        <!-- Tabs Navigation -->
        <div class="mb-8" x-data="{ activeTab: 'beginners' }">
            
            <!-- Tab Buttons - Horizontal on Desktop, Stacked on Mobile -->
            <div class="flex flex-col sm:flex-row sm:justify-start gap-3 sm:gap-4 mb-6">
                <button 
                    @click="activeTab = 'beginners'"
                    :class="activeTab === 'beginners' ? 'bg-[#FF6B35] text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                    class="px-8 py-3 rounded-lg font-semibold text-sm sm:text-base transition-all duration-200 shadow-sm"
                >
                    BEGINNERS
                </button>
                
                <button 
                    @click="activeTab = 'intermediate'"
                    :class="activeTab === 'intermediate' ? 'bg-[#FF6B35] text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                    class="px-8 py-3 rounded-lg font-semibold text-sm sm:text-base transition-all duration-200 shadow-sm"
                >
                    INTERMEDIATE
                </button>
                
                <button 
                    @click="activeTab = 'advanced'"
                    :class="activeTab === 'advanced' ? 'bg-[#FF6B35] text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                    class="px-8 py-3 rounded-lg font-semibold text-sm sm:text-base transition-all duration-200 shadow-sm"
                >
                    ADVANCED
                </button>
            </div>

            <!-- Tab Content -->
            <div class="min-h-[500px]">
                
                <!-- Beginners Content -->
                <div x-show="activeTab === 'beginners'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        
                        <!-- PDF Card 1 -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <div class="relative h-48 bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                                <img src="https://placehold.co/400x300/4F46E5/FFFFFF/png?text=Beginner+Piano+Guide" alt="PDF Thumbnail" class="w-full h-full object-cover">
                            </div>
                            <div class="p-5">
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">Piano Basics Guide</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Essential piano fundamentals for beginners</p>
                                
                                <div class="flex gap-3">
                                    <button class="flex-1 bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View
                                    </button>
                                    <button class="flex-1 bg-[#FFD736] hover:bg-[#E5C634] text-gray-800 py-2 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        Download
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- PDF Card 2 -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <div class="relative h-48 bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center">
                                <img src="https://placehold.co/400x300/10B981/FFFFFF/png?text=Chord+Charts" alt="PDF Thumbnail" class="w-full h-full object-cover">
                            </div>
                            <div class="p-5">
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">Basic Chord Charts</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Common chords and finger positions</p>
                                
                                <div class="flex gap-3">
                                    <button class="flex-1 bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View
                                    </button>
                                    <button class="flex-1 bg-[#FFD736] hover:bg-[#E5C634] text-gray-800 py-2 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        Download
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- PDF Card 3 -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <div class="relative h-48 bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center">
                                <img src="https://placehold.co/400x300/8B5CF6/FFFFFF/png?text=Reading+Music" alt="PDF Thumbnail" class="w-full h-full object-cover">
                            </div>
                            <div class="p-5">
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">Reading Music Notes</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Learn to read sheet music easily</p>
                                
                                <div class="flex gap-3">
                                    <button class="flex-1 bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View
                                    </button>
                                    <button class="flex-1 bg-[#FFD736] hover:bg-[#E5C634] text-gray-800 py-2 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        Download
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Intermediate Content -->
                <div x-show="activeTab === 'intermediate'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        
                        <!-- PDF Card 1 -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <div class="relative h-48 bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center">
                                <img src="https://placehold.co/400x300/F97316/FFFFFF/png?text=Jazz+Chords" alt="PDF Thumbnail" class="w-full h-full object-cover">
                            </div>
                            <div class="p-5">
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">Jazz Chord Progressions</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Explore jazz harmony and voicings</p>
                                
                                <div class="flex gap-3">
                                    <button class="flex-1 bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View
                                    </button>
                                    <button class="flex-1 bg-[#FFD736] hover:bg-[#E5C634] text-gray-800 py-2 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        Download
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- PDF Card 2 -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <div class="relative h-48 bg-gradient-to-br from-pink-400 to-pink-600 flex items-center justify-center">
                                <img src="https://placehold.co/400x300/EC4899/FFFFFF/png?text=Advanced+Scales" alt="PDF Thumbnail" class="w-full h-full object-cover">
                            </div>
                            <div class="p-5">
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">Advanced Scale Patterns</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Master complex scales and modes</p>
                                
                                <div class="flex gap-3">
                                    <button class="flex-1 bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View
                                    </button>
                                    <button class="flex-1 bg-[#FFD736] hover:bg-[#E5C634] text-gray-800 py-2 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        Download
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- PDF Card 3 -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <div class="relative h-48 bg-gradient-to-br from-teal-400 to-teal-600 flex items-center justify-center">
                                <img src="https://placehold.co/400x300/14B8A6/FFFFFF/png?text=Improvisation" alt="PDF Thumbnail" class="w-full h-full object-cover">
                            </div>
                            <div class="p-5">
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">Improvisation Techniques</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Learn to improvise with confidence</p>
                                
                                <div class="flex gap-3">
                                    <button class="flex-1 bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View
                                    </button>
                                    <button class="flex-1 bg-[#FFD736] hover:bg-[#E5C634] text-gray-800 py-2 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        Download
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Advanced Content -->
                <div x-show="activeTab === 'advanced'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        
                        <!-- PDF Card 1 -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <div class="relative h-48 bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center">
                                <img src="https://placehold.co/400x300/EF4444/FFFFFF/png?text=Pro+Techniques" alt="PDF Thumbnail" class="w-full h-full object-cover">
                            </div>
                            <div class="p-5">
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">Professional Techniques</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Advanced performance and recording tips</p>
                                
                                <div class="flex gap-3">
                                    <button class="flex-1 bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View
                                    </button>
                                    <button class="flex-1 bg-[#FFD736] hover:bg-[#E5C634] text-gray-800 py-2 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        Download
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- PDF Card 2 -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <div class="relative h-48 bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center">
                                <img src="https://placehold.co/400x300/6366F1/FFFFFF/png?text=Music+Theory" alt="PDF Thumbnail" class="w-full h-full object-cover">
                            </div>
                            <div class="p-5">
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">Advanced Music Theory</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Deep dive into harmonic analysis</p>
                                
                                <div class="flex gap-3">
                                    <button class="flex-1 bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View
                                    </button>
                                    <button class="flex-1 bg-[#FFD736] hover:bg-[#E5C634] text-gray-800 py-2 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        Download
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- PDF Card 3 -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <div class="relative h-48 bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center">
                                <img src="https://placehold.co/400x300/F59E0B/FFFFFF/png?text=Master+Class" alt="PDF Thumbnail" class="w-full h-full object-cover">
                            </div>
                            <div class="p-5">
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">Master Class Notes</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Professional pianist insights and tips</p>
                                
                                <div class="flex gap-3">
                                    <button class="flex-1 bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View
                                    </button>
                                    <button class="flex-1 bg-[#FFD736] hover:bg-[#E5C634] text-gray-800 py-2 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        Download
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

