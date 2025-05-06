<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-6 px-4 ">
    <div class="max-w-7xl mx-auto space-y-3">
        
        <!-- Top Row -->
        <div class="flex justify-between items-center">
            <h1 class="text-xl font-bold">Dashboard</h1>
            <div class="flex items-center space-x-2">
                <i class="fa fa-user-circle text-xl "></i>
                <span class="text-sm font-medium">{{ auth()->user()->name }}</span>
            </div>
        </div>

        <!-- Second Row -->
        <div class="flex justify-between items-center">
            <p class="text-sm lg:text-base font-medium text-[#404348]">
                Welcome back, <span class="font-semibold text-black">{{ auth()->user()->name }}</span>
            </p>
            <div class="flex items-center space-x-2 text-sm cursor-pointer px-3 py-2 rounded-md border border-gray-200 shadow-sm bg-[#EDEFF2] hover:shadow-md hover:bg-gray-50 transition">
              <i class="fa fa-bookmark "></i>
              <span class="font-medium text-gray-800">My Bookmarks</span>
          </div>

        </div>

    </div>
</section>
