@extends('layouts.member')

@section('content')
<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-4 px-4 border-b border-gray-150 dark:border-gray-800">
 <div class="max-w-7xl mx-auto flex items-center h-8 gap-2 text-sm text-gray-500">
   <a href="/home" class="hover:text-gray-700">Dashboard</a>
   <span>/</span>
   <span class="text-[#C85A5A] font-medium">Events</span>
 </div>
</section>

<section class="bg-gray-100">

<div id="live-show-page"
     @if(session('success')) data-flash-success="{{ session('success') }}" @endif
     @if(session('info')) data-flash-info="{{ session('info') }}" @endif
     @if(session('error')) data-flash-error="{{ session('error') }}" @endif
>

</div>
</section>

@endsection