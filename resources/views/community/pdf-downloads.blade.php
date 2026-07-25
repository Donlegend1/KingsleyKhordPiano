@extends('layouts.community')

@section('breadcrumb-parent', 'Overview')
@section('breadcrumb-parent-url', '/member/my-library')
@section('breadcrumb', 'PDF Files')

@section('page-search')
    <div class="relative group">
        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-[#FF6B35] transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 1 0 0-16 8 8 0 0 0 0 16Z"/>
        </svg>
        <input
            type="text"
            x-model="search"
            placeholder="Search PDF files..."
            class="w-full pl-10 pr-9 py-2.5 rounded-xl border-0 bg-gray-100 dark:bg-white/5 text-sm text-gray-800 dark:text-gray-100 placeholder-gray-400 outline-none ring-1 ring-transparent focus:bg-white dark:focus:bg-[#161617] focus:ring-2 focus:ring-[#FF6B35]/40 transition-all"
        >
        <button type="button" x-show="search !== ''" x-cloak @click="search = ''"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 6 6 18M6 6l12 12"/>
            </svg>
        </button>
    </div>
@endsection

@section('content')

<!-- Main Content Section -->
<section class="px-4 sm:px-6 pt-1 pb-6 bg-gray-50 dark:bg-black">
    <div class="max-w-7xl mx-auto">

        <!-- Tabs Navigation -->
        <div class="mb-8" x-data="{ activeType: 'all', tabsOpen: false, viewingPdfUrl: null, viewingPdfTitle: '' }">

            @php
                $typeTabs = [
                    'all'      => 'All Files',
                    'chords'   => 'Chords',
                    'scales'   => 'Scales',
                    'exercise' => 'Exercise',
                    'handouts' => 'Handouts',
                ];
            @endphp

            <!-- Mobile: Dropdown accordion -->
            <div class="sm:hidden relative mb-8" @click.outside="tabsOpen = false">
                <button type="button" @click="tabsOpen = !tabsOpen"
                    class="w-full flex items-center justify-between px-6 py-3 rounded-lg font-semibold text-sm bg-[#FF6B35] text-white shadow-sm transition-all duration-200">
                    <span x-text="({{ \Illuminate\Support\Js::from($typeTabs) }})[activeType]"></span>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="tabsOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="tabsOpen" x-transition x-cloak
                    class="absolute left-0 right-0 mt-2 rounded-xl bg-white dark:bg-[#161617] border border-gray-100 dark:border-white/10 shadow-xl overflow-hidden z-20">
                    @foreach ($typeTabs as $key => $label)
                        <button type="button" @click="activeType = '{{ $key }}'; tabsOpen = false"
                            class="block w-full text-left px-6 py-3 font-semibold text-sm transition-colors duration-150"
                            :class="activeType === '{{ $key }}' ? 'bg-[#FF6B35] text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5'">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Desktop: Pill row -->
            <div class="hidden sm:flex gap-4 mb-8">
                @foreach ($typeTabs as $key => $label)
                    <button
                        type="button"
                        @click="activeType = '{{ $key }}'"
                        :class="activeType === '{{ $key }}' ? 'bg-[#FF6B35] text-white' : 'bg-white dark:bg-[#161617] text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/10'"
                        class="flex-1 px-8 py-3 rounded-lg font-semibold text-sm sm:text-base transition-all duration-200 shadow-sm"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <!-- Tab Content -->
            <div class="min-h-[500px]">

                <!-- Beginners Content -->
                <div  x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                @if($pdfList->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($pdfList as $pdf)
                        <div
                            x-show="(activeType === 'all' || activeType === '{{ $pdf->type }}') && (search === '' || {{ \Illuminate\Support\Js::from(Str::lower($pdf->title)) }}.includes(search.toLowerCase()))"
                            class="group bg-white dark:bg-[#161617] rounded-[28px] border border-gray-200/70 dark:border-white/10 shadow-sm overflow-hidden hover:-translate-y-1 hover:shadow-2xl transition-all duration-300">
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
                                </div>
                                
                                <div class="grid grid-cols-2 gap-3 mt-auto">
                                    <button type="button"
                                        @click="viewingPdfUrl = {{ \Illuminate\Support\Js::from(route('community.pdf-view', $pdf)) }}; viewingPdfTitle = {{ \Illuminate\Support\Js::from($pdf->title) }}"
                                        class="flex items-center justify-center gap-2 rounded-2xl border border-gray-300 dark:border-white/10 bg-gray-100/90 dark:bg-white/5 px-4 py-3 text-sm font-semibold text-gray-800 dark:text-gray-100 transition-all duration-200 hover:border-gray-400 hover:bg-white dark:hover:bg-white/10">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                        View
                                    </button>
                                    <a href="{{ route('community.pdf-view', $pdf) }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center gap-2 rounded-2xl bg-[#FF6B35] px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-orange-500/20 transition-all duration-200 hover:-translate-y-0.5 hover:bg-[#E55A2B]">
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

            <!-- In-page PDF viewer modal -->
            <div x-show="viewingPdfUrl" x-cloak x-transition.opacity
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70"
                @keydown.escape.window="viewingPdfUrl = null">
                <div @click.outside="viewingPdfUrl = null"
                    class="relative w-full max-w-4xl h-[85vh] bg-white dark:bg-[#161617] rounded-2xl shadow-2xl overflow-hidden flex flex-col">
                    <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 dark:border-white/10">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white truncate" x-text="viewingPdfTitle"></h3>
                        <button type="button" @click="viewingPdfUrl = null"
                            class="text-gray-400 hover:text-gray-700 dark:hover:text-white transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>
                        </button>
                    </div>
                    <iframe :src="viewingPdfUrl" class="flex-1 w-full" title="PDF preview"></iframe>
                </div>
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
