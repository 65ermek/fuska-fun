<!-- resources/views/admin/job-categories/show.blade.php -->
@extends('layouts.admin')

@section('title', __('admin.category_details'))

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">@lang('admin.category_details')</h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-right">
                        <a href="{{ route('admin.job-categories.edit', $category) }}" class="btn btn-primary mr-2">
                            <i class="fas fa-edit"></i> @lang('admin.edit')
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
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@lang('admin.category_information')</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">@lang('admin.id')</th>
                                    <td>{{ $category->id }}</td>
                                </tr>
                                <tr>
                                    <th>@lang('admin.name')</th>
                                    <td>
                                        {{ $category->name }}
                                        @if($category->translated_name)
                                            <br>
                                            <small class="text-muted">
                                                (@lang('categories.' . $category->slug))
                                            </small>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>@lang('admin.slug')</th>
                                    <td>
                                        <code>{{ $category->slug }}</code>
                                    </td>
                                </tr>
                                <tr>
                                    <th>@lang('admin.sort_order')</th>
                                    <td>{{ $category->sort }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">@lang('admin.status')</th>
                                    <td>
                                        @if($category->is_active)
                                            <span class="badge badge-success">@lang('admin.active')</span>
                                        @else
                                            <span class="badge badge-danger">@lang('admin.inactive')</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>@lang('admin.created_at')</th>
                                    <td>{{ $category->created_at->format('d.m.Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>@lang('admin.updated_at')</th>
                                    <td>{{ $category->updated_at->format('d.m.Y H:i') }}</td>
                                </tr>
                                @if($category->deleted_at)
                                    <tr>
                                        <th>@lang('admin.deleted_at')</th>
                                        <td>
                                            <span class="text-danger">{{ $category->deleted_at->format('d.m.Y H:i') }}</span>
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <!-- Изображение категории -->
                    @if($category->image)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>@lang('admin.category_image')</h5>
                                <div class="mt-2">
                                    @if(Storage::disk('public')->exists($category->image))
                                        <img src="{{ Storage::disk('public')->url($category->image) }}"
                                             alt="{{ $category->name }}"
                                             class="img-thumbnail"
                                             style="max-height: 300px;">
                                    @else
                                        <img src="{{ asset('images/' . $category->image) }}"
                                             alt="{{ $category->name }}"
                                             class="img-thumbnail"
                                             style="max-height: 300px;">
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Описание -->
                    @if($category->description)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>@lang('admin.description')</h5>
                                <div class="p-3 bg-light rounded">
                                    {{ $category->description }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <div>
                            @if($category->deleted_at)
                                <form action="{{ route('admin.job-categories.restore', $category) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-warning btn-sm">
                                        <i class="fas fa-undo"></i> @lang('admin.restore')
                                    </button>
                                </form>
                            @endif
                        </div>
                        <div>
                            @if(!$category->deleted_at)
                                <form action="{{ route('admin.job-categories.destroy', $category) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('@lang('admin.confirm_delete')')">
                                        <i class="fas fa-trash"></i> @lang('admin.delete')
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Боковая панель с статистикой -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@lang('admin.statistics')</h3>
                </div>
                <div class="card-body">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $category->jobs_count ?? 0 }}</h3>
                            <p>@lang('admin.jobs_in_category')</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            @lang('admin.view_jobs') <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Быстрые действия -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@lang('admin.quick_actions')</h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.job-categories.edit', $category) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-edit mr-1"></i> @lang('admin.edit_category')
                        </a>
                        <a href="{{ route('admin.job-categories.create') }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-plus mr-1"></i> @lang('admin.create_new_category')
                        </a>
                        <a href="{{ route('admin.job-categories.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-list mr-1"></i> @lang('admin.all_categories')
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
