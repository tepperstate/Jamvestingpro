@extends('layouts.user.app')
@section('title', 'Bot Trading History')
@section('content')

<style>
.mobile-bot-history-container {
    padding: 15px;
    background: #0b0e14;
    min-height: 100vh;
    padding-bottom: 90px;
}
.stat-card-mobile {
    background: rgba(16, 18, 27, 0.5);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 215, 0, 0.1);
    border-radius: 16px;
    padding: 15px;
    margin-bottom: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}
.stat-value {
    font-size: 1.5rem;
    font-weight: bold;
    font-family: 'Outfit', sans-serif;
}
.nav-pills-mobile {
    display: flex;
    background: rgba(255,255,255,0.05);
    border-radius: 12px;
    padding: 5px;
    margin-bottom: 20px;
    overflow-x: auto;
}
.nav-pills-mobile .nav-item {
    flex: 1;
}
.nav-pills-mobile .nav-link {
    color: #94a3b8;
    text-align: center;
    border-radius: 8px;
    padding: 8px 5px;
    font-size: 0.75rem;
    font-weight: 600;
    white-space: nowrap;
}
.nav-pills-mobile .nav-link.active {
    background: linear-gradient(135deg, #ffd700, #ffaa00);
    color: #000 !important;
}
.ledger-card-mobile {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 215, 0, 0.1);
    border-radius: 16px;
    padding: 15px;
    margin-bottom: 12px;
}
.search-input-glass {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,215,0,0.2);
    color: #fff;
    border-radius: 12px;
}
.search-input-glass:focus {
    background: rgba(255,255,255,0.08);
    border-color: #ffd700;
    color: #fff;
    box-shadow: none;
}
.gold-text { color: #ffd700; }
</style>

<div class="mobile-bot-history-container">
    <h4 class="text-white font-weight-bold mb-3" style="font-family: 'Outfit', sans-serif;">Bot Performance</h4>

    <!-- Stats Grid -->
    <div class="row g-2 mb-4">
        <div class="col-6">
            <div class="stat-card-mobile text-center">
                <div class="small text-secondary mb-1" style="font-size: 0.7rem;">NET YIELD</div>
                <div class="stat-value {{ $win >= 0 ? 'text-success' : 'text-danger' }}">
                    ${{ number_format($win ?? 0, 2) }}
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="stat-card-mobile text-center">
                <div class="small text-secondary mb-1" style="font-size: 0.7rem;">DRAWDOWN</div>
                <div class="stat-value text-danger">
                    -${{ number_format($loss ?? 0, 2) }}
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="stat-card-mobile text-center">
                <div class="small text-secondary mb-1" style="font-size: 0.7rem;">TRADES</div>
                <div class="stat-value gold-text">{{ $orderCount ?? 0 }}</div>
            </div>
        </div>
        <div class="col-6">
            <div class="stat-card-mobile text-center">
                <div class="small text-secondary mb-1" style="font-size: 0.7rem;">WIN RATE</div>
                <div class="stat-value text-info">
                    @php 
                        $winCount = $transactions->where('status', 'win')->count();
                        $rate = $orderCount > 0 ? ($winCount / $orderCount) * 100 : 0;
                    @endphp
                    {{ number_format($rate, 1) }}%
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav-pills-mobile">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('trades.history') ? 'active' : '' }}" href="{{ route('trades.history') }}">MANUAL</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('copy-trading.history') ? 'active' : '' }}" href="{{ route('copy-trading.history') }}">
                COPY
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('bots.history') ? 'active' : '' }}" href="{{ auth()->user()->hasFeature('bot_trading') ? route('bots.history') : route('user.upgrade') }}">
                BOT @unless(auth()->user()->hasFeature('bot_trading'))<i class="ri-lock-line ms-1"></i>@endunless
            </a>
        </li>
    </ul>

    <!-- Search -->
    <div class="input-group mb-4">
        <span class="input-group-text bg-transparent border-end-0 border-secondary"><i class="ri-search-line text-secondary"></i></span>
        <input type="text" id="ledger-search-mobile" class="form-control search-input-glass border-start-0" placeholder="Search history...">
    </div>

    <!-- Ledger List -->
    <div id="ledger-list-mobile">
        @forelse($transactions as $t)
        <div class="ledger-card-mobile ledger-item">
            @php
                $botSymbol = !empty($t->symbol) && $t->symbol !== 'N/A' ? $t->symbol : 'BOT';
                $botName = !empty($t->name) && $t->name !== 'N/A' ? $t->name : 'AI Bot';
                $status = strtolower($t->status);
                $statusClass = $status === 'win' ? 'text-success' : ($status === 'loss' ? 'text-danger' : 'text-warning');
                $statusIcon = $status === 'win' ? 'checkbox-circle' : ($status === 'loss' ? 'close-circle' : 'time-line');
            @endphp
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom" style="border-color: rgba(255,255,255,0.05) !important;">
                <div class="d-flex align-items-center gap-2">
                    <x-asset-logo :symbol="$botSymbol" size="sm" />
                    <div>
                        <div class="text-white fw-bold name-target">{{ $botName }}</div>
                        <div class="text-secondary" style="font-size: 0.65rem;">{{ \Carbon\Carbon::parse($t->created_at)->format('d M y H:i') }}</div>
                    </div>
                </div>
                <span class="badge" style="background: rgba(255,215,0,0.1); color: #ffd700; border: 1px solid rgba(255,215,0,0.2); font-size: 0.6rem;">AUTO</span>
            </div>
            
            <div class="row g-2 mb-2">
                <div class="col-6">
                    <div class="text-secondary" style="font-size: 0.7rem;">Exposure</div>
                    <div class="text-white fw-bold">${{ number_format($t->amount, 2) }}</div>
                </div>
                <div class="col-6 text-end">
                    <div class="text-secondary" style="font-size: 0.7rem;">Resulting P/L</div>
                    <div class="fw-bold {{ $status === 'win' ? 'text-success' : ($status === 'loss' ? 'text-danger' : 'text-white') }}">
                        {{ $status === 'win' ? '+' : ($status === 'loss' ? '-' : '') }}${{ number_format(abs($t->profit ?? 0), 2) }}
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-2">
                <div class="text-secondary" style="font-size: 0.7rem;">Status</div>
                <div class="d-flex align-items-center gap-1 {{ $statusClass }}">
                    <i class="ri-{{ $statusIcon }}-line" style="font-size: 0.9rem;"></i>
                    <span class="fw-bold small text-uppercase" style="font-size: 0.7rem;">{{ $status }}</span>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-5">
            <i class="ri-bar-chart-2-line text-secondary" style="font-size: 3rem; opacity: 0.5;"></i>
            <p class="text-secondary small mt-3">Your bot history is empty.</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-center mobile-pagination">
        {{ $transactions->links() }}
    </div>
</div>

<script>
    document.getElementById('ledger-search-mobile').addEventListener('keyup', function() {
        let val = this.value.toLowerCase();
        let items = document.querySelectorAll('.ledger-item');
        items.forEach(item => {
            let text = item.querySelector('.name-target').innerText.toLowerCase();
            item.style.display = text.includes(val) ? '' : 'none';
        });
    });
</script>
@endsection
