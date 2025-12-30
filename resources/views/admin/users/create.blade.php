<!-- resources/views/admin/users/create.blade.php -->
@extends('layouts.admin')

@section('title', __('admin.create_user'))

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">@lang('admin.create_user')</h1>
                </div>
                <div class="col-sm-6">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary float-right">
                        <i class="fas fa-arrow-left"></i> @lang('admin.back')
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">@lang('admin.create_user_form')</h3>
                </div>

                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf

                    <!-- Скрытое поле с ролью "manager" -->
                    <input type="hidden" name="role" value="manager">

                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">@lang('admin.name') *</label>
                            <input type="text" name="name" id="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}"
                                   placeholder="@lang('admin.name_placeholder')" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">@lang('admin.email') *</label>
                            <input type="email" name="email" id="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}"
                                   placeholder="@lang('admin.email_placeholder')" required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">@lang('admin.password') *</label>
                            <input type="password" name="password" id="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="@lang('admin.password_placeholder')" required>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">@lang('admin.confirm_password') *</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   class="form-control"
                                   placeholder="@lang('admin.confirm_password_placeholder')" required>
                        </div>

                        <!-- Поле роли скрыто -->
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> @lang('admin.create_user')
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">@lang('admin.tips')</h3>
                </div>
                <div class="card-body">
                    <p><strong>@lang('admin.name'):</strong> @lang('admin.name_tip')</p>
                    <p><strong>@lang('admin.email'):</strong> @lang('admin.email_tip')</p>
                    <p><strong>@lang('admin.password'):</strong> @lang('admin.password_tip')</p>
                    <p><strong>@lang('admin.role'):</strong>
                        <span class="badge badge-info">@lang('admin.manager')</span> - @lang('admin.manager_tip')
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
