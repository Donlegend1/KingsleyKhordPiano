<section class="container mx-auto px-4 py-12">
  <div class="flex flex-col md:flex-row items-stretch gap-8">
   
    <div class="w-full md:w-3/5 bg-white p-6 rounded-lg shadow-lg">
      <h2 class="text-3xl font-bold text-gray-800 mb-6">Contact Us</h2>
      <form class="space-y-4 h-full" method="POST" action="/contact/send">
        @csrf
        
        <div class="flex flex-col sm:flex-row gap-4">
          <div class="w-full">
            <label class="block text-gray-700 mb-1" for="first_name">First Name</label>
            <input name="first_name" id="first_name" type="text" placeholder="First Name"
                  value="{{ old('first_name') }}"
                  class="w-full border p-3 rounded focus:ring focus:ring-blue-200 @error('first_name') border-red-500 @else border-gray-300 @enderror">
            @error('first_name')
              <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
          </div>
          
          <div class="w-full">
            <label class="block text-gray-700 mb-1" for="last_name">Last Name</label>
            <input name="last_name" id="last_name" type="text" placeholder="Last Name"
                  value="{{ old('last_name') }}"
                  class="w-full border p-3 rounded focus:ring focus:ring-blue-200 @error('last_name') border-red-500 @else border-gray-300 @enderror">
            @error('last_name')
              <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
          </div>
        </div>

        <div>
          <label class="block text-gray-700 mb-1" for="email">Email</label>
          <input name="email" id="email" type="email" placeholder="Your Email"
                value="{{ old('email') }}"
                class="w-full border p-3 rounded focus:ring focus:ring-blue-200 @error('email') border-red-500 @else border-gray-300 @enderror">
          @error('email')
            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label class="block text-gray-700 mb-1" for="subject">Subject</label>
          <input name="subject" id="subject" type="text" placeholder="Message subject here"
                value="{{ old('subject') }}"
                class="w-full border p-3 rounded focus:ring focus:ring-blue-200 @error('subject') border-red-500 @else border-gray-300 @enderror">
          @error('subject')
            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label class="block text-gray-700 mb-1" for="message">Message</label>
          <textarea name="message" id="message" rows="5" placeholder="Your Message"
                    class="w-full border p-3 rounded focus:ring focus:ring-blue-200 @error('message') border-red-500 @else border-gray-300 @enderror">{{ old('message') }}</textarea>
          @error('message')
            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
          @enderror
        </div>

        <button type="submit"
                class="bg-[#FFD736] hover:bg-[#a7923e] text-black font-semibold py-3 px-6 rounded transition-colors">
          Send Message <i class="fa fa-paper-plane ml-2" aria-hidden="true"></i>
        </button>
      </form>

    </div>

    <!-- Image (Right Side) -->
    <div class="w-full md:w-2/5 hidden md:block">
      <img src="/images/banner.png" alt="Contact Us" class="w-full h-full object-cover rounded-lg shadow-lg">
    </div>
  </div>
</section>
