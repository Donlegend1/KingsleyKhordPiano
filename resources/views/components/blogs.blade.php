<section class="py-16 bg-gray-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="flex flex-col sm:flex-row justify-between items-center mb-10">
      <div class="text-center sm:text-left max-w-2xl">
        <h2 class="text-3xl sm:text-4xl font-bold mb-2">Latest Courses</h2>
        <p class="text-gray-600">
          Explore tips, techniques, and insights to grow your gospel piano mastery and musical journey.
        </p>
      </div>

      <a href="/member/extra-courses" class="mt-4 sm:mt-0 inline-block bg-black text-white px-5 py-2 rounded-lg shadow hover:bg-yellow-600 transition">
        View All
      </a>
    </div>

    <div class="bg-white p-6 sm:p-10 rounded-xl shadow-lg">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @foreach ($extracourses as $blog)
          <div class="bg-white rounded-lg border border-gray-200 overflow-hidden group hover:shadow-lg transition">
            <div class="w-full h-48 overflow-hidden">
              <img src="/storage/{{ $blog->thumbnail }}" alt="{{ $blog->title }}"
                   class="w-full h-full object-cover transition duration-300 group-hover:scale-105" />
            </div>
            <div class="p-5">
              <h3 class="text-lg font-semibold mb-2 group-hover:text-yellow-500 transition">{{ $blog->title }}</h3>
              <p class="text-sm text-gray-600 mb-4">{{ $blog['excerpt'] }}</p>
              <a href="member/lesson/{{$blog->id}}" class="text-sm font-medium text-yellow-600 hover:underline">View <span class="fa fa-eye"></span></a>
            </div>
          </div>
        @endforeach
      </div>
    </div>

  </div>
</section>
