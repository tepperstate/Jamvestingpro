@extends('layouts.user.app')
@section('title', 'Crypto Loans')

@section('content')
<div class="mobile-container pb-5 px-3 mt-3">
    <!-- Header -->
    <div class="mb-4 text-center">
        <h1 class="h3 font-weight-bold text-white mb-2" style="font-family: 'Outfit', sans-serif;">Crypto Loans</h1>
        <p class="text-secondary small mb-0">Use your crypto as collateral to borrow stablecoins without selling your assets.</p>
    </div>

    <!-- Loan Plans -->
    <h4 class="font-weight-bold text-white mb-3" style="font-family: 'Outfit', sans-serif;">Available Plans</h4>
    <div class="d-flex flex-column gap-4 mb-5">
        @foreach($plans as $plan)
        <div class="glass-card-gold p-4 position-relative overflow-hidden" style="border-radius: 20px;">
            <div style="position: absolute; top: -30px; left: -30px; width: 100px; height: 100px; background: radial-gradient(circle, rgba(153,0,0,0.15), transparent); border-radius: 50%;"></div>
            
            <h5 class="text-gold font-weight-bold mb-4">{{ $plan->name }}</h5>
            
            <div class="d-flex justify-content-between mb-4">
                <div>
                    <small class="text-secondary d-block text-uppercase mb-1" style="font-size: 0.65rem;">Collateral</small>
                    <div class="d-flex align-items-center">
                        <img src="{{ \App\Services\AssetLogoService::getLogoUrl($plan->collateral_asset, $plan->asset_type ?? 'crypto') }}" style="width: 20px; height: 20px; border-radius: 50%; margin-right: 6px;">
                        <strong class="text-white">{{ $plan->collateral_asset }}</strong>
                    </div>
                </div>
                <div class="text-right">
                    <small class="text-secondary d-block text-uppercase mb-1" style="font-size: 0.65rem;">Borrow</small>
                    <strong class="text-white">{{ $plan->loan_asset }}</strong>
                </div>
            </div>
            
            <div class="p-3 mb-4 rounded" style="background: rgba(0,0,0,0.3); border: 1px solid rgba(153,0,0,0.1);">
                <div class="d-flex justify-content-between mb-2 pb-2 border-bottom" style="border-color: rgba(255,255,255,0.05) !important;">
                    <span class="small text-secondary">Max LTV</span>
                    <span class="text-success font-weight-bold small">{{ $plan->max_ltv }}%</span>
                </div>
                <div class="d-flex justify-content-between mb-2 pb-2 border-bottom" style="border-color: rgba(255,255,255,0.05) !important;">
                    <span class="small text-secondary">Daily Interest</span>
                    <span class="text-danger font-weight-bold small">{{ $plan->interest_rate_daily }}%</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="small text-secondary">Term</span>
                    <span class="text-white font-weight-bold small">{{ $plan->duration_days }} days</span>
                </div>
            </div>

            <button class="btn btn-outline-gold w-100 py-2 rounded-pill fw-bold" data-toggle="modal" data-target="#borrowModal{{ $plan->id }}">
                Borrow Now
            </button>
        </div>
        @endforeach
    </div>

    <!-- Active Loans -->
    <h4 class="font-weight-bold text-white mb-3" style="font-family: 'Outfit', sans-serif;">Your Active Loans</h4>
    <div class="d-flex flex-column gap-3">
        @forelse($positions as $pos)
        <div class="glass-card-gold p-3" style="border-radius: 16px;">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom" style="border-color: rgba(255,255,255,0.05) !important;">
                <span class="text-secondary small">#{{ $pos->loan_id }}</span>
                @if($pos->status == 'liquidated')
                    <span class="badge" style="background: rgba(220,53,69,0.1); color: #dc3545;">Liquidated</span>
                @elseif($pos->status == 'repaid')
                    <span class="badge" style="background: rgba(40,167,69,0.1); color: #28a745;">Repaid</span>
                @else
                    <span class="badge" style="background: rgba(153,0,0,0.1); color: #990000;">Active</span>
                @endif
            </div>

            <div class="row g-2 mb-3">
                <div class="col-6">
                    <div class="small text-secondary" style="font-size: 0.65rem;">Collateral ({{ $pos->plan->collateral_asset }})</div>
                    <div class="text-white font-weight-bold" style="font-size: 0.85rem;">{{ rtrim(rtrim(sprintf('%.8f', $pos->collateral_amount), '0'), '.') }}</div>
                </div>
                <div class="col-6 text-right">
                    <div class="small text-secondary" style="font-size: 0.65rem;">Borrowed</div>
                    <div class="text-white font-weight-bold" style="font-size: 0.85rem;">${{ number_format($pos->loan_amount, 2) }}</div>
                </div>
            </div>

            <div class="p-2 mb-3 rounded" style="background: rgba(0,0,0,0.3);">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="small text-secondary">LTV</div>
                    <div class="font-weight-bold {{ $pos->current_ltv >= $pos->plan->liquidation_ltv ? 'text-danger' : 'text-success' }}" style="font-size: 0.85rem;">
                        {{ $pos->current_ltv }}%
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center pt-2 border-top" style="border-color: rgba(255,255,255,0.05) !important;">
                <div>
                    <div class="small text-secondary" style="font-size: 0.65rem;">Interest</div>
                    <div class="text-warning font-weight-bold" style="font-size: 0.85rem;">${{ number_format($pos->interest_accrued, 2) }}</div>
                </div>
                <div class="text-right">
                    <div class="small text-secondary" style="font-size: 0.65rem;">Balance</div>
                    <div class="text-gold font-weight-bold" style="font-size: 0.95rem;">${{ number_format($pos->remaining_balance, 2) }}</div>
                </div>
            </div>
        </div>
        @empty
        <div class="glass-card-gold p-4 text-center">
            <i class="ri-wallet-3-line text-white-50 fs-1 d-block mb-2"></i>
            <p class="text-secondary small mb-0">No active loans found.</p>
        </div>
        @endforelse
    </div>
</div>

@push('modals')
@foreach($plans as $plan)
<div class="modal fade" id="borrowModal{{ $plan->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('user.loans.borrow', $plan->id) }}" method="POST">
            @csrf
            <div class="modal-content glass-card-gold" style="border: 1px solid rgba(153,0,0,0.2);">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-gold fw-bold">Borrow {{ $plan->loan_asset }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body py-4">
                    <p class="text-white-50 small mb-4">Borrow {{ $plan->loan_asset }} against your {{ $plan->collateral_asset }} collateral.</p>

                    <div class="form-group mb-3">
                        <label class="text-white-50 mb-2 small">Collateral Amount ({{ $plan->collateral_asset }})</label>
                        <input type="number" name="collateral_amount" class="form-control text-white border-0 shadow-none" required step="0.00000001" style="background: rgba(0,0,0,0.5); border-radius: 12px;">
                    </div>
                    
                    <div class="form-group mb-4">
                        <label class="text-white-50 mb-2 small">Loan Amount ({{ $plan->loan_asset }})</label>
                        <input type="number" name="loan_amount" class="form-control text-white border-0 shadow-none" required step="0.01" style="background: rgba(0,0,0,0.5); border-radius: 12px;">
                    </div>

                    <div class="alert border-0 d-flex align-items-center p-3" style="background: rgba(255, 193, 7, 0.1); color: #ffc107; border-radius: 12px;">
                        <i class="ri-error-warning-line fs-4 me-3"></i>
                        <div style="font-size: 0.75rem;">Ensure your requested loan amount does not exceed max LTV of <strong>{{ $plan->max_ltv }}%</strong>. Liquidation occurs at <strong>{{ $plan->liquidation_ltv }}%</strong>.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-gold w-100 fw-bold py-3 rounded-pill">Confirm Loan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach
@endpush

<style>
    body { background-color: #0a0b0e; color: #fff; }
    .text-gold { color: #990000 !important; }
    .glass-card-gold {
        background: rgba(16, 18, 27, 0.6);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(153, 0, 0, 0.15);
    }
    .btn-gold {
        background: linear-gradient(45deg, #990000, #f3e5ab);
        color: #0a0b0e;
        border: none;
    }
    .btn-outline-gold {
        border: 1px solid #990000;
        color: #990000;
        background: transparent;
    }
    .btn-outline-gold:hover {
        background: #990000;
        color: #0a0b0e;
    }
</style>
@endsection
