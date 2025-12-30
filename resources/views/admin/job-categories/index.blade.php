<!-- resources/views/admin/job-categories/index.blade.php -->
@extends('layouts.admin')

@section('title', __('admin.job_categories'))

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">@lang('admin.category_list')</h3>
            <div class="card-tools">
                <a href="{{ route('admin.job-categories.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i>
                    <span class="d-none d-sm-inline"> @lang('admin.create_category')</span>
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <!-- List-group вариант для мобильных -->
            <div class="table-responsive d-block d-md-none">
                <div class="list-group">
                    @foreach($categories as $category)
                        <div class="list-group-item mb-3">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">
                                    {{ __('categories.' . $category->slug) }}
                                    @if(!__('categories.' . $category->slug, [], false))
                                        <small class="text-muted">({{ $category->name }})</small>
                                    @endif
                                </h5>
                                <small class="text-muted">ID: {{ $category->id }}</small>
                            </div>
                            <p class="mb-1">
                                <strong>@lang('admin.slug'):</strong> {{ $category->slug }}<br>
                                <strong>@lang('admin.sort_order'):</strong> {{ $category->sort }}
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted">
                                        <i class="far fa-calendar mr-1"></i>
                                        {{ $category->created_at->format('d.m.Y H:i') }}
                                    </small>
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.job-categories.show', $category) }}" class="btn btn-info" title="@lang('admin.view')">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.job-categories.edit', $category) }}" class="btn btn-primary" title="@lang('admin.edit')">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.job-categories.destroy', $category) }}" method="POST" class="d-inline">
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

            <!-- Оригинальная таблица для десктопов -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>@lang('admin.name')</th>
                        <th>@lang('admin.slug')</th>
                        <th>@lang('admin.sort_order')</th>
                        <th>@lang('admin.created_at')</th>
                        <th>@lang('admin.actions')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($categories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td>
                                {{ __('categories.' . $category->slug) }}
                                @if(!__('categories.' . $category->slug, [], false))
                                    <small class="text-muted">({{ $category->name }})</small>
                                @endif
                            </td>
                            <td>{{ $category->slug }}</td>
                            <td>{{ $category->sort }}</td>
                            <td>{{ $category->created_at->format('d.m.Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.job-categories.show', $category) }}" class="btn btn-info btn-sm" title="@lang('admin.view')">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.job-categories.edit', $category) }}" class="btn btn-primary btn-sm" title="@lang('admin.edit')">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.job-categories.destroy', $category) }}" method="POST" class="d-inline">
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
            @include('components.pagination', ['paginator' => $categories])
        </div>
    </div>
@endsection
