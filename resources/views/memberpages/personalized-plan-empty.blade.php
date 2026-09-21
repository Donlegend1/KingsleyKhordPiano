@extends('layouts.member')

@section('content')

<div class="min-h-screen bg-gray-50 flex items-start justify-center px-4 pt-24 pb-16">
  <div class="relative bg-white rounded-[2rem] border border-gray-100 shadow-xl shadow-gray-200/60 p-12 sm:p-16 max-w-2xl w-full text-center overflow-hidden">
    {{-- Decorative background accents --}}
    <div class="absolute -top-24 -right-24 w-64 h-64 rounded-full bg-[#1447A6]/5"></div>
    <div class="absolute -bottom-28 -left-20 w-56 h-56 rounded-full bg-[#1447A6]/5"></div>

    <div class="relative">
      <div class="mx-auto mb-7 w-20 h-20 rounded-2xl bg-[#1447A6]/10 flex items-center justify-center">
        <i class="fa-regular fa-compass text-[#1447A6] text-3xl"></i>
      </div>

      <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mb-3">You're yet to submit your details</h1>
      <p class="text-base text-gray-500 max-w-md mx-auto mb-9 leading-relaxed">
        Tell us a little about your goals and playing level, and Kingsley will build you a personalized roadmap to get there.
      </p>

      <a href="{{ route('member.personalized-guidance.create') }}"
         class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl bg-[#1447A6] hover:bg-[#0F3A8A] text-white text-sm font-bold transition-all shadow-lg shadow-[#1447A6]/25 hover:shadow-xl hover:-translate-y-0.5">
        Get My Customized Roadmap
        <i class="fa-solid fa-arrow-right text-xs"></i>
      </a>
    </div>
  </div>
</div>

@endsection
