@props([
    'categories' => null,
    'active' => request('category'),
])

@php
    $cats = $categories ?: \App\Models\JobCategory::orderBy('sort')->get();
@endphp

{{-- показываем только на десктопе --}}
<aside class="bzs-sidebar d-none d-md-block">
    <div class="list-group list-group-flush">
        <a href="{{ route('jobs.index') }}"
           class="list-group-item list-group-item-action d-flex justify-content-between {{ $active ? '' : 'active' }}">
            <span>{{ __('categories.all') }}</span>
        </a>

        @foreach($cats as $c)
            <a href="{{ route('jobs.index', ['category' => $c->slug]) }}"
               class="list-group-item list-group-item-action d-flex justify-content-between {{ $active === $c->slug ? 'active' : '' }}">
                <span>{{ __('categories.' . $c->slug) }}</span>
            </a>
        @endforeach
    </div>
</aside>
