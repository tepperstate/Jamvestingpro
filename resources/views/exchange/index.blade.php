@extends('layouts.user.app')

@section('title', 'Portfolio')

@section('content')
@php
    $logoMap = [];
    $mirrorMap = [];
    if(isset($assets)){
        foreach($assets as $a) {
            $logoMap[$a->symbols] = \App\Services\AssetLogoService::getLogoUrl($a->symbols, isset($a->exchanges_id) ? ($a->exchanges_id == 1 ? 'forex' : ($a->exchanges_id == 3 ? 'stock' : 'crypto')) : 'crypto', $a->image ?? $a->image1 ?? $a->image2 ?? '');
            if(!empty($a->mirror_symbol)) { $mirrorMap[$a->symbols] = $a->mirror_symbol; }
        }
    }
    if(isset($asset)){
        foreach($asset as $a) {
            $logoMap[$a->symbols] = \App\Services\AssetLogoService::getLogoUrl($a->symbols, isset($a->exchanges_id) ? ($a->exchanges_id == 1 ? 'forex' : ($a->exchanges_id == 3 ? 'stock' : 'crypto')) : 'crypto', $a->image ?? $a->image1 ?? $a->image2 ?? '');
            if(!empty($a->mirror_symbol)) { $mirrorMap[$a->symbols] = $a->mirror_symbol; }
        }
    }
@endphp
<!-- Remix Icon for modern look -->
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

<div class="container-fluid">
    <!-- Top Stats Row (Equity, Profit, Today) -->
<div class="bento-grid mb-4">
    <style>

        
        .micro-label { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; color: rgba(255,255,255,0.3); margin-bottom: 0.5rem; }
        .hero-stat { font-family: 'Outfit', sans-serif; font-weight: 700; letter-spacing: -1px; }

        .portfolio-row:hover { background: rgba(255,255,255,0.06) !important; }
        .portfolio-fund-name { transition: color 0.3s ease; }
        .portfolio-row:hover .portfolio-fund-name { color: #0ea5e9 !important; }

        .input-amount-field-premium {
            background: rgba(0, 0, 0, 0.3) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: white !important;
            padding: 0.8rem 1rem !important;
            font-size: 1.25rem !important;
            font-weight: 700 !important;
            border-radius: 12px !important;
            width: 100% !important;
            transition: all 0.3s ease !important;
            text-align: center;
        }
        .input-amount-field-premium:focus {
            border-color: #0ea5e9 !important;
            background: rgba(0, 0, 0, 0.5) !important;
            box-shadow: 0 0 20px rgba(14, 165, 233, 0.2) !important;
        }
    </style>

    <!-- Top Stats (Integrated into Grid) -->
    <div class="glass-card-premium p-4 bento-col-4 d-flex align-items-center gap-3">
        <div class="icon-box bg-primary-soft">
            <i class="ri-wallet-3-line text-primary"></i>
        </div>
        <div>
            <div class="micro-label">Account Equity</div>
            <div class="h4 mb-0 outfit font-weight-bold text-white tracking-tighter number-font">${{ number_format($equity, 2) }}</div>
        </div>
    </div>
    
    <div class="glass-card-premium p-4 bento-col-4 d-flex align-items-center gap-3">
        <div class="icon-box bg-success-soft">
            <i class="ri-line-chart-line text-success"></i>
        </div>
        <div>
            <div class="micro-label">Total P/L</div>
            <div class="h4 mb-0 outfit font-weight-bold tracking-tighter number-font {{ $sumPL >= 0 ? 'text-success' : 'text-danger' }}">{{ $sumPL < 0 ? '-' : '' }}${{ number_format(abs($sumPL), 2) }}</div>
        </div>
    </div>
    
    <div class="glass-card-premium p-4 bento-col-4 d-flex align-items-center gap-3">
        <div class="icon-box bg-info-soft">
            <i class="ri-time-line text-info"></i>
        </div>
        <div>
            <div class="micro-label">Today's Profit</div>
            <div class="h4 mb-0 outfit font-weight-bold tracking-tighter number-font {{ $today >= 0 ? 'text-info' : 'text-danger' }}">{{ $today < 0 ? '-' : '' }}${{ number_format(abs($today), 2) }}</div>
        </div>
    </div>

    <!-- Main Banner (Full Width within Grid) -->
    <div class="bento-col-12 glass-card-premium p-0 main-trading-banner overflow-hidden position-relative" style="border-radius: 24px;">
        <div style="position: absolute; inset: -20px; background: url('{{ asset('assets/img/trade_room_bg_v2.png') }}') center/cover; filter: blur(10px); opacity: 0.4; z-index: 0;"></div>
        <img src="{{ asset('assets/img/trade_room_bg_v2.png') }}" style="width: 100%; height: 100%; object-fit: cover; object-position: center; position: relative; z-index: 1;">
        <div class="position-absolute h-100 w-100 top-0 d-flex align-items-center px-5" style="background: linear-gradient(90deg, rgba(0,0,0,0.95) 0%, rgba(0,0,0,0.8) 40%, rgba(0,0,0,0) 100%); z-index: 2;">
            <div>
                <div class="badge bg-success-glass mb-3 px-3 py-2" style="font-size: 10px; font-weight: 800; letter-spacing: 1.5px; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);">MARKET ACCESS: ACTIVE</div>
                <h2 class="outfit font-weight-bold text-white mb-2 tracking-tighter" style="font-size: 2.2rem; text-shadow: 0 4px 15px rgba(0,0,0,0.9);">Live Markets</h2>
                <p class="mb-0" style="font-size: 0.95rem; max-width: 450px; color: rgba(255,255,255,0.95); text-shadow: 0 2px 8px rgba(0,0,0,0.9); font-weight: 500;">Direct access to global execution pools with zero-latency visual confirmation.</p>
            </div>
        </div>
    </div>
</div>

    <style>
        .main-trading-banner { height: 220px; }
        .banner-title { font-size: 2.2rem; }
        .banner-subtitle { font-size: 1.1rem; max-width: 500px; }
        
        @media (max-width: 768px) {
            .main-trading-banner { height: 140px; }
            .banner-title { font-size: 1.4rem; }
            .banner-subtitle { font-size: 0.85rem; max-width: 300px; }
            .banner-overlay { px-3 !important; }
        }
        
        @media (max-width: 576px) {
            .main-trading-banner { height: 100px; border-radius: 12px; }
            .banner-title { font-size: 1.1rem; }
            .banner-subtitle { display: none; }
            .chart-header-mobile { flex-direction: column; align-items: flex-start !important; gap: 10px !important; }
            #trading-chart-wrapper { min-height: 450px !important; } /* Increased from 380px for better visibility */
            .btn-trade-call, .btn-trade-put { height: 50px !important; font-size: 1rem !important; }
        }
    </style>

    <!-- Main Trading Layout (Bento Grid) -->
    <div class="bento-grid">
        <!-- Chart Section (75%) -->
        <div class="bento-col-9 bento-row-2" data-aos="fade-right" data-aos-delay="200">
            <div class="glass-card-premium p-4 d-flex flex-column h-100" style="border-radius: 24px;">
                <div class="d-flex justify-content-between align-items-center mb-4 chart-header-mobile">
                    <div class="d-flex align-items-center gap-4">
                        <button class="btn btn-premium btn-glow" id="select_market_btn" style="min-width: 160px; padding: 12px 24px; border-radius: 14px; background: linear-gradient(135deg, var(--accent-primary) 0%, #0ea5e9 100%); color: #000; font-weight: 800; font-size: 11px; letter-spacing: 1px;" data-toggle="modal" data-target="#asset_list">
                            <i class="ri-search-2-line me-2"></i>SELECT MARKET
                        </button>
                        <div class="d-flex align-items-center gap-3 bg-black-soft p-2 px-3 rounded-pill" style="min-width: 200px; border: 1px solid rgba(255,255,255,0.08);">
                            <div id="active-asset-logo" class="d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: rgba(0,0,0,0.4); border-radius: 10px; border: 1px solid rgba(255,255,255,0.1); padding: 5px;">
                                <!-- Logo injected by JS -->
                            </div>
                            <div class="d-flex flex-column" style="line-height: 1;">
                                <div class="h5 mb-1 outfit font-weight-bold text-white tracking-tighter" id="show_market" style="font-size: 1.2rem; letter-spacing: -0.5px;">{{ $symbol }}</div>
                                <div id="active-price-display" class="font-weight-bold text-primary" style="font-family: 'Inter'; font-size: 0.8rem; letter-spacing: -0.2px;">
                                    ${{ number_format($asset->where('symbols', $symbol)->first()->buy ?? 0, 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="chart-container flex-grow-1 d-flex flex-column" id="trading-chart-wrapper" style="border-radius: 18px; overflow: hidden; min-height: 500px; border: 1px solid rgba(255,255,255,0.1); background: #000000;">
                    <div id="chart-mount" class="spotlight-vibe flex-grow-1" style="min-height: 500px; border-radius: 20px;">
                        <!-- Chart injected dynamically via initChart() -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Execution Panel (25%) -->
        <div class="bento-col-3 bento-row-2" data-aos="fade-left" data-aos-delay="400">
            <div class="glass-card-premium p-4 h-100" id="execution-panel" style="border-radius: 24px;">
                <div class="d-flex align-items-center gap-3 mb-5">
                    <div class="icon-box bg-primary-soft" style="width: 38px; height: 38px; font-size: 1.1rem;">
                        <i class="ri-flashlight-line text-primary"></i>
                    </div>
                    <div>
                        <h5 class="outfit font-weight-bold mb-0 text-white">Execution Desk</h5>
                        <div class="micro-label" style="margin-bottom: 0;">System Terminal v2.0</div>
                    </div>
                </div>
                
                <form id="post_trade">
                    @csrf
                    <div class="form-group mb-4">
                        <label class="micro-label" for="expiretime">Trade Duration</label>
                        <select class="form-control premium-input" name="expiretime" id="expiretime" aria-label="Trade Duration" style="background: rgba(0,0,0,0.4); border-radius: 12px; height: 48px; border-color: rgba(255,255,255,0.1);">
                            <option value="1">1 Minute</option>
                            <option value="5">5 Minutes</option>
                            <option value="15">15 Minutes</option>
                            <option value="30">30 Minutes</option>
                            <option value="60">1 Hour</option>
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label class="micro-label" for="leverage">Execution Leverage</label>
                        <select class="form-control premium-input" name="leverage" id="leverage" aria-label="Execution Leverage" style="background: rgba(0,0,0,0.4); border-radius: 12px; height: 48px; border-color: rgba(255,255,255,0.1);" {{ auth()->user()->hasFeature('high_leverage') ? '' : 'disabled' }}>
                            <option value="1:1">1:1 (Standard)</option>
                            @if(auth()->user()->hasFeature('high_leverage'))
                            <option value="1:10">1:10</option>
                            <option value="1:50">1:50</option>
                            <option value="1:100">1:100</option>
                            <option value="1:500">1:500</option>
                            @endif
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label class="micro-label" for="trade-amount">Investment Principal</label>
                        <input type="number" class="input-amount-field-premium" id="trade-amount" name="amount" placeholder="0.00" value="100" aria-label="Investment Principal" required>
                    </div>

                    <div class="payout-summary glass-panel p-4 mb-4 text-center" style="background: rgba(255,255,255,0.03); border-radius: 18px; border: 1px solid rgba(255,255,255,0.05);">
                        <div class="micro-label mb-2">EXPECTED RECOVERY (<span id="payout-perc">90</span>%)</div>
                        <div class="h2 mb-0 outfit font-weight-bold text-success tracking-tighter number-font" id="expected-payout">$190.00</div>
                    </div>

                    <input type="hidden" id="active-asset-symbol" value="{{ $symbol }}">
<input type="hidden" id="initial-ex-id" value="{{ $asset->where('symbols', $default_asset)->first()->exchanges_id ?? 2 }}">
                    <input type="hidden" id="percentage_order" value="90">
                    <input type="hidden" id="rate" value="0">

                    <div class="d-grid gap-3 mt-3">
                        <button type="button" class="btn-trade-call w-100 py-3 mb-3" id="btn-buy" aria-label="Place Call Trade" style="height: 60px; font-size: 1.2rem;">
                            <i class="ri-arrow-up-circle-fill me-2" aria-hidden="true"></i> CALL
                        </button>
                        <button type="button" class="btn-trade-put w-100 py-3" id="btn-sell" aria-label="Place Put Trade" style="height: 60px; font-size: 1.2rem;">
                            <i class="ri-arrow-down-circle-fill me-2" aria-hidden="true"></i> PUT
                        </button>
                    </div>
                </form>

                <div id="trade-loader" class="mt-3 shimmer-skeleton" style="display:none; height: 60px; width: 100%; border-radius: 12px;"></div>
            </div>
        </div>
    </div>

    <!-- Active Trades Section -->
    <div class="row mt-5" data-aos="fade-up" data-aos-delay="400">
        <div class="col-12">
            <div class="glass-card-premium p-4 shadow-lg" style="border-radius: 28px;">
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <div>
                        <h5 class="outfit font-weight-bold mb-1 text-white">Active Operational Positions</h5>
                        <div class="micro-label" style="margin-bottom: 0;">Live Transaction Ledger</div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <a href="{{ route('trades.history') }}" class="btn btn-xs btn-outline-premium px-3" style="border-radius: 12px; font-size: 10px; font-weight: 800; letter-spacing: 1px;">
                            <i class="ri-history-line me-1"></i> ARCHIVE
                        </a>
                        <span class="badge" style="background: rgba(255,255,255,0.1); color: white; padding: 6px 12px; border-radius: 10px; font-weight: 800; font-size: 11px;" id="trade_count">0</span>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0" style="border-collapse: separate; border-spacing: 0 4px;">
                        <thead>
                            <tr>
                                <th class="micro-label border-0 pb-3">SECURITY</th>
                                <th class="micro-label border-0 pb-3">LEVERAGE</th>
                                <th class="micro-label border-0 pb-3">SIDE</th>
                                <th class="micro-label border-0 pb-3">PRINCIPAL</th>
                                <th class="micro-label border-0 pb-3">STRIKE</th>
                                <th class="micro-label border-0 pb-3">STATUS</th>
                                <th class="micro-label border-0 pb-3">EXPIRY</th>
                                <th class="micro-label border-0 pb-3 text-end">EXECUTE</th>
                            </tr>
                        </thead>
                        <tbody id="active-trades-list">
                            <tr>
                                <td colspan="8" class="text-center py-5 text-secondary" style="opacity: 0.5;">Awaiting market execution...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Asset Selection Modal -->
<div class="modal fade" id="asset_list" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content glass-card-premium overflow-hidden" style="border-radius: 32px; border-color: rgba(255,255,255,0.15) !important;">
            <div class="modal-header border-0 p-4">
                <h4 class="modal-title outfit font-weight-bold">Select Market</h4>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-4 bg-dark-deep" style="background: #080a0f;">
                <div class="search-box mb-4">
                    <div class="position-relative">
                        <i class="ri-search-2-line position-absolute start-0 top-50 translate-middle-y ms-3 text-muted" aria-hidden="true"></i>
                        <input type="text" id="market-search" class="form-control premium-input w-100 ps-5" aria-label="Search markets" placeholder="Search markets (Stocks, Crypto, Forex)..." style="height: 54px; font-size: 1rem; border-radius: 16px;">
                    </div>
                </div>
                <div class="market-categories mb-4 d-flex gap-2 overflow-auto pb-2 no-scrollbar">
                    @foreach($cat as $c)
                    <button class="btn btn-sm btn-outline-premium px-4 cat-btn rounded-pill" onclick="filterCategory('{{ $c->id }}')" style="white-space: nowrap;">{{ $c->name }}</button>
                    @endforeach
                </div>
                <div class="row g-3 no-scrollbar align-items-stretch" id="market-grid" style="max-height: 60vh; overflow-y: auto;">
                    @foreach($asset as $a)
                    <div class="col-lg-3 col-md-4 col-6 mb-2">
                        <div class="asset-card glass-card p-3 d-flex flex-column gap-2 h-100" onclick="selectAsset('{{ $a->symbols }}', '{{ $a->percentage }}', '{{ $a->buy }}', '{{ $a->exchanges_id }}')">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="symbol-logo-wrapper">
                                    <x-asset-logo :symbol="$a->symbols" size="sm" />
                                </div>
                                <span class="badge bg-success-soft text-success px-2" style="font-size: 0.7rem;">+{{ $a->percentage }}%</span>
                            </div>
                            <div class="mt-auto">
                                <div class="outfit font-weight-bold text-white mb-0" style="font-size: 0.95rem; line-height: 1.2;">{{ $a->symbols }}</div>
                                <div class="small text-secondary tracking-widest" id="{{ $a->symbols }}-price" style="font-family: 'Inter'; font-size: 0.75rem;">${{ number_format($a->buy, 2) }}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .icon-box { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    .bg-primary-soft { background: rgba(59, 130, 246, 0.1); }
    .bg-success-soft { background: rgba(255, 51, 51, 0.1); }
    .bg-info-soft { background: rgba(6, 182, 212, 0.1); }
    
    .premium-input { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); color: white; border-radius: 12px; padding: 12px; transition: 0.3s; }
    .premium-input:focus { background: rgba(255,255,255,0.05); border-color: var(--accent-primary); box-shadow: 0 0 15px rgba(255, 51, 51, 0.1); }
    
    .premium-input-large { background: rgba(59, 130, 246, 0.05); border: 2px solid rgba(59, 130, 246, 0.2); color: white; border-radius: 16px; padding: 16px; font-size: 1.5rem; font-weight: 700; text-align: center; }
    
    .btn-trade-call { background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; border-radius: 14px; color: white; font-weight: 700; transition: transform 0.2s; }
    .btn-trade-put { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border: none; border-radius: 14px; color: white; font-weight: 700; transition: transform 0.2s; }
    .btn-trade-call:hover, .btn-trade-put:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.3); }
    
    .asset-card { border-radius: 20px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer; border: 1px solid rgba(255,255,255,0.05); background: rgba(255,255,255,0.01); }
    .asset-card:hover { border-color: var(--accent-primary); background: rgba(255, 51, 51, 0.04); transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.4); }
    .asset-card:active { transform: scale(0.96); }
    
    .bg-dark-glass { background: rgba(0,0,0,0.4); backdrop-filter: blur(10px); }
    .border-glass { border: 1px solid rgba(255,255,255,0.08); }

    .pending-progress { width: 100%; min-width: 120px; }
    .progress-label { font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,0.5); }
    .progress-pct { font-size: 9px; font-weight: 800; font-family: 'Outfit'; color: rgba(255,255,255,0.8); }
    .progress-track { height: 4px; background: rgba(255,255,255,0.05); border-radius: 4px; overflow: hidden; margin-top: 4px; }
    .progress-fill { height: 100%; background: var(--accent-primary); width: 5%; animation: progressAnim 15s ease-out forwards; }
    @keyframes progressAnim {
        0% { width: 5%; }
        50% { width: 60%; }
        90% { width: 90%; }
        100% { width: 93%; }
    }
    
    .spin-anim { display: inline-block; animation: spin 2s linear infinite; }
    @keyframes spin { 100% { transform: rotate(360deg); } }

    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    
    .spotlight-vibe { position: relative; overflow: hidden; }
    .spotlight-vibe::after { content: ''; position: absolute; top: 0; left: -100%; width: 50%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.05), transparent); animation: sweep 3s infinite; }
    @keyframes sweep { 0% { left: -100%; } 50% { left: 150%; } 100% { left: 150%; } }
    
    .glass-pill { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; font-size: 0.75rem; }
    .btn-outline-premium { border: 1px solid rgba(255, 51, 51, 0.3); color: #ff3333; transition: 0.3s; }
    .btn-outline-premium:hover { background: rgba(255, 51, 51, 0.1); color: white; border-color: #ff3333; }
</style>

@endsection

@push('js')
<script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
<script>
    let logoMap = @json($logoMap ?? []);
    let mirrorMap = @json($mirrorMap ?? (object)[]);
    let currentAsset = "{{ $default_asset ?? 'BTCUSD' }}";
    let widget = null;

    function getLogoUrl(symbol, exchanges_id) {
        if(logoMap[symbol]) return logoMap[symbol];
        return `${window.APP_URL}/api/stock-logo/${symbol}?v=1.1.1`;
    }

    function initChart(symbol, exchanges_id) {
        if (typeof TradingView === 'undefined') {
            console.warn("TradingView library not loaded — skipping chart init");
            return;
        }
        let lookupSymbol = mirrorMap[symbol] || symbol;
        let tvSymbol = lookupSymbol.replace('/', '');
        
        // Smart prefixing for TradingView
        if (exchanges_id == 1) {
            // Forex → FX: prefix
            tvSymbol = "FX:" + tvSymbol;
        } else if (exchanges_id == 2 || symbol.includes('USDT') || symbol.includes('BTC') || symbol.includes('ETH')) {
            // Crypto → BINANCE: prefix, ensure USDT suffix
            tvSymbol = "BINANCE:" + tvSymbol.replace(/USD$/, 'USDT');
        }
        // Stocks (exchanges_id == 3 or other): NO prefix — let TradingView auto-resolve
        // This fixes NVR, BRK.A, LISN.SW, 000858.SZ, 9988.HK etc. that are not on NASDAQ

        // Cleanup existing widget properly
        if (window.tvWidget) {
            try {
                window.tvWidget.remove();
            } catch (e) {
                console.warn("TradingView cleanup failed", e);
            }
            window.tvWidget = null;
        }

        // Clear existing widget DOM to prevent overlapping bug
        document.getElementById('chart-mount').innerHTML = '<div id="tradingview_widget" style="width: 100%; height: 100%;"></div>';

        window.tvWidget = new TradingView.widget({
            "autosize": true,
            "symbol": tvSymbol,
            "interval": "1",
            "timezone": "Etc/UTC",
            "theme": "dark",
            "style": "1",
            "locale": "en",
            "toolbar_bg": "#f1f3f6",
            "enable_publishing": false,
            "allow_symbol_change": true,
            "container_id": "tradingview_widget",
            "backgroundColor": "rgba(2, 6, 23, 1)",
            "gridColor": "rgba(255, 255, 255, 0.02)",
            "save_image": false,
            "hide_side_toolbar": false
        });
    }

    function selectAsset(symbol, perc, price, exchanges_id) {
        currentAsset = symbol;
        $('#show_market').text(symbol);
        $('#active-asset-symbol').val(symbol);
        $('#payout-perc').text(perc);
        $('#percentage_order').val(perc);
        $('#active-price-display').text('$' + parseFloat(price).toFixed(2));
        $('#asset_list').modal('hide');

        // Update Logo
        let logoUrl = getLogoUrl(symbol, exchanges_id);
        $('#active-asset-logo').html(`<img src="${logoUrl}" onerror="this.src='${window.APP_URL}/api/stock-logo/${symbol}?v=1.1.1'" style="width: 30px; height: 30px; border-radius: 8px; object-fit: contain;">`);
        
        $('#initial-ex-id').val(exchanges_id);

        initChart(symbol, exchanges_id);
        updatePayout();
    }

    function updatePayout() {
        let amount = parseFloat($('#trade-amount').val()) || 0;
        let perc = parseFloat($('#percentage_order').val()) || 0;
        let payout = amount + (amount * (perc / 100));
        $('#expected-payout').text('$' + payout.toLocaleString());
    }

    // Trade Validation & Execution Result System (Triggers Backend Cron equivalent)
    async function executeResult() {
        try {
            await fetch("{{ route('execute_result_after_time') }}");
        } catch (e) {
            console.log("Execute result check failed", e);
        }
    }

    // Trade Polling & Updates
    function fetchTrades() {
        fetch("{{ route('trade.js') }}")
            .then(res => res.json())
            .then(data => {
                $('#trade_count').text(data.count_trade);
                let html = '';
                if (data.data.length === 0) {
                    html = '<tr><td colspan="8" class="text-center py-5 text-secondary">No active trades running.</td></tr>';
                } else {
                    data.data.forEach(trade => {
                        let statusColor = trade.status === 'win' ? 'text-success' : (trade.status === 'loss' ? 'text-danger' : 'text-white');
                        html += `
                        <tr class="portfolio-row align-middle" style="background: rgba(255,255,255,0.02);">
                            <td class="py-3 border-0" style="border-radius: 10px 0 0 10px; padding-left: 1rem;">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="${getLogoUrl(trade.symbol)}" onerror="this.onerror=null; this.src='{{ asset('assets/img/profit.svg') }}';" 
                                         style="width: 32px; height: 32px; border-radius: 8px; background: rgba(0,0,0,0.3); padding: 5px; border: 1px solid rgba(255,255,255,0.1); object-fit: contain;">
                                    <div class="outfit font-weight-bold portfolio-fund-name" style="font-size: 0.95rem;">${trade.symbol}</div>
                                </div>
                            </td>
                            <td class="py-3 border-0 align-middle"><span class="badge" style="background: rgba(14, 165, 233, 0.1); color: #0ea5e9; font-weight: 800; font-size: 10px;">${trade.leverage || '1:1'}</span></td>
                            <td class="py-3 border-0 align-middle"><span class="badge ${trade.type === 'call' ? 'bg-success-glass text-success' : 'bg-danger-glass text-danger'}" style="font-size: 10px; font-weight: 800;">${trade.type.toUpperCase()}</span></td>
                            <td class="py-3 border-0 align-middle outfit fw-bold">$${parseFloat(trade.amount).toLocaleString()}</td>
                            <td class="py-3 border-0 align-middle outfit text-secondary" style="font-size: 0.9rem;">${trade.strike_rate ? parseFloat(trade.strike_rate).toFixed(5) : 'N/A'}</td>
                            <td class="py-3 border-0 align-middle">
                                ${trade.approval_status === 'pending' ?
                                `<div class="pending-progress">
                                    <div class="d-flex justify-content-between w-100"><span class="progress-label text-warning">Pending Approval</span><span class="progress-pct">99%</span></div>
                                    <div class="progress-track" style="background: rgba(255,255,255,0.05);"><div class="progress-fill" style="width: 99%; animation: none; background: var(--bs-warning);"></div></div>
                                </div>` :
                                (trade.status === 'pending' ? 
                                `<div class="pending-progress">
                                    <div class="d-flex justify-content-between w-100"><span class="progress-label">Processing</span><span class="progress-pct">93%</span></div>
                                    <div class="progress-track"><div class="progress-fill"></div></div>
                                </div>` : 
                                `<span class="badge ${trade.status === 'win' ? 'bg-success-glass text-success' : 'bg-danger-glass text-danger'}" style="padding: 6px 12px; border-radius: 8px; font-size: 10px; font-weight: 800; letter-spacing: 1px;">
                                    ${trade.status.toUpperCase()}
                                </span>`)}
                            </td>
                            <td class="py-3 border-0 align-middle small text-secondary" style="font-weight: 600;">${trade.expire_time}</td>
                            <td class="py-3 border-0 align-middle text-end" style="border-radius: 0 10px 10px 0; padding-right: 1rem;">
                                ${trade.approval_status === 'pending' ? 
                                    `<span class="badge bg-warning-glass text-warning py-2 px-3" style="border-radius: 6px; font-size: 9px;"><i class="ri-loader-4-line spin-anim"></i> WAITING</span>` 
                                : (trade.status === 'pending' ? 
                                    (trade.user_exit === 'on' ? 
                                        `<button onclick="exitTrade(${trade.id})" class="btn btn-premium btn-sm py-1 px-3" style="font-size: 9px; font-weight: 800; letter-spacing: 1px;">
                                            EXIT
                                        </button>` : 
                                        `<span class="badge bg-black-soft text-muted py-2 px-3" style="border-radius: 6px; font-size: 9px;"><i class="ri-lock-2-line"></i></span>`) 
                                    : `<i class="ri-checkbox-circle-fill text-success" style="font-size: 1.2rem;"></i>`)}
                            </td>
                        </tr>
                        `;
                        if(trade.modal === 'open') {
                            toastr[trade.status === 'win' ? 'success' : 'error'](`Trade Finished: ${trade.status.toUpperCase()} ($${trade.p_l})`);
                            closeTradeModal(trade.id);
                        }
                    });
                }
                $('#active-trades-list').html(html);
                
                // Check if any button is waiting for a trade approval
                $('#btn-buy, #btn-sell').each(function() {
                    const pendingId = $(this).attr('data-pending-order');
                    if (pendingId) {
                        // Check if this ID is still in data.data with approval_status == 'pending'
                        const tradeData = data.data.find(t => t.id == pendingId);
                        if (!tradeData || tradeData.approval_status !== 'pending') {
                            // Trade is either approved or doesn't exist anymore, restore button!
                            clearInterval($(this).data('loader-interval'));
                            $(this).html($(this).data('original-html'));
                            $(this).removeData('loader-interval');
                            $(this).removeAttr('data-pending-order');
                            $(this).attr('disabled', false);
                            
                            // Re-enable the other button too
                            const otherBtn = $(this).attr('id') === 'btn-buy' ? $('#btn-sell') : $('#btn-buy');
                            otherBtn.attr('disabled', false);
                        }
                    }
                });
            })
            .catch(err => {
                console.error("Polling timeout/error", err);
                if (err instanceof SyntaxError || err.message.includes('JSON')) {
                    window.location.reload();
                }
            });
    }

    function closeTradeModal(id) {
        fetch("{{ route('dashboard.trade.close-modal', ['id' => 'TMP_ID']) }}".replace('TMP_ID', id));
    }

    function exitTrade(id) {
        fetch("{{ route('dashboard.trade.exit', ['id' => 'EXIT_ID']) }}".replace('EXIT_ID', id))
        .then(res => res.json())
        .then(data => {
            if (data.status) { toastr.success(data.status); fetchTrades(); }
            else { toastr.error(data.error); }
        })
        .catch(err => toastr.error("Exit failed: " + err.message));
    }

    function submitTrade(type) {
        const formData = {
            asset: $('#active-asset-symbol').val(),
            amount: $('#trade-amount').val(),
            expiretime: $('#expiretime').val(),
            leverage: $('#leverage').val(),
            type: type,
            rate: $('#rate').val(),
            _token: "{{ csrf_token() }}"
        };

        if (!formData.amount || formData.amount <= 0) return toastr.error("Invalid amount");
        if (!formData.rate || parseFloat(formData.rate) <= 0) return toastr.error("Rate not loaded yet — please wait");

        $('#btn-buy, #btn-sell').attr('disabled', true);
        $('#trade-loader').show();

        fetch("{{ route('trade') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'pending_approval') {
                const btn = type === 'call' ? $('#btn-buy') : $('#btn-sell');
                
                // Save original content
                if (!btn.data('original-html')) {
                    btn.data('original-html', btn.html());
                }
                
                // Start loader 0-100
                let pct = 0;
                btn.html(`<i class="ri-loader-4-line ri-spin"></i> <span class="loader-pct">0</span>%`);
                const intervalId = setInterval(() => {
                    pct++;
                    if(pct > 100) pct = 0;
                    btn.find('.loader-pct').text(pct);
                }, 20); // 100 * 20ms = 2s per cycle
                
                btn.data('loader-interval', intervalId);
                btn.attr('data-pending-order', data.order_id);
                
                toastr.info("Trade placed. Awaiting approval...");
                fetchTrades();
            } else if (data.status) {
                toastr.success(data.status);
                
                // Restore buttons
                $('#btn-buy, #btn-sell').each(function() {
                    if ($(this).data('loader-interval')) {
                        clearInterval($(this).data('loader-interval'));
                        $(this).html($(this).data('original-html'));
                        $(this).removeData('loader-interval');
                        $(this).removeAttr('data-pending-order');
                    }
                    $(this).attr('disabled', false);
                });
                
                fetchTrades();
            } else {
                toastr.error(data.error);
            }
        })
        .catch(err => toastr.error("Trade request failed: " + err.message))
        .finally(() => {
            $('#btn-buy, #btn-sell').attr('disabled', false);
            $('#trade-loader').hide();
        });
    }

    // Real-time Price Polling
    async function pollPrices() {
        if (!currentAsset) return;
        
        let exId = $('#initial-ex-id').val();
        let symbolForUrl = currentAsset.replace('/', '-');
        let lookupSymbol = mirrorMap[currentAsset] || currentAsset;
        let binanceSymbol = lookupSymbol.replace('/', '').toUpperCase();
        if (binanceSymbol.includes('USD') && !binanceSymbol.includes('USDT')) binanceSymbol = binanceSymbol.replace('USD', 'USDT');

        try {
            // Attempt direct Binance API for Crypto first (Real-time speed)
            if (exId == 2 || binanceSymbol.includes('USDT') || binanceSymbol.includes('BTC')) {
                const bResponse = await fetch(`https://api.binance.com/api/v3/ticker/price?symbol=${binanceSymbol}`);
                if (bResponse.ok) {
                    const bData = await bResponse.json();
                    if (bData.price) {
                        let price = parseFloat(bData.price);
                        $('#rate').val(price);
                        $('#active-price-display').text('$' + price.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                        return; // Successfully got direct price
                    }
                }
            }

            // Fallback: Use global APP_URL for consistent routing
            const assetResponse = await fetch(`${window.APP_URL}/dashboard/asset-price/${symbolForUrl}?exchange_id=${exId}`);
            const assetData = await assetResponse.json();
            if (assetData.price) {
                let price = parseFloat(assetData.price);
                $('#rate').val(price);
                $(`#${currentAsset}-price`).text('$' + price.toFixed(2));
                $('#ob-mid-price').text('$' + price.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                
                // Update Live Price Display
                $('#active-price-display').text('$' + price.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            }
        } catch (e) {
            console.error("Dashboard Poll fail", e);
        }
    }

    $(document).ready(function() {
        // Get initial exchange ID for chart prefixing
        let initialExId = "{{ $asset->where('symbols', $default_asset ?? 'BTCUSD')->first()->exchanges_id ?? 1 }}";
        try {
            initChart(currentAsset, initialExId);
        } catch (e) {
            console.warn("TradingView init failed (non-blocking)", e);
        }
        
        // Set initial logo
        let logoUrl = getLogoUrl(currentAsset, initialExId);
        $('#active-asset-logo').html(`<img src="${logoUrl}" onerror="this.src='{{ asset('assets/img/profit.svg') }}'" style="width: 30px; height: 30px; border-radius: 8px; object-fit: contain;">`);

        setInterval(fetchTrades, 3000);
        setInterval(pollPrices, 2000); // Poll prices every 2s
        setInterval(executeResult, 4000); // Trigger backend trade completion check
        
        fetchTrades();
        pollPrices();

        $('#trade-amount').on('input', updatePayout);

        $('#btn-buy').on('click', () => submitTrade('call'));
        $('#btn-sell').on('click', () => submitTrade('put'));
        
        // Search functionality
        $('#market-search').on('keyup', function() {
            let val = $(this).val().toLowerCase();
            $('#market-grid .asset-card').each(function() {
                let symbol = $(this).find('.outfit').text().toLowerCase();
                $(this).parent().toggle(symbol.includes(val));
            });
        });
    });

    async function filterCategory(id) {
        const res = await fetch("{{ route('dashboard.asset.show', ['id' => 'TMP_ID']) }}".replace('TMP_ID', id));
        const data = await res.json();
        let html = '';
        data.data.forEach(a => {
            if(a.mirror_symbol) { mirrorMap[a.symbols] = a.mirror_symbol; }
            let logoUrl = a.logo_url || getLogoUrl(a.symbols, a.exchanges_id);
            html += `
            <div class="col-lg-3 col-md-4 col-6 mb-2">
                <div class="asset-card glass-card p-3 d-flex flex-column gap-2 h-100" onclick="selectAsset('${a.symbols}', '${a.percentage}', '${a.buy}', '${a.exchanges_id}')">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="symbol-logo-wrapper">
                            <img src="${logoUrl}" onerror="this.onerror=null; this.src='{{ asset('assets/img/profit.svg') }}';" 
                                 style="width: 36px; height: 36px; border-radius: 10px; background: rgba(0,0,0,0.3); padding: 4px; object-fit: contain; border: 1px solid rgba(255,255,255,0.05);">
                        </div>
                        <span class="badge bg-success-soft text-success px-2" style="font-size: 0.7rem;">+${a.percentage}%</span>
                    </div>
                    <div class="mt-auto">
                        <div class="outfit font-weight-bold text-white mb-0" style="font-size: 0.95rem; line-height: 1.2;">${a.symbols}</div>
                        <div class="small text-secondary tracking-widest" style="font-family: 'Inter'; font-size: 0.75rem;">$${parseFloat(a.buy).toFixed(2)}</div>
                    </div>
                </div>
            </div>`;
        });
        $('#market-grid').html(html);
    }

    // Staggered Entrance Animation
    window.addEventListener('load', () => {
        if (typeof anime !== 'undefined') {
            anime({
                targets: '.bento-col-4, .main-trading-banner',
                translateY: [40, 0],
                opacity: [0, 1],
                delay: anime.stagger(150),
                easing: 'easeOutSpring(1, 80, 10, 0)',
                duration: 1200
            });
            
            anime({
                targets: '.bento-col-9, .bento-col-3',
                translateY: [60, 0],
                opacity: [0, 1],
                delay: anime.stagger(150, {start: 300}),
                easing: 'easeOutSpring(1, 80, 10, 0)',
                duration: 1500
            });
        }
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
@endpush


