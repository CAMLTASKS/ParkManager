@if ($paginator->hasPages())
    <nav class="app-pagination" aria-label="{{ $label ?? 'Paginacion' }}">
        <span class="app-pagination-summary">
            {{ $paginator->firstItem() }}-{{ $paginator->lastItem() }} de {{ $paginator->total() }}
        </span>
        <div class="app-pagination-actions">
            @if ($paginator->onFirstPage())
                <span class="app-page-button disabled">Anterior</span>
            @else
                <a class="app-page-button" href="{{ $paginator->previousPageUrl() }}" rel="prev">Anterior</a>
            @endif

            <span class="app-page-current">Pagina {{ $paginator->currentPage() }} de {{ $paginator->lastPage() }}</span>

            @if ($paginator->hasMorePages())
                <a class="app-page-button" href="{{ $paginator->nextPageUrl() }}" rel="next">Siguiente</a>
            @else
                <span class="app-page-button disabled">Siguiente</span>
            @endif
        </div>
    </nav>
@endif
