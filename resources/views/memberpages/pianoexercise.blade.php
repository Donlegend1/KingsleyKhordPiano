
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
  <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-6" x-data="{ activeTab: 'all' }">

    <!-- Main Content Area -->
    <div class="flex-1">
      
      <!-- Tabs -->
      <div class="mb-6">
        <div class="flex flex-wrap gap-2 border-b">
          <template x-for="tab in ['all', 'independence', 'coordination', 'flexibility', 'strength', 'dexterity']" :key="tab">
            <button
              class="py-2 px-4 text-gray-600 hover:text-blue-500 border-b-2"
              :class="{ 'border-blue-500 text-blue-500': activeTab === tab }"
              @click="activeTab = tab"
              x-text="tab.charAt(0).toUpperCase() + tab.slice(1)">
            </button>
          </template>
        </div>
      </div>

   <!-- Tab Panels -->
<div class="space-y-6">

  <!-- All Tab -->
  <div x-show="activeTab === 'all'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse ($all as $exercise)
      <div class="bg-white p-6 rounded-lg shadow flex flex-col items-center space-y-4">
        <img src="{{ $exercise->thumbnail_url }}" alt="{{ $exercise->title }}" class="w-full h-56 object-cover rounded-md">
        <h3 class="font-bold text-gray-800">{{ $exercise->title }}</h3>
        <a href="/member/lesson/{{ $exercise->id }}" class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition text-center">
          Watch Now
        </a>
      </div>
    @empty
      <div class="col-span-full text-center text-gray-500 py-12">
        No exercises found for this category.
      </div>
    @endforelse
    @if ($all->total() > 9)
  <div class="flex justify-center py-6 bg-gray-100 rounded-md mt-4">
    {{ $all->links() }}
  </div>
@endif
  </div>

  <!-- Independence Tab -->
  <div x-show="activeTab === 'independence'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse ($independence as $exercise)
      <div class="bg-white p-6 rounded-lg shadow flex flex-col items-center space-y-4">
        <img src="{{ $exercise->thumbnail_url }}" alt="{{ $exercise->title }}" class="w-full h-56 object-cover rounded-md">
        <h3 class="font-bold text-gray-800">{{ $exercise->title }}</h3>
        <a href="/member/lesson/{{ $exercise->id }}" class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition text-center">
          Watch Now
        </a>
      </div>
    @empty
      <div class="col-span-full text-center text-gray-500 py-12">
        No exercises found for this category.
      </div>
    @endforelse
     @if ($independence->total() > 9)
  <div class="flex justify-center py-6 bg-gray-100 rounded-md mt-4">
    {{ $independence->links() }}
  </div>
@endif
  </div>

  <!-- Coordination Tab -->
  <div x-show="activeTab === 'coordination'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse ($coordination as $exercise)
      <div class="bg-white p-6 rounded-lg shadow flex flex-col items-center space-y-4">
        <img src="{{ $exercise->thumbnail_url }}" alt="{{ $exercise->title }}" class="w-full h-56 object-cover rounded-md">
        <h3 class="font-bold text-gray-800">{{ $exercise->title }}</h3>
        <a href="/member/lesson/{{ $exercise->id }}" class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition text-center">
          Watch Now
        </a>
      </div>
    @empty
      <div class="col-span-full text-center text-gray-500 py-12">
        No exercises found for this category.
      </div>
    @endforelse

     @if ($coordination->total() > 9)
  <div class="flex justify-center py-6 bg-gray-100 rounded-md mt-4">
    {{ $coordination->links() }}
  </div>
@endif
  </div>

  <!-- Flexibility Tab -->
  <div x-show="activeTab === 'flexibility'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse ($flexibility as $exercise)
      <div class="bg-white p-6 rounded-lg shadow flex flex-col items-center space-y-4">
        <img src="{{ $exercise->thumbnail_url }}" alt="{{ $exercise->title }}" class="w-full h-56 object-cover rounded-md">
        <h3 class="font-bold text-gray-800">{{ $exercise->title }}</h3>
        <a href="/member/lesson/{{ $exercise->id }}" class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition text-center">
          Watch Now
        </a>
      </div>
    @empty
      <div class="col-span-full text-center text-gray-500 py-12">
        No exercises found for this category.
      </div>
    @endforelse
     @if ($flexibility->total() > 9)
  <div class="flex justify-center py-6 bg-gray-100 rounded-md mt-4">
    {{ $flexibility->links() }}
  </div>
@endif
  </div>

  <!-- Strength Tab -->
  <div x-show="activeTab === 'strength'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse ($strength as $exercise)
      <div class="bg-white p-6 rounded-lg shadow flex flex-col items-center space-y-4">
        <img src="{{ $exercise->thumbnail_url }}" alt="{{ $exercise->title }}" class="w-full h-56 object-cover rounded-md">
        <h3 class="font-bold text-gray-800">{{ $exercise->title }}</h3>
        <a href="/member/lesson/{{ $exercise->id }}" class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition text-center">
          Watch Now
        </a>
      </div>
    @empty
      <div class="col-span-full text-center text-gray-500 py-12">
        No exercises found for this category.
      </div>
    @endforelse
      @if ($strength->total() > 9)
  <div class="flex justify-center py-6 bg-gray-100 rounded-md mt-4">
    {{ $strength->links() }}
  </div>
@endif
  </div>

  <!-- Dexterity Tab -->
  <div x-show="activeTab === 'dexterity'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse ($dexterity as $exercise)
      <div class="bg-white p-6 rounded-lg shadow flex flex-col items-center space-y-4">
        <img src="{{ $exercise->thumbnail_url }}" alt="{{ $exercise->title }}" class="w-full h-56 object-cover rounded-md">
        <h3 class="font-bold text-gray-800">{{ $exercise->title }}</h3>
        <a href="/member/lesson/{{ $exercise->id }}" class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition text-center">
          Watch Now
        </a>
      </div>
    @empty
      <div class="col-span-full text-center text-gray-500 py-12">
        No exercises found for this category.
      </div>
    @endforelse
     @if ($dexterity->total() > 9)
  <div class="flex justify-center py-6 bg-gray-100 rounded-md mt-4">
    {{ $dexterity->links() }}
  </div>
@endif
  </div>

</div>

    </div>

    <!-- Sidebar -->
    =<aside class="w-full lg:w-64 bg-white p-6 rounded-lg shadow-lg h-fit">
  <h3 class="text-lg font-semibold mb-4">Filter by Category</h3>
  <ul class="space-y-3 text-sm" x-data="{ categories: ['all', 'independence', 'coordination', 'flexibility', 'strength', 'dexterity'] }">
    <template x-for="category in categories" :key="category">
      <li>
        <label class="flex items-center space-x-3 cursor-pointer">
          <input 
            type="radio" 
            class="hidden" 
            name="category" 
            :value="category" 
            x-model="activeTab"
          >
          <div 
            class="w-4 h-4 rounded border border-gray-400 flex items-center justify-center" 
            :class="{ 'bg-blue-600 border-blue-600': activeTab === category }"
          >
            <svg x-show="activeTab === category" class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <span x-text="category.charAt(0).toUpperCase() + category.slice(1)" :class="{ 'font-semibold text-blue-600': activeTab === category }"></span>
        </label>
      </li>
    </template>
  </ul>
</aside>

</div>
</section>



@endsection