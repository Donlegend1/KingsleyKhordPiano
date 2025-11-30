@extends('layouts.member')

@section('content')
<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-6 px-4">
 <div class="max-w-7xl mx-auto space-y-3">
   
   <!-- Top Row -->
   <div class="flex justify-between items-center">
     <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
       <a href="/home" class="hover:text-blue-600">Dashboard</a>
       <span>/</span>
       <a href="/member/premium-booking" class="hover:text-blue-600 font-semibold">One on One Coaching</a>
     </div>
     <div class="flex items-center space-x-2">
       <i class="fa fa-user-circle text-xl"></i>
     </div>
   </div>

   <!-- Second Row -->
   <div>
     <h1 class="text-xl font-bold">Piano Coaching​</h1>
   </div>

 </div>
</section>

<div class="max-w-[580px] mx-auto">
  <p class="text-center text-red-600 text-2xl font-bold">
    As a premium member, you’re eligible for one free 1-on-1 session every month
  </p>
</div>

<div class="calendly-inline-widget" data-url="https://calendly.com/kingsleykhord/1v1-live-lesson?hide_gdpr_banner=1" style="min-width:320px;height:700px;"></div>


@endsection