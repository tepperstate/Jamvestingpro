@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Bank Wire Transfer Requests</h4>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Reference</th>
                            <th>User</th>
                            <th>Amount</th>
                            <th>Bank</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $req)
                        <tr>
                            <td>{{ $req->initiated_at->format('Y-m-d') }}</td>
                            <td>{{ $req->payment_reference }}</td>
                            <td>{{ $req->user->name ?? 'User #' . $req->user_id }}</td>
                            <td>{{ $req->amount }} {{ $req->currency }}</td>
                            <td>{{ $req->bank_name }}</td>
                            <td>
                                <span class="badge bg-{{ $req->finance_status === 'approved' ? 'success' : ($req->finance_status === 'rejected' ? 'danger' : ($req->finance_status === 'pending' ? 'warning' : 'secondary')) }}">
                                    {{ strtoupper($req->finance_status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.finance.bank-wire.show', $req->id) }}" class="btn btn-sm btn-primary">Review</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">No bank wire requests found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
