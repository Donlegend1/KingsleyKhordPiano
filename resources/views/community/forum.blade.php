@extends("layouts.hub")

@section("title", "Forum")

@section("breadcrumbs")
    @include('community.partials.breadcrumbs', ['items' => [['label' => 'Forums']]])
@endsection

@section("content")
@php
    $contributorName = static function ($member) {
        $name = trim(($member->display_name ?? '') ?: (($member->first_name ?? '') . ' ' . ($member->last_name ?? '')));
        if ($name !== '') {
            return $name;
        }
        return $member->user_name ? ltrim($member->user_name, '@') : 'Community member';
    };

    // A distinct icon (Heroicons outline path) per forum/community category,
    // instead of reusing the same chat-bubble icon for all of them.
    $categoryIcons = [
        'beginner_guided_practice' => 'M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25',
        'intermediate_guided_practice' => 'M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25',
        'advanced_guided_practice' => 'M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25',
        'student_challenges' => 'M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0',
        'progress_report' => 'M2.25 18L9 11.25l4.306 4.306a11.95 11.95 0 015.814-5.518l2.74-1.22m0 0l-5.94-2.281m5.94 2.28l-2.28 5.941',
        'public_pledges' => 'M3 3v1.5M3 21v-6m0 0l2.77-.693a9 9 0 016.208.682l.108.054a9 9 0 006.086.71l3.114-.732a48.524 48.524 0 01-.005-10.499l-3.11.732a9 9 0 01-6.085-.711l-.108-.054a9 9 0 00-6.208-.682L3 4.5M3 15V4.5',
        'workspace_showcase' => 'M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.174C3.05 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.8-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.174 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316zM16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z',
    ];
    $defaultIcon = 'M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z';
@endphp
<div class="p-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        <div class="lg:col-span-2">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">Forums</h1>
            </div>

            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-t-sm shadow-sm overflow-hidden">
                <div class="bg-slate-500 dark:bg-slate-700 px-6 py-4">
                    <h2 class="text-base font-bold text-white">Forums</h2>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($forumCategories as $category)
                        <a
                            href="{{ route('community.forum.category', $category['subcategory']) }}"
                            class="flex items-center gap-3 sm:gap-4 px-4 sm:px-6 py-5 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors"
                        >
                            <span class="hidden sm:flex w-11 h-11 rounded-full bg-slate-600 text-white items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.75" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="{{ $categoryIcons[$category['subcategory']] ?? $defaultIcon }}"/>
                                </svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-base font-bold text-blue-800 dark:text-blue-400">{{ $category['title'] }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $category['description'] }}</p>
                            </div>
                            <div class="text-center flex-shrink-0">
                                <p class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($category['count']) }}</p>
                                <p class="text-xs text-gray-400">posts</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-t-sm shadow-sm overflow-hidden mt-6">
                <div class="bg-slate-500 dark:bg-slate-700 px-6 py-4">
                    <h2 class="text-base font-bold text-white">Community</h2>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($communityCategories as $category)
                        <a
                            href="{{ route('community.forum.category', $category['subcategory']) }}"
                            class="flex items-center gap-3 sm:gap-4 px-4 sm:px-6 py-5 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors"
                        >
                            <span class="hidden sm:flex w-11 h-11 rounded-full bg-slate-600 text-white items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.75" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="{{ $categoryIcons[$category['subcategory']] ?? $defaultIcon }}"/>
                                </svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-base font-bold text-blue-800 dark:text-blue-400">{{ $category['title'] }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $category['description'] }}</p>
                            </div>
                            <div class="text-center flex-shrink-0">
                                <p class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($category['count']) }}</p>
                                <p class="text-xs text-gray-400">posts</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        @include('community.partials.sidebar', ['hideCommunityStats' => true, 'hideRecentMembers' => true])
    </div>
</div>
@endsection
