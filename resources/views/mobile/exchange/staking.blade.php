@extends('layouts.user.app')
@section('title', 'Crypto Staking')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

<div class="mobile-container pb-5 px-3 mt-3">
    <!-- Header -->
    <div class="mb-4 text-center">
        <h1 class="h3 font-weight-bold text-white mb-2" style="font-family: 'Outfit', sans-serif;">Crypto Staking</h1>
        <p class="text-secondary small mb-3">Stake your crypto and earn rewards automatically.</p>
        <div class="glass-card-gold px-3 py-2 text-center mx-auto" style="max-width: 200px; border-radius: 16px;">
            <div class="small text-gold text-uppercase" style="font-size: 0.65rem; letter-spacing: 1px;">Active Positions</div>
            <div class="h4 mb-0 text-white font-weight-bold">{{ $positions->where('status', 'active')->count() }}</div>
        </div>
    </div>

    <!-- Active Positions -->
    @if($positions->count() > 0)
    <h4 class="font-weight-bold text-white mb-3" style="font-family: 'Outfit', sans-serif;">My Vault Allocations</h4>
    <div class="d-flex flex-column gap-3 mb-5">
        @foreach($positions as $pos)
        @php
            $endDate = \Illuminate\Support\Carbon::parse($pos->end_date);
            $daysLeft = max(0, now()->diffInDays($endDate, false));
            $isMatured = now()->gte($endDate);
            
            $initialAmount = floatval($pos->amount);
            $earned = floatval($pos->earned);
            $currentValue = $initialAmount + $earned;
            
            $apy = floatval($pos->plan?->apy_percentage ?? 0);
            $totalDays = max(1, $pos->plan?->lock_days ?? 1);
            $totalHours = $totalDays * 24;
            
            $termMultiplier = 1 + ($apy / 100);
            $hourlyRate = pow($termMultiplier, 1 / $totalHours) - 1;
            $perSecondRate = pow(1 + $hourlyRate, 1 / 3600) - 1;
            
            $lastCredited = $pos->updated_at ? \Illuminate\Support\Carbon::parse($pos->updated_at) : \Illuminate\Support\Carbon::parse($pos->start_date);
            $secondsSinceCredit = max(0, now()->diffInSeconds($lastCredited));
            
            $liveCurrentValue = $currentValue * pow(1 + $perSecondRate, $secondsSinceCredit);
            $liveAccrued = $liveCurrentValue - $initialAmount;
            
            if ($pos->status === 'completed' || $isMatured) {
                $liveAccrued = $earned;
                $perSecondRate = 0;
            }
        @endphp
        <div class="glass-card-gold p-3 active-staking-row" data-principal="{{ $initialAmount }}" data-current-val="{{ $liveCurrentValue }}" data-rate="{{ $perSecondRate }}" data-status="{{ $pos->status }}" style="border-radius: 20px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <div class="font-weight-bold text-white" style="font-size: 0.9rem;">{{ $pos->plan?->name ?? 'Unknown' }}</div>
                    <div class="small text-gold" style="font-size: 0.7rem;">{{ $pos->plan?->apy_percentage ?? 0 }}% APY</div>
                </div>
                <span class="badge rounded-pill px-2 py-1" style="background: {{ $pos->status == 'active' ? 'rgba(153, 0, 0, 0.1)' : 'rgba(255,255,255,0.05)' }}; color: {{ $pos->status == 'active' ? '#990000' : '#6b7280' }}; font-size: 0.6rem;">
                    {{ strtoupper($pos->status) }}
                </span>
            </div>
            
            <div class="d-flex justify-content-between mb-3">
                <div>
                    <div class="small text-secondary" style="font-size: 0.7rem;">Staked</div>
                    <div class="font-weight-bold text-white">${{ number_format($initialAmount, 2) }}</div>
                </div>
                <div class="text-right">
                    <div class="small text-secondary" style="font-size: 0.7rem;">Earned</div>
                    <div class="font-weight-bold text-success live-staking-display">+${{ number_format($liveAccrued, 6) }}</div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center pt-2 border-top" style="border-color: rgba(255,255,255,0.05) !important;">
                <div>
                    <div class="small text-white" style="font-size: 0.75rem;">{{ $isMatured ? 'Matured' : $daysLeft . ' days left' }}</div>
                    <div class="small text-secondary" style="font-size: 0.65rem;">{{ $endDate->format('M d, Y') }}</div>
                </div>
                @if($pos->status == 'active')
                <button onclick="unstake({{ $pos->id }})" class="btn btn-sm btn-outline-gold px-3 rounded-pill" style="font-size: 0.7rem; border: 1px solid #990000; color: #990000;">
                    {{ $isMatured ? 'Claim' : 'Unstake' }}
                </button>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Staking Plans -->
    <h4 class="font-weight-bold text-white mb-3" style="font-family: 'Outfit', sans-serif;">Staking Plans</h4>
    <div class="d-flex flex-column gap-4 mb-4">
        @foreach($plans as $plan)
        <div class="glass-card-gold p-4 text-center" style="border-radius: 24px;">
            <div class="d-flex justify-content-center mb-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: rgba(153, 0, 0, 0.1); border: 1px solid rgba(153, 0, 0, 0.2);">
                    <img src="{{ \App\Services\AssetLogoService::getLogoUrl($plan->symbol, $plan->asset_type ?? 'crypto') }}" style="width: 32px; height: 32px; border-radius: 50%; object-fit: contain;">
                </div>
            </div>
            
            <h5 class="font-weight-bold text-white mb-0" style="font-family: 'Outfit', sans-serif;">{{ $plan->name }}</h5>
            <div class="small text-secondary mb-3">{{ $plan->symbol }} · {{ $plan->lock_days }}-Day Lock</div>
            
            <div class="py-3 mb-3" style="background: rgba(153, 0, 0, 0.05); border-radius: 16px; border: 1px solid rgba(153, 0, 0, 0.1);">
                <div class="small text-gold text-uppercase mb-1" style="font-size: 0.65rem;">Annual Percentage Yield</div>
                <div class="h2 mb-0 text-white font-weight-bold">{{ $plan->apy_percentage }}%</div>
            </div>

            <div class="d-flex justify-content-between mb-4">
                <div class="text-left">
                    <div class="small text-secondary" style="font-size: 0.7rem;">Min Stake</div>
                    <div class="font-weight-bold text-white">${{ number_format($plan->min_amount) }}</div>
                </div>
                <div class="text-right">
                    <div class="small text-secondary" style="font-size: 0.7rem;">Max Stake</div>
                    <div class="font-weight-bold text-white">${{ number_format($plan->max_amount) }}</div>
                </div>
            </div>

            <button class="btn btn-gold w-100 py-2 rounded-pill shadow-lg" onclick="stakeNow('{{ $plan->id }}', '{{ $plan->name }}', '{{ $plan->min_amount }}', '{{ $plan->max_amount }}')" style="font-weight: 700;">
                <i class="ri-lock-line me-2"></i> Stake Now
            </button>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mb-5">
        {{ $plans->links('pagination::bootstrap-5') }}
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
    .btn-outline-gold:active { background: rgba(153,0,0,0.2); }
</style>

@push('js')
<script>
    function stakeNow(id, name, min, max) {
        let amount = prompt(`Enter stake amount for ${name} (Min: $${min}, Max: $${max}):`, min);
        if (amount && parseFloat(amount) >= parseFloat(min)) {
            fetch("{{ route('user.staking.stake') }}", {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                body: JSON.stringify({id, amount})
            })
            .then(res => res.json().then(data => ({ok: res.ok, data})))
            .then(({ok, data}) => {
                if (ok && data.status) {
                    toastr.success(data.status);
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    toastr.error(data.error || 'Staking failed');
                }
            })
            .catch(() => toastr.error('Network error'));
        }
    }

    function unstake(id) {
        if (!confirm('Are you sure you want to unstake? Early withdrawal forfeits yield.')) return;
        fetch("{{ route('user.staking.unstake') }}", {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            body: JSON.stringify({id})
        })
        .then(res => res.json().then(data => ({ok: res.ok, data})))
        .then(({ok, data}) => {
            if (ok && data.status) {
                toastr.success(data.status);
                setTimeout(() => window.location.reload(), 1500);
            } else {
                toastr.error(data.error || 'Unstaking failed');
            }
        })
        .catch(() => toastr.error('Network error'));
    }

    // Dripping Profit Animation
    window.addEventListener('load', () => {
        const rows = document.querySelectorAll('.active-staking-row');
        if (rows.length > 0) {
            let lastTick = performance.now();
            function tickStakingProfit(timestamp) {
                const deltaSeconds = (timestamp - lastTick) / 1000;
                lastTick = timestamp;

                rows.forEach(row => {
                    if (row.dataset.status === 'completed') return;
                    let principal = parseFloat(row.dataset.principal);
                    let currentVal = parseFloat(row.dataset.currentVal);
                    let rate = parseFloat(row.dataset.rate);
                    currentVal = currentVal * Math.pow(1 + rate, deltaSeconds);
                    row.dataset.currentVal = currentVal;
                    let accrued = currentVal - principal;
                    let display = row.querySelector('.live-staking-display');
                    if (display) display.innerHTML = `+$${accrued.toFixed(6)}`;
                });
                requestAnimationFrame(tickStakingProfit);
            }
            requestAnimationFrame(tickStakingProfit);
        }
    });
</script>
@endpush
@endsection
