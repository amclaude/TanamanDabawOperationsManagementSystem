@php
    $paginator = method_exists($data, 'hasPages') ? $data : null;
@endphp

@if ($paginator && $paginator->hasPages())
    @php
        $windowSize = 5;
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();
        $halfWindow = (int) floor($windowSize / 2);

        $start = max(1, $currentPage - $halfWindow);
        $end = min($lastPage, $start + $windowSize - 1);
        $start = max(1, $end - $windowSize + 1);
    @endphp

    <nav class="pagination-wrapper {{ $wrapperClass ?? '' }}" aria-label="Pagination navigation">
        <ul class="pagination-list">
            @if ($paginator->onFirstPage())
                <li><span class="pagination-link is-disabled" aria-disabled="true">&lt;&lt;</span></li>
                <li><span class="pagination-link is-disabled" aria-disabled="true">&lt;</span></li>
            @else
                <li><a class="pagination-link" href="{{ $paginator->url(1) }}" aria-label="First page">&lt;&lt;</a></li>
                <li><a class="pagination-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous page">&lt;</a></li>
            @endif

            @foreach ($paginator->getUrlRange($start, $end) as $page => $url)
                @if ($page === $currentPage)
                    <li><span class="pagination-link is-active" aria-current="page">{{ $page }}</span></li>
                @else
                    <li><a class="pagination-link" href="{{ $url }}">{{ $page }}</a></li>
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li><a class="pagination-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next page">&gt;</a></li>
                <li><a class="pagination-link" href="{{ $paginator->url($lastPage) }}" aria-label="Last page">&gt;&gt;</a></li>
            @else
                <li><span class="pagination-link is-disabled" aria-disabled="true">&gt;</span></li>
                <li><span class="pagination-link is-disabled" aria-disabled="true">&gt;&gt;</span></li>
            @endif
        </ul>
    </nav>
@endif
