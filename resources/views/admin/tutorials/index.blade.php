@extends('layouts.admin')

@section('content')
<main class="flex-1 p-6 overflow-y-auto">
  
  {{-- Header --}}
  <header class="mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
    <div>
      <h2 class="text-2xl font-bold text-gray-800 mb-1">Community Tutorials</h2>
      <p class="text-sm text-gray-500">Manage video tutorials listed under the community tab.</p>
    </div>
    <div>
      <a href="{{ route('admin.tutorials.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold shadow transition">
        <i class="fa fa-plus"></i> Upload Tutorial
      </a>
    </div>
  </header>

  {{-- Status Alerts --}}
  @if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-r-lg shadow-sm" role="alert">
      <p class="text-sm font-semibold">{{ session('success') }}</p>
    </div>
  @endif

  {{-- Tutorials Table --}}
  <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-gray-50 border-b border-gray-200">
            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Thumbnail</th>
            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Title & Instructor</th>
            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Video Info</th>
            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Duration</th>
            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Level Label</th>
            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Status</th>
            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          @forelse($tutorials as $t)
            <tr class="hover:bg-gray-50 transition-colors">
              <td class="px-6 py-4 whitespace-nowrap">
                @if($t->thumbnail)
                  <img src="{{ asset($t->thumbnail) }}" alt="" class="w-16 h-10 object-cover rounded-lg border border-gray-200">
                @else
                  <div class="w-16 h-10 bg-gray-100 rounded-lg flex items-center justify-center border border-gray-200 text-gray-400">
                    <i class="fa fa-video text-xs"></i>
                  </div>
                @endif
              </td>
              <td class="px-6 py-4">
                <div class="text-sm font-bold text-gray-800">{{ $t->title }}</div>
                <div class="flex items-center gap-1.5 mt-1">
                  @if($t->author_avatar)
                    <img src="{{ asset($t->author_avatar) }}" class="w-4 h-4 rounded-full object-cover">
                  @else
                    <div class="w-4 h-4 rounded-full bg-gray-200 flex items-center justify-center text-[8px] font-bold text-gray-600">
                      {{ strtoupper(substr($t->author_name, 0, 1)) }}
                    </div>
                  @endif
                  <span class="text-xs text-gray-400 font-medium">{{ $t->author_name }}</span>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-xs font-bold px-2 py-0.5 rounded-full uppercase bg-indigo-50 border border-indigo-100 text-indigo-700">
                  {{ $t->video_type }}
                </span>
                <div class="text-[10px] text-gray-400 mt-1 max-w-[150px] truncate" title="{{ $t->video_url }}">
                  {{ $t->video_url }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-700">
                {{ $t->duration ?? '--:--' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                @if($t->level)
                  <span class="text-xs font-medium bg-gray-100 border border-gray-200 text-gray-700 px-2 py-0.5 rounded-full">
                    {{ $t->level }}
                  </span>
                @else
                  <span class="text-xs text-gray-400 italic">None</span>
                @endif
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center gap-1 text-xs font-bold {{ $t->status === 'active' ? 'text-green-600' : 'text-red-500' }}">
                  <span class="w-1.5 h-1.5 rounded-full {{ $t->status === 'active' ? 'bg-green-600' : 'bg-red-500' }}"></span>
                  {{ ucfirst($t->status) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                <div class="inline-flex gap-2">
                  <a href="{{ route('admin.tutorials.edit', $t->id) }}" class="p-1.5 hover:bg-gray-100 rounded-lg text-blue-600 hover:text-blue-700 transition" title="Edit">
                    <i class="fa fa-edit"></i>
                  </a>
                  
                  <form action="{{ route('admin.tutorials.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this tutorial?')" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-1.5 hover:bg-red-50 rounded-lg text-red-500 hover:text-red-600 transition" title="Delete">
                      <i class="fa fa-trash"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="px-6 py-12 text-center text-gray-400 italic text-sm">
                No tutorials uploaded yet. Click "Upload Tutorial" to add one!
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</main>
@endsection
