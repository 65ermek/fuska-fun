@extends('layouts.bazos')

@section('title', __('messages.my_ads.title'))

@section('content')
    <h5 class="mb-3">{{ __('messages.my_ads.page_title') }}</h5>

    {{-- vždy zobrazíme blok pro obnovení --}}
    {{-- vždy zobrazíme blok pro obnovení --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h6 class="mb-3">{{ __('messages.my_ads.recover_title') }}</h6>

            @php
                $firstJob = $jobs->first();
                // 🔥 ПРИОРИТЕТ: СЕССИЯ -> ОБЪЯВЛЕНИЯ -> ПУСТО
                $prefilledEmail = session('customer_email') ?? ($firstJob->email ?? '');
                $prefilledPhone = session('customer_phone') ?? ($firstJob->phone ?? '');
            @endphp

            {{-- 🔥 ОТЛАДОЧНАЯ ИНФОРМАЦИЯ --}}
            @if(session('customer_email'))
                <div class="alert alert-info mb-3 py-2">
                    <small>
                        <strong>👤 Aktuální uživatel:</strong>
                        {{ session('customer_name') }} ({{ session('customer_email') }})
                    </small>
                </div>
            @endif

            <form method="POST" action="{{ route('jobs.recover') }}"
                  id="recoverForm"
                  class="d-flex flex-wrap align-items-center gap-2">
                @csrf

                <input type="email"
                       name="email"
                       id="recoverEmail"
                       class="form-control form-control-sm"
                       placeholder="{{ __('messages.my_ads.email_placeholder') }}"
                       value="{{ old('email', session('customer_email') ?? session('user_email') ?? '') }}"
                       style="max-width: 260px;"
                       required>
                @if(session('customer_phone'))
                    <span class="small text-muted ms-1">
                        {{ __('messages.my_ads.phone_label') }} {{ session('customer_phone') }}
                    </span>
                @endif

                <button type="submit" class="btn btn-primary btn-sm px-3 ms-auto">
                    {{ __('messages.my_ads.recover_button') }}
                </button>
            </form>
        </div>
    </div>

    @if($jobs->isEmpty())
        <div class="alert alert-info">
            {{ __('messages.my_ads.empty_message') }}
        </div>
    @else
        {{-- záhlaví uživatele (bez duplicity e-mailu, protože už je nahoře) --}}
        <div class="bzs-user-header border rounded bg-white p-3 mb-3 shadow-sm">
            <div class="mt-0">
                <ul class="nav nav-pills small">
                    <li class="nav-item me-2">
                        <a class="nav-link {{ !request('archiv') ? 'active' : '' }}" href="{{ route('jobs.my') }}">{{ __('messages.my_ads.tabs.ads') }}</a>
                    </li>
                    <li class="nav-item me-2">
                        <a class="nav-link {{ request('archiv') ? 'active' : '' }}" href="{{ route('jobs.my', ['archiv' => 1]) }}">{{ __('messages.my_ads.tabs.archive') }}</a>
                    </li>
                    <li class="nav-item me-2">
                        <a class="nav-link disabled" href="#">{{ __('messages.my_ads.tabs.ratings') }}</a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- seznam inzerátů --}}
        <div class="listainzerat inzeratyflex">
            <div class="inzeratynadpis">{{ __('messages.my_ads.list_header.all_ads', ['count' => count($jobs)]) }}</div>
            <div class="inzeratycena"><b>{{ __('messages.my_ads.list_header.price') }}</b></div>
            <div class="inzeratylok">{{ __('messages.my_ads.list_header.location') }}</div>
            <div class="inzeratyview">{{ __('messages.my_ads.list_header.views') }}</div>
        </div>
        @foreach($jobs as $job)
            @php
                $src = $job->photos->first()->path ?? '/images/default.png';
            @endphp

            <div class="inzeraty-wrapper"> {{-- обёртка для hover --}}
                <a href="{{ route('jobs.show', $job->slug) }}" class="text-reset text-decoration-none">
                    <div class="inzeratyflex">
                        <div class="inzeratynadpis">
                            <img src="{{ $src }}"
                                 class="obrazek"
                                 alt="{{ $job->title }}"
                                 width="170" height="128"
                                 style="object-fit:cover;">

                            <div class="d-flex align-items-center flex-wrap mt-2">
                                <h2 class="nadpis me-2 mb-0">{{ $job->title }}</h2>

                                @if($job->top || $job->paid_at)
                                    <span class="ztop mx-1">{{ __('messages.my_ads.ad.top') }}</span>
                                @endif

                                <span class="velikost10 text-muted">[{{ $job->created_at->format('j.n. Y') }}]</span>
                            </div>

                            <div class="popis">
                                {{ \Illuminate\Support\Str::limit(strip_tags($job->description), 220) }}
                            </div>
                        </div>

                        <div class="inzeratycena">
                            <b><span translate="no">{{ $job->price_label ?? __('messages.my_ads.ad.price_negotiable') }}</span></b>
                        </div>

                        <div class="inzeratylok">
                            {{ $job->city ?? __('messages.my_ads.ad.no_location') }}<br>{{ $job->district ?? '' }}
                        </div>

                        <div class="inzeratyview">
                            {{ $job->views ?? 0 }} ×
                        </div>
                    </div>
                </a>

                <div class="inzeratyupdatedel">
                    <a href="{{ route('jobs.manage', $job->slug) }}">{{ __('messages.my_ads.ad.manage') }}</a>
                </div>
            </div>
        @endforeach

        @if($jobs->isEmpty())
            <div class="alert alert-info mt-3">{{ __('messages.my_ads.no_ads') }}</div>
        @endif
    @endif
@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const recoverEmail = document.getElementById('recoverEmail');
            const currentEmail = "{{ session('customer_email') }}".trim();

            if (recoverEmail) {
                let debounceTimer;

                recoverEmail.addEventListener('input', function(e) {
                    const email = e.target.value.trim();

                    if (email && isValidEmail(email) && email !== currentEmail) {
                        clearTimeout(debounceTimer);
                        debounceTimer = setTimeout(() => {
                            document.getElementById('recoverForm').submit();
                        }, 1500);
                    }
                });
            }

            function isValidEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }
        });
    </script>
@endpush
