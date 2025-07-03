@extends("layouts.app")

@section("content")
<!-- Breadcrumb -->
<nav class="bg-gray-100 py-3 px-5 rounded-md w-full text-sm text-gray-600" aria-label="Breadcrumb">
    <ol class="list-reset flex items-center space-x-2">
     
        <li>
            <a href="/" class="text-gray-600 hover:text-[#FFD736] font-medium">
                Home
            </a>
        </li>
        <li>
            <span class="mx-2 text-gray-400">/</span>
        </li>
        <li aria-current="page" class="text-gray-500 font-semibold truncate max-w-[200px]">
            {{ $pageTitle ?? 'Current Page' }}
        </li>
    </ol>
</nav>

@include("components.contact")
@endsection