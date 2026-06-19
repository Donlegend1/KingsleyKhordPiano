<section class="mx-auto px-4 py-16 max-w-2xl">
  <div class="bg-white p-8 sm:p-12 rounded-2xl border border-gray-100">
    <div class="text-center mb-8">
      <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">
        Get in Touch with Us
      </h2>
      <p class="mt-2 text-sm text-gray-500 leading-relaxed">
        Our friendly team would love to hear from you.
      </p>
    </div>

    <form class="space-y-4" method="POST" action="/contact/send">
      @csrf

      <!-- Name Fields -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label for="first_name" class="block text-sm font-semibold text-gray-700 mb-1.5">
            First Name
          </label>
          <input
            type="text"
            id="first_name"
            name="first_name"
            placeholder="First Name"
            value="{{ old('first_name') }}"
            class="w-full border border-gray-200 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition @error('first_name') border-red-500 @enderror"
          />
          @error('first_name')
            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label for="last_name" class="block text-sm font-semibold text-gray-700 mb-1.5">
            Last Name
          </label>
          <input
            type="text"
            id="last_name"
            name="last_name"
            placeholder="Last Name"
            value="{{ old('last_name') }}"
            class="w-full border border-gray-200 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition @error('last_name') border-red-500 @enderror"
          />
          @error('last_name')
            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
          @enderror
        </div>
      </div>

      <!-- Email -->
      <div>
        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">
          Email
        </label>
        <input
          type="email"
          id="email"
          name="email"
          placeholder="Your Email"
          value="{{ old('email') }}"
          class="w-full border border-gray-200 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition @error('email') border-red-500 @enderror"
        />
        @error('email')
          <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Subject -->
      <div>
        <label for="subject" class="block text-sm font-semibold text-gray-700 mb-1.5">
          Subject
        </label>
        <input
          type="text"
          id="subject"
          name="subject"
          placeholder="Message subject here"
          value="{{ old('subject') }}"
          class="w-full border border-gray-200 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition @error('subject') border-red-500 @enderror"
        />
        @error('subject')
          <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Message -->
      <div>
        <label for="message" class="block text-sm font-semibold text-gray-700 mb-1.5">
          Message
        </label>
        <textarea
          id="message"
          name="message"
          rows="5"
          placeholder="Your Message"
          class="w-full border border-gray-200 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition resize-none @error('message') border-red-500 @enderror"
        >{{ old('message') }}</textarea>
        @error('message')
          <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- reCAPTCHA -->
      <div class="pt-1">
        {!! NoCaptcha::display() !!}
        @error('g-recaptcha-response')
          <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Button -->
      <button
        type="submit"
        class="w-full flex items-center justify-center gap-2 bg-gray-900 hover:bg-gray-800 text-white font-semibold py-3 rounded-lg transition-colors mt-2"
      >
        Send Message <i class="fa fa-paper-plane text-sm"></i>
      </button>
    </form>
  </div>
</section>
