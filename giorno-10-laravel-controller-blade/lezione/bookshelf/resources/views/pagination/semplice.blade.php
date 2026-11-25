{{-- Paginazione in HTML semplice, senza Tailwind ne' Bootstrap.
     Laravel 13 non include piu' le viste "default" e "simple-default". --}}
@if ($paginator->hasPages())
    <nav class="paginazione">
        @if ($paginator->onFirstPage())
            <span>&laquo; Precedente</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo; Precedente</a>
        @endif

        @if (method_exists($paginator, 'lastPage'))
            <span>Pagina {{ $paginator->currentPage() }} di {{ $paginator->lastPage() }}</span>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next">Successiva &raquo;</a>
        @else
            <span>Successiva &raquo;</span>
        @endif
    </nav>
@endif
