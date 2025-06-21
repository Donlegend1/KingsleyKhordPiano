<!-- Desktop Section -->
<section class="hidden md:block container mx-auto my-12">
  <div class="max-w-5xl mx-auto bg-[#0068DF] py-16 px-12 rounded-full flex items-center justify-between w-full">
    <div>
      <h2 class="text-5xl font-bold text-white">Download Free Roadmap</h2>
    </div>
    <form class="relative w-full max-w-md" action="{{ route('subscribe') }}" method="POST">
      @csrf
      <input 
       name="email"
        type="email" 
        placeholder="Enter your email" 
        class="w-full px-6 py-3 pr-36 rounded-full text-sm focus:outline-none"
      />
      <div class="mt-9 ml-2">
       <button 
        type="submit"
        class="absolute top-1/2 right-2 -translate-y-1/2 bg-[#FFD736] hover:bg-[#c2ab39] text-black font-bold px-6 py-2 rounded-full text-sm"
      >
        Get it now
      </button>
      </div>
    
    </form>
  </div>
</section>

<!-- Mobile Section -->
<section class="block md:hidden container mx-auto bg-cover bg-center  px-4 my-5 py-auto">
  <div class="max-w-5xl mx-auto bg-[#0068DF] py-12 px-6 rounded-3xl flex flex-col items-center justify-between gap-6">
    <div class="text-center">
      <h2 class="text-3xl font-bold text-white">Download Free Roadmap</h2>
    </div>
    <form class="w-full flex flex-col sm:flex-row items-center gap-4" action="{{ route('subscribe') }}" method="POST">
      @csrf
      <input 
        name="email"
        type="email" 
        placeholder="Enter your email" 
        class="w-full px-4 py-3 pl-6 rounded-full text-sm focus:outline-none"
      />
      <button 
        type="submit"
        class="bg-[#FFD736] hover:bg-[#c2ab39] text-black font-bold py-3 px-6 rounded-full w-full sm:w-auto"
      >
        Get it now
      </button>
    </form>
  </div>
</section>
