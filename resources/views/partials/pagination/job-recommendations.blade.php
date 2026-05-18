@if ($paginator->hasPages())
<div class="flex items-center justify-center gap-2 pt-8">
    @if ($paginator->onFirstPage())
        <span class="w-10 h-10 flex items-center justify-center rounded-lg border border-outline-variant text-on-surface-variant/40 cursor-not-allowed">
            <span class="material-symbols-outlined">chevron_left</span>
        </span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="w-10 h-10 flex items-center justify-center rounded-lg border border-outline-variant hover:bg-surface-container-high transition-colors">
            <span class="material-symbols-outlined">chevron_left</span>
        </a>
    @endif

    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="px-2 text-on-surface-variant">{{ $element }}</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="w-10 h-10 flex items-center justify-center rounded-lg bg-secondary text-white font-bold">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="w-10 h-10 flex items-center justify-center rounded-lg border border-outline-variant hover:bg-surface-container-high transition-colors font-bold">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="w-10 h-10 flex items-center justify-center rounded-lg border border-outline-variant hover:bg-surface-container-high transition-colors">
            <span class="material-symbols-outlined">chevron_right</span>
        </a>
    @else
        <span class="w-10 h-10 flex items-center justify-center rounded-lg border border-outline-variant text-on-surface-variant/40 cursor-not-allowed">
            <span class="material-symbols-outlined">chevron_right</span>
        </span>
    @endif
</div>
@endif
