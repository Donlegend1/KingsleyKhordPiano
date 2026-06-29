@extends('layouts.community')

@section('page-title')
    <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white leading-tight truncate">MIDI Files</h1>
@endsection

@section('page-search')
    <div class="relative">
        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 1 0 0-16 8 8 0 0 0 0 16Z"/>
        </svg>
        <input
            type="text"
            x-model="search"
            placeholder="Search MIDI files..."
            class="w-full pl-11 pr-4 py-2.5 rounded-full border border-gray-200 bg-white text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#FF6B35]/30 focus:border-[#FF6B35] transition"
        >
    </div>
@endsection

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 pb-8">

    <!-- MIDI Files Grid -->
    @if($midiFiles->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
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

                <div
                    x-show="search === '' || {{ \Illuminate\Support\Js::from(Str::lower($midiFile->name)) }}.includes(search.toLowerCase())"
                    class="group bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col h-full overflow-hidden">
                    
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
