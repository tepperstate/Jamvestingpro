@extends('layouts.user.app')

@section('title', 'My Signals')

@section('content')
<style>
@media (max-width: 767.98px) {
    .mobile-cards-view {
        display: flex !important;
    }
}
</style>
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-5 gap-3">
        <div>
            <h1 class="h2 outfit font-weight-bold text-white mb-2">My Signals</h1>
            <p class="text-secondary small mb-0">Your active signal subscriptions and intelligence feeds.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="active-badge">
                <span class="active-dot"></span>
                {{ count($data) }} Active {{ Str::plural('Feed', count($data)) }}
            </div>
            <a href="{{ route('signal') }}" class="btn btn-outline-premium px-4 py-2">
                <i class="ri-add-line me-1"></i> Browse Signals
            </a>
        </div>
    </div>

    @if(count($data) > 0)
    <!-- Desktop Table View -->
    <div class="d-none d-md-block mb-4">
        <div class="table-responsive glass-card p-0 overflow-hidden" style="border-radius: 24px; background: rgba(16, 18, 27, 0.6); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.05);">
            <table class="table table-hover align-middle mb-0 text-white table-borderless" style="background: transparent;">
                <thead style="background: rgba(0,0,0,0.4); border-bottom: 1px solid rgba(255,255,255,0.05);">
                    <tr>
                        <th class="px-4 py-4 text-secondary small text-uppercase tracking-wide">Signal Name</th>
                        <th class="py-4 text-secondary small text-uppercase tracking-wide">Status</th>
                        <th class="py-4 text-secondary small text-uppercase tracking-wide">Investment</th>
                        <th class="py-4 text-secondary small text-uppercase tracking-wide">Accuracy</th>
                        <th class="py-4 text-secondary small text-uppercase tracking-wide">Frequency</th>
                        <th class="px-4 py-4 text-end text-secondary small text-uppercase tracking-wide">Action</th>
                    </tr>
                </thead>
                <tbody style="border-top: none;">
                    @foreach ($data as $d)
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="my-signal-avatar" style="width: 40px; height: 40px; font-size: 1.2rem;">
                                    <i class="ri-pulse-line"></i>
                                </div>
                                <div>
                                    <h6 class="outfit font-weight-bold text-white mb-0">{{ $d->name }}</h6>
                                    <span class="text-secondary" style="font-size: 0.75rem;">Lifetime access</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-3">
                            <span class="status-pill status-active"><span class="status-dot-sm"></span> Active</span>
                        </td>
                        <td class="py-3 outfit fw-bold">${{ number_format($d->amount) }}</td>
                        <td class="py-3 text-success fw-bold">92.4%</td>
                        <td class="py-3 text-info fw-bold">Daily</td>
                        <td class="px-4 py-3 text-end">
                            <a href="{{ route('signals.user-history') }}" class="btn btn-signal-history px-3 py-2">
                                <i class="ri-history-line"></i> History
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile Cards View -->
    <div class="row g-4 mobile-cards-view d-md-none">
        @foreach ($data as $d)
        <div class="col-xl-4 col-md-6">
            <div class="my-signal-card">
                <!-- Card Header -->
                <div class="my-signal-header">
                    <div class="my-signal-avatar">
                        <i class="ri-pulse-line"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="outfit font-weight-bold text-white mb-1">{{ $d->name }}</h5>
                        <div class="d-flex align-items-center gap-2">
                            <span class="status-pill status-active"><span class="status-dot-sm"></span> Active</span>
                            <span class="text-secondary small">Lifetime access</span>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="my-signal-stats">
                    <div class="ms-stat">
                        <div class="ms-stat-label">Investment</div>
                        <div class="ms-stat-value">${{ number_format($d->amount) }}</div>
                    </div>
                    <div class="ms-stat">
                        <div class="ms-stat-label">Accuracy</div>
                        <div class="ms-stat-value text-success">92.4%</div>
                    </div>
                    <div class="ms-stat">
                        <div class="ms-stat-label">Frequency</div>
                        <div class="ms-stat-value text-info">Daily</div>
                    </div>
                </div>

                <!-- Features -->
                <div class="my-signal-features">
                    <div class="msf-item"><i class="ri-check-line text-success"></i> Real-time entry alerts</div>
                    <div class="msf-item"><i class="ri-check-line text-success"></i> Take profit targets</div>
                    <div class="msf-item"><i class="ri-check-line text-success"></i> Stop-loss levels</div>
                    <div class="msf-item"><i class="ri-check-line text-success"></i> Risk/reward analysis</div>
                </div>

                <!-- Actions -->
                <div class="my-signal-actions">
                    <a href="{{ route('signals.user-history') }}" class="btn btn-signal-history w-100">
                        <i class="ri-history-line me-2"></i> View Signal History
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @else
    <!-- Empty State -->
    <div class="empty-signals-state">
        <div class="empty-icon-box">
            <i class="ri-radar-line"></i>
        </div>
        <h3 class="outfit font-weight-bold text-white mb-3">No Active Signals</h3>
        <p class="text-secondary mb-4" style="max-width: 440px;">You haven't subscribed to any trading signals yet. Browse our premium signal feeds to get started with AI-powered market intelligence.</p>
        <a href="{{ route('signal') }}" class="btn btn-subscribe px-5 py-3">
            <i class="ri-radar-line me-2"></i> Explore Signals
        </a>
    </div>
    @endif

    <!-- Signal Performance Summary (only show if user has signals) -->
    @if(count($data) > 0)
    <div class="row g-4 mt-4">
        <div class="col-lg-4">
            <div class="perf-card">
                <div class="perf-icon bg-success-glow"><i class="ri-line-chart-line"></i></div>
                <div>
                    <div class="perf-label">Total Invested</div>
                    <div class="perf-value">${{ number_format($data->sum('amount')) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="perf-card">
                <div class="perf-icon bg-info-glow"><i class="ri-radar-line"></i></div>
                <div>
                    <div class="perf-label">Active Feeds</div>
                    <div class="perf-value">{{ count($data) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="perf-card">
                <div class="perf-icon bg-warning-glow"><i class="ri-shield-star-line"></i></div>
                <div>
                    <div class="perf-label">Avg. Accuracy</div>
                    <div class="perf-value text-success">92.4%</div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
    /* My Signal Card */
    .my-signal-card {
        background: rgba(16, 18, 27, 0.6);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 24px;
        padding: 28px;
        transition: all 0.35s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .my-signal-card:hover {
        border-color: rgba(59, 130, 246, 0.2);
        transform: translateY(-4px);
        box-shadow: 0 16px 48px rgba(0,0,0,0.3);
    }

    .my-signal-header {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 24px;
    }

    .my-signal-avatar {
        width: 52px; height: 52px;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(168, 85, 247, 0.1));
        border: 1px solid rgba(59, 130, 246, 0.2);
        border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem; color: #60a5fa;
        flex-shrink: 0;
    }

    /* Status Pill */
    .status-pill {
        font-size: 0.7rem;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        letter-spacing: 0.3px;
    }
    .status-active {
        background: rgba(255, 51, 51, 0.12);
        color: #34d399;
        border: 1px solid rgba(255, 51, 51, 0.2);
    }
    .status-dot-sm {
        width: 6px; height: 6px;
        background: #34d399;
        border-radius: 50%;
        animation: dot-pulse 2s infinite;
    }
    @keyframes dot-pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.4; }
    }

    /* Stats */
    .my-signal-stats {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
    }
    .ms-stat {
        flex: 1;
        padding: 14px;
        background: rgba(0,0,0,0.25);
        border: 1px solid rgba(255,255,255,0.04);
        border-radius: 14px;
        text-align: center;
    }
    .ms-stat-label { font-size: 0.6rem; letter-spacing: 0.5px; color: #64748b; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; }
    .ms-stat-value { font-size: 0.95rem; font-weight: 800; color: #fff; font-family: 'Outfit', sans-serif; }

    /* Features */
    .my-signal-features {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        margin-bottom: 24px;
        flex-grow: 1;
    }
    .msf-item {
        font-size: 0.78rem;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .msf-item i { font-size: 0.7rem; }

    /* Action Button */
    .btn-signal-history {
        background: rgba(59, 130, 246, 0.1);
        border: 1px solid rgba(59, 130, 246, 0.2);
        color: #60a5fa;
        border-radius: 14px;
        padding: 12px;
        font-weight: 700;
        font-size: 0.85rem;
        transition: all 0.3s;
    }
    .btn-signal-history:hover {
        background: rgba(59, 130, 246, 0.2);
        color: #93c5fd;
        transform: translateY(-2px);
    }

    /* Header Badge */
    .active-badge {
        background: rgba(255, 51, 51, 0.1);
        border: 1px solid rgba(255, 51, 51, 0.2);
        color: #34d399;
        padding: 8px 18px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .active-dot {
        width: 8px; height: 8px;
        background: #34d399;
        border-radius: 50%;
        animation: dot-pulse 2s infinite;
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

    /* Empty State */
    .empty-signals-state {
        text-align: center;
        padding: 80px 20px;
    }
    .empty-icon-box {
        width: 96px; height: 96px;
        background: rgba(59, 130, 246, 0.08);
        border: 1px solid rgba(59, 130, 246, 0.15);
        border-radius: 28px;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 24px;
        font-size: 2.5rem; color: #60a5fa;
    }
    .btn-subscribe {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        border: none;
        border-radius: 14px;
        font-weight: 700;
        transition: all 0.3s;
    }
    .btn-subscribe:hover {
        background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
    }

    /* Performance Cards */
    .perf-card {
        background: rgba(16, 18, 27, 0.4);
        border: 1px solid rgba(255,255,255,0.05);
        border-radius: 20px;
        padding: 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.3s;
    }
    .perf-card:hover { border-color: rgba(255,255,255,0.08); }
    .perf-icon {
        width: 48px; height: 48px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem; flex-shrink: 0;
    }
    .bg-success-glow { background: rgba(255, 51, 51, 0.12); color: #34d399; }
    .bg-info-glow { background: rgba(6, 182, 212, 0.12); color: #22d3ee; }
    .bg-warning-glow { background: rgba(245, 158, 11, 0.12); color: #fbbf24; }
    .perf-label { font-size: 0.75rem; color: #64748b; font-weight: 600; }
    .perf-value { font-size: 1.3rem; font-weight: 800; color: #fff; font-family: 'Outfit', sans-serif; }

    @media (max-width: 768px) {
        .my-signal-stats { flex-direction: column; }
        .my-signal-features { grid-template-columns: 1fr; }
    }
</style>
@endsection
