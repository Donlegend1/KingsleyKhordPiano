@extends('layouts.community')

@section('breadcrumb-parent', 'Overview')
@section('breadcrumb-parent-url', '/member/my-library')
@section('breadcrumb', 'Bookmark')

@section('content')
<section class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white py-10 px-6 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- Page Title -->
        <div class="flex items-center gap-2.5 mb-8">
            <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-gray-50 dark:bg-white/5 text-gray-500 dark:text-gray-400">
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
                    'App\Models\Upload' => $bookmark->bookmarkable?->category ? \Illuminate\Support\Str::title($bookmark->bookmarkable->category) : 'Lesson',
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

                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-5 flex flex-col hover:shadow-sm transition-shadow">

                        <div class="flex items-center justify-between mb-4">
                            <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-gray-50 dark:bg-white/5 text-gray-500 dark:text-gray-400 flex-shrink-0">
                                @if($isPost)
                                    <i class="fas fa-comment-dots text-sm"></i>
                                @else
                                    <i class="fas fa-play text-sm"></i>
                                @endif
                            </span>
                            <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wide">
                                {{ $categoryLabel }}
                            </span>
                        </div>

                        <h3 class="text-[15px] font-bold text-gray-900 dark:text-white mb-5 line-clamp-2 flex-1">{{ $title }}</h3>

                        <a href="{{ $url }}"
                            class="flex items-center justify-center gap-2 w-full py-2.5 rounded-lg bg-gray-900 hover:bg-black text-white text-sm font-semibold transition-colors">
                            <i class="fas fa-play text-[10px]"></i>
                            View
                        </a>
                    </div>
                @endforeach

            </div>
        @endif
    </div>
</section>
@endsection
