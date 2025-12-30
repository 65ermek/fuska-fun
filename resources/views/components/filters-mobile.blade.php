@props([
    // чтобы сохранить выбранную категорию
    'category' => request('category'),
])

<div id="mobileFilters" class="mobile-filter-overlay d-md-none">
    <div class="mobile-filter-panel">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">{{ __('messages.filters.title') }}</h6>
            <button type="button" class="btn-close" aria-label="{{ __('messages.filters.close') }}" id="closeFilters"></button>
        </div>

        <form method="get" action="{{ route('jobs.index') }}" class="small">
            @if($category)
                <input type="hidden" name="category" value="{{ $category }}">
            @endif

            <div class="mb-2">
                <label class="form-label mb-1">{{ __('messages.filters.keyword') }}</label>
                <input type="text" name="q" class="form-control form-control-sm"
                       value="{{ request('q') }}" placeholder="{{ __('messages.filters.keyword_placeholder') }}">
            </div>

            <div class="mb-2">
                <label class="form-label mb-1">{{ __('messages.filters.city') }}</label>
                <input type="text" name="city" class="form-control form-control-sm"
                       value="{{ request('city') }}" placeholder="{{ __('messages.filters.city_placeholder') }}">
            </div>

            <div class="mb-2">
                <label class="form-label mb-1">{{ __('messages.filters.pay') }}</label>
                <select name="pay" class="form-select form-select-sm">
                    <option value="">{{ __('messages.filters.pay_select') }}</option>
                    <option value="per_hour" @selected(request('pay')==='per_hour')>{{ __('messages.filters.pay_per_hour') }}</option>
                    <option value="per_job"  @selected(request('pay')==='per_job')>{{ __('messages.filters.pay_per_job') }}</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label mb-1">{{ __('messages.filters.sort') }}</label>
                <select name="sort" class="form-select form-select-sm">
                    <option value="new"   @selected(request('sort','new')==='new')>{{ __('messages.filters.sort_new') }}</option>
                    <option value="price" @selected(request('sort')==='price')>{{ __('messages.filters.sort_price') }}</option>
                    <option value="views" @selected(request('sort')==='views')>{{ __('messages.filters.sort_views') }}</option>
                </select>
            </div>

            <button class="btn btn-primary btn-sm w-100">{{ __('messages.filters.filter_button') }}</button>
        </form>
    </div>
</div>
