@extends('layouts.admin')

@section('content')
<main class="flex-1 p-4 sm:p-6 bg-gray-50 min-h-screen">

    <div class="max-w-7xl mx-auto" 
         x-data="{ openModal: false, selectedVideo: null }">

        <h1 class="text-2xl font-bold mb-6">Website Videos</h1>

         <!-- Add New Video Button -->
            <div class="my-5 text-right">
                <button 
                    @click="openModal = true; selectedVideo = null"
                    class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                >
                    Add New
                </button>
            </div>

        <!-- If no videos -->
        @if ($websiteVideos->isEmpty())
            <div class="bg-white border rounded-lg p-10 shadow-sm flex flex-col items-center justify-center text-center">
                <p class="text-gray-600 mb-6">No videos added yet.</p>
            </div>
        @else
            <!-- Video Grid -->
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($websiteVideos as $video)
                    <div class="bg-white rounded-lg shadow border p-4 flex flex-col">
                        <div class="aspect-w-16 aspect-h-9 bg-gray-200 mb-3">
                            <iframe 
                                src="https://drive.google.com/file/d/{{ $video->video_url }}/preview"
                                class="w-full h-full rounded"
                                frameborder="0"
                                allow="autoplay; encrypted-media"
                                allowfullscreen>
                            </iframe>
                        </div>
                        <p class="font-semibold text-gray-800">{{ $video->title }}</p>
                        <div class="mt-4 flex justify-between">
                            <button 
                                @click="openModal = true; selectedVideo = {{ $video->toJson() }}"
                                class="px-4 py-1 bg-yellow-500 text-white text-sm rounded hover:bg-yellow-600"
                            >
                                <span class="fa fa-edit"></span>
                            </button>
                            <form action="/admin/website-video/delete/{{$video->id}}" method="post">
                             @csrf
                             <button type="submit">
                              <span class="fa fa-trash text-red-500"></span>
                             </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Modal -->
        <div 
            x-show="openModal" 
            x-cloak
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        >
            <div class="bg-white w-full max-w-lg rounded-lg shadow-lg p-6 relative">
                <!-- Close -->
                <button 
                    @click="openModal = false" 
                    class="absolute top-2 right-2 text-gray-600 hover:text-red-600 text-2xl font-bold"
                >&times;</button>

                <h2 class="text-xl font-bold mb-4" x-text="selectedVideo ? 'Edit Video' : 'Add New Video'"></h2>

                <form method="POST" :action="selectedVideo ? '/admin/website-video/' + selectedVideo.id : '/admin/website-video'">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input 
                            type="text" 
                            name="title" 
                            class="w-full border rounded-lg px-3 py-2" 
                            x-bind:value="selectedVideo ? selectedVideo.title : ''"
                            required
                        >
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Google Drive ID</label>
                        <input 
                            type="text" 
                            name="video_url" 
                            class="w-full border rounded-lg px-3 py-2"
                            x-bind:value="selectedVideo ? selectedVideo.drive_id : ''"
                            required
                        >
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Google Drive ID</label>
                        <select name="video_category" id="" class="w-full border rounded-lg px-3 py-2">
                          <option value="">--select--</option>
                          <option value="tour">Video for Website Tour</option>
                          <option value="session">Video for Practise Session</option>
                          <option value="setUp">Video for Arranging Setup</option>
                          <option value="exper">Video for Quality Experience</option>
                        </select>
                    </div>

                    <div class="mt-6 flex justify-end space-x-2">
                        <button 
                            type="button" 
                            @click="openModal = false"
                            class="px-4 py-2 border rounded-lg"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                        >
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</main>
@endsection
