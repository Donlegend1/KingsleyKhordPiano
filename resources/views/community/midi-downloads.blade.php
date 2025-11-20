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
            <span>Midi Files</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold text-[#1F2937] dark:text-white">Midi Files</h1>
        <p class="text-gray-600 dark:text-gray-300 mt-2">Download MIDI files organized by skill level to practice and learn</p>
    </div>
</div>

<!-- Main Content Section -->
<section class="px-4 sm:px-6 pb-6 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto">

        <!-- MIDI Files Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            <!-- Midi Card 1 -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <!-- Midi File Visual -->
                <div class="relative h-32 bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center p-4">
                    <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14.23 12.004a2.236 2.236 0 0 1 2.235 2.236A2.236 2.236 0 0 1 14.23 16.476a2.236 2.236 0 0 1-2.235-2.236 2.236 2.236 0 0 1 2.235-2.236zm2.648-10.69c.366 0 .662.297.662.662v3.34c1.154.28 2.01 1.32 2.01 2.585a2.67 2.67 0 0 1-2.667 2.667h-4.666a2.67 2.67 0 0 1-2.667-2.667c0-1.265.856-2.305 2.01-2.585V1.976c0-.365.296-.662.662-.662h2.326zm-.662 4.666c-.735 0-1.333.598-1.333 1.333 0 .735.598 1.333 1.333 1.333h4.666c.735 0 1.333-.598 1.333-1.333 0-.735-.598-1.333-1.333-1.333h-4.666z"/>
                    </svg>
                    <div class="absolute inset-0 bg-blue-500 opacity-10"></div>
                </div>

                <div class="p-5">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">C Major Scale</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Key: C Major | BPM: 120</p>

                    <button class="w-full bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        VIEW & DOWNLOAD
                    </button>
                </div>
            </div>

            <!-- Midi Card 2 -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <!-- Midi File Visual -->
                <div class="relative h-32 bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center p-4">
                    <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14.23 12.004a2.236 2.236 0 0 1 2.235 2.236A2.236 2.236 0 0 1 14.23 16.476a2.236 2.236 0 0 1-2.235-2.236 2.236 2.236 0 0 1 2.235-2.236zm2.648-10.69c.366 0 .662.297.662.662v3.34c1.154.28 2.01 1.32 2.01 2.585a2.67 2.67 0 0 1-2.667 2.667h-4.666a2.67 2.67 0 0 1-2.667-2.667c0-1.265.856-2.305 2.01-2.585V1.976c0-.365.296-.662.662-.662h2.326zm-.662 4.666c-.735 0-1.333.598-1.333 1.333 0 .735.598 1.333 1.333 1.333h4.666c.735 0 1.333-.598 1.333-1.333 0-.735-.598-1.333-1.333-1.333h-4.666z"/>
                    </svg>
                    <div class="absolute inset-0 bg-green-500 opacity-10"></div>
                </div>

                <div class="p-5">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Basic I-IV-V Chord</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Key: C Major | BPM: 100</p>

                    <button class="w-full bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        VIEW & DOWNLOAD
                    </button>
                </div>
            </div>

            <!-- Midi Card 3 -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <!-- Midi File Visual -->
                <div class="relative h-32 bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center p-4">
                    <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14.23 12.004a2.236 2.236 0 0 1 2.235 2.236A2.236 2.236 0 0 1 14.23 16.476a2.236 2.236 0 0 1-2.235-2.236 2.236 2.236 0 0 1 2.235-2.236zm2.648-10.69c.366 0 .662.297.662.662v3.34c1.154.28 2.01 1.32 2.01 2.585a2.67 2.67 0 0 1-2.667 2.667h-4.666a2.67 2.67 0 0 1-2.667-2.667c0-1.265.856-2.305 2.01-2.585V1.976c0-.365.296-.662.662-.662h2.326zm-.662 4.666c-.735 0-1.333.598-1.333 1.333 0 .735.598 1.333 1.333 1.333h4.666c.735 0 1.333-.598 1.333-1.333 0-.735-.598-1.333-1.333-1.333h-4.666z"/>
                    </svg>
                    <div class="absolute inset-0 bg-purple-500 opacity-10"></div>
                </div>

                <div class="p-5">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Rhythm Patterns</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">4/4 Time | BPM: 90</p>

                    <button class="w-full bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        VIEW & DOWNLOAD
                    </button>
                </div>
            </div>

            <!-- Midi Card 4 -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <!-- Midi File Visual -->
                <div class="relative h-32 bg-gradient-to-br from-yellow-400 to-yellow-600 flex items-center justify-center p-4">
                    <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14.23 12.004a2.236 2.236 0 0 1 2.235 2.236A2.236 2.236 0 0 1 14.23 16.476a2.236 2.236 0 0 1-2.235-2.236 2.236 2.236 0 0 1 2.235-2.236zm2.648-10.69c.366 0 .662.297.662.662v3.34c1.154.28 2.01 1.32 2.01 2.585a2.67 2.67 0 0 1-2.667 2.667h-4.666a2.67 2.67 0 0 1-2.667-2.667c0-1.265.856-2.305 2.01-2.585V1.976c0-.365.296-.662.662-.662h2.326zm-.662 4.666c-.735 0-1.333.598-1.333 1.333 0 .735.598 1.333 1.333 1.333h4.666c.735 0 1.333-.598 1.333-1.333 0-.735-.598-1.333-1.333-1.333h-4.666z"/>
                    </svg>
                    <div class="absolute inset-0 bg-yellow-500 opacity-10"></div>
                </div>

                <div class="p-5">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Left Hand Patterns</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Beginner Level | BPM: 80</p>

                    <button class="w-full bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        VIEW & DOWNLOAD
                    </button>
                </div>
            </div>

            <!-- Midi Card 5 -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <!-- Midi File Visual -->
                <div class="relative h-32 bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center p-4">
                    <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14.23 12.004a2.236 2.236 0 0 1 2.235 2.236A2.236 2.236 0 0 1 14.23 16.476a2.236 2.236 0 0 1-2.235-2.236 2.236 2.236 0 0 1 2.235-2.236zm2.648-10.69c.366 0 .662.297.662.662v3.34c1.154.28 2.01 1.32 2.01 2.585a2.67 2.67 0 0 1-2.667 2.667h-4.666a2.67 2.67 0 0 1-2.667-2.667c0-1.265.856-2.305 2.01-2.585V1.976c0-.365.296-.662.662-.662h2.326zm-.662 4.666c-.735 0-1.333.598-1.333 1.333 0 .735.598 1.333 1.333 1.333h4.666c.735 0 1.333-.598 1.333-1.333 0-.735-.598-1.333-1.333-1.333h-4.666z"/>
                    </svg>
                    <div class="absolute inset-0 bg-orange-500 opacity-10"></div>
                </div>

                <div class="p-5">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Jazz Progressions</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Key: F Minor | BPM: 140</p>

                    <button class="w-full bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        VIEW & DOWNLOAD
                    </button>
                </div>
            </div>

            <!-- Midi Card 6 -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <!-- Midi File Visual -->
                <div class="relative h-32 bg-gradient-to-br from-pink-400 to-pink-600 flex items-center justify-center p-4">
                    <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14.23 12.004a2.236 2.236 0 0 1 2.235 2.236A2.236 2.236 0 0 1 14.23 16.476a2.236 2.236 0 0 1-2.235-2.236 2.236 2.236 0 0 1 2.235-2.236zm2.648-10.69c.366 0 .662.297.662.662v3.34c1.154.28 2.01 1.32 2.01 2.585a2.67 2.67 0 0 1-2.667 2.667h-4.666a2.67 2.67 0 0 1-2.667-2.667c0-1.265.856-2.305 2.01-2.585V1.976c0-.365.296-.662.662-.662h2.326zm-.662 4.666c-.735 0-1.333.598-1.333 1.333 0 .735.598 1.333 1.333 1.333h4.666c.735 0 1.333-.598 1.333-1.333 0-.735-.598-1.333-1.333-1.333h-4.666z"/>
                    </svg>
                    <div class="absolute inset-0 bg-pink-500 opacity-10"></div>
                </div>

                <div class="p-5">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Complex Scales</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Dorian Mode | BPM: 120</p>

                    <button class="w-full bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        VIEW & DOWNLOAD
                    </button>
                </div>
            </div>

            <!-- Midi Card 7 -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <!-- Midi File Visual -->
                <div class="relative h-32 bg-gradient-to-br from-teal-400 to-teal-600 flex items-center justify-center p-4">
                    <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14.23 12.004a2.236 2.236 0 0 1 2.235 2.236A2.236 2.236 0 0 1 14.23 16.476a2.236 2.236 0 0 1-2.235-2.236 2.236 2.236 0 0 1 2.235-2.236zm2.648-10.69c.366 0 .662.297.662.662v3.34c1.154.28 2.01 1.32 2.01 2.585a2.67 2.67 0 0 1-2.667 2.667h-4.666a2.67 2.67 0 0 1-2.667-2.667c0-1.265.856-2.305 2.01-2.585V1.976c0-.365.296-.662.662-.662h2.326zm-.662 4.666c-.735 0-1.333.598-1.333 1.333 0 .735.598 1.333 1.333 1.333h4.666c.735 0 1.333-.598 1.333-1.333 0-.735-.598-1.333-1.333-1.333h-4.666z"/>
                    </svg>
                    <div class="absolute inset-0 bg-teal-500 opacity-10"></div>
                </div>

                <div class="p-5">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Arpeggio Patterns</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Major 7th Arps | BPM: 110</p>

                    <button class="w-full bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        VIEW & DOWNLOAD
                    </button>
                </div>
            </div>

            <!-- Midi Card 8 -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <!-- Midi File Visual -->
                <div class="relative h-32 bg-gradient-to-br from-cyan-400 to-cyan-600 flex items-center justify-center p-4">
                    <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14.23 12.004a2.236 2.236 0 0 1 2.235 2.236A2.236 2.236 0 0 1 14.23 16.476a2.236 2.236 0 0 1-2.235-2.236 2.236 2.236 0 0 1 2.235-2.236zm2.648-10.69c.366 0 .662.297.662.662v3.34c1.154.28 2.01 1.32 2.01 2.585a2.67 2.67 0 0 1-2.667 2.667h-4.666a2.67 2.67 0 0 1-2.667-2.667c0-1.265.856-2.305 2.01-2.585V1.976c0-.365.296-.662.662-.662h2.326zm-.662 4.666c-.735 0-1.333.598-1.333 1.333 0 .735.598 1.333 1.333 1.333h4.666c.735 0 1.333-.598 1.333-1.333 0-.735-.598-1.333-1.333-1.333h-4.666z"/>
                    </svg>
                    <div class="absolute inset-0 bg-cyan-500 opacity-10"></div>
                </div>

                <div class="p-5">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Blues Turnaround</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">12-Bar Blues | BPM: 130</p>

                    <button class="w-full bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        VIEW & DOWNLOAD
                    </button>
                </div>
            </div>

            <!-- Midi Card 9 -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <!-- Midi File Visual -->
                <div class="relative h-32 bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center p-4">
                    <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14.23 12.004a2.236 2.236 0 0 1 2.235 2.236A2.236 2.236 0 0 1 14.23 16.476a2.236 2.236 0 0 1-2.235-2.236 2.236 2.236 0 0 1 2.235-2.236zm2.648-10.69c.366 0 .662.297.662.662v3.34c1.154.28 2.01 1.32 2.01 2.585a2.67 2.67 0 0 1-2.667 2.667h-4.666a2.67 2.67 0 0 1-2.667-2.667c0-1.265.856-2.305 2.01-2.585V1.976c0-.365.296-.662.662-.662h2.326zm-.662 4.666c-.735 0-1.333.598-1.333 1.333 0 .735.598 1.333 1.333 1.333h4.666c.735 0 1.333-.598 1.333-1.333 0-.735-.598-1.333-1.333-1.333h-4.666z"/>
                    </svg>
                    <div class="absolute inset-0 bg-red-500 opacity-10"></div>
                </div>

                <div class="p-5">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Advanced Voicings</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Quartal Harmony | BPM: 150</p>

                    <button class="w-full bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        VIEW & DOWNLOAD
                    </button>
                </div>
            </div>

            <!-- Midi Card 10 -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <!-- Midi File Visual -->
                <div class="relative h-32 bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center p-4">
                    <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14.23 12.004a2.236 2.236 0 0 1 2.235 2.236A2.236 2.236 0 0 1 14.23 16.476a2.236 2.236 0 0 1-2.235-2.236 2.236 2.236 0 0 1 2.235-2.236zm2.648-10.69c.366 0 .662.297.662.662v3.34c1.154.28 2.01 1.32 2.01 2.585a2.67 2.67 0 0 1-2.667 2.667h-4.666a2.67 2.67 0 0 1-2.667-2.667c0-1.265.856-2.305 2.01-2.585V1.976c0-.365.296-.662.662-.662h2.326zm-.662 4.666c-.735 0-1.333.598-1.333 1.333 0 .735.598 1.333 1.333 1.333h4.666c.735 0 1.333-.598 1.333-1.333 0-.735-.598-1.333-1.333-1.333h-4.666z"/>
                    </svg>
                    <div class="absolute inset-0 bg-indigo-500 opacity-10"></div>
                </div>

                <div class="p-5">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Polyrhythmic Patterns</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">7/8 Time Signature | BPM: 160</p>

                    <button class="w-full bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        VIEW & DOWNLOAD
                    </button>
                </div>
            </div>

            <!-- Midi Card 11 -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <!-- Midi File Visual -->
                <div class="relative h-32 bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center p-4">
                    <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14.23 12.004a2.236 2.236 0 0 1 2.235 2.236A2.236 2.236 0 0 1 14.23 16.476a2.236 2.236 0 0 1-2.235-2.236 2.236 2.236 0 0 1 2.235-2.236zm2.648-10.69c.366 0 .662.297.662.662v3.34c1.154.28 2.01 1.32 2.01 2.585a2.67 2.67 0 0 1-2.667 2.667h-4.666a2.67 2.67 0 0 1-2.667-2.667c0-1.265.856-2.305 2.01-2.585V1.976c0-.365.296-.662.662-.662h2.326zm-.662 4.666c-.735 0-1.333.598-1.333 1.333 0 .735.598 1.333 1.333 1.333h4.666c.735 0 1.333-.598 1.333-1.333 0-.735-.598-1.333-1.333-1.333h-4.666z"/>
                    </svg>
                    <div class="absolute inset-0 bg-amber-500 opacity-10"></div>
                </div>

                <div class="p-5">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Concert Etudes</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Chopin Style | BPM: 180</p>

                    <button class="w-full bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        VIEW & DOWNLOAD
                    </button>
                </div>
            </div>

            <!-- Midi Card 12 -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <!-- Midi File Visual -->
                <div class="relative h-32 bg-gradient-to-br from-rose-400 to-rose-600 flex items-center justify-center p-4">
                    <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14.23 12.004a2.236 2.236 0 0 1 2.235 2.236A2.236 2.236 0 0 1 14.23 16.476a2.236 2.236 0 0 1-2.235-2.236 2.236 2.236 0 0 1 2.235-2.236zm2.648-10.69c.366 0 .662.297.662.662v3.34c1.154.28 2.01 1.32 2.01 2.585a2.67 2.67 0 0 1-2.667 2.667h-4.666a2.67 2.67 0 0 1-2.667-2.667c0-1.265.856-2.305 2.01-2.585V1.976c0-.365.296-.662.662-.662h2.326zm-.662 4.666c-.735 0-1.333.598-1.333 1.333 0 .735.598 1.333 1.333 1.333h4.666c.735 0 1.333-.598 1.333-1.333 0-.735-.598-1.333-1.333-1.333h-4.666z"/>
                    </svg>
                    <div class="absolute inset-0 bg-rose-500 opacity-10"></div>
                </div>

                <div class="p-5">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Master Class MIDI</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Professional Level | BPM: 200</p>

                    <button class="w-full bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        VIEW & DOWNLOAD
                    </button>
                </div>
            </div>

        </div>

    </div>
</section>
@endsection
