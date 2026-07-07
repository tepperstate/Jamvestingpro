@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Review Bank Wire Transfer #{{ $wireRequest->payment_reference }}</h4>
        <a href="{{ route('admin.finance.bank-wire.index') }}" class="btn btn-outline-secondary">Back to List</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark text-white">Transfer Details</div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Amount:</strong> <span class="fs-5 text-primary">{{ $wireRequest->amount }} {{ $wireRequest->currency }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Date Initiated:</strong> {{ $wireRequest->initiated_at->format('Y-m-d H:i') }}
                        </div>
                    </div>
                    
                    <hr>
                    <h6 class="text-muted text-uppercase mb-3">Bank Information</h6>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Bank Name:</strong> {{ $wireRequest->bank_name }}<br>
                            <strong>Account Holder:</strong> {{ $wireRequest->account_holder_name }}<br>
                            <strong>Account Number:</strong> {{ $wireRequest->account_number }}
                        </div>
                        <div class="col-md-6">
                            @if($wireRequest->routing_number)
                                <strong>Routing Number:</strong> {{ $wireRequest->routing_number }}<br>
                            @endif
                            @if($wireRequest->swift_bic)
                                <strong>SWIFT/BIC:</strong> {{ $wireRequest->swift_bic }}<br>
                            @endif
                            @if($wireRequest->iban)
                                <strong>IBAN:</strong> {{ $wireRequest->iban }}<br>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Bank Address:</strong><br>
                        {{ $wireRequest->bank_address }}<br>
                        {{ $wireRequest->bank_city }}, {{ $wireRequest->bank_state }} {{ $wireRequest->bank_zip }}<br>
                        {{ $wireRequest->bank_country }}
                    </div>
                    
                    @if($wireRequest->user_notes)
                    <hr>
                    <div class="alert alert-info">
                        <strong>User Notes:</strong> {{ $wireRequest->user_notes }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">Finance Decision</div>
                <div class="card-body">
                    <p>Current Status: <strong><span class="badge bg-{{ $wireRequest->finance_status === 'approved' ? 'success' : ($wireRequest->finance_status === 'rejected' ? 'danger' : 'warning') }}">{{ strtoupper($wireRequest->finance_status) }}</span></strong></p>
                    
                    @if($wireRequest->finance_status === 'pending' || $wireRequest->finance_status === 'reviewed')
                    <form action="{{ route('admin.finance.bank-wire.update', $wireRequest->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="finance_status" class="form-label">Action</label>
                            <select name="finance_status" id="finance_status" class="form-select" required>
                                <option value="">Select Action...</option>
                                <option value="approved">Approve Transfer</option>
                                <option value="rejected">Reject Transfer</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="finance_notes" class="form-label">Internal Notes (Visible to User on Reject)</label>
                            <textarea name="finance_notes" id="finance_notes" rows="3" class="form-control" required></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Submit Decision</button>
                    </form>
                    @else
                        <div class="alert alert-secondary mt-3">
                            <strong>Decision Recorded on:</strong> {{ $wireRequest->finance_reviewed_at?->format('Y-m-d H:i') }}<br>
                            <strong>Notes:</strong> {{ $wireRequest->finance_notes }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
