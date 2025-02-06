@if ($paginator->hasPages())
    <ul class="pagination">
        {{-- Botón "Anterior" --}}
        @if ($paginator->onFirstPage())
            <li class="disabled"><span class="page">&laquo;</span></li>
        @else
            <li><a href="{{ $paginator->previousPageUrl() }}" class="page">&laquo;</a></li>
        @endif

        {{-- Enlaces de páginas --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <li class="disabled"><span class="page">{{ $element }}</span></li>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li><span class="page active">{{ $page }}</span></li>
                    @else
                        <li><a href="{{ $url }}" class="page">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Botón "Siguiente" --}}
        @if ($paginator->hasMorePages())
            <li><a href="{{ $paginator->nextPageUrl() }}" class="page">&raquo;</a></li>
        @else
            <li class="disabled"><span class="page">&raquo;</span></li>
        @endif
    </ul>
@endif
