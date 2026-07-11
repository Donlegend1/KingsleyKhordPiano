@extends('layouts.member')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4">
    {{-- Page Title --}}
    <div class="my-5">
 <h2 class="text-2xl font-bold text-gray-800 mb-6">Support Center</h2>
    <p>Experiencing difficulties with your account? Are you having trouble navigating the website? Need help with your account? Refer to the below FAQs for a fast and convenient solution.</p>

    </div>
   
    {{-- FAQs --}}
      <div x-data class="bg-white p-6 rounded-2xl shadow-md mb-10">
    <h3 class="text-xl font-semibold text-gray-800 mb-4">Frequently Asked Questions</h3>

    <div class="space-y-4">
        {{-- FAQ Item 1 --}}
         <div x-data="{ open: false }" class="border-b pb-2">
            <button @click="open = !open" class="w-full text-left flex justify-between items-center text-gray-700 font-medium">
                <span>How Can I cancel my account?</span>
                <svg :class="open ? 'rotate-180' : ''" class="h-5 w-5 text-gray-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <p x-show="open" x-transition class="text-sm text-gray-600 mt-2">To terminate your account, visit your 
             <a href="/member/profile" class="text-blue-500">account page </a>
             
             and click on the Subscriptions tab. You will find a ‘Cancel’ option next to your existing subscription</p>
        </div>

         <div x-data="{ open: false }" class="border-b pb-2">
            <button @click="open = !open" class="w-full text-left flex justify-between items-center text-gray-700 font-medium">
                <span>My payment has failed, please what should I do?</span>
                <svg :class="open ? 'rotate-180' : ''" class="h-5 w-5 text-gray-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <p x-show="open" x-transition class="text-sm text-gray-600 mt-2">
             Such occurrences are common! However, rest assured that your account will remain active unless your payment fails a total of 4 times.

            If you update your credit/debit card information, you can find the ‘Update Card’ button on your
            <a href="/member/profile" class="text-blue-500">account page </a>

             under the Subscriptions tab. As soon as you update your card information, your payment will be processed right away.

            Please make sure there are sufficient funds in your account to ensure successful payment upon retry.

            Our payment processors will attempt to process payment again 4-5 days after a failed payment. However, your membership access will be suspended until payment goes through successfully. We will make 4 attempts to collect payment before automatically cancelling your account.

            If your membership is revoked because of payment failures, you must create a new subscription to restore access to your membership.   
             </p>
        </div>
        <div x-data="{ open: false }" class="border-b pb-2">
            <button @click="open = !open" class="w-full text-left flex justify-between items-center text-gray-700 font-medium">
                <span>How can I reset my password?</span>
                <svg :class="open ? 'rotate-180' : ''" class="h-5 w-5 text-gray-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <p x-show="open" x-transition class="text-sm text-gray-600 mt-2">Go to your <a href="/member/profile" class="text-blue-500">account page </a> and follow the prompts.</p>
        </div>

        {{-- FAQ Item 2 --}}
        <div x-data="{ open: false }" class="border-b pb-2">
            <button @click="open = !open" class="w-full text-left flex justify-between items-center text-gray-700 font-medium">
                <span>Where can I view my subscription details?</span>
                <svg :class="open ? 'rotate-180' : ''" class="h-5 w-5 text-gray-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <p x-show="open" x-transition class="text-sm text-gray-600 mt-2">Check the Subscription section in your profile page to view or manage your plan.</p>
        </div>

        {{-- FAQ Item 3 --}}
        <div x-data="{ open: false }" class="border-b pb-2">
            <button @click="open = !open" class="w-full text-left flex justify-between items-center text-gray-700 font-medium">
                <span>How do I contact support directly?</span>
                <svg :class="open ? 'rotate-180' : ''" class="h-5 w-5 text-gray-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <p x-show="open" x-transition class="text-sm text-gray-600 mt-2">Use the form below to send us a message, and our team will respond via email.</p>
        </div>
    </div>
</div>

<div class="my-5">
 <p class="text-xl font-sf">If you have any questions not addressed above, please use the form below to contact me and I will respond as soon as possible. My goal is to respond to every email within a day.</p>
</div>


    {{-- Contact Support Form --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
                <i class="fa-regular fa-paper-plane text-indigo-600 text-sm"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900 leading-tight">Send Us a Message</h3>
                <p class="text-sm text-gray-500">We usually reply within a day.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="flex items-start gap-2 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl p-4 mb-6">
                <i class="fa-solid fa-circle-check mt-0.5"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <form action="/support/send" method="POST" class="space-y-5">
            @csrf
            <div>
                <label for="subject" class="block text-sm font-semibold text-gray-700 mb-1.5">Subject</label>
                <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required
                       placeholder="What's this about?"
                       class="block w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 shadow-sm transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 focus:outline-none">
                @error('subject')
                    <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="message" class="block text-sm font-semibold text-gray-700 mb-1.5">Message</label>
                <textarea name="message" id="message" rows="5" required
                          placeholder="Tell us how we can help..."
                          class="block w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 shadow-sm transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 focus:outline-none resize-none">{{ old('message') }}</textarea>
                @error('message')
                    <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-semibold text-sm shadow-sm shadow-indigo-200 transition-all duration-150 hover:scale-[1.02] active:scale-95">
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                    Send Message
                </button>
            </div>
        </form>
    </div>
</div>

@endsection