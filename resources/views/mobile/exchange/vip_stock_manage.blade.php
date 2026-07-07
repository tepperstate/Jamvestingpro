@extends('layouts.user.app')
@section('title', 'Manage VIP Stock')
@section('content')
<style>
    .mobile-glass-container { padding: 15px; padding-bottom: 80px; font-family: 'Outfit', sans-serif; background: #0a0b0e; color: #fff; min-height: 100vh; }
    .glass-card { background: rgba(255,255,255,0.03); backdrop-filter: blur(16px); border: 1px solid rgba(255,215,0,0.15); border-radius: 20px; padding: 20px; margin-bottom: 20px; }
    .gold-accent { color: #FFD700; }
    
    .stat-block { background: rgba(0,0,0,0.3); padding: 15px; border-radius: 12px; margin-bottom: 15px; border: 1px solid rgba(255,255,255,0.05); }
    .stat-label { font-size: 10px; text-transform: uppercase; color: rgba(255,255,255,0.5); font-weight: 800; margin-bottom: 5px; }
    .stat-val { font-size: 24px; font-weight: 800; color: #34d399; }
    
    .input-glass { background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 15px; border-radius: 12px; width: 100%; font-size: 16px; font-weight: 800; text-align: center; margin-bottom: 10px; }
    .btn-gold { background: linear-gradient(135deg, #FFD700, #990000); color: #000; border: none; padding: 15px; border-radius: 12px; font-weight: 800; width: 100%; text-transform: uppercase; }
    .btn-danger-glass { background: rgba(239, 68, 68, 0.2); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.4); padding: 15px; border-radius: 12px; font-weight: 800; width: 100%; text-transform: uppercase; }
</style>

<div class="mobile-glass-container">
    <div class="d-flex align-items-center mb-4">
        <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.1); border-radius: 12px; display: flex; justify-content: center; align-items: center; margin-right: 15px;">
            <x-asset-logo :symbol="$data->symbol" size="md" />
        </div>
        <div>
            <h2 style="font-weight: 800; font-size: 22px; margin: 0;">{{ $data->symbol }}</h2>
            <div style="font-size: 12px; color: rgba(255,255,255,0.5);">{{ $data->name }}</div>
        </div>
    </div>

    <!-- Chart -->
    <div class="glass-card" style="padding: 10px; height: 250px;">
        <div id="stock_chart" style="height: 100%; width: 100%;"></div>
    </div>

    <!-- Holdings & Buy -->
    <div class="glass-card">
        <div class="stat-block">
            <div class="stat-label">Market Value</div>
            <div class="stat-val">${{ number_format($price, 2) }}</div>
        </div>

        <div class="stat-block" style="border-color: rgba(255,215,0,0.2);">
            <div class="stat-label gold-accent">Your Holdings</div>
            <div style="font-size: 20px; font-weight: 800;">
                {{ isset($stock_query) ? number_format($stock_query->amount, 4) : '0.0000' }} Units
            </div>
            @if(isset($stock_query))
            <div style="font-size: 11px; color: rgba(255,255,255,0.5); margin-top: 5px;">
                Equity: <span style="color: #34d399;">${{ number_format($price * $stock_query->amount, 2) }}</span>
            </div>
            @endif
        </div>

        <form action="{{route('stocks.trade-post')}}" method="post" id="buyStockForm">
            @csrf
            <input type="hidden" name="name" value="{{$data->name}}">
            <input type="hidden" name="symbol" value="{{$data->symbol}}">
            <input type="hidden" name="price" value="{{$price}}">
            <input type="hidden" name="id" value="{{$data->id}}">
            
            <input type="number" name="amount" class="input-glass" placeholder="Units to Buy" required step="0.0001">
            <button type="submit" class="btn-gold">Buy Shares</button>
        </form>
    </div>

    <!-- Sell / Liquidate -->
    <div class="glass-card">
        <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 15px; color: #ef4444;">Liquidation</h3>
        @if(isset($stock_query) && $stock_query->amount > 0)
            <input type="number" id="sell_amount_vip" class="input-glass" value="{{$stock_query->amount}}" max="{{$stock_query->amount}}" step="0.0001" style="color: #ef4444;">
            <button id="btnSellVip" class="btn-danger-glass" data-id="{{$data->id}}" data-price="{{$price}}" data-max="{{$stock_query->amount}}">
                Liquidate Position
            </button>
        @else
            <p style="font-size: 12px; color: rgba(255,255,255,0.5); text-align: center; margin: 0;">No active holdings to liquidate.</p>
        @endif
    </div>
</div>

@push('js')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
$(document).ready(function() {
    const symbol = "{{ $data->symbol }}";
    const basePrice = {{ $price }};
    
    function renderChart(seriesData) {
        var options = {
            series: [{ name: symbol, data: seriesData }],
            chart: { type: 'area', height: '100%', width: '100%', background: 'transparent', toolbar: { show: false }, parentHeightOffset: 0 },
            theme: { mode: 'dark' }, colors: ['#34d399'],
            dataLabels: { enabled: false }, stroke: { curve: 'smooth', width: 2 },
            xaxis: { type: 'datetime', labels: {show: false}, axisBorder: { show: false }, axisTicks: { show: false } },
            yaxis: { show: false },
            grid: { show: false }
        };
        new ApexCharts(document.querySelector("#stock_chart"), options).render();
    }

    function generateMockData() {
        let mockData = []; let currentPrice = basePrice; let now = new Date().getTime(); let dayMs = 24 * 60 * 60 * 1000;
        for (let i = 30; i >= 0; i--) { mockData.push([now - (i * dayMs), parseFloat(currentPrice.toFixed(2))]); currentPrice *= (1 + (Math.random() * 0.04 - 0.02)); }
        return mockData.sort((a, b) => a[0] - b[0]);
    }
    
    fetch(`https://query1.finance.yahoo.com/v8/finance/chart/${symbol}?range=1mo&interval=1d`)
        .then(r => r.json())
        .then(data => {
            if (data.chart?.result?.[0]) {
                const res = data.chart.result[0]; const ts = res.timestamp; const cls = res.indicators.quote[0].close;
                let cData = []; for(let i=0; i<ts.length; i++) { if(cls[i]!==null) cData.push([ts[i]*1000, cls[i]]); }
                renderChart(cData);
            } else renderChart(generateMockData());
        }).catch(() => renderChart(generateMockData()));

    $('#buyStockForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this); const btn = form.find('button[type="submit"]');
        btn.prop('disabled', true).text('Processing...');
        $.post(form.attr('action'), form.serialize(), function(res) {
            toastr.success(res.status || "Order executed"); setTimeout(() => location.reload(), 1500);
        }).fail(function(xhr) {
            toastr.error(xhr.responseJSON?.message || "Error"); btn.prop('disabled', false).text('Buy Shares');
        });
    });

    $('#btnSellVip').on('click', function(e) {
        e.preventDefault(); const btn = $(this);
        const amount = parseFloat($('#sell_amount_vip').val());
        if (!amount || amount <= 0 || amount > parseFloat(btn.data('max'))) { toastr.error('Invalid amount'); return; }
        if (confirm('Finalize liquidation?')) {
            btn.prop('disabled', true).text('Processing...');
            $.post("{{ route('stocks.sell') }}", { _token: "{{ csrf_token() }}", id: btn.data('id'), amount: amount, price: btn.data('price') }, function(res) {
                toastr.success(res.status || "Liquidated"); setTimeout(() => location.reload(), 1500);
            }).fail(function(xhr) {
                toastr.error(xhr.responseJSON?.message || "Error"); btn.prop('disabled', false).text('Liquidate Position');
            });
        }
    });
});
</script>
@endpush
@endsection
