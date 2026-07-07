@extends('layouts.user.app')
@section('title', 'Portfolio')
@section('content')
@php
    $logoMap = [];
    $mirrorMap = [];
    $assetList = isset($assets) ? $assets : (isset($asset) ? $asset : collect([]));
    foreach($assetList as $a) {
        $logoMap[$a->symbols] = \App\Services\AssetLogoService::getLogoUrl($a->symbols, isset($a->exchanges_id) ? ($a->exchanges_id == 1 ? 'forex' : ($a->exchanges_id == 3 ? 'stock' : 'crypto')) : 'crypto', $a->image ?? $a->image1 ?? $a->image2 ?? '');
        if(!empty($a->mirror_symbol)) { $mirrorMap[$a->symbols] = $a->mirror_symbol; }
    }
@endphp
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
<style>
    .mobile-glass-container { padding: 10px; padding-bottom: 80px; background: #0a0b0e; min-height: 100vh; font-family: 'Outfit', sans-serif; color: white; }
    .glass-card { background: rgba(255,255,255,0.02); backdrop-filter: blur(10px); border: 1px solid rgba(255,215,0,0.15); border-radius: 16px; padding: 15px; margin-bottom: 15px; }
    .gold-text { color: #FFD700; }
    
    .stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px; }
    .stat-box { background: rgba(0,0,0,0.3); border: 1px solid rgba(255,215,0,0.1); border-radius: 12px; padding: 12px; }
    .stat-label { font-size: 10px; font-weight: 800; color: rgba(255,255,255,0.5); text-transform: uppercase; margin-bottom: 4px; }
    .stat-value { font-size: 16px; font-weight: 800; }
    
    .chart-container { height: 350px; background: #000; border-radius: 16px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05); margin-bottom: 15px; }
    
    .market-selector { display: flex; align-items: center; justify-content: space-between; background: rgba(0,0,0,0.4); padding: 10px 15px; border-radius: 12px; margin-bottom: 15px; border: 1px solid rgba(255,215,0,0.2); }
    .btn-market { background: linear-gradient(135deg, #FFD700, #990000); color: #000; border: none; padding: 6px 12px; border-radius: 8px; font-size: 11px; font-weight: 800; }
    
    .premium-input { background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.1); color: #fff; border-radius: 10px; padding: 10px; width: 100%; font-size: 14px; margin-bottom: 10px; }
    .premium-input:focus { border-color: #FFD700; outline: none; }
    .amount-input { font-size: 20px; font-weight: 800; text-align: center; height: 50px; color: #FFD700; }
    
    .btn-call { background: linear-gradient(135deg, #ff3333, #059669); border: none; color: white; border-radius: 12px; padding: 15px; font-weight: 800; font-size: 16px; width: 100%; margin-bottom: 10px; }
    .btn-put { background: linear-gradient(135deg, #ef4444, #dc2626); border: none; color: white; border-radius: 12px; padding: 15px; font-weight: 800; font-size: 16px; width: 100%; }
    
    .trade-row { background: rgba(255,255,255,0.03); border-radius: 10px; padding: 10px; margin-bottom: 8px; display: flex; flex-direction: column; gap: 5px; border-left: 2px solid #FFD700; }
    .trade-header { display: flex; justify-content: space-between; align-items: center; }
    
    #asset_list .modal-content { background: #111318; border: 1px solid #FFD700; border-radius: 20px; color: white; }
    .asset-card { background: rgba(255,255,255,0.05); padding: 10px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; }
</style>

<div class="mobile-glass-container">
    <!-- Top Stats -->
    <div class="stats-grid">
        <div class="stat-box" style="grid-column: span 2;">
            <div class="stat-label">Account Equity</div>
            <div class="stat-value gold-text" style="font-size: 24px;">${{ number_format($equity, 2) }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Total P/L</div>
            <div class="stat-value {{ $sumPL >= 0 ? 'text-success' : 'text-danger' }}">{{ $sumPL < 0 ? '-' : '' }}${{ number_format(abs($sumPL), 2) }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Today's Profit</div>
            <div class="stat-value {{ $today >= 0 ? 'text-info' : 'text-danger' }}">{{ $today < 0 ? '-' : '' }}${{ number_format(abs($today), 2) }}</div>
        </div>
    </div>

    <!-- Market Selector -->
    <div class="market-selector">
        <div class="d-flex align-items-center gap-2">
            <div id="active-asset-logo" style="width:30px; height:30px;"></div>
            <div>
                <div id="show_market" style="font-weight: 800; font-size: 16px;">{{ $symbol }}</div>
                <div id="active-price-display" class="gold-text" style="font-size: 12px; font-weight: 700;">
                    ${{ number_format(isset($asset) ? ($asset->where('symbols', $symbol)->first()->buy ?? 0) : 0, 2) }}
                </div>
            </div>
        </div>
        <button class="btn-market" data-toggle="modal" data-target="#asset_list"><i class="ri-search-line"></i> MARKETS</button>
    </div>

    <!-- Chart -->
    <div class="chart-container">
        <div id="chart-mount" style="height: 100%;"></div>
    </div>

    <!-- Execution Panel -->
    <div class="glass-card">
        <div class="stat-label mb-3 text-center" style="font-size: 12px; color: #FFD700;">Execution Desk</div>
        <form id="post_trade">
            @csrf
            <div class="row">
                <div class="col-6 pr-1">
                    <label class="stat-label">Duration</label>
                    <select class="premium-input" name="expiretime" id="expiretime">
                        <option value="1">1 Min</option>
                        <option value="5">5 Min</option>
                        <option value="15">15 Min</option>
                        <option value="30">30 Min</option>
                        <option value="60">1 Hour</option>
                    </select>
                </div>
                <div class="col-6 pl-1">
                    <label class="stat-label">Leverage</label>
                    <select class="premium-input" name="leverage" id="leverage" {{ auth()->user()->hasFeature('high_leverage') ? '' : 'disabled' }}>
                        <option value="1:1">1:1</option>
                        @if(auth()->user()->hasFeature('high_leverage'))
                        <option value="1:10">1:10</option>
                        <option value="1:50">1:50</option>
                        <option value="1:100">1:100</option>
                        @endif
                    </select>
                </div>
            </div>
            
            <label class="stat-label mt-2">Investment Amount</label>
            <input type="number" class="premium-input amount-input" id="trade-amount" name="amount" value="100">
            
            <div class="text-center mb-3">
                <span class="stat-label">Expected Payout (<span id="payout-perc">90</span>%)</span>
                <div id="expected-payout" style="font-size: 18px; font-weight: 800; color: #ff3333;">$190.00</div>
            </div>

            <input type="hidden" id="active-asset-symbol" value="{{ $symbol }}">
            <input type="hidden" id="initial-ex-id" value="{{ isset($asset) ? ($asset->where('symbols', $default_asset ?? 'BTCUSD')->first()->exchanges_id ?? 2) : 2 }}">
            <input type="hidden" id="percentage_order" value="90">
            <input type="hidden" id="rate" value="0">

            <button type="button" class="btn-call" id="btn-buy"><i class="ri-arrow-up-line"></i> CALL</button>
            <button type="button" class="btn-put" id="btn-sell"><i class="ri-arrow-down-line"></i> PUT</button>
        </form>
    </div>

    <!-- Active Trades -->
    <div class="glass-card mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="stat-label" style="font-size: 12px; color: #FFD700; margin: 0;">Active Positions</div>
            <span class="badge" style="background: rgba(255,215,0,0.2); color: #FFD700;" id="trade_count">0</span>
        </div>
        <div id="active-trades-list">
            <div class="text-center py-3 text-secondary" style="font-size: 12px;">Awaiting execution...</div>
        </div>
        <a href="{{ route('trades.history') }}" class="btn-market text-center d-block mt-3" style="text-decoration:none; padding:10px;">Archive</a>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="asset_list" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="height: 80vh;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" style="font-weight: 800; color: #FFD700;">Select Market</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body overflow-auto">
                <input type="text" id="market-search" class="premium-input mb-3" placeholder="Search markets...">
                <div class="d-flex gap-2 overflow-auto pb-2 mb-3" style="white-space: nowrap;">
                    @if(isset($cat))
                    @foreach($cat as $c)
                    <button class="btn btn-sm" onclick="filterCategory('{{ $c->id }}')" style="background: rgba(255,255,255,0.1); color: #fff; border-radius: 8px;">{{ $c->name }}</button>
                    @endforeach
                    @endif
                </div>
                <div id="market-grid">
                    @foreach($assetList as $a)
                    <div class="asset-card" onclick="selectAsset('{{ $a->symbols }}', '{{ $a->percentage }}', '{{ $a->buy }}', '{{ $a->exchanges_id ?? 2 }}')">
                        <div class="d-flex align-items-center gap-2">
                            <x-asset-logo :symbol="$a->symbols" size="sm" />
                            <div>
                                <div style="font-weight: 800; font-size: 14px;">{{ $a->symbols }}</div>
                                <div style="font-size: 11px; color: #ff3333;">+{{ $a->percentage }}%</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div id="{{ $a->symbols }}-price" style="font-weight: 700; font-size: 14px;">${{ number_format($a->buy, 2) }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
    </div>
</div>

@include('mobile.components.bottom-nav')

@endsection

@push('js')
<script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
<script>
    let logoMap = @json($logoMap ?? []);
    let mirrorMap = @json($mirrorMap ?? (object)[]);
    let currentAsset = "{{ $default_asset ?? 'BTCUSD' }}";

    function getLogoUrl(symbol, exchanges_id) {
        if(logoMap[symbol]) return logoMap[symbol];
        return `${window.APP_URL}/api/stock-logo/${symbol}?v=1.1.1`;
    }

    function initChart(symbol, exchanges_id) {
        if (typeof TradingView === 'undefined') return;
        let lookupSymbol = mirrorMap[symbol] || symbol;
        let tvSymbol = lookupSymbol.replace('/', '');
        
        if (exchanges_id == 1) tvSymbol = "FX:" + tvSymbol;
        else if (exchanges_id == 2 || symbol.includes('USDT') || symbol.includes('BTC') || symbol.includes('ETH')) {
            tvSymbol = "BINANCE:" + tvSymbol.replace(/USD$/, 'USDT');
        }

        if (window.tvWidget) {
            try { window.tvWidget.remove(); } catch (e) {}
            window.tvWidget = null;
        }

        document.getElementById('chart-mount').innerHTML = '<div id="tradingview_widget" style="width: 100%; height: 100%;"></div>';

        window.tvWidget = new TradingView.widget({
            "autosize": true,
            "symbol": tvSymbol,
            "interval": "1",
            "timezone": "Etc/UTC",
            "theme": "dark",
            "style": "1",
            "locale": "en",
            "toolbar_bg": "#000",
            "enable_publishing": false,
            "allow_symbol_change": false,
            "container_id": "tradingview_widget",
            "backgroundColor": "#000",
            "gridColor": "rgba(255, 255, 255, 0.05)",
            "save_image": false,
            "hide_top_toolbar": true,
            "hide_legend": true
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

        let logoUrl = getLogoUrl(symbol, exchanges_id);
        $('#active-asset-logo').html(`<img src="${logoUrl}" onerror="this.src='${window.APP_URL}/api/stock-logo/${symbol}'" style="width: 100%; height: 100%; border-radius: 8px; object-fit: contain;">`);
        
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

    async function executeResult() {
        try { await fetch("{{ route('execute_result_after_time') }}"); } catch (e) {}
    }

    function fetchTrades() {
        fetch("{{ route('trade.js') }}")
            .then(res => res.json())
            .then(data => {
                $('#trade_count').text(data.count_trade);
                let html = '';
                if (data.data.length === 0) {
                    html = '<div class="text-center py-3 text-secondary" style="font-size:12px;">No active trades.</div>';
                } else {
                    data.data.forEach(trade => {
                        let color = trade.status === 'win' ? '#ff3333' : (trade.status === 'loss' ? '#ef4444' : '#FFD700');
                        html += `
                        <div class="trade-row">
                            <div class="trade-header">
                                <strong style="font-size:14px;">${trade.symbol}</strong>
                                <span style="font-size:10px; padding:2px 6px; border-radius:4px; background:rgba(255,255,255,0.1); color:${trade.type==='call'?'#ff3333':'#ef4444'}">${trade.type.toUpperCase()}</span>
                            </div>
                            <div style="display:flex; justify-content:space-between; font-size:12px;">
                                <span>$${parseFloat(trade.amount).toLocaleString()}</span>
                                <span style="color:${color}; font-weight:800;">${trade.status.toUpperCase()}</span>
                            </div>
                            <div style="display:flex; justify-content:space-between; font-size:10px; color:rgba(255,255,255,0.5);">
                                <span>Rate: ${trade.strike_rate ? parseFloat(trade.strike_rate).toFixed(5) : 'N/A'}</span>
                                <span>Exp: ${trade.expire_time}</span>
                            </div>
                            ${trade.status === 'pending' && trade.user_exit === 'on' ? 
                                `<button onclick="exitTrade(${trade.id})" style="background:rgba(239,68,68,0.2); color:#ef4444; border:none; padding:5px; border-radius:5px; font-size:10px; font-weight:800; margin-top:5px;">CLOSE</button>` : ''}
                        </div>`;
                        if(trade.modal === 'open') {
                            toastr[trade.status === 'win' ? 'success' : 'error'](`Trade Finished: ${trade.status.toUpperCase()}`);
                            fetch("{{ route('dashboard.trade.close-modal', ['id' => 'TMP_ID']) }}".replace('TMP_ID', trade.id));
                        }
                    });
                }
                $('#active-trades-list').html(html);
            })
            .catch(err => {
                if (err.message && err.message.includes('JSON')) window.location.reload();
            });
    }

    function exitTrade(id) {
        fetch("{{ route('dashboard.trade.exit', ['id' => 'EXIT_ID']) }}".replace('EXIT_ID', id))
        .then(res => res.json())
        .then(data => { if (data.status) toastr.success(data.status); else toastr.error(data.error); fetchTrades(); });
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
        if (!formData.rate || parseFloat(formData.rate) <= 0) return toastr.error("Waiting for rate...");

        $('#btn-buy, #btn-sell').attr('disabled', true);
        fetch("{{ route('trade') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        })
        .then(res => res.json())
        .then(data => {
            if (data.status) { toastr.success(data.status); fetchTrades(); } 
            else toastr.error(data.error);
        })
        .finally(() => $('#btn-buy, #btn-sell').attr('disabled', false));
    }

    async function pollPrices() {
        if (!currentAsset) return;
        let exId = $('#initial-ex-id').val();
        let lookupSymbol = mirrorMap[currentAsset] || currentAsset;
        let binanceSymbol = lookupSymbol.replace('/', '').toUpperCase();
        if (binanceSymbol.includes('USD') && !binanceSymbol.includes('USDT')) binanceSymbol = binanceSymbol.replace('USD', 'USDT');

        try {
            if (exId == 2 || binanceSymbol.includes('USDT')) {
                const bResponse = await fetch(`https://api.binance.com/api/v3/ticker/price?symbol=${binanceSymbol}`);
                if (bResponse.ok) {
                    const bData = await bResponse.json();
                    if (bData.price) {
                        let price = parseFloat(bData.price);
                        $('#rate').val(price);
                        $('#active-price-display').text('$' + price.toFixed(2));
                        return;
                    }
                }
            }
            const assetResponse = await fetch(`${window.APP_URL}/dashboard/asset-price/${currentAsset.replace('/', '-')}?exchange_id=${exId}`);
            const assetData = await assetResponse.json();
            if (assetData.price) {
                let price = parseFloat(assetData.price);
                $('#rate').val(price);
                $('#active-price-display').text('$' + price.toFixed(2));
                $(`#${currentAsset}-price`).text('$' + price.toFixed(2));
            }
        } catch (e) {}
    }

    $(document).ready(function() {
        let initialExId = $('#initial-ex-id').val();
        try { initChart(currentAsset, initialExId); } catch(e){}
        $('#active-asset-logo').html(`<img src="${getLogoUrl(currentAsset, initialExId)}" style="width:100%; height:100%; border-radius:8px;">`);

        setInterval(fetchTrades, 3000);
        setInterval(pollPrices, 2000);
        setInterval(executeResult, 4000);
        fetchTrades(); pollPrices();

        $('#trade-amount').on('input', updatePayout);
        $('#btn-buy').on('click', () => submitTrade('call'));
        $('#btn-sell').on('click', () => submitTrade('put'));

        $('#market-search').on('keyup', function() {
            let val = $(this).val().toLowerCase();
            $('#market-grid .asset-card').each(function() {
                let symbol = $(this).find('div:first').text().toLowerCase();
                $(this).toggle(symbol.includes(val));
            });
        });
    });

    async function filterCategory(id) {
        const res = await fetch("{{ route('dashboard.asset.show', ['id' => 'TMP_ID']) }}".replace('TMP_ID', id));
        const data = await res.json();
        let html = '';
        data.data.forEach(a => {
            if(a.mirror_symbol) mirrorMap[a.symbols] = a.mirror_symbol;
            let logoUrl = a.logo_url || getLogoUrl(a.symbols, a.exchanges_id);
            html += `
            <div class="asset-card" onclick="selectAsset('${a.symbols}', '${a.percentage}', '${a.buy}', '${a.exchanges_id}')">
                <div class="d-flex align-items-center gap-2">
                    <img src="${logoUrl}" style="width:30px; height:30px; border-radius:8px;">
                    <div>
                        <div style="font-weight: 800; font-size: 14px;">${a.symbols}</div>
                        <div style="font-size: 11px; color: #ff3333;">+${a.percentage}%</div>
                    </div>
                </div>
                <div class="text-right">
                    <div style="font-weight: 700; font-size: 14px;">$${parseFloat(a.buy).toFixed(2)}</div>
                </div>
            </div>`;
        });
        $('#market-grid').html(html);
    }
</script>
@endpush
