<section class="bg-[#F7F3DF] text-gray-800 py-3 px-4 shadow relative">
  <div class="max-w-7xl mx-auto flex items-center justify-between flex-wrap">
    
    <!-- Message -->
    <div class="w-full lg:w-auto text-sm lg:text-base font-medium mb-2 lg:mb-0">
      <span class="block text-right lg:text-left">
        Hey {{ auth()->user()->name }}, it seems you are new here. Take a quick tour to make the most of your experience here.
      </span>
    </div>

    <!-- Buttons -->
    <div class="flex items-center space-x-3 ml-auto">
      <a href="/member/getstarted" class="px-4 py-1.5 bg-[#404348] text-white text-sm rounded-full hover:bg-yellow-400 transition inline-flex items-center">
        Get Started <i class="fa fa-angle-right ml-2" aria-hidden="true"></i>
      </a>
      <form action="/member/getstarted/updated" method="post">
        @csrf
        <button type="submit" class="px-4 py-1.5 bg-transparent border border-[#404348] text-[#404348] text-sm rounded-full hover:bg-yellow-50 transition inline-flex items-center">
          Close <i class="fa fa-times ml-2" aria-hidden="true"></i>
        </button>
      </form>
    </div>
    
  </div>
</section>
