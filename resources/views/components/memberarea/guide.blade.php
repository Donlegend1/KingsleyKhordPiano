<section class="max-w-7xl mx-auto px-4 py-6 
    grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-3 gap-4">

  <!-- Card 1 -->
  <a href="/member/library" class="block transition-shadow hover:shadow-md">
    <div class="min-h-[120px] h-full flex justify-between items-center p-6 bg-[#F3F5F6] rounded-lg shadow-sm border">
      <div class="flex items-center space-x-4">
        <div class="w-12 h-12 flex items-center justify-center bg-[#E8EDF2] rounded-xl">
          <i class="fa fa-book-open text-[#435065] text-xl"></i>
        </div>
        <div>
          <h4 class="font-semibold text-[#435065] text-[20px] font-sf">My Library</h4>
          <p class="text-sm text-[#5E6779] mt-1 font-sf">Access all downloadable files and materials</p>
        </div>
      </div>
      <i class="fa fa-angle-right text-gray-400 text-lg"></i>
    </div>
  </a>

  <!-- Card 2 -->
  {{-- <a href="/member/learn-songs" class="block transition-shadow hover:shadow-md">
    <div class="min-h-[120px] h-full flex justify-between items-center p-6 bg-[#F3F5F6] rounded-lg shadow-sm border">
      <div class="flex items-center space-x-4">
        <img src="/images/music.png" class="w-12 h-12 object-contain" />
        <div>
          <h4 class="font-semibold text-[#435065] text-[20px] font-sf">Learn Songs</h4>
          <p class="text-sm text-[#5E6779] mt-1 font-sf">Master songs like you’ve never done</p>
        </div>
      </div>
      <i class="fa fa-angle-right text-gray-400 text-lg"></i>
    </div>
  </a> --}}

  <!-- Card 3 -->
  <a href="https://discord.gg/gFXnRnaf5N" target="_blank" rel="noopener noreferrer" class="block transition-shadow hover:shadow-md">
    <div class="min-h-[120px] h-full flex justify-between items-center p-6 bg-[#F3F5F6] rounded-lg shadow-sm border">
      <div class="flex items-center space-x-4">
        <img src="/images/community.svg" class="w-12 h-12 object-contain" />
        <div>
          <h4 class="font-semibold text-[#435065] text-[20px] font-sf">Community</h4>
          <p class="text-sm text-[#5E6779] mt-1 font-sf">View the latest community activities</p>
        </div>
      </div>
      <i class="fa fa-angle-right text-gray-400 text-lg"></i>
    </div>
  </a>

  <!-- Card 4 (New) -->
  <div id="premium-call-button"
     data-premium="{{ auth()->user()->premium ? '1' : '0' }}">
  </div>

  

</section>
