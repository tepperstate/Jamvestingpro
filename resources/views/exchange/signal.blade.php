@extends('layouts.user.app')

@section('title', 'Trading Signals')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-5" data-aos="fade-down">
        <div class="col-xl-7">
            <h1 class="h2 outfit font-weight-bold text-white mb-2">Trading Signals</h1>
            <p class="text-secondary small mb-0">AI-powered market intelligence. Subscribe to premium signal feeds for precise entries, exits, and risk management.</p>
        </div>
        <div class="col-xl-5 text-xl-end d-flex align-items-center justify-content-xl-end gap-3 mt-3 mt-xl-0">
            <div class="signal-stat-chip">
                <div class="stat-label">ACCURACY</div>
                <div class="stat-value text-success">
                    <span class="status-dot-pulse bg-success"></span>
                    92.4%
                </div>
            </div>
            <div class="signal-stat-chip">
                <div class="stat-label">SUBSCRIBERS</div>
                <div class="stat-value text-info">{{ $data->sum('used') }}+</div>
            </div>
            <a href="{{ route('signals.user') }}" class="btn btn-outline-premium px-4 py-2">
                <i class="ri-radar-line me-2"></i> My Signals
            </a>
        </div>
    </div>

    <!-- Signals Feed -->
    <div class="row g-4">
        @foreach($data as $index => $signal)
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="signal-card h-100">
                <!-- Tier Badge + Icon -->
                <div class="signal-card-header">
                    <span class="tier-badge @if($signal->amount >= 15000) tier-pro @elseif($signal->amount >= 5000) tier-premium @else tier-starter @endif">
                        @if($signal->amount >= 15000) <i class="ri-vip-diamond-line me-1"></i>PRO
                        @elseif($signal->amount >= 5000) <i class="ri-star-line me-1"></i>PREMIUM
                        @else <i class="ri-flashlight-line me-1"></i>STARTER
                        @endif
                    </span>
                    <div class="signal-icon">
                        @if($signal->image)
                            <img src="{{ asset('storage/image/'.$signal->image) }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" alt="{{ $signal->name }}" style="width:48px; height:48px; border-radius:16px; object-fit:cover;">
                            <div class="signal-icon-fallback" style="display:none;"><i class="ri-pulse-line"></i></div>
                        @else
                            <div class="signal-icon-fallback"><i class="ri-pulse-line"></i></div>
                        @endif
                    </div>
                </div>

                <!-- Signal Info -->
                <div class="signal-card-body">
                    <h3 class="outfit font-weight-bold mb-2 text-white">{{ $signal->name }}</h3>
                    <p class="text-secondary small mb-4 lh-base">Leverages {{ $signal->day }} distinct technical indicators to identify optimal entry points in real-time volatility.</p>

                    <!-- Stats Row -->
                    <div class="signal-stats-row">
                        <div class="signal-stat">
                            <div class="stat-icon bg-success-glow"><i class="ri-check-double-line"></i></div>
                            <div>
                                <div class="stat-tiny">SUCCESS</div>
                                <div class="stat-num text-success">92.4%</div>
                            </div>
                        </div>
                        <div class="signal-stat">
                            <div class="stat-icon bg-warning-glow"><i class="ri-shield-line"></i></div>
                            <div>
                                <div class="stat-tiny">RISK</div>
                                <div class="stat-num text-warning">MEDIUM</div>
                            </div>
                        </div>
                        <div class="signal-stat">
                            <div class="stat-icon bg-info-glow"><i class="ri-time-line"></i></div>
                            <div>
                                <div class="stat-tiny">FREQ</div>
                                <div class="stat-num text-info">DAILY</div>
                            </div>
                        </div>
                    </div>

                    <!-- Features -->
                    <div class="signal-features">
                        <div class="feature-item"><i class="ri-check-line text-success"></i> Real-time alerts</div>
                        <div class="feature-item"><i class="ri-check-line text-success"></i> Entry & exit levels</div>
                        <div class="feature-item"><i class="ri-check-line text-success"></i> Stop-loss included</div>
                        <div class="feature-item"><i class="ri-check-line text-success"></i> Risk/reward ratio</div>
                    </div>
                </div>

                <!-- Price + CTA -->
                <div class="signal-card-footer">
                    <div class="signal-price">
                        <span class="price-currency">$</span>
                        <span class="price-amount">{{ number_format($signal->amount) }}</span>
                        <span class="price-period">/ lifetime</span>
                    </div>
                    <button class="btn btn-subscribe w-100" onclick="openSubscribeModal('{{ $signal->id }}', '{{ $signal->name }}', '{{ number_format($signal->amount) }}', '{{ $signal->amount }}')">
                        <i class="ri-radar-line me-2"></i> Subscribe Now
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Info Cards -->
    <div class="row g-4 mt-4">
        <div class="col-lg-4">
            <div class="info-glass-card">
                <div class="info-icon bg-info-glow"><i class="ri-lightbulb-flash-line"></i></div>
                <h6 class="outfit font-weight-bold mb-2 text-white">How It Works</h6>
                <p class="text-secondary small mb-0">Signals are delivered directly to your dashboard with precise Entry Price, Take Profit, and Stop Loss levels powered by AI analytics.</p>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="info-glass-card">
                <div class="info-icon bg-success-glow"><i class="ri-bar-chart-grouped-line"></i></div>
                <h6 class="outfit font-weight-bold mb-2 text-white">Track Record</h6>
                <p class="text-secondary small mb-0">Our signal algorithms maintain a 92.4% accuracy rate across 50+ markets including forex, crypto, and major indices.</p>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="info-glass-card">
                <div class="info-icon bg-warning-glow"><i class="ri-shield-check-line"></i></div>
                <h6 class="outfit font-weight-bold mb-2 text-white">Risk Warning</h6>
                <p class="text-secondary small mb-0">Trading involves significant risk. Past performance is not indicative of future results. Never trade with money you cannot afford to lose.</p>
            </div>
        </div>
    </div>
</div>

<!-- Subscribe Modal (replaces ugly confirm() dialog) -->
<div class="modal fade" id="subscribeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content subscribe-modal-content">
            <div class="modal-body p-0">
                <div class="subscribe-modal-header">
                    <div class="subscribe-modal-icon">
                        <i class="ri-radar-line"></i>
                    </div>
                    <h4 class="outfit font-weight-bold text-white mb-1">Confirm Subscription</h4>
                    <p class="text-secondary small mb-0">You're about to unlock premium intelligence</p>
                </div>
                <div class="subscribe-modal-body">
                    <div class="subscribe-detail-row">
                        <span class="text-secondary">Signal Feed</span>
                        <span class="text-white font-weight-bold" id="modal-signal-name">—</span>
                    </div>
                    <div class="subscribe-detail-row">
                        <span class="text-secondary">Access Type</span>
                        <span class="text-info font-weight-bold">Lifetime</span>
                    </div>
                    <div class="subscribe-detail-row border-0">
                        <span class="text-secondary">Total Cost</span>
                        <span class="text-success h4 outfit font-weight-bold mb-0" id="modal-signal-price">$0</span>
                    </div>

                    <div class="subscribe-balance-note">
                        <i class="ri-information-line text-info me-2"></i>
                        <span class="small text-secondary">Amount will be deducted from your available balance.</span>
                    </div>
                </div>
                <div class="subscribe-modal-footer">
                    <button class="btn btn-outline-light btn-lg flex-grow-1" data-dismiss="modal" onclick="$('#subscribeModal').modal('hide')">
                        Cancel
                    </button>
                    <button class="btn btn-subscribe btn-lg flex-grow-1" id="confirmSubscribeBtn" onclick="confirmSubscribe()">
                        <i class="ri-check-line me-1"></i> Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Signal Cards */
    .signal-card {
        background: rgba(16, 18, 27, 0.6);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 24px;
        padding: 0;
        display: flex;
        flex-direction: column;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }
    .signal-card:hover {
        transform: translateY(-8px);
        border-color: rgba(59, 130, 246, 0.3);
        box-shadow: 0 20px 60px rgba(0,0,0,0.4), 0 0 30px rgba(59, 130, 246, 0.08);
    }

    .signal-card-header {
        padding: 24px 24px 0;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .signal-card-body {
        padding: 20px 24px;
        flex-grow: 1;
    }

    .signal-card-footer {
        padding: 0 24px 24px;
        border-top: 1px solid rgba(255,255,255,0.04);
        padding-top: 20px;
        margin-top: auto;
    }

    /* Tier Badges */
    .tier-badge {
        font-size: 0.7rem;
        font-weight: 800;
        letter-spacing: 0.8px;
        padding: 6px 14px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
    }
    .tier-pro { background: rgba(168, 85, 247, 0.12); color: #a855f7; border: 1px solid rgba(168, 85, 247, 0.2); }
    .tier-premium { background: rgba(59, 130, 246, 0.12); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.2); }
    .tier-starter { background: rgba(255, 51, 51, 0.12); color: #34d399; border: 1px solid rgba(255, 51, 51, 0.2); }

    .signal-icon { width: 48px; height: 48px; }
    .signal-icon-fallback {
        width: 48px; height: 48px;
        background: rgba(59, 130, 246, 0.1);
        border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        color: #60a5fa; font-size: 1.5rem;
    }

    /* Stats */
    .signal-stats-row {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
    }
    .signal-stat {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 12px;
        background: rgba(0,0,0,0.25);
        border: 1px solid rgba(255,255,255,0.04);
        border-radius: 14px;
    }
    .stat-icon {
        width: 32px; height: 32px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.9rem; flex-shrink: 0;
    }
    .bg-success-glow { background: rgba(255, 51, 51, 0.12); color: #34d399; }
    .bg-warning-glow { background: rgba(245, 158, 11, 0.12); color: #fbbf24; }
    .bg-info-glow { background: rgba(6, 182, 212, 0.12); color: #22d3ee; }
    .stat-tiny { font-size: 0.55rem; letter-spacing: 0.5px; color: #64748b; font-weight: 700; }
    .stat-num { font-size: 0.75rem; font-weight: 800; }

    /* Features */
    .signal-features {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 6px 12px;
        margin-bottom: 4px;
    }
    .feature-item {
        font-size: 0.78rem;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .feature-item i { font-size: 0.7rem; }

    /* Price */
    .signal-price {
        text-align: center;
        margin-bottom: 16px;
        display: flex;
        align-items: baseline;
        justify-content: center;
        gap: 4px;
    }
    .price-currency { font-size: 1.2rem; color: #64748b; font-weight: 600; }
    .price-amount { font-size: 2.2rem; font-weight: 800; color: #fff; font-family: 'Outfit', sans-serif; }
    .price-period { font-size: 0.85rem; color: #64748b; margin-left: 4px; }

    /* Subscribe Button */
    .btn-subscribe {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        border: none;
        border-radius: 14px;
        padding: 14px 24px;
        font-weight: 700;
        font-size: 0.9rem;
        letter-spacing: 0.3px;
        transition: all 0.3s;
    }
    .btn-subscribe:hover {
        background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
    }

    /* Header Chips */
    .signal-stat-chip {
        background: rgba(16, 18, 27, 0.6);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 14px;
        padding: 10px 18px;
    }
    .signal-stat-chip .stat-label { font-size: 0.6rem; letter-spacing: 1px; color: #64748b; font-weight: 700; }
    .signal-stat-chip .stat-value { font-size: 1.1rem; font-weight: 800; font-family: 'Outfit', sans-serif; display: flex; align-items: center; gap: 6px; }
    .status-dot-pulse { width: 8px; height: 8px; border-radius: 50%; animation: dot-pulse 2s infinite; }
    @keyframes dot-pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.4); }
    }

    .btn-outline-premium {
        border: 1px solid rgba(59, 130, 246, 0.4);
        color: #60a5fa;
        border-radius: 14px;
        font-weight: 700;
        transition: all 0.3s;
    }
    .btn-outline-premium:hover {
        background: rgba(59, 130, 246, 0.1);
        color: white;
        border-color: #3b82f6;
    }

    /* Info Cards */
    .info-glass-card {
        background: rgba(16, 18, 27, 0.4);
        border: 1px solid rgba(255,255,255,0.05);
        border-radius: 20px;
        padding: 24px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        transition: all 0.3s;
    }
    .info-glass-card:hover { border-color: rgba(255,255,255,0.08); }
    .info-icon { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }

    /* Subscribe Modal */
    .subscribe-modal-content {
        background: #0f1219 !important;
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 28px;
        overflow: hidden;
    }
    .subscribe-modal-header {
        text-align: center;
        padding: 32px 32px 20px;
        background: linear-gradient(180deg, rgba(59, 130, 246, 0.06) 0%, transparent 100%);
    }
    .subscribe-modal-icon {
        width: 64px; height: 64px;
        background: rgba(59, 130, 246, 0.12);
        border: 1px solid rgba(59, 130, 246, 0.2);
        border-radius: 20px;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 16px;
        font-size: 1.8rem; color: #60a5fa;
    }
    .subscribe-modal-body { padding: 0 32px 24px; }
    .subscribe-detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 0;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }
    .subscribe-balance-note {
        display: flex;
        align-items: center;
        padding: 14px 16px;
        background: rgba(6, 182, 212, 0.06);
        border: 1px solid rgba(6, 182, 212, 0.1);
        border-radius: 14px;
        margin-top: 16px;
    }
    .subscribe-modal-footer {
        display: flex;
        gap: 12px;
        padding: 0 32px 32px;
    }
    .subscribe-modal-footer .btn { border-radius: 14px; font-weight: 700; padding: 14px; }

    @media (max-width: 768px) {
        .signal-stats-row { flex-direction: column; }
        .signal-features { grid-template-columns: 1fr; }
    }
</style>

@endsection

@push('js')
<script>
    let pendingSignal = {};

    function openSubscribeModal(id, name, displayAmount, rawAmount) {
        pendingSignal = { id, name, rawAmount };
        document.getElementById('modal-signal-name').textContent = name;
        document.getElementById('modal-signal-price').textContent = '$' + displayAmount;
        $('#subscribeModal').modal('show');
    }

    function confirmSubscribe() {
        const btn = document.getElementById('confirmSubscribeBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';

        const formData = new FormData();
        formData.append('id', pendingSignal.id);
        formData.append('name', pendingSignal.name);
        formData.append('amount', pendingSignal.rawAmount);
        formData.append('_token', '{{ csrf_token() }}');

        fetch("{{ route('signals.buy') }}", {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            $('#subscribeModal').modal('hide');
            if (data.status) {
                toastr.success(data.status);
                setTimeout(() => window.location.href = "{{ route('signals.user') }}", 1500);
            } else if (data.error) {
                toastr.error(data.error);
            } else {
                toastr.error('An unexpected error occurred');
            }
        })
        .catch(err => {
            console.log(err);
            toastr.error('Failed to process request');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-check-line me-1"></i> Confirm';
        });
    }
</script>
@endpush
