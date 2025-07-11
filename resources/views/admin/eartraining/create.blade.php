@extends('layouts.admin')

@section('content')

 <main class="flex-1 p-6">
   <header class="mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
    <div>
      <h2 class="text-xl font-bold text-gray-800 mb-1">Dashboard</h2>
      <a href="/admin/ear-training">Ear training</a>
      <a href="/admin/ear-training/create" class="text-blue-600 hover:underline text-sm">Create</a>
    </div>
    
    <div class="flex items-center gap-3 flex-wrap w-full md:w-auto">
      <input 
        type="text" 
        placeholder="Search..." 
        class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none"
      >
      <button class="relative bg-blue-600 text-white px-3 py-2 rounded-lg hover:bg-blue-700">
        <i class="fa fa-bell"></i>
        <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
      </button>
      <div class="w-10 h-10 bg-gray-300 rounded-full overflow-hidden flex items-center justify-center text-gray-500 text-sm">
        <i class="fa fa-user"></i>
      </div>
    </div>
  </header>


     <div id="ear-training-quiz">

     </div>
   </main>

@endsection