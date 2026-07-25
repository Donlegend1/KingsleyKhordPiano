@extends('layouts.admin')

@section('content')
<main class="flex-1 p-6 overflow-y-auto">
  
  {{-- Header --}}
  <header class="mb-6">
    <a href="{{ route('admin.tutorials.index') }}" class="text-xs text-blue-600 hover:underline inline-flex items-center gap-1 mb-2">
      <i class="fa fa-arrow-left"></i> Back to List
    </a>
    <h2 class="text-2xl font-bold text-gray-800">Edit Tutorial</h2>
    <p class="text-sm text-gray-500">Modify the video tutorial details.</p>
  </header>

  {{-- Validation Errors --}}
  @if($errors->any())
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg shadow-sm" role="alert">
      <h4 class="text-sm font-bold mb-1">Please correct the errors:</h4>
      <ul class="list-disc list-inside text-xs">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Form --}}
  <div class="bg-white border border-gray-200 rounded-xl shadow-sm max-w-3xl p-6">
    <form action="{{ route('admin.tutorials.update', $tutorial->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
      @csrf
      @method('PUT')

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        {{-- Title --}}
        <div class="md:col-span-2">
          <label for="title" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Tutorial Title *</label>
          <input 
            type="text" 
            name="title" 
            id="title" 
            value="{{ old('title', $tutorial->title) }}" 
            required 
            placeholder="e.g. Piano Basics for Beginners"
            class="w-full text-sm px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none"
          >
        </div>

        {{-- Description --}}
        <div class="md:col-span-2">
          <label for="description" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Description</label>
          <textarea 
            name="description" 
            id="description" 
            rows="3" 
            placeholder="Provide a short description of what is taught in this video..."
            class="w-full text-sm px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none resize-none"
          >{{ old('description', $tutorial->description) }}</textarea>
        </div>

        {{-- Video Type --}}
        <div>
          <label for="video_type" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Video Provider *</label>
          <select 
            name="video_type" 
            id="video_type" 
            required
            class="w-full text-sm px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none bg-white"
          >
            <option value="vimeo" {{ old('video_type', $tutorial->video_type) === 'vimeo' ? 'selected' : '' }}>Vimeo</option>
            <option value="iframe" {{ old('video_type', $tutorial->video_type) === 'iframe' ? 'selected' : '' }}>Iframe</option>
            <option value="youtube" {{ old('video_type', $tutorial->video_type) === 'youtube' ? 'selected' : '' }}>YouTube</option>
            <option value="google" {{ old('video_type', $tutorial->video_type) === 'google' ? 'selected' : '' }}>Google Drive</option>
            <option value="local" {{ old('video_type', $tutorial->video_type) === 'local' ? 'selected' : '' }}>Local Path</option>
          </select>
        </div>

        {{-- Video URL / ID --}}
        <div>
          <label for="video_url" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Video URL or Video ID *</label>
          <textarea 
            name="video_url" 
            id="video_url" 
            required
            placeholder="e.g. 1195123553, full URL or embed code"
            rows="3"
            class="w-full text-sm px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none"
          >{{ old('video_url', $tutorial->video_url) }}</textarea>
        </div>

        {{-- Duration --}}
        <div>
          <label for="duration" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Duration</label>
          <input 
            type="text" 
            name="duration" 
            id="duration" 
            value="{{ old('duration', $tutorial->duration) }}" 
            placeholder="e.g. 18:45"
            class="w-full text-sm px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none"
          >
        </div>

        {{-- Level Label --}}
        <div>
          <label for="level" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Level Label (Optional)</label>
          <select 
            name="level" 
            id="level"
            class="w-full text-sm px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none bg-white"
          >
            <option value="">None</option>
            <option value="Beginner" {{ old('level', $tutorial->level) === 'Beginner' ? 'selected' : '' }}>Beginner</option>
            <option value="Intermediate" {{ old('level', $tutorial->level) === 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
            <option value="Advanced" {{ old('level', $tutorial->level) === 'Advanced' ? 'selected' : '' }}>Advanced</option>
          </select>
        </div>

        {{-- Author Name --}}
        <div>
          <label for="author_name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Instructor Name</label>
          <input 
            type="text" 
            name="author_name" 
            id="author_name" 
            value="{{ old('author_name', $tutorial->author_name) }}"
            class="w-full text-sm px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none"
          >
        </div>

        {{-- Status --}}
        <div>
          <label for="status" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Status</label>
          <select 
            name="status" 
            id="status"
            class="w-full text-sm px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none bg-white"
          >
            <option value="active" {{ old('status', $tutorial->status) === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', $tutorial->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
          </select>
        </div>

        {{-- Thumbnail Image --}}
        <div>
          <label for="thumbnail" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Video Thumbnail</label>
          @if($tutorial->thumbnail)
            <div class="mb-2">
              <img src="{{ asset($tutorial->thumbnail) }}" class="w-32 h-20 object-cover rounded-lg border border-gray-200">
              <span class="text-[10px] text-gray-400">Current Thumbnail</span>
            </div>
          @endif
          <input 
            type="file" 
            name="thumbnail" 
            id="thumbnail" 
            accept="image/*"
            class="w-full text-sm border border-gray-300 rounded-lg file:mr-4 file:py-2 file:px-4 file:rounded-l-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
          >
        </div>

        {{-- Instructor Avatar --}}
        <div>
          <label for="author_avatar" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Instructor Avatar</label>
          @if($tutorial->author_avatar)
            <div class="mb-2">
              <img src="{{ asset($tutorial->author_avatar) }}" class="w-12 h-12 rounded-full object-cover border border-gray-200">
              <span class="text-[10px] text-gray-400 block mt-1">Current Avatar</span>
            </div>
          @endif
          <input 
            type="file" 
            name="author_avatar" 
            id="author_avatar" 
            accept="image/*"
            class="w-full text-sm border border-gray-300 rounded-lg file:mr-4 file:py-2 file:px-4 file:rounded-l-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
          >
        </div>

      </div>

      <div class="pt-4 border-t border-gray-100 flex justify-end gap-3">
        <a href="{{ route('admin.tutorials.index') }}" class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
          Cancel
        </a>
        <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold shadow transition">
          Update Tutorial
        </button>
      </div>

    </form>
  </div>

</main>
@endsection
