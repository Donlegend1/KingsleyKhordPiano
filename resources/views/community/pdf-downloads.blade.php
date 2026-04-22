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
            
            <!-- Tab Buttons - Spread out on Desktop, Stacked on Mobile -->
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 mb-6">
                <button 
                    @click="activeTab = 'beginners'"
                    :class="activeTab === 'beginners' ? 'bg-[#FF6B35] text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                    class="flex-1 px-8 py-3 rounded-lg font-semibold text-sm sm:text-base transition-all duration-200 shadow-sm"
                >
                    BEGINNERS
                </button>
                
                <button 
                    @click="activeTab = 'intermediate'"
                    :class="activeTab === 'intermediate' ? 'bg-[#FF6B35] text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                    class="flex-1 px-8 py-3 rounded-lg font-semibold text-sm sm:text-base transition-all duration-200 shadow-sm"
                >
                    INTERMEDIATE
                </button>
                
                <button 
                    @click="activeTab = 'advanced'"
                    :class="activeTab === 'advanced' ? 'bg-[#FF6B35] text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                    class="flex-1 px-8 py-3 rounded-lg font-semibold text-sm sm:text-base transition-all duration-200 shadow-sm"
                >
                    ADVANCED
                </button>
            </div>

            <!-- Tab Content -->
            <div class="min-h-[500px]">
                
                <!-- Beginners Content -->
                <div x-show="activeTab === 'beginners'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                @if($beginners->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($beginners as $pdf)
                        <div class="group bg-white dark:bg-gray-800 rounded-[28px] border border-gray-200/70 dark:border-gray-700 shadow-sm overflow-hidden hover:-translate-y-1 hover:shadow-2xl transition-all duration-300">
                            <div class="relative h-52 bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.35),_transparent_55%),linear-gradient(135deg,_#4F8DF7,_#3267D6)] flex items-center justify-center overflow-hidden">
                                @if($pdf->thumbnail)
                                    <img src="/{{ $pdf->thumbnail }}" alt="{{ $pdf->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="h-full w-full bg-white">
                                        <canvas
                                            class="pdf-preview-canvas h-full w-full"
                                            data-pdf-preview="{{ route('community.pdf-view', $pdf) }}"
                                            data-pdf-title="{{ $pdf->title }}"
                                        ></canvas>
                                    </div>
                                    <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(to_top,rgba(17,24,39,0.14),transparent_28%),linear-gradient(135deg,transparent_0,transparent_45%,rgba(255,255,255,0.08)_45%,rgba(255,255,255,0.08)_55%,transparent_55%,transparent_100%)]"></div>
                                @endif
                                <div class="absolute left-4 top-4 rounded-full bg-white/15 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-white backdrop-blur-sm">
                                    PDF Resource
                                </div>
                            </div>
                            <div class="p-5 flex flex-col gap-4">
                                <div class="space-y-2">
                                    <h3 class="text-xl font-semibold leading-tight text-gray-900 dark:text-white line-clamp-2">{{ $pdf->title }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Preview in-browser or download a copy for practice.</p>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-3 mt-auto">
                                    <a href="{{ route('community.pdf-view', $pdf) }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center gap-2 rounded-2xl border border-gray-300 dark:border-gray-600 bg-gray-100/90 dark:bg-gray-700/80 px-4 py-3 text-sm font-semibold text-gray-800 dark:text-gray-100 transition-all duration-200 hover:border-gray-400 hover:bg-white dark:hover:bg-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                        View
                                    </a>
                                    <a href="/member/community/space/pdf/downloads/{{ $pdf->id }}" class="flex items-center justify-center gap-2 rounded-2xl bg-[#FF6B35] px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-orange-500/20 transition-all duration-200 hover:-translate-y-0.5 hover:bg-[#E55A2B]">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="8 17 12 21 16 17"></polyline><line x1="12" y1="12" x2="12" y2="21"></line><path d="M20.88 18.09A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.29"></path></svg>
                                        Download
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-gray-400 dark:text-gray-600 mb-4"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                        <h3 class="text-lg font-semibold text-gray-600 dark:text-gray-300">No PDFs Available</h3>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">Check back soon for beginner resources</p>
                    </div>
                @endif
            </div>
            <!-- Intermediate Content -->
            <div x-show="activeTab === 'intermediate'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                @if($intermediate->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($intermediate as $pdf)
                        <div class="group bg-white dark:bg-gray-800 rounded-[28px] border border-gray-200/70 dark:border-gray-700 shadow-sm overflow-hidden hover:-translate-y-1 hover:shadow-2xl transition-all duration-300">
                            <div class="relative h-52 bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.35),_transparent_55%),linear-gradient(135deg,_#F79A4F,_#D96A22)] flex items-center justify-center overflow-hidden">
                                @if($pdf->thumbnail)
                                    <img src="/{{ $pdf->thumbnail }}" alt="{{ $pdf->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="h-full w-full bg-white">
                                        <canvas
                                            class="pdf-preview-canvas h-full w-full"
                                            data-pdf-preview="{{ route('community.pdf-view', $pdf) }}"
                                            data-pdf-title="{{ $pdf->title }}"
                                        ></canvas>
                                    </div>
                                    <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(to_top,rgba(17,24,39,0.14),transparent_28%),linear-gradient(135deg,transparent_0,transparent_45%,rgba(255,255,255,0.08)_45%,rgba(255,255,255,0.08)_55%,transparent_55%,transparent_100%)]"></div>
                                @endif
                                <div class="absolute left-4 top-4 rounded-full bg-white/15 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-white backdrop-blur-sm">
                                    PDF Resource
                                </div>
                            </div>
                            <div class="p-5 flex flex-col gap-4">
                                <div class="space-y-2">
                                    <h3 class="text-xl font-semibold leading-tight text-gray-900 dark:text-white line-clamp-2">{{ $pdf->title }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Preview in-browser or download a copy for practice.</p>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-3 mt-auto">
                                    <a href="{{ route('community.pdf-view', $pdf) }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center gap-2 rounded-2xl border border-gray-300 dark:border-gray-600 bg-gray-100/90 dark:bg-gray-700/80 px-4 py-3 text-sm font-semibold text-gray-800 dark:text-gray-100 transition-all duration-200 hover:border-gray-400 hover:bg-white dark:hover:bg-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                        View
                                    </a>
                                    <a href="/member/community/space/pdf/downloads/{{ $pdf->id }}" class="flex items-center justify-center gap-2 rounded-2xl bg-[#FF6B35] px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-orange-500/20 transition-all duration-200 hover:-translate-y-0.5 hover:bg-[#E55A2B]">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="8 17 12 21 16 17"></polyline><line x1="12" y1="12" x2="12" y2="21"></line><path d="M20.88 18.09A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.29"></path></svg>
                                        Download
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-gray-400 dark:text-gray-600 mb-4"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                        <h3 class="text-lg font-semibold text-gray-600 dark:text-gray-300">No PDFs Available</h3>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">Check back soon for intermediate resources</p>
                    </div>
                @endif
            </div>

                <!-- Advanced Content -->
                <div x-show="activeTab === 'advanced'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                    @if($advanced->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            @foreach($advanced as $pdf)
                            <div class="group bg-white dark:bg-gray-800 rounded-[28px] border border-gray-200/70 dark:border-gray-700 shadow-sm overflow-hidden hover:-translate-y-1 hover:shadow-2xl transition-all duration-300">
                                <div class="relative h-52 bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.35),_transparent_55%),linear-gradient(135deg,_#F56565,_#C53030)] flex items-center justify-center overflow-hidden">
                                    @if($pdf->thumbnail)
                                        <img src="/{{ $pdf->thumbnail }}" alt="{{ $pdf->title }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="h-full w-full bg-white">
                                            <canvas
                                                class="pdf-preview-canvas h-full w-full"
                                                data-pdf-preview="{{ route('community.pdf-view', $pdf) }}"
                                                data-pdf-title="{{ $pdf->title }}"
                                            ></canvas>
                                        </div>
                                        <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(to_top,rgba(17,24,39,0.14),transparent_28%),linear-gradient(135deg,transparent_0,transparent_45%,rgba(255,255,255,0.08)_45%,rgba(255,255,255,0.08)_55%,transparent_55%,transparent_100%)]"></div>
                                    @endif
                                    <div class="absolute left-4 top-4 rounded-full bg-white/15 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-white backdrop-blur-sm">
                                        PDF Resource
                                    </div>
                                </div>
                                <div class="p-5 flex flex-col gap-4">
                                    <div class="space-y-2">
                                        <h3 class="text-xl font-semibold leading-tight text-gray-900 dark:text-white line-clamp-2">{{ $pdf->title }}</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Preview in-browser or download a copy for practice.</p>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-3 mt-auto">
                                        <a href="{{ route('community.pdf-view', $pdf) }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center gap-2 rounded-2xl border border-gray-300 dark:border-gray-600 bg-gray-100/90 dark:bg-gray-700/80 px-4 py-3 text-sm font-semibold text-gray-800 dark:text-gray-100 transition-all duration-200 hover:border-gray-400 hover:bg-white dark:hover:bg-gray-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                            View
                                        </a>
                                        <a href="/member/community/space/pdf/downloads/{{ $pdf->id }}" class="flex items-center justify-center gap-2 rounded-2xl bg-[#FF6B35] px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-orange-500/20 transition-all duration-200 hover:-translate-y-0.5 hover:bg-[#E55A2B]">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="8 17 12 21 16 17"></polyline><line x1="12" y1="12" x2="12" y2="21"></line><path d="M20.88 18.09A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.29"></path></svg>
                                            Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-gray-400 dark:text-gray-600 mb-4"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                            <h3 class="text-lg font-semibold text-gray-600 dark:text-gray-300">No PDFs Available</h3>
                            <p class="text-gray-500 dark:text-gray-400 mt-2">Check back soon for advanced resources</p>
                        </div>
                    @endif
                </div>

            </div>
        </div>

    </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.5.136/pdf.min.mjs" type="module"></script>
<script type="module">
    import * as pdfjsLib from "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.5.136/pdf.min.mjs";

    pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.5.136/pdf.worker.min.mjs";

    const canvases = document.querySelectorAll(".pdf-preview-canvas");

    const renderPreview = async (canvas) => {
        const url = canvas.dataset.pdfPreview;

        if (!url) {
            return;
        }

        try {
            const loadingTask = pdfjsLib.getDocument(url);
            const pdf = await loadingTask.promise;
            const page = await pdf.getPage(1);

            const unscaledViewport = page.getViewport({ scale: 1 });
            const cssWidth = canvas.clientWidth || 320;
            const cssHeight = canvas.clientHeight || 208;
            const scale = Math.max(cssWidth / unscaledViewport.width, cssHeight / unscaledViewport.height);
            const viewport = page.getViewport({ scale });
            const context = canvas.getContext("2d");

            canvas.width = viewport.width;
            canvas.height = viewport.height;

            await page.render({
                canvasContext: context,
                viewport,
            }).promise;
        } catch (error) {
            canvas.replaceWith(document.createTextNode("Preview unavailable"));
        }
    };

    const observer = new IntersectionObserver((entries) => {
        for (const entry of entries) {
            if (!entry.isIntersecting) {
                continue;
            }

            renderPreview(entry.target);
            observer.unobserve(entry.target);
        }
    }, { rootMargin: "120px" });

    canvases.forEach((canvas) => observer.observe(canvas));
</script>

@endsection
