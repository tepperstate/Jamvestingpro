@extends('layouts.user.app')

@section('title', 'Stocks & ETFs')

@section('content')
<style>
/* Glassmorphism Mobile Trading Design - Jamvesting Pro */
:root {
    --gold-primary: #990000;
    --gold-glow: rgba(153, 0, 0, 0.4);
    --glass-bg: rgba(20, 22, 28, 0.75);
    --glass-border: rgba(153, 0, 0, 0.15);
}
body, .content-wrapper, .wrapper {
    background: #0d0e12 !important;
    background-image: radial-gradient(circle at 50% 0%, #1a1c24 0%, #0d0e12 70%) !important;
    color: #e0e6ed !important;
    font-family: 'Inter', -apple-system, sans-serif !important;
}
.box, .card, .nav-tabs-custom, .tab-content {
    background: var(--glass-bg) !important;
    backdrop-filter: blur(16px) !important;
    -webkit-backdrop-filter: blur(16px) !important;
    border: 1px solid var(--glass-border) !important;
    border-radius: 20px !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5) !important;
    margin-bottom: 20px !important;
    overflow: hidden;
}
.box-header {
    border-bottom: 1px solid var(--glass-border) !important;
    background: transparent !important;
}
.btn-success, .btn-primary, .btn-info {
    background: linear-gradient(135deg, #f5d76e 0%, #990000 100%) !important;
    border: none !important;
    color: #0d0e12 !important;
    font-weight: 800 !important;
    border-radius: 12px !important;
    box-shadow: 0 4px 15px var(--gold-glow) !important;
    text-transform: uppercase !important;
    letter-spacing: 1px !important;
    transition: all 0.3s ease !important;
}
.btn-success:active, .btn-primary:active {
    transform: translateY(2px) !important;
    box-shadow: 0 2px 8px var(--gold-glow) !important;
}
.text-success, .text-info, span[style*="springgreen"], span[style*="color:green"] {
    color: var(--gold-primary) !important;
    text-shadow: 0 0 10px var(--gold-glow) !important;
}
input.form-control, select.form-control, .input-group-text {
    background: rgba(0, 0, 0, 0.4) !important;
    border: 1px solid var(--glass-border) !important;
    color: #fff !important;
    border-radius: 12px !important;
    padding: 12px !important;
}
input.form-control:focus {
    border-color: var(--gold-primary) !important;
    box-shadow: 0 0 8px var(--gold-glow) !important;
}
.table {
    color: #e0e6ed !important;
}
.table th {
    border-bottom: 2px solid var(--glass-border) !important;
    color: var(--gold-primary) !important;
    text-transform: uppercase;
    font-size: 12px;
}
.table td {
    border-top: 1px solid rgba(255,255,255,0.05) !important;
}
.nav-tabs .nav-link.active {
    background: transparent !important;
    color: var(--gold-primary) !important;
    border-bottom: 3px solid var(--gold-primary) !important;
}
.nav-tabs .nav-link {
    color: #8892a0 !important;
    border: none !important;
}
/* Responsive Mobile Adjustments */
@media (max-width: 768px) {
    .content, .container-full { padding: 10px !important; }
    .box { border-radius: 16px !important; padding: 15px !important; }
    h3, h4 { font-size: 1.2rem !important; }
    .row { margin-left: -5px; margin-right: -5px; }
    .col-12 { padding-left: 5px; padding-right: 5px; }
    
    /* Convert Tables to Cards */
    .table-responsive { overflow-x: hidden !important; border: none !important; }
    .table thead { display: none; }
    .table, .table tbody, .table tr, .table td { display: block; width: 100%; }
    .table tr.portfolio-row {
        background: rgba(255,255,255,0.02) !important;
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 20px !important;
        margin-bottom: 16px;
        padding: 16px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px 0;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    }
    .table tr.portfolio-row td { border: none !important; padding: 0 !important; border-radius: 0 !important; }
    
    /* Security Column (Full Width Header) */
    .table tr.portfolio-row td:first-child {
        flex: 0 0 100%;
        border-bottom: 1px solid rgba(255,255,255,0.06) !important;
        padding-bottom: 12px !important;
    }
    
    /* Grid items for data */
    .table tr.portfolio-row td:not(:first-child):not(:last-child) {
        flex: 1 1 48%;
        text-align: left !important;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
    }
    
    /* Add Labels using nth-child */
    .table tr.portfolio-row td:not(:first-child):not(:last-child)::before {
        font-size: 9px; color: rgba(255,255,255,0.4); text-transform: uppercase; font-weight: 800; margin-bottom: 6px; display: block; letter-spacing: 0.5px;
    }
    
    /* Portfolio Table */
    div[data-portfolio-section] .table tr.portfolio-row td:nth-child(2)::before { content: 'LOTS'; }
    div[data-portfolio-section] .table tr.portfolio-row td:nth-child(3)::before { content: 'AVG COST'; }
    div[data-portfolio-section] .table tr.portfolio-row td:nth-child(4)::before { content: 'MARKET VALUE'; }
    div[data-portfolio-section] .table tr.portfolio-row td:nth-child(5)::before { content: 'PERFORMANCE'; }
    
    /* Market Table */
    .table-responsive:not(:first-of-type) .table tr.portfolio-row td:nth-child(2)::before { content: 'TREND'; }
    .table-responsive:not(:first-of-type) .table tr.portfolio-row td:nth-child(3)::before { content: 'VARIANCE'; }
    .table-responsive:not(:first-of-type) .table tr.portfolio-row td:nth-child(4)::before { content: 'UNIT PRICE'; }
    .table-responsive:not(:first-of-type) .table tr.portfolio-row td:nth-child(5)::before { content: 'VOLUME'; }
    .table-responsive:not(:first-of-type) .table tr.portfolio-row td:nth-child(6)::before { content: 'CAPITALIZATION'; }
    
    /* Execute Column */
    .table tr.portfolio-row td:last-child {
        flex: 0 0 100%;
        text-align: center !important;
        margin-top: 4px;
    }
    .table tr.portfolio-row td:last-child button {
        width: 100%;
        padding: 12px !important;
        font-size: 13px !important;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(153, 0, 0, 0.2) !important;
    }
    
    /* Fix global search padding */
    .search-glass { width: 100% !important; margin-top: 15px; }
    .market-navigation { padding-bottom: 10px; margin-bottom: 20px; }
    .market-tab { font-size: 11px; padding: 8px 12px; border-radius: 8px; background: rgba(255,255,255,0.05); white-space: nowrap; }
    .market-tab.active { background: var(--gold-primary); color: #000 !important; font-weight: 700; }
}
</style>
@php
    $logoMap = [];
    if(isset($stocks)){
        foreach($stocks as $s) {
            $logoMap[$s->symbol] = \App\Services\AssetLogoService::getLogoUrl($s->symbol, 'stock', $s->image ?? '');
        }
    }
    if(isset($portfolio)){
        foreach($portfolio as $p) {
            $logoMap[$p->symbol] = \App\Services\AssetLogoService::getLogoUrl($p->symbol, 'stock', $p->image ?? '');
        }
    }
@endphp
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<div class="container-fluid py-4" style="max-width: 1600px;">
    <style>
        .glass-card-premium { transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); border: 1px solid rgba(255,255,255,0.08); background: #000000 !important; backdrop-filter: blur(20px); }
        .glass-card-premium:hover { border-color: rgba(14, 165, 233, 0.4) !important; box-shadow: 0 20px 40px rgba(0,0,0,0.4), 0 0 20px rgba(14, 165, 233, 0.1); }
        
        .portfolio-row:hover { background: rgba(255,255,255,0.06) !important; }
        .portfolio-fund-name { transition: color 0.3s ease; }
        .portfolio-row:hover .portfolio-fund-name { color: #0ea5e9 !important; }

        .modal-content.glass-card-premium {
            background: rgba(0, 0, 0, 0.8) !important;
            backdrop-filter: blur(30px) saturate(150%);
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            box-shadow: 0 40px 100px rgba(0,0,0,0.8) !important;
        }
        
        .micro-label { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; color: rgba(255,255,255,0.4); margin-bottom: 0.5rem; }
        .hero-stat { font-family: 'Outfit', sans-serif; font-weight: 700; letter-spacing: -1px; }

        .input-amount-field-premium {
            background: rgba(0, 0, 0, 0.3) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: white !important;
            padding: 1rem 1.5rem !important;
            font-size: 1.5rem !important;
            font-weight: 700 !important;
            border-radius: 16px !important;
            width: 100% !important;
            transition: all 0.3s ease !important;
        }
        .input-amount-field-premium:focus {
            border-color: #0ea5e9 !important;
            background: rgba(0, 0, 0, 0.5) !important;
            box-shadow: 0 0 20px rgba(14, 165, 233, 0.2) !important;
        }
    </style>

    <!-- Modern Header -->
    <div class="d-flex flex-column mb-4 gap-3" data-aos="fade-down">
        <div>
            <div class="d-flex align-items-center flex-wrap gap-2">
                <h1 class="outfit mb-0 text-white" style="font-size: 1.8rem; line-height: 1.2;">Stocks & ETFs</h1>
            </div>
            <p class="text-secondary mb-0" style="font-size: 0.85rem;">Overview of notes regarding your investment</p>
        </div>
        <div class="d-flex gap-2 w-100">
            <div class="input-group search-glass flex-grow-1">
                <span class="input-group-text bg-transparent border-0 text-secondary" style="padding: 0.25rem 0.5rem;"><i class="ri-search-2-line"></i></span>
                <input type="text" id="global-stock-search" class="form-control bg-transparent border-0 text-white form-control-sm" placeholder="Search stocks...">
            </div>
            <button class="btn btn-sm btn-premium-outline text-nowrap"><i class="ri-export-line"></i> Export</button>
        </div>
    </div>

    <!-- My Portfolio Section -->
    <div class="row mb-5" data-aos="fade-up" data-aos-delay="150" data-portfolio-section style="{{ (!isset($portfolio) || $portfolio->count() === 0) ? 'display:none;' : '' }}">
        <div class="col-12">
            <div class="glass-card-premium p-4" style="border-radius: 24px;">
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <div>
                        <h4 class="outfit font-weight-bold mb-1 text-white">Active Equity Positions</h4>
                        <div class="micro-label" style="margin-bottom: 0;">Market Overview</div>
                    </div>
                    <div class="badge" data-portfolio-count style="background: rgba(14, 165, 233, 0.1); color: #0ea5e9; padding: 8px 16px; border-radius: 12px; font-weight: 800; font-size: 11px; letter-spacing: 1.5px; border: 1px solid rgba(14, 165, 233, 0.2);">
                        {{ isset($portfolio) ? $portfolio->count() : 0 }} ASSETS HELD
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table mb-0" style="border-collapse: separate; border-spacing: 0 8px;">
                        <thead>
                            <tr>
                                <th class="micro-label border-0 pb-3">SECURITY</th>
                                <th class="micro-label border-0 pb-3">LOTS</th>
                                <th class="micro-label border-0 pb-3">AVG COST</th>
                                <th class="micro-label border-0 pb-3">MARKET VALUE</th>
                                <th class="micro-label border-0 pb-3 text-end">PERFORMANCE</th>
                                <th class="micro-label border-0 pb-3 text-end">EXECUTE</th>
                            </tr>
                        </thead>
                        <tbody data-portfolio-tbody>
                            @if(isset($portfolio))
                            @foreach($portfolio as $pos)
                            <tr class="portfolio-row pointer" onclick="showStockDetails('{{ $pos->stock_id }}')" style="background: rgba(255,255,255,0.03); transition: all 0.3s ease;">
                                <td class="py-3 border-0" style="border-radius: 12px 0 0 12px; padding-left: 1.5rem;">
                                    <div class="d-flex align-items-center gap-3">
                                        <x-asset-logo :symbol="$pos->symbol" size="sm" assetType="stock" />
                                        <div>
                                            <div class="text-white fw-bold portfolio-fund-name" style="font-size: 0.95rem;">{{ $pos->symbol }}</div>
                                            <div class="text-secondary small" style="font-size: 0.75rem; opacity: 0.6;">{{ $pos->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 border-0 align-middle font-weight-bold text-white">{{ number_format($pos->units, 4) }}</td>
                                <td class="py-3 border-0 align-middle text-secondary small">${{ number_format($pos->buy, 2) }}</td>
                                <td class="py-3 border-0 align-middle outfit fw-bold text-white">${{ number_format($pos->units * $pos->buy, 2) }}</td>
                                <td class="py-3 border-0 align-middle text-end">
                                    <span class="text-success fw-bold" style="font-size: 0.9rem;">+0.00%</span>
                                </td>
                                <td class="py-3 border-0 align-middle text-end" style="border-radius: 0 12px 12px 0; padding-right: 1.5rem;">
                                    <button class="btn btn-premium btn-sm py-1 px-3" onclick="showStockDetails('{{ $pos->stock_id }}')" style="font-size: 10px; letter-spacing: 1px; font-weight: 800;">TRADE</button>
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Market Navigation & Table -->
    <div class="glass-card-premium p-4" data-aos="fade-up" data-aos-delay="200" style="border-radius: 28px;">
        <!-- Market Navigation -->
        <div class="market-navigation d-flex flex-nowrap gap-3 border-bottom border-white border-opacity-10 mb-4 overflow-auto pb-3" style="scrollbar-width: none; white-space: nowrap; -webkit-overflow-scrolling: touch;">
            <a href="{{ route('stocks.trade', ['market' => 'nasdaq', 'type' => $current_type]) }}" class="flex-shrink-0 market-tab {{ $current_market == 'nasdaq' ? 'active' : 'text-muted' }}" data-market="nasdaq">NASDAQ GLOBAL</a>
            <a href="{{ route('stocks.trade', ['market' => 's&p500', 'type' => $current_type]) }}" class="flex-shrink-0 market-tab {{ $current_market == 's&p500' ? 'active' : 'text-muted' }}" data-market="s&p500">S&P 500 COMPOSITE</a>
            <a href="{{ route('stocks.trade', ['market' => 'dow', 'type' => $current_type]) }}" class="flex-shrink-0 market-tab {{ $current_market == 'dow' ? 'active' : 'text-muted' }}" data-market="dow">DOW JONES INDUSTRIALS</a>
            <a href="{{ route('stocks.trade', ['market' => 'nyse', 'type' => $current_type]) }}" class="flex-shrink-0 market-tab {{ $current_market == 'nyse' ? 'active' : 'text-muted' }}" data-market="nyse">NYSE LISTED</a>
        </div>

        <div class="table-responsive">
            <table class="table mb-0" style="border-collapse: separate; border-spacing: 0 4px;">
                <thead>
                    <tr>
                        <th class="micro-label border-0 pb-3">SECURITY</th>
                        <th class="micro-label border-0 pb-3">TREND</th>
                        <th class="micro-label border-0 pb-3 text-end">VARIANCE</th>
                        <th class="micro-label border-0 pb-3 text-end">UNIT PRICE</th>
                        <th class="micro-label border-0 pb-3 text-end">VOLUME</th>
                        <th class="micro-label border-0 pb-3 text-end">CAPITALIZATION</th>
                        <th class="micro-label border-0 pb-3 text-end">EXECUTE</th>
                    </tr>
                </thead>
                <tbody id="stock-table-body">
                    @foreach($stocks as $stock)
                    <tr class="portfolio-row pointer" onclick="showStockDetails('{{ $stock->id }}')" style="background: rgba(255,255,255,0.02); transition: all 0.2s ease;">
                        <td class="py-3 border-0" style="border-radius: 8px 0 0 8px; padding-left: 1rem;">
                            <div class="d-flex align-items-center gap-3">
                                <x-asset-logo :symbol="$stock->symbol" size="sm" assetType="stock" />
                                <div>
                                    <div class="fw-bold text-white mb-0 portfolio-fund-name" style="font-size: 0.9rem;">{{ $stock->symbol }}</div>
                                    <div class="text-secondary" style="font-size: 10px; font-weight: 700; opacity: 0.5;">{{ $stock->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 border-0 align-middle">
                            <div id="chart-{{ $stock->symbol }}" style="width: 70px; height: 25px; opacity: 0.8;">
                                @php
                                    $var = $stock->changes_percentage != 0 ? $stock->changes_percentage : (crc32($stock->symbol) % 1000) / 100 - 5;
                                    $color = $var >= 0 ? '#ff3333' : '#ef4444';
                                    $pts = [[0, $var >= 0 ? 20 : 5]];
                                    for ($i = 1; $i <= 5; $i++) {
                                        $x = $i * 10;
                                        $y = $var >= 0 
                                            ? (20 - (15 * ($i/5)) + (sin(crc32($stock->symbol) + $i) * 5))
                                            : (5 + (15 * ($i/5)) + (sin(crc32($stock->symbol) + $i) * 5));
                                        $y = max(2, min(23, $y));
                                        $pts[] = [$x, $y];
                                    }
                                    $pts[] = [60, $var >= 0 ? 5 : 20];
                                    $path = 'M' . implode(' L', array_map(function($p) { return $p[0].' '.round($p[1], 1); }, $pts));
                                @endphp
                                <svg viewBox="0 0 60 25" class="w-100 h-100">
                                    <path d="{{ $path }}" fill="none" stroke="{{ $color }}" stroke-width="2" stroke-linecap="round"></path>
                                </svg>
                            </div>
                        </td>
                        <td class="py-3 border-0 align-middle text-end">
                            <span id="var-{{ $stock->symbol }}" class="fw-bold {{ $var >= 0 ? 'text-success' : 'text-danger' }}" style="font-size: 0.85rem;" data-var="{{ $var }}">
                                {{ $var >= 0 ? '+' : '' }}{{ number_format($var, 2) }}%
                            </span>
                        </td>
                        <td class="py-3 border-0 align-middle outfit fw-bold text-white text-end" style="font-size: 0.95rem;">$<span id="price-{{ $stock->symbol }}">{{ number_format($stock->buy, 2) }}</span></td>
                        <td class="py-3 border-0 align-middle text-secondary small text-end" style="font-weight: 600;">
                            <span id="vol-{{ $stock->symbol }}">
                                @php
                                    $vol = $stock->volume > 0 ? $stock->volume : abs(crc32($stock->symbol)) % 10000000 + 100000;
                                @endphp
                                @if($vol >= 1000000)
                                    {{ number_format($vol / 1000000, 2) }}M
                                @elseif($vol >= 1000)
                                    {{ number_format($vol / 1000, 2) }}K
                                @else
                                    {{ number_format($vol) }}
                                @endif
                            </span>
                        </td>
                        <td class="py-3 border-0 align-middle text-secondary small text-end" style="font-weight: 600;">
                            <span id="cap-{{ $stock->symbol }}">
                                @php
                                    $cap = abs(crc32($stock->symbol . 'cap')) % 2000 + 50;
                                    $capDec = abs(crc32($stock->symbol . 'dec')) % 99;
                                    echo '$' . $cap . '.' . sprintf('%02d', $capDec) . 'B';
                                @endphp
                            </span>
                        </td>
                        <td class="py-3 border-0 align-middle text-end" style="border-radius: 0 8px 8px 0; padding-right: 1rem;">
                            <button class="btn btn-premium btn-sm py-1 px-3" onclick="showStockDetails('{{ $stock->id }}')" style="font-size: 10px; letter-spacing: 1px; font-weight: 800;">TRADE</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-5 d-flex justify-content-center">
            <div class="pagination-wrapper">
                {{ $stocks->links() }}
            </div>
        </div>
    </div>
</div>

<!-- SIDE MODAL: Stock Details -->
<div class="modal fade modal-side" id="stockDetailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content glass-card-premium overflow-hidden">
            <div class="modal-header border-0 p-4 pb-0 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div id="detail-logo-wrapper">
                        <x-asset-logo symbol="" size="md" imgId="detail-logo" assetType="stock" />
                    </div>
                    <div>
                        <h4 class="outfit fw-bold text-white mb-0" id="detail-symbol">TICKER</h4>
                        <div class="text-muted small" id="detail-name">Company Name, Inc.</div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-icon-glass"><i class="ri-bookmark-line"></i></button>
                    <button class="btn btn-icon-glass"><i class="ri-notification-3-line"></i></button>
                    <button class="btn btn-icon-glass" data-dismiss="modal"><i class="ri-close-line"></i></button>
                </div>
            </div>
            <div class="modal-body p-4 custom-scrollbar" style="overflow-y: auto; height: calc(100% - 160px);">
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-end mb-2">
                        <div>
                            <div class="display-6 outfit fw-bold text-white mb-1">$<span id="detail-price">3,120.55</span></div>
                            <div class="text-success fw-bold small">+140.42 <i class="ri-arrow-right-up-line"></i> 4.50%</div>
                        </div>
                        <span class="badge badge-success-glass py-2 px-3">Market open</span>
                    </div>
                    
                    <!-- Time Toggles -->
                    <div class="d-flex gap-2 mb-4 bg-black-soft p-1 rounded-pill" style="width: fit-content;">
                        <button class="btn btn-xs btn-time active">1d</button>
                        <button class="btn btn-xs btn-time">1w</button>
                        <button class="btn btn-xs btn-time">1m</button>
                        <button class="btn btn-xs btn-time">3m</button>
                        <button class="btn btn-xs btn-time">1y</button>
                    </div>

                    <!-- Mini Chart Placeholder -->
                    <!-- Performance Chart -->
                    <div class="mini-chart-container mb-4" style="height: 250px; background: rgba(255,255,255,0.02); border-radius: 20px; border: 1px solid rgba(255,255,255,0.05); position: relative; overflow: hidden;">
                         <div id="asset-performance-chart" style="height: 100%; width: 100%;"></div>
                         <!-- Loading Overlay for Chart -->
                         <div id="chart-loader" class="position-absolute inset-0 d-flex align-items-center justify-content-center bg-dark-glass" style="display:none !important; z-index: 5;">
                            <div class="spinner-border text-primary spinner-border-sm" role="status"></div>
                         </div>
                    </div>

                    <!-- Market Insights (News) -->
                    <div class="market-news-section mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="outfit font-weight-bold text-white mb-0"><i class="ri-newspaper-line me-2 text-primary"></i>Latest News</h6>
                            <span class="badge badge-primary-glass small" id="news-count">0 Articles</span>
                        </div>
                        <div id="news-feed-container" class="news-scroll-area" style="max-height: 300px; overflow-y: auto; scrollbar-width: thin;">
                            <div class="text-center py-4 text-secondary small">
                                <i class="ri-loader-4-line animate-spin h4 d-block mb-2"></i>
                                Loading market insights...
                            </div>
                        </div>
                    </div>
                    <!-- Market Stats -->
                    <div class="row row-cols-2 g-3 mb-4">
                        <div class="col">
                            <div class="glass-card p-3 h-100" style="background: rgba(59, 130, 246, 0.05); border: 1px solid rgba(59, 130, 246, 0.15); border-radius: 16px;">
                                <small class="text-secondary d-block mb-1" style="font-size: 10px; letter-spacing: 0.5px; opacity: 0.8;">OPEN</small>
                                <span class="font-weight-bold outfit text-white" id="stat-open" style="font-size: 1.1rem;">-</span>
                            </div>
                        </div>
                        <div class="col">
                            <div class="glass-card p-3 h-100" style="background: rgba(255, 51, 51, 0.05); border: 1px solid rgba(255, 51, 51, 0.15); border-radius: 16px;">
                                <small class="text-secondary d-block mb-1" style="font-size: 10px; letter-spacing: 0.5px; opacity: 0.8;">HIGH</small>
                                <span class="font-weight-bold outfit text-white" id="stat-high" style="font-size: 1.1rem;">-</span>
                            </div>
                        </div>
                        <div class="col">
                            <div class="glass-card p-3 h-100" style="background: rgba(245, 158, 11, 0.05); border: 1px solid rgba(245, 158, 11, 0.15); border-radius: 16px;">
                                <small class="text-secondary d-block mb-1" style="font-size: 10px; letter-spacing: 0.5px; opacity: 0.8;">VOL</small>
                                <span class="font-weight-bold outfit text-white" id="stat-vol" style="font-size: 1.1rem;">-</span>
                            </div>
                        </div>
                        <div class="col">
                            <div class="glass-card p-3 h-100" style="background: rgba(139, 92, 246, 0.05); border: 1px solid rgba(139, 92, 246, 0.15); border-radius: 16px;">
                                <small class="text-secondary d-block mb-1" style="font-size: 10px; letter-spacing: 0.5px; opacity: 0.8;">P/E</small>
                                <span class="font-weight-bold outfit text-white" id="stat-pe" style="font-size: 1.1rem;">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- My Position Card -->
                    <div class="glass-card bg-black-soft p-4 mb-4" style="border-radius: 24px;">
                        <h5 class="outfit fw-bold text-white mb-4">My position</h5>
                        <div class="row g-4 mb-4">
                            <div class="col-6">
                                <div class="text-muted x-small mb-1">Lots</div>
                                <div class="text-white fw-bold" id="pos-lots">9,842</div>
                            </div>
                            <div class="col-6 text-end">
                                <div class="text-muted x-small mb-1">Avg. price</div>
                                <div class="text-white fw-bold" id="pos-avg-price">$0.00</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted x-small mb-1">Total value</div>
                                <div class="text-white fw-bold" id="pos-value">$1,116,763</div>
                            </div>
                            <div class="col-6 text-end">
                                <div class="text-muted x-small mb-1">Allocation</div>
                                <div class="d-flex align-items-center justify-content-end gap-2">
                                    <span class="text-white fw-bold">64%</span>
                                    <div class="allocation-circle"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row g-4">
                            <div class="col-6">
                                <div class="text-muted x-small mb-1">Today's profit</div>
                                <div class="text-success fw-bold" id="pos-day-profit">+$0.00</div>
                            </div>
                            <div class="col-6 text-end">
                                <div class="text-muted x-small mb-1">Total profit</div>
                                <div class="text-success fw-bold" id="pos-total-profit">+$0.00</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer border-0 p-4 d-flex gap-3">
                <button class="btn btn-outline-danger flex-grow-1 py-3 outfit fw-bold" onclick="openTradeFlow('sell')">Sell</button>
                <button class="btn btn-premium flex-grow-1 py-3 outfit fw-bold" onclick="openTradeFlow('buy')">Buy</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: Buy/Sell Flow -->
<div class="modal fade" id="stockTradeFlowModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card-premium overflow-hidden">
            <div class="modal-header border-0 p-4 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-icon-glass" onclick="backToDetails()"><i class="ri-arrow-left-line"></i></button>
                    <div id="trade-logo-wrapper">
                        <x-asset-logo symbol="" size="sm" imgId="trade-logo" assetType="stock" />
                    </div>
                    <h4 class="outfit fw-bold text-white mb-0"><span id="trade-symbol">MSFT</span> <span id="trade-type-label">Buy</span></h4>
                </div>
                <div class="dropdown">
                    <button id="order-type-btn" class="btn btn-sm btn-filter dropdown-toggle" data-toggle="dropdown" data-boundary="window">Market order</button>
                    <div class="dropdown-menu dropdown-menu-right search-glass p-1" style="width: 260px; z-index: 2000;">
                        <div class="order-type-item mb-1 active" onclick="selectOrderType('Market order', this)">
                            <div class="d-flex align-items-center gap-2">
                                <div class="btn-icon-glass-sm bg-success-glass text-success" style="width: 28px; height: 28px; font-size: 0.8rem;"><i class="ri-pulse-line"></i></div>
                                <div>
                                    <div class="text-white small fw-bold" style="font-size: 0.75rem;">Market order</div>
                                    <div class="text-muted" style="font-size: 0.65rem;">Buy at current market price</div>
                                </div>
                            </div>
                        </div>
                        <div class="order-type-item" onclick="selectOrderType('Limit order', this)">
                            <div class="d-flex align-items-center gap-2">
                                <div class="btn-icon-glass-sm bg-primary-glass text-primary" style="width: 28px; height: 28px; font-size: 0.8rem;"><i class="ri-arrow-left-right-line"></i></div>
                                <div>
                                    <div class="text-white small fw-bold" style="font-size: 0.75rem;">Limit order</div>
                                    <div class="text-muted" style="font-size: 0.65rem;">Buy at a price you set</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="btn btn-icon-glass" data-dismiss="modal"><i class="ri-close-line"></i></button>
            </div>
            <div class="modal-body p-4 custom-scrollbar" style="overflow-y: auto; max-height: 80vh;">
                <div class="mb-4">
                    <div class="text-muted x-small mb-1">Market price</div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="h2 outfit fw-bold text-white mb-0">$<span id="trade-market-price">3,120.55</span></div>
                        <span class="badge badge-success-glass">Market open</span>
                    </div>
                </div>

                <!-- Amount/Price Toggle -->
                <div class="d-flex border-bottom border-dark mb-4">
                    <button class="flex-grow-1 border-0 bg-transparent text-white fw-bold py-2 border-bottom border-primary" style="border-width: 2px !important;">Amount</button>
                    <button class="flex-grow-1 border-0 bg-transparent text-muted py-2">Price</button>
                </div>

                <div class="input-amount-wrapper mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="micro-label" id="buying-selling-label" style="margin-bottom: 0;">Buying amount</div>
                        <button class="btn btn-link text-primary x-small fw-bold p-0" onclick="setAllIn()">All in <i class="ri-add-circle-line"></i></button>
                    </div>
                    <input type="number" id="trade-amount-input" class="input-amount-field-premium" value="2" oninput="calculateTotal()" placeholder="0.00">
                </div>

                <div class="glass-card bg-black-soft p-3 mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">Total price</div>
                        <div class="text-white fw-bold outfit h4 mb-0">$<span id="trade-total">2,450.80</span></div>
                    </div>
                </div>

                <div class="d-flex justify-content-between text-muted x-small mb-4">
                    <span id="available-type-label">Available balance:</span>
                    <span class="text-white fw-bold" id="available-balance-label">${{ number_format(auth()->user()->balance->amount, 2) }}</span>
                </div>

                <button class="btn btn-premium w-100 py-3 outfit fw-bold" id="btn-place-order" onclick="submitTrade()">Place an order</button>
            </div>
        </div>
    </div>
</div>

<!-- SUCCESS MODAL -->
<div class="modal fade" id="stockSuccessModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card-premium p-5 text-center" style="max-width: 440px; margin: auto; border-radius: 32px;">
            <div class="mb-4">
                <div id="success-logo-wrapper" class="mx-auto mb-3">
                    <x-asset-logo symbol="" size="lg" imgId="success-logo" assetType="stock" />
                </div>
                <h4 class="outfit fw-bold text-white mb-2"><span id="success-symbol">TICKER</span></h4>
                <div class="badge badge-success-glass py-2 px-3">Success <i class="ri-checkbox-circle-fill"></i></div>
            </div>
            
            <div class="text-muted small mb-4" id="success-type-label">Market Buy</div>
            
            <div class="glass-card bg-black-soft p-4 mb-4">
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted small">Lots</span>
                    <span class="text-white fw-bold" id="success-lots">2</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted small">Commission</span>
                    <span class="text-white fw-bold">0.01%</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted small">Total value</span>
                    <span class="text-white fw-bold" id="success-total">$2,360,76</span>
                </div>
            </div>

            <button class="btn btn-premium w-100 py-3 mb-3 outfit fw-bold" data-dismiss="modal">Done</button>
            <a href="#" class="text-primary small fw-bold d-block"><i class="ri-file-list-3-line"></i> Order Detail</a>
        </div>
    </div>
</div>

<style>
    #stockTradeFlowModal .modal-content {
        background: #000000 !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important;
    }
    
    #stockTradeFlowModal .input-amount-field {
        background: rgba(255, 255, 255, 0.05) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        color: white !important;
        padding: 1.5rem !important;
        font-size: 2rem !important;
        font-family: 'Outfit', sans-serif !important;
        border-radius: 16px !important;
        width: 100% !important;
        text-align: center !important;
        transition: all 0.3s ease !important;
    }
    
    #stockTradeFlowModal .input-amount-field:focus {
        background: rgba(255, 255, 255, 0.08) !important;
        border-color: #0ea5e9 !important;
        outline: none !important;
        box-shadow: 0 0 20px rgba(14, 165, 233, 0.1) !important;
    }    .search-glass, .search-glass-sm { 
        background: rgba(0, 0, 0, 0.6); 
        backdrop-filter: blur(20px) saturate(180%);
        border: 1px solid rgba(255, 255, 255, 0.1); 
        border-radius: var(--radius-xl); 
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
    }
    .search-glass:focus-within, .search-glass-sm:focus-within { 
        border-color: rgba(14, 165, 233, 0.5); 
        background: rgba(0, 0, 0, 0.8); 
        box-shadow: 0 0 25px rgba(14, 165, 233, 0.15); 
    }
    
    .dropdown-menu.search-glass {
        border: 1px solid rgba(14, 165, 233, 0.3) !important;
        box-shadow: 0 10px 40px rgba(0,0,0,0.4) !important;
    }

    .market-tab { 
        font-size: 0.9rem; 
        font-weight: 700; 
        text-decoration: none !important; 
        transition: all 0.3s ease; 
        padding-bottom: 12px; 
        border: none; 
        background: none; 
        border-bottom: 2px solid transparent; 
        color: rgba(255, 255, 255, 0.5); 
        margin-right: 8px; 
        display: inline-block;
        position: relative;
    }
    .market-tab.active { 
        color: #0ea5e9 !important; 
        border-bottom-color: #0ea5e9; 
    }
    .market-tab:hover { 
        color: white !important; 
    }

    .btn-filter { 
        background: rgba(255, 255, 255, 0.03); 
        border: 1px solid rgba(255, 255, 255, 0.08); 
        color: rgba(255, 255, 255, 0.6); 
        border-radius: 100px; 
        padding: 0.6rem 1.25rem; 
        font-size: 0.8rem; 
        font-weight: 600; 
        white-space: nowrap; 
        transition: all 0.3s ease; 
        margin-right: 0.75rem; 
        display: inline-block; 
        backdrop-filter: blur(4px);
    }
    .btn-filter:hover {
        background: rgba(255, 255, 255, 0.08);
        color: white;
        border-color: rgba(255, 255, 255, 0.2);
    }
    .btn-filter.active { 
        background: rgba(14, 165, 233, 0.15); 
        border-color: rgba(14, 165, 233, 0.4); 
        color: #0ea5e9; 
        box-shadow: 0 0 15px rgba(14, 165, 233, 0.1);
    }

    .order-type-item {
        padding: 10px 15px;
        border-radius: 12px;
        transition: all 0.2s ease;
        cursor: pointer;
        border: 1px solid transparent;
    }
    .order-type-item:hover {
        background: rgba(255, 255, 255, 0.05);
    }
    .order-type-item.active {
        background: rgba(14, 165, 233, 0.1);
        border-color: rgba(14, 165, 233, 0.3);
    }

    .btn-icon-glass { 
        width: 2.5rem; 
        height: 2.5rem; 
        background: rgba(255,255,255,0.05); 
        border: 1px solid rgba(255, 255, 255, 0.1); 
        color: rgba(255, 255, 255, 0.7); 
        border-radius: 10px; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        transition: all 0.3s ease; 
        padding: 0; 
    }
    .btn-icon-glass:hover { 
        background: rgba(255,255,255,0.1); 
        border-color: rgba(255, 255, 255, 0.2);
        color: white; 
        transform: scale(1.05);
    }
    
    .ri-close-line { font-size: 1.25rem; }

    .bg-black-soft { background: rgba(0,0,0,0.3) !important; }
    .btn-xs { padding: 2px 10px; font-size: 0.7rem; font-weight: 700; border-radius: 99px; }
    .btn-time { background: transparent; border: none; color: var(--text-muted); }
    .btn-time.active { background: white; color: black; }

    .stat-box { background: rgba(255,255,255,0.02); padding: 12px; border-radius: 16px; border: 1px solid var(--glass-border); }
    
    /* Stealth Scrollbar */
    ::-webkit-scrollbar { width: 3px; height: 3px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.05); border-radius: 20px; }
    ::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.1); }
    .custom-scrollbar { scrollbar-width: none; -ms-overflow-style: none; }
    .custom-scrollbar::-webkit-scrollbar { display: none; }
    .x-small { font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.05em; }
    
    .allocation-circle { width: 14px; height: 14px; border-radius: 50%; border: 3px solid var(--accent-primary); border-top-color: transparent; transform: rotate(45deg); }

    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: var(--glass-border); border-radius: 10px; }
    
    .pagination-wrapper .pagination { gap: 4px; }
    .pagination-wrapper .page-link { background: rgba(255,255,255,0.02) !important; border: 1px solid var(--glass-border) !important; color: var(--text-muted) !important; border-radius: 8px !important; }
    .pagination-wrapper .page-item.active .page-link { background: var(--accent-primary) !important; border-color: var(--accent-primary) !important; color: white !important; }
    
    .pulse-ring { width: 100px; height: 100px; background: rgba(255, 51, 51, 0.2); border-radius: 50%; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); animation: pulse-ring 2s cubic-bezier(0.455, 0.03, 0.515, 0.955) infinite; }
    
    .order-type-item.active { background: rgba(14, 165, 233, 0.1); border-color: rgba(14, 165, 233, 0.2); }
    .btn-icon-glass-sm { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 8px; display: flex; align-items: center; justify-content: center; }
</style>

@include('mobile.components.bottom-nav')

@endsection


@push('js')
<script>
    let logoMap = @json($logoMap ?? []);
    function getLogoUrl(symbol) {
        if(logoMap[symbol]) return logoMap[symbol];
        return `${window.APP_URL}/api/stock-logo/${symbol}?type=stock`;
    }

    let currentStock = null;
    let stocksData = @json($stocks->keyBy('id'));
    let portfolioData = @json($portfolio->keyBy('symbol'));

    let performanceChart = null;

    function initPerformanceChart() {
        if (performanceChart) return;
        const options = {
            series: [{ name: 'Price', data: [150, 152, 148, 155, 153, 158, 160] }],
            chart: { 
                type: 'area', 
                height: '100%', 
                toolbar: { show: false }, 
                sparkline: { enabled: true },
                animations: { enabled: true, easing: 'easeinout', speed: 800 }
            },
            stroke: { curve: 'smooth', width: 2, colors: ['#0ea5e9'] },
            fill: { 
                type: 'gradient',
                gradient: { shadeIntensity: 1, opacityFrom: 0.45, opacityTo: 0.05, stops: [0, 90, 100] } 
            },
            colors: ['#0ea5e9'],
            tooltip: { theme: 'dark', x: { show: false } }
        };
        performanceChart = new ApexCharts(document.querySelector("#asset-performance-chart"), options);
        performanceChart.render();
    }

    function fetchNews(symbol) {
        const apiKey = '{{ $alphavantage_api_key ?? "KG3EIIA0Q6MCGONL" }}';
        const url = `https://www.alphavantage.co/query?function=NEWS_SENTIMENT&tickers=${symbol}&apikey=${apiKey}`;
        
        const container = $('#news-feed-container');
        container.html('<div class="text-center py-4 text-secondary small"><i class="ri-loader-4-line animate-spin h4 d-block mb-2"></i>Loading market insights...</div>');

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data.feed && data.feed.length > 0) {
                    let html = '';
                    data.feed.slice(0, 5).forEach(item => {
                        const sentiment = parseFloat(item.overall_sentiment_score);
                        let badgeClass = 'bg-secondary';
                        let sentimentText = 'NEUTRAL';
                        if (sentiment > 0.15) { badgeClass = 'bg-success text-white'; sentimentText = 'BULLISH'; }
                        else if (sentiment < -0.15) { badgeClass = 'bg-danger text-white'; sentimentText = 'BEARISH'; }

                        html += `
                            <div class="news-item border-bottom border-dark pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-white small fw-bold" style="opacity:0.8">${item.source}</span>
                                    <span class="badge ${badgeClass}" style="font-size:9px">${sentimentText}</span>
                                </div>
                                <a href="${item.url}" target="_blank" class="text-white text-decoration-none d-block mb-1 news-title" style="font-size:0.9rem; line-height:1.4;">${item.title}</a>
                                <div class="text-secondary" style="font-size:10px">${new Date(item.time_published.substring(0,4) + '-' + item.time_published.substring(4,6) + '-' + item.time_published.substring(6,8)).toLocaleDateString()}</div>
                            </div>
                        `;
                    });
                    container.html(html);
                } else {
                    container.html('<div class="text-center py-4 text-secondary small"><i class="ri-article-line h3 d-block mb-2 text-dark"></i>No recent news found for this asset.</div>');
                }
            })
            .catch(err => {
                container.html('<div class="text-center py-4 text-secondary small">Error loading news feed.</div>');
            });
    }

    var polygonApiKey = '{{ $polygon_api_key ?? "" }}';

    function formatNumber(num) {
        if (!num) return 'N/A';
        if (num >= 1e9) return '$' + (num / 1e9).toFixed(2) + 'B';
        if (num >= 1e6) return '$' + (num / 1e6).toFixed(2) + 'M';
        if (num >= 1e3) return '$' + (num / 1e3).toFixed(2) + 'K';
        return '$' + parseFloat(num).toFixed(2);
    }

    function syncMarketData() {
        const rows = document.querySelectorAll('#stock-table-body tr');
        let symbols = Array.from(rows).map(row => {
            const el = row.querySelector('.fw-bold.text-white');
            return el ? el.textContent.trim() : null;
        }).filter(Boolean);
        
        // Include Momentum Card symbols
        const momentumCards = document.querySelectorAll('.stock-top-card');
        momentumCards.forEach(card => {
            const symEl = card.querySelector('.outfit.fw-bold.text-white');
            if (symEl) symbols.push(symEl.textContent.trim());
        });
        
        symbols = [...new Set(symbols)]; // Unique

        if (symbols.length === 0 || !polygonApiKey) {
            const ind = document.getElementById('live-indicator');
            if (ind) {
                ind.innerHTML = '<i class="ri-check-line me-1"></i>DATA SYNCED';
                ind.classList.replace('bg-secondary', 'bg-primary');
            }
            return;
        }
        container.html('<div class="text-center py-4 text-secondary small"><i class="ri-loader-4-line animate-spin h4 d-block mb-2"></i>Loading market insights...</div>');
        
        fetch(url)
            .then(res => res.json())
            .then(data => {
                const news = data.feed || [];
                $('#news-count').text(news.length + ' Articles');
                let html = '';
                if (news.length > 0) {
                    news.slice(0, 5).forEach(item => {
                        html += `
                            <div class="news-item mb-3 p-3 glass-panel rounded-3" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                                <div class="text-white small fw-bold mb-1">${item.title}</div>
                                <div class="text-muted x-small">${item.source} • ${new Date(item.time_published.replace(/^(\d{4})(\d{2})(\d{2})T(\d{2})(\d{2})(\d{2})$/, '$1-$2-$3T$4:$5:$6')).toLocaleDateString()}</div>
                            </div>
                        `;
                    });
                } else {
                    html = '<div class="text-center py-4 text-muted small">No recent news for ' + symbol + '</div>';
                }
                container.html(html);
            })
            .catch(() => {
                container.html('<div class="text-center py-4 text-danger small">Failed to load news</div>');
            });
    }

    function showStockDetails(id) { console.log('showStockDetails RUNNING for id:', id);
        let stock = stocksData[id];
        if (!stock) {
            let pStock = Object.values(portfolioData).find(p => p.stock_id == id);
            if (pStock) {
                stock = {
                    id: pStock.stock_id,
                    symbol: pStock.symbol,
                    name: pStock.name,
                    buy: parseFloat(pStock.buy || pStock.asset_base_price || 0),
                    daily_open: parseFloat(pStock.buy || pStock.asset_base_price || 0),
                    daily_high: parseFloat(pStock.buy || pStock.asset_base_price || 0),
                    volume: "100K",
                    changes_percentage: 0
                };
            }
        }
        currentStock = stock;
        
        let detailLogo = getLogoUrl(stock.symbol);
        let logoEl = $('#detail-logo');
        logoEl.attr('src', detailLogo);
        logoEl.on('error', function() {
            let cleanSym = stock.symbol.replace(/[^a-zA-Z0-9]/g, '').substring(0, 3).toUpperCase() || 'S';
            $(this).attr('src', `https://ui-avatars.com/api/?name=${encodeURIComponent(cleanSym)}&background=0ea5e9&color=fff&bold=true`);
            $(this).off('error');
        });
        $('#detail-symbol').text(stock.symbol);
        $('#detail-name').text(stock.name);
        
        const priceValue = parseFloat(stock.buy || stock.amount || 0);
        $('#detail-price').text(priceValue.toLocaleString(undefined, {minimumFractionDigits: 2}));
        
        // Real Portfolio Data
        const pos = portfolioData[stock.symbol];
        if (pos) {
            const units = parseFloat(pos.units);
            const avgPrice = parseFloat(pos.avg_price || 0);
            const totalCost = parseFloat(pos.total_cost || 0);
            const currentValue = units * priceValue;
            const totalProfit = currentValue - totalCost;
            
            $('#pos-lots').text(units.toLocaleString(undefined, {maximumFractionDigits: 4}));
            $('#pos-avg-price').text('$' + avgPrice.toLocaleString(undefined, {minimumFractionDigits: 2}));
            $('#pos-value').text('$' + currentValue.toLocaleString(undefined, {minimumFractionDigits: 2}));
            
            const profitPrefix = totalProfit >= 0 ? '+$' : '-$';
            $('#pos-total-profit').text(profitPrefix + Math.abs(totalProfit).toLocaleString(undefined, {minimumFractionDigits: 2}))
                .removeClass('text-success text-danger')
                .addClass(totalProfit >= 0 ? 'text-success' : 'text-danger');
            
            // Day profit (estimate based on stock change percentage)
            const changePercent = parseFloat(stock.changes_percentage || 0);
            const dayProfit = currentValue * (changePercent / 100);
            const dayPrefix = dayProfit >= 0 ? '+$' : '-$';
            $('#pos-day-profit').text(dayPrefix + Math.abs(dayProfit).toLocaleString(undefined, {minimumFractionDigits: 2}))
                .removeClass('text-success text-danger')
                .addClass(dayProfit >= 0 ? 'text-success' : 'text-danger');
        } else {
            $('#pos-lots').text('0');
            $('#pos-avg-price').text('$0.00');
            $('#pos-value').text('$0.00');
            $('#pos-total-profit').text('$0.00').removeClass('text-danger').addClass('text-success');
            $('#pos-day-profit').text('$0.00').removeClass('text-danger').addClass('text-success');
        }

        // Initialize and Update Chart
        if (!performanceChart) {
            initPerformanceChart();
        }
        
        // Mock some data movement for chart
        const basePrice = priceValue;
        const mockData = Array.from({length: 12}, () => (basePrice * (0.98 + Math.random() * 0.04)).toFixed(2));
        performanceChart.updateSeries([{ data: mockData }]);

        // Set Market Stats
        $('#stat-open').text(stock.daily_open && stock.daily_open > 0 ? parseFloat(stock.daily_open).toLocaleString(undefined, {minimumFractionDigits: 2}) : (priceValue * 0.998).toLocaleString(undefined, {minimumFractionDigits: 2}));
        $('#stat-high').text(stock.daily_high && stock.daily_high > 0 ? parseFloat(stock.daily_high).toLocaleString(undefined, {minimumFractionDigits: 2}) : (priceValue * 1.015).toLocaleString(undefined, {minimumFractionDigits: 2}));
        $('#stat-vol').text(stock.volume || (Math.floor(Math.random() * 900) + 100) + 'K');
        $('#stat-pe').text((Math.random() * 15 + 10).toFixed(2));
        fetchNews(stock.symbol);

        console.log('REACHED MODAL SHOW TRY BLOCK'); try { jQuery('#stockDetailModal').modal('show'); console.log('EXECUTED MODAL SHOW!');
            setTimeout(() => window.dispatchEvent(new Event('resize')), 200);
        } catch (e) {
            console.error("Modal open failed", e);
            // Fallback for BS5 or if jQuery fails
            // jQuery modal failed, likely due to missing DOM element
        }
    }

    function openTradeFlow(type) {
        if(!currentStock) return;
        
        $('#trade-type-label').text(type === 'buy' ? 'Buy' : 'Sell');
        $('#trade-symbol').text(currentStock.symbol);
        $('#trade-market-price').text(parseFloat(currentStock.buy || currentStock.amount).toLocaleString(undefined, {minimumFractionDigits: 2}));
        
        // Update Labels
        $('#buying-selling-label').text(type === 'buy' ? 'Buying amount' : 'Selling amount');
        
        if (type === 'buy') {
            $('#available-type-label').text('Available balance:');
            $('#available-balance-label').text('$' + ({{ auth()->user()->balance ? auth()->user()->balance->amount : 0 }}).toLocaleString(undefined, {minimumFractionDigits: 2}));
        } else {
            $('#available-type-label').text('Available units:');
            const pos = portfolioData[currentStock.symbol];
            const units = pos ? parseFloat(pos.units) : 0;
            $('#available-balance-label').text(units.toLocaleString(undefined, {maximumFractionDigits: 4}) + ' Lots');
        }

        // Update the button text to be more descriptive
        $('#btn-place-order').text((type === 'buy' ? 'Buy ' : 'Sell ') + currentStock.symbol);
        
        // Update Logo
        let logoSrc = getLogoUrl(currentStock.symbol);
        let logoEl = $('#trade-logo');
        logoEl.attr('src', logoSrc);
        logoEl.on('error', function() {
            let cleanSym = currentStock.symbol.replace(/[^a-zA-Z0-9]/g, '').substring(0, 3).toUpperCase() || 'S';
            $(this).attr('src', `https://ui-avatars.com/api/?name=${encodeURIComponent(cleanSym)}&background=0ea5e9&color=fff&bold=true`);
            $(this).off('error');
        });
        
        // Hide details modal if it's open to avoid overlapping
        $('#stockDetailModal').modal('hide');
        setTimeout(() => $('#stockTradeFlowModal').modal('show'), 300);
        
        calculateTotal();
    }

    function backToDetails() {
        $('#stockTradeFlowModal').modal('hide');
        setTimeout(() => $('#stockDetailModal').modal('show'), 400 * 0.5);
    }

    function calculateTotal() {
        let amount = parseFloat($('#trade-amount-input').val()) || 0;
        let price = parseFloat(currentStock.buy || currentStock.amount);
        let total = amount * price;
        $('#trade-total').text(total.toLocaleString(undefined, {minimumFractionDigits: 2}));
        
        const type = $('#trade-type-label').text().toLowerCase();
        let canTrade = true;
        
        if (type === 'buy') {
            let balance = {{ auth()->user()->balance ? auth()->user()->balance->amount : 0 }};
            if (total > balance) canTrade = false;
        } else {
            const pos = portfolioData[currentStock.symbol];
            const units = pos ? parseFloat(pos.units) : 0;
            if (amount > units) canTrade = false;
        }

        if(!canTrade) {
            $('#btn-place-order').prop('disabled', true).addClass('opacity-50');
            $('#available-balance-label').addClass('text-danger');
        } else {
            $('#btn-place-order').prop('disabled', false).removeClass('opacity-50');
            $('#available-balance-label').removeClass('text-danger');
        }
    }

    function setAllIn() {
        const type = $('#trade-type-label').text().toLowerCase();
        if (type === 'buy') {
            let balance = {{ auth()->user()->balance ? auth()->user()->balance->amount : 0 }};
            let price = parseFloat(currentStock.buy || currentStock.amount);
            $('#trade-amount-input').val(Math.floor(balance / price * 10000) / 10000);
        } else {
            const pos = portfolioData[currentStock.symbol];
            const units = pos ? parseFloat(pos.units) : 0;
            $('#trade-amount-input').val(units);
        }
        calculateTotal();
    }
    
    function selectOrderType(type, el) {
        $('#order-type-btn').text(type);
        $('.order-type-item').removeClass('active');
        $(el).addClass('active');
    }

    function submitTrade() {
        const id = currentStock.id;
        const amount = $('#trade-amount-input').val();
        const type = $('#trade-type-label').text().toLowerCase();
        const url = type === 'buy' ? "{{ route('stocks.trade-post') }}" : "{{ route('stocks.sell') }}";

        const btn = $('#btn-place-order');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({id, amount})
        })
        .then(res => {
            const contentType = res.headers.get('content-type') || '';
            if (!contentType.includes('application/json')) {
                // Server returned HTML (likely 419 CSRF expired, 500, or redirect)
                return res.text().then(html => {
                    console.error('Non-JSON response (status ' + res.status + '):', html.substring(0, 500));
                    if (res.status === 419) {
                        throw new Error('Session expired. Please refresh the page and try again.');
                    }
                    throw new Error('Server error (HTTP ' + res.status + '). Please refresh and try again.');
                });
            }
            return res.json().then(data => ({ ok: res.ok, data }));
        })
        .then(result => {
            if (!result || !result.data) return; // handled in text() branch above
            const {ok, data} = result;
            if(ok && data.status) {
                $('#stockTradeFlowModal').modal('hide');
                
                // Show Success Modal
                let successLogoSrc = getLogoUrl(currentStock.symbol);
                let successLogoEl = $('#success-logo');
                successLogoEl.attr('src', successLogoSrc);
                successLogoEl.on('error', function() {
                    let cleanSym = currentStock.symbol.replace(/[^a-zA-Z0-9]/g, '').substring(0, 3).toUpperCase() || 'S';
                    $(this).attr('src', `https://ui-avatars.com/api/?name=${encodeURIComponent(cleanSym)}&background=0ea5e9&color=fff&bold=true`);
                    $(this).off('error');
                });
                $('#success-type-label').text('Market ' + type.charAt(0).toUpperCase() + type.slice(1));
                $('#success-lots').text(amount);
                $('#success-total').text('$' + parseFloat($('#trade-total').text().replace(/,/g, '')).toLocaleString());
                
                setTimeout(() => $('#stockSuccessModal').modal('show'), 400);
                
                // === DYNAMIC PORTFOLIO REFRESH (no page reload) ===
                if (data.portfolio) {
                    refreshPortfolioUI(data.portfolio);
                }
                if (data.balance !== undefined) {
                    const formatted = parseFloat(data.balance).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    $('.bal').text('$' + formatted);
                }
                // Re-enable the order button
                btn.prop('disabled', false).text('Place an order');
                // Refresh the stock detail panel if still visible
                if (currentStock) {
                    showStockDetails(currentStock.id);
                }
            } else {
                toastr.error(data.message || data.error || 'Trade execution failed. Please try again.');
                btn.prop('disabled', false).text('Place an order');
            }
        })
        .catch(err => {
            console.error('Trade submission error:', err);
            toastr.error(err.message || 'Connection error. Please check your network and try again.');
            btn.prop('disabled', false).text('Place an order');
        });
    }

    /**
     * Dynamically rebuild the Active Equity Positions table from fresh server data.
     * Also updates the in-memory portfolioData used by showStockDetails().
     */
    function refreshPortfolioUI(portfolioArray) {
        // Rebuild portfolioData (keyed by symbol)
        portfolioData = {};
        portfolioArray.forEach(pos => {
            portfolioData[pos.symbol] = pos;
        });

        const section = document.querySelector('[data-portfolio-section]');
        
        if (portfolioArray.length === 0) {
            // No positions left — hide the entire portfolio section
            if (section) section.style.display = 'none';
            return;
        }

        // If section was hidden or doesn't exist, show/create it
        if (section) {
            section.style.display = '';
        }

        // Update the asset count badge
        const badge = document.querySelector('[data-portfolio-count]');
        if (badge) badge.textContent = portfolioArray.length + ' ASSETS HELD';

        // Rebuild the table body
        const tbody = document.querySelector('[data-portfolio-tbody]');
        if (!tbody) return;

        tbody.innerHTML = '';
        const appUrl = window.APP_URL || '';
        const fallbackImg = '{{ asset("assets/img/profit.svg") }}';

        portfolioArray.forEach(pos => {
            const units = parseFloat(pos.units);
            const buyPrice = parseFloat(pos.buy);
            const marketValue = (units * buyPrice).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            const avgPrice = parseFloat(pos.avg_price || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            const logoSrc = getLogoUrl(pos.symbol);

            const tr = document.createElement('tr');
            tr.className = 'portfolio-row pointer';
            tr.style.cssText = 'background: rgba(255,255,255,0.03); transition: all 0.3s ease;';
            tr.setAttribute('onclick', "showStockDetails('" + pos.stock_id + "')");

            tr.innerHTML = `
                <td class="py-3 border-0" style="border-radius: 12px 0 0 12px; padding-left: 1.5rem;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="asset-logo-container" style="width: 38px; height: 38px; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.12); border-radius: 8px !important; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(8px) saturate(180%); overflow: hidden; position: relative;">
                            <img src="${logoSrc}" alt="${pos.symbol}" loading="lazy" width="28" height="28" style="object-fit: contain !important; border-radius: 0 !important;" onerror="let cleanSym = this.alt.replace(/[^a-zA-Z0-9]/g, '').substring(0, 3).toUpperCase() || 'S'; this.src='https://ui-avatars.com/api/?name=' + encodeURIComponent(cleanSym) + '&background=0ea5e9&color=fff&bold=true'; this.onerror=null;">
                        </div>
                        <div>
                            <div class="text-white fw-bold portfolio-fund-name" style="font-size: 0.95rem;">${pos.symbol}</div>
                            <div class="text-secondary small" style="font-size: 0.75rem; opacity: 0.6;">${pos.name}</div>
                        </div>
                    </div>
                </td>
                <td class="py-3 border-0 align-middle font-weight-bold text-white">${units.toLocaleString(undefined, {maximumFractionDigits: 4})}</td>
                <td class="py-3 border-0 align-middle text-secondary small">$${avgPrice}</td>
                <td class="py-3 border-0 align-middle outfit fw-bold text-white">$${marketValue}</td>
                <td class="py-3 border-0 align-middle text-end">
                    <span class="text-success fw-bold" style="font-size: 0.9rem;">+0.00%</span>
                </td>
                <td class="py-3 border-0 align-middle text-end" style="border-radius: 0 12px 12px 0; padding-right: 1.5rem;">
                    <button class="btn btn-premium btn-sm py-1 px-3" onclick="showStockDetails('${pos.stock_id}')" style="font-size: 10px; letter-spacing: 1px; font-weight: 800;">TRADE</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    // Search logic refinements
    // Unified Filtering Logic
    function filterStocks() {
        const searchVal = document.getElementById('global-stock-search').value.toLowerCase();
        const activeMarket = document.querySelector('.market-tab.active').getAttribute('data-market');
        const activeCategory = document.querySelector('.btn-filter.active').getAttribute('data-category');
        
        const rows = document.querySelectorAll('#stock-table-body tr');

        rows.forEach(row => {
            const symbol = row.querySelector('.fw-bold.text-white').textContent.toLowerCase();
            const name = row.querySelector('.text-muted').textContent.toLowerCase();
            const rowMarket = row.getAttribute('data-market'); 
            
            const matchesSearch = symbol.includes(searchVal) || name.includes(searchVal);
            
            // For now, if activeMarket is "Nasdaq", we show all unless it matches search.
            // In a real database scenario, we'd filter by actual market column.
            // We'll simulate by showing only some if market is not default.
            let matchesMarket = true;
            if (activeMarket !== 'Nasdaq') {
                // Mock filtering: show only if symbol starts with first letter of market
                matchesMarket = symbol.startsWith(activeMarket.charAt(0).toLowerCase());
            }

            row.style.display = (matchesSearch && matchesMarket) ? '' : 'none';
        });
    }

    // Category button logic
    document.querySelectorAll('.btn-filter').forEach(btn => {
        btn.onclick = function() {
            document.querySelectorAll('.btn-filter').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            filterStocks();
            toastr.success('Filtering by ' + this.getAttribute('data-category'));
        };
    });

    const globalSearch = document.getElementById('global-stock-search'); if (globalSearch) globalSearch.onkeyup = filterStocks;
    
    const innerSearch = document.getElementById('inner-stock-search'); if (innerSearch) innerSearch.onkeyup = function() {
        document.getElementById('global-stock-search').value = this.value;
        filterStocks();
    };

    // Market tab logic
    document.querySelectorAll('.market-tab').forEach(tab => {
        tab.onclick = function(e) {
            e.preventDefault();
            document.querySelectorAll('.market-tab').forEach(t => {
                t.classList.remove('active');
                t.classList.add('text-muted');
            });
            this.classList.add('active');
            this.classList.remove('text-muted');
            
            // In a real app, this would fetch from server. For now, we clear search and show all.
            document.getElementById('global-stock-search').value = '';
            filterStocks();
            
            toastr.info('Switched to ' + this.textContent + ' market');
        };
    });
    var polygonApiKey = '{{ $polygon_api_key ?? "" }}';

    function formatNumber(num) {
        if (!num) return 'N/A';
        if (num >= 1e9) return '$' + (num / 1e9).toFixed(2) + 'B';
        if (num >= 1e6) return '$' + (num / 1e6).toFixed(2) + 'M';
        if (num >= 1e3) return (num / 1e3).toFixed(2) + 'K';
        return num.toLocaleString();
    }

    function syncMarketData() {
        const rows = document.querySelectorAll('#stock-table-body tr');
        let symbols = Array.from(rows).map(row => row.querySelector('.fw-bold.text-white').textContent.trim()).filter(Boolean);
        
        // Include Momentum Card symbols
        const momentumCards = document.querySelectorAll('.stock-top-card');
        momentumCards.forEach(card => {
            const symEl = card.querySelector('.outfit.fw-bold.text-white');
            if (symEl) symbols.push(symEl.textContent.trim());
        });
        
        symbols = [...new Set(symbols)]; // Unique

        if (symbols.length === 0 || !polygonApiKey) return;

        // 1. Fetch Snapshot for initial data
        fetch(`https://api.polygon.io/v2/snapshot/locale/us/markets/stocks/tickers?tickers=${symbols.join(',')}&apiKey=${polygonApiKey}`)
            .then(res => res.json())
            .then(data => {
                if (data && data.tickers) {
                    data.tickers.forEach(t => {
                        const sym = t.ticker;
                        const varEl = document.getElementById(`var-${sym}`);
                        const priceEl = document.getElementById(`price-${sym}`);
                        const volEl = document.getElementById(`vol-${sym}`);
                        
                        if (varEl && t.todaysChangePerc !== undefined) {
                            const change = parseFloat(t.todaysChangePerc);
                            varEl.className = change >= 0 ? 'fw-bold text-success' : 'fw-bold text-danger';
                            varEl.textContent = (change >= 0 ? '+' : '') + change.toFixed(2) + '%';
                        }
                        if (priceEl && t.day && t.day.c) {
                            priceEl.textContent = t.day.c.toLocaleString(undefined, {minimumFractionDigits: 2});
                        }
                        if (volEl && t.day && t.day.v) {
                            volEl.textContent = formatNumber(t.day.v).replace('$', '');
                        }
                        
                        // Simple dynamic SVG trend line
                        const chartDiv = document.getElementById(`chart-${sym}`);
                        if (chartDiv && t.todaysChangePerc !== undefined) {
                            const change = t.todaysChangePerc;
                            const color = change >= 0 ? '#ff3333' : '#ef4444';
                            
                            // Generate deterministic pseudo-random path based on symbol
                            let hash = 0;
                            for (let i = 0; i < sym.length; i++) hash = sym.charCodeAt(i) + ((hash << 5) - hash);
                            
                            let pts = [[0, change >= 0 ? 20 : 5]];
                            for (let i = 1; i <= 5; i++) {
                                let x = i * 10;
                                let y = change >= 0 
                                    ? (20 - (15 * (i/5)) + (Math.sin(hash + i) * 5))
                                    : (5 + (15 * (i/5)) + (Math.sin(hash + i) * 5));
                                y = Math.max(2, Math.min(23, y));
                                pts.push([x, y.toFixed(1)]);
                            }
                            pts.push([60, change >= 0 ? 5 : 20]);
                            const path = 'M' + pts.map(p => p.join(' ')).join(' L');
                            
                            chartDiv.innerHTML = `<svg viewBox="0 0 60 25" class="w-100 h-100">
                                <path d="${path}" fill="none" stroke="${color}" stroke-width="2" stroke-linecap="round"></path>
                            </svg>`;
                        }
                    });
                }
            }).catch(e => console.error("Snapshot error:", e));

        // 2. Fetch Market Cap (Staggered to prevent rate limit on free tier)
        symbols.forEach((sym, index) => {
            setTimeout(() => {
                fetch(`https://api.polygon.io/v3/reference/tickers/${sym}?apiKey=${polygonApiKey}`)
                    .then(res => res.json())
                    .then(data => {
                        const capEl = document.getElementById(`cap-${sym}`);
                        if (capEl) {
                            if (data && data.results && data.results.market_cap) {
                                capEl.textContent = formatNumber(data.results.market_cap);
                            }
                            // If API fails, retain the PHP-generated pseudo-random market cap
                        }
                    }).catch(e => {});
            }, index * 250);
        });

        // 3. WebSocket Connection for live updates
        const ws = new WebSocket('wss://delayed.polygon.io/stocks');
        
        ws.onopen = () => {
            const ind = document.getElementById('live-indicator');
            if (ind) {
                ind.innerHTML = '<i class="ri-pulse-line me-1"></i>LIVE';
                ind.classList.replace('bg-secondary', 'bg-success');
            }
            ws.send(JSON.stringify({"action":"auth","params":polygonApiKey}));
        };

        ws.onmessage = (event) => {
            const data = JSON.parse(event.data);
            data.forEach(msg => {
                if (msg.ev === 'status' && msg.status === 'auth_success') {
                    const subscribeParams = symbols.map(s => `A.${s}`).join(',');
                    ws.send(JSON.stringify({"action":"subscribe", "params":subscribeParams}));
                }
                
                if (msg.ev === 'A') {
                    const sym = msg.sym;
                    const price = msg.c;
                    
                    // Table row
                    const priceEl = document.getElementById(`price-${sym}`);
                    if (priceEl) {
                        const oldPrice = parseFloat(priceEl.textContent.replace(/[$,]/g, ''));
                        priceEl.textContent = price.toLocaleString(undefined, {minimumFractionDigits: 2});
                        if (price > oldPrice) { priceEl.style.color = '#ff3333'; } 
                        else if (price < oldPrice) { priceEl.style.color = '#ef4444'; }
                        setTimeout(() => priceEl.style.color = '', 1000);
                    }

                    // Momentum Cards
                    momentumCards.forEach(card => {
                        const cardSym = card.querySelector('.outfit.fw-bold.text-white');
                        const cardPrice = card.querySelector('.h3.outfit.fw-bold.text-white');
                        if (cardSym && cardPrice && cardSym.textContent.trim() === sym) {
                            cardPrice.textContent = '$' + price.toLocaleString(undefined, {minimumFractionDigits: 2});
                        }
                    });
                    
                    // Modal updates
                    if (currentStock && currentStock.symbol === sym) {
                        $('#detail-price').text(price.toLocaleString(undefined, {minimumFractionDigits: 2}));
                        $('#trade-market-price').text(price.toLocaleString(undefined, {minimumFractionDigits: 2}));
                        
                        const pos = portfolioData[sym];
                        if (pos) {
                            const units = parseFloat(pos.units);
                            const currentValue = units * price;
                            const totalProfit = currentValue - parseFloat(pos.total_cost || 0);
                            
                            $('#pos-value').text('$' + currentValue.toLocaleString(undefined, {minimumFractionDigits: 2}));
                            const profitPrefix = totalProfit >= 0 ? '+' : '';
                            $('#pos-total-profit').text(profitPrefix + '$' + totalProfit.toLocaleString(undefined, {minimumFractionDigits: 2}))
                                .removeClass('text-success text-danger')
                                .addClass(totalProfit >= 0 ? 'text-success' : 'text-danger');
                        }
                        if (typeof calculateTotal === 'function') calculateTotal();
                    }
                }
            });
        };
        
        ws.onerror = ws.onclose = () => {
            const ind = document.getElementById('live-indicator');
            if (ind) {
                ind.innerHTML = '<i class="ri-error-warning-line me-1"></i>DISCONNECTED';
                ind.classList.replace('bg-success', 'bg-danger');
            }
        };
    }

    // Initialize logic
    $(document).ready(function() {
        syncMarketData();
    });
</script>
@endpush









