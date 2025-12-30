<header class="bzs-header border-bottom">
    {{-- Верхняя панель --}}
    <div class="container py-2 d-flex align-items-center justify-content-between flex-wrap gap-2">

        {{-- 🟦 Логотип --}}
        <a href="{{ route('jobs.index') }}" class="text-decoration-none fw-bold fs-4 text-primary">
            Fuska.fun
        </a>
        {{-- 🔹 Правый блок: ссылки и язык --}}
        <div class="d-flex align-items-center gap-3 flex-wrap">

            {{-- ⭐ Избранные --}}
            <a href="{{ route('jobs.favorites') }}" class="text-decoration-none text-secondary small d-none d-md-inline">
                <i class="far fa-star me-1"></i> {{ __('messages.navigation.favorites') }}
            </a>
            <a href="{{ route('jobs.favorites') }}" class="text-secondary fs-5 d-md-none" title="{{ __('messages.navigation.favorites') }}">
                <i class="far fa-star"></i>
            </a>

            {{-- 👤 Мои объявления --}}
            <a href="{{ route('jobs.my') }}" class="text-decoration-none text-secondary small d-none d-md-inline">
                <i class="far fa-user me-1"></i> {{ __('messages.navigation.my_ads') }}
            </a>
            <a href="{{ route('jobs.my') }}" class="text-secondary fs-5 d-md-none" title="{{ __('messages.navigation.my_ads') }}">
                <i class="far fa-user"></i>
            </a>

            {{-- ➕ Добавить объявление --}}
            <a href="{{ route('jobs.create') }}"
               class="btn btn-success btn-sm px-3 d-none d-md-inline">
                + {{ __('messages.navigation.add_ad') }}
            </a>
            <a href="{{ route('jobs.create') }}"
               class="btn btn-success btn-sm d-md-none"
               title="{{ __('messages.navigation.add_ad') }}">
                <i class="fas fa-plus"></i>
            </a>

            {{-- 🌐 Переключатель языка --}}
            @php
                $supported = (array) config('locales.supported', ['cs']);
                $labels    = (array) config('locales.labels', []);
            @endphp

            {{-- десктоп --}}
            <form action="{{ route('set-locale') }}" method="POST"
                  class="mb-0 d-none d-md-block">
                @csrf
                <select name="locale"
                        class="form-select form-select-sm"
                        style="min-width:120px"
                        onchange="this.form.submit()">
                    @foreach($supported as $loc)
                        <option value="{{ $loc }}" @selected(app()->getLocale()===$loc)>
                            {{ $labels[$loc] ?? strtoupper($loc) }}
                        </option>
                    @endforeach
                </select>
            </form>

            {{-- мобилка: просто иконка языка --}}
            <div class="dropdown d-md-none">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle"
                        type="button"
                        id="langMenu"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                    🌐
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="langMenu">
                    @foreach($supported as $loc)
                        <li>
                            <form action="{{ route('set-locale') }}" method="POST" class="m-0">
                                @csrf
                                <input type="hidden" name="locale" value="{{ $loc }}">
                                <button type="submit" class="dropdown-item">
                                    {{ $labels[$loc] ?? strtoupper($loc) }}
                                </button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    {{-- Нижняя панель: фильтры (только десктоп) --}}
    <div class="border-top bg-light d-none d-md-block">
        <div class="container py-2">
            <form method="get"
                  class="bzs-filterbar row gx-2 gy-2 align-items-center mb-0">

                <input type="hidden" name="category" value="{{ request('category') }}">

                {{-- 🔍 Поиск --}}
                <div class="col-12 col-md-3">
                    <input type="text" name="q"
                           class="form-control form-control-sm w-100"
                           placeholder="{{ __('messages.navigation.search_placeholder') }}"
                           value="{{ request('q') }}">
                </div>

                {{-- 🏙️ Город --}}
                <div class="col-12 col-md-2">
                    <input type="text" name="city"
                           class="form-control form-control-sm w-100"
                           placeholder="{{ __('messages.navigation.city_placeholder') }}"
                           value="{{ request('city') }}">
                </div>

                {{-- 💰 Оплата --}}
                <div class="col-6 col-md-2">
                    <select name="pay" class="form-select form-select-sm w-100">
                        <option value="">{{ __('messages.navigation.pay_label') }}</option>
                        <option value="per_hour" @selected(request('pay')==='per_hour')>{{ __('messages.navigation.pay_per_hour') }}</option>
                        <option value="per_job"  @selected(request('pay')==='per_job')>{{ __('messages.navigation.pay_per_job') }}</option>
                    </select>
                </div>

                {{-- ⚖️ Сортировка --}}
                <div class="col-6 col-md-3">
                    <select name="sort" class="form-select form-select-sm w-100">
                        <option value="new"  @selected(request('sort','new')==='new')>{{ __('messages.navigation.sort_new') }}</option>
                        <option value="price"@selected(request('sort')==='price')>{{ __('messages.navigation.sort_price') }}</option>
                    </select>
                </div>

                {{-- 🔘 Кнопка --}}
                <div class="col-12 col-md-2 text-md-end">
                    <button class="btn btn-outline-primary btn-sm w-100 w-md-auto">
                        {{ __('messages.navigation.filter_button') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @include('components.filters-mobile', ['category' => request('category')])
</header>
