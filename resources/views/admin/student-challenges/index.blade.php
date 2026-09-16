@extends('layouts.admin')

@section('content')
<main class="flex-1 p-6 overflow-y-auto">

  {{-- Header --}}
  <header class="mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
    <div>
      <h2 class="text-2xl font-bold text-gray-800 mb-1">Student Challenges</h2>
      <p class="text-sm text-gray-500">Manage the monthly challenge topics listed under the community forum.</p>
    </div>
    <div>
      <a href="{{ route('admin.student-challenges.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold shadow transition">
        <i class="fa fa-plus"></i> Add Challenge Topic
      </a>
    </div>
  </header>

  {{-- Status Alerts --}}
  @if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-r-lg shadow-sm" role="alert">
      <p class="text-sm font-semibold">{{ session('success') }}</p>
    </div>
  @endif

  {{-- Challenges Table --}}
  <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-gray-50 border-b border-gray-200">
            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Title</th>
            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Pinned Video</th>
            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Submissions</th>
            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Pinned</th>
            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          @forelse($challenges as $challenge)
            <tr class="hover:bg-gray-50 transition-colors">
              <td class="px-6 py-4">
                <div class="text-sm font-bold text-gray-800">{{ $challenge->title }}</div>
                <div class="text-xs text-gray-400 mt-0.5">Created {{ $challenge->created_at->format('M j, Y') }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                @if($challenge->video_url)
                  <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded-full">
                    <i class="fa fa-video"></i> Set
                  </span>
                @else
                  <span class="text-xs text-gray-400 italic">None</span>
                @endif
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-700">
                {{ $challenge->submissions_count }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                @if($challenge->is_pinned)
                  <span class="inline-flex items-center gap-1 text-xs font-bold text-amber-700 bg-amber-50 border border-amber-100 px-2 py-0.5 rounded-full">
                    <i class="fa fa-thumbtack"></i> Pinned
                  </span>
                @else
                  <span class="text-xs text-gray-400 italic">No</span>
                @endif
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                <div class="inline-flex gap-2">
                  <a href="{{ route('admin.student-challenges.edit', $challenge->id) }}" class="p-1.5 hover:bg-gray-100 rounded-lg text-blue-600 hover:text-blue-700 transition" title="Edit">
                    <i class="fa fa-edit"></i>
                  </a>

                  <form action="{{ route('admin.student-challenges.destroy', $challenge->id) }}" method="POST" onsubmit="return confirm('Delete this challenge topic and all its submissions? This cannot be undone.')" class="inline-block">
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
              <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic text-sm">
                No challenge topics yet. Click "Add Challenge Topic" to create one!
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</main>
@endsection
