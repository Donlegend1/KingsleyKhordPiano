@extends('layouts.member')

@section('content')
<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-6 px-4">
 <div class="max-w-7xl mx-auto space-y-3">
   
   <!-- Top Row -->
   <div class="flex justify-between items-center">
     <div class="flex items-center gap-2 text-sm text-gray-500">
       <a href="/home" class="hover:text-gray-700">Dashboard</a>
       <span>/</span>
       <span class="text-[#6366F1] font-medium">Live Session</span>
     </div>
     <div class="flex items-center space-x-2">
       <i class="fa fa-user-circle text-xl"></i>
     </div>
   </div>

   <!-- Second Row -->
   {{-- <div>
     <h1 class="text-xl font-bold">Live Session</h1>
   </div> --}}

 </div>
</section>

<section class="bg-gray-100">

<div id="live-show-page">

</div>
</section>

@endsection