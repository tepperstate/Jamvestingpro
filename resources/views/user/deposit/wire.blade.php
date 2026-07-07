@extends('layouts.user.app')

@section('title', 'Wire Transfer Deposit')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="glass-card-premium p-4 mb-4">
                <h4 class="font-weight-bold text-white mb-3">Wire Transfer Deposit</h4>
                <p class="text-secondary mb-4">Submit a wire transfer request to fund your account. Once submitted, you will receive our banking instructions. Please allow 1-3 business days for processing after the transfer is made.</p>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('wire.deposit.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-secondary small">Amount (USD)</label>
                            <input type="number" name="amount" class="form-control" placeholder="1000.00" min="100" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary small">Your Bank Name</label>
                            <input type="text" name="bank_name" class="form-control" placeholder="e.g. Chase Bank" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary small">Account Name</label>
                            <input type="text" name="account_name" class="form-control" placeholder="Name on bank account" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary small">Account Number</label>
                            <input type="text" name="account_number" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary small">Routing Number (Optional)</label>
                            <input type="text" name="routing_number" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary small">SWIFT/BIC Code (Optional)</label>
                            <input type="text" name="swift_code" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label text-secondary small">Additional Message (Optional)</label>
                            <textarea name="message" class="form-control" rows="2"></textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2">Submit Wire Request</button>
                </form>
            </div>

            <!-- History -->
            @if($requests->count() > 0)
            <div class="glass-card-premium p-4">
                <h5 class="text-white mb-4">Recent Wire Requests</h5>
                <div class="table-responsive">
                    <table class="table table-dark table-borderless text-white mb-0">
                        <thead>
                            <tr class="text-secondary small" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Bank</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $req)
                            <tr>
                                <td>{{ $req->created_at->format('M d, Y') }}</td>
                                <td class="font-weight-bold">${{ number_format($req->amount, 2) }}</td>
                                <td>{{ $req->bank_name }}</td>
                                <td>
                                    @if($req->status === 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($req->status === 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.glass-card-premium {
    background: rgba(16, 18, 27, 0.4);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 16px;
}
.form-control {
    background: rgba(0, 0, 0, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: white;
}
.form-control:focus {
    background: rgba(0, 0, 0, 0.3);
    border-color: #0d6efd;
    color: white;
    box-shadow: none;
}
</style>
@endsection
