<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        {{-- Left side: Latest Courses --}}
        <div>
            <div class="flex items-center gap-3 mb-6">
                <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Latest Lessons</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                @foreach ($latestCourses as $category => $latestCourse)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-lg transition overflow-hidden flex flex-col group">
                        {{-- Thumbnail --}}
                        <a href="/member/lesson/{{ $latestCourse->id }}" class="block relative overflow-hidden">
                            <div class="absolute inset-0 bg-black bg-opacity-20 group-hover:bg-opacity-0 transition z-10"></div>
                            <img src="{{ $latestCourse->thumbnail_url }}" 
                                 alt="{{ $latestCourse->title }}" 
                                 class="w-full h-40 object-cover transform group-hover:scale-105 transition duration-500">
                            
                            {{-- Category Badge --}}
                            <div class="absolute top-3 left-3 z-20">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] uppercase tracking-wider font-bold shadow-sm backdrop-blur-md bg-white/90 text-gray-800">
                                    @if ($category === 'piano exercise')
                                        <i class="fas fa-music text-blue-600"></i>
                                    @elseif ($category === 'extra courses')
                                        <i class="fas fa-book-open text-green-600"></i>
                                    @elseif ($category === 'quick lessons')
                                        <i class="fas fa-bolt text-yellow-500"></i>
                                    @elseif ($category === 'learn songs')
                                        <i class="fas fa-guitar text-purple-600"></i>
                                    @endif
                                    {{ ucwords($category) }}
                                </span>
                            </div>
                        </a>

                        {{-- Card Content --}}
                        <div class="p-4 flex flex-col flex-1">
                            {{-- Title --}}
                            <h3 class="text-base font-bold text-gray-800 mb-4 line-clamp-2 group-hover:text-blue-600 transition">
                                <a href="/member/lesson/{{ $latestCourse->id }}">
                                    {{ $latestCourse->title }}
                                </a>
                            </h3>

                            {{-- Button --}}
                            <div class="mt-auto">
                                <a href="/member/lesson/{{ $latestCourse->id }}" 
                                   class="inline-flex items-center justify-center w-full gap-2 px-4 py-2.5 bg-gray-50 hover:bg-blue-600 text-gray-700 hover:text-white text-sm font-semibold rounded-lg transition duration-300">
                                    <i class="fas fa-play"></i> Watch Now
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Right side: Latest Comments --}}
        <div>
            <div class="flex items-center gap-3 mb-6">
                <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Latest Comments</h2>
            </div>
            
            <div class="space-y-4">
                @forelse($latestComments as $comment)
                    @php
                        $commentUrl = $comment->course ? "/member/lesson/{$comment->course_id}" : ($comment->url ?? '#');
                    @endphp
                    <a href="{{ $commentUrl }}" class="block bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:border-blue-200 transition group">
                        <div class="flex items-start gap-4">
                            @if($comment->user->passport)
                                <img src="{{ $comment->user->passport }}" class="w-12 h-12 rounded-full object-cover shadow-sm">
                            @else
                                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-lg shadow-sm">
                                    {{ strtoupper(substr($comment->user->first_name ?? $comment->user->name ?? 'U', 0, 1)) }}
                                </div>
                            @endif
                            <div class="flex-1">
                                <div class="flex justify-between items-center mb-1">
                                    <h4 class="font-bold text-gray-800">{{ $comment->user->first_name ?? $comment->user->name }}</h4>
                                    <span class="text-xs text-gray-500 font-medium bg-gray-100 px-2 py-1 rounded-full">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-gray-600 text-sm mb-3 line-clamp-2 leading-relaxed group-hover:text-gray-900 transition">{{ $comment->comment }}</p>
                                
                                @if($comment->course)
                                    <span class="inline-flex items-center gap-1.5 text-blue-600 text-xs font-semibold group-hover:text-blue-800 transition">
                                        <i class="fas fa-play-circle"></i> On: <span class="truncate max-w-[200px] inline-block align-bottom">{{ $comment->course->title }}</span>
                                    </span>
                                @elseif($comment->url)
                                    <span class="inline-flex items-center gap-1.5 text-blue-600 text-xs font-semibold group-hover:text-blue-800 transition">
                                        <i class="fas fa-link"></i> View Context
                                    </span>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center">
                        <i class="fas fa-comment-slash text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500 font-medium">No comments yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
