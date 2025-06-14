@extends('layouts.admin')

@section('content')

 <main class="flex-1 p-6">
    <header class="mb-6 flex items-center justify-between">
     <div>
      <h2 class="text-xl font-bold">Dashboard</h2>
      <a href="/dashboard/ear-training">Ear Traning</a>
     </div>
      
      <div class="flex items-center gap-4">
        <input type="text" placeholder="Search..." class="px-4 py-2 border rounded-lg">
        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
          <i class="fa fa-bell"></i>
        </button>
        <div class="w-10 h-10 bg-gray-300 rounded-full">
         
        </div>
      </div>
    </header>

     <div id="ear-training">

     </div>
   </main>

@endsection