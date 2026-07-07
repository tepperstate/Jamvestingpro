@extends('layouts.user.app')
@section('title', 'Liquidity Pools')

@section('content')
<div class="mobile-container pb-5 px-3 mt-3">
    <!-- Header -->
    <div class="mb-4 text-center">
        <h1 class="h3 font-weight-bold text-white mb-2" style="font-family: 'Outfit', sans-serif;">Liquidity Pools</h1>
        <p class="text-secondary small mb-0">Provide liquidity to earn trading fees and yield farm rewards.</p>
    </div>

    <!-- Active Liquidity Pools -->
    <div class="d-flex flex-column gap-4 mb-5">
        @foreach($pools as $pool)
        <div class="glass-card-gold p-4 position-relative overflow-hidden" style="border-radius: 20px;">
            <div style="position: absolute; top: -30px; left: -30px; width: 100px; height: 100px; background: radial-gradient(circle, rgba(153,0,0,0.15), transparent); border-radius: 50%;"></div>
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="text-white font-weight-bold mb-0">{{ $pool->name }}</h5>
                <span class="badge" style="background: rgba(40,167,69,0.1); color: #28a745; font-size: 0.8rem; padding: 6px 12px; border-radius: 8px;">
                    {{ $pool->apy }}% APY
                </span>
            </div>

            <div class="row mb-4">
                <div class="col-6">
                    <span class="text-secondary d-block" style="font-size: 0.65rem;">Total Value Locked</span>
                    <strong class="text-white" style="font-size: 1rem;">${{ number_format($pool->tvl, 2) }}</strong>
                </div>
                <div class="col-6 text-right">
                    <span class="text-secondary d-block" style="font-size: 0.65rem;">24h Volume</span>
                    <strong class="text-white" style="font-size: 1rem;">${{ number_format($pool->volume_24h, 2) }}</strong>
                </div>
            </div>

            <form action="{{ route('user.liquidity.deposit') }}" method="POST">
                @csrf
                <input type="hidden" name="pool_id" value="{{ $pool->id }}">
                <div class="form-group mb-3">
                    <div class="input-group">
                        <span class="input-group-text border-0 text-white-50" style="background: rgba(0,0,0,0.5); border-radius: 12px 0 0 12px;">$</span>
                        <input type="number" name="amount" class="form-control text-white border-0 shadow-none" placeholder="Amount (USD)" required style="background: rgba(0,0,0,0.5); border-radius: 0 12px 12px 0;">
                    </div>
                </div>
                <button type="submit" class="btn btn-gold w-100 py-3 rounded-pill fw-bold shadow">Add Liquidity</button>
            </form>
        </div>
        @endforeach
    </div>

    <!-- Your Positions -->
    <h4 class="font-weight-bold text-white mb-3" style="font-family: 'Outfit', sans-serif;">Your Positions</h4>
    <div class="d-flex flex-column gap-3">
        @forelse($positions as $pos)
        <div class="glass-card-gold p-3" style="border-radius: 16px;">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom" style="border-color: rgba(255,255,255,0.05) !important;">
                <h6 class="text-white font-weight-bold mb-0">{{ $pos->pool->name }}</h6>
                <span class="badge" style="background: rgba(255,255,255,0.1); color: #fff;">{{ ucfirst($pos->status) }}</span>
            </div>
            
            <div class="row g-2 mb-2">
                <div class="col-6">
                    <div class="small text-secondary" style="font-size: 0.65rem;">Deposited</div>
                    <div class="text-white font-weight-bold" style="font-size: 0.85rem;">${{ number_format($pos->amount_deposited, 2) }}</div>
                </div>
                <div class="col-6 text-right">
                    <div class="small text-secondary" style="font-size: 0.65rem;">Earned</div>
                    <div class="text-success font-weight-bold" style="font-size: 0.85rem;">+${{ number_format($pos->earned_fees + $pos->earned_rewards, 2) }}</div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between align-items-center pt-2 border-top" style="border-color: rgba(255,255,255,0.05) !important;">
                <div class="small text-secondary" style="font-size: 0.7rem;">Current Value</div>
                <div class="text-gold font-weight-bold" style="font-size: 1rem;">${{ number_format($pos->current_value, 2) }}</div>
            </div>
        </div>
        @empty
        <div class="glass-card-gold p-4 text-center">
            <i class="ri-water-flash-line text-white-50 fs-1 d-block mb-2"></i>
            <p class="text-secondary small mb-0">No active positions.</p>
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
