@extends('layouts.member')

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

                if ($isPost) {
                    $author = $item?->user;
                    $authorName = trim(($author?->first_name ?? '') . ' ' . ($author?->last_name ?? '')) ?: 'Member';
                    $imageBlock = $item?->media?->firstWhere('type', 'image')
                        ?? $item?->blocks?->firstWhere('type', 'image');
                    $postImage = $imageBlock
                        ? asset($imageBlock->file_path ?? $imageBlock->content)
                        : null;
                    $textBlock = $item?->blocks?->firstWhere('type', 'text');
                    $excerpt = $textBlock ? \Illuminate\Support\Str::limit($textBlock->content, 90) : null;
                } else {
                    $lessonThumb = $item?->thumbnail_url ?? ($item?->thumbnail ? asset($item->thumbnail) : null);
                }
            @endphp

                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow">

                        @if($isPost)
                            <!-- Post Preview -->
                            <a href="{{ $url }}" class="block">
                                @if($postImage)
                                    <img src="{{ $postImage }}" alt="Post by {{ $authorName }}" class="w-full h-36 object-cover">
                                @else
                                    <div class="w-full h-36 bg-gray-50 dark:bg-gray-900 flex flex-col items-center justify-center px-6 text-center">
                                        <div class="w-10 h-10 rounded-full bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-semibold text-sm mb-2">
                                            {{ strtoupper(substr($authorName, 0, 1)) }}
                                        </div>
                                        @if($excerpt)
                                            <p class="text-xs text-gray-400 dark:text-gray-500 line-clamp-2">{{ $excerpt }}</p>
                                        @endif
                                    </div>
                                @endif
                            </a>

                            <div class="p-4 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-[11px] font-semibold text-indigo-500 uppercase tracking-wide mb-0.5">Post</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $authorName }}</p>
                                </div>
                                <a href="{{ $url }}"
                                    class="flex-shrink-0 text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 transition">
                                    View →
                                </a>
                            </div>
                        @else
                            <!-- Lesson Preview -->
                            <a href="{{ $url }}" class="block">
                                @if($lessonThumb)
                                    <img src="{{ $lessonThumb }}"
                                        alt="{{ $item?->title ?? 'Bookmarked item' }}"
                                        class="w-full h-36 object-cover">
                                @else
                                    <div class="w-full h-36 bg-gray-50 dark:bg-gray-900 flex items-center justify-center">
                                        <div class="w-10 h-10 rounded-full bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-500 dark:text-blue-400">
                                            <i class="fas fa-play text-xs"></i>
                                        </div>
                                    </div>
                                @endif
                            </a>

                            <div class="p-4 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-[11px] font-semibold text-blue-500 uppercase tracking-wide mb-0.5">Lesson</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $item?->title }}</p>
                                </div>
                                <a href="{{ $url }}"
                                    class="flex-shrink-0 text-xs font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 transition">
                                    View →
                                </a>
                            </div>
                        @endif
                    </div>
                @endforeach

            </div>
        @endif
    </div>
</section>
@endsection
