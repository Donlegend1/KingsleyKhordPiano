@extends('layouts.member')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4">
    {{-- Page Title --}}
    <div class="my-5">
 <h2 class="text-2xl font-bold text-gray-800 mb-6">Support Center</h2>
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

        <form action="/support/send" method="POST" enctype="multipart/form-data" class="space-y-5">
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

            <div x-data="{ fileName: null }">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Attachment <span class="font-normal text-gray-400">(optional)</span></label>
                <label for="attachment"
                       class="flex items-center justify-center gap-2 w-full py-6 rounded-xl border border-dashed border-gray-300 text-sm text-gray-500 cursor-pointer hover:border-indigo-400 hover:text-indigo-600 hover:bg-indigo-50/40 transition">
                    <i class="fa-solid fa-paperclip"></i>
                    <span x-text="fileName ?? 'Click to attach a file'"></span>
                </label>
                <input type="file" name="attachment" id="attachment" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.txt"
                       class="sr-only" @change="fileName = $event.target.files[0]?.name ?? null">
                <p class="text-xs text-gray-400 mt-1.5">Images, PDF, or Word docs, up to 10MB.</p>
                @error('attachment')
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