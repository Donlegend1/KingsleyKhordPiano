@extends("layouts.hub")

@section("title", "Change Display Name")

@section("content")
<div class="p-6">
    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden">

        <div class="bg-gray-50 dark:bg-gray-900/40 px-6 sm:px-8 py-5 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white">Change Display Name</h2>
        </div>

        <div class="px-6 sm:px-8 py-6">

            @if(session('success'))
                <div class="flex items-start gap-2 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl p-4 mb-6">
                    <i class="fa-solid fa-circle-check mt-0.5"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @php $remaining = max(0, \App\Http\Controllers\CommunityIndexController::DISPLAY_NAME_CHANGE_LIMIT - $changeCount); @endphp

            <ul class="list-disc pl-5 mb-6">
                <li class="text-gray-800 dark:text-gray-200">
                    You have made <strong>{{ $changeCount }} of {{ \App\Http\Controllers\CommunityIndexController::DISPLAY_NAME_CHANGE_LIMIT }}</strong>
                    display name changes since {{ $windowStart->format('m/d/Y') }}.
                    You are permitted to make {{ \App\Http\Controllers\CommunityIndexController::DISPLAY_NAME_CHANGE_LIMIT }} changes
                    in a {{ \App\Http\Controllers\CommunityIndexController::DISPLAY_NAME_CHANGE_WINDOW_DAYS }}-day period.
                </li>
            </ul>

            @error('display_name')
                <div class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl p-4 mb-6">
                    <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                    <span>{{ $message }}</span>
                </div>
            @enderror

            @if($remaining > 0)
                <form action="{{ route('community.display-name.update') }}" method="POST">
                    @csrf

                    <label class="flex items-center gap-2 text-lg font-bold text-gray-900 dark:text-white mb-2">
                        New display name
                        <span class="text-[11px] font-bold text-red-500 tracking-wide">REQUIRED</span>
                    </label>
                    <input type="text" name="display_name" value="{{ old('display_name') }}" required minlength="2" maxlength="50"
                        placeholder="{{ $user->display_name }}"
                        class="block w-full max-w-md rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-4 py-3 text-sm text-gray-900 dark:text-white shadow-sm focus:border-gray-500 focus:ring-2 focus:ring-gray-100 focus:outline-none mb-6">

                    <button type="submit"
                        class="bg-[#C15C55] hover:bg-[#B04D46] text-white font-semibold px-6 py-3 rounded-lg transition-colors">
                        Save
                    </button>
                </form>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    You've used all your display name changes for this period. Please try again after {{ $windowStart->copy()->addDays(\App\Http\Controllers\CommunityIndexController::DISPLAY_NAME_CHANGE_WINDOW_DAYS)->format('M d, Y') }}.
                </p>
            @endif
        </div>
    </div>
</div>
@endsection
