@extends('layouts.user.app')
@section('title', 'Spot Trading')

@section('content')
<style>
/* Sleek Mobile Spot Trading UI */
body { background: #0b0e11 !important; color: #EAECEF !important; font-family: 'Inter', sans-serif; }
.mobile-trading-wrapper { padding: 0; padding-bottom: 10px; display: flex; flex-direction: column; }
.trade-header { padding: 16px; background: #181a20; border-bottom: 1px solid #2b3139; display: flex; justify-content: space-between; align-items: center; }
.pair-title { font-size: 20px; font-weight: 700; color: #fff; display: flex; align-items: center; gap: 8px; }
.price-display { font-size: 24px; font-weight: 700; }
.change-display { font-size: 13px; font-weight: 600; padding: 4px 8px; border-radius: 4px; }
.text-green { color: #ff3333; }
.bg-green { background: rgba(255, 51, 51, 0.1); color: #ff3333; }
.text-red { color: #f6465d; }
.bg-red { background: rgba(246, 70, 93, 0.1); color: #f6465d; }

/* Tabs */
.trade-tabs { display: flex; background: #181a20; border-bottom: 1px solid #2b3139; }
.trade-tab { flex: 1; text-align: center; padding: 12px 0; font-size: 14px; font-weight: 600; color: #848e9c; cursor: pointer; border-bottom: 2px solid transparent; }
.trade-tab.active { color: #fcd535; border-bottom-color: #fcd535; }

/* Content Areas */
.chart-container { height: 300px; background: #181a20; border-bottom: 1px solid #2b3139; }
.order-book-list { flex: 1; padding: 16px; font-size: 13px; color: #848e9c; }
.ob-row { display: flex; justify-content: space-between; margin-bottom: 8px; position: relative; }
.ob-row span { position: relative; z-index: 2; }
.ob-bar { position: absolute; right: 0; top: 0; height: 100%; z-index: 1; opacity: 0.1; }
.ob-bar.red { background: #f6465d; }
.ob-bar.green { background: #ff3333; }

.trade-panel { padding: 16px; }
.action-buttons { display: flex; gap: 8px; margin-bottom: 16px; }
.action-btn { flex: 1; padding: 10px; text-align: center; border-radius: 8px; font-weight: 600; font-size: 14px; color: #fff; background: #2b3139; cursor: pointer; }
.action-btn.buy.active { background: #ff3333; }
.action-btn.sell.active { background: #f6465d; }

.input-box { background: #2b3139; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; padding: 12px; margin-bottom: 12px; }
.input-box input { background: transparent; border: none; color: #fff; font-size: 16px; width: 100%; outline: none; }
.input-label { color: #848e9c; font-size: 12px; white-space: nowrap; margin-left: 8px; }

.slider-container { display: flex; justify-content: space-between; margin-bottom: 20px; position: relative; }
.slider-step { width: 24%; height: 8px; background: #2b3139; border-radius: 4px; cursor: pointer; }
.slider-step.active { background: #ff3333; }

.submit-btn { width: 100%; padding: 14px; border-radius: 8px; border: none; font-size: 16px; font-weight: 700; color: #fff; background: #ff3333; transition: 0.2s; cursor: pointer; }
.submit-btn.sell { background: #f6465d; }

.orders-list { padding: 16px; }
.order-card { background: #181a20; border-radius: 12px; padding: 16px; margin-bottom: 12px; border: 1px solid #2b3139; }
.order-header { display: flex; justify-content: space-between; margin-bottom: 8px; font-weight: 600; }
.order-details { display: flex; justify-content: space-between; font-size: 12px; color: #848e9c; }
</style>

<div class="mobile-trading-wrapper">
    <!-- Header -->
    <div class="trade-header">
        <div>
            <div class="pair-title" id="current-pair">BTC/USDT <i class="ri-arrow-down-s-line" style="font-size: 16px; color:#848e9c;"></i></div>
            <div style="font-size: 12px; color: #848e9c;">Volume 24h: 34,210M</div>
        </div>
        <div style="text-align: right;">
            <div class="price-display text-green" id="current-price">0.00</div>
            <div class="change-display bg-green" id="current-change">+0.00%</div>
        </div>
    </div>

    <!-- Permanent Chart -->
    <div class="chart-container" style="border-top: 1px solid #2b3139;">
        <div id="tradingview_chart" style="height:100%;width:100%"></div>
    </div>

    <!-- Tabs -->
    <div class="trade-tabs">
        <div class="trade-tab active" onclick="switchTab('trade')">Trade</div>
        <div class="trade-tab" onclick="switchTab('orders')">Orders</div>
    </div>

    <!-- Tab Contents -->

    <div id="tab-trade" style="flex-grow: 1; display: flex; flex-direction: column;">
        <!-- Trade Panel Right side -->
        <div class="trade-panel" style="width: 100%; flex-grow: 1; display: flex; flex-direction: column;">
            <div class="text-center mb-3">
                <div class="price-display text-green" style="font-size:32px;" id="ob-current-price">0.00</div>
            </div>

            <div class="action-buttons">
                <div class="action-btn buy active" onclick="setSide('buy')">Buy</div>
                <div class="action-btn sell" onclick="setSide('sell')">Sell</div>
            </div>

            <div class="input-box" style="padding: 8px 12px;">
                <select class="form-control" id="order-type" style="background:transparent; border:none; color:#fff; padding:0; outline:none; font-weight:600;">
                    <option value="market">Market Order</option>
                    <option value="limit">Limit Order</option>
                </select>
            </div>

            <div class="input-box">
                <input type="number" id="trade-price" placeholder="Price">
                <span class="input-label">USDT</span>
            </div>

            <div class="input-box">
                <input type="number" id="trade-amount" placeholder="Amount">
                <span class="input-label" id="base-coin">BTC</span>
            </div>

            <div class="slider-container" id="pct-slider">
                <div class="slider-step" data-pct="25"></div>
                <div class="slider-step" data-pct="50"></div>
                <div class="slider-step" data-pct="75"></div>
                <div class="slider-step" data-pct="100"></div>
            </div>

            <div style="font-size: 12px; color: #848e9c; margin-bottom: 4px; display:flex; justify-content:space-between;">
                <span>Avail:</span>
                <span id="avail-bal" style="color: #fff; font-weight: 600;">{{ number_format($usdBalance ? $usdBalance->amount : 0, 2) }} USDT</span>
            </div>

            <button class="submit-btn" id="submit-trade" style="margin-top: 0px; margin-bottom: 0px;">Buy BTC</button>
        </div>
    </div>

    <div id="tab-orders" style="display: none; flex-grow: 1; flex-direction: column;">
        <div class="orders-list">
            @forelse($orders as $o)
            <div class="order-card">
                <div class="order-header">
                    <span class="{{ $o->type == 'buy' ? 'text-green' : 'text-red' }}" style="text-transform: uppercase;">{{ $o->type }} {{ str_replace('USDT','',$o->symbol) }}</span>
                    <span style="font-size: 12px; padding: 2px 6px; border-radius: 4px; background: rgba(255,255,255,0.1);">{{ ucfirst($o->status) }}</span>
                </div>
                <div class="order-details">
                    <div>
                        <div style="color: #fff; font-size: 14px; margin-bottom: 4px;">{{ $o->amount }} <span style="font-size: 11px; color: #848e9c;">{{ str_replace('USDT','',$o->symbol) }}</span></div>
                        <div>Price: ${{ number_format($o->price, 2) }}</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="color: #fff; font-size: 14px; margin-bottom: 4px;">${{ number_format($o->total_usd, 2) }} <span style="font-size: 11px; color: #848e9c;">Total</span></div>
                        <div>{{ $o->created_at->format('M d, H:i') }}</div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center" style="color: #848e9c; padding: 40px 0;">
                <i class="ri-file-list-3-line" style="font-size: 48px; opacity: 0.5;"></i>
                <p class="mt-2">No open orders</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Assets Modal -->
<div class="modal fade" id="assetsModal" tabindex="-1" style="z-index: 10500;">
    <div class="modal-dialog modal-dialog-centered" style="margin: 0; align-items: flex-end; min-height: 100%;">
        <div class="modal-content" style="background: #181a20; border-radius: 20px 20px 0 0; border: none; height: 80vh;">
            <div class="modal-header" style="border-bottom: 1px solid #2b3139;">
                <h5 class="modal-title" style="color:#fff;">Select Asset</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-0" style="overflow-y: auto;">
                @foreach($assets as $asset)
                <div class="p-3 border-bottom asset-selector" data-id="{{ $asset->id }}" data-symbol="{{ $asset->symbol }}" data-price="{{ $asset->buy ?? 0 }}" data-change="{{ $asset->changes ?? 0 }}" style="border-color:#2b3139 !important; display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <div style="color:#fff; font-weight:600; font-size:16px;">{{ $asset->symbol }}</div>
                        <div style="color:#848e9c; font-size:12px;">{{ $asset->name }}</div>
                    </div>
                    <div style="text-align:right;">
                        <div style="color:#fff; font-weight:600;">${{ number_format($asset->buy ?? 0, 2) }}</div>
                        <div class="{{ ($asset->changes ?? 0) >= 0 ? 'text-green' : 'text-red' }}" style="font-size:12px;">{{ ($asset->changes ?? 0) >= 0 ? '+' : '' }}{{ number_format($asset->changes ?? 0, 2) }}%</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('js')
<script src="https://s3.tradingview.com/tv.js"></script>
<script>
let currentSide = 'buy';
let currentAsset = {
    id: "{{ $assets->first()->id ?? 'null' }}",
    symbol: "{{ $assets->first()->symbol ?? '' }}",
    buy: parseFloat("{{ $assets->first()->buy ?? 0 }}") || 0,
    changes: parseFloat("{{ $assets->first()->changes ?? 0 }}") || 0
};
let usdBalance = parseFloat("{{ $usdBalance->amount ?? 0 }}") || 0;
let holdings = {!! json_encode($holdings ?? []) !!};
let chartWidget = null;

$(document).ready(function() {
    if(currentAsset) {
        selectAsset(currentAsset.id, currentAsset.symbol, currentAsset.buy, currentAsset.changes || 0);
        setTimeout(function() {
            initChart(currentAsset.symbol);
        }, 300);
    }

    $('.pair-title').click(function() {
        $('#assetsModal').modal('show');
    });

    $('.asset-selector').click(function() {
        let id = $(this).data('id');
        let sym = $(this).data('symbol');
        let prc = parseFloat($(this).data('price'));
        let chg = parseFloat($(this).data('change'));
        selectAsset(id, sym, prc, chg);
        $('#assetsModal').modal('hide');
    });

    $('#order-type').change(function() {
        if($(this).val() == 'market') {
            $('#trade-price').val(currentAsset.buy).prop('readonly', true);
        } else {
            $('#trade-price').prop('readonly', false);
        }
    });

    $('#submit-trade').click(function() {
        let amt = parseFloat($('#trade-amount').val());
        let prc = parseFloat($('#trade-price').val());
        
        if(!amt || amt <= 0) return alert('Enter amount');

        $(this).prop('disabled', true).text('Processing...');

        fetch("{{ route('user.spot_trading.order') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                id: currentAsset.id,
                amount: amt,
                type: currentSide,
                margin_mode: 'spot',
                leverage: 1,
                order_type: $('#order-type').val(),
                limit_price: prc
            })
        }).then(res => res.json()).then(data => {
            if (data.error || data.message) {
                alert(data.error || data.message);
                $('#submit-trade').prop('disabled', false).text(currentSide === 'buy' ? 'Buy' : 'Sell');
            } else {
                alert(data.status || 'Order submitted');
                location.reload();
            }
        }).catch(err => {
            alert('Error submitting order');
            $('#submit-trade').prop('disabled', false).text(currentSide.toUpperCase() + ' ' + currentAsset.symbol.replace('USDT',''));
        });
    });

    // Slider percentages
    $('.slider-step').click(function() {
        $('.slider-step').removeClass('active');
        $(this).addClass('active');
        $(this).prevAll('.slider-step').addClass('active');

        let pct = parseInt($(this).data('pct')) / 100;
        let prc = parseFloat($('#trade-price').val()) || currentAsset.buy;
        if(currentSide === 'buy') {
            let maxAmt = (usdBalance / prc) * pct;
            $('#trade-amount').val(maxAmt.toFixed(4));
        } else {
            let base = currentAsset.symbol.replace('USDT','').replace('USD','');
            let holding = holdings[base] ? parseFloat(holdings[base].balance) : 0;
            $('#trade-amount').val((holding * pct).toFixed(4));
        }
    });
});

function switchTab(tab) {
    $('.trade-tab').removeClass('active');
    event.currentTarget.classList.add('active');
    $('#tab-trade, #tab-orders').hide();
    $('#tab-' + tab).css('display', 'flex');
}

function setSide(side) {
    currentSide = side;
    $('.action-btn').removeClass('active');
    $('.action-btn.' + side).addClass('active');
    
    let base = currentAsset.symbol.replace('USDT','').replace('USD','');
    let btn = $('#submit-trade');
    let avail = $('#avail-bal');

    if(side === 'buy') {
        btn.removeClass('sell').text('Buy ' + base);
        avail.text(usdBalance.toFixed(2) + ' USDT');
        $('.slider-step.active').css('background', '#ff3333');
    } else {
        btn.addClass('sell').text('Sell ' + base);
        let holding = holdings[base] ? parseFloat(holdings[base].balance) : 0;
        avail.text(holding.toFixed(4) + ' ' + base);
        $('.slider-step.active').css('background', '#f6465d');
    }
}

function selectAsset(id, symbol, price, change) {
    currentAsset = { id: id, symbol: symbol, buy: price };
    $('#current-pair').html(symbol + ' <i class="ri-arrow-down-s-line" style="font-size: 16px; color:#848e9c;"></i>');
    $('#current-price').text(price.toFixed(2));
    $('#ob-current-price').text(price.toFixed(2));
    $('#trade-price').val(price.toFixed(2));
    $('#base-coin').text(symbol.replace('USDT','').replace('USD',''));
    
    let chgEl = $('#current-change');
    let prcEl = $('#current-price');
    chgEl.text((change >= 0 ? '+' : '') + change.toFixed(2) + '%');
    
    if(change >= 0) {
        chgEl.removeClass('bg-red').addClass('bg-green text-green');
        prcEl.removeClass('text-red').addClass('text-green');
        $('#ob-current-price').removeClass('text-red').addClass('text-green');
    } else {
        chgEl.removeClass('bg-green text-green').addClass('bg-red');
        prcEl.removeClass('text-green').addClass('text-red');
        $('#ob-current-price').removeClass('text-green').addClass('text-red');
    }

    setSide(currentSide);
    renderOrderBook(price);
    
    if(chartWidget) {
        initChart(symbol);
    }
}

function renderOrderBook(centerPrice) {
    let asks = '';
    let bids = '';
    for(let i=0; i<8; i++) {
        let pAsks = centerPrice * (1 + (Math.random() * 0.005 + 0.001));
        let aAsks = Math.random() * 2 + 0.1;
        asks += `<div class="ob-row text-red"><span>${pAsks.toFixed(2)}</span><span style="color:#fff;">${aAsks.toFixed(3)}</span><div class="ob-bar red" style="width:${Math.random()*80}%"></div></div>`;
        
        let pBids = centerPrice * (1 - (Math.random() * 0.005 + 0.001));
        let aBids = Math.random() * 2 + 0.1;
        bids += `<div class="ob-row text-green"><span>${pBids.toFixed(2)}</span><span style="color:#fff;">${aBids.toFixed(3)}</span><div class="ob-bar green" style="width:${Math.random()*80}%"></div></div>`;
    }
    $('#ob-asks').html(asks);
    $('#ob-bids').html(bids);
}

function initChart(symbol) {
    if (typeof TradingView === 'undefined') {
        setTimeout(function() { initChart(symbol); }, 500);
        return;
    }
    document.getElementById('tradingview_chart').innerHTML = '';
    
    let cleanSymbol = symbol.replace(/[^A-Za-z0-9]/g, '').toUpperCase();
    let tvSymbol = "BINANCE:" + (cleanSymbol.endsWith("USDT") ? cleanSymbol : cleanSymbol + "USDT");

    chartWidget = new TradingView.widget({
        "width": "100%",
        "height": 300,
        "symbol": tvSymbol,
        "interval": "60",
        "timezone": "Etc/UTC",
        "theme": "dark",
        "style": "1",
        "locale": "en",
        "enable_publishing": false,
        "backgroundColor": "#181a20",
        "gridColor": "#2b3139",
        "hide_top_toolbar": true,
        "hide_legend": true,
        "save_image": false,
        "container_id": "tradingview_chart"
    });
}
</script>
@endpush
@endsection
