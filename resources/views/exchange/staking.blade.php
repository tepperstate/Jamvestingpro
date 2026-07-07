@extends('layouts.user.app')
@section('title', 'Crypto Staking')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-5" data-aos="fade-up">
        <div class="col-xl-8">
            <h1 class="outfit font-weight-bold text-white mb-1" style="font-size: 2rem;">Crypto Staking</h1>
            <p class="text-secondary mb-0">Stake your crypto and earn rewards automatically. Longer lock periods earn higher APY.</p>
        </div>
        <div class="col-xl-4 text-xl-end d-flex align-items-center justify-content-xl-end mt-3 mt-xl-0">
            <div class="glass-card px-4 py-3 text-center shadow-lg" style="background: rgba(16, 18, 27, 0.4); backdrop-filter: blur(16px); border-radius: 20px; border: 1px solid rgba(255,255,255,0.05);">
                <div class="small text-secondary text-uppercase tracking-wider">Active Positions</div>
                <div class="h3 mb-0 text-primary outfit font-weight-bold">{{ $positions->where('status', 'active')->count() }}</div>
            </div>
        </div>
    </div>

    <!-- Staking Plans (Mobile Cards) -->
    <div class="row g-4 mb-5 d-lg-none">
        @foreach($plans as $plan)
        <div class="col-12 mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
            <div class="glass-card-premium h-100" style="background: rgba(16, 18, 27, 0.5); backdrop-filter: blur(16px); border-radius: 24px; border: 1px solid rgba(255,255,255,0.06); padding: 2rem; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: rgba(59, 130, 246, 0.08) !important; border: 1px solid rgba(59, 130, 246, 0.15);">
                        <img src="{{ \App\Services\AssetLogoService::getLogoUrl($plan->symbol, $plan->asset_type ?? 'crypto') }}" style="width: 32px; height: 32px; border-radius: 50%; object-fit: contain;">
                    </div>
                    <div>
                        <h3 class="outfit font-weight-bold mb-0 text-white">{{ $plan->name }}</h3>
                        <div class="small text-secondary">{{ $plan->symbol }} · {{ $plan->lock_days }}-Day Lock</div>
                    </div>
                </div>

                <div class="text-center py-4 mb-4" style="background: rgba(255, 51, 51, 0.04); border-radius: 16px; border: 1px solid rgba(255, 51, 51, 0.08);">
                    <div class="small text-secondary text-uppercase mb-1">Annual Percentage Yield</div>
                    <div class="h1 mb-0 text-success outfit font-weight-bold">{{ $plan->apy_percentage }}%</div>
                    <div class="small text-secondary">APY</div>
                </div>

                <div class="row g-0 mb-4">
                    <div class="col-6">
                        <div class="small text-secondary mb-1">Min Stake</div>
                        <div class="font-weight-bold text-white">${{ number_format($plan->min_amount) }}</div>
                    </div>
                    <div class="col-6 text-end">
                        <div class="small text-secondary mb-1">Max Stake</div>
                        <div class="font-weight-bold text-white">${{ number_format($plan->max_amount) }}</div>
                    </div>
                </div>

                <button class="btn btn-premium w-100 py-3 shadow-lg" onclick="stakeNow('{{ $plan->id }}', '{{ $plan->name }}', '{{ $plan->min_amount }}', '{{ $plan->max_amount }}')" style="font-weight: 800; border-radius: 14px;">
                    <i class="ri-lock-line me-2"></i> Stake Now
                </button>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Staking Plans (Desktop Table) -->
    <div class="row mb-5 d-none d-lg-block" data-aos="fade-up">
        <div class="col-12">
            <div class="glass-card satin-border overflow-hidden" style="border-radius: 20px;">
                <div class="table-responsive">
                    <table class="table text-white mb-0 align-middle">
                        <thead style="background: rgba(255,255,255,0.02);">
                            <tr>
                                <th class="border-0 small text-secondary py-3">ASSET</th>
                                <th class="border-0 small text-secondary py-3">LOCK PERIOD</th>
                                <th class="border-0 small text-secondary py-3">APY</th>
                                <th class="border-0 small text-secondary py-3">MIN STAKE</th>
                                <th class="border-0 small text-secondary py-3">MAX STAKE</th>
                                <th class="border-0 small text-secondary py-3 text-end">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($plans as $plan)
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);">
                                <td class="py-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: rgba(59, 130, 246, 0.08) !important; border: 1px solid rgba(59, 130, 246, 0.15);">
                                            <img src="{{ \App\Services\AssetLogoService::getLogoUrl($plan->symbol, $plan->asset_type ?? 'crypto') }}" style="width: 24px; height: 24px; border-radius: 50%; object-fit: contain;">
                                        </div>
                                        <div>
                                            <h6 class="outfit font-weight-bold mb-0 text-white">{{ $plan->name }}</h6>
                                            <div class="small text-secondary">{{ $plan->symbol }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4">{{ $plan->lock_days }} Days</td>
                                <td class="py-4 text-success font-weight-bold">{{ $plan->apy_percentage }}%</td>
                                <td class="py-4">${{ number_format($plan->min_amount) }}</td>
                                <td class="py-4">${{ number_format($plan->max_amount) }}</td>
                                <td class="py-4 text-end">
                                    <button class="btn btn-premium px-4 py-2 shadow-lg" onclick="stakeNow('{{ $plan->id }}', '{{ $plan->name }}', '{{ $plan->min_amount }}', '{{ $plan->max_amount }}')" style="font-weight: 800; border-radius: 10px; font-size: 0.85rem;">
                                        <i class="ri-lock-line me-1"></i> Stake
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mb-5 pagination-custom">
        {{ $plans->links('pagination::bootstrap-5') }}
    </div>

    <!-- Active Positions -->
    @if($positions->count() > 0)
    <div class="row mb-5" data-aos="fade-up">
        <div class="col-12">
            <h2 class="outfit font-weight-bold text-white mb-4">My Vault Allocations</h2>
            <div class="glass-card satin-border overflow-hidden" style="border-radius: 20px;">
                <div class="table-responsive">
                    <table class="table text-white mb-0">
                        <thead style="background: rgba(255,255,255,0.02);">
                            <tr>
                                <th class="border-0 small text-secondary py-3">PROTOCOL</th>
                                <th class="border-0 small text-secondary py-3">STAKED</th>
                                <th class="border-0 small text-secondary py-3">EARNED</th>
                                <th class="border-0 small text-secondary py-3">MATURITY</th>
                                <th class="border-0 small text-secondary py-3">STATUS</th>
                                <th class="border-0 small text-secondary py-3 text-end">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
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
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);" class="active-staking-row" data-principal="{{ $initialAmount }}" data-current-val="{{ $liveCurrentValue }}" data-rate="{{ $perSecondRate }}" data-status="{{ $pos->status }}">
                                <td class="py-4">
                                    <div class="font-weight-bold">{{ $pos->plan?->name ?? 'Unknown' }}</div>
                                    <div class="small text-secondary">{{ $pos->plan?->apy_percentage ?? 0 }}% APY</div>
                                </td>
                                <td class="py-4 font-weight-bold">${{ number_format($initialAmount, 2) }}</td>
                                <td class="py-4 text-success font-weight-bold live-staking-display" style="text-shadow: 0 0 10px rgba(255, 51, 51, 0.4); transition: color 0.2s;">+${{ number_format($liveAccrued, 6) }}</td>
                                <td class="py-4">
                                    <div class="small">{{ $isMatured ? 'Matured' : $daysLeft . ' days left' }}</div>
                                    <div class="small text-secondary">{{ $endDate->format('M d, Y') }}</div>
                                </td>
                                <td class="py-4">
                                    <span class="badge rounded-pill px-3 py-2" style="background: {{ $pos->status == 'active' ? 'rgba(255, 51, 51, 0.1)' : 'rgba(255,255,255,0.05)' }}; color: {{ $pos->status == 'active' ? '#ff3333' : '#6b7280' }}; font-size: 0.65rem;">
                                        {{ strtoupper($pos->status) }}
                                    </span>
                                </td>
                                <td class="py-4 text-end">
                                    @if($pos->status == 'active')
                                    <button onclick="unstake({{ $pos->id }})" class="btn btn-sm btn-outline-warning px-3" style="border-radius: 10px; font-size: 0.75rem;">
                                        {{ $isMatured ? 'Claim' : 'Unstake' }}
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@endsection

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

    // Dripping Profit Animation for Staking
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
                    if (display) {
                        display.innerHTML = `+$${accrued.toFixed(6)}`;
                    }
                });

                requestAnimationFrame(tickStakingProfit);
            }

            requestAnimationFrame(tickStakingProfit);
        }
    });
</script>
@endpush
