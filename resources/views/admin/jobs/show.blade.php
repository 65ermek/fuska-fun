@extends('layouts.admin')

@section('title', __('admin.job_details'))

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">@lang('admin.job_details')</h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-right">
                        <a href="{{ route('admin.jobs.edit', $job) }}" class="btn btn-primary mr-2">
                            <i class="fas fa-edit"></i> @lang('admin.edit')
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
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@lang('admin.job_information')</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">@lang('admin.id')</th>
                                    <td>{{ $job->id }}</td>
                                </tr>
                                <tr>
                                    <th>@lang('admin.title')</th>
                                    <td>{{ $job->title }}</td>
                                </tr>
                                <tr>
                                    <th>@lang('admin.category')</th>
                                    <td>
                                        <span class="badge badge-secondary">
                                            {{ __('categories.' . ($job->category->slug ?? 'unknown')) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>@lang('admin.city')</th>
                                    <td>{{ $job->city }}</td>
                                </tr>
                                <tr>
                                    <th>@lang('admin.district')</th>
                                    <td>{{ $job->district ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">@lang('admin.status')</th>
                                    <td>
                                        <span class="badge badge-{{ $job->status == 'active' ? 'success' : ($job->status == 'pending' ? 'warning' : 'danger') }}">
                                            {{ $job->status }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>@lang('admin.pay_type')</th>
                                    <td>
                                        @if($job->pay_type == 'per_hour')
                                            @lang('admin.per_hour')
                                        @else
                                            @lang('admin.per_job')
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>@lang('admin.price')</th>
                                    <td>
                                        @if($job->price)
                                            {{ $job->price }}
                                            @if($job->pay_type == 'per_hour')
                                                @lang('admin.per_hour')
                                            @else
                                                @lang('admin.per_job')
                                            @endif
                                            @if($job->price_negotiable)
                                                <br><small class="text-muted">(@lang('admin.negotiable'))</small>
                                            @endif
                                        @else
                                            <span class="text-muted">@lang('admin.not_specified')</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>@lang('admin.created_at')</th>
                                    <td>{{ $job->created_at->format('d.m.Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>@lang('admin.updated_at')</th>
                                    <td>{{ $job->updated_at->format('d.m.Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Описание -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>@lang('admin.description')</h5>
                            <div class="p-3 bg-light rounded">
                                {!! nl2br(e($job->description)) !!}
                            </div>
                        </div>
                    </div>

                    <!-- Фотографии -->
                    @if($job->photos && $job->photos->count() > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>@lang('admin.photos') ({{ $job->photos->count() }})</h5>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($job->photos as $photo)
                                        <a href="{{ asset($photo->path) }}" target="_blank" class="d-block">
                                            <img src="{{ asset($photo->path) }}"
                                                 alt="Photo {{ $loop->iteration }}"
                                                 class="img-thumbnail"
                                                 style="width: 150px; height: 150px; object-fit: cover;"
                                                 loading="lazy">
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Контактная информация -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">@lang('admin.contact_information')</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">@lang('admin.contact_name')</th>
                                    <td>{{ $job->contact_name }}</td>
                                </tr>
                                <tr>
                                    <th>@lang('admin.phone')</th>
                                    <td>
                                        <a href="tel:{{ $job->phone }}" class="text-decoration-none">
                                            {{ $job->phone }}
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">@lang('admin.email')</th>
                                    <td>
                                        @if($job->email)
                                            <a href="mailto:{{ $job->email }}" class="text-decoration-none">
                                                {{ $job->email }}
                                            </a>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>@lang('admin.telegram')</th>
                                    <td>
                                        @if($job->telegram)
                                            <a href="https://t.me/{{ $job->telegram }}" target="_blank" class="text-decoration-none">
                                                {{ $job->telegram }}
                                            </a>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>@lang('admin.whatsapp')</th>
                                    <td>
                                        @if($job->whatsapp)
                                            <a href="https://wa.me/{{ $job->whatsapp }}" target="_blank" class="text-decoration-none">
                                                {{ $job->whatsapp }}
                                            </a>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Боковая панель -->
        <div class="col-md-4">
            <!-- Статус -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@lang('admin.quick_actions')</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.jobs.update-status', $job) }}" method="POST" class="mb-3">
                        @csrf
                        @method('PATCH')
                        <div class="form-group">
                            <label for="status">@lang('admin.change_status')</label>
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
                    </form>

                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.jobs.edit', $job) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-edit mr-1"></i> @lang('admin.edit_job')
                        </a>
                        <form action="{{ route('admin.jobs.destroy', $job) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100 text-left"
                                    onclick="return confirm('@lang('admin.confirm_delete')')">
                                <i class="fas fa-trash mr-1"></i> @lang('admin.delete_job')
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Дополнительная информация -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">@lang('admin.additional_info')</h3>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('admin.language')
                            <span class="badge badge-info">{{ strtoupper($job->lang) }}</span>
                        </li>

                        <li class="list-group-item">
                            <small class="text-muted">@lang('admin.ip_address')</small>
                            <div>{{ $job->ip }}</div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
