<!-- resources/views/admin/customers/show.blade.php -->
@extends('layouts.admin')

@section('title', __('admin.customer_details'))

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">@lang('admin.view_customer')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">@lang('admin.customers')</a></li>
                        <li class="breadcrumb-item active">@lang('admin.view')</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="card card-primary">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <img class="profile-user-img img-fluid img-circle"
                             src="{{ $customer->avatar_url ?? asset('images/avatars/default-avatar.png') }}"
                             alt="{{ $customer->name }}"
                             style="width: 100px; height: 100px; object-fit: cover;">
                    </div>

                    <h3 class="profile-username text-center">{{ $customer->name }}</h3>
                </div>
            </div>

            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">@lang('admin.actions')</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" onsubmit="return confirm('@lang('admin.are_you_sure')')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block">
                            <i class="fas fa-trash"></i> @lang('admin.delete')
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@lang('admin.customer_information')</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong><i class="fas fa-user mr-1"></i> @lang('admin.name')</strong>
                            <p class="text-muted">{{ $customer->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong><i class="fas fa-envelope mr-1"></i> @lang('admin.email')</strong>
                            <p class="text-muted">{{ $customer->email }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <strong><i class="fas fa-calendar mr-1"></i> @lang('admin.registered')</strong>
                            <p class="text-muted">{{ $customer->created_at->format('d.m.Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong><i class="fas fa-sync mr-1"></i> @lang('admin.updated_at')</strong>
                            <p class="text-muted">{{ $customer->updated_at->format('d.m.Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <strong><i class="fas fa-key mr-1"></i> @lang('admin.email_verified')</strong>
                            <p class="text-muted">
                                @if($customer->email_verified_at)
                                    <span class="badge badge-success">@lang('admin.yes')</span>
                                @else
                                    <span class="badge badge-warning">@lang('admin.no')</span>
                                @endif
                            </p>
                        </div>
                        @if(method_exists($customer, 'jobs'))
                            <div class="col-md-6">
                                <strong><i class="fas fa-briefcase mr-1"></i> @lang('admin.jobs')</strong>
                                <p class="text-muted">{{ $customer->jobs()->count() ?? 0 }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
