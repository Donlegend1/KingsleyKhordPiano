<section class="mx-auto px-4 py-16 max-w-4xl" >
  <div class="bg-white p-8 rounded-2xl shadow-xl border border-gray-100">
    <div class="text-center mb-8">
      <h2 class="text-3xl md:text-4xl font-extrabold text-gray-800 mb-2">
        Get in Touch with Us
      </h2>
      <p class="text-sm text-gray-500">
        Our friendly team would love to hear from you.
      </p>
    </div>

    <form class="space-y-6" method="POST" action="/contact/send">
      @csrf

      <!-- Name Fields -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div>
          <label for="first_name" class="block text-gray-700 mb-1 font-medium">
            First Name
          </label>
          <input
            type="text"
            id="first_name"
            name="first_name"
            placeholder="First Name"
            value="{{ old('first_name') }}"
            class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-yellow-300 focus:border-yellow-400 transition @error('first_name') border-red-500 @enderror"
          />
          @error('first_name')
            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label for="last_name" class="block text-gray-700 mb-1 font-medium">
            Last Name
          </label>
          <input
            type="text"
            id="last_name"
            name="last_name"
            placeholder="Last Name"
            value="{{ old('last_name') }}"
            class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-yellow-300 focus:border-yellow-400 transition @error('last_name') border-red-500 @enderror"
          />
          @error('last_name')
            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
          @enderror
        </div>
      </div>

      <!-- Email -->
      <div>
        <label for="email" class="block text-gray-700 mb-1 font-medium">
          Email
        </label>
        <input
          type="email"
          id="email"
          name="email"
          placeholder="Your Email"
          value="{{ old('email') }}"
          class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-yellow-300 focus:border-yellow-400 transition @error('email') border-red-500 @enderror"
        />
        @error('email')
          <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Subject -->
      <div>
        <label for="subject" class="block text-gray-700 mb-1 font-medium">
          Subject
        </label>
        <input
          type="text"
          id="subject"
          name="subject"
          placeholder="Message subject here"
          value="{{ old('subject') }}"
          class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-yellow-300 focus:border-yellow-400 transition @error('subject') border-red-500 @enderror"
        />
        @error('subject')
          <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Message -->
      <div>
        <label for="message" class="block text-gray-700 mb-1 font-medium">
          Message
        </label>
        <textarea
          id="message"
          name="message"
          rows="5"
          placeholder="Your Message"
          class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-yellow-300 focus:border-yellow-400 transition @error('message') border-red-500 @enderror"
        >{{ old('message') }}</textarea>
        @error('message')
          <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- reCAPTCHA -->
      <div class="mt-4">
        {!! NoCaptcha::display() !!}
        @error('g-recaptcha-response')
          <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Button -->
      <div class="text-center">
        <button
          type="submit"
          class="bg-[#FFD736] hover:bg-[#d6b937] text-black font-semibold py-3 px-8 rounded-lg shadow-md transition-transform transform hover:-translate-y-1"
        >
          Send Message <i class="fa fa-paper-plane ml-2"></i>
        </button>
      </div>
    </form>
  </div>
</section>
