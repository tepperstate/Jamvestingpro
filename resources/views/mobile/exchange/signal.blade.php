@extends('layouts.user.app')
@section('title', 'Trading Signals')
@section('content')
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

<style>
.mobile-signals-container {
    padding: 15px;
    background: #0b0e14;
    min-height: 100vh;
    padding-bottom: 90px;
}
.page-title-mobile {
    font-size: 1.5rem;
    font-family: 'Outfit', sans-serif;
    font-weight: 800;
    color: #fff;
    margin-bottom: 5px;
}
.page-desc-mobile {
    font-size: 0.8rem;
    color: #94a3b8;
    line-height: 1.4;
    margin-bottom: 20px;
}
.stat-pill-scroll {
    display: flex;
    overflow-x: auto;
    gap: 10px;
    padding-bottom: 10px;
    margin-bottom: 15px;
}
.stat-pill-scroll::-webkit-scrollbar { display: none; }
.signal-stat-chip-mobile {
    background: rgba(16, 18, 27, 0.6);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 12px;
    padding: 8px 15px;
    white-space: nowrap;
}
.stat-label-m { font-size: 0.6rem; letter-spacing: 1px; color: #64748b; font-weight: 700; }
.stat-value-m { font-size: 1rem; font-weight: 800; font-family: 'Outfit', sans-serif; display: flex; align-items: center; gap: 6px; }

.signal-card-mobile {
    background: rgba(16, 18, 27, 0.7);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 20px;
    margin-bottom: 20px;
    overflow: hidden;
    position: relative;
}
.signal-card-mobile-header {
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}
.signal-icon-m {
    width: 44px; height: 44px;
    background: rgba(59, 130, 246, 0.1);
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    color: #60a5fa; font-size: 1.2rem;
}
.signal-icon-m img {
    width: 100%; height: 100%; border-radius: 12px; object-fit: cover;
}
.tier-badge-m {
    font-size: 0.65rem;
    font-weight: 800;
    letter-spacing: 0.5px;
    padding: 4px 10px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
}
.tier-pro { background: rgba(168, 85, 247, 0.12); color: #a855f7; border: 1px solid rgba(168, 85, 247, 0.2); }
.tier-premium { background: rgba(59, 130, 246, 0.12); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.2); }
.tier-starter { background: rgba(255, 51, 51, 0.12); color: #34d399; border: 1px solid rgba(255, 51, 51, 0.2); }

.signal-card-mobile-body {
    padding: 0 20px 15px;
}
.signal-stats-grid-m {
    display: flex;
    gap: 8px;
    margin-bottom: 15px;
}
.stat-box-m {
    flex: 1;
    background: rgba(0,0,0,0.3);
    border-radius: 10px;
    padding: 8px;
    text-align: center;
    border: 1px solid rgba(255,255,255,0.05);
}
.stat-box-m .tiny-label { font-size: 0.55rem; color: #64748b; font-weight: 700; margin-bottom: 2px; }
.stat-box-m .tiny-val { font-size: 0.8rem; font-weight: 800; }

.features-list-m {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    margin-bottom: 15px;
}
.feature-item-m {
    font-size: 0.75rem;
    color: #94a3b8;
    display: flex;
    align-items: center;
    gap: 5px;
}

.signal-card-mobile-footer {
    padding: 15px 20px;
    border-top: 1px solid rgba(255,255,255,0.05);
    background: rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.price-display-m {
    display: flex;
    align-items: baseline;
    gap: 2px;
}
.price-currency-m { font-size: 0.9rem; color: #64748b; font-weight: 600; }
.price-amount-m { font-size: 1.5rem; font-weight: 800; color: #fff; font-family: 'Outfit', sans-serif; }
.price-period-m { font-size: 0.7rem; color: #64748b; }

.btn-subscribe-m {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: #fff;
    font-weight: 700;
    border: none;
    border-radius: 10px;
    padding: 10px 15px;
    font-size: 0.85rem;
}

/* Modal */
.glass-modal-mobile {
    background: rgba(16, 18, 27, 0.95) !important;
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 20px 20px 0 0;
}
</style>

<div class="mobile-signals-container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="page-title-mobile">Trading Signals</h1>
        <a href="{{ route('signals.user') }}" class="btn btn-sm" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.3); border-radius: 8px;">
            <i class="ri-radar-line"></i> My Signals
        </a>
    </div>
    <p class="page-desc-mobile">AI-powered market intelligence. Subscribe to premium signal feeds for precise entries, exits, and risk management.</p>

    <div class="stat-pill-scroll">
        <div class="signal-stat-chip-mobile">
            <div class="stat-label-m">ACCURACY</div>
            <div class="stat-value-m text-success">
                <span style="width:6px;height:6px;border-radius:50%;background:#ff3333;"></span> 92.4%
            </div>
        </div>
        <div class="signal-stat-chip-mobile">
            <div class="stat-label-m">SUBSCRIBERS</div>
            <div class="stat-value-m text-info">{{ $data->sum('used') }}+</div>
        </div>
    </div>

    @foreach($data as $index => $signal)
    <div class="signal-card-mobile">
        <div class="signal-card-mobile-header">
            <span class="tier-badge-m @if($signal->amount >= 15000) tier-pro @elseif($signal->amount >= 5000) tier-premium @else tier-starter @endif">
                @if($signal->amount >= 15000) <i class="ri-vip-diamond-line me-1"></i>PRO
                @elseif($signal->amount >= 5000) <i class="ri-star-line me-1"></i>PREMIUM
                @else <i class="ri-flashlight-line me-1"></i>STARTER
                @endif
            </span>
            <div class="signal-icon-m">
                @if($signal->image)
                    <img src="{{ asset('storage/image/'.$signal->image) }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" alt="{{ $signal->name }}">
                    <div style="display:none;"><i class="ri-pulse-line"></i></div>
                @else
                    <div><i class="ri-pulse-line"></i></div>
                @endif
            </div>
        </div>

        <div class="signal-card-mobile-body">
            <h4 class="outfit font-weight-bold text-white mb-2">{{ $signal->name }}</h4>
            <p class="text-secondary mb-3" style="font-size: 0.75rem;">Leverages {{ $signal->day }} distinct technical indicators to identify optimal entry points.</p>

            <div class="signal-stats-grid-m">
                <div class="stat-box-m">
                    <div class="tiny-label">SUCCESS</div>
                    <div class="tiny-val text-success">92.4%</div>
                </div>
                <div class="stat-box-m">
                    <div class="tiny-label">RISK</div>
                    <div class="tiny-val text-warning">MED</div>
                </div>
                <div class="stat-box-m">
                    <div class="tiny-label">FREQ</div>
                    <div class="tiny-val text-info">DAILY</div>
                </div>
            </div>

            <div class="features-list-m">
                <div class="feature-item-m"><i class="ri-check-line text-success"></i> Real-time alerts</div>
                <div class="feature-item-m"><i class="ri-check-line text-success"></i> Entry & exit levels</div>
                <div class="feature-item-m"><i class="ri-check-line text-success"></i> Stop-loss included</div>
                <div class="feature-item-m"><i class="ri-check-line text-success"></i> Risk/reward ratio</div>
            </div>
        </div>

        <div class="signal-card-mobile-footer">
            <div class="price-display-m">
                <span class="price-currency-m">$</span>
                <span class="price-amount-m">{{ number_format($signal->amount) }}</span>
                <span class="price-period-m">/ lifetime</span>
            </div>
            <button class="btn-subscribe-m shadow-sm" onclick="openSubscribeModal('{{ $signal->id }}', '{{ $signal->name }}', '{{ number_format($signal->amount) }}', '{{ $signal->amount }}')">
                Subscribe <i class="ri-arrow-right-line ms-1"></i>
            </button>
        </div>
    </div>
    @endforeach
</div>

<!-- Subscribe Modal Mobile -->
<div class="modal fade" id="subscribeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered m-0" style="align-items: flex-end; min-height: 100%;">
        <div class="modal-content glass-modal-mobile text-white w-100">
            <div class="modal-body p-4 text-center">
                <div style="width: 60px; height: 60px; background: rgba(59, 130, 246, 0.1); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: #3b82f6; margin: 0 auto 15px;">
                    <i class="ri-radar-line"></i>
                </div>
                <h5 class="font-weight-bold mb-1">Confirm Subscription</h5>
                <p class="small text-secondary mb-4">You're about to unlock premium intelligence</p>

                <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-secondary border-opacity-25 mb-2">
                    <span class="text-secondary small">Signal Feed</span>
                    <span class="font-weight-bold text-white" id="modal-signal-name">—</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-secondary border-opacity-25 mb-3">
                    <span class="text-secondary small">Total Cost</span>
                    <span class="text-success h4 outfit font-weight-bold mb-0" id="modal-signal-price">$0</span>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button class="btn w-50 py-2" style="background: rgba(255,255,255,0.1); color: #fff; border-radius: 12px; font-weight: bold;" data-dismiss="modal">Cancel</button>
                    <button class="btn w-50 py-2" style="background: #3b82f6; color: #fff; border-radius: 12px; font-weight: bold;" id="confirmSubscribeBtn" onclick="confirmSubscribe()">Confirm</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        btn.innerHTML = '<i class="ri-loader-4-line fa-spin"></i> processing';

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
            }
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = 'Confirm';
        });
    }
</script>
@endsection
