@extends('layouts.user.app')
@section('title', 'My Signals')
@section('content')

<style>
.mobile-mysignals-container {
    padding: 15px;
    background: #0b0e14;
    min-height: 100vh;
    padding-bottom: 90px;
}
.page-header-m {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
.page-title-m {
    font-size: 1.4rem;
    font-family: 'Outfit', sans-serif;
    font-weight: 800;
    color: #fff;
    margin-bottom: 0;
}
.active-badge-m {
    background: rgba(255, 51, 51, 0.1);
    border: 1px solid rgba(255, 51, 51, 0.2);
    color: #34d399;
    padding: 6px 12px;
    border-radius: 10px;
    font-size: 0.75rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 5px;
}
.active-dot-m {
    width: 6px; height: 6px;
    background: #34d399;
    border-radius: 50%;
    animation: dot-pulse 2s infinite;
}

.my-signal-card-m {
    background: rgba(16, 18, 27, 0.6);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 20px;
    padding: 20px;
    margin-bottom: 15px;
}
.my-signal-header-m {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    margin-bottom: 15px;
}
.my-signal-avatar-m {
    width: 48px; height: 48px;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(168, 85, 247, 0.1));
    border: 1px solid rgba(59, 130, 246, 0.2);
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; color: #60a5fa;
    flex-shrink: 0;
}
.status-pill-m {
    font-size: 0.65rem;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    background: rgba(255, 51, 51, 0.12);
    color: #34d399;
    border: 1px solid rgba(255, 51, 51, 0.2);
}

.ms-stats-m {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
}
.ms-stat-box {
    flex: 1;
    background: rgba(0,0,0,0.25);
    border: 1px solid rgba(255,255,255,0.04);
    border-radius: 12px;
    padding: 10px;
    text-align: center;
}
.ms-stat-box .lbl { font-size: 0.6rem; color: #64748b; font-weight: 700; text-transform: uppercase; margin-bottom: 2px; }
.ms-stat-box .val { font-size: 0.9rem; font-weight: 800; color: #fff; font-family: 'Outfit', sans-serif; }

.btn-history-m {
    background: rgba(59, 130, 246, 0.1);
    border: 1px solid rgba(59, 130, 246, 0.2);
    color: #60a5fa;
    border-radius: 12px;
    padding: 12px;
    font-weight: 700;
    font-size: 0.85rem;
    width: 100%;
    display: block;
    text-align: center;
    text-decoration: none;
}

.empty-state-m {
    text-align: center;
    padding: 50px 15px;
}
</style>

<div class="mobile-mysignals-container">
    <div class="page-header-m">
        <h1 class="page-title-m">My Signals</h1>
        <div class="active-badge-m">
            <span class="active-dot-m"></span>
            {{ count($data) }} Active
        </div>
    </div>
    
    <div class="d-flex mb-4">
        <a href="{{ route('signal') }}" class="btn btn-sm w-100" style="background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,255,255,0.1); border-radius: 10px;">
            <i class="ri-add-line me-1"></i> Browse More Signals
        </a>
    </div>

    @if(count($data) > 0)
        @foreach($data as $d)
        <div class="my-signal-card-m">
            <div class="my-signal-header-m">
                <div class="my-signal-avatar-m"><i class="ri-pulse-line"></i></div>
                <div class="flex-grow-1">
                    <h5 class="outfit font-weight-bold text-white mb-1">{{ $d->name }}</h5>
                    <div class="d-flex align-items-center gap-2">
                        <span class="status-pill-m"><span class="active-dot-m me-1"></span> Active</span>
                        <span class="text-secondary" style="font-size:0.7rem;">Lifetime</span>
                    </div>
                </div>
            </div>

            <div class="ms-stats-m">
                <div class="ms-stat-box">
                    <div class="lbl">Investment</div>
                    <div class="val">${{ number_format($d->amount) }}</div>
                </div>
                <div class="ms-stat-box">
                    <div class="lbl">Accuracy</div>
                    <div class="val text-success">92.4%</div>
                </div>
                <div class="ms-stat-box">
                    <div class="lbl">Freq</div>
                    <div class="val text-info">Daily</div>
                </div>
            </div>

            <a href="{{ route('signals.user-history') }}" class="btn-history-m">
                <i class="ri-history-line me-1"></i> View Signal History
            </a>
        </div>
        @endforeach
    @else
        <div class="empty-state-m">
            <div style="width: 70px; height: 70px; background: rgba(59, 130, 246, 0.1); border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: #60a5fa; margin: 0 auto 20px;">
                <i class="ri-radar-line"></i>
            </div>
            <h4 class="outfit font-weight-bold text-white mb-2">No Active Signals</h4>
            <p class="text-secondary small mb-4">Subscribe to premium signal feeds for precise market intelligence.</p>
            <a href="{{ route('signal') }}" class="btn py-2 px-4" style="background: #3b82f6; color:#fff; border-radius: 12px; font-weight: bold;">
                Explore Signals
            </a>
        </div>
    @endif
</div>

@endsection
