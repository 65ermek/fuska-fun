<!-- resources/views/admin/users/edit.blade.php -->
@extends('layouts.admin')

@section('title', __('admin.edit_user'))

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">@lang('admin.edit_user')</h1>
                </div>
                <div class="col-sm-6">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary float-right">
                        <i class="fas fa-arrow-left"></i> @lang('admin.back_to_list')
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
                    <h3 class="card-title">@lang('admin.editing_user'): {{ $user->name }}</h3>
                </div>

                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">@lang('admin.name') *</label>
                            <input type="text" name="name" id="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}"
                                   placeholder="@lang('admin.name_placeholder')" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">@lang('admin.email') *</label>
                            <input type="email" name="email" id="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}"
                                   placeholder="@lang('admin.email_placeholder')" required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">@lang('admin.password')</label>
                            <input type="password" name="password" id="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="@lang('admin.password_leave_empty')">
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">@lang('admin.password_min')</small>
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">@lang('admin.confirm_password')</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   class="form-control"
                                   placeholder="@lang('admin.confirm_password_placeholder')">
                        </div>

                        <div class="form-group">
                            <label for="role">@lang('admin.role') *</label>
                            <select name="role" id="role"
                                    class="form-control @error('role') is-invalid @enderror" required>
                                <option value="">@lang('admin.select_role')</option>
                                <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>@lang('admin.user')</option>
                                <option value="manager" {{ old('role', $user->role) == 'manager' ? 'selected' : '' }}>@lang('admin.manager')</option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>@lang('admin.admin')</option>
                            </select>
                            @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> @lang('admin.save_changes')
                        </button>

                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-info float-right">
                            <i class="fas fa-eye"></i> @lang('admin.view')
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">@lang('admin.information')</h3>
                </div>
                <div class="card-body">
                    <p><strong>ID:</strong> {{ $user->id }}</p>
                    <p><strong>@lang('admin.created_at'):</strong> {{ $user->created_at->format('d.m.Y H:i') }}</p>
                    <p><strong>@lang('admin.updated_at'):</strong> {{ $user->updated_at->format('d.m.Y H:i') }}</p>

                    @if($user->email_verified_at)
                        <p><strong>@lang('admin.email_verified'):</strong>
                            <span class="badge badge-success">@lang('admin.yes')</span>
                        </p>
                    @else
                        <p><strong>@lang('admin.email_verified'):</strong>
                            <span class="badge badge-warning">@lang('admin.no')</span>
                        </p>
                    @endif
                </div>
            </div>

            @if($user->id !== auth()->id())
                <div class="card card-danger">
                    <div class="card-header">
                        <h3 class="card-title">@lang('admin.danger_zone')</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                              onsubmit="return confirm('@lang('admin.confirm_delete')')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-block">
                                <i class="fas fa-trash"></i> @lang('admin.delete_user')
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
