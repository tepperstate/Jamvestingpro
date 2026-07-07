@extends('layouts.user.app')
@section('title', 'Copy Trading History')
@section('content')

<style>
.mobile-copy-history-container {
    padding: 15px;
    background: #0b0e14;
    min-height: 100vh;
    padding-bottom: 90px;
}
.page-title-m {
    font-size: 1.5rem;
    font-family: 'Outfit', sans-serif;
    font-weight: 800;
    color: #fff;
    margin-bottom: 20px;
}

/* Stats */
.ch-stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 20px;
}
.ch-stat-box {
    background: rgba(16, 18, 27, 0.7);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 16px;
    padding: 15px;
    text-align: center;
}
.ch-stat-lbl { font-size: 0.6rem; color: #64748b; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; }
.ch-stat-val { font-size: 1.2rem; font-weight: 800; color: #fff; font-family: 'Outfit', sans-serif; }

/* Filter Tabs */
.ch-tabs-m {
    display: flex;
    background: rgba(0,0,0,0.3);
    border-radius: 12px;
    padding: 4px;
    margin-bottom: 15px;
    border: 1px solid rgba(255,255,255,0.05);
}
.ch-tab-m {
    flex: 1;
    text-align: center;
    padding: 8px;
    border-radius: 8px;
    font-size: 0.7rem;
    font-weight: 700;
    color: #64748b;
    text-decoration: none;
    transition: all 0.2s;
}
.ch-tab-m.active {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: #fff;
}

/* Search */
.search-wrapper-m {
    position: relative;
    margin-bottom: 20px;
}
.search-wrapper-m i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #64748b;
}
.search-input-m {
    width: 100%;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 12px;
    padding: 10px 10px 10px 35px;
    color: #fff;
    font-size: 0.85rem;
}

/* Cards */
.ch-card-m {
    background: rgba(16, 18, 27, 0.6);
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 16px;
    padding: 15px;
    margin-bottom: 12px;
}
.ch-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    padding-bottom: 10px;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}
.ch-icon {
    width: 36px; height: 36px;
    background: rgba(14, 165, 233, 0.1);
    color: #0ea5e9;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
}
.ch-trader-name { font-weight: 800; font-family: 'Outfit', sans-serif; color: #fff; }
.ch-date { font-size: 0.65rem; color: #64748b; }

.ch-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 10px;
}
.ch-info-lbl { font-size: 0.65rem; color: #64748b; margin-bottom: 2px; }
.ch-info-val { font-size: 0.9rem; font-weight: bold; color: #fff; }

.ch-status-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 8px;
}
.status-pill {
    font-size: 0.65rem;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 6px;
    text-transform: uppercase;
}
.status-success { background: rgba(255, 51, 51, 0.1); color: #34d399; }
.status-warning { background: rgba(245, 158, 11, 0.1); color: #fbbf24; }
.status-danger { background: rgba(239, 68, 68, 0.1); color: #f87171; }
</style>

<div class="mobile-copy-history-container">
    <h1 class="page-title-m">Copy Portfolio</h1>

    <div class="ch-stats-grid">
        <div class="ch-stat-box">
            <div class="ch-stat-lbl">Capital Allocated</div>
            <div class="ch-stat-val">${{ number_format($totalCapital ?? 0, 2) }}</div>
        </div>
        <div class="ch-stat-box">
            <div class="ch-stat-lbl">Net Profit</div>
            <div class="ch-stat-val {{ ($totalPL ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                {{ ($totalPL ?? 0) >= 0 ? '+' : '' }}${{ number_format($totalPL ?? 0, 2) }}
            </div>
        </div>
        <div class="ch-stat-box">
            <div class="ch-stat-lbl">Active Copies</div>
            <div class="ch-stat-val text-warning">{{ $activeOrders->count() ?? 0 }}</div>
        </div>
        <div class="ch-stat-box">
            <div class="ch-stat-lbl">Win Rate</div>
            <div class="ch-stat-val text-primary">
                @php
                    $winCount = isset($orders) ? $orders->where('profit', '>', 0)->count() : 0;
                    $orderCount = isset($orders) ? $orders->count() : 0;
                    $rate = $orderCount > 0 ? ($winCount / $orderCount) * 100 : 0;
                @endphp
                {{ number_format($rate, 1) }}%
            </div>
        </div>
    </div>

    <div class="ch-tabs-m">
        <a href="{{ route('trades.history') }}" class="ch-tab-m {{ request()->routeIs('trades.history') ? 'active' : '' }}">MANUAL</a>
        <a href="{{ route('copy-trading.history') }}" class="ch-tab-m {{ request()->routeIs('copy-trading.history') ? 'active' : '' }}">COPY</a>
        <a href="{{ auth()->user()->hasFeature('bot_trading') ? route('bots.history') : route('user.upgrade') }}" class="ch-tab-m {{ request()->routeIs('bots.history') ? 'active' : '' }}">BOT</a>
    </div>

    <div class="search-wrapper-m">
        <i class="ri-search-line"></i>
        <input type="text" id="ledger-search" class="search-input-m" placeholder="Search parameters...">
    </div>

    <div id="ledger-list">
        @forelse($orders as $t)
        <div class="ch-card-m ledger-mobile-card">
            @php
                $status = strtolower($t->status);
                $statusClass = $status === 'completed' || $status === 'closed' ? 'status-success' : ($status === 'pending' || $status === 'running' || $status === 'active' ? 'status-warning' : 'status-danger');
            @endphp
            <div class="ch-card-header">
                <div class="d-flex align-items-center gap-2">
                    <div class="ch-icon"><i class="ri-user-star-line"></i></div>
                    <div>
                        <div class="ch-trader-name">{{ $t->trader_name ?? 'Expert Trader' }}</div>
                        <div class="ch-date">{{ \Carbon\Carbon::parse($t->created_at)->format('d M, Y H:i') }}</div>
                    </div>
                </div>
            </div>
            
            <div class="ch-grid-2">
                <div>
                    <div class="ch-info-lbl">Capital</div>
                    <div class="ch-info-val">${{ number_format($t->amount, 2) }}</div>
                </div>
                <div class="text-end">
                    <div class="ch-info-lbl">Current Profit</div>
                    <div class="ch-info-val {{ ($t->profit ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ ($t->profit ?? 0) >= 0 ? '+' : '' }}${{ number_format($t->profit ?? 0, 2) }}
                    </div>
                </div>
            </div>

            <div class="ch-status-row">
                <div class="ch-info-lbl mb-0">Status</div>
                <div class="status-pill {{ $statusClass }}">{{ $status }}</div>
            </div>
        </div>
        @empty
        <div class="text-center py-5">
            <div style="font-size: 3rem; color: rgba(255,255,255,0.1); margin-bottom: 10px;">
                <i class="ri-file-copy-line"></i>
            </div>
            <p class="text-secondary small">Your copy trading history is empty.</p>
        </div>
        @endforelse
    </div>

    @if(isset($orders) && method_exists($orders, 'links'))
    <div class="d-flex justify-content-center mt-4">
        {{ $orders->links('pagination::bootstrap-4') }}
    </div>
    @endif
</div>

<script>
    document.getElementById('ledger-search').onkeyup = function() {
        let val = this.value.toLowerCase();
        let cards = document.querySelectorAll('.ledger-mobile-card');
        cards.forEach(card => {
            card.style.display = card.innerText.toLowerCase().includes(val) ? '' : 'none';
        });
    };
</script>

@endsection
