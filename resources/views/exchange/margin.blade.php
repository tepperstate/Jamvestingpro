@extends('layouts.user.app')
@section('title', 'Margin Trading')

@section('content')
<!-- Remix Icon & Google Fonts for premium design -->
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    /* Absolute Binance Spot Trading Color Palette */
    :root {
        --binance-bg: var(--bg-main, #000000);
        --binance-panel: rgba(0, 0, 0, 0.55);
        --binance-border: rgba(255, 255, 255, 0.08);
        --binance-text: var(--text-primary, #f8fafc);
        --binance-muted: var(--text-muted, #94a3b8);
        --binance-green: #ff3333;
        --binance-red: #f43f5e;
        --binance-yellow: #0ea5e9; /* Sky Blue primary accent */
        --binance-input: rgba(0, 0, 0, 0.25);
        --binance-hover: rgba(255, 255, 255, 0.03);
        --binance-green-alpha: rgba(255, 51, 51, 0.12);
        --binance-red-alpha: rgba(244, 63, 94, 0.12);
    }

    .spot-terminal-wrapper {
        font-family: 'Inter', sans-serif;
        background-color: transparent !important;
        color: var(--binance-text);
        min-height: 100vh;
        padding: 16px;
        margin: 0;
    }

    /* Outer layout structure */
    .spot-grid-layout {
        display: grid;
        grid-template-columns: 280px 1fr 320px;
        grid-gap: 12px;
        margin-top: 12px;
        align-items: stretch;
    }

    @media (max-width: 1400px) {
        .spot-grid-layout {
            grid-template-columns: 240px 1fr 280px;
        }
    }

    @media (max-width: 1200px) {
        .spot-grid-layout {
            display: flex;
            flex-direction: column;
        }
    }

    /* Panels & Cards styling */
    .binance-panel {
        background-color: var(--binance-panel);
        border: 1px solid var(--binance-border);
        border-radius: 12px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.35);
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }
    .binance-panel:hover {
        border-color: rgba(14, 165, 233, 0.2);
        box-shadow: 0 12px 40px 0 rgba(0, 0, 0, 0.45);
    }

    /* Region 1: Top Bar Header */
    .spot-header-bar {
        background-color: var(--binance-panel);
        border: 1px solid var(--binance-border);
        border-radius: 12px;
        padding: 12px 20px;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 24px;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.35);
    }

    .spot-header-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .header-crypto-logo {
        width: 32px;
        height: 32px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
        padding: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .header-crypto-logo img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .header-ticker-name {
        line-height: 1.1;
    }

    .header-ticker-symbol {
        font-family: 'Outfit', sans-serif;
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--binance-text);
    }

    .header-ticker-link {
        font-size: 0.75rem;
        color: var(--binance-yellow);
        text-decoration: none;
    }

    .header-ticker-link:hover {
        text-decoration: underline;
    }

    .header-metric-box {
        display: flex;
        flex-direction: column;
        line-height: 1.2;
    }

    .header-metric-label {
        font-size: 0.7rem;
        color: var(--binance-muted);
        text-transform: uppercase;
        margin-bottom: 2px;
    }

    .header-metric-value {
        font-size: 0.85rem;
        font-weight: 600;
    }

    .header-metric-value.large-price {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--binance-text);
    }

    .header-metric-value.green {
        color: var(--binance-green);
    }

    .header-metric-value.red {
        color: var(--binance-red);
    }

    /* Region 2: Order Book (Left Panel) */
    .orderbook-panel {
        grid-column: 1;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .orderbook-header {
        padding: 10px 16px;
        border-bottom: 1px solid var(--binance-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .orderbook-layout-tabs {
        display: flex;
        gap: 6px;
    }

    .ob-layout-btn {
        background: transparent;
        border: none;
        color: var(--binance-muted);
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .ob-layout-btn:hover, .ob-layout-btn.active {
        color: var(--binance-text);
        background: var(--binance-border);
    }

    .ob-precision-select {
        background: transparent;
        border: none;
        color: var(--binance-muted);
        font-size: 0.75rem;
        cursor: pointer;
        outline: none;
    }

    .ob-table-header {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        padding: 8px 16px;
        font-size: 0.7rem;
        color: var(--binance-muted);
        text-transform: uppercase;
    }

    .ob-rows-container {
        flex: 1;
        overflow-y: hidden !important;
        font-size: 0.75rem;
        position: relative;
    }

    .ob-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        padding: 3px 16px;
        position: relative;
        cursor: pointer;
    }

    .ob-row:hover {
        background-color: var(--binance-hover);
    }

    .ob-row-bg-bar {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        z-index: 1;
        opacity: 0.15;
        transition: width 0.3s ease;
    }

    .ob-row-val {
        position: relative;
        z-index: 2;
    }

    .ob-row-val.price-ask {
        color: var(--binance-red);
    }

    .ob-row-val.price-bid {
        color: var(--binance-green);
    }

    .ob-row-val.right-align {
        text-align: right;
    }

    .ob-mid-price-bar {
        padding: 10px 16px;
        background: rgba(255, 255, 255, 0.02);
        border-top: 1px solid var(--binance-border);
        border-bottom: 1px solid var(--binance-border);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .ob-mid-price {
        font-size: 1.15rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .ob-mid-usd {
        font-size: 0.75rem;
        color: var(--binance-muted);
    }

    /* Region 3: TradingView Chart (Center top) */
    .chart-panel {
        min-height: 560px;
        display: flex;
        flex-direction: column;
    }

    .chart-control-bar {
        padding: 8px 16px;
        border-bottom: 1px solid var(--binance-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .chart-intervals {
        display: flex;
        align-items: center;
        gap: 2px;
    }

    .chart-int-btn {
        background: transparent;
        border: none;
        color: var(--binance-muted);
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 500;
    }

    .chart-int-btn:hover, .chart-int-btn.active {
        color: var(--binance-text);
        background: var(--binance-border);
    }

    .chart-types-switch {
        display: flex;
        gap: 8px;
    }

    .chart-type-btn {
        background: transparent;
        border: none;
        color: var(--binance-muted);
        font-size: 0.75rem;
        padding: 4px 8px;
        cursor: pointer;
    }

    .chart-type-btn.active, .chart-type-btn:hover {
        color: var(--binance-yellow);
    }

    /* Region 4: Markets & Search Widget (Right top) */
    .markets-panel {
        grid-column: 3;
        height: 420px;
    }

    .markets-search-container {
        padding: 10px 16px;
        border-bottom: 1px solid var(--binance-border);
        position: relative;
    }

    .markets-search-input {
        width: 100%;
        background-color: var(--binance-input);
        border: 1px solid var(--binance-border);
        border-radius: 4px;
        color: var(--binance-text);
        padding: 6px 12px 6px 32px;
        font-size: 0.8rem;
        outline: none;
    }

    .markets-search-input:focus {
        border-color: var(--binance-yellow);
    }

    .markets-search-icon {
        position: absolute;
        left: 26px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--binance-muted);
        font-size: 0.9rem;
    }

    .markets-tabs {
        display: flex;
        padding: 0 16px;
        border-bottom: 1px solid var(--binance-border);
        background: rgba(0,0,0,0.1);
        overflow-x: auto;
    }

    .markets-tab-item {
        padding: 8px 10px;
        font-size: 0.7rem;
        font-weight: 600;
        color: var(--binance-muted);
        cursor: pointer;
        white-space: nowrap;
    }

    .markets-tab-item:hover, .markets-tab-item.active {
        color: var(--binance-yellow);
        border-bottom: 2px solid var(--binance-yellow);
    }

    .markets-list-header {
        display: grid;
        grid-template-columns: 1.8fr 1fr 1fr;
        padding: 6px 16px;
        font-size: 0.65rem;
        color: var(--binance-muted);
        text-transform: uppercase;
        border-bottom: 1px solid var(--binance-border);
    }

    .markets-list-container {
        flex: 1;
        overflow-y: auto;
    }

    .market-list-row {
        display: grid;
        grid-template-columns: 1.8fr 1fr 1fr;
        padding: 6px 16px;
        font-size: 0.75rem;
        cursor: pointer;
        align-items: center;
        border-bottom: 1px solid rgba(255,255,255,0.01);
    }

    .market-list-row:hover {
        background-color: var(--binance-hover);
    }

    .market-list-symbol {
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .market-star {
        color: var(--binance-muted);
        font-size: 0.8rem;
    }

    .market-star.active {
        color: var(--binance-yellow);
    }

    .market-list-price {
        font-weight: 500;
    }

    .market-list-change {
        text-align: right;
    }

    .market-list-change.green {
        color: var(--binance-green);
    }

    .market-list-change.red {
        color: var(--binance-red);
    }

    /* Region 5: Trading Desk (Center Bottom) */
    .tradingdesk-panel {
        padding: 16px;
        background-color: var(--binance-panel);
    }

    .tradingdesk-header-tabs {
        display: flex;
        border-bottom: 1px solid var(--binance-border);
        margin-bottom: 16px;
        justify-content: space-between;
        align-items: center;
    }

    .td-main-tabs {
        display: flex;
        gap: 20px;
    }

    .td-main-tab {
        padding-bottom: 10px;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--binance-muted);
        cursor: pointer;
        position: relative;
    }

    .td-main-tab.active {
        color: var(--binance-text);
    }

    .td-main-tab.active::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        right: 0;
        height: 2px;
        background-color: var(--binance-yellow);
    }

    .td-sub-tabs {
        display: flex;
        gap: 16px;
        margin-bottom: 16px;
    }

    .td-sub-tab {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--binance-muted);
        cursor: pointer;
        padding: 2px 6px;
        border-radius: 4px;
    }

    .td-sub-tab.active {
        color: var(--binance-yellow);
        background: rgba(240, 185, 11, 0.1);
    }

    .td-columns {
        display: grid;
        grid-template-columns: 1fr 1fr;
        grid-gap: 24px;
    }

    @media (max-width: 768px) {
        .td-columns {
            grid-template-columns: 1fr;
        }
    }

    .td-column {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .td-avbl-row {
        display: flex;
        justify-content: space-between;
        font-size: 0.75rem;
        color: var(--binance-muted);
    }

    .td-avbl-val {
        color: var(--binance-text);
        font-weight: 600;
    }

    .td-input-group {
        background-color: var(--binance-input);
        border: 1px solid var(--binance-border);
        border-radius: 4px;
        display: flex;
        align-items: center;
        padding: 6px 12px;
        position: relative;
    }

    .td-input-group:focus-within {
        border-color: var(--binance-yellow);
    }

    .td-input-label {
        font-size: 0.75rem;
        color: var(--binance-muted);
        width: 60px;
        flex-shrink: 0;
    }

    .td-input-field {
        background: transparent;
        border: none;
        color: var(--binance-text);
        width: 100%;
        text-align: right;
        font-size: 0.9rem;
        font-weight: 600;
        outline: none;
        padding: 4px 0;
    }

    .td-input-suffix {
        font-size: 0.75rem;
        color: var(--binance-text);
        margin-left: 8px;
        font-weight: 600;
        width: 40px;
        text-align: right;
    }

    /* Percentage Slider node style */
    .slider-wrap {
        padding: 8px 4px 16px;
        position: relative;
    }

    .percentage-points {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin-top: -6px;
        padding: 0 4px;
        z-index: 10;
    }

    .pct-point {
        width: 10px;
        height: 10px;
        background-color: var(--binance-input);
        border: 2px solid var(--binance-border);
        border-radius: 50%;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .pct-point:hover, .pct-point.active {
        border-color: var(--binance-yellow);
        background-color: var(--binance-yellow);
        transform: scale(1.2);
    }

    /* Custom range input styling */
    .custom-range-input {
        width: 100%;
        height: 3px;
        background: var(--binance-border);
        outline: none;
        -webkit-appearance: none;
        border-radius: 2px;
    }

    .custom-range-input::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: var(--binance-yellow);
        cursor: pointer;
        border: 2px solid var(--binance-panel);
        transition: transform 0.1s ease;
    }

    .custom-range-input::-webkit-slider-thumb:hover {
        transform: scale(1.3);
    }

    .td-action-btn {
        width: 100%;
        padding: 10px;
        border-radius: 4px;
        font-size: 0.9rem;
        font-weight: 700;
        color: #ffffff;
        border: none;
        cursor: pointer;
        text-transform: uppercase;
        transition: background-color 0.2s ease;
    }

    .td-action-btn.buy-btn {
        background-color: var(--binance-green);
    }

    .td-action-btn.buy-btn:hover {
        background-color: #0b9e64;
    }

    .td-action-btn.sell-btn {
        background-color: var(--binance-red);
    }

    .td-action-btn.sell-btn:hover {
        background-color: #c4384a;
    }

    /* Region 6: Market Trades (Right Bottom) */
    .trades-panel {
        grid-column: 3;
        flex: 1;
        min-height: 320px;
    }

    .trades-header-tabs {
        display: flex;
        border-bottom: 1px solid var(--binance-border);
    }

    .trades-tab {
        padding: 10px 16px;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--binance-muted);
        cursor: pointer;
    }

    .trades-tab.active {
        color: var(--binance-text);
        border-bottom: 2px solid var(--binance-yellow);
    }

    .trades-list-header {
        display: grid;
        grid-template-columns: 1fr 1fr 1.2fr;
        padding: 6px 16px;
        font-size: 0.65rem;
        color: var(--binance-muted);
        text-transform: uppercase;
    }

    .trades-rows-container {
        flex: 1;
        overflow-y: auto;
    }

    .trades-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1.2fr;
        padding: 3px 16px;
        font-size: 0.7rem;
    }

    .trades-row-price.green {
        color: var(--binance-green);
    }

    .trades-row-price.red {
        color: var(--binance-red);
    }

    .trades-row-time {
        text-align: right;
        color: var(--binance-muted);
    }

    /* Market activities banner */
    .market-activities-box {
        background: rgba(0,0,0,0.15);
        border-top: 1px solid var(--binance-border);
        padding: 10px 16px;
    }

    .ma-title {
        font-size: 0.7rem;
        color: var(--binance-muted);
        text-transform: uppercase;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .ma-row {
        display: flex;
        justify-content: space-between;
        font-size: 0.75rem;
    }

    .ma-row-pair {
        font-weight: 600;
    }

    .ma-row-percent {
        color: var(--binance-green);
        font-weight: 600;
    }

    /* Bottom operational ledger (Full width) */
    .ledger-panel {
        margin-top: 8px;
        background-color: var(--binance-panel);
    }

    .ledger-header {
        padding: 12px 20px;
        border-bottom: 1px solid var(--binance-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .ledger-title {
        font-size: 0.9rem;
        font-weight: 700;
    }

    .ledger-table-wrap {
        padding: 8px;
    }

    .ledger-badge-side {
        font-size: 0.65rem;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 3px;
    }

    .ledger-badge-side.buy {
        background-color: var(--binance-green-alpha);
        color: var(--binance-green);
    }

    .ledger-badge-side.sell {
        background-color: var(--binance-red-alpha);
        color: var(--binance-red);
    }

    .ledger-badge-status {
        font-size: 0.65rem;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 3px;
    }

    .ledger-badge-status.pending {
        background-color: rgba(240, 185, 11, 0.15);
        color: var(--binance-yellow);
    }

    .ledger-badge-status.approved {
        background-color: var(--binance-green-alpha);
        color: var(--binance-green);
    }

    .ledger-badge-status.rejected {
        background-color: var(--binance-red-alpha);
        color: var(--binance-red);
    }

    /* Custom dark scrollbar matching Binance */
    .binance-scrollbar::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .binance-scrollbar::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.01);
        border-radius: 3px;
    }
    .binance-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.15);
        border-radius: 3px;
    }
    .binance-scrollbar::-webkit-scrollbar-thumb:hover {
        background: var(--binance-yellow);
    }
</style>

<div class="spot-terminal-wrapper container-fluid">
    <!-- Region 1: Top Bar Header -->
    <div class="spot-header-bar">
        <div class="spot-header-left">
            <div class="header-crypto-logo" id="header-logo-mount">
                <!-- Injected via JS -->
            </div>
            <div class="header-ticker-name">
                <div class="header-ticker-symbol" id="header-symbol-display">BTC/USDT</div>
                <a href="#" class="header-ticker-link" id="header-ticker-link">Bitcoin Price</a>
            </div>
        </div>

        <!-- Metric Items -->
        <div class="header-metric-box" style="min-width: 130px;">
            <div class="header-metric-label" id="header-ticker-price-label">Price(USDT)</div>
            <div class="header-metric-value large-price text-white" id="header-main-price">30,605.92</div>
        </div>

        <div class="header-metric-box">
            <div class="header-metric-label">24h Change</div>
            <div class="header-metric-value green" id="header-change-val">+1,491.36 +5.12%</div>
        </div>

        <div class="header-metric-box">
            <div class="header-metric-label">24h High</div>
            <div class="header-metric-value text-white" id="header-high-val">30,928.71</div>
        </div>

        <div class="header-metric-box">
            <div class="header-metric-label">24h Low</div>
            <div class="header-metric-value text-white" id="header-low-val">29,077.98</div>
        </div>

        <div class="header-metric-box">
            <div class="header-metric-label" id="header-vol-base-label">24h Volume(BTC)</div>
            <div class="header-metric-value text-white" id="header-vol-base">55,021.79</div>
        </div>

        <div class="header-metric-box">
            <div class="header-metric-label" id="header-vol-quote-label">24h Volume(USDT)</div>
            <div class="header-metric-value text-white" id="header-vol-quote">1,651,298,849.47</div>
        </div>
    </div>

    <!-- Layout Grid -->
    <div class="spot-grid-layout">
        <!-- Region 2: Order Book (Left Column) -->
        <div class="binance-panel orderbook-panel">
            <div class="orderbook-header">
                <div class="orderbook-layout-tabs">
                    <button class="ob-layout-btn active" id="ob-layout-both" onclick="setObLayout('both')">
                        <i class="ri-align-justify text-warning"></i>
                    </button>
                    <button class="ob-layout-btn" id="ob-layout-bids" onclick="setObLayout('bids')">
                        <i class="ri-text-align-left text-success"></i>
                    </button>
                    <button class="ob-layout-btn" id="ob-layout-asks" onclick="setObLayout('asks')">
                        <i class="ri-text-align-right text-danger"></i>
                    </button>
                </div>
                <select class="ob-precision-select" id="ob-precision">
                    <option value="0.01">0.01</option>
                    <option value="0.1">0.1</option>
                    <option value="1">1</option>
                </select>
            </div>

            <!-- Header columns -->
            <div class="ob-table-header">
                <div>Price(USDT)</div>
                <div class="text-end" id="ob-amt-header">Amount(BTC)</div>
                <div class="text-end">Total</div>
            </div>

            <!-- Asks (Sells) Rows -->
            <div class="ob-rows-container binance-scrollbar d-flex flex-column-reverse" id="ob-asks-container" style="flex: 1; min-height: 0;">
                <!-- Simulated asks injected via JS -->
            </div>

            <!-- Middle price display -->
            <div class="ob-mid-price-bar">
                <div class="ob-mid-price text-success" id="ob-mid-price-mount">
                    30,605.92 <i class="ri-arrow-up-fill"></i>
                </div>
                <div class="ob-mid-usd" id="ob-mid-usd-mount">$30,605.92</div>
            </div>

            <!-- Bids (Buys) Rows -->
            <div class="ob-rows-container binance-scrollbar" id="ob-bids-container" style="flex: 1; min-height: 0;">
                <!-- Simulated bids injected via JS -->
            </div>
        </div>

        <!-- Middle Column: Chart & Trading Desk -->
        <div class="d-flex flex-column gap-3" style="grid-column: 2; height: 100%;">
            <!-- Region 3: TradingView Chart -->
            <div class="binance-panel chart-panel flex-grow-1">
                <div class="chart-control-bar">
                    <div class="chart-intervals">
                        <button class="chart-int-btn" data-val="1">1m</button>
                        <button class="chart-int-btn" data-val="5">5m</button>
                        <button class="chart-int-btn active" data-val="15">15m</button>
                        <button class="chart-int-btn" data-val="60">1h</button>
                        <button class="chart-int-btn" data-val="240">4h</button>
                        <button class="chart-int-btn" data-val="D">1d</button>
                        <button class="chart-int-btn" data-val="W">1w</button>
                    </div>
                    <div class="chart-types-switch">
                        <span class="chart-type-btn active">Trading View</span>
                        <span class="chart-type-btn">Original</span>
                        <span class="chart-type-btn">Depth</span>
                    </div>
                </div>
                <div class="chart-container flex-grow-1 d-flex flex-column" id="trading-chart-wrapper" style="border-radius: 12px; overflow: hidden; min-height: 540px; background: var(--binance-bg);">
                    <div id="chart-mount" class="flex-grow-1 d-flex flex-column" style="min-height: 540px;">
                        <!-- Chart injected dynamically -->
                    </div>
                </div>
            </div>

            <!-- Region 5: Trading Desk -->
            <div class="binance-panel tradingdesk-panel">
                <div class="tradingdesk-header-tabs">
                    <div class="td-main-tabs">
                        <span class="td-main-tab active" onclick="setMarginMode('spot', 1, this)">Spot</span>
                        <span class="td-main-tab" onclick="setMarginMode('cross', 3, this)">Cross 3x</span>
                        <span class="td-main-tab" onclick="setMarginMode('isolated', 10, this)">Isolated 10x</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-warning text-dark" id="leverage-badge" style="font-size: 0.65rem; font-weight: 800; padding: 3px 8px; border-radius: 4px; display: none;">3x</span>
                        <div class="text-secondary" style="font-size: 0.75rem;">
                            <i class="ri-settings-3-line"></i> Fee Level
                        </div>
                    </div>
                </div>

                <div class="td-sub-tabs">
                    <span class="td-sub-tab active" onclick="setOrderType('limit', this)">Limit</span>
                    <span class="td-sub-tab" onclick="setOrderType('market', this)">Market</span>
                    <span class="td-sub-tab" onclick="setOrderType('stop-limit', this)">Stop-limit</span>
                </div>

                <div class="td-columns">
                    <!-- Buy Panel -->
                    <div class="td-column">
                        <div class="td-avbl-row">
                            <span>Avbl</span>
                            <span class="td-avbl-val" id="td-buy-avbl">$0.00 USDT</span>
                        </div>

                        <!-- Stop-Limit Trigger Price Input (hidden by default) -->
                        <div class="td-input-group" id="buy-trigger-group" style="display: none;">
                            <span class="td-input-label">Trigger</span>
                            <input type="number" step="any" class="td-input-field" id="buy-trigger-price" placeholder="Trigger Price">
                            <span class="td-input-suffix">USDT</span>
                        </div>

                        <!-- Price Input -->
                        
<div class="td-input-group" style="margin-bottom: 12px;">
    <span class="td-input-label">Leverage</span>
    <input type="number" step="1" min="1" max="100" value="10" class="td-input-field" id="leverage-input" placeholder="10x">
    <span class="td-input-suffix">X</span>
</div>
<div class="td-input-group" id="buy-price-group">
                            <span class="td-input-label">Price</span>
                            <input type="number" step="any" class="td-input-field" id="buy-price" placeholder="0.00" oninput="recalcTotals('buy')">
                            <span class="td-input-suffix">USDT</span>
                        </div>

                        <!-- Amount Input -->
                        <div class="td-input-group">
                            <span class="td-input-label">Amount</span>
                            <input type="number" step="any" class="td-input-field" id="buy-amount" placeholder="0.00" oninput="recalcTotals('buy')">
                            <span class="td-input-suffix" id="buy-amt-suffix">BTC</span>
                        </div>

                        <!-- Percentage Slider -->
                        <div class="slider-wrap">
                            <input type="range" class="custom-range-input" id="buy-range" min="0" max="100" value="0" oninput="slidePercent('buy')">
                            <div class="percentage-points">
                                <span class="pct-point" onclick="clickPct('buy', 0)"></span>
                                <span class="pct-point" onclick="clickPct('buy', 25)"></span>
                                <span class="pct-point" onclick="clickPct('buy', 50)"></span>
                                <span class="pct-point" onclick="clickPct('buy', 75)"></span>
                                <span class="pct-point" onclick="clickPct('buy', 100)"></span>
                            </div>
                        </div>

                        <!-- Total Input -->
                        <div class="td-input-group">
                            <span class="td-input-label">Total</span>
                            <input type="number" step="any" class="td-input-field" id="buy-total" placeholder="0.00" oninput="recalcFromTotal('buy')">
                            <span class="td-input-suffix">USDT</span>
                        </div>

                        <button class="td-action-btn buy-btn" id="buy-btn-label" onclick="submitMarginOrder('buy')">Long BTC</button>
                    </div>

                    <!-- Sell Panel -->
                    <div class="td-column">
                        <div class="td-avbl-row">
                            <span>Avbl</span>
                            <span class="td-avbl-val" id="td-sell-avbl">0.000000 BTC</span>
                        </div>

                        <!-- Stop-Limit Trigger Price Input (hidden by default) -->
                        <div class="td-input-group" id="sell-trigger-group" style="display: none;">
                            <span class="td-input-label">Trigger</span>
                            <input type="number" step="any" class="td-input-field" id="sell-trigger-price" placeholder="Trigger Price">
                            <span class="td-input-suffix">USDT</span>
                        </div>

                        <!-- Price Input -->
                        
<div class="td-input-group" style="margin-bottom: 12px;">
    <span class="td-input-label">Leverage</span>
    <input type="number" step="1" min="1" max="100" value="10" class="td-input-field" id="leverage-input" placeholder="10x">
    <span class="td-input-suffix">X</span>
</div>
<div class="td-input-group" id="sell-price-group">
                            <span class="td-input-label">Price</span>
                            <input type="number" step="any" class="td-input-field" id="sell-price" placeholder="0.00" oninput="recalcTotals('sell')">
                            <span class="td-input-suffix">USDT</span>
                        </div>

                        <!-- Amount Input -->
                        <div class="td-input-group">
                            <span class="td-input-label">Amount</span>
                            <input type="number" step="any" class="td-input-field" id="sell-amount" placeholder="0.00" oninput="recalcTotals('sell')">
                            <span class="td-input-suffix" id="sell-amt-suffix">BTC</span>
                        </div>

                        <!-- Percentage Slider -->
                        <div class="slider-wrap">
                            <input type="range" class="custom-range-input" id="sell-range" min="0" max="100" value="0" oninput="slidePercent('sell')">
                            <div class="percentage-points">
                                <span class="pct-point" onclick="clickPct('sell', 0)"></span>
                                <span class="pct-point" onclick="clickPct('sell', 25)"></span>
                                <span class="pct-point" onclick="clickPct('sell', 50)"></span>
                                <span class="pct-point" onclick="clickPct('sell', 75)"></span>
                                <span class="pct-point" onclick="clickPct('sell', 100)"></span>
                            </div>
                        </div>

                        <!-- Total Input -->
                        <div class="td-input-group">
                            <span class="td-input-label">Total</span>
                            <input type="number" step="any" class="td-input-field" id="sell-total" placeholder="0.00" oninput="recalcFromTotal('sell')">
                            <span class="td-input-suffix">USDT</span>
                        </div>

                        <button class="td-action-btn sell-btn" id="sell-btn-label" onclick="submitMarginOrder('sell')">Short BTC</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Markets & Trades -->
        <div class="d-flex flex-column gap-3" style="grid-column: 3; height: 100%;">
            <!-- Region 4: Markets List -->
            <div class="binance-panel markets-panel">
                <div class="markets-search-container">
                    <i class="ri-search-2-line markets-search-icon"></i>
                    <input type="text" class="markets-search-input" id="market-search" placeholder="Search" oninput="filterMarkets()">
                </div>

                <div class="markets-tabs">
                    <span class="markets-tab-item active" onclick="setMarketTab('USDT')">USDT</span>
                    <span class="markets-tab-item" onclick="setMarketTab('BTC')">BTC</span>
                    <span class="markets-tab-item" onclick="setMarketTab('ETH')">ETH</span>
                    <span class="markets-tab-item" onclick="setMarketTab('ALTS')">ALTS</span>
                    <span class="markets-tab-item" onclick="setMarketTab('BNB')">BNB</span>
                </div>

                <div class="markets-list-header">
                    <div>Pair</div>
                    <div>Price</div>
                    <div style="text-align: right;">Change</div>
                </div>

                <div class="markets-list-container binance-scrollbar" id="markets-list-mount">
                    @foreach($assets as $a)
                    <div class="market-list-row market-item-row" 
                         data-symbol="{{ $a->symbol }}" 
                         data-name="{{ $a->name }}" 
                         data-id="{{ $a->id }}" 
                         data-price="{{ $a->buy }}" 
                         data-changes="{{ $a->changes }}" 
                         onclick="selectCryptoPair('{{ $a->id }}', '{{ $a->symbol }}', '{{ $a->buy }}', '{{ $a->name }}', '{{ $a->changes }}')">
                        <div class="market-list-symbol">
                            <i class="ri-star-line market-star"></i>
                              @php
                                  $quoteAsset = str_ends_with($a->symbol, 'USDT') ? 'USDT' : (str_ends_with($a->symbol, 'BTC') ? 'BTC' : (str_ends_with($a->symbol, 'ETH') ? 'ETH' : (str_ends_with($a->symbol, 'BNB') ? 'BNB' : 'USDT')));
                                  $baseAsset = substr($a->symbol, 0, -strlen($quoteAsset));
                              @endphp
                              <img src="{{ \App\Services\AssetLogoService::getLogoUrl($a->symbol, $a->type ?? 'crypto', $a->image ?? '') }}" onerror="this.onerror=null; this.src='/assets/img/profit.svg';" style="width: 20px; height: 20px; border-radius: 50%; margin-right: 5px;">
                              <span>{{ $baseAsset }}/{{ $quoteAsset }}</span>
                        </div>
                        <div class="market-list-price text-white">${{ number_format($a->buy, 2) }}</div>
                        <div class="market-list-change {{ $a->changes >= 0 ? 'green' : 'red' }}" data-live-change-symbol="{{ $a->symbol }}">
                            {{ $a->changes >= 0 ? '+' : '' }}{{ number_format($a->changes, 2) }}%
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Region 6: Market Trades -->
            <div class="binance-panel trades-panel">
                <div class="trades-header-tabs">
                    <span class="trades-tab active">Market Trades</span>
                    <span class="trades-tab">My Trades</span>
                </div>

                <div class="trades-list-header">
                    <div>Price(USDT)</div>
                    <div class="text-end" id="trades-amount-header">Amount(BTC)</div>
                    <div class="text-align-right" style="text-align: right;">Time</div>
                </div>

                <div class="trades-rows-container binance-scrollbar" id="trades-rows-mount">
                    <!-- Simulated live trade executions injected via JS -->
                </div>

                <!-- Market Activities widget -->
                <div class="market-activities-box">
                    <div class="ma-title">Market Activities</div>
                    <div class="ma-row">
                        <span class="ma-row-pair" id="ma-pair">SOL/USDT</span>
                        <span class="ma-row-percent" id="ma-percent">+3.23% in 5 min</span>
                        <span class="text-secondary" style="font-size: 0.65rem;" id="ma-time">19:55:04</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ledger/History Section (Full Width) -->
    <div class="binance-panel ledger-panel">
        <div class="ledger-header">
            <span class="ledger-title">Spot Execution Log</span>
            <span class="text-secondary small outfit">{{ $orders->total() }} Total Trades</span>
        </div>
        <div class="ledger-table-wrap">
            <div class="table-responsive">
                <table class="table text-white mb-0 align-middle" style="font-size:0.75rem; border-collapse: separate; border-spacing: 0 4px;">
                    <thead>
                        <tr class="text-secondary" style="border-bottom: 1px solid var(--binance-border);">
                            <th class="border-0">ASSET Symbol</th>
                            <th class="border-0">Type</th>
                            <th class="border-0">Amount</th>
                            <th class="border-0">Entry/Trigger Price</th>
                            <th class="border-0">Reserves (USD)</th>
                            <th class="border-0">Current Price</th>
                            <th class="border-0 text-end">Unrealized P/L</th>
                            <th class="border-0 text-end">Ledger Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $o)
                        <tr class="spot-order-row" data-id="{{ $o->id }}" data-symbol="{{ $o->symbol }}" data-type="{{ $o->type }}" data-price="{{ $o->price }}" data-amount="{{ $o->amount }}" data-ordertype="{{ $o->order_type }}" data-trigger="{{ $o->trigger_price }}" data-status="{{ $o->status }}" style="background: rgba(255,255,255,0.01); border-radius: 4px;">
                            <td class="py-2 border-0">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 24px; height: 24px; background: rgba(0,0,0,0.3); border: 1px solid var(--binance-border); padding: 3px;">
                                        <img src="{{ \App\Services\AssetLogoService::getLogoUrl($o->symbol, $o->asset->type ?? 'crypto', '') }}" onerror="this.onerror=null; this.src='/assets/img/profit.svg';" style="width: 100%; height: 100%; object-fit: contain;">
                                    </div>
                                    <span class="font-weight-bold text-white">{{ $o->symbol }}</span>
                                </div>
                            </td>
                            <td class="py-2 border-0">
                                <span class="ledger-badge-side {{ $o->type == 'buy' ? 'buy' : 'sell' }}">{{ strtoupper($o->type) }}</span>
                                @if($o->order_type != 'market')
                                    <span class="badge bg-secondary ms-1" style="font-size: 0.6rem;">{{ strtoupper($o->order_type) }}</span>
                                @endif
                            </td>
                            <td class="py-2 border-0 font-weight-bold">{{ number_format($o->amount, 6) }}</td>
                            <td class="py-2 border-0 text-secondary">${{ number_format($o->price, 2) }}</td>
                            <td class="py-2 border-0 text-white font-weight-bold">${{ number_format($o->total_usd, 2) }}</td>
                            <td class="py-2 border-0 text-secondary" id="sim-price-{{ $o->id }}">--</td>
                            <td class="py-2 border-0 text-end font-weight-bold" id="sim-pl-{{ $o->id }}">--</td>
                            <td class="py-2 border-0 text-end">
                                @if($o->status == 'pending')
                                <div class="pending-progress">
                                    <div class="d-flex justify-content-between w-100"><span class="progress-label">{{ $o->order_type == 'market' ? 'Filling' : 'Trigger' }}</span><span class="progress-pct">93%</span></div>
                                    <div class="progress-track"><div class="progress-fill"></div></div>
                                </div>
                                @else
                                <span id="sim-status-{{ $o->id }}" class="ledger-badge-status {{ $o->status == 'approved' ? 'approved' : 'rejected' }}">
                                    {{ strtoupper($o->status) }}
                                </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-secondary">No trades processed inside active ledger.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($orders->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $orders->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('js')
<script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>

@php
    $logoMap = [];
    $mirrorMap = [];
    foreach($assets as $a) {
        $logoMap[$a->symbol] = \App\Services\AssetLogoService::getLogoUrl($a->symbol, $a->type ?? 'crypto', $a->image ?? '');
        if(!empty($a->mirror_symbol)) { $mirrorMap[$a->symbol] = $a->mirror_symbol; }
    }
@endphp

<script>
    // State variables
    let currentAssetId = '{{ optional($assets->first())->id ?? "" }}';
    let currentSymbol = '{{ optional($assets->first())->symbol ?? "BTCUSDT" }}';
    let currentName = '{{ optional($assets->first())->name ?? "Bitcoin" }}';
    let currentPrice = parseFloat('{{ optional($assets->first())->buy ?? 30605.92 }}');
    let currentChange = parseFloat('{{ optional($assets->first())->changes ?? 5.12 }}');
    let activeInterval = '15';
    let marketActiveTab = 'USDT';

    var tickerDataMap = @json($tickerData ?? []);
    var logoMap = @json($logoMap ?? []);
    var mirrorMap = @json($mirrorMap ?? (object)[]);

    // Margin & Order Type state
    let currentMarginMode = 'spot';  // spot | cross | isolated
    let currentLeverage = 1;         // 1x (spot), 3x (cross), 10x (isolated)
    let currentOrderType = 'limit';  // limit | market | stop-limit

    const userUsdBalance = parseFloat('{{ auth()->user()->is_demo ? ($usdBalance->demo ?? 0) : ($usdBalance->amount ?? 0) }}');
    const userHoldings = @json($holdings);

    $(document).ready(function() {
        // Initial setup
        selectCryptoPair(currentAssetId, currentSymbol, currentPrice, currentName, currentChange);

        // Chart interval switcher
        $('.chart-int-btn').click(function() {
            $('.chart-int-btn').removeClass('active');
            $(this).addClass('active');
            activeInterval = $(this).attr('data-val');
            initTradingViewWidget(currentSymbol, activeInterval);
        });

        // Run simulations
        startOrderBookSimulation();
        startMarketTradesSimulation();
        startMarketActivitiesSimulation();
    });

    // Mount TradingView Widget
    function initTradingViewWidget(symbol, interval) {
        // Clear existing widget DOM to prevent overlapping bug
        document.getElementById('chart-mount').innerHTML = '<div id="tradingview_widget" style="width: 100%; height: 100%;" class="flex-grow-1"></div>';
        let lookupSymbol = mirrorMap[symbol] || symbol;
        let cleanSymbol = lookupSymbol.toUpperCase();
        let tvSymbol = "BINANCE:" + (cleanSymbol.endsWith("USDT") ? cleanSymbol : cleanSymbol + "USDT");

        window.tvWidget = new TradingView.widget({
            "autosize": true,
            "symbol": tvSymbol,
            "interval": interval,
            "timezone": "Etc/UTC",
            "theme": "dark",
            "style": "1",
            "locale": "en",
            "toolbar_bg": "#000000",
            "enable_publishing": false,
            "allow_symbol_change": false,
            "container_id": "tradingview_widget",
            "backgroundColor": "#000000",
            "gridColor": "rgba(255, 255, 255, 0.02)",
            "save_image": false,
            "hide_side_toolbar": true,
            "hide_top_toolbar": false
        });
    }

    // Switch pairs
    function selectCryptoPair(id, symbol, price, name, changes) {
        currentAssetId = id;
        currentSymbol = symbol;
        currentName = name;
        currentPrice = parseFloat(price);
        currentChange = parseFloat(changes);

        // UI modifications
        let cleanBase = symbol.replace('USDT', '');
        $('#header-symbol-display').text(cleanBase + '/USDT');
        $('#header-ticker-link').text(name + ' Price');
        $('#header-main-price').text(currentPrice.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));

        // Change values
        let changeEl = $('#header-change-val');
        changeEl.attr('data-live-change-symbol', currentSymbol);
        changeEl.attr('data-is-header', 'true');
        let changePct = currentChange.toFixed(2) + '%';
        let changeAbs = (currentPrice * currentChange / 100).toFixed(2);
        changeEl.text((currentChange >= 0 ? '+' : '') + changeAbs + ' ' + (currentChange >= 0 ? '+' : '') + changePct);
        if (currentChange >= 0) {
            changeEl.removeClass('red').addClass('green');
        } else {
            changeEl.removeClass('green').addClass('red');
        }

        // High, Low, Volumes
        let ticker = tickerDataMap[symbol];
        let rangeLow = ticker && ticker.lowPrice ? parseFloat(ticker.lowPrice) : currentPrice * 0.965;
        let rangeHigh = ticker && ticker.highPrice ? parseFloat(ticker.highPrice) : currentPrice * 1.035;
        let volBase = ticker && ticker.volume ? parseFloat(ticker.volume) : 1254300 / currentPrice;
        let volQuote = ticker && ticker.quoteVolume ? parseFloat(ticker.quoteVolume) : 1254300 * 1.25;

        $('#header-high-val').text(rangeHigh.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#header-low-val').text(rangeLow.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#header-vol-base-label').text('24h Volume(' + cleanBase + ')');
        $('#header-vol-base').text(volBase.toLocaleString(undefined, {maximumFractionDigits: 2}));
        $('#header-vol-quote').text('$' + volQuote.toLocaleString(undefined, {maximumFractionDigits: 2}));

        // Logo
        let logoUrl = logoMap[symbol] || '/assets/img/profit.svg';
        $('#header-logo-mount').html(`<img src="${logoUrl}" onerror="this.onerror=null; this.src='/assets/img/profit.svg';">`);

        // Labels in trade desk
        $('#buy-btn-label').text('Buy ' + cleanBase);
        $('#sell-btn-label').text('Sell ' + cleanBase);
        $('#buy-amt-suffix').text(cleanBase);
        $('#sell-amt-suffix').text(cleanBase);
        $('#ob-amt-header').text('Amount(' + cleanBase + ')');
        $('#trades-amount-header').text('Amount(' + cleanBase + ')');

        // Inputs default values
        $('#buy-price').val(currentPrice.toFixed(2));
        $('#sell-price').val(currentPrice.toFixed(2));
        $('#buy-amount').val('');
        $('#sell-amount').val('');
        $('#buy-total').val('');
        $('#sell-total').val('');
        $('#buy-range').val(0);
        $('#sell-range').val(0);
        $('.pct-point').removeClass('active');

        // Balances
        $('#td-buy-avbl').text(userUsdBalance.toLocaleString(undefined, {minimumFractionDigits: 2}) + ' USDT');
        let heldObj = userHoldings[symbol];
        let heldAmount = heldObj ? parseFloat(heldObj.amount) : 0;
        $('#td-sell-avbl').text(heldAmount.toFixed(6) + ' ' + cleanBase);

        // Load chart & update simulations
        initTradingViewWidget(symbol, activeInterval);
        simulateOrderBookData();
        simulateMarketTradesData();
    }

    // Trade Desk Form Recalculations
    function recalcTotals(side) {
        let price = parseFloat($(`#${side}-price`).val()) || 0;
        let amount = parseFloat($(`#${side}-amount`).val()) || 0;
        let total = price * amount;
        $(`#${side}-total`).val(total.toFixed(2));
        updateSliderPoint(side);
    }

    function recalcFromTotal(side) {
        let price = parseFloat($(`#${side}-price`).val()) || 0;
        let total = parseFloat($(`#${side}-total`).val()) || 0;
        let amount = price > 0 ? total / price : 0;
        $(`#${side}-amount`).val(amount.toFixed(6));
        updateSliderPoint(side);
    }

    function slidePercent(side) {
        let val = parseInt($(`#${side}-range`).val());
        applyPercentageValue(side, val);
    }

    function clickPct(side, val) {
        $(`#${side}-range`).val(val);
        applyPercentageValue(side, val);
    }

    function applyPercentageValue(side, val) {
        // Highlight active point nodes
        let cols = $(`#${side}-range`).siblings('.percentage-points').find('.pct-point');
        cols.removeClass('active');
        if (val === 0) cols.eq(0).addClass('active');
        else if (val === 25) cols.eq(1).addClass('active');
        else if (val === 50) cols.eq(2).addClass('active');
        else if (val === 75) cols.eq(3).addClass('active');
        else if (val === 100) cols.eq(4).addClass('active');

        let price = parseFloat($(`#${side}-price`).val()) || currentPrice;

        if (side === 'buy') {
            let buyPower = userUsdBalance * (val / 100);
            $(`#${side}-total`).val(buyPower.toFixed(2));
            let amount = price > 0 ? buyPower / price : 0;
            $(`#${side}-amount`).val(amount.toFixed(6));
        } else {
            let heldObj = userHoldings[currentSymbol];
            let heldAmount = heldObj ? parseFloat(heldObj.amount) : 0;
            let sellAmt = heldAmount * (val / 100);
            $(`#${side}-amount`).val(sellAmt.toFixed(6));
            let total = sellAmt * price;
            $(`#${side}-total`).val(total.toFixed(2));
        }
    }

    function updateSliderPoint(side) {
        let val = 0;
        let price = parseFloat($(`#${side}-price`).val()) || currentPrice;
        let total = parseFloat($(`#${side}-total`).val()) || 0;

        if (side === 'buy') {
            val = userUsdBalance > 0 ? (total / userUsdBalance) * 100 : 0;
        } else {
            let heldObj = userHoldings[currentSymbol];
            let heldAmount = heldObj ? parseFloat(heldObj.amount) : 0;
            let amt = parseFloat($(`#${side}-amount`).val()) || 0;
            val = heldAmount > 0 ? (amt / heldAmount) * 100 : 0;
        }

        $(`#${side}-range`).val(Math.min(100, Math.round(val)));
    }

    // ===== Margin Mode Switching =====
    function setMarginMode(mode, leverage, el) {
        currentMarginMode = mode;
        currentLeverage = leverage;
        $('.td-main-tab').removeClass('active');
        $(el).addClass('active');

        // Leverage badge
        if (leverage > 1) {
            $('#leverage-badge').text(leverage + 'x').show();
        } else {
            $('#leverage-badge').hide();
        }

        // Update available balance display with leverage
        updateAvailableBalanceDisplay();

        // Recalculate totals with new leverage
        recalcTotals('buy');
        recalcTotals('sell');

        let modeLabel = mode === 'spot' ? 'Spot' : (mode === 'cross' ? 'Cross ' + leverage + 'x' : 'Isolated ' + leverage + 'x');
        toastr.info('Switched to ' + modeLabel + ' trading mode.', 'MODE CHANGED');
    }

    // ===== Order Type Switching =====
    function setOrderType(type, el) {
        currentOrderType = type;
        $('.td-sub-tab').removeClass('active');
        $(el).addClass('active');

        // Toggle price input visibility based on order type
        if (type === 'market') {
            // Market order — hide price inputs, use current market price
            $('#buy-price-group, #sell-price-group').hide();
            $('#buy-trigger-group, #sell-trigger-group').hide();
            // Set price fields to current price for calculations
            $('#buy-price').val(currentPrice.toFixed(2));
            $('#sell-price').val(currentPrice.toFixed(2));
        } else if (type === 'stop-limit') {
            // Stop-limit — show trigger price + limit price
            $('#buy-price-group, #sell-price-group').show();
            $('#buy-trigger-group, #sell-trigger-group').show();
            $('#buy-price').attr('placeholder', 'Limit Price');
            $('#sell-price').attr('placeholder', 'Limit Price');
        } else {
            // Limit order — standard behavior
            $('#buy-price-group, #sell-price-group').show();
            $('#buy-trigger-group, #sell-trigger-group').hide();
            $('#buy-price').attr('placeholder', '0.00');
            $('#sell-price').attr('placeholder', '0.00');
        }

        recalcTotals('buy');
        recalcTotals('sell');
    }

    // ===== Update Available Balance with Leverage =====
    function updateAvailableBalanceDisplay() {
        let leveragedBalance = userUsdBalance * currentLeverage;
        $('#td-buy-avbl').text(leveragedBalance.toLocaleString(undefined, {minimumFractionDigits: 2}) + ' USDT');
        if (currentLeverage > 1) {
            $('#td-buy-avbl').append(' <small class="text-warning">(' + currentLeverage + 'x)</small>');
        }

        let cleanBase = currentSymbol.replace('USDT', '');
        let heldObj = userHoldings[currentSymbol];
        let heldAmount = heldObj ? parseFloat(heldObj.amount) : 0;
        let leveragedHolding = heldAmount * currentLeverage;
        $('#td-sell-avbl').text(leveragedHolding.toFixed(6) + ' ' + cleanBase);
        if (currentLeverage > 1) {
            $('#td-sell-avbl').append(' <small class="text-warning">(' + currentLeverage + 'x)</small>');
        }
    }

    // Submit Order
    function submitMarginOrder(side) {
        let price = parseFloat($(`#${side}-price`).val()) || currentPrice;
        let amount = parseFloat($(`#${side}-amount`).val()) || 0;

        if (amount <= 0) {
            toastr.error('Please enter a valid amount to trade.', 'INVALID AMOUNT');
            return;
        }

        // Validate stop-limit trigger price
        if (currentOrderType === 'stop-limit') {
            let triggerPrice = parseFloat($(`#${side}-trigger-price`).val()) || 0;
            if (triggerPrice <= 0) {
                toastr.error('Please enter a valid trigger price.', 'MISSING TRIGGER');
                return;
            }
        }

        let btn = $(`#${side}-btn-label`);
        let originalText = btn.text();
        btn.prop('disabled', true).html('<i class="ri-loader-4-line ri-spin"></i> Processing...');

        // For market orders, always use current price
        let orderPrice = currentOrderType === 'market' ? currentPrice : price;

        // Map buy orders to submit total USD, sells to submit base token amount
        let mappedAmount = side === 'buy' ? (orderPrice * amount) / currentPrice : amount;

        let orderPayload = {
            id: currentAssetId,
            type: side,
            amount: mappedAmount,
            price: currentPrice,
            order_type: currentOrderType,
            margin_mode: currentMarginMode,
            leverage: currentLeverage
        };

        // Include trigger price for stop-limit orders
        if (currentOrderType === 'stop-limit') {
            orderPayload.trigger_price = parseFloat($(`#${side}-trigger-price`).val()) || 0;
        }

        fetch("{{ route('margin.open') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            body: JSON.stringify(orderPayload)
        })
        .then(res => res.json().then(data => ({ok: res.ok, data: data})))
        .then(({ok, data}) => {
            if (ok && data.status === 'pending_approval') {
                const originalHtml = btn.html();
                let pct = 0;
                btn.html(`<i class="ri-loader-4-line ri-spin"></i> <span class="loader-pct">0</span>%`);
                setInterval(() => {
                    pct++;
                    if(pct > 99) pct = 0;
                    btn.find('.loader-pct').text(pct);
                }, 30); // 3 seconds total to hit 100 roughly
                
                toastr.info('Trade placed. Awaiting approval...');
                setTimeout(() => window.location.reload(), 3000);
            } else if (ok && data.status) {
                toastr.success(data.status, 'SUCCESS');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                toastr.error(data.error || 'Execution rejected.', 'REJECTED');
                btn.prop('disabled', false).text(originalText);
            }
        })
        .catch(err => {
            console.error(err);
            toastr.error('Network connectivity disruption.', 'ERROR');
            btn.prop('disabled', false).text(originalText);
        });
    }

    // Market Search & Tabs filtering
    function setMarketTab(tab) {
        marketActiveTab = tab;
        $('.markets-tab-item').removeClass('active');
        $(event.target).addClass('active');
        filterMarkets();
    }

    function filterMarkets() {
        let query = $('#market-search').val().toLowerCase().trim();
        $('.market-item-row').each(function() {
            let symbol = $(this).attr('data-symbol').toUpperCase();
            let name = $(this).attr('data-name').toLowerCase();
            
            let matchesSearch = symbol.includes(query) || name.includes(query);
            let matchesTab = false;

            if (marketActiveTab === 'USDT') {
                matchesTab = symbol.endsWith('USDT');
            } else if (marketActiveTab === 'BTC') {
                matchesTab = symbol.endsWith('BTC') || (symbol.includes('BTC') && !symbol.endsWith('USDT'));
            } else if (marketActiveTab === 'BNB') {
                matchesTab = symbol.includes('BNB');
            } else if (marketActiveTab === 'ETH') {
                matchesTab = symbol.endsWith('ETH') || (symbol.includes('ETH') && !symbol.endsWith('USDT') && !symbol.endsWith('BTC'));
            } else {
                // ALTS
                matchesTab = !symbol.endsWith('USDT') && !symbol.endsWith('BTC') && !symbol.includes('BNB') && !symbol.endsWith('ETH') && !symbol.includes('ETH');
            }

            if (matchesSearch && matchesTab) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    // Simulated Order Book generator
    let activeObLayout = 'both';
    function setObLayout(layout) {
        activeObLayout = layout;
        $('.ob-layout-btn').removeClass('active');
        $(`#ob-layout-${layout}`).addClass('active');

        if (layout === 'both') {
            $('#ob-asks-container').show().css('flex', '1');
            $('#ob-bids-container').show().css('flex', '1');
        } else if (layout === 'asks') {
            $('#ob-asks-container').show().css('flex', '1');
            $('#ob-bids-container').hide();
        } else {
            $('#ob-asks-container').hide();
            $('#ob-bids-container').show().css('flex', '1');
        }
        simulateOrderBookData();
    }

    function simulateOrderBookData() {
        let asksCont = $('#ob-asks-container');
        let bidsCont = $('#ob-bids-container');
        asksCont.empty();
        bidsCont.empty();

        let midPrice = currentPrice;
        let step = currentPrice * 0.0003;
        
        let numRows = activeObLayout === 'both' ? 24 : 48;

        // Generate asks (higher prices)
        if (activeObLayout === 'both' || activeObLayout === 'asks') {
            for (let i = 1; i <= numRows; i++) {
                let p = midPrice + (i * step);
                let a = Math.random() * (12 / i) + 0.01;
                let total = p * a;
                let barPct = Math.min(100, (a / 5) * 100);

                asksCont.append(`
                    <div class="ob-row" onclick="fillPriceField(${p.toFixed(2)})">
                        <div class="ob-row-bg-bar" style="background-color: var(--binance-red); width: ${barPct}%;"></div>
                        <div class="ob-row-val price-ask">${p.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
                        <div class="ob-row-val text-end">${a.toFixed(5)}</div>
                        <div class="ob-row-val text-end">${total.toLocaleString(undefined, {maximumFractionDigits: 0})}</div>
                    </div>
                `);
            }
        }

        // Generate bids (lower prices)
        if (activeObLayout === 'both' || activeObLayout === 'bids') {
            for (let i = 1; i <= numRows; i++) {
                let p = midPrice - (i * step);
                let a = Math.random() * (12 / i) + 0.01;
                let total = p * a;
                let barPct = Math.min(100, (a / 5) * 100);

                bidsCont.append(`
                    <div class="ob-row" onclick="fillPriceField(${p.toFixed(2)})">
                        <div class="ob-row-bg-bar" style="background-color: var(--binance-green); width: ${barPct}%;"></div>
                        <div class="ob-row-val price-bid">${p.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
                        <div class="ob-row-val text-end">${a.toFixed(5)}</div>
                        <div class="ob-row-val text-end">${total.toLocaleString(undefined, {maximumFractionDigits: 0})}</div>
                    </div>
                `);
            }
        }

        $('#ob-mid-price-mount').html(`${midPrice.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})} <i class="ri-arrow-up-fill"></i>`);
        $('#ob-mid-usd-mount').text('$' + midPrice.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    }

    function startOrderBookSimulation() {
        setInterval(() => {
            // Apply slight fluctuation
            currentPrice = currentPrice * (1 + (Math.random() * 0.0006 - 0.0003));
            $('#header-main-price').text(currentPrice.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            simulateOrderBookData();
        }, 1800);
    }

    function fillPriceField(price) {
        $('#buy-price').val(price.toFixed(2));
        $('#sell-price').val(price.toFixed(2));
        recalcTotals('buy');
        recalcTotals('sell');
    }

    // Market Trades Simulation
    function simulateMarketTradesData() {
        let mount = $('#trades-rows-mount');
        mount.empty();

        for (let i = 0; i < 15; i++) {
            let isBuy = Math.random() > 0.48;
            let p = currentPrice * (1 + (Math.random() * 0.002 - 0.001));
            let a = Math.random() * 2.5 + 0.005;
            let timeStr = new Date(Date.now() - i * 4000).toTimeString().split(' ')[0];

            mount.append(`
                <div class="trades-row">
                    <span class="trades-row-price ${isBuy ? 'green' : 'red'}">${p.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                    <span class="text-end text-white">${a.toFixed(5)}</span>
                    <span class="trades-row-time">${timeStr}</span>
                </div>
            `);
        }
    }

    function startMarketTradesSimulation() {
        setInterval(() => {
            let mount = $('#trades-rows-mount');
            let isBuy = Math.random() > 0.45;
            let p = currentPrice * (1 + (Math.random() * 0.001 - 0.0005));
            let a = Math.random() * 1.8 + 0.001;
            let timeStr = new Date().toTimeString().split(' ')[0];

            let newRow = `
                <div class="trades-row" style="display: none;">
                    <span class="trades-row-price ${isBuy ? 'green' : 'red'}">${p.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                    <span class="text-end text-white">${a.toFixed(5)}</span>
                    <span class="trades-row-time">${timeStr}</span>
                </div>
            `;
            mount.prepend(newRow);
            mount.find('.trades-row').first().slideDown(150);
            if (mount.find('.trades-row').length > 25) {
                mount.find('.trades-row').last().remove();
            }
        }, 2200);
    }

    // Market activities random banners
    const activitiesCrypto = ['SOL', 'AVAX', 'ETH', 'BTC', 'ADA', 'XRP', 'BNB'];
    function startMarketActivitiesSimulation() {
        setInterval(() => {
            let c = activitiesCrypto[Math.floor(Math.random() * activitiesCrypto.length)];
            let change = (Math.random() * 4 + 1.2).toFixed(2);
            let timeStr = new Date().toTimeString().split(' ')[0];

            $('#ma-pair').text(c + '/USDT');
            $('#ma-percent').text('+' + change + '% in 5 min');
            $('#ma-time').text(timeStr);
        }, 12000);
    }
    // Spot P/L and Limit Order Simulation
    function startSpotSimulation() {
        setInterval(() => {
            // Build map of current prices from market list
            let livePrices = {};
            $('.market-item-row').each(function() {
                let sym = $(this).attr('data-symbol').toUpperCase();
                let prc = parseFloat($(this).attr('data-price'));
                livePrices[sym] = prc;
            });
            // Override currently active chart symbol with hyper-active tick price
            livePrices[currentSymbol] = currentPrice;

            $('.spot-order-row').each(function() {
                let $row = $(this);
                let id = $row.attr('data-id');
                let symbol = $row.attr('data-symbol').toUpperCase();
                let type = $row.attr('data-type'); // buy / sell
                let orderType = $row.attr('data-ordertype'); // market / limit / stop-limit
                let entryPrice = parseFloat($row.attr('data-price'));
                let amount = parseFloat($row.attr('data-amount'));
                let triggerPrice = parseFloat($row.attr('data-trigger'));
                let status = $row.attr('data-status');

                if (status !== 'pending') {
                    // For approved/rejected, we don't simulate live P/L (or you can show final, but let's keep it static)
                    return; 
                }

                let livePrc = livePrices[symbol];
                if (!livePrc) return;

                // For P/L calc, if it's a Buy, we profit if current > entry. If Sell, profit if entry > current.
                // However, in spot, a Sell means you already have USD, so no P/L. But as a simulation, we can show it as short.
                let pl = 0;

                // Handle Limit / Stop-Limit Triggers visually
                let isTriggered = false;
                if (orderType === 'market') {
                    isTriggered = true;
                } else if (orderType === 'limit') {
                    if (type === 'buy' && livePrc <= entryPrice) isTriggered = true;
                    if (type === 'sell' && livePrc >= entryPrice) isTriggered = true;
                } else if (orderType === 'stop-limit') {
                    if (type === 'buy' && livePrc >= triggerPrice) isTriggered = true;
                    if (type === 'sell' && livePrc <= triggerPrice) isTriggered = true;
                }

                if (isTriggered) {
                    if (type === 'buy') {
                        pl = (livePrc - entryPrice) * amount;
                    } else {
                        pl = (entryPrice - livePrc) * amount;
                    }

                    // Fluctuate the P/L slightly every tick to make it look hyper-active
                    pl = pl + (Math.random() * (pl * 0.02 || 0.5) * (Math.random() > 0.5 ? 1 : -1));

                    let plColor = pl >= 0 ? 'text-success' : 'text-danger';
                    let plSign = pl >= 0 ? '+' : '';
                    
                    $(`#sim-price-${id}`).text(`$${livePrc.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})}`);
                    $(`#sim-pl-${id}`).html(`<span class="${plColor}">${plSign}$${pl.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})}</span>`);
                    
                    let badge = $(`#sim-status-${id}`);
                    if (badge.text().trim() !== 'OPEN POSITION') {
                        badge.removeClass('pending').addClass('approved').text('OPEN POSITION');
                        badge.css('background', 'rgba(255, 51, 51, 0.1)').css('color', '#ff3333');
                    }
                } else {
                    $(`#sim-price-${id}`).text(`$${livePrc.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})}`);
                    $(`#sim-pl-${id}`).html(`<span class="text-secondary">Waiting Trigger</span>`);
                    
                    let badge = $(`#sim-status-${id}`);
                    if (badge.text().trim() !== 'PENDING TRIGGER') {
                        badge.removeClass('approved').addClass('pending').text('PENDING TRIGGER');
                    }
                }
            });
        }, 1000);
    }

    // Call the new simulation function
    $(document).ready(function() {
        startSpotSimulation();
    });
</script>
@endpush


