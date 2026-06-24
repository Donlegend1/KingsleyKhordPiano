@extends('layouts.member')

@section('content')
<section class="bg-gradient-to-r from-blue-50 via-purple-50 to-pink-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 text-gray-900 dark:text-white py-10 px-6">
    <div class="max-w-7xl mx-auto">
        <!-- Page Title -->
        <div class="flex items-center justify-center mb-8 space-x-2">
            <i class="fas fa-bookmark text-gray-800 text-xl"></i>
            <h2 class="text-xl font-extrabold text-gray-800 dark:text-white">My Bookmarks</h2>
        </div>

        @if($bookmarks->isEmpty())
            <div class="flex flex-col items-center justify-center text-center py-12 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <i class="fas fa-folder-open text-gray-400 text-5xl mb-4"></i>
                <p class="text-gray-600 dark:text-gray-300 text-lg">You haven’t bookmarked any videos yet.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($bookmarks as $bookmark)
                @php
                $item = $bookmark->bookmarkable;
                $isPost = $bookmark->bookmarkable_type === 'App\Models\Post';

                $url = match ($bookmark->bookmarkable_type) {
                    'App\Models\Course' =>
                        '/member/course/' . $item?->level
                        . '?selected_course=' . $item?->id,

                    'App\Models\Upload',
                    'App\Models\LearnSong',
                    'App\Models\ExtraCourse' =>
                        '/member/lesson/' . $item?->id,

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
                }
            @endphp

                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition transform hover:-translate-y-1">

                        @if($isPost)
                            <!-- Post Preview -->
                            <a href="{{ $url }}" class="block">
                                @if($postImage)
                                    <img src="{{ $postImage }}" alt="Post by {{ $authorName }}" class="w-full h-44 object-cover">
                                @else
                                    <div class="w-full h-44 bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-gray-700 dark:to-gray-900 flex flex-col items-center justify-center px-6 text-center">
                                        <div class="w-12 h-12 rounded-full bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center text-indigo-600 dark:text-indigo-300 font-bold text-lg mb-3">
                                            {{ strtoupper(substr($authorName, 0, 1)) }}
                                        </div>
                                        @if($excerpt)
                                            <p class="text-sm text-gray-600 dark:text-gray-300 italic line-clamp-3">"{{ $excerpt }}"</p>
                                        @else
                                            <p class="text-sm text-gray-400 dark:text-gray-500">Community post</p>
                                        @endif
                                    </div>
                                @endif
                            </a>

                            <div class="p-5 text-center space-y-3">
                                <h3 class="text-sm font-bold text-gray-800 dark:text-white">
                                    <i class="fas fa-comment-dots text-indigo-500 mr-1"></i>
                                    {{ $authorName }}
                                </h3>

                                <div class="flex justify-center space-x-3">
                                    <a href="{{ $url }}"
                                    class="flex items-center gap-2 text-white bg-blue-600 hover:bg-blue-700 font-semibold px-4 py-2 rounded-lg shadow">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </div>
                            </div>
                        @else
                            <!-- Lesson Preview -->
                            <a href="{{ $url }}">
                                <img src="{{ $item?->thumbnail_url ?? ($item?->thumbnail ? asset($item->thumbnail) : asset('logo/logoblack.png')) }}"
                                    alt="{{ $item?->title ?? 'Bookmarked item' }}"
                                    class="w-full h-44 object-cover">
                            </a>

                            <div class="p-5 text-center space-y-3">
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white line-clamp-2">
                                    <i class="fas fa-play-circle text-blue-500 mr-1"></i>
                                    {{ $item?->title }}
                                </h3>

                                <div class="flex justify-center space-x-3">
                                    <a href="{{ $url }}"
                                    class="flex items-center gap-2 text-white bg-blue-600 hover:bg-blue-700 font-semibold px-4 py-2 rounded-lg shadow">
                                        <i class="fas fa-video"></i> View
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach

            </div>
        @endif
    </div>
</section>
@endsection
