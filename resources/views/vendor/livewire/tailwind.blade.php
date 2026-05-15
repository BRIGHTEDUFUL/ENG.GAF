@if ($paginator->hasPages())
<nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between gap-4 flex-wrap">
    <p class="text-xs text-sky-600 font-medium">
        Showing <span class="font-bold text-gaf-navy">{{ $paginator->firstItem() }}</span>–<span class="font-bold text-gaf-navy">{{ $paginator->lastItem() }}</span>
        of <span class="font-bold text-gaf-navy">{{ $paginator->total() }}</span> results
    </p>
    <div class="flex items-center gap-1">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
        <span class="px-3 py-1.5 text-xs font-medium text-sky-300 rounded-lg cursor-not-allowed">← Prev</span>
        @else
        <button wire:click="previousPage" class="px-3 py-1.5 text-xs font-semibold text-gaf-blue bg-white border border-sky-200 rounded-lg hover:bg-sky-50 transition-colors">← Prev</button>
        @endif

        {{-- Page numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
            <span class="px-2 py-1.5 text-xs text-gray-400">{{ $element }}</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                    <span class="px-3 py-1.5 text-xs font-bold text-white bg-gaf-blue rounded-lg">{{ $page }}</span>
                    @else
                    <button wire:click="gotoPage({{ $page }})" class="px-3 py-1.5 text-xs font-semibold text-gaf-blue bg-white border border-sky-200 rounded-lg hover:bg-sky-50 transition-colors">{{ $page }}</button>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
        <button wire:click="nextPage" class="px-3 py-1.5 text-xs font-semibold text-gaf-blue bg-white border border-sky-200 rounded-lg hover:bg-sky-50 transition-colors">Next →</button>
        @else
        <span class="px-3 py-1.5 text-xs font-medium text-sky-300 rounded-lg cursor-not-allowed">Next →</span>
        @endif
    </div>
</nav>
@endif
