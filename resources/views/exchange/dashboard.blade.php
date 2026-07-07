@extends('layouts.user.app')

@section('title', 'Home')

@section('content')
<style>
    .bento-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        grid-auto-rows: minmax(150px, auto);
        gap: 20px;
        padding-bottom: 2rem;
    }

    .bento-grid > div {
        min-width: 0; /* Prevents flex/grid children from overflowing their container */
    }

    .glass-card-premium { padding: 1.5rem; }
    .glass-card-premium:hover { transform: translateY(-4px); }
    
    .portfolio-row:hover { background: rgba(255,255,255,0.06) !important; }
    .portfolio-fund-name { transition: color 0.3s ease; }
    .portfolio-row:hover .portfolio-fund-name { color: #0ea5e9 !important; }

    .micro-label { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; color: rgba(255,255,255,0.3); margin-bottom: 0.5rem; }
    .hero-stat { font-family: 'Outfit', sans-serif; font-weight: 700; letter-spacing: -1px; }

    .widget-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    .widget-title {
        font-family: 'Outfit';
        font-size: 0.85rem;
        font-weight: 800;
        color: rgba(255,255,255,0.4);
        display: flex;
        align-items: center;
        gap: 10px;
        text-transform: uppercase;
        letter-spacing: 1.5px;
    }

    .widget-title i {
        color: var(--accent-primary);
        width: 18px;
        height: 18px;
    }

    /* Grid Mapping */
    .w-portfolio { grid-column: span 1; grid-row: span 1; }
    .w-trades { grid-column: span 1; grid-row: span 1; }
    .w-chart { 
        grid-column: span 2; 
        grid-row: span 2; 
        border: 1px solid rgba(14, 165, 233, 0.12); 
        box-shadow: 0 0 20px rgba(14, 165, 233, 0.05);
        background: rgba(8, 12, 25, 0.6);
    }
    .w-banner { grid-column: span 2; grid-row: span 1; padding: 0 !important; height: 350px; }
    .w-heatmap { grid-column: span 1; grid-row: span 1; }
    .w-notifications { grid-column: span 1; grid-row: span 1; }
    .w-summary { grid-column: span 1; grid-row: span 1; }
    .w-watchlist { grid-column: span 1; grid-row: span 1; }
    .w-history { grid-column: span 3; grid-row: span 1; }
    .w-actions { grid-column: span 1; grid-row: span 1; }

    @media (max-width: 1400px) {
        .bento-grid { grid-template-columns: repeat(3, 1fr); gap: 18px; }
        .w-chart { grid-column: span 3; }
        .w-banner { grid-column: span 3; }
        .w-history { grid-column: span 3; }
        .w-portfolio, .w-trades, .w-heatmap, .w-notifications, .w-summary, .w-watchlist, .w-actions { grid-column: span 1; }
    }

    @media (max-width: 1100px) {
        .bento-grid { grid-template-columns: repeat(2, 1fr); gap: 15px; }
        .w-chart { grid-column: span 2; }
        .w-banner { grid-column: span 2; }
        .w-history { grid-column: span 2; }
        .w-portfolio, .w-trades, .w-heatmap, .w-notifications, .w-summary, .w-watchlist, .w-actions { grid-column: span 1; }
    }

    @media (max-width: 768px) {
        .bento-grid { grid-template-columns: 1fr; gap: 15px; }
        .w-chart, .w-history, .w-portfolio, .w-trades, .w-heatmap, .w-notifications, .w-summary, .w-watchlist, .w-actions, .w-banner { 
            grid-column: span 1; 
            grid-row: auto; 
        }
        .w-chart { height: 450px; }
    }

    @media (max-width: 576px) {
        .widget { padding: 16px; border-radius: 16px; }
        .widget-title { font-size: 0.8rem; }
        .content-area { padding-bottom: 80px; } /* Space for bottom actions */
        .w-chart { height: 320px; }
        .w-banner { display: none; } /* Hide heavy banner on small mobile */
        
        .btn-action { padding: 10px; font-size: 0.8rem; }
        .portfolio-value { font-size: clamp(1.25rem, 5vw, 2rem) !important; }
        .widget-header { flex-wrap: wrap; gap: 8px; }
    }

    /* Gradient Text */
    .text-gradient {
        background: linear-gradient(135deg, #fff 0%, #94a3b8 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* Custom Scrollbar for widgets */
    .widget-content::-webkit-scrollbar { width: 4px; }
    .widget-content::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }

    /* Buttons */
    .btn-action {
        width: 100%;
        padding: 12px;
        border-radius: 12px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s;
        border: none;
        font-size: 0.9rem;
        cursor: pointer;
        position: relative;
        z-index: 10;
        pointer-events: auto !important;
    }
    .btn-buy { background: rgba(14, 165, 233, 0.08); color: #0ea5e9; border: 1px solid rgba(14, 165, 233, 0.15); }
    .btn-sell { background: linear-gradient(135deg, #ff3333, #ff3333); color: white; }
    .btn-buy:hover { background: rgba(14, 165, 233, 0.15); color: #38bdf8; border-color: rgba(14, 165, 233, 0.3); }
    .btn-sell:hover { filter: brightness(1.08); transform: translateY(-1px); }

    .chart-container-inner {
        position: absolute;
        top: 60px;
        left: 0;
        right: 0;
        bottom: 0;
        border-radius: 0 0 18px 18px;
        overflow: hidden;
    }

    .asset-logo-small {
        width: 32px;
        height: 32px;
        border-radius: var(--radius-md, 8px);
        background: rgba(255,255,255,0.05);
        padding: 4px;
        object-fit: contain;
    }
</style>

<div class="bento-grid">
    @if(auth()->user()->kyc_status != 'approved')
    <!-- KYC Warning Banner -->
    <div class="widget" style="grid-column: span 4; background: rgba(239, 68, 68, 0.05); border: 1px solid rgba(239, 68, 68, 0.2); padding: 15px 24px;">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-danger-soft text-danger rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i data-lucide="shield-alert"></i>
                </div>
                <div>
                    <h6 class="outfit font-weight-bold mb-0 text-white">Identity Verification Required</h6>
                    <p class="text-secondary small mb-0">Complete your KYC to unlock full features and higher limits.</p>
                </div>
            </div>
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('profile', ['tab' => 'verification']) }}" class="btn btn-premium btn-sm px-4">Verify Now</a>
                    <button class="btn btn-sm text-secondary p-0 ms-2 dismiss-kyc" onclick="$(this).closest('.widget').fadeOut()" style="background:transparent; border:none;">
                        <i data-lucide="x" class="h5 mb-0"></i>
                    </button>
                </div>
            </div>
    </div>
    @endif
    <!-- Portfolio Value -->
    <div class="glass-card-premium w-portfolio" data-aos="fade-up">

        <div class="widget-header">
            <div class="widget-title text-truncate" style="max-width: 70%;"><i data-lucide="wallet"></i> Portfolio</div>
            <i data-lucide="more-horizontal" class="text-secondary pointer flex-shrink-0"></i>
        </div>
        <div class="display-5 outfit font-weight-bold mb-1 text-white portfolio-value number-font" style="letter-spacing: -1.5px; font-size: clamp(1.8rem, 4vw, 2.5rem); text-shadow: 0 0 20px rgba(14, 165, 233, 0.1); line-height: 1.1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" id="portfolio-equity-val">${{ number_format($equity, 2) }}</div>
        <div class="text-success small d-flex align-items-center gap-1 mb-3">
            <i data-lucide="arrow-up-right" style="width:14px;"></i> +4.2% today
        </div>
        <div style="height: 60px;">
             <svg viewBox="0 0 200 60" style="width:100%; height:100%;">
                <path d="M0 45 Q 40 10 80 40 T 160 20 T 200 10" fill="none" stroke="var(--accent-success)" stroke-width="2.5" />
                <path d="M0 45 Q 40 10 80 40 T 160 20 T 200 10 V 60 H 0 Z" fill="url(#grad-p)" opacity="0.15" />
                <defs>
                    <linearGradient id="grad-p" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" style="stop-color:var(--accent-success);stop-opacity:1" />
                        <stop offset="100%" style="stop-color:var(--accent-success);stop-opacity:0" />
                    </linearGradient>
                </defs>
             </svg>
        </div>
    </div>

    <!-- Active Trades -->
    <div class="glass-card-premium w-trades" data-aos="fade-up" data-aos-delay="100">

        <div class="widget-header">
            <div class="widget-title text-truncate" style="max-width: 65%;"><i data-lucide="zap"></i> Live Orders</div>
            <span class="badge badge-success px-2 py-1 flex-shrink-0" id="trade_count_bento" style="font-size:0.65rem; border-radius:6px; white-space: nowrap;">0 OPEN</span>
        </div>
        <div class="widget-content">
            @foreach($trade->take(2) as $t)
            <div class="d-flex align-items-center justify-content-between mb-3 p-2" style="background:rgba(255,255,255,0.02); border-radius:12px; border:1px solid rgba(255,255,255,0.05);">
                <div class="d-flex align-items-center gap-3">
                    <x-asset-logo :symbol="$t->symbol" size="sm" />
                    <div>
                        <div class="small font-weight-bold">{{ $t->symbol }}</div>
                        <div class="text-secondary" style="font-size:0.7rem;">${{ number_format($t->amount) }} • {{ strtoupper($t->type) }}</div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-success small font-weight-bold">+0.8%</div>
                </div>
            </div>
            @endforeach
            @if($trade->count() == 0)
                <div class="text-center py-4 text-secondary small">No active orders</div>
            @endif
        </div>
    </div>

    <!-- Real-time Chart -->
    <div class="glass-card-premium w-chart position-relative" data-aos="zoom-in" data-aos-delay="200">

        <div class="widget-header">
            <div class="widget-title"><i data-lucide="line-chart"></i> Market Terminal</div>
            <div class="d-flex gap-3 align-items-center">
                <div class="glass-pill px-2 py-1 small" style="background:rgba(14, 165, 233, 0.1); color:var(--accent-primary); border:1px solid rgba(14, 165, 233, 0.2);">LIVE</div>
                <i data-lucide="maximize-2" class="text-secondary pointer" style="width:16px;"></i>
            </div>
        </div>
        <div class="chart-container-inner">
            <div id="tradingview_bento" style="height: 100%;"></div>
        </div>
    </div>

    <!-- Market Heatmap -->
    <div class="glass-card-premium w-heatmap" data-aos="fade-up" data-aos-delay="300">

        <div class="widget-header">
            <div class="widget-title"><i data-lucide="grid"></i> Market Map</div>
        </div>
        <div style="display: grid; grid-template-columns: 2fr 1fr; grid-template-rows: 1fr 1fr; gap: 8px; height: 120px;">
            <div style="grid-row: span 2; background: rgba(255, 51, 51, 0.08); border: 1px solid rgba(255, 51, 51, 0.15); border-radius: 12px; padding: 12px; display: flex; flex-direction: column; justify-content: space-between;">
                <div class="d-flex align-items-center gap-2">
                    <x-asset-logo symbol="BTC" size="xs" assetType="crypto" />
                    <span class="font-weight-bold small">BTC</span>
                </div>
                <span class="h4 mb-0 text-success font-weight-bold">+2.5%</span>
            </div>
            <div style="background: rgba(255, 51, 51, 0.04); border: 1px solid rgba(255, 51, 51, 0.1); border-radius: 12px; padding: 8px; display: flex; justify-content: space-between; align-items: center;">
                <div class="d-flex align-items-center gap-2">
                    <x-asset-logo symbol="ETH" size="xs" assetType="crypto" />
                    <span class="small font-weight-bold">ETH</span>
                </div>
                <span class="small text-success">+7.8%</span>
            </div>
            <div style="background: rgba(244, 63, 94, 0.04); border: 1px solid rgba(244, 63, 94, 0.1); border-radius: 12px; padding: 8px; display: flex; justify-content: space-between; align-items: center;">
                <div class="d-flex align-items-center gap-2">
                    <x-asset-logo symbol="TSLA" size="xs" assetType="stock" />
                    <span class="small font-weight-bold">TSLA</span>
                </div>
                <span class="small text-danger">-1.2%</span>
            </div>
        </div>
    </div>

    <!-- Notifications -->
    <div class="glass-card-premium w-notifications" data-aos="fade-up" data-aos-delay="400">

        <div class="widget-header">
            <div class="widget-title"><i data-lucide="bell"></i> Feedback</div>
            <span class="text-secondary small" style="font-size:0.7rem;">{{ count($notifications) }} RECENT</span>
        </div>
        <div class="noti-list" style="max-height: 200px; overflow-y: auto;">
            @forelse($notifications as $n)
            <div class="d-flex gap-3 mb-3 pb-2" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                <div class="{{ str_contains(strtolower($n->message), 'success') || str_contains(strtolower($n->message), 'approved') ? 'text-success' : (str_contains(strtolower($n->message), 'error') || str_contains(strtolower($n->message), 'fail') || str_contains(strtolower($n->message), 'rejected') ? 'text-danger' : 'text-primary') }} mt-1">
                    <i data-lucide="{{ str_contains(strtolower($n->message), 'success') || str_contains(strtolower($n->message), 'approved') ? 'check-circle-2' : (str_contains(strtolower($n->message), 'error') || str_contains(strtolower($n->message), 'fail') || str_contains(strtolower($n->message), 'rejected') ? 'alert-circle' : 'info') }}" style="width:14px;"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="font-weight-bold small text-white text-truncate" style="max-width: 180px;">{{ $n->message }}</div>
                    <div class="text-secondary x-small">{{ $n->created_at->diffForHumans() }}</div>
                </div>
            </div>
            @empty
            <div class="text-center py-4 text-secondary small">No system messages</div>
            @endforelse
        </div>
    </div>

    <!-- Move Banner Here -->
    <div class="glass-card-premium w-banner" data-aos="fade-up" data-aos-delay="450" style="background: rgba(8, 12, 25, 0.8); height: 350px; padding: 0 !important; overflow: hidden; position: relative;">

        <div class="h-100 w-100 position-relative">
            <img src="{{ asset('assets/img/stock_banner_v6.png') }}" style="width: 100%; height: 100%; object-fit: cover; object-position: center; display: block;">
            <div class="position-absolute h-100 w-100 top-0 d-flex align-items-center px-4" style="background: linear-gradient(90deg, rgba(16,18,27,0.7) 0%, rgba(16,18,27,0) 100%); pointer-events: none; z-index: 2;">
                <div>
                    <h3 class="outfit font-weight-bold mb-0 text-white" style="font-size: 1.1rem;">Live Markets</h3>
                    <p class="text-secondary mb-0" style="font-size: 0.7rem;">Real-time price feeds.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Summary -->
    <div class="glass-card-premium w-summary" data-aos="fade-up" data-aos-delay="500">

        <div class="widget-header">
            <div class="widget-title"><i data-lucide="pie-chart"></i> Summary</div>
        </div>
        <div class="mb-4 d-flex justify-content-between align-items-end">
            <div>
                <div class="text-secondary small mb-1" style="font-size:0.75rem; letter-spacing: 0.5px;">TOTAL EQUITY</div>
                <div class="display-6 outfit font-weight-bold text-white mb-0 number-font" style="letter-spacing: -0.5px;">${{ number_format($equity, 2) }}</div>
            </div>
            <div class="text-right">
                <div class="text-secondary small mb-1" style="font-size:0.7rem;">STRIKE RATE</div>
                <div class="h4 outfit font-weight-bold text-primary mb-0">{{ $strike_rate }}%</div>
            </div>
        </div>
        <div class="d-flex justify-content-between p-3" style="background:rgba(0,0,0,0.2); border-radius:14px; border:1px solid rgba(255,255,255,0.03);">
            <div>
                <div class="text-secondary small mb-1" style="font-size:0.6rem;">AVAILABLE</div>
                <div class="font-weight-bold text-success" style="font-size:0.9rem;">${{ number_format($usd, 2) }}</div>
            </div>
            <div class="text-right">
                <div class="text-secondary small mb-1" style="font-size:0.6rem;">IN TRADE</div>
                <div class="font-weight-bold text-primary" style="font-size:0.9rem;">${{ number_format($margin, 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Top Wallet Coins -->
    <div class="glass-card-premium w-watchlist" data-aos="fade-up" data-aos-delay="600">

        <div class="widget-header">
            <div class="widget-title"><i data-lucide="wallet"></i> Top Wallet Coins</div>
        </div>
        <div class="watchlist-items">
            @php
                $myWallets = \App\Models\UserWallet::where('user_id', auth()->id())
                    ->orderBy('balance', 'desc')
                    ->take(4)
                    ->get();
                    
                if ($myWallets->isEmpty()) {
                    $fallbackSymbols = ['BTC', 'ETH', 'USDT', 'SOL'];
                    foreach ($fallbackSymbols as $sym) {
                        $myWallets->push((object)[
                            'coin_symbol' => $sym,
                            'balance' => 0
                        ]);
                    }
                }
            @endphp
            
            @foreach($myWallets as $wallet)
            <div class="d-flex justify-content-between mb-3 align-items-center wallet-ws-row" data-symbol="{{ strtolower($wallet->coin_symbol) }}usdt" data-balance="{{ $wallet->balance }}">
                <div class="d-flex align-items-center gap-3">
                    <x-asset-logo :symbol="$wallet->coin_symbol" size="sm" />
                    <div>
                        <span class="font-weight-bold small d-block">{{ $wallet->coin_symbol }}</span>
                        <span class="text-secondary" style="font-size: 0.7rem;">Active Wallet</span>
                    </div>
                </div>
                <div class="text-right">
                    <div class="small font-weight-bold">{{ number_format($wallet->balance, 6) }} {{ $wallet->coin_symbol }}</div>
                    <div class="text-success wallet-usd-val" style="font-size:0.6rem;">$--</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Trade History / Activity Feed -->
    <div class="glass-card-premium w-history" data-aos="fade-up" data-aos-delay="700" style="padding: 0; overflow: hidden;">

        <div class="widget-header" style="padding: 24px 32px 12px 32px; margin-bottom: 0;">
            <div class="widget-title"><i data-lucide="history"></i> Recent Transactions</div>
            <div class="d-flex gap-2 align-items-center">
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle px-3 py-1" type="button" data-toggle="dropdown" style="border-radius: 8px; font-size: 0.75rem; font-weight: 700;">
                        HISTORY TYPES
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark shadow-lg" style="background: rgba(0, 0, 0, 0.95); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px;">
                        <li><a class="dropdown-item py-2" href="{{ route('trades.history') }}"><i class="ri-history-line me-2 text-primary"></i>Manual Trades</a></li>
                        <li><a class="dropdown-item py-2" href="{{ auth()->user()->hasFeature('bot_trading') ? route('bots.history') : route('user.upgrade') }}"><i class="ri-robot-2-line me-2 text-info"></i>Bot Trading @unless(auth()->user()->hasFeature('bot_trading'))<i class="ri-lock-line ms-2 opacity-50"></i>@endunless</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('copy-trading.history') }}"><i class="ri-user-received-2-line me-2 text-success"></i>Copy Trading</a></li>
                    </ul>
                </div>
                <a href="{{ route('trades.history') }}" class="text-secondary small font-weight-bold" style="font-size:0.75rem; margin-left: 10px;">VIEW ALL <i data-lucide="arrow-right" style="width:14px; margin-bottom: 2px;"></i></a>
            </div>
        </div>
        <div class="px-3 pb-3">
             <div class="table-responsive">
                <table class="table mb-0" style="border-collapse: separate; border-spacing: 0 4px;">
                    <thead>
                        <tr>
                            <th class="micro-label border-0 pb-3">ASSET</th>
                            <th class="micro-label border-0 pb-3 text-end">STATUS</th>
                            <th class="micro-label border-0 pb-3 text-end">RESULT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trade->take(5) as $t)
                        <tr class="portfolio-row pointer" style="background: rgba(255,255,255,0.02); transition: all 0.2s ease;">
                            <td class="py-3 border-0" style="border-radius: 8px 0 0 8px; padding-left: 1rem;">
                                <div class="d-flex align-items-center gap-2">
                                    <x-asset-logo :symbol="$t->symbol" size="xs" />
                                    <div class="fw-bold text-white mb-0 portfolio-fund-name" style="font-size: 0.85rem;">{{ $t->symbol }}</div>
                                </div>
                            </td>
                            <td class="py-3 border-0 align-middle text-end">
                                @if($t->status == 'pending')
                                <div class="pending-progress">
                                    <div class="d-flex justify-content-between w-100"><span class="progress-label">Processing</span><span class="progress-pct">93%</span></div>
                                    <div class="progress-track"><div class="progress-fill"></div></div>
                                </div>
                                @else
                                <span class="badge {{ $t->status == 'win' ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger' }}" style="font-size: 9px; font-weight: 800;">{{ strtoupper($t->status) }}</span>
                                @endif
                            </td>
                            <td class="py-3 border-0 align-middle text-end" style="border-radius: 0 8px 8px 0; padding-right: 1rem;">
                                <span class="text-white fw-bold outfit" style="font-size: 0.85rem;">{{ $t->p_l < 0 ? '-' : '' }}${{ number_format(abs($t->p_l), 2) }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="glass-card-premium w-actions" data-aos="fade-up" data-aos-delay="800" style="overflow: visible;">

        <div class="widget-header">
            <div class="widget-title"><i data-lucide="mouse-pointer-2"></i> Quick Trade</div>
        </div>
        <form id="post_trade_bento" class="mt-1" onsubmit="return false;">
            @csrf
            <div class="form-group mb-3">
                 <label class="text-secondary small mb-2" style="font-size:0.6rem;">SELECT ASSET</label>
                 
                 <div class="custom-select-wrapper position-relative" id="quickTradeDropdownWrapper">
                     <!-- Visible custom select -->
                     <div class="form-control d-flex align-items-center justify-content-between w-100" id="quickTradeDropdownBtn" style="background:rgba(0,0,0,0.3); border:1px solid rgba(255,255,255,0.1); color:white; border-radius:10px; font-size:0.85rem; cursor: pointer; min-height: 40px;">
                         <div class="d-flex align-items-center gap-2">
                             <img id="bento-asset-logo-img" src="{{ \App\Services\AssetLogoService::getLogoUrl($asset->first()->symbols ?? '', $asset->first()->type ?? 'crypto', $asset->first()->image ?? '') }}" style="width: 22px; height: 22px; object-fit: contain; border-radius: 4px;" onerror="let sym = '{{ $asset->first()->symbols ?? 'X' }}'.replace(/[^a-zA-Z0-9]/g, '').substring(0, 3).toUpperCase() || 'X'; this.onerror=null; this.src='https://ui-avatars.com/api/?name=' + encodeURIComponent(sym) + '&background=0ea5e9&color=fff&bold=true';">
                             <span id="selected-asset-text" class="fw-bold">{{ $asset->first()->symbols ?? '' }}</span>
                         </div>
                         <i class="ri-arrow-down-s-line ms-auto text-secondary"></i>
                     </div>
                     
                     <!-- Hidden select for actual form submission -->
                     <select name="asset" id="asset_select_bento" class="d-none">
                        @foreach($asset as $a)
                        <option value="{{ $a->symbols }}" data-logo-url="{{ \App\Services\AssetLogoService::getLogoUrl($a->symbols, $a->type, $a->image ?? '') }}">{{ $a->symbols }}</option>
                        @endforeach
                     </select>
                     
                     <!-- Dropdown options with logos -->
                     <ul id="quickTradeDropdownMenu" class="w-100 shadow-lg" style="display: none; position: absolute; top: 100%; left: 0; z-index: 1000; background: rgba(16, 18, 27, 0.95); backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; max-height: 300px; overflow-y: auto; padding: 0; margin: 5px 0 0 0; list-style: none;">
                        @foreach($asset as $a)
                        <li>
                            <a class="d-flex align-items-center gap-2 custom-asset-option py-2 px-3 text-decoration-none" href="#" data-value="{{ $a->symbols }}" data-logo-url="{{ \App\Services\AssetLogoService::getLogoUrl($a->symbols, $a->type, $a->image ?? '') }}">
                                <img src="{{ \App\Services\AssetLogoService::getLogoUrl($a->symbols, $a->type, $a->image ?? '') }}" style="width: 20px; height: 20px; object-fit: contain; border-radius: 4px;" onerror="let sym = '{{ $a->symbols ?? 'X' }}'.replace(/[^a-zA-Z0-9]/g, '').substring(0, 3).toUpperCase() || 'X'; this.onerror=null; this.src='https://ui-avatars.com/api/?name=' + encodeURIComponent(sym) + '&background=0ea5e9&color=fff&bold=true';">
                                <span class="text-white fw-medium">{{ $a->symbols }}</span>
                            </a>
                        </li>
                        @endforeach
                     </ul>
                 </div>
            </div>

            <style>
                .custom-asset-option:hover {
                    background: rgba(255,255,255,0.1) !important;
                }
            </style>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const btn = document.getElementById('quickTradeDropdownBtn');
                    const menu = document.getElementById('quickTradeDropdownMenu');
                    const wrapper = document.getElementById('quickTradeDropdownWrapper');
                    
                    if(btn && menu) {
                        btn.addEventListener('click', function(e) {
                            e.stopPropagation();
                            menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
                        });

                        document.addEventListener('click', function(e) {
                            if (wrapper && !wrapper.contains(e.target)) {
                                menu.style.display = 'none';
                            }
                        });
                    }

                    const options = document.querySelectorAll('.custom-asset-option');
                    const hiddenSelect = document.getElementById('asset_select_bento');
                    const selectedText = document.getElementById('selected-asset-text');
                    const selectedImg = document.getElementById('bento-asset-logo-img');

                    options.forEach(option => {
                        option.addEventListener('click', function(e) {
                            e.preventDefault();
                            const value = this.getAttribute('data-value');
                            // Pull the actual src from the rendered image, which will already have fallback applied if it failed
                            const logo = this.querySelector('img').src;
                            
                            // Update visible UI
                            if(selectedText) selectedText.textContent = value;
                            if(selectedImg) {
                                selectedImg.src = logo;
                                // Reset the onerror handler for the main image with the new symbol, just in case the src is still loading and fails
                                selectedImg.onerror = function() {
                                    let sym = (value || 'X').replace(/[^a-zA-Z0-9]/g, '').substring(0, 3).toUpperCase();
                                    this.onerror = null;
                                    this.src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(sym) + '&background=0ea5e9&color=fff&bold=true';
                                };
                            }
                            
                            // Update hidden select
                            if(hiddenSelect) hiddenSelect.value = value;
                            
                            // Close menu
                            if(menu) menu.style.display = 'none';
                        });
                    });
                });
            </script>
            
            <div class="form-group mb-3">
                <label class="text-secondary small mb-2" style="font-size:0.6rem;">INVESTMENT AMOUNT</label>
                <div class="input-group">
                    <span class="input-group-text border-0" style="background:rgba(0,0,0,0.2); color:var(--text-secondary);">$</span>
                    <input type="number" class="form-control" name="amount" value="100" id="bento_amount_input" style="background:rgba(0,0,0,0.3); border:1px solid rgba(255,255,255,0.1); color:white; font-weight:700; border-left:none;">
                </div>
            </div>
            
            <div class="d-flex gap-2 mt-4">
                <button type="button" class="btn-action btn-buy" onclick="submitTradeBento('call')">BUY</button>
                <button type="button" class="btn-action btn-sell" onclick="submitTradeBento('put')">SELL</button>
            </div>
        </form>
    </div>
</div>

@push('js')
<script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
<script>
    // Initialize Lucide Icons
    lucide.createIcons();

    // TradingView Widget
    new TradingView.widget({
        "autosize": true,
        "symbol": "BINANCE:BTCUSDT",
        "interval": "1",
        "timezone": "Etc/UTC",
        "theme": "dark",
        "style": "1",
        "locale": "en",
        "toolbar_bg": "#f1f3f6",
        "enable_publishing": false,
        "allow_symbol_change": true,
        "container_id": "tradingview_bento",
        "backgroundColor": "#0a0b14",
        "gridColor": "rgba(255, 255, 255, 0.02)",
        "hide_top_toolbar": false,
        "hide_legend": true,
        "save_image": false
    });

    function submitTradeBento(type) {

        const amount = document.getElementById('bento_amount_input').value;
        const asset = document.getElementById('asset_select_bento').value;

        const formData = {
            asset: asset,
            amount: amount,
            expiretime: 1, 
            type: type,
            _token: "{{ csrf_token() }}"
        };

        if (!formData.amount || formData.amount <= 0) return toastr.error("Invalid amount");

        fetch("{{ route('trade') }}", {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            body: JSON.stringify(formData)
        })
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                toastr.success(data.status);
                setTimeout(() => location.reload(), 1500);
            } else {
                toastr.error(data.error || "Trade failed");
            }
        })
        .catch(err => {

            toastr.error("An error occurred");
        });
    }

    function fetchBentoStatus() {
        fetch("{{ route('trade.js') }}")
            .then(res => res.json())
            .then(data => {
                $('#trade_count_bento').text(data.count_trade + ' OPEN');
            });
    }

    async function pollWatchlist() {
        $('.watchlist-item-row').each(async function() {
            let symbol = $(this).data('symbol');
            let exId = $(this).data('ex-id');
            let symbolForUrl = symbol.replace('/', '-');

            try {
                const response = await fetch(`${window.APP_URL}/dashboard/asset-price/${symbolForUrl}?exchange_id=${exId}`);
                const data = await response.json();
                if (data.price) {
                    let p = parseFloat(data.price);
                    $(this).find('.asset-price-val').text('$' + p.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                }
            } catch (e) {}
        });
    }

    function initBinanceWebSocket() {
        const streams = [];
        $('.wallet-ws-row').each(function() {
            let sym = $(this).data('symbol');
            if(sym && !sym.startsWith('usd')) {
                streams.push(sym + '@ticker');
            }
        });

        if (streams.length === 0) return;

        const wsUrl = `wss://stream.binance.com:9443/stream?streams=${streams.join('/')}`;
        const ws = new WebSocket(wsUrl);

        ws.onmessage = function (event) {
            const data = JSON.parse(event.data);
            if (data && data.data && data.data.c) {
                const symbol = data.data.s.toLowerCase();
                const price = parseFloat(data.data.c);
                
                $(`.wallet-ws-row[data-symbol="${symbol}"]`).each(function() {
                    const balance = parseFloat($(this).data('balance'));
                    const usdValue = balance * price;
                    $(this).find('.wallet-usd-val').text('~$' + usdValue.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                });
            }
        };
        
        ws.onerror = function (error) {
            console.error('WebSocket Error ', error);
        };
    }

    $(document).ready(function() {
        fetchBentoStatus();
        pollWatchlist();
        initBinanceWebSocket();
        setInterval(fetchBentoStatus, 10000);
        setInterval(pollWatchlist, 5000);
    });

    // Staggered Entrance Animation
    window.addEventListener('load', () => {
        if (typeof anime !== 'undefined') {
            anime({
                targets: '.glass-card-premium, .widget',
                scale: [0.95, 1],
                opacity: [0, 1],
                translateY: [40, 0],
                delay: anime.stagger(100),
                easing: 'easeOutSpring(1, 80, 10, 0)',
                duration: 1200
            });
        }
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
@endpush
@endsection


