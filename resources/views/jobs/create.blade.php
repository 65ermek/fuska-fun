@extends('layouts.bazos')
@section('title', __('messages.create_ad.title'))

@section('content')
    <h1 class="h5 mb-3">{{ __('messages.create_ad.page_title') }}</h1>

    <form method="POST" action="{{ route('jobs.store') }}" enctype="multipart/form-data" class="card p-3">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small">{{ __('messages.create_ad.labels.category') }}</label>
                <select name="job_category_id" class="form-select @error('job_category_id') is-invalid @enderror" required>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ old('job_category_id') == $c->id ? 'selected' : '' }}>
                            {{ __('categories.' . $c->slug) }}
                        </option>
                    @endforeach
                </select>
                @error('job_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label small">{{ __('messages.create_ad.labels.city') }}</label>
                <input name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city') }}" required>
                @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label small">{{ __('messages.create_ad.labels.district') }}</label>
                <input name="district" class="form-control @error('district') is-invalid @enderror" value="{{ old('district') }}">
                @error('district') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label class="form-label small">{{ __('messages.create_ad.labels.title') }}</label>
                <input name="title" class="form-control @error('title') is-invalid @enderror" maxlength="120" value="{{ old('title') }}" required>
                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label class="form-label small">{{ __('messages.create_ad.labels.description') }}</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="6" required>{{ old('description') }}</textarea>
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label small">{{ __('messages.create_ad.labels.pay_type') }}</label>
                <select name="pay_type" class="form-select @error('pay_type') is-invalid @enderror">
                    <option value="per_job" {{ old('pay_type') === 'per_job' ? 'selected' : '' }}>
                        {{ __('messages.create_ad.pay_types.per_job') }}
                    </option>
                    <option value="per_hour" {{ old('pay_type') === 'per_hour' ? 'selected' : '' }}>
                        {{ __('messages.create_ad.pay_types.per_hour') }}
                    </option>
                </select>
                @error('pay_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label small">{{ __('messages.create_ad.labels.price') }}</label>
                <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" min="0" value="{{ old('price') }}">
                @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4 d-flex align-items-end">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="price_negotiable" value="1" id="neg" {{ old('price_negotiable') ? 'checked' : '' }}>
                    <label class="form-check-label" for="neg">
                        {{ __('messages.create_ad.labels.price_negotiable') }}
                    </label>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label small d-block">
                {{ __('messages.create_ad.labels.photos') }}
                <small class="text-muted">{{ __('messages.create_ad.labels.photos_note') }}</small>
            </label>

            <span id="dropzoneods">
                <button type="button" id="uploadbutton" class="btn btn-success btn-sm px-3 d-none d-md-inline">
                    {{ __('messages.create_ad.labels.add_photos_button') }}
                </button>

                <div id="dropzonea" class="dropzone border rounded p-2" style="min-height: 120px;">
                    <div id="dropzone-placeholder" class="dz-default dz-message text-muted">
                        <span>{{ __('messages.create_ad.labels.photos_placeholder') }}</span>
                    </div>
                    <!-- Сюда будут добавляться превью фотографий -->
                </div>
            </span>

            <input type="file" id="photoInput" name="photos[]" accept="image/*" multiple hidden>
            <input type="hidden" name="photo_order" id="photo_order">

            @error('photos.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        <hr>
        <h5 class="mt-3 mb-2">{{ __('messages.create_ad.labels.personal_info') }}</h5>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">{{ __('messages.create_ad.labels.contact_name') }}</label>
                <input type="text" name="contact_name" class="form-control @error('contact_name') is-invalid @enderror" value="{{ old('contact_name') }}">
                @error('contact_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">{{ __('messages.create_ad.labels.phone') }}</label>
                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">
                    {{ __('messages.create_ad.labels.email') }}
                    <small class="text-muted">{{ __('messages.create_ad.labels.email_note') }}</small>
                </label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">{{ __('messages.create_ad.labels.password') }}</label>
                <input type="password" name="plain_password" class="form-control @error('plain_password') is-invalid @enderror"
                       placeholder="{{ __('messages.create_ad.labels.password_placeholder') }}">
                @error('plain_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="mt-3 d-flex gap-2">
            <button class="btn btn-success btn-sm px-3" onclick="this.disabled=true; this.form.submit();">
                {{ __('messages.create_ad.buttons.submit') }}
            </button>
            <a href="{{ route('jobs.index') }}" class="btn btn-outline-secondary">
                {{ __('messages.create_ad.buttons.back') }}
            </a>
        </div>
    </form>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dropzone = document.getElementById('dropzonea');
            const button = document.getElementById('uploadbutton');
            const fileInput = document.getElementById('photoInput');
            const maxFiles = 20;
            let selectedFiles = [];

            // 📌 ДОБАВЛЕНО — мобильный клик по зоне вызывает выбор фото / камеру
            dropzone.addEventListener('click', () => fileInput.click());

            // Обработчик кнопки загрузки
            button.addEventListener('click', () => fileInput.click());

            // Обработчик выбора файлов через input
            fileInput.addEventListener('change', (e) => {
                handleFiles(e.target.files);
                fileInput.value = '';
            });

            // Drag and drop функциональность
            dropzone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropzone.classList.add('bg-light');
            });

            dropzone.addEventListener('dragleave', () => dropzone.classList.remove('bg-light'));

            dropzone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropzone.classList.remove('bg-light');
                handleFiles(e.dataTransfer.files);
            });

            function handleFiles(files) {
                for (let file of files) {
                    if (!file.type.startsWith('image/')) {
                        alert('{{ __("messages.create_ad.javascript.invalid_file_type") }}');
                        continue;
                    }

                    if (file.size > 15 * 1024 * 1024) { // 5MB limit
                        alert('{{ __("messages.create_ad.javascript.file_too_big") }} (max 15 MB)');
                        continue;
                    }

                    if (selectedFiles.length >= maxFiles) {
                        alert('{{ __("messages.create_ad.javascript.too_many_files") }}');
                        break;
                    }

                    const index = selectedFiles.push(file) - 1;

                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const wrapper = document.createElement('div');
                        wrapper.className = 'img-wrapper position-relative d-inline-block m-1';
                        wrapper.dataset.index = index;
                        wrapper.innerHTML = `
                    <img src="${e.target.result}" class="rounded border" style="width:100px; height:100px; object-fit:cover;">
                    <div class="drag-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" fill="white" viewBox="0 0 28 28">
                            <path xmlns="http://www.w3.org/2000/svg" style="fill:white;" d="..."/>
                        </svg>
                    </div>
                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" data-index="${index}" style="padding: 0.1rem 0.4rem;">×</button>
                `;

                        dropzone.appendChild(wrapper);

                        wrapper.querySelector('button').addEventListener('click', function () {
                            const idx = parseInt(this.dataset.index);
                            selectedFiles.splice(idx, 1);
                            wrapper.remove();
                            rebuildHiddenInput();
                            updatePlaceholderVisibility();
                            updatePhotoOrder();
                        });

                        rebuildHiddenInput();
                        updatePlaceholderVisibility();
                        updatePhotoOrder();
                    };
                    reader.readAsDataURL(file);
                }
            }

            function updatePlaceholderVisibility() {
                const placeholder = document.getElementById('dropzone-placeholder');
                const hasImages = dropzone.querySelectorAll('.img-wrapper').length > 0;
                if (placeholder) placeholder.style.display = hasImages ? 'none' : 'block';
            }

            function rebuildHiddenInput() {
                const old = document.getElementById('dynamic-photos');
                if (old) old.remove();

                const dt = new DataTransfer();
                const wrappers = dropzone.querySelectorAll('.img-wrapper');

                wrappers.forEach(wrapper => {
                    const index = wrapper.dataset.index;
                    const file = selectedFiles[index];
                    if (file) dt.items.add(file);
                });

                const newInput = document.createElement('input');
                newInput.type = 'file';
                newInput.name = 'photos[]';
                newInput.id = 'dynamic-photos';
                newInput.multiple = true;
                newInput.hidden = true;
                newInput.files = dt.files;

                fileInput.parentNode.appendChild(newInput);
            }

            new Sortable(dropzone, {
                animation: 150,
                ghostClass: 'sortable-ghost',
                onEnd: () => {
                    rebuildHiddenInput();
                    updatePhotoOrder();
                }
            });

            function updatePhotoOrder() {
                const order = [];
                const wrappers = dropzone.querySelectorAll('.img-wrapper');
                wrappers.forEach(wrapper => {
                    if (wrapper.dataset.index !== undefined) {
                        order.push(wrapper.dataset.index);
                    }
                });
                document.getElementById('photo_order').value = order.join(',');
            }

            rebuildHiddenInput();
            updatePlaceholderVisibility();
            updatePhotoOrder();
        });
    </script>

@endpush

@push('styles')
    <style>
        .dropzone {
            border: 2px dashed #dee2e6;
            border-radius: 0.375rem;
            transition: background-color 0.2s;
        }

        .dropzone.bg-light {
            background-color: #f8f9fa !important;
        }

        .img-wrapper {
            position: relative;
            display: inline-block;
            margin: 0.25rem;
        }

        .drag-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0;
            transition: opacity 0.2s;
            pointer-events: none;
        }

        .img-wrapper:hover .drag-icon {
            opacity: 0.7;
        }

        .sortable-ghost {
            opacity: 0.4;
        }

        .img-wrapper.sortable-ghost .drag-icon {
            opacity: 1;
        }
    </style>
@endpush
