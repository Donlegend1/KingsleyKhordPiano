@extends('layouts.community')

@section('content')

<!-- Header Section -->
<div class="bg-white dark:bg-[#161617] border-b border-gray-200 dark:border-white/10 mb-6">
    <div class="px-6 py-5">
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white leading-tight">Application</h1>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    @php
        $apps = [
            ['name' => 'Zoom', 'badge' => 'Video Conferencing', 'desc' => 'Video meetings and online classes.', 'initial' => 'Z', 'bg' => 'bg-blue-500', 'link' => 'https://support.zoom.com/hc/en/article?id=zm_kb&sysparm_article=KB0060928'],
            ['name' => 'OBS Studio', 'badge' => 'Screen Recording', 'desc' => 'Free screen recording and live streaming.', 'initial' => 'O', 'bg' => 'bg-gray-900', 'link' => 'https://obsproject.com/download'],
            ['name' => 'VB Virtual Audio Cable', 'badge' => 'Virtual Audio', 'desc' => 'Route audio between applications.', 'initial' => 'V', 'bg' => 'bg-slate-600', 'link' => 'https://vb-audio.com/Cable/'],
            ['name' => 'Midiculous', 'badge' => 'Virtual MIDI', 'desc' => 'Multi-port virtual MIDI router.', 'initial' => 'M', 'bg' => 'bg-purple-600', 'link' => 'https://gospelmusicians.com/'],
            ['name' => 'SampleTank 4', 'badge' => 'Virtual Instrument', 'desc' => 'Powerful sound library and workstation.', 'initial' => 'S', 'bg' => 'bg-red-600', 'link' => 'https://www.ikmultimedia.com/demodownload/'],
            ['name' => 'Metronome', 'badge' => 'Practice Tool', 'desc' => 'Keep perfect time while you practice.', 'initial' => 'M', 'bg' => 'bg-gray-800', 'link' => 'https://www.nch.com.au/metronome/index.html?srsltid=AfmBOooS6pYV-Ps3r9IuHbXIYxEOxTa8ow75ebbeV56vDULG63SxcCr_'],
            ['name' => 'Keyscape', 'badge' => 'Virtual Instrument', 'desc' => 'Stunning piano sounds by Spectrasonics.', 'initial' => 'K', 'bg' => 'bg-gray-900', 'link' => 'https://khordsounds.com/product/keyscape/'],
            ['name' => 'loopMIDI', 'badge' => 'Virtual MIDI', 'desc' => 'Create virtual MIDI loopback ports.', 'initial' => 'L', 'bg' => 'bg-sky-500', 'macos' => false, 'link' => 'https://www.tobias-erichsen.de/software/loopmidi.html'],
            ['name' => 'MIDI-OX', 'badge' => 'MIDI Utility', 'desc' => 'Monitor and send MIDI messages.', 'initial' => 'X', 'bg' => 'bg-green-700', 'macos' => false, 'link' => 'https://midiox.org/download-midi-ox/'],
        ];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach ($apps as $app)
            <div class="bg-white dark:bg-[#161617] border border-gray-200 dark:border-white/10 rounded-2xl p-5">
                <div class="flex items-start gap-3">
                    <div class="w-12 h-12 rounded-xl {{ $app['bg'] }} text-white flex items-center justify-center font-bold text-lg flex-shrink-0">
                        {{ $app['initial'] }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ $app['name'] }}</h3>
                            <span class="text-xs font-medium text-indigo-600 dark:text-blue-400 bg-indigo-50 dark:bg-blue-500/10 px-2 py-0.5 rounded-full">{{ $app['badge'] }}</span>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $app['desc'] }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 mt-5">
                    <a href="{{ $app['link'] }}" target="_blank" rel="noopener noreferrer" class="px-4 py-2 border border-gray-300 dark:border-white/10 rounded-lg text-sm font-semibold text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-white/5 transition">
                        Download
                    </a>
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-white/5 px-2.5 py-1 rounded-md">Windows</span>
                    @if($app['macos'] ?? true)
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-white/5 px-2.5 py-1 rounded-md">macOS</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>


</div>
@endsection
