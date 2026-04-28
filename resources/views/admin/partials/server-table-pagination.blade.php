<div class="table-pagination">
    @if ($paginator->onFirstPage())
        <span class="btn btn-light btn-sm disabled" aria-disabled="true">Sebelumnya</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-light btn-sm">Sebelumnya</a>
    @endif

    <span class="table-page-indicator">{{ $paginator->currentPage() }}</span>

    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-light btn-sm">Berikutnya</a>
    @else
        <span class="btn btn-light btn-sm disabled" aria-disabled="true">Berikutnya</span>
    @endif
</div>
