@extends("layouts.hub")

@section("title", "Account Settings")

@section("breadcrumbs")
    @include('community.partials.breadcrumbs', ['items' => [['label' => 'Account Settings']]])
@endsection

@section("content")
<div class="p-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Account Settings</h1>

    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden">

        {{-- Display Name --}}
        <div class="flex items-center justify-between gap-4 px-6 sm:px-8 py-6 border-b border-gray-100 dark:border-gray-700">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Display Name</h3>
                <p class="text-gray-500 dark:text-gray-400 mt-1">{{ $user->display_name }}</p>
            </div>
            <a href="{{ route('community.display-name') }}" class="text-blue-600 hover:text-blue-700 font-medium flex-shrink-0">Change</a>
        </div>

        {{-- Email Address --}}
        <div class="flex items-center justify-between gap-4 px-6 sm:px-8 py-6 border-b border-gray-100 dark:border-gray-700">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Email Address</h3>
                <p class="text-gray-500 dark:text-gray-400 mt-1">{{ $user->email }}</p>
            </div>
            <a href="/member/profile" class="text-blue-600 hover:text-blue-700 font-medium flex-shrink-0">Change</a>
        </div>

        {{-- Profile Status --}}
        <div class="px-6 sm:px-8 py-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Profile Status</h3>

            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden">
                <div class="flex items-center justify-between gap-3 px-5 py-4">
                    <h4 class="text-base font-bold text-gray-900 dark:text-white">
                        @if($profilePercent < 100)
                            Next Step: {{ $profileNextStep }}
                        @else
                            Your profile is complete
                        @endif
                    </h4>
                    @if($profilePercent < 100)
                        <a href="/member/profile"
                            class="bg-[#C15C55] hover:bg-[#B04D46] text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors flex-shrink-0">
                            Complete My Profile
                        </a>
                    @endif
                </div>
                <div class="relative h-8 bg-[#EFE1DE] dark:bg-gray-700 border-l-4 border-[#C15C55]">
                    <div class="absolute inset-y-0 left-0 bg-[#C15C55] transition-all duration-500" style="width: {{ $profilePercent }}%"></div>
                    <span class="relative z-10 flex items-center h-full px-4 text-sm text-white">
                        Your profile is {{ $profilePercent }}% complete!
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
