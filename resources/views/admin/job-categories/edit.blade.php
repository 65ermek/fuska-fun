<!-- resources/views/admin/job-categories/edit.blade.php -->
@extends('layouts.admin')

@section('title', __('admin.edit_category'))

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">@lang('admin.edit_category')</h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-right">
                        <a href="{{ route('admin.job-categories.show', $jobCategory) }}" class="btn btn-info mr-2">
                            <i class="fas fa-eye"></i> @lang('admin.view')
                        </a>
                        <a href="{{ route('admin.job-categories.index') }}" class="btn btn-secondary">
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
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">@lang('admin.edit_category_form')</h3>
                </div>
                <form action="{{ route('admin.job-categories.update', $jobCategory) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">@lang('admin.name') *</label>
                            <input type="text" name="name" id="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $jobCategory->name) }}"
                                   placeholder="@lang('admin.category_name_placeholder')"
                                   required
                                   oninput="generateSlug(this.value)">
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="slug">@lang('admin.slug') *</label>
                            <input type="text" name="slug" id="slug"
                                   class="form-control @error('slug') is-invalid @enderror"
                                   value="{{ old('slug', $jobCategory->slug) }}"
                                   placeholder="@lang('admin.slug_placeholder')"
                                   required>
                            @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                @lang('admin.slug_help')
                            </small>
                        </div>
                        <!-- Текущее изображение -->
                        @if($jobCategory->image)
                            <div class="form-group">
                                <label>@lang('admin.current_image')</label>
                                <div class="mt-2">
                                    @if(Storage::disk('public')->exists($jobCategory->image))
                                        <img src="{{ asset('images/' . $jobCategory->image) }}"
                                             alt="{{ $jobCategory->name }}"
                                             class="img-thumbnail"
                                             style="max-height: 300px;">
                                    @else
                                        <!-- Если файл в storage не найден, используем изображение из public -->
                                        <img src="{{ asset('images/' . $jobCategory->image) }}"
                                             alt="{{ $jobCategory->name }}"
                                             class="img-thumbnail"
                                             style="max-height: 300px;">
                                    @endif
                                    <div class="mt-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input"
                                                   name="remove_image" id="remove_image" value="1">
                                            <label class="custom-control-label text-danger" for="remove_image">
                                                @lang('admin.remove_image')
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Загрузка нового изображения -->
                        <div class="form-group">
                            <label for="image">@lang('admin.category_image')</label>
                            <div class="custom-file">
                                <input type="file" name="image" id="image"
                                       class="custom-file-input @error('image') is-invalid @enderror"
                                       accept="image/*">
                                <label class="custom-file-label" for="image" id="image-label">
                                    @if($jobCategory->image)
                                        @lang('admin.replace_image')
                                    @else
                                        @lang('admin.choose_file')
                                    @endif
                                </label>
                                @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                @lang('admin.image_help')
                            </small>
                        </div>

                        <!-- Превью нового изображения -->
                        <div class="form-group" id="image-preview-container" style="display: none;">
                            <label>@lang('admin.new_image_preview')</label>
                            <div class="mt-2">
                                <img id="image-preview" src="#" alt="Preview" class="img-thumbnail" style="max-height: 200px;">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="sort">@lang('admin.sort_order')</label>
                            <input type="number" name="sort" id="sort"
                                   class="form-control @error('sort') is-invalid @enderror"
                                   value="{{ old('sort', $jobCategory->sort) }}"
                                   placeholder="@lang('admin.sort_order_placeholder')">
                            @error('sort')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input"
                                       name="is_active" id="is_active" value="1"
                                    {{ old('is_active', $jobCategory->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">@lang('admin.active')</label>
                            </div>
                            <small class="form-text text-muted">
                                @lang('admin.active_help')
                            </small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> @lang('admin.update_category')
                        </button>
                        <a href="{{ route('admin.job-categories.show', $jobCategory) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> @lang('admin.cancel')
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Боковая панель с информацией -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@lang('admin.category_info')</h3>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('admin.id')
                            <span class="badge badge-primary badge-pill">{{ $jobCategory->id }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('admin.created_at')
                            <small class="text-muted">{{ $jobCategory->created_at->format('d.m.Y H:i') }}</small>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('admin.updated_at')
                            <small class="text-muted">{{ $jobCategory->updated_at->format('d.m.Y H:i') }}</small>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('admin.jobs_count')
                            <span class="badge badge-info badge-pill">{{ $jobCategory->jobs_count ?? 0 }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Быстрые действия -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@lang('admin.quick_actions')</h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.job-categories.show', $jobCategory) }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-eye mr-1"></i> @lang('admin.view_category')
                        </a>
                        <a href="{{ route('admin.job-categories.create') }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-plus mr-1"></i> @lang('admin.create_new_category')
                        </a>
                        @if(!$jobCategory->deleted_at)
                            <form action="{{ route('admin.job-categories.destroy', $jobCategory) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100 text-left"
                                        onclick="return confirm('@lang('admin.confirm_delete')')">
                                    <i class="fas fa-trash mr-1"></i> @lang('admin.delete_category')
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Автоматическое создание slug из названия
        function generateSlug(name) {
            if (!name) return;

            const slugField = document.getElementById('slug');
            if (!slugField.value || slugField.dataset.manual !== 'true') {
                const slug = name
                    .toLowerCase()
                    .trim()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/[\s_-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                slugField.value = slug;
            }
        }

        // Пометка ручного редактирования slug
        document.getElementById('slug').addEventListener('input', function() {
            this.dataset.manual = 'true';
        });

        // Превью загружаемого изображения
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('image-preview');
            const container = document.getElementById('image-preview-container');
            const label = document.getElementById('image-label');

            if (file) {
                // Обновляем название файла в label
                label.textContent = file.name;

                // Показываем превью
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    container.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                label.textContent = '{{ $jobCategory->image ? __("admin.replace_image") : __("admin.choose_file") }}';
                container.style.display = 'none';
            }
        });

        // Инициализация BS custom file input
        document.addEventListener('DOMContentLoaded', function() {
            bsCustomFileInput.init();
        });
    </script>
@endpush
