@extends('layouts.admin.app')
@section('content')

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-5">
        <div>
            <h1 class="h2 mb-1 text-white font-weight-bold">Edit Deposit #{{ $deposit->id }}</h1>
            <p class="text-muted mb-0">Modify deposit details. Any changes to status or amount will automatically recalculate the user's balance.</p>
        </div>
        <a href="{{ route('admin.deposit') }}" class="btn btn-outline-light glass-panel px-4 py-2 satin-border font-weight-bold">
            <i class="ri-arrow-go-back-line mr-2"></i> Back to Deposits
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success glass-panel border-success text-success mb-4">
        {{ session('success') }}
    </div>
    @endif

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="glass-panel p-4 satin-border" style="border-radius: 16px;">
                <form action="{{ route('admin.history.deposit.update', $deposit->id) }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 form-group mb-4">
                            <label class="text-muted small text-uppercase font-weight-bold tracking-wider mb-2">Deposit Amount ($)</label>
                            <input type="number" step="any" name="amount" class="form-control glass-panel border-0 text-white px-3" style="height: 50px; border-radius:12px;" value="{{ $deposit->amount }}" required>
                        </div>
                        <div class="col-md-6 form-group mb-4">
                            <label class="text-muted small text-uppercase font-weight-bold tracking-wider mb-2">Deposit Status</label>
                            <select name="status" class="form-control glass-panel border-0 text-white px-3" style="height: 50px; border-radius:12px;" required>
                                <option value="pending" {{ $deposit->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="success" {{ $deposit->status == 'success' ? 'selected' : '' }}>Success</option>
                                <option value="failed" {{ $deposit->status == 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                        </div>
                        <div class="col-md-12 form-group mb-4">
                            <label class="text-muted small text-uppercase font-weight-bold tracking-wider mb-2">Submission Date</label>
                            <input type="datetime-local" name="created_at" class="form-control glass-panel border-0 text-white px-3" style="height: 50px; border-radius:12px;" value="{{ \Carbon\Carbon::parse($deposit->created_at)->format('Y-m-d\TH:i') }}" required>
                        </div>
                    </div>

                    <div class="text-right mt-4">
                        <button type="submit" class="btn btn-primary px-5 py-3 font-weight-bold" style="border-radius: 12px; font-size: 1.1rem; box-shadow: 0 0 20px rgba(14, 165, 233, 0.3);">
                            <i class="ri-save-line mr-2"></i> Update Deposit & Recalculate Balance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
