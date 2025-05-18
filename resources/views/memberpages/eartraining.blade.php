@extends('layouts.member')

@section('content')
<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-6 px-4">
 <div class="max-w-7xl mx-auto space-y-3">
   
   <!-- Top Row -->
   <div class="flex justify-between items-center">
     <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
       <a href="/home" class="hover:text-blue-600">Dashboard</a>
       <span>/</span>
       <a href="/member/ear-training" class="hover:text-blue-600 font-semibold">Ear Training</a>
     </div>
     <div class="flex items-center space-x-2">
       <i class="fa fa-user-circle text-xl"></i>
     </div>
   </div>

   <!-- Second Row -->
   <div>
     <h1 class="text-xl font-bold">Ear Training</h1>
   </div>

 </div>
</section>
<section class="bg-gray-100 py-12 px-6">
 <div class="max-w-7xl mx-auto">
   <h2 class="text-2xl font-bold mb-8 text-center">Ear Training Quiz</h2>

   <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
     
     <!-- Card 1 -->
     <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
       <img src="/icons/eartraining1.webp" alt="Finger Strengthening" class="w-full  object-cover">
       <div class="p-6 text-center">
         <h3 class="text-lg font-bold mb-2 text-gray-800">Relative Pitch (Part One)</h3>
         <p class="text-gray-600 mb-4">Ear Training Quize</p>
         <a href="#" class="text-blue-500 hover:bg-black hover:text-white font-semibold border border-blue-600 p-2 rounded-lg">START QUIZ</a>
       </div>
     </div>

     <!-- Card 1 -->
     <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
      <img src="/icons/eartraining2.webp" alt="Finger Strengthening" class="w-full  object-cover">
      <div class="p-6 text-center">
        <h3 class="text-lg font-bold mb-2 text-gray-800">Relative Pitch (Part One)</h3>
        <p class="text-gray-600 mb-4">Ear Training Quize</p>
        <a href="#" class="text-blue-500 hover:bg-black hover:text-white font-semibold border border-blue-600 p-2 rounded-lg">START QUIZ</a>
      </div>
    </div>
    <!-- Card 1 -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
     <img src="/icons/eartraining3.webp" alt="Finger Strengthening" class="w-full  object-cover">
     <div class="p-6 text-center">
       <h3 class="text-lg font-bold mb-2 text-gray-800">Relative Pitch (Part One)</h3>
       <p class="text-gray-600 mb-4">Ear Training Quize</p>
       <a href="#" class="text-blue-500 hover:bg-black hover:text-white font-semibold border border-blue-600 p-2 rounded-lg">START QUIZ</a>
     </div>
   </div>
   <!-- Card 1 -->
   <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
    <img src="/icons/eartraining4.webp" alt="Finger Strengthening" class="w-full  object-cover">
    <div class="p-6 text-center">
      <h3 class="text-lg font-bold mb-2 text-gray-800">Relative Pitch (Part One)</h3>
      <p class="text-gray-600 mb-4">Ear Training Quize</p>
      <a href="#" class="text-blue-500 hover:bg-black hover:text-white font-semibold border border-blue-600 p-2 rounded-lg">START QUIZ</a>
    </div>
  </div>
  <!-- Card 1 -->
  <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
   <img src="/icons/eartraining5.webp" alt="Finger Strengthening" class="w-full  object-cover">
   <div class="p-6 text-center">
     <h3 class="text-lg font-bold mb-2 text-gray-800">Relative Pitch (Part One)</h3>
     <p class="text-gray-600 mb-4">Ear Training Quize</p>
     <a href="#" class="text-blue-500 hover:bg-black hover:text-white font-semibold border border-blue-600 p-2 rounded-lg">START QUIZ</a>
   </div>
 </div>
 <!-- Card 1 -->
 <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
  <img src="/icons/eartraining6.webp" alt="Finger Strengthening" class="w-full  object-cover">
  <div class="p-6 text-center">
    <h3 class="text-lg font-bold mb-2 text-gray-800">Relative Pitch (Part One)</h3>
    <p class="text-gray-600 mb-4">Ear Training Quize</p>
    <a href="#" class="text-blue-500 hover:bg-black hover:text-white font-semibold border border-blue-600 p-2 rounded-lg">START QUIZ</a>
  </div>
</div>


   </div>
 </div>
</section>


@endsection