<!-- resources/views/admin/users/index.blade.php -->
@extends('layouts.admin')

@section('title', __('admin.user_management'))

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">@lang('admin.users')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
                        <li class="breadcrumb-item active">@lang('admin.users')</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">@lang('admin.user_list')</h3>
            <div class="card-tools">
                <a href="{{ route('admin.users.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i>
                    <span class="d-none d-sm-inline"> @lang('admin.create_user')</span>
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
                <!-- List-group вариант -->
                <div class="table-responsive d-block d-md-none">
                    <div class="list-group">
                        @foreach($users as $user)
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">{{ $user->name }}</h5>
                                    <small class="text-muted">ID: {{ $user->id }}</small>
                                </div>
                                <p class="mb-1">{{ $user->email }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                        <span class="badge badge-{{ $user->role === 'admin' ? 'success' : ($user->role === 'manager' ? 'warning' : 'info') }}">
                            @lang('admin.' . $user->role)
                        </span>
                                        <small class="text-muted">
                                            <i class="far fa-calendar mr-1"></i>
                                            {{ $user->created_at->format('d.m.Y H:i') }}
                                        </small>
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-info" title="@lang('admin.view')">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary" title="@lang('admin.edit')">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger"
                                                    onclick="return confirm('@lang('admin.confirm_delete')')"
                                                    title="@lang('admin.delete')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Оригинальная таблица для десктопов -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>@lang('admin.name')</th>
                            <th>@lang('admin.email')</th>
                            <th>@lang('admin.role')</th>
                            <th>@lang('admin.created_at')</th>
                            <th>@lang('admin.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                        <span class="badge badge-{{ $user->role === 'admin' ? 'success' : 'info' }}">
                            @lang('admin.' . $user->role)
                        </span>
                                </td>
                                <td>{{ $user->created_at->format('d.m.Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-info btn-sm" title="@lang('admin.view')">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary btn-sm" title="@lang('admin.edit')">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('@lang('admin.confirm_delete')')"
                                                title="@lang('admin.delete')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>



            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection
