<section class="container mx-auto px-4 py-16">
  <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- Book a 1-on-1 Lesson Card --}}
    <div class="bg-white rounded-3xl shadow-md p-8 flex flex-col items-center text-center">
      <div class="w-40 h-40 mb-6 flex items-center justify-center">
        <img src="{{ asset('images/book.png') }}" alt="1-on-1 Lesson" class="w-full h-full object-contain" />
      </div>
      <h2 class="text-2xl font-extrabold text-gray-900 mb-3">Book a 1-on-1 Lesson</h2>
      <p class="text-gray-500 text-sm leading-relaxed mb-6">
        Get personalized guidance with private<br class="hidden sm:block" /> one-on-one coaching from an expert.
      </p>
      <a
        href="https://tr.ee/kingsleykhordbooking"
        target="_blank"
        rel="noopener noreferrer"
        class="mt-auto w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-4 px-6 rounded-2xl flex items-center justify-center gap-2 transition-colors duration-200"
      >
        Book My Session
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
        </svg>
      </a>
    </div>

    {{-- Download Free Roadmap Card --}}
    <div class="bg-white rounded-3xl shadow-md p-8 flex flex-col items-center text-center">
      <div class="w-40 h-40 mb-6 flex items-center justify-center">
        <img src="{{ asset('images/road.png') }}" alt="Free Roadmap" class="w-full h-full object-contain" />
      </div>
      <h2 class="text-2xl font-extrabold text-gray-900 mb-3">Download Free Roadmap</h2>
      <p class="text-gray-500 text-sm mb-6 leading-relaxed">
        Get your free roadmap and start your<br class="hidden sm:block" /> journey in the right direction.
      </p>
      <form class="mt-auto w-full flex flex-col gap-3" action="{{ route('subscribe') }}" method="POST">
        @csrf
        <div class="flex items-center border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus-within:border-[#5B4FCF] transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
          </svg>
          <input
            name="email"
            type="email"
            placeholder="Enter your email"
            required
            class="flex-1 bg-transparent text-sm text-gray-700 placeholder-gray-400 focus:outline-none"
          />
        </div>
        <button
          type="submit"
          class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-4 px-6 rounded-2xl transition-colors duration-200"
        >
          Get It Now
        </button>
      </form>
    </div>

  </div>
</section>
