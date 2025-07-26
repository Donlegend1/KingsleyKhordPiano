@if ($paginator->hasPages())
  <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center space-x-2">
    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
      <span class="px-4 py-2 text-gray-400 bg-gray-100 border border-gray-300 rounded-md cursor-not-allowed">
        ← Prev
      </span>
    @else
      <a href="{{ $paginator->previousPageUrl() }}"
         class="px-4 py-2 bg-white text-gray-800 border border-gray-300 rounded-md hover:bg-gray-200 transition">
        ← Prev
      </a>
    @endif

    {{-- Page Numbers --}}
    @foreach ($elements as $element)
      @if (is_string($element))
        <span class="px-3 py-2 text-gray-500">{{ $element }}</span>
      @endif

      @if (is_array($element))
        @foreach ($element as $page => $url)
          @if ($page == $paginator->currentPage())
            <span class="px-4 py-2 bg-[#FFD736] text-black border border-[#FFD736] rounded-md font-semibold">
              {{ $page }}
            </span>
          @else
            <a href="{{ $url }}"
               class="px-4 py-2 bg-white text-gray-800 border border-gray-300 rounded-md hover:bg-gray-200 transition">
              {{ $page }}
            </a>
          @endif
        @endforeach
      @endif
    @endforeach

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
      <a href="{{ $paginator->nextPageUrl() }}"
         class="px-4 py-2 bg-white text-gray-800 border border-gray-300 rounded-md hover:bg-gray-200 transition">
        Next →
      </a>
    @else
      <span class="px-4 py-2 text-gray-400 bg-gray-100 border border-gray-300 rounded-md cursor-not-allowed">
        Next →
      </span>
    @endif
  </nav>
@endif
