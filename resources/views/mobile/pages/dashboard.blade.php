@extends('layouts.user.app')
@section('title', 'Mobile Dashboard')
@push('css')
    <style>
        :root {
            --bg: #050505;
            --surface: rgba(255, 255, 255, 0.02);
            --surface-border: rgba(255, 255, 255, 0.06);
            --gold: #FFD700;
            --gold-dim: rgba(153, 0, 0, 0.2);
            --green: #00E676;
            --red: #FF3D00;
            --t1: #FFFFFF;
            --t2: rgba(255, 255, 255, 0.7);
            --t3: rgba(255, 255, 255, 0.4);
            --radius: 24px;
        }
        
        .btn-active:active { transform: scale(0.96); opacity: 0.8; }
        
        /* Glassmorphism */
        .glass-card {
            background: var(--surface);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--surface-border);
            border-radius: var(--radius);
        }

        /* Top Header - Removed since we use app.blade.php header */
        /* Premium Portfolio Card */
        .portfolio-card {
            padding: 24px !important; margin: 0 16px 24px !important;
            background: linear-gradient(135deg, rgba(255,215,0,0.12) 0%, rgba(153,0,0,0.02) 100%);
            border: 1px solid rgba(255,215,0,0.2);
            position: relative; overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.1);
            animation: fadeIn 0.8s ease-out;
            border-radius: 24px;
        }
        /* Glow Mesh inside Card */
        .portfolio-card::before {
            content: ''; position: absolute; top: -50px; right: -50px; width: 150px; height: 150px;
            background: radial-gradient(circle, rgba(255,215,0,0.15) 0%, transparent 70%);
            border-radius: 50%; z-index: 0; pointer-events: none;
        }
        .portfolio-content { position: relative; z-index: 1; }
        .portfolio-label { color: var(--gold); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; display: flex; align-items: center; gap: 6px; }
        .portfolio-value { font-size: 32px; font-weight: 800; letter-spacing: -1px; margin-bottom: 8px; font-family: 'Inter', sans-serif; text-shadow: 0 2px 10px rgba(255,215,0,0.2); }
        .portfolio-change { font-size: 13px; color: var(--green); display: flex; align-items: center; gap: 6px; font-weight: 600; background: rgba(0,230,118,0.1); padding: 4px 10px; border-radius: 20px; display: inline-flex; }

        /* Quick Actions Grid */
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px 8px;
            padding: 0 20px 24px;
            animation: slideUp 0.6s ease-out;
        }
        .action-btn {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px; 
            padding: 12px 4px;
            display: flex; flex-direction: column; align-items: center; gap: 8px;
            text-decoration: none; color: var(--t1); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .action-icon { 
            width: 42px; height: 42px; border-radius: 12px; 
            background: linear-gradient(135deg, rgba(255,215,0,0.1), rgba(153,0,0,0.02)); 
            border: 1px solid rgba(255,215,0,0.1);
            display: flex; align-items: center; justify-content: center; 
            font-size: 20px; color: var(--gold); 
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .action-label { font-size: 11px; font-weight: 600; letter-spacing: 0px; text-align: center; line-height: 1.2; }

        /* Section Title */
        .section-header { display: flex; justify-content: space-between; align-items: center; margin: 0 24px 16px; }
        .section-title { font-size: 20px; font-weight: 700; color: var(--t1); }
        .section-link { font-size: 13px; color: var(--gold); font-weight: 600; text-decoration: none; }

        /* Assets List */
        .asset-list { display: flex; flex-direction: column; gap: 10px; padding: 0 20px 24px; animation: slideUp 0.8s ease-out; }
        .asset-item { 
            padding: 14px 16px; display: flex; justify-content: space-between; align-items: center; 
            text-decoration: none; color: inherit; transition: 0.3s;
            background: linear-gradient(90deg, var(--surface) 0%, rgba(255,255,255,0.01) 100%);
        }
        .asset-left { display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0; }
        .asset-icon { 
            width: 38px; height: 38px; border-radius: 12px; flex-shrink: 0;
            background: #111; display: flex; align-items: center; justify-content: center; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.05);
        }
        .asset-left > div { min-width: 0; }
        .asset-name { font-size: 15px; font-weight: 700; margin-bottom: 2px; color: var(--t1); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .asset-symbol { font-size: 11px; color: var(--t3); font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .asset-right { text-align: right; flex-shrink: 0; margin-left: 10px; }
        .asset-balance { font-size: 15px; font-weight: 700; margin-bottom: 2px; font-family: 'Inter', sans-serif; white-space: nowrap; }
        .asset-usd { font-size: 11px; font-weight: 600; white-space: nowrap; }

        /* Animations */
        @keyframes slideDown { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        @keyframes fadeIn { from { opacity: 0; scale: 0.95; } to { opacity: 1; scale: 1; } }
    </style>
@endpush

@section('content')
<div class="content">

    <!-- Portfolio Card -->
    <div class="portfolio-card glass-card btn-active">
        <div class="portfolio-content">
            <div class="portfolio-label"><i class="ri-secure-payment-line"></i> Total Balance</div>
            <div class="portfolio-value">$<span class="odometer">{{ number_format($usd ?? 0, 2) }}</span></div>
            <div class="portfolio-change">
                <i class="ri-arrow-right-up-line"></i> Active Status
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="actions-grid">
        <a href="{{ route('deposit') }}" class="action-btn btn-active">
            <div class="action-icon"><i class="ri-arrow-down-circle-line"></i></div>
            <span class="action-label">Deposit</span>
        </a>
        <a href="{{ route('withdraw') }}" class="action-btn btn-active">
            <div class="action-icon"><i class="ri-arrow-up-circle-line"></i></div>
            <span class="action-label">Withdraw</span>
        </a>
        <a href="{{ route('dashboard.trade-home') }}" class="action-btn btn-active">
            <div class="action-icon"><i class="ri-exchange-funds-line"></i></div>
            <span class="action-label">Trade</span>
        </a>
        <a href="{{ auth()->user()->hasFeature('copy_trading') ? route('copy-trading.index') : route('user.upgrade') }}" class="action-btn btn-active">
            <div class="action-icon"><i class="ri-user-received-2-line"></i></div>
            <span class="action-label">Copy Trade</span>
        </a>
        <a href="{{ auth()->user()->hasFeature('bot_trading') ? route('bot') : route('user.upgrade') }}" class="action-btn btn-active">
            <div class="action-icon"><i class="ri-robot-2-line"></i></div>
            <span class="action-label">Bot Trade</span>
        </a>
        <a href="{{ route('stocks.trade') }}" class="action-btn btn-active">
            <div class="action-icon"><i class="ri-stock-line"></i></div>
            <span class="action-label">Stocks/ETFs</span>
        </a>
        <a href="{{ auth()->user()->hasFeature('mutual_funds') ? route('user.mutual_funds') : route('user.upgrade') }}" class="action-btn btn-active">
            <div class="action-icon"><i class="ri-pie-chart-line"></i></div>
            <span class="action-label">Mutual Funds</span>
        </a>
        <a href="{{ route('dashboard.portfolio') }}" class="action-btn btn-active">
            <div class="action-icon"><i class="ri-pie-chart-2-line"></i></div>
            <span class="action-label">Portfolio</span>
        </a>
    </div>

    <div class="section-header">
        <h2 class="section-title">Your Assets</h2>
        <a href="{{ route('dashboard.wallets') }}" class="section-link">View All</a>
    </div>
    
    @php
        $myWallets = \App\Models\UserWallet::where('user_id', auth()->id())
            ->orderBy('balance', 'desc')
            ->take(4)
            ->get();
            
        if ($myWallets->isEmpty()) {
            $fallbackSymbols = ['BTC', 'ETH', 'USDT'];
            foreach ($fallbackSymbols as $sym) {
                $myWallets->push((object)[
                    'coin_symbol' => $sym,
                    'balance' => 0
                ]);
            }
        }
    @endphp

    <style>
        .asset-scroller {
            display: flex;
            flex-wrap: nowrap;
            gap: 16px;
            padding: 0 20px 24px;
            overflow-x: auto;
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none;  /* IE and Edge */
            -webkit-overflow-scrolling: touch;
            margin: 0 -10px; /* offset padding slightly */
        }
        .asset-scroller::-webkit-scrollbar {
            display: none;
        }
        .asset-square-card {
            min-width: 130px;
            height: 140px;
            padding: 16px;
            border-radius: 20px;
            background: linear-gradient(145deg, rgba(255,255,255,0.04), rgba(255,255,255,0.01));
            border: 1px solid rgba(255,255,255,0.05);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: 0.3s;
            text-decoration: none;
            color: var(--t1);
            flex-shrink: 0;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
        .asset-square-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .asset-square-icon {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: rgba(0,0,0,0.4);
            display: flex; align-items: center; justify-content: center;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .asset-square-name { font-size: 15px; font-weight: 700; color: var(--t1); margin-top: 4px; }
        .asset-square-symbol { font-size: 11px; color: var(--t3); text-transform: uppercase; font-weight: 500; }
        .asset-square-bottom {
            display: flex;
            flex-direction: column;
        }
        .asset-square-balance { font-size: 16px; font-weight: 700; font-family: 'Inter', sans-serif; letter-spacing: -0.5px; }
        .asset-square-usd { font-size: 11px; font-weight: 600; color: #00E676; display: flex; align-items: center; gap: 4px;}
    </style>
    
    <div class="asset-scroller">
        @php
            $stockAssets = [
                ['symbol' => 'AAPL', 'balance' => 0.0000],
                ['symbol' => 'TSLA', 'balance' => 0.0000],
                ['symbol' => 'MSFT', 'balance' => 0.0000],
            ];
        @endphp
        @foreach($stockAssets as $stock)
        <a href="{{ route('stocks.trade') }}" class="asset-square-card btn-active">
            <div class="asset-square-top">
                <div class="asset-square-icon">
                    <img src="{{ \App\Services\AssetLogoService::getLogoUrl($stock['symbol'], 'stock', '') }}" style="width:20px; height:20px; object-fit: contain;" onerror="this.src='/assets/img/profit.svg'">
                </div>
            </div>
            <div>
                <div class="asset-square-name">{{ $stock['symbol'] }}</div>
                <div class="asset-square-symbol">STOCK ASSET</div>
            </div>
            <div class="asset-square-bottom">
                <div class="asset-square-balance">{{ number_format($stock['balance'], 4) }}</div>
                <div class="asset-square-usd"><i class="ri-checkbox-circle-fill"></i> Secure</div>
            </div>
        </a>
        @endforeach
    </div>

    <!-- Market Overview Chart -->
    <div class="section-header" style="margin-top: 8px;">
        <h2 class="section-title">Market Overview</h2>
    </div>
    <div class="chart-container" style="padding: 0 20px 24px;">
        <div class="tradingview-widget-container" style="border-radius: 20px; overflow: hidden; height: 320px; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
            <div id="tradingview_mobile" style="height:100%;width:100%"></div>
            <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
            <script type="text/javascript">
            new TradingView.widget({
                "autosize": true,
                "symbol": "SPACEX",
                "interval": "D",
                "timezone": "Etc/UTC",
                "theme": "dark",
                "style": "1",
                "locale": "en",
                "toolbar_bg": "#f1f3f6",
                "enable_publishing": false,
                "allow_symbol_change": false,
                "container_id": "tradingview_mobile",
                "backgroundColor": "rgba(0,0,0,0)",
                "gridColor": "rgba(255, 255, 255, 0.02)",
                "hide_top_toolbar": false,
                "hide_legend": false,
                "save_image": false
            });
            </script>
        </div>
    </div>

    <div class="section-header">
        <h2 class="section-title">Recent Trades</h2>
        <a href="{{ route('trades.history') }}" class="section-link">History</a>
    </div>
    <div class="asset-list">
        @forelse($trade->take(4) as $t)
        <a href="{{ route('trades.history') }}" class="asset-item glass-card btn-active">
            <div class="asset-left">
                <div class="asset-icon" style="background: {{ $t->type == 'call' || $t->type == 'buy' ? 'rgba(0,230,118,0.1)' : 'rgba(255,61,0,0.1)' }}; border-color: transparent;">
                    <i class="{{ $t->type == 'call' || $t->type == 'buy' ? 'ri-line-chart-line text-success' : 'ri-line-chart-down-line text-danger' }}"></i>
                </div>
                <div>
                    <div class="asset-name">{{ $t->symbol }}</div>
                    <div class="asset-symbol text-uppercase">{{ $t->type }}</div>
                </div>
            </div>
            <div class="asset-right">
                <div class="asset-balance {{ $t->status == 'win' ? 'text-success' : 'text-danger' }}">
                    {{ $t->p_l < 0 ? '-' : '+' }}${{ number_format(abs($t->p_l), 2) }}
                </div>
                <div class="asset-usd" style="color: var(--t3);">{{ strtoupper($t->status) }}</div>
            </div>
        </a>
        @empty
        <div class="text-center py-4" style="color: var(--t3); font-size: 14px; font-weight: 500;">
            <i class="ri-ghost-line" style="font-size: 32px; display: block; margin-bottom: 8px; opacity: 0.5;"></i>
            No recent trades
        </div>
        @endforelse
    </div>
</div>

@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/odometer.js/0.4.8/odometer.min.js"></script>
    <script>
        document.addEventListener('error', function(e) {
            if (e.target.tagName && e.target.tagName.toLowerCase() === 'img') {
                if (!e.target.dataset.fallbackApplied) {
                    e.target.dataset.fallbackApplied = "true";
                    let name = e.target.alt ? e.target.alt.replace(/[^a-zA-Z0-9]/g, '').substring(0, 2) : 'XX';
                    if (name.trim() === '') name = 'XX';
                    e.target.src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(name) + '&background=2d3748&color=fff&rounded=true&bold=true&font-size=0.4';
                }
            }
        }, true);
    </script>
@endpush
@endsection
