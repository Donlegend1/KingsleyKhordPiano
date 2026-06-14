<section class="bg-[#F7F3DF] dark:bg-[#2A2920] border-b border-[#EADFBA] dark:border-[#423E2A] text-gray-800 dark:text-gray-200 py-3 px-4 shadow-sm relative transition-colors duration-200">
  <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center justify-between gap-3">
    
    <!-- Message -->
    <div class="flex items-center space-x-3 flex-grow min-w-0">
      <div class="p-1.5 bg-[#EADFBA] dark:bg-[#423E2A] rounded-lg text-gray-700 dark:text-gray-300 flex-shrink-0">
        <i class="fa-solid fa-wand-magic-sparkles text-sm"></i>
      </div>
      <p class="text-sm font-medium text-gray-700 dark:text-gray-300 leading-snug">
        Hey <span class="font-bold text-gray-900 dark:text-white">{{ auth()->user()->name }}</span>, it seems you are new here. Take a quick tour to make the most of your experience here.
      </p>
    </div>

    <!-- Buttons -->
    <div class="flex items-center space-x-3 ml-auto md:ml-0 flex-shrink-0">
      <a href="/member/getstarted" class="px-4 py-1.5 bg-[#404348] dark:bg-[#FFD736] text-white dark:text-black text-sm font-bold rounded-full hover:bg-yellow-400 dark:hover:bg-yellow-300 transition-all duration-200 shadow-sm inline-flex items-center whitespace-nowrap">
        Get Started <i class="fa fa-angle-right ml-1.5" aria-hidden="true"></i>
      </a>
      <form action="/member/getstarted/updated" method="post" class="inline-flex">
        @csrf
        <button type="submit" class="px-4 py-1.5 bg-transparent border border-[#404348] dark:border-gray-500 text-[#404348] dark:text-gray-300 text-sm font-medium rounded-full hover:bg-[#EADFBA] dark:hover:bg-gray-800 transition-all duration-200 inline-flex items-center whitespace-nowrap">
          Close <i class="fa fa-times ml-1.5" aria-hidden="true"></i>
        </button>
      </form>
    </div>
    
  </div>
</section>
