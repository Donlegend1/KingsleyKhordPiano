<section class="container mx-auto px-4 py-12">
  <div class="flex flex-col md:flex-row items-stretch gap-8">
   
    <div class="w-full md:w-3/5 bg-white p-6 rounded-lg shadow-lg">
      <h2 class="text-3xl font-bold text-gray-800 mb-6">Contact Us</h2>
      <form class="space-y-4 h-full">
        <div class="flex flex-col sm:flex-row gap-4">
          <div class="w-full">
            <label class="block text-gray-700 mb-1" for="first_name">First Name</label>
            <input id="first_name" type="text" placeholder="First Name" class="w-full border border-gray-300 p-3 rounded focus:ring focus:ring-blue-200">
          </div>
          <div class="w-full">
            <label class="block text-gray-700 mb-1" for="last_name">Last Name</label>
            <input id="last_name" type="text" placeholder="Last Name" class="w-full border border-gray-300 p-3 rounded focus:ring focus:ring-blue-200">
          </div>
        </div>
        <div>
          <label class="block text-gray-700 mb-1" for="email">Email</label>
          <input id="email" type="email" placeholder="Your Email" class="w-full border border-gray-300 p-3 rounded focus:ring focus:ring-blue-200">
        </div>
        <div>
          <label class="block text-gray-700 mb-1" for="email">Subject</label>
          <input id="subject" type="text" placeholder="Message subject here" class="w-full border border-gray-300 p-3 rounded focus:ring focus:ring-blue-200">
        </div>
        <div>
          <label class="block text-gray-700 mb-1" for="message">Message</label>
          <textarea id="message" rows="5" placeholder="Your Message" class="w-full border border-gray-300 p-3 rounded focus:ring focus:ring-blue-200"></textarea>
        </div>
        <button type="submit" class="bg-[#FFD736] hover:bg-[#a7923e] text-black font-semibold py-3 px-6 rounded transition-colors">
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
