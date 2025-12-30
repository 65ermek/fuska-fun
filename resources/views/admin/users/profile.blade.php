<!-- resources/views/admin/users/profile.blade.php -->
@extends('layouts.admin')

@section('title', __('admin.my_profile'))

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">@lang('admin.my_profile')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
                        <li class="breadcrumb-item active">@lang('admin.profile')</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-3">
            <!-- Profile Image -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center position-relative">
                        <!-- Аватарка с карандашиком -->
                        <div class="avatar-container position-relative d-inline-block">
                            <img class="profile-user-img img-fluid img-circle"
                                 src="{{ Auth::user()->avatar_url }}"
                                 alt="{{ Auth::user()->name }}"
                                 id="avatar-preview"
                                 style="width: 100px; height: 100px; object-fit: cover;">

                            <!-- Карандашик для редактирования -->
                            <div class="avatar-edit position-absolute" style="bottom: 5px; right: 5px;">
                                <label for="avatar-upload" class="btn btn-primary btn-sm rounded-circle p-0" style="width: 30px; height: 30px; cursor: pointer;" title="@lang('admin.change_avatar')">
                                    <i class="fas fa-pencil-alt" style="font-size: 12px; margin-top: 7px"></i>
                                </label>
                                <input type="file" id="avatar-upload" name="avatar" accept="image/*" style="display: none;">
                            </div>
                        </div>
                    </div>
                    <h3 class="profile-username text-center">{{ Auth::user()->name }}</h3>

                    <p class="text-muted text-center">@lang('admin.' . Auth::user()->role)</p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>@lang('admin.email')</b> <a class="float-right">{{ Auth::user()->email }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>@lang('admin.registered')</b> <a class="float-right">{{ Auth::user()->created_at->format('d.m.Y') }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a class="nav-link active" href="#settings" data-toggle="tab">@lang('admin.settings')</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#avatar" data-toggle="tab">@lang('admin.avatar')</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="active tab-pane" id="settings">
                            <form action="{{ route('admin.profile.update') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <label for="name">@lang('admin.name')</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name', Auth::user()->name) }}">
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="email">@lang('admin.email')</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email" value="{{ old('email', Auth::user()->email) }}">
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="password">@lang('admin.new_password')</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                           id="password" name="password" placeholder="@lang('admin.password_leave_empty')">
                                    @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="password_confirmation">@lang('admin.confirm_password')</label>
                                    <input type="password" class="form-control"
                                           id="password_confirmation" name="password_confirmation" placeholder="@lang('admin.confirm_password_placeholder')">
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">@lang('admin.update_profile')</button>
                                </div>
                            </form>
                        </div>

                        <!-- Новая вкладка для аватарки -->
                        <div class="tab-pane" id="avatar">
                            <form action="{{ route('admin.profile.avatar') }}" method="POST" enctype="multipart/form-data" id="avatar-form">
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <label for="avatar-upload-main">@lang('admin.upload_avatar')</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input @error('avatar') is-invalid @enderror"
                                               id="avatar-upload-main" name="avatar" accept="image/*">
                                        <label class="custom-file-label" for="avatar-upload-main">@lang('admin.choose_file')</label>
                                        @error('avatar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="form-text text-muted">@lang('admin.avatar_tip')</small>
                                </div>

                                <!-- Предпросмотр -->
                                <div class="form-group text-center">
                                    <img src="{{ Auth::user()->avatar_url }}"
                                         id="avatar-main-preview"
                                         class="img-circle elevation-2"
                                         style="width: 150px; height: 150px; object-fit: cover;"
                                         alt="@lang('admin.avatar_preview')">
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-upload"></i> @lang('admin.upload_avatar')
                                    </button>

                                    @if(Auth::user()->avatar)
                                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirmRemoveAvatarModal">
                                            <i class="fas fa-trash"></i> @lang('admin.remove_avatar')
                                        </button>
                                    @endif
                                </div>
                            </form>

                            <!-- Форма удаления аватарки (внутри вкладки) -->
                            @if(Auth::user()->avatar)
                                <form id="removeAvatarForm" action="{{ route('admin.profile.avatar.remove') }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Модальное окно для подтверждения удаления аватарки -->
    <div class="modal fade" id="confirmRemoveAvatarModal" tabindex="-1" role="dialog" aria-labelledby="confirmRemoveAvatarModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmRemoveAvatarModalLabel">@lang('admin.confirm_action')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @lang('admin.confirm_remove_avatar')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('admin.cancel')</button>
                    <button type="button" class="btn btn-danger" id="confirmRemoveAvatar">@lang('admin.delete')</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Предпросмотр при выборе файла в основной форме
            const avatarUploadMain = document.getElementById('avatar-upload-main');
            const avatarPreview = document.getElementById('avatar-main-preview');

            if (avatarUploadMain && avatarPreview) {
                avatarUploadMain.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            avatarPreview.src = e.target.result;
                            const smallPreview = document.getElementById('avatar-preview');
                            if (smallPreview) {
                                smallPreview.src = e.target.result;
                            }
                        }
                        reader.readAsDataURL(file);

                        const fileName = file.name;
                        const nextSibling = e.target.nextElementSibling;
                        if (nextSibling) {
                            nextSibling.innerText = fileName;
                        }
                    }
                });
            }

            // Карандашик - открывает загрузку файла и переключает на вкладку
            const avatarUploadSmall = document.getElementById('avatar-upload');
            if (avatarUploadSmall) {
                avatarUploadSmall.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const avatarTab = document.querySelector('a[href="#avatar"]');
                        if (avatarTab) {
                            avatarTab.click();
                        }

                        const avatarUploadMain = document.getElementById('avatar-upload-main');
                        if (avatarUploadMain) {
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(file);
                            avatarUploadMain.files = dataTransfer.files;
                            const event = new Event('change', { bubbles: true });
                            avatarUploadMain.dispatchEvent(event);
                        }
                    }
                });
            }

            // Удаление аватарки через модальное окно
            const confirmRemoveBtn = document.getElementById('confirmRemoveAvatar');
            if (confirmRemoveBtn) {
                confirmRemoveBtn.addEventListener('click', function() {
                    const form = document.getElementById('removeAvatarForm');
                    if (form) {
                        form.submit();
                    }
                });
            }

            // Инициализация BS custom file input
            if (typeof bsCustomFileInput !== 'undefined') {
                bsCustomFileInput.init();
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
@endpush

@push('styles')
    <style>
        .avatar-container {
            position: relative;
            display: inline-block;
        }

        .avatar-edit {
            position: absolute;
            bottom: 5px;
            right: 5px;
        }

        .avatar-edit label {
            width: 30px;
            height: 30px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #007bff;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }

        .avatar-edit label:hover {
            background: #0056b3;
            transform: scale(1.1);
        }

        .profile-user-img {
            border: 3px solid #dee2e6;
            transition: all 0.3s ease;
        }

        .avatar-container:hover .profile-user-img {
            border-color: #007bff;
        }
    </style>
@endpush
