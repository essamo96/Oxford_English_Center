@if ($paginator->hasPages())
<ul class="pagination">
    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
    <li class="page-item disabled">
        <a class="page-link" href="#" tabindex="-1">السابق</a>
    </li>
    @else
    <li><a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">السابق</a></li>
    @endif

    {{-- Pagination Elements --}}
    @foreach ($elements as $element)
    {{-- "Three Dots" Separator --}}
    @if (is_string($element))
    <li class="page-link disabled"><span>{{ $element }}</span></li>
    @endif

    {{-- Array Of Links --}}
    @if (is_array($element))
    @foreach ($element as $page => $url)
    @if ($page == $paginator->currentPage())
    <li class="page-item active">
        <a class="page-link" href="#">{{ $page }}</a>
    </li>
    @else
    <li><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
    @endif
    @endforeach
    @endif
    @endforeach

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
    <li><a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">التالي</a></li>
    @else
    <li class="page-item disabled">
        <a class="page-link" href="#" tabindex="-1">التالي</a>
    </li>
    @endif
</ul>
@endif
