@extends('layouts.community')

@section('breadcrumb-parent', 'Overview')
@section('breadcrumb-parent-url', '/member/my-library')
@section('breadcrumb', 'Bookmark')

@section('content')
<section class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white py-10 px-6 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- Page Title -->
        <div class="flex items-center gap-2.5 mb-8">
            <div class="flex items-center justify-center w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                <i class="fas fa-bookmark text-sm"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">My Bookmarks</h2>
        </div>

        @if($bookmarks->isEmpty())
            <div class="flex flex-col items-center justify-center text-center py-16 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700">
                <div class="w-14 h-14 rounded-full bg-gray-50 dark:bg-gray-700 flex items-center justify-center mb-4">
                    <i class="fas fa-folder-open text-gray-300 dark:text-gray-500 text-xl"></i>
                </div>
                <p class="text-gray-500 dark:text-gray-400">You haven't bookmarked anything yet.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($bookmarks as $bookmark)
                @php
                $item = $bookmark->bookmarkable;
                $isPost = $bookmark->bookmarkable_type === 'App\Models\Post';

                $url = match ($bookmark->bookmarkable_type) {
                    'App\Models\Course' =>
                        '/member/course/' . $item?->level
                        . '?selected_course=' . $item?->id,

                    'App\Models\Upload' =>
                        '/member/lesson/' . $item?->id . '?type=upload',

                    'App\Models\LearnSong' =>
                        '/member/lesson/' . $item?->id . '?type=learn_song',

                    'App\Models\ExtraCourse' =>
                        '/member/lesson/' . $item?->id . '?type=extra_course',

                    'App\Models\Post' =>
                        '/member/post/' . $item?->id,

                    default => '#',
                };

                $categoryLabel = match ($bookmark->bookmarkable_type) {
                    'App\Models\Course' => 'Roadmap',
                    'App\Models\Upload' => 'Quick Lesson',
                    'App\Models\LearnSong' => 'Learn Song',
                    'App\Models\ExtraCourse' => 'Extra Course',
                    'App\Models\Post' => 'Post',
                    default => 'Bookmark',
                };

                if ($isPost) {
                    $author = $item?->user;
                    $authorName = trim(($author?->first_name ?? '') . ' ' . ($author?->last_name ?? '')) ?: 'Member';
                    $title = $authorName . "'s Post";
                } else {
                    $title = $item?->title ?? 'Untitled';
                }
            @endphp

                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 flex flex-col shadow-sm hover:shadow-md transition-shadow">

                        <div class="flex items-center justify-between mb-5">
                            <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-500 dark:text-indigo-400 flex-shrink-0">
                                @if($isPost)
                                    <i class="fas fa-comment-dots text-lg"></i>
                                @else
                                    <i class="fas fa-play text-base"></i>
                                @endif
                            </div>
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full border border-indigo-200 dark:border-indigo-500/30 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 text-xs font-bold uppercase tracking-wide">
                                {{ $categoryLabel }}
                            </span>
                        </div>

                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 line-clamp-2 flex-1">{{ $title }}</h3>

                        <a href="{{ $url }}"
                            class="flex items-center justify-center gap-2.5 w-full py-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold uppercase tracking-wide transition-colors shadow-sm">
                            <span class="w-5 h-5 rounded-full bg-white/25 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-play text-[9px] ml-0.5"></i>
                            </span>
                            View
                        </a>
                    </div>
                @endforeach

            </div>
        @endif
    </div>
</section>
@endsection
