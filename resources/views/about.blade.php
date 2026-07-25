@extends("layouts.app")

@section("content")
<!-- Breadcrumb -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="/" class="hover:text-gray-700">Home</a>
        <span>/</span>
        <span class="text-blue-600 font-medium">{{ $pageTitle ?? 'Current Page' }}</span>
    </div>
</div>

@include("components.about")

@endsection