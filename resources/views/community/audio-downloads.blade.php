@extends('layouts.community')

@section('breadcrumb-parent', 'Overview')
@section('breadcrumb-parent-url', '/member/my-library')
@section('breadcrumb', 'Audio Files')

@section('page-search')
    <div class="relative group">
        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-[#FF6B35] transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 1 0 0-16 8 8 0 0 0 0 16Z"/>
        </svg>
        <input
            type="text"
            x-model="search"
            placeholder="Search audio files..."
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

@php
    $mimeFor = function ($path) {
        return match (strtolower(pathinfo($path ?? '', PATHINFO_EXTENSION))) {
            'wav' => 'audio/wav',
            'ogg' => 'audio/ogg',
            'm4a' => 'audio/mp4',
            default => 'audio/mpeg',
        };
    };

    $mapTrack = function ($audio) use ($mimeFor) {
        return [
            'id' => $audio->id,
            'title' => $audio->title,
            'artist' => $audio->artist,
            'collection' => $audio->category === 'piano_plays' ? 'Piano Plays' : 'Track & Loops',
            'category' => $audio->category,
            'duration' => $audio->duration,
            'src' => asset($audio->audio_file),
            'mime' => $mimeFor($audio->audio_file),
            'downloadUrl' => "/member/community/space/audio/downloads/{$audio->id}",
        ];
    };

    $allAudio = $pianoPlays->concat($tracksAndLoops)->sortByDesc('created_at')->values();
@endphp

<section
    x-data="audioPlayerPage({
        all: {{ \Illuminate\Support\Js::from($allAudio->map($mapTrack)->values()) }},
        pianoPlays: {{ \Illuminate\Support\Js::from($pianoPlays->map($mapTrack)->values()) }},
        tracksAndLoops: {{ \Illuminate\Support\Js::from($tracksAndLoops->map($mapTrack)->values()) }},
    })"
    class="px-4 sm:px-6 pt-1 bg-gray-50 dark:bg-black min-h-screen"
    :class="currentTrack ? 'pb-32' : 'pb-6'"
>
    <div class="max-w-7xl mx-auto">

        <!-- Tabs -->
        @php
            $audioTabs = [
                'all' => 'All Files',
                'piano_plays' => 'Piano Plays',
                'tracks_loops' => 'Track & Loops',
            ];
        @endphp
        <div class="mb-6" x-data="{ tabsOpen: false }">

            <!-- Mobile: Dropdown accordion -->
            <div class="sm:hidden relative" @click.outside="tabsOpen = false">
                <button type="button" @click="tabsOpen = !tabsOpen"
                    class="w-full flex items-center justify-between px-6 py-3 rounded-lg font-semibold text-sm bg-[#FF6B35] text-white shadow-sm transition-all duration-200">
                    <span x-text="({ all: 'All Files', piano_plays: 'Piano Plays', tracks_loops: 'Track & Loops' })[activeTab]"></span>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="tabsOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="tabsOpen" x-transition x-cloak
                    class="absolute left-0 right-0 mt-2 rounded-xl bg-white dark:bg-[#161617] border border-gray-100 dark:border-white/10 shadow-xl overflow-hidden z-20">
                    @foreach ($audioTabs as $key => $label)
                        <button type="button" @click="activeTab = '{{ $key }}'; tabsOpen = false"
                            class="block w-full text-left px-6 py-3 font-semibold text-sm transition-colors duration-150"
                            :class="activeTab === '{{ $key }}' ? 'bg-[#FF6B35] text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5'">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Desktop: Pill row -->
            <div class="hidden sm:flex gap-4">
                @foreach ($audioTabs as $key => $label)
                    <button type="button" @click="activeTab = '{{ $key }}'"
                        :class="activeTab === '{{ $key }}' ? 'bg-[#FF6B35] text-white' : 'bg-white dark:bg-[#161617] text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/10'"
                        class="flex-1 px-8 py-3 rounded-lg font-semibold text-sm sm:text-base transition-all duration-200 shadow-sm">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white dark:bg-[#161617] rounded-2xl border border-gray-100 dark:border-white/10 overflow-hidden shadow-sm overflow-x-auto"
            x-show="activeTabTracks.length > 0">
            <table class="w-full text-left border-collapse sm:min-w-[560px]">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-white/10 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                        <th class="px-3 sm:px-4 py-3 w-10 sm:w-12">#</th>
                        <th class="px-3 sm:px-4 py-3">Title</th>
                        <th class="px-4 py-3 hidden sm:table-cell">Artist</th>
                        <th class="px-4 py-3 hidden md:table-cell">Album/Collection</th>
                        <th class="px-3 sm:px-4 py-3 text-right w-16 sm:w-20">Duration</th>
                        <th class="px-2 sm:px-4 py-3 w-8 sm:w-10"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(track, index) in activeTabTracks" :key="track.id">
                        <tr
                            x-show="search === '' || track.title.toLowerCase().includes(search.toLowerCase())"
                            @click="playTrack(track, index)"
                            class="border-b border-gray-50 dark:border-white/5 last:border-b-0 cursor-pointer transition-colors"
                            :class="currentTrack && currentTrack.id === track.id ? 'bg-orange-50 dark:bg-orange-500/10' : 'hover:bg-gray-50 dark:hover:bg-white/5'"
                        >
                            <td class="px-3 sm:px-4 py-3.5 sm:py-3 text-sm" :class="currentTrack && currentTrack.id === track.id ? 'text-[#FF6B35]' : 'text-gray-400'">
                                <svg x-show="currentTrack && currentTrack.id === track.id && isPlaying" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                                <span x-show="!(currentTrack && currentTrack.id === track.id && isPlaying)" x-text="index + 1"></span>
                            </td>
                            <td class="px-3 sm:px-4 py-3.5 sm:py-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 sm:w-9 sm:h-9 rounded-md bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-white/80" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18V6l12-2v12M9 18a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm12-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 line-clamp-2 sm:truncate" x-text="track.title"></p>
                                        <p class="text-xs text-gray-400 truncate sm:hidden" x-text="track.artist || ''"></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 hidden sm:table-cell" x-text="track.artist || '—'"></td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 hidden md:table-cell" x-text="track.collection || '—'"></td>
                            <td class="px-3 sm:px-4 py-3.5 sm:py-3 text-sm text-gray-400 text-right whitespace-nowrap" x-text="track.duration || '—'"></td>
                            <td class="px-2 sm:px-4 py-3.5 sm:py-3 relative" @click.stop x-data="{ open: false }" @click.outside="open = false">
                                <button type="button" @click="open = !open" class="text-gray-400 hover:text-gray-700 dark:hover:text-white p-2 -m-2" aria-label="More options">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 6a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 6a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"/></svg>
                                </button>
                                <div x-show="open" x-cloak x-transition
                                    class="absolute right-2 sm:right-4 top-10 w-40 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden z-50">
                                    <a :href="track.downloadUrl" @click="open = false"
                                        class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/60">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 13v8l-4-4m4 4 4-4M4.393 15.269A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.436 8.284"/></svg>
                                        Download
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div x-show="activeTabTracks.length === 0" class="text-center py-12">
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-gray-400 dark:text-gray-600 mb-4"><path d="M9 19V5m0 0a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2z"/></svg>
            <h3 class="text-lg font-semibold text-gray-600 dark:text-gray-300">No Audio Available</h3>
            <p class="text-gray-500 dark:text-gray-400 mt-2">Check back soon for audio resources</p>
        </div>
    </div>

    <!-- Persistent Bottom Player -->
    <div x-show="currentTrack" x-cloak x-transition
        class="fixed bottom-0 left-0 right-0 bg-white dark:bg-[#161617] border-t border-gray-200 dark:border-white/10 shadow-[0_-4px_20px_rgba(0,0,0,0.06)] z-40">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 py-2.5 sm:py-3 flex items-center gap-2 sm:gap-4">

            <!-- Track info -->
            <div class="flex items-center gap-2 sm:gap-3 w-24 sm:w-64 min-w-0 flex-shrink-0">
                <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-lg bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white/80" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18V6l12-2v12M9 18a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm12-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate" x-text="currentTrack?.title"></p>
                    <p class="text-xs text-gray-400 truncate" x-text="currentTrack?.artist || ''"></p>
                </div>
            </div>

            <!-- Controls -->
            <div class="flex-1 flex flex-col items-center gap-1 min-w-0">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="relative group">
                        <button type="button" @click="prevTrack" class="text-gray-500 hover:text-gray-800 dark:hover:text-white" aria-label="Previous">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 6h2v12H6zm3.5 6l8.5 6V6z"/></svg>
                        </button>
                        <span class="pointer-events-none absolute bottom-full left-1/2 -translate-x-1/2 mb-2 whitespace-nowrap rounded-md bg-gray-900 dark:bg-black px-2 py-1 text-[11px] font-medium text-white opacity-0 scale-95 transition-all duration-150 group-hover:opacity-100 group-hover:scale-100">Previous</span>
                    </div>

                    <div class="relative group">
                        <button type="button" @click="togglePlay" class="w-9 h-9 rounded-full bg-[#FF6B35] hover:bg-[#E55A2B] text-white flex items-center justify-center transition-colors" aria-label="Play/Pause">
                            <svg x-show="!isPlaying" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            <svg x-show="isPlaying" x-cloak class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                        </button>
                        <span class="pointer-events-none absolute bottom-full left-1/2 -translate-x-1/2 mb-2 whitespace-nowrap rounded-md bg-gray-900 dark:bg-black px-2 py-1 text-[11px] font-medium text-white opacity-0 scale-95 transition-all duration-150 group-hover:opacity-100 group-hover:scale-100" x-text="isPlaying ? 'Pause' : 'Play'"></span>
                    </div>

                    <div class="relative group">
                        <button type="button" @click="nextTrack" class="text-gray-500 hover:text-gray-800 dark:hover:text-white" aria-label="Next">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M16 18h2V6h-2zM6 18l8.5-6L6 6z"/></svg>
                        </button>
                        <span class="pointer-events-none absolute bottom-full left-1/2 -translate-x-1/2 mb-2 whitespace-nowrap rounded-md bg-gray-900 dark:bg-black px-2 py-1 text-[11px] font-medium text-white opacity-0 scale-95 transition-all duration-150 group-hover:opacity-100 group-hover:scale-100">Next</span>
                    </div>

                    <!-- Single button cycling: Order → Shuffle All → Repeat All → Repeat One -->
                    <div class="relative group">
                        <button type="button" @click="cyclePlayMode" :class="playMode !== 'order' ? 'text-[#FF6B35]' : 'text-gray-400 hover:text-gray-500'" class="relative" aria-label="Playback mode">
                            <!-- Order (sequential) -->
                            <svg x-show="playMode === 'order'" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h13M8 12h13M8 17h13M3 7h.01M3 12h.01M3 17h.01"/>
                            </svg>
                            <!-- Shuffle All -->
                            <svg x-show="playMode === 'shuffle'" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 3h5v5m0-5-6.5 6.5M4 20 14.5 9.5M21 16v5h-5m5 0-6.5-6.5M4 4l5 5"/>
                            </svg>
                            <!-- Repeat All / Repeat One share the loop icon -->
                            <svg x-show="playMode === 'repeat_all' || playMode === 'repeat_one'" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 1l4 4-4 4M3 11V9a4 4 0 0 1 4-4h14M7 23l-4-4 4-4M21 13v2a4 4 0 0 1-4 4H3"/>
                            </svg>
                            <span x-show="playMode === 'repeat_one'" x-cloak class="absolute -top-1.5 -right-1.5 text-[8px] font-bold bg-[#FF6B35] text-white rounded-full w-3.5 h-3.5 flex items-center justify-center leading-none">1</span>
                        </button>
                        <span class="pointer-events-none absolute bottom-full left-1/2 -translate-x-1/2 mb-2 whitespace-nowrap rounded-md bg-gray-900 dark:bg-black px-2 py-1 text-[11px] font-medium text-white opacity-0 scale-95 transition-all duration-150 group-hover:opacity-100 group-hover:scale-100"
                            x-text="playMode === 'order' ? 'Order' : playMode === 'shuffle' ? 'Shuffle All' : playMode === 'repeat_all' ? 'Loop All' : 'Repeat One'"></span>
                    </div>
                </div>

                <div class="w-full flex items-center gap-2">
                    <span class="text-[11px] text-gray-400 w-9 text-right" x-text="formatTime(currentTime)"></span>
                    <input type="range" min="0" :max="duration || 0" step="0.1" x-model.number="currentTime" @input="seek" class="flex-1 h-1 accent-[#FF6B35]">
                    <span class="text-[11px] text-gray-400 w-9" x-text="formatTime(duration)"></span>
                </div>
            </div>

            <!-- Volume -->
            <div class="hidden md:flex items-center gap-2 w-28 flex-shrink-0">
                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M3 10v4h4l5 5V5L7 10H3z"/></svg>
                <input type="range" min="0" max="1" step="0.01" x-model.number="volume" @input="setVolume" class="flex-1 h-1 accent-[#FF6B35]">
            </div>

            <audio x-ref="player" @timeupdate="onTimeUpdate" @loadedmetadata="onLoadedMetadata" @ended="onEnded"></audio>
        </div>
    </div>
</section>

<script>
    function audioPlayerPage(data) {
        return {
            all: data.all,
            pianoPlays: data.pianoPlays,
            tracksAndLoops: data.tracksAndLoops,
            activeTab: 'piano_plays',
            currentTrack: null,
            currentIndex: -1,
            isPlaying: false,
            currentTime: 0,
            duration: 0,
            volume: 1,
            playMode: 'order', // 'order' | 'shuffle' | 'repeat_all' | 'repeat_one'

            get activeTabTracks() {
                if (this.activeTab === 'piano_plays') return this.pianoPlays;
                if (this.activeTab === 'tracks_loops') return this.tracksAndLoops;
                return this.all;
            },

            playTrack(track, index) {
                if (this.currentTrack && this.currentTrack.id === track.id) {
                    this.togglePlay();
                    return;
                }
                this.currentTrack = track;
                this.currentIndex = index;
                this.$nextTick(() => {
                    const el = this.$refs.player;
                    el.src = track.src;
                    el.play();
                    this.isPlaying = true;
                });
            },

            togglePlay() {
                const el = this.$refs.player;
                if (!el || !this.currentTrack) return;
                if (this.isPlaying) {
                    el.pause();
                    this.isPlaying = false;
                } else {
                    el.play();
                    this.isPlaying = true;
                }
            },

            randomIndex(list) {
                if (list.length <= 1) return 0;
                let idx;
                do { idx = Math.floor(Math.random() * list.length); } while (idx === this.currentIndex);
                return idx;
            },

            prevTrack() {
                const list = this.activeTabTracks;
                if (!list.length) return;
                if (this.playMode === 'shuffle') {
                    const idx = this.randomIndex(list);
                    this.playTrack(list[idx], idx);
                    return;
                }
                const idx = this.currentIndex > 0 ? this.currentIndex - 1 : list.length - 1;
                this.playTrack(list[idx], idx);
            },

            nextTrack() {
                const list = this.activeTabTracks;
                if (!list.length) return;
                if (this.playMode === 'shuffle') {
                    const idx = this.randomIndex(list);
                    this.playTrack(list[idx], idx);
                    return;
                }
                const idx = this.currentIndex < list.length - 1 ? this.currentIndex + 1 : 0;
                this.playTrack(list[idx], idx);
            },

            cyclePlayMode() {
                const order = ['order', 'shuffle', 'repeat_all', 'repeat_one'];
                this.playMode = order[(order.indexOf(this.playMode) + 1) % order.length];
            },

            onTimeUpdate(e) {
                this.currentTime = e.target.currentTime;
            },

            onLoadedMetadata(e) {
                this.duration = e.target.duration;
            },

            onEnded() {
                if (this.playMode === 'repeat_one') {
                    this.$refs.player.currentTime = 0;
                    this.$refs.player.play();
                    return;
                }
                const list = this.activeTabTracks;
                const isLast = this.playMode !== 'shuffle' && this.currentIndex >= list.length - 1;
                if (isLast && this.playMode !== 'repeat_all') {
                    this.isPlaying = false;
                    return;
                }
                this.nextTrack();
            },

            seek() {
                if (this.$refs.player) {
                    this.$refs.player.currentTime = this.currentTime;
                }
            },

            setVolume() {
                if (this.$refs.player) {
                    this.$refs.player.volume = this.volume;
                }
            },

            formatTime(sec) {
                if (!sec || isNaN(sec)) return '0:00';
                const m = Math.floor(sec / 60);
                const s = Math.floor(sec % 60).toString().padStart(2, '0');
                return `${m}:${s}`;
            },
        };
    }
</script>

@endsection
