@extends('layouts.admin')

@section('content')
<main class="flex-1 p-6 overflow-y-auto">

  {{-- Header --}}
  <header class="mb-6">
    <a href="{{ route('admin.student-challenges.index') }}" class="text-xs text-blue-600 hover:underline inline-flex items-center gap-1 mb-2">
      <i class="fa fa-arrow-left"></i> Back to List
    </a>
    <h2 class="text-2xl font-bold text-gray-800">Add Challenge Topic</h2>
    <p class="text-sm text-gray-500">Create a new monthly challenge topic under the Student Challenges forum.</p>
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
    <form action="{{ route('admin.student-challenges.store') }}" method="POST" class="space-y-6">
      @csrf

      <div>
        <label for="title" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Topic Title *</label>
        <input
          type="text"
          name="title"
          id="title"
          value="{{ old('title') }}"
          required
          placeholder="e.g. December Challenge"
          class="w-full text-sm px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none"
        >
      </div>

      <div>
        <label for="description" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Description</label>
        <textarea
          name="description"
          id="description"
          rows="4"
          placeholder="Explain the challenge for this topic..."
          class="w-full text-sm px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none resize-none"
        >{{ old('description') }}</textarea>
      </div>

      <div>
        <label for="video_url" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Pinned Video URL (Optional)</label>
        <input
          type="text"
          name="video_url"
          id="video_url"
          value="{{ old('video_url') }}"
          placeholder="e.g. https://www.youtube.com/watch?v=... or https://drive.google.com/file/d/.../view"
          class="w-full text-sm px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none"
        >
        <p class="text-xs text-gray-400 mt-1.5">Shown pinned at the top of this challenge's post page. Paste a YouTube, Vimeo, or Google Drive share link — it's auto-detected and embedded, no need to pick a type. For Google Drive, make sure the file's sharing is set to "Anyone with the link".</p>
      </div>

      <div class="flex items-center gap-2">
        <input type="checkbox" name="is_pinned" id="is_pinned" value="1" {{ old('is_pinned') ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-400">
        <label for="is_pinned" class="text-sm font-medium text-gray-700">Pin this topic to the top of the Topics list</label>
      </div>

      <div class="pt-4 border-t border-gray-100 flex justify-end gap-3">
        <a href="{{ route('admin.student-challenges.index') }}" class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
          Cancel
        </a>
        <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold shadow transition">
          Save Challenge Topic
        </button>
      </div>

    </form>
  </div>

</main>
@endsection
