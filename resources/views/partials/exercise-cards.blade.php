@forelse ($exercises as $exercise)
  <div class="bg-white p-4 md:p-6 rounded-lg shadow flex flex-col items-center space-y-4">
    <img src="{{ $exercise->thumbnail_url }}" alt="{{ $exercise->title }}" class="w-full h-56 object-cover rounded-md">
    <h3 class="font-bold text-gray-800 text-center">{{ $exercise->title }}</h3>
    <a href="/member/lesson/{{ $exercise->id }}"
       class="border border-black px-4 py-2 rounded-lg hover:bg-black hover:text-white transition text-center w-full">
      Watch Now
    </a>
  </div>
@empty
  <div class="col-span-full text-center text-gray-500 py-12">No exercises found for this category.</div>
@endforelse
