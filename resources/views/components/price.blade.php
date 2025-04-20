<section class="container mx-auto px-5 md:px-28 h-[85vh] bg-white">
  <div class="text-center my-12 mx-auto">
    <p class="font-bold text-4xl">Gain Immediate Entry to </p>
    <p class="font-bold text-4xl my-3 text-[#BC1414]">Kingsley Khord Piano Academy</p>
    <p class="text-sm text-gray-500 mx-auto max-w-lg">
      This structured hands-on piano training was created due to high demand and a lack of available in-depth resources and guidance. 
    </p>
  </div>

  <div class="flex flex-col md:flex-row justify-center gap-6">
    <!-- Pricing Card 1 -->
 <div class="bg-white border border-[#C2D3DD73] rounded-xl shadow-lg p-6 w-full md:w-1/3">
  <img src="/icons/price1.png" alt="" class=" mb-4">
  
  <h3 class="text-xl font-semibold text-black mb-4 ">Monthly Membership</h3>
  <p class="text-2xl font-bold mb-2 ">$25 / ₦37,000</p>
  <p class="text-sm border border-gray-100 my-4"></p>

  <ul class="text-sm text-gray-700 mb-6 list-disc list-inside">
    <li>Roadmap for all skill levels</li>
    <li>Premium midi files</li>
    <li>Ear Training Quiz</li>
    <li>Practice Routine</li>
    <li>Supportive Community</li>
  </ul>

  <div class="flex justify-center">
    <button class="w-full bg-white border border-[#C2D3DD73] text-black px-4 py-2 rounded hover:bg-gray-100 transition"
    onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: { plan: 'Monthly Membership', amount: '$25 / ₦37,000' } }))">
      Choose Plan
    </button>
  </div>
</div>
<div class="bg-white border border-[#C2D3DD73] rounded-xl shadow-lg p-6 w-full md:w-1/3"  style="background-image: url('/images/Background.jpg')">
  <img src="/icons/price2.png" alt="" class=" mb-4 p-3 border bg-[#C2D3DD73] rounded-3xl">
  
  <h3 class="text-xl font-semibold text-black mb-4 ">Yearly Membership <span class="mx-5 p-1 rounded-md text-sm bg-gray-200">save $100</span></h3>
  <p class="text-2xl font-bold mb-2 ">$200 / ₦300,000</p>
  <p class="text-sm border border-gray-100 my-4"></p>

  <ul class="text-sm text-gray-700 mb-6 list-disc list-inside">
    <li>Roadmap for all skill levels</li>
    <li>Premium midi files</li>
    <li>Ear Training Quiz</li>
    <li>Practice Routine</li>
    <li>Supportive Community</li>
  </ul>

  <div class="flex justify-center">
    <button class="w-full bg-[#1B223C] border border-[#C2D3DD73] text-white px-4 py-2 rounded hover:bg-gray-700 transition"
     onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: { plan: 'Yearly Membership', amount: '$200 / ₦300,000' } }))">
      Choose Plan
    </button>
  </div>
</div>



<div 
  x-data="{ open: false, plan: '', amount: '' }" 
  x-show="open"
  @open-modal.window="open = true; plan = $event.detail.plan; amount = $event.detail.amount"
  @keydown.escape.window="open = false"
  @click.away="open = false"  
  class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50 py-9"
  x-cloak
  x-transition:enter="transition ease-out duration-300"
  x-transition:enter-start="opacity-0 transform scale-95"
>
  <div class="bg-[#2D2D2D] rounded-xl shadow-xl p-12 w-full max-w-lg mx-auto">
    <!-- Modal Header -->
    <h2 class="text-2xl font-bold text-center text-white mb-2">
      Choose your Preferred Payment Option
    </h2>
    <p class="text-center text-white mb-4" x-text="plan + ' - ' + amount"></p>
    
    <!-- Payment Options -->
    <div class="flex flex-col gap-6">
      <!-- Paystack Option -->
      <a :href="'/paystack-route?plan=' + plan" class="bg-[#FAFAFA] hover:bg-[#e7dfdf] py-3 rounded text-center font-semibold flex items-center justify-center">
        <img src="/icons/paystack.png" alt="Paystack" />
      </a>

      <!-- Stripe Option -->
      <a :href="'/stripe-route?plan=' + plan" class="bg-[#FFD736] hover:bg-[#a7923e] py-3 rounded text-center font-semibold flex items-center justify-center">
        <img src="/icons/stripe.png" alt="Stripe" />
      </a>
    </div>

    <!-- Footer Section -->
    <div class="mt-4 flex items-center justify-center text-sm text-gray-400">
      <p>Powered by 
        <span class="inline-block  bg-gray-300 rounded-md">
          <img src="/icons/stripe2.png" alt="Stripe" /> 
        </span>
        <span class="inline-block  bg-gray-300 rounded-md">
          <img src="/icons/paystack2.png" alt="Paystack"  /> 
        </span>
      </p>
    </div>
  </div>
</div>




  </div>
</section>
