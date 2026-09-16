    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">

        <div class="lg:col-span-3">
            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm p-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $category['title'] }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ $category['description'] }}</p>
            </div>

            @if(!empty($category['video_url']))
                <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden mt-4">
                    <div class="px-4 py-2.5 border-b border-gray-100 dark:border-gray-700 flex items-center gap-1.5">
                        <span title="Pinned">📌</span>
                        <h3 class="text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Challenge Video</h3>
                    </div>
                    <div class="aspect-video">
                        <iframe
                            class="w-full h-full"
                            src="{{ \App\Helpers\VideoHelper::linkToEmbed($category['video_url']) }}"
                            title="{{ $category['title'] }} — Challenge Video"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                        ></iframe>
                    </div>
                </div>
            @endif

            <div class="bg-blue-50 dark:bg-blue-500/10 border border-blue-100 dark:border-blue-500/20 rounded-xl p-4 mt-4" x-data="{ showVideo: false }">
                <p class="text-sm text-blue-900 dark:text-blue-200 leading-relaxed">
                    📹 Hey! Before you post your video, upload it to YouTube first, then just paste the link right into your post. Need a hand?
                    <button type="button" @click="showVideo = true" class="font-semibold underline hover:no-underline">View here</button>.
                </p>

                <div x-show="showVideo" x-cloak class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" @click.self="showVideo = false">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden">
                        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h2 class="text-base font-bold text-gray-900 dark:text-white">How to Submit a Video</h2>
                            <button type="button" @click="showVideo = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="aspect-video">
                            <template x-if="showVideo">
                                <iframe
                                    class="w-full h-full"
                                    src="https://www.youtube.com/embed/ocg1B9uYuEs?start=468"
                                    title="How to upload your video to YouTube and share the link"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen
                                ></iframe>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <div id="post-composer" data-subcategory="{{ $category['subcategory'] }}" data-parent-post-id="{{ $category['parent_post_id'] ?? '' }}" class="mt-4"></div>

            @unless($hideTopicsList ?? false)
                <div id="category-post-feed" data-subcategory="{{ $category['subcategory'] }}" data-parent-post-id="{{ $category['parent_post_id'] ?? '' }}" class="mt-4"></div>
            @endunless
        </div>

        <div class="space-y-4">
            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden">
                <div class="grid grid-cols-2 divide-x divide-gray-100 dark:divide-gray-700 bg-gray-50 dark:bg-gray-900/40 px-4 py-4 text-center">
                    <div>
                        <p class="text-[11px] uppercase tracking-wide text-gray-400">Posts</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white mt-0.5">{{ $category['count'] }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] uppercase tracking-wide text-gray-400">Last Reply</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white mt-0.5">{{ $categoryStats['last_reply_label'] }}</p>
                    </div>
                </div>

                @if($categoryStats['top_posters']->isNotEmpty())
                    <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                        <h3 class="text-[11px] font-bold uppercase tracking-wide text-gray-400 mb-3">Top Posters in this Topic</h3>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach($categoryStats['top_posters'] as $poster)
                                <div class="flex items-center gap-2 min-w-0">
                                    @if($poster['avatar'])
                                        <img src="{{ asset($poster['avatar']) }}" alt="{{ $poster['name'] }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-xs font-bold text-indigo-600 dark:text-indigo-400 flex-shrink-0">
                                            {{ strtoupper(substr($poster['name'], 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 truncate">{{ $poster['name'] }}</p>
                                        <p class="text-xs text-gray-400">{{ $poster['posts_count'] }} {{ Str::plural('post', $poster['posts_count']) }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($categoryStats['popular_post'])
                    <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                        <h3 class="text-[11px] font-bold uppercase tracking-wide text-gray-400 mb-3">Popular Posts</h3>
                        <a href="{{ $categoryStats['popular_post']['url'] }}" class="flex items-start gap-2 -m-1 p-1 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                            @if($categoryStats['popular_post']['avatar'])
                                <img src="{{ asset($categoryStats['popular_post']['avatar']) }}" alt="{{ $categoryStats['popular_post']['author'] }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                            @else
                                <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-xs font-bold text-indigo-600 dark:text-indigo-400 flex-shrink-0">
                                    {{ strtoupper(substr($categoryStats['popular_post']['author'], 0, 1)) }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $categoryStats['popular_post']['author'] }}</p>
                                <p class="text-xs text-gray-400">{{ $categoryStats['popular_post']['date'] }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1 truncate">{{ $categoryStats['popular_post']['title'] }}</p>
                            </div>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
