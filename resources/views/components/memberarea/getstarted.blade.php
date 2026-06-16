<div x-data="{ isOpen: true }" x-show="isOpen" class="fixed inset-0 z-[99999] flex items-center justify-center overflow-y-auto px-4 py-6" x-cloak x-transition>
  <!-- Backdrop -->
  <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="isOpen = false"></div>

  <!-- Modal Content -->
  <div class="relative bg-white dark:bg-gray-800 rounded-3xl max-w-lg w-full p-8 shadow-2xl border border-gray-100 dark:border-gray-700 text-center transform transition-all overflow-hidden" 
       x-show="isOpen" 
       x-transition:enter="ease-out duration-300" 
       x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
       x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
     
     <!-- Close Button -->
     <button @click="isOpen = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors" aria-label="Close modal">
        <i class="fa-solid fa-xmark text-lg"></i>
     </button>

     <!-- Decorative Magic Sparkles Icon -->
     <div class="mx-auto w-16 h-16 bg-gradient-to-tr from-yellow-400 to-[#FFD736] text-slate-900 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-yellow-400/20">
        <i class="fa-solid fa-wand-magic-sparkles text-2xl"></i>
     </div>

     <!-- Title & Description -->
     <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
        Welcome to KingsleyKhord!
     </h3>
     <p class="text-sm text-gray-500 dark:text-gray-400 mb-8 leading-relaxed max-w-sm mx-auto">
        Hey <span class="font-bold text-gray-900 dark:text-white">{{ auth()->user()->first_name ?: auth()->user()->name }}</span>, it seems you are new here. Take a quick tour to make the most of your experience.
     </p>

     <!-- Action Buttons -->
     <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
        <a href="/member/getstarted" class="w-full sm:w-auto px-8 py-3 bg-[#404348] dark:bg-[#FFD736] hover:bg-black dark:hover:bg-yellow-400 text-white dark:text-black font-bold rounded-xl transition duration-200 shadow-md flex items-center justify-center gap-1.5 text-sm">
           Get Started <i class="fa fa-angle-right" aria-hidden="true"></i>
        </a>
        <form action="/member/getstarted/updated" method="post" class="w-full sm:w-auto">
           @csrf
           <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-transparent border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium rounded-xl transition duration-200 flex items-center justify-center gap-1.5 text-sm">
              Don't show again <i class="fa fa-times" aria-hidden="true"></i>
           </button>
        </form>
     </div>
  </div>
</div>
