{{-- resources/views/admin/photos/index.blade.php --}}
@extends('layouts.admin')

@section('title', __('admin.photo_cleanup'))

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">@lang('admin.photo_cleanup_title')</h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-right">
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
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@lang('admin.orphaned_photos_folders')</h3>

                    @if(!empty($orphanedPhotos))
                        <div class="card-tools">
                            <form action="{{ route('admin.photos.cleanup.all') }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('@lang('admin.delete_all_confirmation')')">
                                    <i class="fas fa-trash"></i> @lang('admin.delete_all')
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                <div class="card-body">
                    @if(empty($orphanedPhotos))
                        <div class="alert alert-success">
                            <i class="fas fa-check"></i> @lang('admin.no_orphaned_photos')
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            @lang('admin.folders_found', ['count' => count($orphanedPhotos)])
                        </div>

                        <!-- Десктопная таблица (скрыта на мобильных) -->
                        <div class="d-none d-md-block">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>@lang('admin.ad_id')</th>
                                        <th>@lang('admin.photos_count')</th>
                                        <th>@lang('admin.size')</th>
                                        <th>@lang('admin.created_date')</th>
                                        <th>@lang('admin.deleted_date')</th>
                                        <th>@lang('admin.days_passed')</th>
                                        <th>@lang('admin.actions')</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($orphanedPhotos as $folder => $info)
                                        <tr>
                                            <td>
                                                <strong>{{ $folder }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge badge-primary">{{ $info['photos_count'] }} @lang('admin.photos')</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-secondary">{{ $info['size'] }}</span>
                                            </td>
                                            <td>
                                                {{ date('d.m.Y H:i', $info['created_at']) }}
                                            </td>
                                            <td>
                                                {{ $info['deleted_at']->format('d.m.Y H:i') }}
                                            </td>
                                            <td>
                                                @if($info['days_since_deletion'] > 30)
                                                    <span class="badge badge-danger">{{ $info['days_since_deletion'] }} @lang('admin.days')</span>
                                                @elseif($info['days_since_deletion'] > 7)
                                                    <span class="badge badge-warning">{{ $info['days_since_deletion'] }} @lang('admin.days')</span>
                                                @else
                                                    <span class="badge badge-success">{{ $info['days_since_deletion'] }} @lang('admin.days')</span>
                                                @endif
                                            </td>
                                            <td>
                                                <form action="{{ route('admin.photos.cleanup.folder', $folder) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                            onclick="return confirm('@lang('admin.delete_folder_confirmation', ['folder' => $folder])')">
                                                        <i class="fas fa-trash"></i> @lang('admin.delete_folder')
                                                    </button>
                                                </form>

                                                <button type="button" class="btn btn-info btn-sm"
                                                        data-toggle="collapse" data-target="#photos-{{ $folder }}">
                                                    <i class="fas fa-eye"></i> @lang('admin.show_photos')
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="7" class="p-0">
                                                <div id="photos-{{ $folder }}" class="collapse">
                                                    <div class="p-3 bg-light">
                                                        <h6 class="mb-2">@lang('admin.photos_from_folder', ['folder' => $folder])</h6>
                                                        <div class="d-flex flex-wrap gap-2">
                                                            @php
                                                                $folderPath = public_path('images/jobs/' . $folder);
                                                                $photos = array_filter(scandir($folderPath), function($item) use ($folderPath) {
                                                                    return !in_array($item, ['.', '..']) && preg_match('/\.(jpg|jpeg|png|gif)$/i', $item);
                                                                });
                                                            @endphp

                                                            @foreach($photos as $photo)
                                                                <div class="text-center">
                                                                    <img src="{{ asset('images/jobs/' . $folder . '/' . $photo) }}"
                                                                         class="img-thumbnail"
                                                                         style="width: 100px; height: 100px; object-fit: cover;"
                                                                         alt="{{ $photo }}"
                                                                         loading="lazy">
                                                                    <div class="small text-muted mt-1">{{ Str::limit($photo, 15) }}</div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Мобильные карточки (видны только на мобильных) -->
                        <div class="d-md-none">
                            <div class="row">
                                @foreach($orphanedPhotos as $folder => $info)
                                    <div class="col-12 mb-3">
                                        <div class="card">
                                            <div class="card-header bg-light">
                                                <h6 class="card-title mb-0">
                                                    @lang('admin.ad_id'): <strong>{{ $folder }}</strong>
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <small class="text-muted">@lang('admin.photos_count'):</small>
                                                        <div class="badge badge-primary">{{ $info['photos_count'] }} @lang('admin.photos')</div>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted">@lang('admin.size'):</small>
                                                        <div class="badge badge-secondary">{{ $info['size'] }}</div>
                                                    </div>
                                                </div>

                                                <div class="row mt-2">
                                                    <div class="col-12">
                                                        <small class="text-muted">@lang('admin.created_date'):</small>
                                                        <div>{{ date('d.m.Y H:i', $info['created_at']) }}</div>
                                                    </div>
                                                </div>

                                                <div class="row mt-2">
                                                    <div class="col-12">
                                                        <small class="text-muted">@lang('admin.deleted_date'):</small>
                                                        <div>{{ $info['deleted_at']->format('d.m.Y H:i') }}</div>
                                                    </div>
                                                </div>

                                                <div class="row mt-2">
                                                    <div class="col-12">
                                                        <small class="text-muted">@lang('admin.days_passed'):</small>
                                                        <div>
                                                            @if($info['days_since_deletion'] > 30)
                                                                <span class="badge badge-danger">{{ $info['days_since_deletion'] }} @lang('admin.days')</span>
                                                            @elseif($info['days_since_deletion'] > 7)
                                                                <span class="badge badge-warning">{{ $info['days_since_deletion'] }} @lang('admin.days')</span>
                                                            @else
                                                                <span class="badge badge-success">{{ $info['days_since_deletion'] }} @lang('admin.days')</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row mt-3">
                                                    <div class="col-12">
                                                        <form action="{{ route('admin.photos.cleanup.folder', $folder) }}" method="POST" class="d-inline-block w-100">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm w-100 mb-2"
                                                                    onclick="return confirm('@lang('admin.delete_folder_confirmation', ['folder' => $folder])')">
                                                                <i class="fas fa-trash"></i> @lang('admin.delete_folder_btn')
                                                            </button>
                                                        </form>

                                                        <button type="button" class="btn btn-info btn-sm w-100"
                                                                data-toggle="collapse" data-target="#mobile-photos-{{ $folder }}">
                                                            <i class="fas fa-eye"></i> @lang('admin.show_photos')
                                                        </button>
                                                    </div>
                                                </div>

                                                <div id="mobile-photos-{{ $folder }}" class="collapse mt-3">
                                                    <div class="border-top pt-3">
                                                        <h6 class="mb-2">@lang('admin.photos_from_folder', ['folder' => $folder])</h6>
                                                        <div class="d-flex flex-wrap gap-2 justify-content-center">
                                                            @php
                                                                $folderPath = public_path('images/jobs/' . $folder);
                                                                $photos = array_filter(scandir($folderPath), function($item) use ($folderPath) {
                                                                    return !in_array($item, ['.', '..']) && preg_match('/\.(jpg|jpeg|png|gif)$/i', $item);
                                                                });
                                                            @endphp

                                                            @foreach($photos as $photo)
                                                                <div class="text-center">
                                                                    <img src="{{ asset('images/jobs/' . $folder . '/' . $photo) }}"
                                                                         class="img-thumbnail"
                                                                         style="width: 80px; height: 80px; object-fit: cover;"
                                                                         alt="{{ $photo }}"
                                                                         loading="lazy">
                                                                    <div class="small text-muted mt-1">{{ Str::limit($photo, 12) }}</div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Адаптивные стили */
        @media (max-width: 767.98px) {
            .card-body {
                padding: 0.75rem;
            }

            .btn-sm {
                padding: 0.25rem 0.5rem;
                font-size: 0.875rem;
            }

            .badge {
                font-size: 0.75rem;
            }
        }

        /* Улучшение отступов для мобильных */
        .mobile-card .row {
            margin-bottom: 0.5rem;
        }

        .mobile-card .col-6, .mobile-card .col-12 {
            margin-bottom: 0.5rem;
        }
    </style>
@endpush
