{{-- resources/views/admin/contact_requests/index.blade.php --}}
@extends('layouts.admin')

@section('title', __('admin.contact_requests'))

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">@lang('admin.contact_requests')</h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-right">
                        {{-- Временно закомментируем статистику --}}
                        {{--
                        <a href="{{ route('admin.contact-requests.stats') }}" class="btn btn-info mr-2">
                            <i class="fas fa-chart-bar"></i> @lang('admin.statistics')
                        </a>
                        --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <!-- Статистика -->
    <div class="row mb-4">
        <div class="col-lg-2 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total'] }}</h3>
                    <p>@lang('admin.total_requests')</p>
                </div>
                <div class="icon"><i class="fas fa-envelope"></i></div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['sent'] }}</h3>
                    <p>@lang('admin.sent')</p>
                </div>
                <div class="icon"><i class="fas fa-check"></i></div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['failed'] }}</h3>
                    <p>@lang('admin.failed')</p>
                </div>
                <div class="icon"><i class="fas fa-times"></i></div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['today'] }}</h3>
                    <p>@lang('admin.today')</p>
                </div>
                <div class="icon"><i class="fas fa-calendar-day"></i></div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $stats['week'] }}</h3>
                    <p>@lang('admin.this_week')</p>
                </div>
                <div class="icon"><i class="fas fa-chart-line"></i></div>
            </div>
        </div>
    </div>

    <!-- Фильтры -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">@lang('admin.filters')</h3>
        </div>
        <div class="card-body">
            <form method="GET" class="row">
                <div class="col-md-3">
                    <label>@lang('admin.status')</label>
                    <select name="status" class="form-control">
                        <option value="">@lang('admin.all')</option>
                        <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>@lang('admin.sent')</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>@lang('admin.failed')</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>@lang('admin.pending')</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>@lang('admin.date_from')</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label>@lang('admin.date_to')</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label>@lang('admin.search')</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="@lang('admin.search_placeholder')">
                </div>
                <div class="col-md-12 mt-3">
                    <button type="submit" class="btn btn-primary">@lang('admin.filter')</button>
                    <a href="{{ route('admin.contact-requests.index') }}" class="btn btn-secondary">@lang('admin.reset')</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Десктопная таблица (скрыта на мобильных) -->
    <div class="card d-none d-md-block">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>@lang('admin.job')</th>
                        <th>@lang('admin.sender')</th>
                        <th>@lang('admin.email')</th>
                        <th>@lang('admin.message')</th>
                        <th>@lang('admin.ip_address')</th>
                        <th>@lang('admin.status')</th>
                        <th>@lang('admin.created_at')</th>
                        <th>@lang('admin.actions')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($contactRequests as $request)
                        <tr>
                            <td>{{ $request->id }}</td>
                            <td>
                                <a href="{{ route('admin.contact-requests.show', $request) }}" title="{{ $request->job->title }}">
                                    {{ Str::limit($request->job->title, 30) }}
                                </a>
                            </td>
                            <td>{{ $request->name }}</td>
                            <td>
                                <a href="mailto:{{ $request->email }}">{{ $request->email }}</a>
                                @if($request->phone)
                                    <br><small class="text-muted">{{ $request->phone }}</small>
                                @endif
                            </td>
                            <td>{{ Str::limit($request->message, 50) }}</td>
                            <td><small class="text-muted">{{ $request->ip_address }}</small></td>
                            <td>
                                <span class="badge badge-{{ $request->status_color }}">
                                    {{ $request->status_text }}
                                </span>
                            </td>
                            <td>{{ $request->created_at->format('d.m.Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.contact-requests.show', $request) }}" class="btn btn-info btn-sm" title="@lang('admin.view_details')">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('admin.contact-requests.destroy', $request) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('@lang('admin.delete_contact_confirm')')"
                                            title="@lang('admin.delete')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Пагинация через компонент -->
            @include('components.pagination', ['paginator' => $contactRequests])
        </div>
    </div>

    <!-- Мобильные карточки (видны только на мобильных) -->
    <div class="d-md-none">
        @if($contactRequests->count() > 0)
            <div class="row">
                @foreach($contactRequests as $request)
                    <div class="col-12 mb-3">
                        <div class="card">
                            <div class="card-header bg-light py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="card-title mb-0 small">
                                        <strong>#{{ $request->id }}</strong> •
                                        <span class="badge badge-{{ $request->status_color }}">
                                            {{ $request->status_text }}
                                        </span>
                                    </h6>
                                    <small class="text-muted">{{ $request->created_at->format('d.m.Y H:i') }}</small>
                                </div>
                            </div>
                            <div class="card-body py-2">
                                <!-- Информация об отправителе -->
                                <div class="mb-2">
                                    <strong class="small">{{ $request->name }}</strong>
                                    <div>
                                        <a href="mailto:{{ $request->email }}" class="small">{{ $request->email }}</a>
                                        @if($request->phone)
                                            <div class="small text-muted">{{ $request->phone }}</div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Информация об объявлении -->
                                <div class="mb-2">
                                    <strong class="small">@lang('admin.job'):</strong>
                                    <div class="small">
                                        <a href="{{ route('admin.jobs.show', $request->job_id) }}">
                                            {{ Str::limit($request->job->title, 40) }}
                                        </a>
                                    </div>
                                </div>

                                <!-- Сообщение -->
                                <div class="mb-2">
                                    <strong class="small">@lang('admin.message'):</strong>
                                    <div class="small text-muted">{{ Str::limit($request->message, 80) }}</div>
                                </div>

                                <!-- Дополнительная информация -->
                                <div class="row small text-muted">
                                    <div class="col-6">
                                        <strong>IP:</strong> {{ $request->ip_address }}
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer py-2">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.contact-requests.show', $request) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> @lang('admin.view_details')
                                    </a>
                                    <form action="{{ route('admin.contact-requests.destroy', $request) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('@lang('admin.delete_contact_confirm')')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Пагинация для мобильных -->
            <div class="mt-3">
                @include('components.pagination', ['paginator' => $contactRequests])
            </div>
        @else
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> @lang('admin.no_contact_requests_found')
            </div>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        /* Адаптивные стили для мобильных */
        @media (max-width: 767.98px) {
            .card-header.bg-light {
                padding: 0.5rem 1rem;
            }

            .card-body.py-2 {
                padding: 0.75rem;
            }

            .card-footer.py-2 {
                padding: 0.5rem 0.75rem;
            }

            .btn-sm {
                padding: 0.25rem 0.5rem;
                font-size: 0.8rem;
            }

            .small-box .inner h3 {
                font-size: 1.5rem;
            }

            .small-box .inner p {
                font-size: 0.8rem;
            }
        }

        /* Улучшение отображения на очень маленьких экранах */
        @media (max-width: 575.98px) {
            .col-6 {
                padding-left: 0.25rem;
                padding-right: 0.25rem;
            }

            .small-box {
                margin-bottom: 0.5rem;
            }
        }
    </style>
@endpush
