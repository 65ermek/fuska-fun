@extends('layouts.admin')

@section('title', __('admin.edit_job'))

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">@lang('admin.edit_job')</h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-right">
                        <a href="{{ route('admin.jobs.show', $job) }}" class="btn btn-info mr-2">
                            <i class="fas fa-eye"></i> @lang('admin.view')
                        </a>
                        <a href="{{ route('admin.jobs.index') }}" class="btn btn-secondary">
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
                    <h3 class="card-title">@lang('admin.edit_job_form')</h3>
                </div>

                <form action="{{ route('admin.jobs.update', $job) }}" method="POST" enctype="multipart/form-data" id="job-edit-form">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <!-- Основные поля (без изменений) -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="job_category_id">@lang('admin.category') *</label>
                                    <select name="job_category_id" id="job_category_id"
                                            class="form-control @error('job_category_id') is-invalid @enderror" required>
                                        <option value="">@lang('admin.select_category')</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('job_category_id', $job->job_category_id) == $category->id ? 'selected' : '' }}>
                                                {{ __('categories.' . $category->slug) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('job_category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">@lang('admin.status') *</label>
                                    <select name="status" id="status"
                                            class="form-control @error('status') is-invalid @enderror" required>
                                        <option value="published" {{ old('status', $job->status) == 'published' ? 'selected' : '' }}>@lang('admin.active')</option>
                                        <option value="pending" {{ old('status', $job->status) == 'pending' ? 'selected' : '' }}>@lang('admin.pending')</option>
                                        <option value="hidden" {{ old('status', $job->status) == 'hidden' ? 'selected' : '' }}>@lang('admin.rejected')</option>
                                    </select>
                                    @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="title">@lang('admin.title') *</label>
                            <input type="text" name="title" id="title"
                                   class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title', $job->title) }}"
                                   placeholder="@lang('admin.title_placeholder')"
                                   required maxlength="120">
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">@lang('admin.description') *</label>
                            <textarea name="description" id="description"
                                      class="form-control @error('description') is-invalid @enderror"
                                      rows="6"
                                      placeholder="@lang('admin.description_placeholder')"
                                      required>{{ old('description', $job->description) }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="city">@lang('admin.city') *</label>
                                    <input type="text" name="city" id="city"
                                           class="form-control @error('city') is-invalid @enderror"
                                           value="{{ old('city', $job->city) }}"
                                           placeholder="@lang('admin.city_placeholder')"
                                           required>
                                    @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="district">@lang('admin.district')</label>
                                    <input type="text" name="district" id="district"
                                           class="form-control @error('district') is-invalid @enderror"
                                           value="{{ old('district', $job->district) }}"
                                           placeholder="@lang('admin.district_placeholder')">
                                    @error('district')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="pay_type">@lang('admin.pay_type') *</label>
                                    <select name="pay_type" id="pay_type"
                                            class="form-control @error('pay_type') is-invalid @enderror" required>
                                        <option value="per_job" {{ old('pay_type', $job->pay_type) == 'per_job' ? 'selected' : '' }}>@lang('admin.per_job')</option>
                                        <option value="per_hour" {{ old('pay_type', $job->pay_type) == 'per_hour' ? 'selected' : '' }}>@lang('admin.per_hour')</option>
                                    </select>
                                    @error('pay_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="price">@lang('admin.price')</label>
                                    <input type="number" name="price" id="price"
                                           class="form-control @error('price') is-invalid @enderror"
                                           value="{{ old('price', $job->price) }}"
                                           placeholder="@lang('admin.price_placeholder')"
                                           min="0" step="0.01">
                                    @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox mt-4 pt-2">
                                        <input type="checkbox" class="custom-control-input"
                                               name="price_negotiable" id="price_negotiable" value="1"
                                            {{ old('price_negotiable', $job->price_negotiable) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="price_negotiable">
                                            @lang('admin.price_negotiable')
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Управление фотографиями - ПЕРЕРАБОТАННАЯ СЕКЦИЯ -->
                        <div class="form-group">
                            <label class="form-label">@lang('admin.photos_management')</label>

                            <!-- Контейнер для управления фото -->
                            <div id="photos-management-container">
                                <!-- Область для Drag & Drop и существующих фото -->
                                <div id="dropzone-area" class="border rounded p-3 mb-3" style="min-height: 120px; background: #f8f9fa;">
                                    <div id="dropzone-placeholder" class="text-center text-muted py-4 {{ $job->photos->count() > 0 ? 'd-none' : '' }}">
                                        <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                        <p class="mb-0">@lang('admin.photos_drag_drop')</p>
                                        <small>@lang('admin.photos_help')</small>
                                    </div>

                                    <!-- Существующие фотографии -->
                                    <div id="existing-photos-container" class="d-flex flex-wrap gap-2 {{ $job->photos->count() == 0 ? 'd-none' : '' }}">
                                        @foreach($job->photos->sortBy('sort') as $index => $photo)
                                            <div class="photo-item position-relative"
                                                 data-photo-id="{{ $photo->id }}"
                                                 data-sort="{{ $photo->sort ?? $index }}">
                                                <img src="{{ $photo->path }}"
                                                     alt="Photo {{ $loop->iteration }}"
                                                     class="img-thumbnail photo-thumbnail"
                                                     style="width: 100px; height: 100px; object-fit: cover;">

                                                <!-- Иконка перетаскивания -->
                                                <div class="drag-handle">
                                                    <i class="fas fa-arrows-alt"></i>
                                                </div>

                                                <!-- Кнопка удаления -->
                                                <button type="button"
                                                        class="btn btn-sm btn-danger photo-remove-btn"
                                                        onclick="removeExistingPhoto(this, {{ $photo->id }})"
                                                        title="@lang('admin.delete_photo')">
                                                    <i class="fas fa-times"></i>
                                                </button>

                                                <!-- Главное фото -->
                                                @if(($photo->sort === 0) || $loop->first)
                                                    <span class="main-photo-badge">
                                                        <i class="fas fa-star"></i>
                                                    </span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Кнопка загрузки -->
                                <div class="mb-3">
                                    <button type="button" id="upload-photos-btn" class="btn btn-success btn-sm">
                                        <i class="fas fa-plus"></i> @lang('admin.add_new_photos')
                                    </button>
                                    <small class="form-text text-muted">@lang('admin.photos_max_size')</small>
                                </div>

                                <!-- Превью новых фотографий -->
                                <div id="new-photos-preview" class="d-flex flex-wrap gap-2 mb-3" style="display: none !important;"></div>

                                <!-- Скрытые поля для управления -->
                                <input type="file" name="photos[]" id="photos-input"
                                       class="d-none" accept="image/*" multiple>
                                <input type="hidden" name="existing_photos" id="existing-photos-input"
                                       value="{{ $job->photos->pluck('id')->implode(',') }}">
                                <input type="hidden" name="removed_photos" id="removed-photos-input" value="">
                                <input type="hidden" name="photo_order" id="photo-order-input"
                                       value="{{ $job->photos->sortBy('sort')->pluck('id')->implode(',') }}">
                            </div>

                            @error('photos.*')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @error('existing_photos')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> @lang('admin.update_job')
                        </button>
                        <a href="{{ route('admin.jobs.show', $job) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> @lang('admin.cancel')
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Боковая панель -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@lang('admin.job_info')</h3>
                </div>
                <div class="card-body">
                    <p><strong>@lang('admin.created_at'):</strong> {{ $job->created_at->format('d.m.Y H:i') }}</p>
                    <p><strong>@lang('admin.updated_at'):</strong> {{ $job->updated_at->format('d.m.Y H:i') }}</p>
                    <p><strong>@lang('admin.current_photos'):</strong> <span id="current-photos-count">{{ $job->photos->count() }}</span></p>
                    <!-- Добавьте эти поля в форму после основных полей -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="contact_name">@lang('admin.contact_name') *</label>
                                <input type="text" name="contact_name" id="contact_name"
                                       class="form-control @error('contact_name') is-invalid @enderror"
                                       value="{{ old('contact_name', $job->contact_name) }}"
                                       placeholder="@lang('admin.contact_name_placeholder')"
                                       required>
                                @error('contact_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">@lang('admin.phone') *</label>
                                <input type="text" name="phone" id="phone"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone', $job->phone) }}"
                                       placeholder="@lang('admin.phone_placeholder')"
                                       required>
                                @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .photo-item {
            position: relative;
            transition: transform 0.2s ease;
        }

        .photo-item:hover {
            transform: scale(1.05);
        }

        .photo-thumbnail {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }

        .drag-handle {
            position: absolute;
            top: 2px;
            left: 2px;
            background: rgba(0,0,0,0.7);
            color: white;
            border-radius: 3px;
            padding: 2px 4px;
            cursor: move;
            font-size: 10px;
        }

        .photo-remove-btn {
            position: absolute;
            top: 2px;
            right: 2px;
            width: 20px;
            height: 20px;
            padding: 0;
            border-radius: 50%;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .main-photo-badge {
            position: absolute;
            bottom: 5px;
            left: 5px;
            background: rgba(0,123,255,0.9);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #dropzone-area.dragover {
            border-color: #28a745;
            background-color: rgba(40, 167, 69, 0.05);
        }

        .photo-item.dragging {
            opacity: 0.5;
            transform: scale(0.9);
        }

        .new-photo-preview {
            position: relative;
        }

        .new-photo-remove {
            position: absolute;
            top: 2px;
            right: 2px;
            width: 20px;
            height: 20px;
            padding: 0;
            border-radius: 50%;
            font-size: 10px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Элементы управления
            const dropzoneArea = document.getElementById('dropzone-area');
            const dropzonePlaceholder = document.getElementById('dropzone-placeholder');
            const existingContainer = document.getElementById('existing-photos-container');
            const photosInput = document.getElementById('photos-input');
            const uploadBtn = document.getElementById('upload-photos-btn');
            const existingPhotosInput = document.getElementById('existing-photos-input');
            const removedPhotosInput = document.getElementById('removed-photos-input');
            const photoOrderInput = document.getElementById('photo-order-input');
            const newPhotosPreview = document.getElementById('new-photos-preview');
            const currentPhotosCount = document.getElementById('current-photos-count');

            // Инициализация
            initPhotoManagement();

            // Инициализация управления фото
            function initPhotoManagement() {
                // Обработчик кнопки загрузки
                if (uploadBtn) {
                    uploadBtn.addEventListener('click', () => photosInput.click());
                }

                // Обработчик выбора файлов
                if (photosInput) {
                    photosInput.addEventListener('change', handleNewPhotos);
                }

                // Drag & Drop
                initDragAndDrop();

                // Инициализация сортировки
                initPhotoSorting();

                updateUIState();
            }

            // Обработка новых фото
            function handleNewPhotos(event) {
                const files = event.target.files;
                if (files.length === 0) return;

                newPhotosPreview.innerHTML = '';
                newPhotosPreview.style.display = 'flex';

                Array.from(files).forEach(file => {
                    if (!file.type.startsWith('image/')) {
                        alert('@lang('admin.only_images_allowed')');
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const previewItem = document.createElement('div');
                        previewItem.className = 'new-photo-preview';
                        previewItem.innerHTML = `
                    <img src="${e.target.result}"
                         class="img-thumbnail"
                         style="width: 100px; height: 100px; object-fit: cover;"
                         alt="New photo">
                    <button type="button"
                            class="btn btn-sm btn-danger new-photo-remove"
                            onclick="removeNewPhotoPreview(this)">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                        newPhotosPreview.appendChild(previewItem);
                    };
                    reader.readAsDataURL(file);
                });
            }

            // Drag & Drop
            function initDragAndDrop() {
                if (!dropzoneArea) return;

                // Предотвращение стандартного поведения
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dropzoneArea.addEventListener(eventName, preventDefaults, false);
                });

                function preventDefaults(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }

                // Подсветка при drag over
                ['dragenter', 'dragover'].forEach(eventName => {
                    dropzoneArea.addEventListener(eventName, () => {
                        dropzoneArea.classList.add('dragover');
                    }, false);
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropzoneArea.addEventListener(eventName, () => {
                        dropzoneArea.classList.remove('dragover');
                    }, false);
                });

                // Обработка drop
                dropzoneArea.addEventListener('drop', handleDrop, false);

                function handleDrop(e) {
                    const dt = e.dataTransfer;
                    const files = dt.files;
                    photosInput.files = files;

                    // Триггерим событие change
                    const event = new Event('change', { bubbles: true });
                    photosInput.dispatchEvent(event);
                }
            }

            // Сортировка фото
            function initPhotoSorting() {
                const container = existingContainer;
                if (!container) return;

                let draggedItem = null;

                container.querySelectorAll('.photo-item').forEach(item => {
                    item.setAttribute('draggable', true);

                    item.addEventListener('dragstart', function(e) {
                        draggedItem = this;
                        setTimeout(() => this.classList.add('dragging'), 0);
                    });

                    item.addEventListener('dragend', function() {
                        this.classList.remove('dragging');
                        draggedItem = null;
                        updatePhotoOrder();
                    });

                    item.addEventListener('dragover', function(e) {
                        e.preventDefault();
                    });

                    item.addEventListener('drop', function(e) {
                        e.preventDefault();
                        if (draggedItem && draggedItem !== this) {
                            const allItems = Array.from(container.querySelectorAll('.photo-item'));
                            const thisIndex = allItems.indexOf(this);
                            const draggedIndex = allItems.indexOf(draggedItem);

                            if (draggedIndex < thisIndex) {
                                container.insertBefore(draggedItem, this.nextSibling);
                            } else {
                                container.insertBefore(draggedItem, this);
                            }
                            updatePhotoOrder();
                        }
                    });
                });
            }

            // Обновление порядка фото
            function updatePhotoOrder() {
                const photoItems = Array.from(document.querySelectorAll('.photo-item'));
                const order = photoItems.map(item => item.getAttribute('data-photo-id'));
                photoOrderInput.value = order.join(',');

                // Обновляем бейдж главного фото
                photoItems.forEach((item, index) => {
                    const badge = item.querySelector('.main-photo-badge');
                    if (index === 0) {
                        if (!badge) {
                            const newBadge = document.createElement('span');
                            newBadge.className = 'main-photo-badge';
                            newBadge.innerHTML = '<i class="fas fa-star"></i>';
                            item.appendChild(newBadge);
                        }
                    } else if (badge) {
                        badge.remove();
                    }
                });
            }

            // Обновление UI состояния
            function updateUIState() {
                const hasExistingPhotos = document.querySelectorAll('.photo-item').length > 0;

                if (hasExistingPhotos) {
                    dropzonePlaceholder.classList.add('d-none');
                    existingContainer.classList.remove('d-none');
                } else {
                    dropzonePlaceholder.classList.remove('d-none');
                    existingContainer.classList.add('d-none');
                }

                // Обновляем счетчик
                currentPhotosCount.textContent = document.querySelectorAll('.photo-item').length;
                updatePhotoOrder();
            }

            // Глобальные функции
            window.removeExistingPhoto = function(button, photoId) {
                if (!confirm('@lang('admin.confirm_photo_delete')')) return;

                const photoItem = button.closest('.photo-item');
                if (photoItem) {
                    photoItem.remove();
                }

                // Добавляем в список удаленных
                const removedPhotos = removedPhotosInput.value ? removedPhotosInput.value.split(',') : [];
                if (!removedPhotos.includes(photoId.toString())) {
                    removedPhotos.push(photoId.toString());
                    removedPhotosInput.value = removedPhotos.join(',');
                }

                // Удаляем из списка существующих
                const existingPhotos = existingPhotosInput.value.split(',').filter(id => id && id !== photoId.toString());
                existingPhotosInput.value = existingPhotos.join(',');

                updateUIState();
            };

            window.removeNewPhotoPreview = function(button) {
                const previewItem = button.closest('.new-photo-preview');
                if (previewItem) {
                    previewItem.remove();
                }

                // Скрываем контейнер превью если пусто
                if (newPhotosPreview.children.length === 0) {
                    newPhotosPreview.style.display = 'none';
                }
            };
        });

        // Логирование при отправке формы для отладки
        document.getElementById('job-edit-form')?.addEventListener('submit', function(e) {
            console.log('=== FORM SUBMISSION DEBUG ===');
            console.log('Existing photos input:', document.getElementById('existing-photos-input').value);
            console.log('Removed photos input:', document.getElementById('removed-photos-input').value);
            console.log('Photo order input:', document.getElementById('photo-order-input').value);
            console.log('New photos files:', document.getElementById('photos-input').files.length);
            console.log('Current photos count:', document.querySelectorAll('.photo-item').length);
        });
    </script>
@endpush
