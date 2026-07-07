@extends('layouts.user.app')
@section('title', 'Auto-Invest (DCA)')

@section('content')
<div class="mobile-container pb-5 px-3 mt-3">
    <!-- Header -->
    <div class="mb-4 text-center">
        <h1 class="h3 font-weight-bold text-white mb-2" style="font-family: 'Outfit', sans-serif;">Auto-Invest (DCA)</h1>
        <p class="text-secondary small mb-0">Automate your crypto purchases with Dollar-Cost Averaging.</p>
    </div>

    @if(session('status'))
        <div class="alert alert-success border-0 mb-4" style="background: rgba(40, 167, 69, 0.1); color: #28a745; border-radius: 12px;">
            <i class="ri-check-line me-1"></i> {{ session('status') }}
        </div>
    @endif

    <!-- DCA Plans -->
    <div class="d-flex flex-column gap-4">
        @forelse($plans ?? [] as $plan)
        <div class="glass-card-gold p-4 position-relative overflow-hidden" style="border-radius: 20px;">
            <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: radial-gradient(circle, rgba(153,0,0,0.15), transparent); border-radius: 50%;"></div>
            
            <div class="text-center mb-4">
                <h5 class="text-white font-weight-bold mb-1">{{ $plan->name }}</h5>
                <span class="badge" style="background: rgba(255,255,255,0.05); color: #fff; font-weight: normal;">Bot Strategy</span>
            </div>

            <div class="d-flex justify-content-between align-items-center p-3 mb-3 rounded" style="background: rgba(0,0,0,0.3); border: 1px solid rgba(153,0,0,0.1);">
                <div>
                    <div class="small text-secondary mb-1" style="font-size: 0.65rem;">Asset to Buy</div>
                    <div class="d-flex align-items-center">
                        <img src="{{ \App\Services\AssetLogoService::getLogoUrl($plan->asset, $plan->asset_type ?? 'crypto') }}" style="width: 20px; height: 20px; border-radius: 50%; margin-right: 8px;">
                        <strong class="text-white">{{ $plan->asset }}</strong>
                    </div>
                </div>
                <div class="text-right">
                    <div class="small text-secondary mb-1" style="font-size: 0.65rem;">Frequency</div>
                    <strong class="text-gold">{{ ucfirst($plan->frequency) }}</strong>
                </div>
            </div>

            <form action="{{ url('dca/subscribe') }}" method="POST">
                @csrf
                <input type="hidden" name="dca_plan_id" value="{{ $plan->id }}">
                <div class="form-group mb-3">
                    <div class="input-group">
                        <span class="input-group-text border-0 text-white-50" style="background: rgba(0,0,0,0.5); border-radius: 12px 0 0 12px;">$</span>
                        <input type="number" name="amount" class="form-control text-white border-0 shadow-none" placeholder="Amount per {{ $plan->frequency }}" required style="background: rgba(0,0,0,0.5); border-radius: 0 12px 12px 0;">
                    </div>
                </div>
                <button type="submit" class="btn btn-gold w-100 py-3 rounded-pill fw-bold shadow">Start Bot</button>
            </form>
        </div>
        @empty
        <div class="glass-card-gold p-5 text-center" style="border-radius: 20px;">
            <i class="ri-robot-2-line text-white-50 fs-1 d-block mb-3"></i>
            <p class="text-secondary small mb-0">No active DCA plans available right now.</p>
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
