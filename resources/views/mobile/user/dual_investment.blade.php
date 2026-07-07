@extends('layouts.user.app')
@section('title', 'Dual Investment')

@section('content')
<div class="mobile-container pb-5 px-3 mt-3">
    <!-- Header -->
    <div class="mb-4 text-center">
        <h1 class="h3 font-weight-bold text-white mb-2" style="font-family: 'Outfit', sans-serif;">Dual Investment</h1>
        <p class="text-secondary small mb-0">High-yield structured products. Predict price direction and earn high APY.</p>
    </div>

    @if(session('status'))
        <div class="alert alert-success border-0 mb-4" style="background: rgba(40, 167, 69, 0.1); color: #28a745; border-radius: 12px;">
            <i class="ri-check-line me-1"></i> {{ session('status') }}
        </div>
    @endif

    <!-- Available Products -->
    <div class="d-flex flex-column gap-4 mb-5">
        @forelse($products ?? [] as $product)
        <div class="glass-card-gold p-4 position-relative overflow-hidden" style="border-radius: 20px;">
            <div style="position: absolute; top: -30px; right: -30px; width: 100px; height: 100px; background: radial-gradient(circle, rgba(153,0,0,0.15), transparent); border-radius: 50%;"></div>
            
            <div class="text-center mb-4">
                <h5 class="text-white font-weight-bold mb-1">{{ $product->name }}</h5>
                <span class="badge" style="background: rgba(153,0,0,0.1); color: #990000;">{{ $product->type ?? 'Dual Product' }}</span>
            </div>
            
            <div class="p-3 mb-4 rounded" style="background: rgba(0,0,0,0.3); border: 1px solid rgba(153,0,0,0.1);">
                <div class="d-flex justify-content-between mb-2 pb-2 border-bottom" style="border-color: rgba(255,255,255,0.05) !important;">
                    <span class="small text-secondary">Underlying</span>
                    <div class="d-flex align-items-center">
                        <img src="{{ \App\Services\AssetLogoService::getLogoUrl($product->underlying_asset, $product->asset_type ?? 'crypto') }}" style="width: 16px; height: 16px; border-radius: 50%; margin-right: 6px;">
                        <strong class="text-white small">{{ $product->underlying_asset }}</strong>
                    </div>
                </div>
                <div class="d-flex justify-content-between mb-2 pb-2 border-bottom" style="border-color: rgba(255,255,255,0.05) !important;">
                    <span class="small text-secondary">Strike Price</span>
                    <strong class="text-white small">${{ number_format($product->strike_price, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2 pb-2 border-bottom" style="border-color: rgba(255,255,255,0.05) !important;">
                    <span class="small text-secondary">APY</span>
                    <strong class="text-success small">{{ $product->apy }}%</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="small text-secondary">Duration</span>
                    <strong class="text-white small">{{ $product->duration_days }} Days</strong>
                </div>
            </div>

            <form action="{{ route('user.dual.buy') }}" method="POST">
                @csrf
                <input type="hidden" name="dual_product_id" value="{{ $product->id }}">
                <div class="form-group mb-3">
                    <div class="input-group">
                        <input type="number" step="any" name="amount" class="form-control text-white border-0 shadow-none" placeholder="Amount ({{ $product->deposit_asset ?? 'USD' }})" required style="background: rgba(0,0,0,0.5); border-radius: 12px 0 0 12px;">
                        <span class="input-group-text border-0 text-gold" style="background: rgba(0,0,0,0.5); border-radius: 0 12px 12px 0;">{{ $product->deposit_asset ?? 'USD' }}</span>
                    </div>
                </div>
                <button type="submit" class="btn btn-gold w-100 py-3 rounded-pill fw-bold shadow">Subscribe</button>
            </form>
        </div>
        @empty
        <div class="glass-card-gold p-5 text-center" style="border-radius: 20px;">
            <i class="ri-exchange-funds-line text-white-50 fs-1 d-block mb-3"></i>
            <p class="text-secondary small mb-0">No active Dual Investment products available right now.</p>
        </div>
        @endforelse
    </div>

    <!-- Active Subscriptions -->
    <h4 class="font-weight-bold text-white mb-3" style="font-family: 'Outfit', sans-serif;">My Subscriptions</h4>
    <div class="d-flex flex-column gap-3">
        @forelse($subscriptions ?? [] as $sub)
        <div class="glass-card-gold p-3" style="border-radius: 16px;">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom" style="border-color: rgba(255,255,255,0.05) !important;">
                <div>
                    <h6 class="text-white font-weight-bold mb-0">{{ optional($sub->dualInvestmentProduct)->name ?? 'Dual Investment' }}</h6>
                    <small class="text-secondary" style="font-size: 0.7rem;">{{ optional($sub->dualInvestmentProduct)->underlying_asset }}</small>
                </div>
                <div>
                    @if($sub->status === 'active' || $sub->status === 'pending')
                        <span class="badge" style="background: rgba(153,0,0,0.1); color: #990000;">Active</span>
                    @elseif($sub->status === 'win')
                        <span class="badge" style="background: rgba(40,167,69,0.1); color: #28a745;">Won</span>
                    @elseif($sub->status === 'loss')
                        <span class="badge" style="background: rgba(220,53,69,0.1); color: #dc3545;">Lost</span>
                    @else
                        <span class="badge" style="background: rgba(255,255,255,0.1); color: #fff;">{{ ucfirst($sub->status) }}</span>
                    @endif
                </div>
            </div>
            
            <div class="row g-2 mb-2">
                <div class="col-6">
                    <div class="small text-secondary" style="font-size: 0.65rem;">Amount</div>
                    <div class="text-white font-weight-bold" style="font-size: 0.85rem;">${{ number_format($sub->amount, 2) }}</div>
                </div>
                <div class="col-6 text-right">
                    <div class="small text-secondary" style="font-size: 0.65rem;">Expected Return</div>
                    <div class="text-success font-weight-bold" style="font-size: 0.85rem;">${{ number_format($sub->expected_return, 2) }}</div>
                </div>
            </div>
            
            <div class="text-right">
                <small class="text-white-50" style="font-size: 0.65rem;">{{ $sub->created_at->format('M d, Y H:i') }}</small>
            </div>
        </div>
        @empty
        <div class="glass-card-gold p-4 text-center">
            <p class="text-secondary small mb-0">No subscriptions found.</p>
        </div>
        @endforelse
    </div>
</div>

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
</style>
@endsection
