@extends('layouts.community')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Breadcrumbs & Header Section -->
    <div class="mb-8">
        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-3">
            <a href="/member/community" class="hover:text-[#FF6B35] transition-colors flex items-center gap-1 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Overview
            </a>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <span class="text-gray-900 font-semibold">MIDI Files</span>
        </div>
        
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white rounded-3xl p-6 md:p-8 border border-gray-100 shadow-sm">
            <div class="space-y-1">
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">MIDI Resources Library</h1>
                <p class="text-gray-500 text-sm sm:text-base">Download premium MIDI files organized to help you practice, learn, and master songs.</p>
            </div>
            <div class="flex items-center gap-2 self-start md:self-center bg-orange-50/80 text-[#FF6B35] font-bold px-4 py-2.5 rounded-2xl text-sm border border-orange-100/50 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <span>{{ $midiFiles->count() }} Files</span>
            </div>
        </div>
    </div>

    <!-- MIDI Files Grid -->
    @if($midiFiles->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 md:gap-8">
            @php
                $gradients = [
                    'from-slate-900 via-indigo-950 to-slate-900',
                    'from-slate-900 via-purple-950 to-slate-900',
                    'from-slate-900 via-violet-950 to-slate-900',
                    'from-slate-900 via-rose-950 to-slate-900',
                    'from-slate-900 via-teal-950 to-slate-900',
                    'from-slate-900 via-emerald-950 to-slate-900',
                ];
                $iconColors = [
                    'text-indigo-400 border-indigo-500/20 bg-indigo-500/10',
                    'text-purple-400 border-purple-500/20 bg-purple-500/10',
                    'text-violet-400 border-violet-500/20 bg-violet-500/10',
                    'text-rose-400 border-rose-500/20 bg-rose-500/10',
                    'text-teal-400 border-teal-500/20 bg-teal-500/10',
                    'text-emerald-400 border-emerald-500/20 bg-emerald-500/10',
                ];
            @endphp

            @foreach ($midiFiles as $index => $midiFile)
                @php
                    $grad = $gradients[$index % count($gradients)];  
                    $iconColor = $iconColors[$index % count($iconColors)];  
                @endphp

                <div class="group bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col h-full overflow-hidden">
                    
                    {{-- Card Visual Header --}}
                    <div class="relative h-48 w-full overflow-hidden bg-gray-50 flex-shrink-0">
                        @if ($midiFile->thumbnail_path)
                            {{-- Image present --}}
                            <img 
                                src="/{{$midiFile->thumbnail_path}}" 
                                alt="{{ $midiFile->name }}" 
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/40 via-transparent to-transparent"></div>
                            <div class="absolute left-4 top-4 rounded-full bg-white/20 backdrop-blur-md border border-white/25 px-3.5 py-1 text-[10px] font-bold uppercase tracking-[0.18em] text-white shadow-sm">
                                MIDI File
                            </div>
                        @else
                            {{-- No Image, use premium pattern --}}
                            <div class="w-full h-full bg-gradient-to-br {{ $grad }} flex items-center justify-center p-6 relative">
                                <!-- Pattern Overlay -->
                                <div class="absolute inset-0 opacity-[0.03] bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>
                                <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(255,255,255,0.05),transparent_70%)]"></div>
                                
                                <!-- Glassmorphic Icon Badge -->
                                <div class="relative z-10 w-16 h-16 rounded-2xl border flex items-center justify-center shadow-lg transition-transform duration-300 group-hover:scale-110 {{ $iconColor }}">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                                    </svg>
                                </div>

                                <div class="absolute left-4 top-4 rounded-full bg-white/10 backdrop-blur-md border border-white/15 px-3.5 py-1 text-[10px] font-bold uppercase tracking-[0.18em] text-white shadow-sm">
                                    MIDI File
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Card Body --}}
                    <div class="p-6 flex flex-col flex-grow">
                        <div class="flex-grow space-y-2 mb-6">
                            <h3 class="text-lg font-bold text-gray-900 leading-snug line-clamp-2 min-h-[2.75rem] group-hover:text-[#FF6B35] transition-colors" title="{{ $midiFile->name }}">
                                {{ $midiFile->name }}
                            </h3>
                            
                            {{-- <p class="text-sm text-gray-500 leading-relaxed line-clamp-2 min-h-[2.5rem]">
                                {{ $midiFile->description ?: 'Download the official MIDI file to practice keys, study the notes, and play along.' }}
                            </p> --}}
                        </div>

                        {{-- Button at bottom --}}
                        <div class="mt-auto">
                            <a href="{{ url('member/community/space/midi-download/' . $midiFile->id) }}"
                               class="w-full bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-3.5 px-4 rounded-2xl font-bold transition-all duration-300 flex items-center justify-center gap-2 shadow-sm hover:shadow-lg hover:shadow-orange-500/20 hover:-translate-y-0.5">
                                <svg class="w-5 h-5 transition-transform duration-300 group-hover:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <span>View </span>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        {{-- Empty State --}}
        <div class="flex flex-col items-center justify-center text-center py-20 bg-white rounded-3xl border border-gray-100 shadow-sm px-6">
            <div class="w-20 h-20 rounded-full bg-slate-50 flex items-center justify-center border border-slate-100 mb-4">
                <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-800">No MIDI Files Found</h3>
            <p class="text-gray-500 mt-2 max-w-md">There are currently no MIDI files uploaded to this space. Please check back later or contact support if you think this is an error.</p>
        </div>
    @endif
</div>
@endsection
