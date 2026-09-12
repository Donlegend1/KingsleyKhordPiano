@php $items = $items ?? []; @endphp
<nav class="flex items-center flex-wrap gap-1.5 text-sm mb-2 sm:mb-4" aria-label="Breadcrumb">
    <a href="{{ route('community.activity-feed') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 font-medium transition-colors">Home</a>
    @foreach($items as $item)
        <svg class="w-3.5 h-3.5 text-gray-300 dark:text-gray-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
        @if(!$loop->last && ($item['url'] ?? null))
            <a href="{{ $item['url'] }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 font-medium transition-colors truncate max-w-[220px]">{{ $item['label'] }}</a>
        @else
            <span class="text-gray-400 dark:text-gray-500 truncate max-w-[220px]">{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>
