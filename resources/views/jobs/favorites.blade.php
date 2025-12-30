@extends('layouts.bazos')

@section('title', __('messages.favorites.title'))

@section('content')
    <h5 class="mb-3">{{ __('messages.favorites.page_title') }}</h5>

    @if($favorites->isEmpty())
        <div class="alert alert-info">
            {{ __('messages.favorites.empty_message') }}
        </div>
    @else
        <div class="listainzerat inzeratyflex">
            <div class="inzeratynadpis">{{ __('messages.favorites.list_header.title', ['count' => count($favorites)]) }}</div>
            <div class="inzeratycena"><b>{{ __('messages.favorites.list_header.price') }}</b></div>
            <div class="inzeratylok">{{ __('messages.favorites.list_header.location') }}</div>
            <div class="inzeratyview">{{ __('messages.favorites.list_header.views') }}</div>
        </div>

        @foreach($favorites as $job)
            @php
                $src = $job->photos->first()->path ?? '/images/default.png';
            @endphp

            <div class="inzeraty-wrapper">
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
                                    <span class="ztop mx-1">{{ __('messages.favorites.ad.top') }}</span>
                                @endif

                                <span class="velikost10 text-muted">[{{ $job->created_at->format('j.n. Y') }}]</span>
                            </div>

                            <div class="popis">
                                {{ \Illuminate\Support\Str::limit(strip_tags($job->description), 220) }}
                            </div>
                        </div>

                        <div class="inzeratycena">
                            <b><span translate="no">{{ $job->price_label ?? __('messages.favorites.ad.price_negotiable') }}</span></b>
                        </div>

                        <div class="inzeratylok">
                            {{ $job->city ?? __('messages.favorites.ad.no_location') }}<br>{{ $job->district ?? '' }}
                        </div>

                        <div class="inzeratyview">
                            {{ $job->views ?? 0 }} ×
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    @endif
@endsection

<style>
    .inzeraty-wrapper { position: relative;  margin-bottom: 20px;}
    .inzeratyupdatedel {
        display: none; /* скрыто */
    }
    .inzeraty-wrapper:hover .inzeratyflex {
        background-color: #bbbbbb;
        transition: background-color 0.2s ease-in-out;
    }
</style>
