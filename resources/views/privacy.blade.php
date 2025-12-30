@extends('layouts.bazos')

@section('title', __('messages.privacy.title'))

@section('content')
    <div class="container py-4">
        <h1 class="h4 mb-4">{{ __('messages.privacy.title') }}</h1>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ __('messages.privacy.cookie_usage') }}</h5>
                <p class="card-text">
                    {{ __('messages.privacy.cookie_description') }}
                </p>

                <h5 class="card-title mt-4">{{ __('messages.privacy.cookie_types') }}</h5>
                <ul>
                    <li>{{ __('messages.privacy.cookie_essential') }}</li>
                    <li>{{ __('messages.privacy.cookie_preference') }}</li>
                    <li>{{ __('messages.privacy.cookie_analytics') }}</li>
                </ul>

                <h5 class="card-title mt-4">{{ __('messages.privacy.cookie_management') }}</h5>
                <p class="card-text">
                    {{ __('messages.privacy.cookie_control') }}
                </p>
            </div>
        </div>
    </div>
@endsection
