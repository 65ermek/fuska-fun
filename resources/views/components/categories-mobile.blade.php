@props([
    'categories' => null,
    'active' => request('category'),
])

@php
    $cats = $categories ?: \App\Models\JobCategory::orderBy('sort')->get();
@endphp

<div id="mobileCategories" class="mobile-cat-overlay d-md-none">
    <div class="mobile-cat-panel">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">{{ __('categories.mobile_title') }}</h6>
            <button type="button" class="btn-close" aria-label="{{ __('categories.close') }}" id="closeCategories"></button>
        </div>

        <div class="list-group list-group-flush">
            <a href="{{ route('jobs.index') }}"
               class="list-group-item list-group-item-action {{ $active ? '' : 'active' }}">
                {{ __('categories.all') }}
            </a>

            @foreach($cats as $c)
                <a href="{{ route('jobs.index', ['category' => $c->slug]) }}"
                   class="list-group-item list-group-item-action {{ $active === $c->slug ? 'active' : '' }}">
                    {{ __('categories.' . $c->slug) }}
                </a>
            @endforeach
        </div>
    </div>
</div>
