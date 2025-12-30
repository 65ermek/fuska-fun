<!-- resources/views/admin/job-categories/create.blade.php -->
@extends('layouts.admin')

@section('title', __('admin.create_category'))

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">@lang('admin.create_category')</h1>
                </div>
                <div class="col-sm-6">
                    <a href="{{ route('admin.job-categories.index') }}" class="btn btn-secondary float-right">
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
                    <h3 class="card-title">@lang('admin.create_category_form')</h3>
                </div>
                <form action="{{ route('admin.job-categories.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- Скрытое поле для slug -->
                    <input type="hidden" name="slug" id="slug" value="{{ old('slug') }}">

                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">@lang('admin.category_name_placeholder') *</label>
                            <input type="text" name="name" id="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}"
                                   placeholder="@lang('admin.category_name_placeholder')"
                                   required
                                   oninput="generateSlug(this.value)">
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="image">@lang('admin.category_image')</label>
                            <div class="custom-file">
                                <input type="file" name="image" id="image"
                                       class="custom-file-input @error('image') is-invalid @enderror"
                                       accept="image/*">
                                <label class="custom-file-label" for="image" id="image-label">
                                    @lang('admin.choose_file')
                                </label>
                                @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                @lang('admin.image_help')
                            </small>
                        </div>

                        <!-- Превью изображения -->
                        <div class="form-group" id="image-preview-container" style="display: none;">
                            <label>@lang('admin.image_preview')</label>
                            <div class="mt-2">
                                <img id="image-preview" src="#" alt="Preview" class="img-thumbnail" style="max-height: 200px;">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="sort">@lang('admin.sort_order')</label>
                            <input type="number" name="sort" id="sort"
                                   class="form-control @error('sort') is-invalid @enderror"
                                   value="{{ old('sort', $nextSortOrder) }}"
                                   placeholder="@lang('admin.sort_order_placeholder')">
                            @error('sort')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                @lang('admin.sort_help', ['next' => $nextSortOrder])
                            </small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> @lang('admin.create_category')
                        </button>
                        <a href="{{ route('admin.job-categories.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> @lang('admin.cancel')
                        </a>
                    </div>
                </form>
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
            const slug = name
                .toLowerCase()
                .trim()
                .replace(/[^\w\s-]/g, '')
                .replace(/[\s_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
            slugField.value = slug;
        }

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
                label.textContent = '@lang('admin.choose_file')';
                container.style.display = 'none';
            }
        });

        // Инициализация BS custom file input
        document.addEventListener('DOMContentLoaded', function() {
            bsCustomFileInput.init();
            // Генерируем slug при загрузке, если есть значение в name
            const nameField = document.getElementById('name');
            if (nameField.value) {
                generateSlug(nameField.value);
            }
        });
    </script>
@endpush
