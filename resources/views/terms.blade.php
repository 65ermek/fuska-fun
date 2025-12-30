@extends('layouts.bazos')

@section('title', __('terms.title'))

@section('content')
    <div class="container py-4">
        <h1 class="h4 mb-4">{{ __('terms.title') }}</h1>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ __('terms.general.title') }}</h5>
                <p class="card-text">
                    {{ __('terms.general.description') }}
                </p>

                <h5 class="card-title mt-4">{{ __('terms.purpose.title') }}</h5>
                <p class="card-text">
                    {{ __('terms.purpose.description') }}
                </p>
                <ul class="mb-3">
                    <li>{{ __('terms.purpose.employer') }}</li>
                    <li>{{ __('terms.purpose.candidate') }}</li>
                    <li>{{ __('terms.purpose.chat') }}</li>
                </ul>

                <h5 class="card-title mt-4">{{ __('terms.users.title') }}</h5>

                <h6 class="card-subtitle mb-2 text-muted">{{ __('terms.users.employers_title') }}</h6>
                <ul class="mb-3">
                    <li>{{ __('terms.users.employer_post') }}</li>
                    <li>{{ __('terms.users.employer_responses') }}</li>
                    <li>{{ __('terms.users.employer_chat') }}</li>
                    <li>{{ __('terms.users.employer_manage') }}</li>
                </ul>

                <h6 class="card-subtitle mb-2 text-muted">{{ __('terms.users.candidates_title') }}</h6>
                <ul class="mb-3">
                    <li>{{ __('terms.users.candidate_view') }}</li>
                    <li>{{ __('terms.users.candidate_apply') }}</li>
                    <li>{{ __('terms.users.candidate_chat') }}</li>
                    <li>{{ __('terms.users.candidate_profile') }}</li>
                </ul>

                <h5 class="card-title mt-4">{{ __('terms.rules.title') }}</h5>
                <p class="card-text">
                    {{ __('terms.rules.prohibited_description') }}
                </p>
                <ul class="mb-3">
                    <li>{{ __('terms.rules.prepayment') }}</li>
                    <li>{{ __('terms.rules.mlm') }}</li>
                    <li>{{ __('terms.rules.pyramid') }}</li>
                    <li>{{ __('terms.rules.illegal') }}</li>
                    <li>{{ __('terms.rules.czech_law') }}</li>
                    <li>{{ __('terms.rules.duplicate') }}</li>
                    <li>{{ __('terms.rules.incomplete') }}</li>
                </ul>

                <p class="card-text">
                    {{ __('terms.rules.required_description') }}
                </p>
                <ul class="mb-3">
                    <li>{{ __('terms.rules.company_info') }}</li>
                    <li>{{ __('terms.rules.requirements') }}</li>
                    <li>{{ __('terms.rules.responsibilities') }}</li>
                    <li>{{ __('terms.rules.conditions') }}</li>
                </ul>

                <h5 class="card-title mt-4">{{ __('terms.free.title') }}</h5>
                <p class="card-text">
                    {{ __('terms.free.description') }}
                </p>

                <h5 class="card-title mt-4">{{ __('terms.admin_rights.title') }}</h5>
                <ul class="mb-3">
                    <li>{{ __('terms.admin_rights.refuse') }}</li>
                    <li>{{ __('terms.admin_rights.delete') }}</li>
                    <li>{{ __('terms.admin_rights.block') }}</li>
                    <li>{{ __('terms.admin_rights.modify') }}</li>
                </ul>

                <h5 class="card-title mt-4">{{ __('terms.liability.title') }}</h5>
                <ul class="mb-3">
                    <li>{{ __('terms.liability.content') }}</li>
                    <li>{{ __('terms.liability.employment') }}</li>
                    <li>{{ __('terms.liability.platform') }}</li>
                    <li>{{ __('terms.liability.accuracy') }}</li>
                </ul>

                <h5 class="card-title mt-4">{{ __('terms.duration.title') }}</h5>
                <p class="card-text">
                    {{ __('terms.duration.description') }}
                </p>

                <h5 class="card-title mt-4">{{ __('terms.chat.title') }}</h5>
                <ul class="mb-3">
                    <li>{{ __('terms.chat.purpose') }}</li>
                    <li>{{ __('terms.chat.prohibited') }}</li>
                    <li>{{ __('terms.chat.monitoring') }}</li>
                    <li>{{ __('terms.chat.storage') }}</li>
                </ul>

                <h5 class="card-title mt-4">{{ __('terms.data.title') }}</h5>

                <h6 class="card-subtitle mb-2 text-muted">{{ __('terms.data.collect_title') }}</h6>
                <ul class="mb-3">
                    <li>{{ __('terms.data.email') }}</li>
                    <li>{{ __('terms.data.name') }}</li>
                    <li>{{ __('terms.data.resume') }}</li>
                    <li>{{ __('terms.data.company') }}</li>
                </ul>

                <h6 class="card-subtitle mb-2 text-muted">{{ __('terms.data.not_collect_title') }}</h6>
                <ul class="mb-3">
                    <li>{{ __('terms.data.passport') }}</li>
                    <li>{{ __('terms.data.cards') }}</li>
                    <li>{{ __('terms.data.financial') }}</li>
                </ul>

                <h6 class="card-subtitle mb-2 text-muted">{{ __('terms.data.use_title') }}</h6>
                <ul class="mb-3">
                    <li>{{ __('terms.data.use_service') }}</li>
                    <li>{{ __('terms.data.use_connection') }}</li>
                    <li>{{ __('terms.data.use_improvement') }}</li>
                    <li>{{ __('terms.data.use_notifications') }}</li>
                </ul>

                <h6 class="card-subtitle mb-2 text-muted">{{ __('terms.data.not_share_title') }}</h6>
                <ul class="mb-3">
                    <li>{{ __('terms.data.share_third') }}</li>
                    <li>{{ __('terms.data.share_unrelated') }}</li>
                </ul>

                <h5 class="card-title mt-4">{{ __('terms.consent.title') }}</h5>
                <p class="card-text">
                    {{ __('terms.consent.description') }}
                </p>

                <h5 class="card-title mt-4">{{ __('terms.changes.title') }}</h5>
                <p class="card-text">
                    {{ __('terms.changes.description') }}
                </p>

                <h5 class="card-title mt-4">{{ __('terms.disputes.title') }}</h5>
                <p class="card-text">
                    {{ __('terms.disputes.description') }}
                </p>

                <h5 class="card-title mt-4">{{ __('terms.contacts.title') }}</h5>
                <p class="card-text">
                    {{ __('terms.contacts.description') }}
                </p>

                <hr class="my-4">

                <h5 class="card-title">{{ __('terms.summary.title') }}</h5>

                <h6 class="card-subtitle mb-2 text-success">{{ __('terms.summary.allowed_title') }}</h6>
                <ul class="mb-3">
                    <li>{{ __('terms.summary.post_free') }}</li>
                    <li>{{ __('terms.summary.search_free') }}</li>
                    <li>{{ __('terms.summary.chat_safe') }}</li>
                    <li>{{ __('terms.summary.manage_posts') }}</li>
                </ul>

                <h6 class="card-subtitle mb-2 text-danger">{{ __('terms.summary.prohibited_title') }}</h6>
                <ul class="mb-3">
                    <li>{{ __('terms.summary.no_prepayment') }}</li>
                    <li>{{ __('terms.summary.no_dubious') }}</li>
                    <li>{{ __('terms.summary.no_law_violation') }}</li>
                    <li>{{ __('terms.summary.no_spam') }}</li>
                </ul>

                <h6 class="card-subtitle mb-2 text-primary">{{ __('terms.summary.data_title') }}</h6>
                <ul class="mb-3">
                    <li>{{ __('terms.summary.data_contact') }}</li>
                    <li>{{ __('terms.summary.data_no_third') }}</li>
                    <li>{{ __('terms.summary.data_gdpr') }}</li>
                </ul>

                <div class="alert alert-info mt-4">
                    <p class="mb-0">
                        {{ __('terms.agreement') }}
                    </p>
                    <p class="mb-0 mt-2 text-muted">
                        <small>{{ __('terms.last_updated') }}</small>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
