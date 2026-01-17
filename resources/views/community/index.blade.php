@extends("layouts.community")

@section("content")

@php
    // Calculate profile completion
    $user = auth()->user();
    $totalFields = 6;
    $completedFields = 0;

    // Check each field
    $hasProfilePhoto = !empty($user->passport);
    // $hasBiography = !empty($user->biography);
    $hasSocialMedia = !empty($user->instagram) || !empty($user->youtube) || !empty($user->facebook) || !empty($user->tiktok);
    $hasSkillLevel = !empty($user->skill_level);
    // $hasPhoneNumber = !empty($user->phone_number);
    $hasCountry = !empty($user->country);

    // Count completed fields
    if ($hasProfilePhoto) $completedFields++;
    // if ($hasBiography) $completedFields++;
    if ($hasSocialMedia) $completedFields++;
    if ($hasSkillLevel) $completedFields++;
    // if ($hasPhoneNumber) $completedFields++;
    if ($hasCountry) $completedFields++;

    // Calculate percentage
    $completionPercentage = round(($completedFields / $totalFields) * 100);

    // Calculate stroke-dashoffset for progress circle (339.292 is the circumference of the circle)
    $strokeDashoffset = 339.292 - (339.292 * $completionPercentage / 100);
@endphp

<!-- Header Section -->
<div class="bg-white dark:bg-gray-800 mb-6">
    <div class="px-4 sm:px-6 py-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-[#1F2937] dark:text-white">Activity Feed</h1>
    </div>
</div>
<!-- Main Feed Section -->
<section class="px-4 sm:px-6 pb-6 bg-gray-50 dark:bg-gray-900">
  <div class="flex flex-col lg:flex-row gap-6">

    <!-- Left: Main Feed Area -->
    <div class="flex-1 space-y-6">
        <!-- Feed Posts Container -->
        <div id="post-list" class="space-y-6"></div>
    </div>

    <!-- Right Sidebar -->
    <div class="hidden lg:block w-80 xl:w-96 flex-shrink-0 space-y-6">

        <!-- Complete Your Profile Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-[#1F2937] dark:text-white">Complete Your Profile</h3>
                <a href="/member/profile" class="text-sm text-[#FFD736] hover:text-[#E5C634] font-medium transition-colors">
                    Edit Profile →
                </a>
            </div>

            <!-- Progress Circle -->
            <div class="flex justify-center mb-5">
                <div class="relative w-32 h-32">
                    <svg class="w-32 h-32 transform -rotate-90" viewBox="0 0 120 120">
                        <circle
                            cx="60"
                            cy="60"
                            r="54"
                            stroke="#E5E7EB"
                            stroke-width="8"
                            fill="none"
                        />
                        <circle
                            cx="60"
                            cy="60"
                            r="54"
                            stroke="#10B981"
                            stroke-width="8"
                            fill="none"
                            stroke-dasharray="339.292"
                            stroke-dashoffset="{{ $strokeDashoffset }}"
                            stroke-linecap="round"
                            class="transition-all duration-300"
                        />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-3xl font-bold text-[#1F2937]">{{ $completionPercentage }}</span>
                        <span class="text-base text-[#6B7280]">%</span>
                        <span class="text-xs text-[#9CA3AF] mt-1">Complete</span>
                    </div>
                </div>
            </div>

            <!-- Checklist Items -->
            <div class="space-y-3">
                <!-- Profile Photo -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        @if($hasProfilePhoto)
                            <div class="w-5 h-5 rounded-full border-2 border-[#10B981] flex items-center justify-center">
                                <svg class="w-3 h-3 text-[#10B981]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <span class="text-sm text-[#10B981] font-medium">Profile Photo</span>
                        @else
                            <div class="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center">
                                <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                            </div>
                            <span class="text-sm text-[#6B7280]">Profile Photo</span>
                        @endif
                    </div>
                </div>

                <!-- Biography -->
                {{-- <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        @if($hasBiography)
                            <div class="w-5 h-5 rounded-full border-2 border-[#10B981] flex items-center justify-center">
                                <svg class="w-3 h-3 text-[#10B981]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <span class="text-sm text-[#10B981] font-medium">Biography</span>
                        @else
                            <div class="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center">
                                <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                            </div>
                            <span class="text-sm text-[#6B7280]">Biography</span>
                        @endif
                    </div>
                </div> --}}

                <!-- Social Media Links -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        @if($hasSocialMedia)
                            <div class="w-5 h-5 rounded-full border-2 border-[#10B981] flex items-center justify-center">
                                <svg class="w-3 h-3 text-[#10B981]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <span class="text-sm text-[#10B981] font-medium">Social Media Links</span>
                        @else
                            <div class="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center">
                                <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                            </div>
                            <span class="text-sm text-[#6B7280]">Social Media Links</span>
                        @endif
                    </div>
                </div>

                <!-- Skill Level -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        @if($hasSkillLevel)
                            <div class="w-5 h-5 rounded-full border-2 border-[#10B981] flex items-center justify-center">
                                <svg class="w-3 h-3 text-[#10B981]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <span class="text-sm text-[#10B981] font-medium">Skill Level</span>
                        @else
                            <div class="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center">
                                <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                            </div>
                            <span class="text-sm text-[#6B7280]">Skill Level</span>
                        @endif
                    </div>
                </div>

                <!-- Phone Number -->
                {{-- <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        @if($hasPhoneNumber)
                            <div class="w-5 h-5 rounded-full border-2 border-[#10B981] flex items-center justify-center">
                                <svg class="w-3 h-3 text-[#10B981]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <span class="text-sm text-[#10B981] font-medium">Phone Number</span>
                        @else
                            <div class="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center">
                                <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                            </div>
                            <span class="text-sm text-[#6B7280]">Phone Number</span>
                        @endif
                    </div>
                </div> --}}

                <!-- Location / Country -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        @if($hasCountry)
                            <div class="w-5 h-5 rounded-full border-2 border-[#10B981] flex items-center justify-center">
                                <svg class="w-3 h-3 text-[#10B981]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <span class="text-sm text-[#10B981] font-medium">Location / Country</span>
                        @else
                            <div class="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center">
                                <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                            </div>
                            <span class="text-sm text-[#6B7280]">Location / Country</span>
                        @endif
                    </div>
                </div>
            </div>
</div>

        <!-- Latest Updates Card -->
        {{-- <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
            <h3 class="text-lg font-bold text-[#1F2937] dark:text-white mb-4">Latest updates</h3>

            <div class="space-y-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-800 dark:text-gray-200"><span class="font-semibold">John</span> posted an update</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">4 years ago</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-800 dark:text-gray-200"><span class="font-semibold">Adele</span> posted an update</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">4 years ago</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-800 dark:text-gray-200"><span class="font-semibold">John</span> posted an update</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">5 years ago</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-800 dark:text-gray-200"><span class="font-semibold">John</span> posted an update in the group <span class="font-semibold">Coffee Addicts</span></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">5 years ago</p>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>
  </div>
</section>

<!-- Floating Action Button -->
<button class="fixed bottom-6 right-6 w-14 h-14 bg-[#FF6B35] dark:bg-[#E55A2B] rounded-full shadow-lg hover:bg-[#E55A2B] dark:hover:bg-[#CC5A27] transition-colors duration-200 flex items-center justify-center">
    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.1 5H19M7 13l-1.1 5M7 13l4 4h4l4-4m-8 4V9m0 8v4"></path>
    </svg>
</button>




@endsection

