@extends('layouts.admin')

@section('title', __('admin.job_management'))
@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">@lang('admin.job_list')</h3>
            <div class="card-tools">
                <!-- Десктопная версия -->
                <form action="{{ route('admin.jobs.index') }}" method="GET" class="d-none d-md-flex align-items-center gap-2">
                    <select name="category_desktop" class="form-control form-control-sm" style="width: 150px;" onchange="this.form.submit()">
                        <option value="" {{ !request()->filled('category_desktop') ? 'selected' : '' }}>@lang('admin.all_categories')</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->slug }}" {{ request('category_desktop') == $category->slug ? 'selected' : '' }}>
                                {{ __('categories.' . $category->slug) }}
                            </option>
                        @endforeach
                    </select>

                    <input type="text" name="search" class="form-control form-control-sm" style="width: 180px;"
                           placeholder="@lang('admin.search')" value="{{ request('search') }}">

                    <button class="btn btn-primary btn-sm" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>

                <!-- Мобильная версия -->
                <form action="{{ route('admin.jobs.index') }}" method="GET" class="d-flex flex-column gap-2 d-md-none">
                    <select name="category_mobile" class="form-control form-control-sm" onchange="this.form.submit()">
                        <option value="" {{ !request()->filled('category_mobile') ? 'selected' : '' }}>@lang('admin.all_categories')</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->slug }}" {{ request('category_mobile') == $category->slug ? 'selected' : '' }}>
                                {{ __('categories.' . $category->slug) }}
                            </option>
                        @endforeach
                    </select>

                    <div class="d-flex gap-2">
                        <input type="text" name="search" class="form-control form-control-sm"
                               placeholder="@lang('admin.search')" value="{{ request('search') }}">

                        <button class="btn btn-primary btn-sm" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <!-- Мобильная версия -->
            <div class="table-responsive d-block d-md-none">
                <div class="list-group">
                    @foreach($jobs as $job)
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ Str::limit($job->title, 50) }}</h6>
                                <small class="text-muted">ID: {{ $job->id }}</small>
                            </div>
                            <p class="mb-1">
                                <strong>@lang('admin.category'):</strong> {{ $job->category->name ?? 'N/A' }}<br>
                                <strong>@lang('admin.city'):</strong> {{ $job->city }}<br>
                                <strong>@lang('admin.price'):</strong>
                                @if($job->price)
                                    {{ $job->price }} {{ $job->pay_type == 'per_hour' ? __('admin.per_hour') : __('admin.per_job') }}
                                    @if($job->price_negotiable) (@lang('admin.negotiable')) @endif
                                @else
                                    @lang('admin.not_specified')
                                @endif
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge badge-{{ $job->status == 'active' ? 'success' : ($job->status == 'pending' ? 'warning' : 'danger') }}">
                                        {{ $statuses[$job->status] ?? $job->status }}
                                    </span>
                                    <small class="text-muted ml-2">
                                        <i class="far fa-calendar"></i> {{ $job->created_at->format('d.m.Y') }}
                                    </small>
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.jobs.show', $job) }}" class="btn btn-info" title="@lang('admin.view')">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.jobs.edit', $job) }}" class="btn btn-primary" title="@lang('admin.edit')">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.jobs.destroy', $job) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger"
                                                onclick="return confirm('@lang('admin.confirm_delete')')"
                                                title="@lang('admin.delete')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Десктопная версия -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th width="50">ID</th>
                        <th>@lang('admin.title')</th>
                        <th>@lang('admin.category')</th>
                        <th>@lang('admin.city')</th>
                        <th>@lang('admin.price')</th>
                        <th>@lang('admin.status')</th>
                        <th>@lang('admin.created_at')</th>
                        <th width="150">@lang('admin.actions')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($jobs as $job)
                        <tr>
                            <td>{{ $job->id }}</td>
                            <td>
                                <a href="{{ route('admin.jobs.show', $job) }}" title="{{ $job->title }}">
                                    {{ Str::limit($job->title, 50) }}
                                </a>
                            </td>
                            <td>
                                    <span class="badge badge-secondary">
                                        {{ $job->category->name ?? 'N/A' }}
                                    </span>
                            </td>
                            <td>{{ $job->city }}</td>
                            <td>
                                @if($job->price)
                                    {{ $job->price }} {{ $job->pay_type == 'per_hour' ? __('admin.per_hour') : __('admin.per_job') }}
                                    @if($job->price_negotiable)
                                        <br><small class="text-muted">(@lang('admin.negotiable'))</small>
                                    @endif
                                @else
                                    <span class="text-muted">@lang('admin.not_specified')</span>
                                @endif
                            </td>
                            <td>
                                    <span class="badge badge-{{ $job->status == 'active' ? 'success' : ($job->status == 'pending' ? 'warning' : 'danger') }}">
                                        {{ $statuses[$job->status] ?? $job->status }}
                                    </span>
                            </td>
                            <td>{{ $job->created_at->format('d.m.Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.jobs.show', $job) }}" class="btn btn-info btn-sm" title="@lang('admin.view')">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.jobs.edit', $job) }}" class="btn btn-primary btn-sm" title="@lang('admin.edit')">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.jobs.destroy', $job) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('@lang('admin.confirm_delete')')"
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
            {{-- Пагинация --}}
            @include('components.pagination', ['paginator' => $jobs])
        </div>
    </div>
@endsection
