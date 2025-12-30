<!-- resources/views/components/pagination.blade.php -->
@props(['paginator'])

@if ($paginator->hasPages())
    <nav aria-label="Pagination" class="mt-3">
        <ul class="pagination justify-content-center">

            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">&laquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo;</a>
                </li>
            @endif

            {{-- Page Numbers --}}
            @for ($page = 1; $page <= $paginator->lastPage(); $page++)
                <li class="page-item d-none d-md-block {{ $page == $paginator->currentPage() ? 'active' : '' }}">
                    @if ($page == $paginator->currentPage())
                        <span class="page-link">{{ $page }}</span>
                    @else
                        <a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                    @endif
                </li>
            @endfor

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">&raquo;</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">&raquo;</span>
                </li>
            @endif
        </ul>

        {{-- Mobile version --}}
        <div class="d-md-none d-flex justify-content-between mt-2">
            @if ($paginator->onFirstPage())
                <span class="btn btn-sm btn-outline-secondary disabled">@lang('admin.previous')</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-sm btn-outline-secondary">@lang('admin.previous')</a>
            @endif

            <span class="btn btn-sm btn-outline-dark disabled">
                {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-sm btn-primary">@lang('admin.next')</a>
            @else
                <span class="btn btn-sm btn-primary disabled">@lang('admin.next')</span>
            @endif
        </div>
    </nav>
@endif
