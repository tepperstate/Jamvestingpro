@extends('layouts.user.app')

@section('title', 'Stock ETFs')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-5" data-aos="fade-up">
        <div class="col-xl-9">
            <h1 class="h2 outfit font-weight-bold text-white mb-2">Stock ETFs</h1>
            <p class="text-secondary small">Invest in top-performing Stock ETFs. Set it and forget it.</p>
        </div>
        <div class="col-xl-3 text-xl-end d-flex align-items-center justify-content-xl-end">
            <div class="glass-card px-4 py-3 text-center shadow-lg" style="background: rgba(16, 18, 27, 0.4); backdrop-filter: blur(16px); border-radius: 20px; border: 1px solid rgba(255,255,255,0.05);">
                <div class="small text-secondary text-uppercase tracking-wider">Active Yield</div>
                <div class="h3 mb-0 text-success outfit font-weight-bold">14.8% APY</div>
            </div>
        </div>
    </div>

    <!-- Stock ETFs Grid -->
    <div class="row g-4 mb-5">
        @foreach($investment as $plan)
        <div class="col-xl-4 col-lg-4 col-md-6 mb-4">
            <div class="glass-card-premium h-100">
                <div class="card-header-img-wrapper" style="height: 120px;">
                    <img src="{{ url('storage/image/' . ($plan->image ?? 'default_investment.png')) }}" alt="{{ $plan->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                    <div class="card-header-overlay"></div>
                </div>

                <div class="card-body-premium pt-0">
                    <div class="package-icon-overlay" style="margin-top: -30px; margin-bottom: 20px; position: relative; z-index: 5;">
                        <div class="bg-dark rounded-circle d-flex align-items-center justify-content-center shadow-lg border border-primary border-opacity-25 overflow-hidden" style="width: 60px; height: 60px; background: #0a0b0e !important;">
                            <img src="{{ $plan->logo }}" alt="{{ $plan->ticker }}" style="width: 100%; height: 100%; object-fit: cover; background: white; padding: 5px;">
                        </div>
                    </div>

                    <div class="d-flex align-items-center mb-1">
                        <h3 class="outfit font-weight-bold mb-0 text-white">{{ $plan->name }}</h3>
                        @if($plan->ticker)
                            <span class="badge badge-primary ms-2" style="font-size: 0.7rem;">{{ $plan->ticker }}</span>
                        @endif
                    </div>
                    <div class="price-tag mb-4">
                        <span class="h2 font-weight-bold text-white number-font">${{ number_format($plan->amount) }}</span>
                        <span class="text-secondary small">/ capital</span>
                    </div>

                    <div class="row g-0 mb-4 py-3 border-top border-bottom text-center" style="border-color: var(--glass-border) !important;">
                        <div class="col-12">
                            <div class="small text-secondary mb-1">Duration</div>
                            <div class="h4 text-white font-weight-bold mb-0">{{ $plan->day }} Days</div>
                        </div>
                    </div>

                    <ul class="list-unstyled mb-4 flex-grow-1">
                        <li class="d-flex align-items-center gap-3 mb-2 small text-secondary">
                            <i class="ri-checkbox-circle-fill text-success"></i>
                            Minimum Entry: <span class="text-white fw-bold number-font">${{ number_format($plan->amount) }}</span>
                        </li>
                        <li class="d-flex align-items-center gap-3 mb-2 small text-secondary">
                            <i class="ri-checkbox-circle-fill text-success"></i>
                            Min Deposit: <span class="text-white fw-bold">${{ number_format($plan->min_deposit) }}</span>
                        </li>
                    </ul>

                    <button class="btn btn-premium w-100 py-3 shadow-lg" onclick="investNow('{{ $plan->slug }}', '{{ addslashes($plan->name) }}', '{{ $plan->amount }}')" style="font-weight: 800;">
                        Allocate Capital
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- My Growth Portfolio -->
    @if($active_investments->count() > 0)
    <div class="row mb-5" data-aos="fade-up">
        <div class="col-12">
            <h2 class="outfit font-weight-bold text-white mb-4">My Stock Portfolio</h2>
            <div class="glass-card satin-border overflow-hidden">
                <div class="table-responsive">
                    <table class="table text-white mb-0">
                        <thead style="background: rgba(255,255,255,0.02);">
                            <tr>
                                <th class="border-0 small text-secondary py-3">PROTOCOL</th>
                                <th class="border-0 small text-secondary py-3">CAPITAL</th>
                                <th class="border-0 small text-secondary py-3">PROFIT ACCRUED</th>
                                <th class="border-0 small text-secondary py-3">TIME REMAINING</th>
                                <th class="border-0 small text-secondary py-3">STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
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

                                    // Fast-forward to exactly right now
                                    $liveCurrentValue = $currentValue * pow(1 + $perSecondRate, $secondsSinceCredit);
                                    $liveAccrued = $liveCurrentValue - $initialAmount;
                                    
                                    if ($active->status === 'completed') {
                                        $liveAccrued = $currentValue - $initialAmount;
                                        $perSecondRate = 0;
                                        $daysRemaining = 0;
                                        $daysPassed = $totalDays;
                                    }
                                @endphp
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);" class="active-investment-row" data-principal="{{ $initialAmount }}" data-current-val="{{ $liveCurrentValue }}" data-rate="{{ $perSecondRate }}" data-status="{{ $active->status }}">
                                    <td class="py-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle bg-white d-flex align-items-center justify-content-center overflow-hidden" style="width: 32px; height: 32px; padding: 2px;">
                                                <img src="{{ $active->package->logo ?? \App\Services\AssetLogoService::getFallbackUrl('Stock') }}" style="width: 100%; height: 100%; object-fit: cover;">
                                            </div>
                                            <div>
                                                <div class="font-weight-bold d-flex align-items-center gap-2">
                                                    {{ $active->plan_name ?? $active->name }}
                                                    @if($active->package?->ticker)
                                                        <span class="badge badge-primary px-2" style="font-size: 0.6rem;">{{ $active->package->ticker }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4">
                                        <div class="font-weight-bold">${{ number_format($initialAmount, 2) }}</div>
                                    </td>
                                    <td class="py-4">
                                        <div class="text-success font-weight-bold live-profit-display number-font" style="text-shadow: 0 0 10px rgba(255, 51, 51, 0.4);">+${{ number_format($liveAccrued, 6) }}</div>
                                        <div class="d-flex align-items-center gap-2 mt-1" style="width: 120px;">
                                            <div class="progress flex-grow-1" style="height: 4px; background: rgba(255,255,255,0.05);">
                                                <div class="progress-bar bg-success" style="width: {{ ($daysPassed / $totalDays) * 100 }}%"></div>
                                            </div>
                                            <div class="x-small text-warning timer-display" style="min-width: 25px; font-weight: 800;">60s</div>
                                        </div>
                                    </td>
                                    <td class="py-4">
                                        <div class="small text-white">{{ $daysRemaining }} Days</div>
                                        <div class="x-small text-secondary">Mature on {{ $endDate->format('M d, Y') }}</div>
                                    </td>
                                    <td class="py-4">
                                        <span class="badge rounded-pill px-3 py-2" style="background: {{ $active->status == 'active' ? 'rgba(255, 51, 51, 0.1)' : 'rgba(255,255,255,0.05)' }}; color: {{ $active->status == 'active' ? '#ff3333' : '#6b7280' }}; font-size: 0.65rem;">
                                            {{ strtoupper($active->status) }}
                                        </span>
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

    <!-- Portfolio Stats -->
    <div class="row">
        <div class="col-12">
            <div class="bento-card p-4 d-flex flex-wrap gap-5 align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <i class="ri-shield-check-line text-success h3 mb-0"></i>
                    <div>
                        <div class="small text-secondary">Principal Protected</div>
                        <div class="font-weight-bold">100% Security</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <i class="ri-flashlight-line text-warning h3 mb-0"></i>
                    <div>
                        <div class="small text-secondary">Payout Frequency</div>
                        <div class="font-weight-bold">Every 24 Hours</div>
                    </div>
                </div>
                <div class="flex-grow-1 text-end">
                    <p class="text-secondary small mb-0 mt-2">All Stock positions are locked until maturity. Early withdrawal penalties apply.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .investment-card { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); border: 1px solid rgba(255,255,255,0.05); }
    .investment-card:hover { transform: translateY(-15px); border-color: #3b82f6; background: rgba(59, 130, 246, 0.05) !important; box-shadow: 0 20px 40px rgba(0,0,0,0.4); }
    
    .x-small { font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.05em; }
    
    .card-footer-glow { height: 4px; background: linear-gradient(90deg, transparent, #3b82f6, transparent); opacity: 0; transition: 0.3s; }
    .investment-card:hover .card-footer-glow { opacity: 1; }
    
    .bento-card { background: rgba(16, 18, 27, 0.4); backdrop-filter: blur(16px); border-radius: 20px; border: 1px solid rgba(255,255,255,0.05); }
</style>

@endsection

@push('js')
<script>
    function investNow(id, name, min) {
        let amount = prompt(`Enter investment amount for ${name} (Min: $${min}):`, min);
        if(amount && parseFloat(amount) >= parseFloat(min)) {
            fetch("{{ route('user.stock_etfs.post') }}", {
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

    // Staggered Entrance Animation
    window.addEventListener('load', () => {
        if (typeof anime !== 'undefined') {
            anime({
                targets: '.glass-card-premium, .investment-card, .bento-card, .active-investment-row',
                translateY: [60, 0],
                opacity: [0, 1],
                delay: anime.stagger(150),
                easing: 'easeOutSpring(1, 80, 10, 0)',
                duration: 1500
            });
            
            anime({
                targets: '.container-fluid > .row:first-child',
                opacity: [0, 1],
                translateY: [-20, 0],
                duration: 800,
                easing: 'easeOutExpo'
            });
        }

        // Dripping Profit Animation every minute using Motion One
        const rows = document.querySelectorAll('.active-investment-row');
        if (rows.length > 0) {
            let secondsLeft = 60;
            
            function updateTimer() {
                secondsLeft--;
                
                rows.forEach(row => {
                    if (row.dataset.status === 'completed') return;
                    
                    let timerDisplay = row.querySelector('.timer-display');
                    if (timerDisplay) {
                        timerDisplay.innerText = secondsLeft + 's';
                    }
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
                        if (display && window.Motion) {
                            // Scale pop
                            Motion.animate(display, { scale: [1, 1.05, 1], color: ['#ff3333', '#34d399', '#ff3333'] }, { duration: 0.5 });
                            
                            // Number count up
                            Motion.animate(oldAccrued, newAccrued, {
                                duration: 1,
                                onUpdate: (latest) => {
                                    display.innerHTML = `+$${latest.toFixed(6)}`;
                                }
                            });
                        } else if (display) {
                            display.innerHTML = `+$${newAccrued.toFixed(6)}`;
                        }
                    });
                }
            }

            setInterval(updateTimer, 1000);
        }
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/motion@10.16.2/dist/motion.global.js"></script>
@endpush
