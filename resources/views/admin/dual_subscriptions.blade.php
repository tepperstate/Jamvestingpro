@extends('layouts.admin.app')
@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dual Investment Subscriptions</h1>
    </div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Product</th>
                            <th>Amount</th>
                            <th>Expected Return</th>
                            <th>Admin Status</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subscriptions as $sub)
                        <tr>
                            <td>{{ $sub->user->name ?? 'Unknown' }}</td>
                            <td>{{ $sub->product->name ?? 'Unknown' }}</td>
                            <td>{{ $sub->amount }}</td>
                            <td>{{ $sub->expected_return }}</td>
                            <td>{{ $sub->admin_status ?? 'Auto' }}</td>
                            <td>{{ ucfirst($sub->status) }}</td>
                            <td>
                                <!-- Actions like edit/delete -->
                                <a href="#" class="btn btn-sm btn-info">Rig Outcome</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $subscriptions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
