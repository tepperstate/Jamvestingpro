@extends('layouts.user.app')
@section('content')

<style>
.mobile-p2p-container {
    padding: 15px;
    background: #0b0e14;
    min-height: 100vh;
    padding-bottom: 90px;
}
.glass-card-mobile {
    background: rgba(16, 18, 27, 0.6);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 215, 0, 0.15); /* Gold accent */
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}
.gold-text {
    color: #ffd700;
}
.p2p-merchant-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255, 215, 0, 0.1);
    color: #ffd700;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    border: 1px solid rgba(255, 215, 0, 0.3);
}
.payment-badge {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #e2e8f0;
    font-size: 0.65rem;
    padding: 4px 8px;
    border-radius: 6px;
    margin-right: 4px;
    margin-bottom: 4px;
    display: inline-block;
}
.btn-p2p-action {
    width: 100%;
    padding: 10px;
    border-radius: 10px;
    font-weight: bold;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}
.btn-buy {
    background: rgba(255, 51, 51, 0.15);
    color: #ff3333;
    border: 1px solid rgba(255, 51, 51, 0.3);
}
.btn-sell {
    background: rgba(239, 68, 68, 0.15);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.3);
}
</style>

<div class="mobile-p2p-container">
    <div class="mb-4">
        <h4 class="text-white font-weight-bold" style="font-family: 'Outfit', sans-serif;">P2P Market</h4>
        <p class="text-secondary small mb-0">Trade directly with other users via escrow.</p>
    </div>

    @forelse($listings as $item)
    <div class="glass-card-mobile">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center">
                <div class="p2p-merchant-avatar me-3">
                    {{ substr($item->user->first_name ?? 'T', 0, 1) }}
                </div>
                <div>
                    <div class="text-white fw-bold">{{ $item->user->first_name ?? 'Trader' }}</div>
                    <div class="small text-secondary" style="font-size: 0.7rem;">
                        <span class="text-success">{{ $item->total_trades }} orders</span> | {{ $item->completion_rate }}%
                    </div>
                </div>
            </div>
            <div class="text-end">
                <div class="d-flex align-items-center justify-content-end mb-1">
                    <img src="{{ \App\Services\AssetLogoService::getLogoUrl($item->asset, $item->asset_type ?? 'crypto') }}" alt="{{ $item->asset }}" style="width: 16px; height: 16px; border-radius: 50%; margin-right: 4px;">
                    <span class="text-white fw-bold">{{ $item->asset }}</span>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-6">
                <div class="text-secondary small">Price</div>
                <div class="text-primary fw-bold" style="font-size: 1.1rem;">{{ $item->price }} <span class="small">{{ $item->currency }}</span></div>
            </div>
            <div class="col-6 text-end">
                <div class="text-secondary small">Limits</div>
                <div class="text-white fw-bold" style="font-size: 0.85rem;">{{ $item->min_order }} - {{ $item->max_order }} {{ $item->currency }}</div>
            </div>
        </div>

        <div class="mb-3">
            <div class="text-secondary small mb-1">Payment Methods</div>
            <div>
                @foreach(json_decode($item->payment_methods) as $pm)
                    <span class="payment-badge">{{ $pm }}</span>
                @endforeach
            </div>
        </div>

        <div>
            <button class="btn btn-p2p-action {{ $item->type == 'sell' ? 'btn-buy' : 'btn-sell' }}">
                {{ $item->type == 'sell' ? 'Buy' : 'Sell' }} {{ $item->asset }}
            </button>
        </div>
    </div>
    @empty
    <div class="text-center py-5">
        <i class="ri-exchange-funds-line text-secondary" style="font-size: 3rem; opacity: 0.5;"></i>
        <p class="text-secondary mt-3">No listings available.</p>
    </div>
    @endforelse
</div>

@endsection
