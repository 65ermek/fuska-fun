@extends('layouts.bazos')
@section('title', __('messages.edit_ad.title'))

@section('content')
    <h1 class="h5 mb-3">{{ __('messages.edit_ad.page_title') }}</h1>

    <form method="POST" action="{{ route('jobs.update', $job->slug) }}" enctype="multipart/form-data" class="card p-3">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small">{{ __('messages.edit_ad.labels.category') }}</label>
                <select name="job_category_id" class="form-select @error('job_category_id') is-invalid @enderror" required>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ (old('job_category_id', $job->job_category_id) == $c->id) ? 'selected' : '' }}>
                            {{ __('categories.' . $c->slug) }}
                        </option>
                    @endforeach
                </select>
                @error('job_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label small">{{ __('messages.edit_ad.labels.city') }}</label>
                <input name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $job->city) }}" required>
                @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label small">{{ __('messages.edit_ad.labels.district') }}</label>
                <input name="district" class="form-control @error('district') is-invalid @enderror" value="{{ old('district', $job->district) }}">
                @error('district') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label class="form-label small">{{ __('messages.edit_ad.labels.title') }}</label>
                <input name="title" class="form-control @error('title') is-invalid @enderror" maxlength="120" value="{{ old('title', $job->title) }}" required>
                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label class="form-label small">{{ __('messages.edit_ad.labels.description') }}</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="6" required>{{ old('description', $job->description) }}</textarea>
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label small">{{ __('messages.edit_ad.labels.pay_type') }}</label>
                <select name="pay_type" class="form-select @error('pay_type') is-invalid @enderror">
                    <option value="per_job" {{ old('pay_type', $job->pay_type) === 'per_job' ? 'selected' : '' }}>
                        {{ __('messages.edit_ad.pay_types.per_job') }}
                    </option>
                    <option value="per_hour" {{ old('pay_type', $job->pay_type) === 'per_hour' ? 'selected' : '' }}>
                        {{ __('messages.edit_ad.pay_types.per_hour') }}
                    </option>
                </select>
                @error('pay_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label small">{{ __('messages.edit_ad.labels.price') }}</label>
                <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" min="0" value="{{ old('price', $job->price) }}">
                @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4 d-flex align-items-end">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="price_negotiable" value="1" id="neg" {{ old('price_negotiable', $job->price_negotiable) ? 'checked' : '' }}>
                    <label class="form-check-label" for="neg">
                        {{ __('messages.edit_ad.labels.price_negotiable') }}
                    </label>
                </div>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label small d-block">
                {{ __('messages.edit_ad.labels.photos') }}
                <small>{{ __('messages.edit_ad.labels.photos_note') }}</small>
            </label>
            <span id="dropzoneods">
                <button type="button" id="uploadbutton" class="btn btn-success btn-sm px-3 d-none d-md-inline">
                    {{ __('messages.edit_ad.labels.add_photos_button') }}
                </button>
                <div id="dropzonea" class="dropzone border rounded p-2" style="min-height: 120px;">
                    <div id="dropzone-placeholder" class="dz-default dz-message text-muted">
                        <span>{{ __('messages.edit_ad.labels.photos_placeholder') }}</span>
                    </div>

                    {{-- Уже загруженные фото --}}
                    @foreach($job->photos->sortBy('sort') as $photo)
                        <div class="img-wrapper position-relative d-inline-block m-1 existing-photo" data-photo-id="{{ $photo->id }}">
                            <img src="{{ $photo->path }}" class="rounded border" style="width:100px; height:100px; object-fit:cover;">
                            <div class="drag-icon">
                                <!-- Иконка движения -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" fill="white" viewBox="0 0 28 28">
                                    <path style="fill:white;" d="M28.19,13.588l-3.806-3.806c..."></path>
                                </svg>
                            </div>
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0"
                                    style="padding: 0.1rem 0.4rem;"
                                    onclick="removeExistingPhoto(this, {{ $photo->id }})">×</button>
                        </div>
                    @endforeach
                </div>
            </span>
            <input type="file" id="photoInput" name="photos[]" accept="image/*" multiple hidden>
            <input type="hidden" name="photo_order" id="photo_order"> {{-- для сортировки --}}

            @error('photos.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        <hr>
        <h5 class="mt-3 mb-2">{{ __('messages.edit_ad.labels.personal_info') }}</h5>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">{{ __('messages.edit_ad.labels.contact_name') }}</label>
                <input type="text" name="contact_name" class="form-control @error('contact_name') is-invalid @enderror" value="{{ old('contact_name', $job->contact_name) }}">
                @error('contact_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">{{ __('messages.edit_ad.labels.phone') }}</label>
                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $job->phone) }}">
                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">
                    {{ __('messages.edit_ad.labels.email') }}
                    <small class="text-muted">{{ __('messages.edit_ad.labels.email_note') }}</small>
                </label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $job->email) }}">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="mt-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm px-3">
                {{ __('messages.edit_ad.buttons.save_changes') }}
            </button>
            <a href="{{ route('jobs.index') }}" class="btn btn-outline-secondary">
                {{ __('messages.edit_ad.buttons.back') }}
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

            button.addEventListener('click', () => fileInput.click());

            fileInput.addEventListener('change', (e) => {
                handleFiles(e.target.files);
                fileInput.value = '';
            });

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
                    if (!file.type.startsWith('image/')) continue;
                    if (selectedFiles.length >= maxFiles) break;

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
                                <path xmlns="http://www.w3.org/2000/svg" style="fill:white;" d="M28.19,13.588l-3.806-3.806c-0.494-0.492-1.271-0.519-1.733-0.054    c-0.462,0.462-0.439,1.237,0.057,1.732l1.821,1.821h-9.047V4.118l1.82,1.82c0.495,0.493,1.271,0.521,1.732,0.055    c0.464-0.464,0.442-1.238-0.055-1.733l-3.805-3.807c-0.495-0.493-1.271-0.517-1.733-0.054c-0.013,0.012-0.021,0.024-0.031,0.038    c-0.017,0.013-0.036,0.025-0.054,0.044l-3.75,3.754C9.118,4.724,9.097,5.493,9.562,5.957c0.463,0.461,1.233,0.443,1.723-0.044    l1.825-1.828v9.196H4.017l1.83-1.827c0.488-0.489,0.505-1.26,0.041-1.721C5.426,9.268,4.656,9.289,4.169,9.776l-3.756,3.752    c-0.017,0.02-0.028,0.037-0.043,0.053c-0.012,0.012-0.026,0.021-0.037,0.03c-0.465,0.467-0.44,1.241,0.057,1.734l3.804,3.807    c0.494,0.495,1.271,0.52,1.733,0.056s0.438-1.24-0.056-1.733l-1.82-1.82h9.059v8.803l-1.817-1.82    c-0.495-0.494-1.271-0.519-1.734-0.054c-0.463,0.463-0.439,1.237,0.056,1.73l3.805,3.807c0.495,0.496,1.271,0.52,1.734,0.057    c0.013-0.013,0.021-0.024,0.029-0.04c0.018-0.013,0.036-0.026,0.056-0.042l3.751-3.756c0.489-0.484,0.51-1.256,0.045-1.721    c-0.465-0.46-1.234-0.442-1.722,0.042l-1.829,1.829l0.001-8.835h9.078l-1.83,1.83c-0.488,0.485-0.506,1.255-0.043,1.722    c0.462,0.462,1.232,0.443,1.721-0.046l3.754-3.754c0.017-0.016,0.029-0.036,0.045-0.053c0.013-0.012,0.027-0.019,0.039-0.03    C28.708,14.859,28.684,14.084,28.19,13.588z"/>
                            </svg>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" data-index="${index}" style="padding: 0.1rem 0.4rem;">×</button>
                    </div>`;


                        dropzone.appendChild(wrapper);

                        wrapper.querySelector('button').addEventListener('click', function () {
                            const idx = parseInt(this.dataset.index);
                            selectedFiles.splice(idx, 1);
                            wrapper.remove();
                            rebuildHiddenInput();
                            updatePlaceholderVisibility();
                        });

                        rebuildHiddenInput();
                        updatePlaceholderVisibility();
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
                const wrappers = dropzone.querySelectorAll('.img-wrapper:not(.existing-photo)');
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

            // Drag-and-drop сортировка
            new Sortable(dropzone, {
                animation: 150,
                ghostClass: 'sortable-ghost',
                onEnd: () => {
                    rebuildHiddenInput();
                    updatePhotoOrder();
                }
            });

            // Удаление уже загруженных фото
            window.removeExistingPhoto = function (button, photoId) {
                if (!confirm('{{ __("messages.edit_ad.javascript.delete_confirm") }}')) return;

                fetch(`/photos/${photoId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(res => {
                    if (res.ok) {
                        button.closest('.img-wrapper').remove();
                        updatePhotoOrder();
                    } else {
                        alert('{{ __("messages.edit_ad.javascript.delete_error") }}');
                    }
                });
            };

            function updatePhotoOrder() {
                const order = [];
                const wrappers = dropzone.querySelectorAll('.img-wrapper');
                wrappers.forEach(wrapper => {
                    if (wrapper.dataset.photoId) {
                        order.push(wrapper.dataset.photoId);
                    }
                });
                document.getElementById('photo_order').value = order.join(',');
            }

            // начальная инициализация
            rebuildHiddenInput();
            updatePlaceholderVisibility();
            updatePhotoOrder();
        });
    </script>
@endpush
