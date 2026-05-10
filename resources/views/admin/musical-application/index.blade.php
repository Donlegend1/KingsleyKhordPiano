@extends('layouts.admin')

@section('content')

 <main class="flex-1 p-6">
   <header class="mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
    <div>
      <h2 class="text-xl font-bold text-gray-800 mb-1">Musical Application</h2>
      <a href="{{ route('admin.musical-application.index') }}" class="text-[#0FA9A0] hover:underline text-sm">Exercises</a>
    </div>
    
    <div class="flex items-center gap-3 flex-wrap w-full md:w-auto">
      <input 
        type="text" 
        placeholder="Search..." 
        class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0FA9A0] focus:outline-none"
      >
      <button class="relative bg-gray-100 text-gray-600 px-3 py-2 rounded-lg hover:bg-gray-200">
        <i class="fa fa-bell"></i>
        <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
      </button>
      <div class="w-10 h-10 bg-gray-300 rounded-full overflow-hidden flex items-center justify-center text-gray-500 text-sm">
        <i class="fa fa-user"></i>
      </div>
    </div>
  </header>

     <div id="musical-applications">
        <!-- React component will be mounted here -->
     </div>
   </main>

@endsection
