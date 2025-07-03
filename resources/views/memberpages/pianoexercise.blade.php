@extends('layouts.member')

@section('content')
<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-6 px-4">
  <div class="max-w-7xl mx-auto space-y-3">

    <!-- Breadcrumb & User -->
    <div class="flex justify-between items-center">
      <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
        <a href="/home" class="hover:text-blue-600">Dashboard</a>
        <span>/</span>
        <a href="/member/piano-exercise" class="hover:text-blue-600 font-semibold">Piano Exercises</a>
      </div>
      <div class="flex items-center space-x-2">
        <i class="fa fa-user-circle text-xl"></i>
      </div>
    </div>

    <div>
      <h1 class="text-xl font-bold">Piano Exercises</h1>
    </div>
  </div>
</section>

<section class="bg-gray-100 py-10">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 xl:px-10">

    <!-- Mobile Filters -->
    <div class="block sm:hidden mb-6 space-y-4">

      <!-- Category Dropdown -->
      <select onchange="window.location.href = this.value" class="w-full border-gray-300 rounded p-2 text-sm">
        <option value="{{ route('piano.exercise', array_filter(['skill_level' => $skillLevel])) }}" {{ is_null($level) ? 'selected' : '' }}>All Categories</option>
        @foreach ($levels as $tab)
          <option value="{{ route('piano.exercise', array_filter(['level' => $tab, 'skill_level' => $skillLevel])) }}" {{ strtolower($level) === $tab ? 'selected' : '' }}>
            {{ ucfirst($tab) }}
          </option>
        @endforeach
      </select>

      <!-- Skill Level Dropdown -->
      <select onchange="window.location.href = this.value" class="w-full border-gray-300 rounded p-2 text-sm">
        <option value="{{ route('piano.exercise', array_filter(['level' => $level])) }}" {{ is_null($skillLevel) ? 'selected' : '' }}>All Skill Levels</option>
        @foreach ($skillLevels as $sl)
          <option value="{{ route('piano.exercise', array_filter(['level' => $level, 'skill_level' => $sl])) }}" {{ strtolower($skillLevel) === $sl ? 'selected' : '' }}>
            {{ ucfirst($sl) }}
          </option>
        @endforeach
      </select>

    </div>

    <div class="flex flex-col lg:flex-row gap-8">

      <!-- Main Content -->
      <div class="flex-1">

        <!-- Desktop Tabs -->
        <div class="hidden sm:flex flex-wrap gap-3 border-b pb-2 mb-8">
          <a href="{{ route('piano.exercise', array_filter(['skill_level' => $skillLevel])) }}" class="py-2 px-4 border-b-2 {{ is_null($level) ? 'border-blue-500 text-blue-500 font-semibold' : 'text-gray-600 hover:text-blue-500' }}">All</a>
          @foreach ($levels as $tab)
            <a href="{{ route('piano.exercise', array_filter(['level' => $tab, 'skill_level' => $skillLevel])) }}" class="py-2 px-4 border-b-2 {{ strtolower($level) === $tab ? 'border-blue-500 text-blue-500 font-semibold' : 'text-gray-600 hover:text-blue-500' }}">{{ ucfirst($tab) }}</a>
          @endforeach
        </div>

        <!-- Exercise Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          @forelse ($exercises as $exercise)
            <div class="bg-white p-6 rounded-lg shadow flex flex-col items-center space-y-4">
              <img src="{{ $exercise->thumbnail_url }}" alt="{{ $exercise->title }}" class="w-full h-56 object-cover rounded-md">
              <h3 class="font-bold text-gray-800 text-center">{{ $exercise->title }}</h3>
              <p class="text-sm text-gray-500">{{ $exercise->level }} | {{ $exercise->skill_level }}</p>
              <a href="/member/lesson/{{ $exercise->id }}" class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition text-center w-full">
                Watch Now
              </a>
            </div>
          @empty
            <div class="col-span-full text-center text-gray-500 py-12">
              No exercises found for this selection.
            </div>
          @endforelse

          @if ($exercises->total() > 9)
            <div class="col-span-full flex justify-center py-8">
              {{ $exercises->withQueryString()->links() }}
            </div>
          @endif

        </div>
      </div>

      <!-- Desktop Sidebar -->
      <aside class="hidden lg:block w-72 bg-white p-6 rounded-xl shadow-md h-fit mt-14 lg:mt-[72px]">
        <h3 class="text-lg font-semibold mb-3 border-b pb-2 text-gray-800">Filter by Skill Level</h3>
        <div class="space-y-2">
          <a href="{{ route('piano.exercise', array_filter(['level' => $level])) }}" class="flex items-center gap-2 px-4 py-2 rounded-md border border-gray-200 text-sm transition hover:bg-blue-50 {{ is_null($skillLevel) ? 'bg-blue-50 border-blue-300 text-blue-600 font-medium' : 'text-gray-700' }}">
            <i class="fas fa-layer-group text-gray-400"></i>
            All
          </a>
          @foreach ($skillLevels as $sl)
            @php
              $url = route('piano.exercise', array_filter(['level' => $level, 'skill_level' => $sl]));
              $icon = match($sl) {
                'beginner' => 'fa-star',
                'intermediate' => 'fa-chart-line',
                'advanced' => 'fa-mountain',
                default => 'fa-circle',
              };
            @endphp
            <a href="{{ $url }}" class="flex items-center gap-2 px-4 py-2 rounded-md border border-gray-200 text-sm transition hover:bg-blue-50 {{ strtolower($skillLevel) === $sl ? 'bg-blue-50 border-blue-300 text-blue-600 font-medium' : 'text-gray-700' }}">
              <i class="fas {{ $icon }} text-gray-400"></i>
              {{ ucfirst($sl) }}
            </a>
          @endforeach
        </div>
      </aside>

    </div>
  </div>
</section>
@endsection
