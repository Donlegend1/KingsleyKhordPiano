<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    {{-- Section Title --}}
    <div class="text-center mb-10">
        <h2 class="text-3xl font-extrabold text-gray-900 flex items-center justify-center gap-3">
            <i class="fas fa-music text-blue-600"></i>
            Latest
        </h2>
        <p class="text-gray-600 mt-2">Discover the newest lessons across different categories</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach ($latestCourses as $category => $latestCourse)
            <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition overflow-hidden flex flex-col">
                {{-- Thumbnail --}}
                <a href="/member/lesson/{{ $latestCourse->id }}" class="block">
                    <img src="{{ $latestCourse->thumbnail_url }}" 
                         alt="{{ $latestCourse->title }}" 
                         class="w-full h-48 object-cover">
                </a>

                {{-- Card Content --}}
                <div class="p-5 flex flex-col flex-1">
                    {{-- Category with Icon --}}
                    <div class="flex items-center gap-2 mb-2">
                        @if ($category === 'piano exercise')
                            <i class="fas fa-music text-blue-600"></i>
                        @elseif ($category === 'extra courses')
                            <i class="fas fa-book-open text-green-600"></i>
                        @elseif ($category === 'quick lessons')
                            <i class="fas fa-bolt text-yellow-500"></i>
                        @elseif ($category === 'learn songs')
                            <i class="fas fa-guitar text-purple-600"></i>
                        @endif
                        <span class="text-xs uppercase tracking-wide text-gray-700 font-semibold">
                            {{ ucwords($category) }}
                        </span>
                    </div>

                    {{-- Title --}}
                    <h3 class="text-lg font-bold text-gray-800 mb-4 line-clamp-2">
                        {{ $latestCourse->title }}
                    </h3>

                    {{-- Button --}}
                    <div class="mt-auto">
                        <a href="/member/lesson/{{ $latestCourse->id }}" 
                           class="inline-block w-full text-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow hover:bg-blue-700 transition">
                            <i class="fas fa-play-circle mr-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
