@extends('layouts.admin')

@section('content')
<main class="flex-1 p-6">
    <header class="mb-6">
        <a href="{{ route('admin.shop.index', ['type' => $product->type]) }}" class="text-sm text-gray-500 hover:text-gray-900 transition">
            <i class="fa-solid fa-chevron-left text-xs"></i> Back
        </a>
        <h2 class="text-xl font-bold text-gray-800 mt-2">Edit {{ $product->type === 'plugin' ? 'Plugin' : 'MIDI File' }}</h2>
    </header>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg p-4 mb-6 max-w-2xl">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @include('admin.shop._form')
</main>
@endsection
