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

         <div x-data="{ open: false }" class="border-b pb-2">
            <button @click="open = !open" class="w-full text-left flex justify-between items-center text-gray-700 font-medium">
                <span>Will you transcrive this song for me? I am unable to catch every chord being played</span>
                <svg :class="open ? 'rotate-180' : ''" class="h-5 w-5 text-gray-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <p x-show="open" x-transition class="text-sm text-gray-600 mt-2">While I don’t transcribe personal midi files anymore (my membership site is my top focus at the moment), I am willing to assist you in identifying the chords for a song. Post a link to a song in the Activity Feed and me or other members can jump in and help.</p>
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
    <div class="bg-white p-6 rounded-2xl shadow-md">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Send Us a Message</h3>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-md p-4 mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form action="/support/send" method="POST" class="space-y-6">
            @csrf
            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                @error('subject')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                <textarea name="message" id="message" rows="5" required
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('message') }}</textarea>
                @error('message')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700 font-medium text-sm">
                    Send Message
                </button>
            </div>
        </form>
    </div>
</div>

@endsection