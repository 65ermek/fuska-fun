@extends('layouts.admin')

@section('title',  __('admin.customers'))

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('admin.customers') }}</h3>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Last seen</th>
                        <th>Created</th>
                        <th></th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($customers as $customer)
                        <tr>
                            <td>{{ $customer->id }}</td>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>
                                {{ optional($customer->last_seen_at)->diffForHumans() ?? '—' }}
                            </td>
                            <td>{{ $customer->created_at->format('d.m.Y') }}</td>
                            <td>
                                <a href="{{ route('admin.customers.show', $customer) }}"
                                   class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.customers.destroy', $customer) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('Delete customer?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{ $customers->links() }}
        </div>
    </div>
@endsection
