{{-- resources/views/admin/contact_requests/show.blade.php --}}
@extends('layouts.admin')

@section('title', __('admin.contact_request_details'))

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">@lang('admin.contact-request_details')</h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-right">
                        <a href="{{ route('admin.contact-requests.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> @lang('admin.back_to_list')
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@lang('admin.message_content')</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>@lang('admin.message'):</strong>
                        <div class="border rounded p-3 mt-2 bg-light">
                            {{ $contactRequest->message }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <strong>@lang('admin.sender_name'):</strong>
                            <p>{{ $contactRequest->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>@lang('admin.email'):</strong>
                            <p><a href="mailto:{{ $contactRequest->email }}">{{ $contactRequest->email }}</a></p>
                        </div>
                    </div>

                    @if($contactRequest->phone)
                        <div class="row">
                            <div class="col-md-6">
                                <strong>@lang('admin.phone'):</strong>
                                <p>{{ $contactRequest->phone }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@lang('admin.details')</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>@lang('admin.status'):</strong>
                        <div>
                            <span class="badge badge-{{ $contactRequest->status_color }}">
                                {{ $contactRequest->status_text }}
                            </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>@lang('admin.job'):</strong>
                        <div>
                            <a href="{{ route('admin.jobs.show', $contactRequest->job_id) }}">
                                {{ $contactRequest->job->title }}
                            </a>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>@lang('admin.ip_address'):</strong>
                        <p><code>{{ $contactRequest->ip_address }}</code></p>
                    </div>

                    <div class="mb-3">
                        <strong>@lang('admin.user_agent'):</strong>
                        <p><small class="text-muted">{{ $contactRequest->user_agent }}</small></p>
                    </div>

                    <div class="mb-3">
                        <strong>@lang('admin.created_at'):</strong>
                        <p>{{ $contactRequest->created_at->format('d.m.Y H:i:s') }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>@lang('admin.updated_at'):</strong>
                        <p>{{ $contactRequest->updated_at->format('d.m.Y H:i:s') }}</p>
                    </div>
                </div>
                <div class="card-footer">
                    <form action="{{ route('admin.contact-requests.destroy', $contactRequest) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger"
                                onclick="return confirm('@lang('admin.delete_contact_confirm')')">
                            <i class="fas fa-trash"></i> @lang('admin.delete_contact')
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
