@extends('layouts.member')
@section('content')
<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-6 px-4">
 <div class="max-w-7xl mx-auto space-y-3">
   
   <!-- Top Row -->
   <div class="flex justify-between items-center">
     <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
       <a href="/home" class="hover:text-blue-600">Dashboard</a>
       <span>/</span>
       <a href="/member/extra-courses" class="hover:text-blue-600 font-semibold">Lession</a>
       <span>/</span>
       <a href="/member/lesson/{{$lesson->id}}" class="hover:text-blue-600 font-semibold">{{$lesson->id}}</a>
     </div>
     <div class="flex items-center space-x-2">
       <i class="fa fa-user-circle text-xl"></i>
     </div>
   </div>

   <!-- Second Row -->
  <div>
  <h1 class="text-xl font-bold">{{ strtoupper($lesson->category) }} / {{ $lesson->title }}</h1>
</div>

 </div>
</section>
<section class="flex items-start justify-center min-h-screen bg-gray-100 p-6">
 <!-- Blog Main Content -->
 <div class="bg-white max-w-3xl w-full rounded-lg shadow-lg p-6 mr-6 space-y-6">
  <!-- Video Section -->
 <div class="w-full">
    {!! $lesson->video_url !!}
</div>

  

  <!-- Comment Section -->
  <div class="mt-6">
    <h3 class="text-xl font-semibold text-gray-800 mb-2">Leave a Comment</h3>
    <form action="#" method="POST" class="space-y-4">
      <textarea 
        class="w-full p-4 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" 
        rows="4" 
        placeholder="Write your comment here..."
      ></textarea>
      <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition">
        Post Comment
      </button>
    </form>
  </div>
</div>


 <!-- Sidebar -->
 <aside class="w-64 bg-white rounded-lg shadow-lg p-4 space-y-8">
  <!-- Product List -->
  <div class="space-y-4">
    <h3 class="text-xl font-semibold text-gray-800 mb-2">Related Products</h3>
    <ul class="space-y-2">
      @foreach ($relatedUploads as $item)
        <li>
        <a href="/member/lesson/{{$item->id}}" class="text-blue-600 hover:underline">{{$item->title}}</a>
      </li>
      @endforeach
     
    </ul>
  </div>

  <!-- Latest Comments -->
  <div class="space-y-4">
    <h3 class="text-xl font-semibold text-gray-800 mb-2">Latest Comments</h3>
    <ul class="space-y-3">
      <li class="flex items-start space-x-3">
        <i class="fa fa-user-circle text-gray-500 text-xl"></i>
        <div>
          <p class="text-gray-800 text-sm">
            <span class="font-semibold">John:</span> "Great tips on finger exercises!"
          </p>
          <span class="text-xs text-gray-500">May 12, 2025</span>
        </div>
      </li>
      <li class="flex items-start space-x-3">
        <i class="fa fa-user-circle text-gray-500 text-xl"></i>
        <div>
          <p class="text-gray-800 text-sm">
            <span class="font-semibold">Emily:</span> "Loved the jazz improvisation section."
          </p>
          <span class="text-xs text-gray-500">May 11, 2025</span>
        </div>
      </li>
      <li class="flex items-start space-x-3">
        <i class="fa fa-user-circle text-gray-500 text-xl"></i>
        <div>
          <p class="text-gray-800 text-sm">
            <span class="font-semibold">Mark:</span> "Helped me improve my playing style!"
          </p>
          <span class="text-xs text-gray-500">May 10, 2025</span>
        </div>
      </li>
    </ul>
  </div>
</aside>

</section>


@endsection