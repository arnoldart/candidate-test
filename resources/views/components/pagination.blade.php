@props(['paginator'])

@if ($paginator->hasPages())
    <div class="bg-white px-6 py-4 flex flex-col sm:flex-row items-center justify-between border-t border-gray-100 gap-4">
        <div class="text-sm text-gray-500">
            Showing <span class="font-medium text-gray-900">{{ $paginator->firstItem() }}</span> to <span class="font-medium text-gray-900">{{ $paginator->lastItem() }}</span> of <span class="font-medium text-gray-900">{{ $paginator->total() }}</span> results
        </div>
        <div class="flex items-center gap-2">
            @if ($paginator->onFirstPage())
                <span class="w-8 h-8 flex items-center justify-center rounded border border-gray-200 text-gray-300 cursor-not-allowed bg-gray-50">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded border border-gray-300 text-gray-500 hover:bg-gray-50 transition-colors hover:text-gray-900">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded border border-gray-300 text-gray-500 hover:bg-gray-50 transition-colors hover:text-gray-900">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
            @else
                <span class="w-8 h-8 flex items-center justify-center rounded border border-gray-200 text-gray-300 cursor-not-allowed bg-gray-50">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </span>
            @endif
        </div>
    </div>
@else
    <div class="bg-white px-6 py-4 flex items-center justify-between border-t border-gray-100">
        <div class="text-sm text-gray-500">
            Showing <span class="font-medium text-gray-900">{{ $paginator->count() }}</span> results
        </div>
    </div>
@endif
