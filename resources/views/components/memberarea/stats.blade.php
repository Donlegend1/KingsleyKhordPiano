<section class="max-w-7xl mx-auto px-4 py-8 grid grid-cols-1 md:grid-cols-2 gap-6">
  <!-- Card 1 -->
<div class="p-8 bg-[#F3F5F6] rounded-lg shadow-md border hover:shadow-lg transition h-full flex flex-col justify-between">
 <div class="flex items-center space-x-5">
  <!-- Icon -->
  <div>
    <img src="/images/progress.svg" alt="Progress Icon" class="w-14 h-14 object-contain" />
  </div>

  <!-- Text Content -->
  <div>
    <h2 class="text-2xl font-bold text-[#435065]">Student Progress</h2>
    <p class="mt-2 text-sm text-[#5E6779]">Pick up from where you left off!</p>
  </div>
</div>

  <div class="font-semibold border-t-2 border-gray-300 my-4"></div>

  @foreach ($categoryProgress as $category)
    <div class="mt-4">
      <label for="progress" class="text-sm text-gray-600">{{ $category['course_category'] }}</label>
      <p class="font-semibold text-[#145CCF]">{{ $category['completed_courses'] }}/{{ $category['total_courses'] }} <span class="text-gray-400">modules in this course</span></p>
      <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
        <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $category['completion_percentage'] }}%"></div>
      </div>
    </div>

    <div class="mt-6 text-left">
      <a href="/member/course/{{ urlencode($category['level']) }}" class="inline-block px-6 py-2 rounded-full bg-transparent border border-[#404348] text-[#404348] transition">
        Continue Learning
        <i class="fa fa-chevron-right ml-2 text-sm text-[#404348]"></i>
      </a>
    </div>
  @endforeach
</div>


 <!-- Card 2 -->
 <div class="p-8 bg-[#F3F5F6] rounded-lg shadow-md border hover:shadow-lg transition h-full flex flex-col justify-between">
 <div class="flex items-center space-x-5">
  <!-- Icon -->
  <div>
    <img src="/images/engagement.png" alt="Progress Icon" class="w-14 h-14 object-contain" />
  </div>

  <!-- Text Content -->
  <div>
    <h2 class="text-2xl font-bold text-[#435065] font-sf">Student Engagement</h2>
    <p class="mt-2 text-sm text-[#5E6779] font-sf">Select one of the options below to get engaged in the Kingsley Khord piano community.</p>
  </div>
</div>

 <section class="mt-5 grid grid-cols-1 gap-2">
  <!-- Card 1 -->
  <div onclick="openModal('qaModal')" class="cursor-pointer">
    <div class="p-4 bg-white rounded-lg shadow-md border hover:shadow-lg transition mb-4 flex justify-between items-center">
      <div>
        <h4 class="font-bold text-[#435065] font-sf">Question & Answer</h4>
        <p class="text-sm text-[#5E6779] font-sf mt-2">Submit a question and get answers from the community</p>
      </div>
      <i class="fa fa-angle-right text-[#5E6779] text-sm"></i>
    </div>
  </div>

  <!-- Card 2 -->
  <div onclick="openModal('requestModal')" class="cursor-pointer">
    <div class="p-4 bg-white rounded-lg shadow-md border hover:shadow-lg transition mb-4 flex justify-between items-center">
      <div>
        <h4 class="font-bold text-[#435065] font-sf">Course Request</h4>
        <p class="text-sm text-[#5E6779] font-sf mt-2">Request for your needed course here</p>
      </div>
      <i class="fa fa-angle-right text-[#5E6779] text-sm"></i>
    </div>
  </div>

  <!-- Card 3 -->
  <div onclick="openModal('progressModal')" class="cursor-pointer">
    <div class="p-4 bg-white rounded-lg shadow-md border hover:shadow-lg transition flex justify-between items-center">
      <div>
        <h4 class="font-bold text-[#435065] font-sf">Student Progress</h4>
        <p class="text-sm text-[#5E6779] font-sf mt-2">Pick up from where you left off</p>
      </div>
      <i class="fa fa-angle-right text-[#5E6779] text-sm"></i>
    </div>
  </div>
</section>

<!-- Modals -->
@foreach ([
    ['id' => 'qaModal', 'title' => 'Question & Answer', 'body' => 'You can ask questions here and get support from instructors and the community.', 'link' =>'#',
     'text'=> 'Stock on something or just curious or just curious about a concept? Drop your question here and get thoughtful practical answers from fellow learners and instructors who have likely walked that same path. 
    Join a community of real conversations, shared struggles and helpful insight'],
    ['id' => 'requestModal', 'title' => 'Course Request', 'body' => 'Submit your request for a new course and we’ll review it for future updates.', 'link' =>'#', 'text'=> 
    'Have a course idea or something specific you are eager to learn? This is where you can let us know. We are always building with you in mind and your request might just be in the next lesson we create.'],
    ['id' => 'progressModal', 'title' => 'Student Progress', 'body' => 'Track your learning journey and resume courses from where you stopped.', 'link' =>'#', 'text'=> 'Whether it is a small or a big breakthrough, share your progress here it help others see what is possible and give you the changes to get encourage,
     support and honest feedback from a community that is rooting for you']
] as $modal)
  <div id="{{ $modal['id'] }}" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="relative bg-white rounded-lg shadow-lg max-w-2xl w-full p-6">
      <!-- Close Icon -->
      <button onclick="closeModal('{{ $modal['id'] }}')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition text-xl font-bold">
        &times;
      </button>

      <!-- Modal Content -->
      <div class="flex flex-col gap-6">
        <div>
          <h3 class=" font-bold text-[#435065] text-[20px] font-sf">{{ $modal['title'] }}</h3>
          <p class="text-sm  mt-5 text-[#5E6779] font-sf">{{ $modal['text'] }}</p>
        </div>
       <div class="mt-3 text-left">
      <a href="{{ $modal['link'] }}" class="inline-block px-6 py-2 bg-black rounded-full border border-[#404348] text-white hover:bg-yellow-500 hover:text-black transition">
        Click here
        <i class="fa fa-chevron-right ml-2 text-sm"></i>
      </a>
    </div>
      </div>
    </div>
  </div>
@endforeach


<!-- Scripts -->
<script>
  function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
  }

  function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
  }
</script>


</div>

</section>
