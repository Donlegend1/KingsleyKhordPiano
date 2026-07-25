@extends('layouts.member')

@section('content')

<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-4 px-4 border-b border-gray-150 dark:border-gray-800">
  <div class="max-w-7xl mx-auto flex items-center h-8 gap-2 text-sm text-gray-500">
    <a href="/home" class="hover:text-gray-700">Dashboard</a>
    <span>/</span>
    <span class="text-blue-600 font-medium">Ear Training</span>
  </div>
</section>

<section class="bg-gray-100 py-6 px-6">
 <div class="max-w-7xl mx-auto">
   {{-- <h2 class="text-2xl font-bold mb-8 text-center">Ear Training Quiz</h2> --}}

   <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse ($data as  $quiz)
     <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
      <a href="/member/ear-training/{{$quiz->id}}">
       <img src="{{ $quiz->thumbnail_url }}" alt="Finger Strengthening" class="w-full  object-cover">
       </a>
       <div class="p-6 text-center my-3">
         <h3 class="text-lg font-bold mb-4 text-gray-800">{{$quiz->title}}</h3>
         {{-- <p class="text-gray-600 mb-4">{{$quiz->description}}</p> --}}
         <a href="/member/ear-training/{{$quiz->id}}" class="text-blue-500 hover:bg-black hover:text-white font-semibold border border-blue-600 p-2 rounded-lg">START QUIZ</a>
       </div>
     </div>
    @empty
     <div class="col-span-full text-center text-gray-500 py-12 text-lg font-semibold">
       <i class="fa fa-exclamation-circle fa-2x mb-2"></i>
       <p>No result found.</p>
     </div>
    @endforelse
   </div>
   
 </div>
  @if ($data->hasPages())
    <div class="col-span-full flex justify-center py-8">
      {{ $data->withQueryString()->links('components.pagination') }}
    </div>
  @endif
</section>


@endsection