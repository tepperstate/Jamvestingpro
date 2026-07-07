@extends('layouts.user.app')

@section('title', 'Withdrawal History')

@section('content')
<style>
@media (max-width: 767.98px) {
    .mobile-cards-view {
        display: flex !important;
    }
}
</style>
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h2 class="outfit font-weight-bold mb-1">Electronic Ledger</h2>
            <p class="text-secondary small mb-0">Monitor your transaction history and settlement status.</p>
        </div>
        <button class="btn btn-premium px-4 py-2" data-toggle="modal" data-target="#cryptoModal">
            <i class="ri-add-circle-line me-1"></i> NEW WITHDRAWAL
        </button>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="glass-card p-4">
                <div class="small text-secondary mb-1">Lifetime Withdrawals</div>
                <div class="h3 mb-0 outfit font-weight-bold text-danger">-${{ number_format($data->where('status', 'confirmed')->sum('amount'), 2) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card p-4">
                <div class="small text-secondary mb-1">Pending Processing</div>
                <div class="h3 mb-0 outfit font-weight-bold text-warning">${{ number_format($data->where('status', 'pending')->sum('amount'), 2) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card p-4">
                <div class="small text-secondary mb-1">Average Payout Time</div>
                <div class="h3 mb-0 outfit font-weight-bold text-primary">1.2h</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="glass-card p-4">
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-dark table-hover align-middle">
                        <thead class="small text-secondary uppercase font-weight-bold">
                            <tr>
                                <th>Requested</th>
                                <th>Reference</th>
                                <th>Method</th>
                                <th>Destination</th>
                                <th>Amount</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $w)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($w->created_at)->format('d M, Y H:i') }}</td>
                                <td><code class="text-primary">{{ $w->trx_id }}</code></td>
                                <td><span class="badge bg-secondary-soft text-white px-3 py-2">{{ strtoupper($w->type) }}</span></td>
                                <td class="small text-secondary text-truncate" style="max-width: 200px;">{{ $w->address }}</td>
                                <td class="font-weight-bold">-${{ number_format($w->amount, 2) }}</td>
                                <td class="text-center">
                                    @php
                                        $statusClass = [
                                            'confirmed' => 'bg-success-soft text-success',
                                            'pending' => 'bg-warning-soft text-warning',
                                            'cancelled' => 'bg-danger-soft text-danger',
                                            'failed' => 'bg-danger-soft text-danger',
                                        ][strtolower($w->status)] ?? 'bg-secondary-soft text-secondary';
                                    @endphp
                                    <span class="badge {{ $statusClass }} px-3 py-2 rounded-pill" style="min-width: 80px;">
                                        {{ strtoupper($w->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center py-5 text-secondary">No withdrawals found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards View -->
                <div class="mobile-cards-view d-md-none flex-column gap-3 mt-3">
                    @forelse($data as $w)
                    <div class="glass-card p-3 ledger-mobile-card" style="background: rgba(16, 18, 27, 0.4); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px;">
                        @php
                            $statusClassText = [
                                'confirmed' => 'text-success',
                                'pending' => 'text-warning',
                                'cancelled' => 'text-danger',
                                'failed' => 'text-danger',
                            ][strtolower($w->status)] ?? 'text-secondary';
                        @endphp
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-secondary border-opacity-25">
                            <div class="d-flex align-items-center gap-2">
                                <div>
                                    <div class="text-white fw-bold outfit ledger-search-target">{{ strtoupper($w->type) }}</div>
                                    <div class="text-secondary" style="font-size: 10px;">{{ \Carbon\Carbon::parse($w->created_at)->format('d M, Y H:i') }}</div>
                                </div>
                            </div>
                            <span class="fw-bold small text-uppercase {{ $statusClassText }}" style="letter-spacing: 0.5px;">{{ $w->status }}</span>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-12">
                                <div class="text-secondary" style="font-size: 0.7rem;">Destination</div>
                                <div class="small text-secondary text-truncate" style="max-width: 250px;">{{ $w->address }}</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between pt-2">
                            <div class="text-secondary" style="font-size: 0.75rem;">Amount</div>
                            <div class="fw-bold text-white">-${{ number_format($w->amount, 2) }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-secondary">No withdrawals found.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-success-soft { background: rgba(255, 51, 51, 0.1); }
    .bg-warning-soft { background: rgba(245, 158, 11, 0.1); }
    .bg-danger-soft { background: rgba(239, 68, 68, 0.1); }
    .bg-secondary-soft { background: rgba(255, 255, 255, 0.05); }
</style>

@include('exchange.modals.withdrawal_types')
@endsection


