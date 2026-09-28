@props(['paginator'])

@php
    $paginator = $paginator->withQueryString()->onEachSide(1);
    $pageLinks = $paginator->linkCollection();
@endphp

@if($paginator->hasPages())
    <nav class="job-pagination" aria-label="Job listings pagination">
        <p class="job-pagination__summary" aria-live="polite">
            Showing <strong>{{ $paginator->firstItem() }}</strong>
            to <strong>{{ $paginator->lastItem() }}</strong>
            of <strong>{{ $paginator->total() }}</strong> results
        </p>

        <ul class="job-pagination__list">
            @foreach($pageLinks as $index => $link)
                @php
                    $isPrevious = $index === 0;
                    $isNext = $index === $pageLinks->count() - 1;
                    $isEllipsis = ! $isPrevious && ! $isNext && $link['url'] === null;
                    $pageNumber = $link['page'];
                    $hideOnMobile = ! $isPrevious && ! $isNext && is_numeric($pageNumber)
                        && abs($pageNumber - $paginator->currentPage()) > 1;
                @endphp

                <li class="job-pagination__item{{ $link['active'] ? ' is-active' : '' }}{{ $isPrevious || $isNext ? ' is-control' : '' }}{{ $isEllipsis ? ' is-ellipsis' : '' }}{{ $hideOnMobile ? ' is-mobile-hidden' : '' }}">
                    @if($isEllipsis)
                        <span class="job-pagination__ellipsis" aria-hidden="true">&hellip;</span>
                    @elseif($link['url'] === null)
                        <span class="job-pagination__link is-disabled" aria-disabled="true" tabindex="-1">
                            {{ $isPrevious ? 'Previous' : ($isNext ? 'Next' : $link['label']) }}
                        </span>
                    @else
                        <a class="job-pagination__link{{ $link['active'] ? ' is-current' : '' }}"
                            href="{{ $link['url'] }}"
                            @if($isPrevious) rel="prev" aria-label="Previous page" @elseif($isNext) rel="next" aria-label="Next page" @elseif($link['active']) aria-current="page" @endif>
                            {{ $isPrevious ? 'Previous' : ($isNext ? 'Next' : $link['label']) }}
                        </a>
                    @endif
                </li>
            @endforeach
        </ul>
    </nav>
@endif