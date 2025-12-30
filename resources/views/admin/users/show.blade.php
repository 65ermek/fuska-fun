<!-- resources/views/admin/users/show.blade.php -->
@extends('layouts.admin')

@section('title', __('admin.user_details'))

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">@lang('admin.view_user')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">@lang('admin.users')</a></li>
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
                             src="{{ $user->avatar_url }}"
                             alt="{{ $user->name }}"
                             style="width: 100px; height: 100px; object-fit: cover;">
                    </div>

                    <h3 class="profile-username text-center">{{ $user->name }}</h3>

                    <p class="text-muted text-center">
                        <span class="badge badge-{{
                            $user->role === 'admin' ? 'success' :
                            ($user->role === 'manager' ? 'warning' : 'info')
                        }}">
                            @lang('admin.' . $user->role)
                        </span>
                    </p>
                </div>
            </div>

            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">@lang('admin.actions')</h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-edit"></i> @lang('admin.edit')
                    </a>

                    @if($user->id !== auth()->id())
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('@lang('admin.are_you_sure')')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-block">
                                <i class="fas fa-trash"></i> @lang('admin.delete')
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@lang('admin.user_information')</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong><i class="fas fa-user mr-1"></i> @lang('admin.name')</strong>
                            <p class="text-muted">{{ $user->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong><i class="fas fa-envelope mr-1"></i> @lang('admin.email')</strong>
                            <p class="text-muted">{{ $user->email }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <strong><i class="fas fa-shield-alt mr-1"></i> @lang('admin.role')</strong>
                            <p class="text-muted">
                                <span class="badge badge-{{
                                    $user->role === 'admin' ? 'success' :
                                    ($user->role === 'manager' ? 'warning' : 'info')
                                }}">
                                    @lang('admin.' . $user->role)
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <strong><i class="fas fa-calendar mr-1"></i> @lang('admin.registered')</strong>
                            <p class="text-muted">{{ $user->created_at->format('d.m.Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <strong><i class="fas fa-sync mr-1"></i> @lang('admin.updated_at')</strong>
                            <p class="text-muted">{{ $user->updated_at->format('d.m.Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong><i class="fas fa-key mr-1"></i> @lang('admin.email_verified')</strong>
                            <p class="text-muted">
                                @if($user->email_verified_at)
                                    <span class="badge badge-success">@lang('admin.yes')</span>
                                @else
                                    <span class="badge badge-warning">@lang('admin.no')</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
