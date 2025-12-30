@extends('layouts.bazos')

@section('title', __('messages.manage_ad.title'))

@section('content')
    <div class="maincontent">

        {{-- 🔶 1. BLOK: Topování inzerátu --}}
        <div class="oranzovy mb-5">
            <b>{{ __('messages.manage_ad.promotion.title') }}</b><br><br>

            {{ __('messages.manage_ad.promotion.description') }}

            <hr class="my-3">

            <span class="ovse">{{ __('messages.manage_ad.promotion.payment_methods.transfer') }}</span><br>
            <form method="post" action="{{ url('/platba-topovani') }}" class="mb-3">
                @csrf
                <select name="pocetpoukazek" required class="form-select form-select-sm w-auto mb-2">
                    <option value="" selected>{{ __('messages.manage_ad.promotion.count_select') }}</option>
                    @for ($i = 1; $i <= 21; $i++)
                        <option value="{{ $i }}">
                            {{ __('messages.manage_ad.promotion.count_option', ['count' => $i, 'price' => $i * 29]) }}
                        </option>
                    @endfor
                </select>
                <input type="hidden" name="idad" value="{{ $job->id }}">
                <button type="submit" class="btn btn-warning btn-sm">{{ __('messages.manage_ad.promotion.promote_button') }}</button>
            </form>

            <span class="ovse">{{ __('messages.manage_ad.promotion.payment_methods.voucher') }}</span>
            (<a href="https://fuska.fun/napoveda#poukazky" target="_blank">{{ __('messages.manage_ad.promotion.voucher_link') }}</a>)

            <form method="post" action="/topovat-poukazkou" class="mt-2">
                <input type="text" name="kodp" maxlength="20" class="form-control form-control-sm mb-2"
                       placeholder="{{ __('messages.manage_ad.promotion.voucher_placeholder') }}">
                <input type="hidden" name="idad" value="{{ $job->id }}">
                <button type="submit" class="btn btn-warning btn-sm">{{ __('messages.manage_ad.promotion.promote_button') }}</button>
            </form>
        </div>

        {{-- 🔶 2. BLOK: Úprava / vymazání inzerátu --}}
        <div class="oranzovy">
            <b>{{ __('messages.manage_ad.management.title') }}</b><br>
            {{ __('messages.manage_ad.management.description') }}

            <form method="post" action="{{ route('jobs.manage_action', $job->slug) }}" class="mt-3">
                @csrf

                <label for="heslobazar" class="form-label">{{ __('messages.manage_ad.management.password_label') }}</label>
                <input type="text" id="heslobazar" name="heslobazar" maxlength="20" class="form-control form-control-sm w-50 mb-3" required>

                <div class="d-flex gap-2">
                    <button type="submit" name="administrace" value="edit" class="btn btn-primary btn-sm">
                        {{ __('messages.manage_ad.management.edit_button') }}
                    </button>
                    <button type="submit" name="administrace" value="delete" class="btn btn-danger btn-sm">
                        {{ __('messages.manage_ad.management.delete_button') }}
                    </button>
                </div>
            </form>
        </div>

    </div>

    {{-- 🔶 MODÁL: Špatné heslo --}}
    <div class="modal fade" id="wrongPasswordModal" tabindex="-1" aria-labelledby="wrongPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content oranzovy">
                <div class="modal-header">
                    <h5 class="modal-title" id="wrongPasswordModalLabel">{{ __('messages.manage_ad.modal.title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('messages.manage_ad.modal.close') }}"></button>
                </div>
                <div class="modal-body">
                    <p>{!! __('messages.manage_ad.modal.message') !!}</p>

                    <form method="POST" action="{{ route('jobs.request_password') }}">
                        @csrf
                        <input type="text" name="potvrzeni" maxlength="3" class="form-control form-control-sm mb-2" required
                               placeholder="{{ __('messages.manage_ad.modal.confirmation_placeholder') }}">
                        <input type="hidden" name="job_id" value="{{ $job->id }}">
                        <button type="submit" class="btn btn-primary btn-sm">{{ __('messages.manage_ad.modal.send_button') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- 🔶 FLASH ZPRÁVA --}}
    @if(session('success'))
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('messages.manage_ad.modal.close') }}"></button>
            </div>
        </div>
    @endif

    {{-- Aktivace modálu při chybě hesla --}}
    @if(session('wrong_password'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let modal = new bootstrap.Modal(document.getElementById('wrongPasswordModal'));
                modal.show();
            });
        </script>
    @endif
    <style>
        .oranzovy { background: #fff4e5; border: 1px solid #ffc107; padding: 20px; border-radius: 5px;}
    </style>
@endsection
