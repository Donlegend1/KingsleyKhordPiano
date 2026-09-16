@extends("layouts.hub")

@section("title", $category['title'])

@php $isStudentChallenges = $category['subcategory'] === 'student_challenges'; @endphp

@section("breadcrumbs")
    @include('community.partials.breadcrumbs', ['items' => [
        ['label' => 'Forums', 'url' => route('community.forum')],
        ['label' => $category['title']],
    ]])
@endsection

@section("content")
<div class="p-6">
@if($isStudentChallenges)
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm p-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $category['title'] }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ $category['description'] }}</p>
            </div>

            @include('community.partials.topics-list')
        </div>

        @include('community.partials.sidebar', ['hideCommunityStats' => true, 'hideRecentMembers' => true])
    </div>
@else
    @include('community.partials.category-page')
@endif
</div>
@endsection
