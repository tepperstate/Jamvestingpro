@extends('layouts.user.app')
@section('title', 'Signal History')
@section('content')

<style>
.mobile-sh-container {
    padding: 15px;
    background: #0b0e14;
    min-height: 100vh;
    padding-bottom: 90px;
}
.page-title-m {
    font-size: 1.4rem;
    font-family: 'Outfit', sans-serif;
    font-weight: 800;
    color: #fff;
    margin-bottom: 0;
}
.sh-stats-scroll {
    display: flex;
    overflow-x: auto;
    gap: 10px;
    padding-bottom: 10px;
    margin-bottom: 15px;
}
.sh-stats-scroll::-webkit-scrollbar { display: none; }
.sh-stat-card-m {
    background: rgba(16, 18, 27, 0.6);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 16px;
    padding: 15px;
    min-width: 140px;
    display: flex;
    flex-direction: column;
}
.sh-stat-icon-m {
    width: 36px; height: 36px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
    margin-bottom: 10px;
}
.sh-label-m { font-size: 0.65rem; color: #64748b; font-weight: 600; text-transform: uppercase; margin-bottom: 4px; }
.sh-val-m { font-size: 1.2rem; font-weight: 800; color: #fff; font-family: 'Outfit', sans-serif; }

.sh-history-card {
    background: rgba(16, 18, 27, 0.6);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 20px;
    overflow: hidden;
}
.sh-history-header {
    padding: 15px;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    display: flex;
    align-items: center;
    gap: 10px;
}

/* List Items */
.sh-list-item {
    padding: 15px;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.sh-list-item:last-child { border-bottom: none; }
.sh-symbol {
    font-weight: 800;
    color: #fff;
    font-size: 0.9rem;
}
.sh-signal-name {
    font-size: 0.7rem;
    color: #94a3b8;
}
.sh-type-pill {
    font-size: 0.65rem;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 4px;
}
.type-buy { background: rgba(255, 51, 51, 0.1); color: #34d399; }
.type-sell { background: rgba(239, 68, 68, 0.1); color: #f87171; }
.sh-amount {
    font-size: 0.8rem;
    font-weight: bold;
    color: #fff;
}
.sh-profit {
    font-size: 0.85rem;
    font-weight: bold;
}
.sh-date {
    font-size: 0.6rem;
    color: #64748b;
    text-align: right;
}

.empty-state-m {
    text-align: center;
    padding: 50px 15px;
}
</style>

<div class="mobile-sh-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title-m">Signal History</h1>
        <a href="{{ route('signals.user') }}" class="btn btn-sm" style="background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px;">
            <i class="ri-radar-line"></i> Feeds
        </a>
    </div>

    <!-- Horizontal Stats -->
    <div class="sh-stats-scroll">
        <div class="sh-stat-card-m">
            <div class="sh-stat-icon-m" style="background: rgba(59, 130, 246, 0.12); color: #60a5fa;">
                <i class="ri-bar-chart-grouped-line"></i>
            </div>
            <div class="sh-label-m">Total Trades</div>
            <div class="sh-val-m">${{ number_format($trade) }}</div>
        </div>
        <div class="sh-stat-card-m">
            <div class="sh-stat-icon-m" style="background: rgba(168, 85, 247, 0.12); color: #a78bfa;">
                <i class="ri-calendar-check-line"></i>
            </div>
            <div class="sh-label-m">Today's Trades</div>
            <div class="sh-val-m">${{ number_format($today) }}</div>
        </div>
        <div class="sh-stat-card-m">
            <div class="sh-stat-icon-m" style="background: {{ $today_profit >= 0 ? 'rgba(255, 51, 51, 0.12)' : 'rgba(239, 68, 68, 0.12)' }}; color: {{ $today_profit >= 0 ? '#34d399' : '#f87171' }};">
                <i class="ri-funds-line"></i>
            </div>
            <div class="sh-label-m">Today's P&L</div>
            <div class="sh-val-m {{ $today_profit >= 0 ? 'text-success' : 'text-danger' }}">
                {{ $today_profit >= 0 ? '+' : '-' }}${{ number_format(abs($today_profit)) }}
            </div>
        </div>
    </div>

    <!-- History List -->
    <div class="sh-history-card">
        <div class="sh-history-header">
            <div style="width: 32px; height: 32px; background: rgba(59, 130, 246, 0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #60a5fa;">
                <i class="ri-list-ordered-2"></i>
            </div>
            <div style="font-weight: bold; color: #fff;">Live Signals</div>
        </div>

        @if(count($data) > 0)
            <div>
                @foreach ($data as $d)
                <div class="sh-list-item">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width: 36px; height: 36px; border-radius: 50%; background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; font-size: 1rem; color: {{ $d->status == 'win' ? '#34d399' : '#f87171' }};">
                            <i class="ri-arrow-{{ $d->status == 'win' ? 'right-up' : 'right-down' }}-line"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-1">
                                <span class="sh-symbol">{{ $d->symbol }}</span>
                                <span class="sh-type-pill {{ $d->type == 'Buy' ? 'type-buy' : 'type-sell' }}">{{ strtoupper($d->type) }}</span>
                            </div>
                            <div class="sh-signal-name">{{ Str::limit($d->name, 15) }}</div>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="sh-profit {{ $d->status == 'win' ? 'text-success' : 'text-danger' }}">
                            @if($d->status == 'win')
                                +${{ number_format($d->profit - $d->amount) }}
                            @else
                                -${{ number_format($d->amount) }}
                            @endif
                        </div>
                        <div class="sh-date">{{ \Carbon\Carbon::parse($d->created_at)->format('M d, H:i') }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="empty-state-m">
                <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.03); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #475569; margin: 0 auto 15px;">
                    <i class="ri-file-list-3-line"></i>
                </div>
                <h5 class="text-white mb-1">No Signal Activity</h5>
                <p class="text-secondary small mb-0">Your signal history will appear here.</p>
            </div>
        @endif
    </div>
</div>

@endsection
