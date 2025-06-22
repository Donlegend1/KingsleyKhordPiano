@extends('layouts.member')

@section('content')
<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-6 px-4">
  <div class="max-w-7xl mx-auto space-y-3">
    <!-- Top Row -->
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

    <!-- Second Row -->
    <div>
      <h1 class="text-xl font-bold">Piano Exercises</h1>
    </div>
  </div>
</section>

<section class="bg-gray-100 p-6 min-h-screen">
  <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-6">
    
    <!-- Main Content -->
    <div class="flex-1">

      <!-- Tabs for Level -->
<div class="flex flex-wrap gap-2 border-b mb-6">
  @php
    $isAll = is_null($level);
    $url = route('piano.exercise', array_filter([
        'skill_level' => $skillLevel,
    ]));
  @endphp
  <a href="{{ $url }}"
     class="py-2 px-4 border-b-2 {{ $isAll ? 'border-blue-500 text-blue-500 font-semibold' : 'text-gray-600 hover:text-blue-500' }}">
     All
  </a>

  @foreach ($levels as $tab)
    @php
      $active = strtolower($level) === $tab;
      $url = route('piano.exercise', array_filter([
          'level' => $tab,
          'skill_level' => $skillLevel,
      ]));
    @endphp
    <a href="{{ $url }}"
       class="py-2 px-4 border-b-2 {{ $active ? 'border-blue-500 text-blue-500 font-semibold' : 'text-gray-600 hover:text-blue-500' }}">
       {{ ucfirst($tab) }}
    </a>
  @endforeach
</div>


      <!-- Exercise Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($exercises as $exercise)
          <div class="bg-white p-6 rounded-lg shadow flex flex-col items-center space-y-4">
            <img src="{{ $exercise->thumbnail_url }}" alt="{{ $exercise->title }}" class="w-full h-56 object-cover rounded-md">
            <h3 class="font-bold text-gray-800">{{ $exercise->title }}</h3>
            <p class="text-sm text-gray-500">{{ $exercise->level }} | {{ $exercise->skill_level }}</p>
            <a href="/member/lesson/{{ $exercise->id }}" class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition text-center">
              Watch Now
            </a>
          </div>
        @empty
          <div class="col-span-full text-center text-gray-500 py-12">
            No exercises found for this selection.
          </div>
        @endforelse

        @if ($exercises->total() > 9)
          <div class="col-span-full flex justify-center py-6">
            {{ $exercises->withQueryString()->links() }}
          </div>
        @endif
      </div>
    </div>

    <!-- Sidebar -->
    <aside class="w-full lg:w-64 bg-white p-6 rounded-lg shadow-lg h-fit">
      <h3 class="text-lg font-semibold mb-4">Filter by Skill Level</h3>
      <ul class="space-y-3 text-sm">
        <li>
          <a href="{{ route('piano.exercise', array_filter(['level' => $level])) }}"
             class="block px-3 py-2 rounded hover:bg-blue-50 {{ is_null($skillLevel) ? 'text-blue-600 font-semibold' : '' }}">
            All
          </a>
        </li>
        @foreach ($skillLevels as $sl)
          @php
            $active = strtolower($skillLevel) === $sl;
            $url = route('piano.exercise', array_filter([
                'level' => $level,
                'skill_level' => $sl,
            ]));
          @endphp
          <li>
            <a href="{{ $url }}"
               class="block px-3 py-2 rounded hover:bg-blue-50 {{ $active ? 'text-blue-600 font-semibold' : '' }}">
              {{ ucfirst($sl) }}
            </a>
          </li>
        @endforeach
      </ul>
    </aside>

  </div>
</section>
@endsection
