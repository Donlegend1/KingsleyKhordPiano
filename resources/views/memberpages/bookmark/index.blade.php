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
                $url = match ($bookmark->bookmarkable_type) {
                    'App\Models\Course' =>
                        '/member/course/' . $bookmark->bookmarkable?->level
                        . '?selected_course=' . $bookmark->bookmarkable?->id,
            
                    'App\Models\Lesson' =>
                        '/member/lesson/' . $bookmark->bookmarkable?->id,
            
                    'App\Models\Post' =>
                        '/member/post/' . $bookmark->bookmarkable?->id,
            
                    default => '#',
                };
            @endphp

                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition transform hover:-translate-y-1">
                        
                        <!-- Thumbnail -->
                        <a href="{{ $url }}">
                            <img src="{{'/logo/logoblack.png' }}" 
                                alt="{{ $bookmark->bookmarkable->title }}" 
                                class="w-full h-44 object-cover">

                            {{-- <p class="text-xs text-gray-500 dark:text-gray-400">{{ ucfirst($bookmark->video->category) }}</p> --}}
                        </a>

                        <!-- Content -->
                        <div class="p-5 text-center space-y-3">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white line-clamp-2">
                                <i class="fas fa-play-circle text-blue-500 mr-1"></i>
                                {{ $bookmark->bookmarkable?->title }}
                            </h3>
                            
                            <div class="flex justify-center space-x-3">
                                <a href="{{ $url }}" 
                                class="flex items-center gap-2 text-white bg-blue-600 hover:bg-blue-700 font-semibold px-4 py-2 rounded-lg shadow">
                                    <i class="fas fa-video"></i> View Lesson
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        @endif
    </div>
</section>
@endsection
