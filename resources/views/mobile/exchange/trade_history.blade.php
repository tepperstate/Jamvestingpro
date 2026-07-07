@extends('layouts.user.app')

@section('title', 'Investing History')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
<style>
    /* Mobile-first, dark theme, glassmorphism, gold accents */
    :root {
        --gold-primary: #FFD700;
        --gold-secondary: #DAA520;
        --gold-glow: rgba(255, 215, 0, 0.2);
        --bg-dark: #0a0b10;
        --glass-bg: rgba(18, 20, 28, 0.65);
        --glass-border: rgba(255, 215, 0, 0.15);
        --text-muted: #8b92a5;
    }

    body {
        background-color: var(--bg-dark);
        color: #ffffff;
    }

    .mobile-container {
        padding: 1rem;
        padding-bottom: 5rem;
        max-width: 100%;
        overflow-x: hidden;
    }

    .glass-card {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 16px;
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
    }

    .gold-text {
        background: linear-gradient(135deg, var(--gold-primary), var(--gold-secondary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .gold-border {
        border-color: rgba(255, 215, 0, 0.3) !important;
    }

    .stats-scroll {
        display: flex;
        overflow-x: auto;
        gap: 1rem;
        padding-bottom: 0.5rem;
        scrollbar-width: none;
    }
    
    .stats-scroll::-webkit-scrollbar {
        display: none;
    }

    .stat-card {
        min-width: 140px;
        flex: 0 0 auto;
        padding: 1.25rem;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; height: 2px;
        background: linear-gradient(90deg, transparent, var(--gold-primary), transparent);
        opacity: 0.5;
    }

    .nav-tabs-mobile {
        display: flex;
        background: rgba(0,0,0,0.3);
        border-radius: 12px;
        padding: 4px;
        margin-bottom: 1.5rem;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .nav-tabs-mobile .nav-item {
        flex: 1;
        text-align: center;
    }

    .nav-tabs-mobile .nav-link {
        color: var(--text-muted);
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        padding: 0.5rem;
        transition: all 0.3s ease;
        border: none;
    }

    .nav-tabs-mobile .nav-link.active {
        background: linear-gradient(135deg, var(--gold-primary), var(--gold-secondary));
        color: #000 !important;
        box-shadow: 0 4px 12px var(--gold-glow);
    }

    .premium-input-group {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 12px;
        overflow: hidden;
    }

    .premium-input-group input {
        background: transparent;
        border: none;
        color: #fff;
        padding: 0.75rem;
        box-shadow: none !important;
    }
    
    .premium-input-group input:focus {
        background: transparent;
        color: #fff;
    }

    .premium-input-group input::placeholder {
        color: var(--text-muted);
    }

    .premium-input-group .input-group-text {
        background: transparent;
        border: none;
        color: var(--gold-primary);
    }

    .trade-card {
        transition: transform 0.2s;
        margin-bottom: 1rem;
    }

    .trade-card:active {
        transform: scale(0.98);
    }

    .trade-status-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .bg-success-glass { background: rgba(255, 51, 51, 0.15); color: #ff3333; border: 1px solid rgba(255, 51, 51, 0.3); }
    .bg-danger-glass { background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }
    .bg-warning-glass { background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); }

    .badge-long { background: rgba(255, 51, 51, 0.15); color: #ff3333; border: 1px solid rgba(255, 51, 51, 0.3); }
    .badge-short { background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }

    .env-badge {
        background: linear-gradient(135deg, rgba(255,215,0,0.1), rgba(218,165,32,0.1));
        border: 1px solid var(--gold-primary);
        color: var(--gold-primary);
        font-size: 0.65rem;
        border-radius: 20px;
        padding: 4px 10px;
        font-weight: 700;
        letter-spacing: 1px;
    }
    
    .action-button {
        background: linear-gradient(135deg, rgba(255,255,255,0.05), rgba(255,255,255,0.01));
        border: 1px solid rgba(255,255,255,0.1);
        color: #fff;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s;
    }

    .action-button:hover {
        background: rgba(255,255,255,0.1);
        color: var(--gold-primary);
    }
</style>

<div class="mobile-container">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="outfit font-weight-bold mb-1 gold-text">Trade History</h4>
            <div class="text-secondary small">Your investing ledger</div>
        </div>
        <div class="env-badge">
            <i class="ri-database-2-line me-1"></i> {{ auth()->user()->is_demo ? 'DEMO' : 'REAL' }}
        </div>
    </div>

    <!-- Analysis Summary (Horizontal Scroll) -->
    <div class="stats-scroll mb-4">
        <div class="glass-card stat-card">
            <div class="small text-muted text-uppercase tracking-wider mb-2" style="font-size: 0.65rem;">Total Yield</div>
            <div class="h3 outfit font-weight-bold mb-0 {{ $win >= 0 ? 'text-success' : 'text-danger' }}">
                ${{ number_format($win ?? 0, 2) }}
            </div>
        </div>
        
        <div class="glass-card stat-card">
            <div class="small text-muted text-uppercase tracking-wider mb-2" style="font-size: 0.65rem;">Max Drawdown</div>
            <div class="h3 outfit font-weight-bold mb-0 text-danger">
                -${{ number_format($loss ?? 0, 2) }}
            </div>
        </div>
        
        <div class="glass-card stat-card">
            <div class="small text-muted text-uppercase tracking-wider mb-2" style="font-size: 0.65rem;">Trades</div>
            <div class="h3 outfit font-weight-bold mb-0 gold-text">
                {{ $orderCount ?? 0 }}
            </div>
        </div>
        
        <div class="glass-card stat-card">
            <div class="small text-muted text-uppercase tracking-wider mb-2" style="font-size: 0.65rem;">Win Rate</div>
            <div class="h3 outfit font-weight-bold mb-0 text-primary">
                @php 
                    $winCount = $transactions->where('status', 'win')->count();
                    $rate = $orderCount > 0 ? ($winCount / $orderCount) * 100 : 0;
                @endphp
                {{ number_format($rate, 1) }}%
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav-tabs-mobile list-unstyled" id="history-tabs">
        <li class="nav-item">
            <a class="d-block nav-link {{ request()->routeIs('trades.history') ? 'active' : '' }}" href="{{ route('trades.history') }}">MANUAL</a>
        </li>
        <li class="nav-item">
            <a class="d-block nav-link {{ request()->routeIs('copy-trading.history') ? 'active' : '' }}" href="{{ route('copy-trading.history') }}">
                COPY
            </a>
        </li>
        <li class="nav-item">
            <a class="d-block nav-link {{ request()->routeIs('bots.history') ? 'active' : '' }}" href="{{ auth()->user()->hasFeature('bot_trading') ? route('bots.history') : route('user.upgrade') }}">
                BOT @unless(auth()->user()->hasFeature('bot_trading'))<i class="ri-lock-line ms-1"></i>@endunless
            </a>
        </li>
    </ul>

    <!-- Actions: Search & PDF -->
    <div class="d-flex gap-2 mb-4">
        <div class="input-group premium-input-group flex-grow-1">
            <span class="input-group-text"><i class="ri-search-line"></i></span>
            <input type="text" id="ledger-search" class="form-control" placeholder="Search asset...">
        </div>
        <a href="{{ route('export.trade_history') }}" class="action-button flex-shrink-0">
            <i class="ri-file-pdf-2-line gold-text" style="font-size: 1.2rem;"></i>
        </a>
    </div>

    <!-- Trades List -->
    <div class="ledger-list">
        @forelse($transactions as $t)
        @php
            $assetSymbol = !empty($t->symbol) && $t->symbol !== 'N/A' ? $t->symbol : (!empty($t->asset->symbols) && $t->asset->symbols !== 'N/A' ? $t->asset->symbols : 'Unknown Asset');
            $exchangeName = !empty($t->exchanges->name) ? $t->exchanges->name : 'Market';
            $isBuy = strtolower($t->type ?? 'buy') === 'buy' || strtolower($t->types ?? 'buy') === 'buy';
            $status = strtolower($t->status);
            $statusClass = $status === 'win' ? 'text-success' : ($status === 'loss' ? 'text-danger' : 'text-warning');
            $statusBgClass = $status === 'win' ? 'bg-success-glass' : ($status === 'loss' ? 'bg-danger-glass' : 'bg-warning-glass');
            $statusIcon = $status === 'win' ? 'arrow-up-line' : ($status === 'loss' ? 'arrow-down-line' : 'time-line');
        @endphp
        <div class="glass-card trade-card p-3 ledger-mobile-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="trade-status-icon {{ $statusBgClass }}">
                        <i class="ri-{{ $statusIcon }}"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <x-asset-logo :symbol="$assetSymbol" size="sm" />
                            <div class="text-white fw-bold outfit fs-5 lh-1 ledger-search-target">{{ $assetSymbol }}</div>
                        </div>
                        <div class="text-muted" style="font-size: 0.7rem;">{{ \Carbon\Carbon::parse($t->created_at)->format('d M, Y • H:i') }}</div>
                    </div>
                </div>
                <div class="text-end">
                    <div class="fw-bold fs-5 lh-1 {{ $status === 'win' ? 'text-success' : ($status === 'loss' ? 'text-danger' : 'text-white') }}">
                        {{ $status === 'win' ? '+' : ($status === 'loss' ? '-' : '') }}${{ number_format(abs($t->p_l ?? 0), 2) }}
                    </div>
                    <div class="badge {{ $isBuy ? 'badge-long' : 'badge-short' }} mt-1" style="font-size: 0.65rem;">
                        {{ strtoupper($isBuy ? 'LONG' : 'SHORT') }}
                    </div>
                </div>
            </div>
            
            <div class="row g-0 pt-3 border-top gold-border" style="border-top-style: dashed !important;">
                <div class="col-4">
                    <div class="text-muted mb-1" style="font-size: 0.65rem; text-transform: uppercase;">Exposure</div>
                    <div class="text-white fw-bold text-truncate" style="font-size: 0.85rem;">${{ number_format($t->amount, 2) }}</div>
                </div>
                <div class="col-4 text-center border-start border-end gold-border" style="border-style: dashed !important;">
                    <div class="text-muted mb-1" style="font-size: 0.65rem; text-transform: uppercase;">Volume</div>
                    <div class="text-white fw-bold text-truncate" style="font-size: 0.85rem;">{{ number_format($t->amount / ($t->unit ?? 1), 4) }}</div>
                </div>
                <div class="col-4 text-end">
                    <div class="text-muted mb-1" style="font-size: 0.65rem; text-transform: uppercase;">Leverage</div>
                    <div class="gold-text fw-bold" style="font-size: 0.85rem;">×{{ $t->leverage ?? '1' }}</div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-5 glass-card">
            <div class="gold-text mb-3">
                <i class="ri-bar-chart-2-line" style="font-size: 3rem;"></i>
            </div>
            <div class="text-white fw-bold mb-1">No Trades Found</div>
            <div class="text-muted small">Your trading history is currently empty.</div>
        </div>
        @endforelse
    </div>

    <div class="mt-4 d-flex justify-content-center pagination-gold">
        {{ $transactions->links() }}
    </div>
</div>

<style>
    .pagination-gold .pagination {
        gap: 5px;
    }
    .pagination-gold .page-link {
        background: var(--glass-bg) !important;
        border: 1px solid var(--glass-border) !important;
        color: var(--text-muted) !important;
        border-radius: 8px !important;
        padding: 8px 14px;
        backdrop-filter: blur(10px);
    }
    .pagination-gold .page-item.active .page-link {
        background: linear-gradient(135deg, var(--gold-primary), var(--gold-secondary)) !important;
        color: #000 !important;
        border-color: transparent !important;
        font-weight: bold;
    }
</style>

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
