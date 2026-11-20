<div class="grid grid-cols-1 gap-x-5 gap-y-7 sm:grid-cols-2 lg:grid-cols-3">
    @foreach($guides as $slug => $guide)
        <a href="{{ route('guides.show', $slug) }}"
           aria-label="{{ $guide['title'] }}"
           class="group block rounded-xl focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#1447A6]">
            <div class="aspect-video overflow-hidden rounded-xl bg-[#151b29] p-3 shadow-sm">
                @if(isset($guide['cover_view']))
                    @include($guide['cover_view'])
                @else
                    <img src="{{ asset($guide['cover']) }}" alt="" loading="lazy" decoding="async"
                         class="h-full w-full object-contain transition-transform duration-300 motion-safe:group-hover:scale-[1.04]">
                @endif
            </div>
        </a>
    @endforeach
</div>
