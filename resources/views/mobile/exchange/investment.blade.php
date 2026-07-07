@extends('layouts.user.app')

@section('title', 'Crypto ETFs')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

<div class="mobile-container pb-5">
    <!-- Header -->
    <div class="mb-4 text-center px-3 mt-3">
        <h1 class="h3 font-weight-bold text-white mb-2" style="font-family: 'Outfit', sans-serif;">Crypto ETFs</h1>
        <p class="text-secondary small mb-3">Invest in top-performing Crypto ETFs.</p>
        <div class="glass-card-gold px-3 py-2 text-center mx-auto" style="max-width: 200px;">
            <div class="small text-gold text-uppercase tracking-wider" style="font-size: 0.7rem;">Active Yield</div>
            <div class="h4 mb-0 text-white font-weight-bold">14.8% APY</div>
        </div>
    </div>

    <!-- Crypto ETFs List -->
    <div class="px-3 mb-5">
        @foreach($investment as $plan)
        <div class="glass-card-gold mb-4 overflow-hidden position-relative" style="border-radius: 24px;">
            <div class="position-relative" style="height: 100px;">
                <img src="{{ url('storage/image/' . ($plan->image ?? 'default_investment.png')) }}" alt="{{ $plan->name }}" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.8;">
                <div class="position-absolute w-100 h-100 top-0 left-0" style="background: linear-gradient(to bottom, rgba(10,11,14,0) 0%, #0a0b0e 100%);"></div>
                <div class="position-absolute" style="bottom: -20px; left: 20px;">
                    <div class="bg-dark rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 48px; height: 48px; border: 2px solid #990000; background: #0a0b0e !important; padding: 4px;">
                        <img src="{{ $plan->logo }}" alt="{{ $plan->ticker }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                    </div>
                </div>
            </div>

            <div class="p-4 pt-4 mt-2">
                <div class="d-flex justify-content-between align-items-end mb-3">
                    <div>
                        <h4 class="font-weight-bold mb-0 text-white" style="font-family: 'Outfit', sans-serif;">{{ $plan->name }}</h4>
                        @if($plan->ticker)
                            <span class="badge" style="background: rgba(153, 0, 0, 0.1); color: #990000; font-size: 0.65rem;">{{ $plan->ticker }}</span>
                        @endif
                    </div>
                    <div class="text-right">
                        <span class="h3 font-weight-bold text-white number-font mb-0">${{ number_format($plan->amount) }}</span>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom" style="border-color: rgba(255,215,0,0.1) !important;">
                    <div>
                        <div class="small text-secondary" style="font-size: 0.7rem;">Duration</div>
                        <div class="text-white font-weight-bold">{{ $plan->day }} Days</div>
                    </div>
                    <div class="text-right">
                        <div class="small text-secondary" style="font-size: 0.7rem;">Min Deposit</div>
                        <div class="text-white font-weight-bold">${{ number_format($plan->min_deposit) }}</div>
                    </div>
                </div>

                <button class="btn btn-gold w-100 py-2 rounded-pill shadow-lg" onclick="investNow('{{ $plan->slug }}', '{{ addslashes($plan->name) }}', '{{ $plan->amount }}')" style="font-weight: 700;">
                    Allocate Capital
                </button>
            </div>
        </div>
        @endforeach
    </div>

    <!-- My ETF Portfolio -->
    @if($active_investments->count() > 0)
    <div class="px-3 mb-5">
        <h3 class="font-weight-bold text-white mb-3" style="font-family: 'Outfit', sans-serif;">My ETF Portfolio</h3>
        
        @foreach($active_investments as $active)
            @php
                $startDate = \Illuminate\Support\Carbon::parse($active->start_date);
                $endDate = \Illuminate\Support\Carbon::parse($active->end_date);
                $daysRemaining = max(0, now()->diffInDays($endDate, false));
                $totalDays = max(1, $active->day);
                $daysPassed = max(0, $startDate->diffInDays(now()));
                
                $initialAmount = floatval($active->amount);
                $currentValue = $active->current_value ? floatval($active->current_value) : $initialAmount;
                
                $totalHours = $totalDays * 24;
                $termMultiplier = 1 + (floatval($active->perc) / 100);
                $hourlyRate = pow($termMultiplier, 1 / $totalHours) - 1;
                $perSecondRate = pow(1 + $hourlyRate, 1 / 3600) - 1;

                $lastCredited = $active->last_credited_date ? \Illuminate\Support\Carbon::parse($active->last_credited_date) : $startDate;
                $secondsSinceCredit = max(0, now()->diffInSeconds($lastCredited));

                $liveCurrentValue = $currentValue * pow(1 + $perSecondRate, $secondsSinceCredit);
                $liveAccrued = $liveCurrentValue - $initialAmount;
                
                if ($active->status === 'completed') {
                    $liveAccrued = $currentValue - $initialAmount;
                    $perSecondRate = 0;
                    $daysRemaining = 0;
                    $daysPassed = $totalDays;
                }
            @endphp
            <div class="glass-card-gold mb-3 p-3 active-investment-row" data-principal="{{ $initialAmount }}" data-current-val="{{ $liveCurrentValue }}" data-rate="{{ $perSecondRate }}" data-status="{{ $active->status }}" style="border-radius: 20px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <img src="{{ $active->package->logo ?? \App\Services\AssetLogoService::getFallbackUrl('ETF') }}" style="width: 32px; height: 32px; border-radius: 50%;">
                        <div>
                            <div class="font-weight-bold text-white" style="font-size: 0.9rem;">{{ $active->plan_name ?? $active->name }}</div>
                            @if($active->package?->ticker)
                                <span class="badge" style="background: rgba(153, 0, 0, 0.1); color: #990000; font-size: 0.6rem;">{{ $active->package->ticker }}</span>
                            @endif
                        </div>
                    </div>
                    <span class="badge rounded-pill px-2 py-1" style="background: {{ $active->status == 'active' ? 'rgba(153, 0, 0, 0.1)' : 'rgba(255,255,255,0.05)' }}; color: {{ $active->status == 'active' ? '#990000' : '#6b7280' }}; font-size: 0.6rem;">
                        {{ strtoupper($active->status) }}
                    </span>
                </div>
                
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <div class="small text-secondary" style="font-size: 0.7rem;">Capital</div>
                        <div class="font-weight-bold text-white">${{ number_format($initialAmount, 2) }}</div>
                    </div>
                    <div class="text-right">
                        <div class="small text-secondary" style="font-size: 0.7rem;">Profit Accrued</div>
                        <div class="text-gold font-weight-bold live-profit-display number-font" style="color: #990000;">+${{ number_format($liveAccrued, 6) }}</div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="small text-secondary" style="font-size: 0.7rem;">Progress</div>
                    <div class="x-small text-gold timer-display" style="font-size: 0.65rem; color: #990000; font-weight: 700;">60s</div>
                </div>
                <div class="progress mb-2" style="height: 4px; background: rgba(255,255,255,0.05); border-radius: 2px;">
                    <div class="progress-bar" style="width: {{ ($daysPassed / $totalDays) * 100 }}%; background: #990000;"></div>
                </div>
                <div class="d-flex justify-content-between">
                    <div class="small text-secondary" style="font-size: 0.65rem;">{{ $daysRemaining }} Days Left</div>
                    <div class="small text-secondary" style="font-size: 0.65rem;">Matures {{ $endDate->format('M d, Y') }}</div>
                </div>
            </div>
        @endforeach
    </div>
    @endif
</div>

<style>
    body { background-color: #0a0b0e; color: #fff; }
    .text-gold { color: #990000 !important; }
    .bg-gold { background-color: #990000 !important; }
    .glass-card-gold {
        background: rgba(16, 18, 27, 0.6);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(153, 0, 0, 0.15);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }
    .btn-gold {
        background: linear-gradient(45deg, #990000, #f3e5ab);
        color: #0a0b0e;
        border: none;
        transition: transform 0.2s;
    }
    .btn-gold:active { transform: scale(0.95); }
    .number-font { font-variant-numeric: tabular-nums; }
</style>

@endsection

@push('js')
<script>
    function investNow(id, name, min) {
        let amount = prompt(`Enter investment amount for ${name} (Min: $${min}):`, min);
        if(amount && parseFloat(amount) >= parseFloat(min)) {
            fetch("{{ route('investment.post') }}", {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                body: JSON.stringify({id, amount})
            })
            .then(res => res.json())
            .then(data => {
                if(data.status) {
                    toastr.success(data.status);
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    toastr.error(data.error || 'Insufficient balance or logic error');
                }
            });
        }
    }

    // Dripping Profit Animation
    window.addEventListener('load', () => {
        const rows = document.querySelectorAll('.active-investment-row');
        if (rows.length > 0) {
            let secondsLeft = 60;
            
            function updateTimer() {
                secondsLeft--;
                
                rows.forEach(row => {
                    if (row.dataset.status === 'completed') return;
                    
                    let timerDisplay = row.querySelector('.timer-display');
                    if (timerDisplay) timerDisplay.innerText = secondsLeft + 's';
                });

                if (secondsLeft <= 0) {
                    secondsLeft = 60;
                    rows.forEach(row => {
                        if (row.dataset.status === 'completed') return;
                        
                        let principal = parseFloat(row.dataset.principal);
                        let currentVal = parseFloat(row.dataset.currentVal);
                        let ratePerSec = parseFloat(row.dataset.rate); 
                        let ratePerMin = Math.pow(1 + ratePerSec, 60) - 1;
                        
                        let oldAccrued = currentVal - principal;
                        currentVal = currentVal * (1 + ratePerMin);
                        row.dataset.currentVal = currentVal;
                        let newAccrued = currentVal - principal;
                        
                        let display = row.querySelector('.live-profit-display');
                        if (display) display.innerHTML = `+$${newAccrued.toFixed(6)}`;
                    });
                }
            }
            setInterval(updateTimer, 1000);
        }
    });
</script>
@endpush
