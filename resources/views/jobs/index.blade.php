@extends('layouts.bazos')

@section('title', __('messages.jobs_listing.title'))

@section('content')
    @php
        $total = $jobs->total();
        $per   = $jobs->perPage();
        $page  = $jobs->currentPage();
        $from  = $total ? (($page - 1) * $per) + 1 : 0;
        $to    = $total ? min($total, $page * $per) : 0;
    @endphp

    {{-- 🔹 Мобильная панель кнопок --}}
    <div class="d-flex d-md-none justify-content-between gap-2 mb-3">
        {{-- Категории --}}
        <button type="button"
                class="btn btn-outline-secondary btn-sm flex-fill"
                id="openCategories">
            {{ __('messages.jobs_listing.categories_button') }}
        </button>

        {{-- Фильтры --}}
        <button type="button"
                class="btn btn-outline-secondary btn-sm flex-fill"
                id="openFilters">
            {{ __('messages.jobs_listing.filters_button') }}
        </button>

        {{-- Новые inzeráty e-mailem (только на мобилке) --}}
        <button type="button"
                class="btn btn-outline-secondary btn-sm flex-fill d-md-none"
                id="subscribeEmail">
            {{ __('messages.jobs_listing.subscribe_button_mobile') }}
        </button>
    </div>
    <div class="maincontent">
        {{-- horní lišta --}}
        <div class="listainzerat inzeratyflex mb-2">
            <div class="inzeratynadpis d-flex align-items-center gap-2">
                {{-- list --}}
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                     fill="currentColor" class="bi bi-list view-toggle"
                     data-view="list" title="{{ __('messages.jobs_listing.list_view_title') }}" style="cursor:pointer">
                    <path fill-rule="evenodd"
                          d="M2.5 12.5a.5.5 0 010-1h15a.5.5 0 010 1h-15zm0-4a.5.5 0 010-1h15a.5.5 0 010 1h-15zm0-4a.5.5 0 010-1h15a.5.5 0 010 1h-15z"/>
                </svg>

                {{-- gallery --}}
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                     fill="currentColor" class="bi bi-grid view-toggle"
                     data-view="gallery" title="{{ __('messages.jobs_listing.gallery_view_title') }}" style="cursor:pointer">
                    <path d="M2 2h4v4H2V2zm0 6h4v4H2V8zm6-6h4v4H8V2zm0 6h4v4H8V8zm6-6h4v4h-4V2zm0 6h4v4h-4V8z"/>
                </svg>

                {{ __('messages.jobs_listing.showing_results', ['from' => $from, 'to' => $to, 'total' => $total]) }}
            </div>

            <div class="inzeratycena">
                <b><a href="{{ request()->fullUrlWithQuery(['sort' => 'price']) }}" class="paction">{{ __('messages.jobs_listing.salary') }}</a></b>
            </div>
            <div class="inzeratylok">{{ __('messages.jobs_listing.location') }}</div>
            <div class="inzeratyview">
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'views']) }}" class="paction">{{ __('messages.jobs_listing.views') }}</a>
            </div>
        </div>

        {{-- кнопка "nové inzeráty e-mailem" (только на десктопе) --}}
        <button type="button"
                class="btn btn-sm btn-outline-secondary mb-3 d-none d-md-inline-block">
            {{ __('messages.jobs_listing.subscribe_button_desktop') }}
        </button>

        {{-- ✅ вид "список" --}}
        <div id="jobsList" class="jobs-view">
            @include('jobs.partials.list', ['jobs' => $jobs])
        </div>

        {{-- ✅ вид "галерея" --}}
        <div id="jobsGallery" class="jobs-view d-none">
            @include('jobs.partials.gallery', ['jobs' => $jobs])
        </div>

        {{-- paginace --}}
        <div class="page-link mt-3">
            {{ $jobs->withQueryString()->links('components.pagination') }}
        </div>
    </div>

    {{-- мобильное меню категорий --}}
    @include('components.categories-mobile', [
        'categories' => $categories ?? null,
        'active'     => request('category')
    ])
@endsection


<script>
    document.addEventListener('DOMContentLoaded', () => {
        const listEl    = document.getElementById('jobsList');
        const galleryEl = document.getElementById('jobsGallery');
        const toggles   = document.querySelectorAll('.view-toggle');

        // по умолчанию: десктоп = list, мобилка = gallery
        const isMobile = window.innerWidth < 768;
        const saved    = localStorage.getItem('viewMode');

        if (isMobile) {
            listEl.classList.add('d-none');
            galleryEl.classList.remove('d-none');
        } else {
            // если было сохранено - подставим
            if (saved === 'gallery') {
                listEl.classList.add('d-none');
                galleryEl.classList.remove('d-none');
            }
        }

        toggles.forEach(btn => {
            btn.addEventListener('click', () => {
                const mode = btn.dataset.view;

                if (mode === 'gallery') {
                    listEl.classList.add('d-none');
                    galleryEl.classList.remove('d-none');
                } else {
                    galleryEl.classList.add('d-none');
                    listEl.classList.remove('d-none');
                }

                // сохраняем только для десктопа
                if (!isMobile) {
                    localStorage.setItem('viewMode', mode);
                }
            });
        });
    });
    document.addEventListener('DOMContentLoaded', function () {
        // === фильтры ===
        const openF = document.getElementById('openFilters');
        const closeF = document.getElementById('closeFilters');
        const overlayF = document.getElementById('mobileFilters');

        if (overlayF) {
            function openFilters() {
                overlayF.classList.add('show');
            }
            function closeFilters() {
                overlayF.classList.remove('show');
            }
            if (openF) openF.addEventListener('click', openFilters);
            if (closeF) closeF.addEventListener('click', closeFilters);

            overlayF.addEventListener('click', function(e){
                if (e.target === overlayF) closeFilters();
            });
        }
    });
</script>
