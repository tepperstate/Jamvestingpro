@extends('layouts.user.app')
@section('title', 'Manage VIP Stock')
@section('content')

<style>
.glass-card-premium { transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); border: 1px solid rgba(255,255,255,0.08); background: #000000 !important; backdrop-filter: blur(20px); border-radius: 24px; padding: 2rem; position: relative; overflow: hidden; }
.hero-stat { font-family: var(--font-outfit); font-size: 2.5rem; font-weight: 800; letter-spacing: -1px; }
.micro-label { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; color: var(--text-muted); opacity: 0.8; }
.btn-invest { height: 50px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; border-radius: 12px; }
</style>

<div class="container-full mt-4">
    <div class="content-header" style="padding:15px 13px 0px">
        <h3>Premium VIP Portfolio</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/user"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('vip_stocks.index') }}">VIP Stocks</a></li>
            <li class="breadcrumb-item active">{{ $data->symbol }}</li>
        </ol>
    </div>

    <section class="content" style="padding:15px 13px 0px">
        <div class="row g-4">
            <!-- Left Panel: Asset Details -->
            <div class="col-xl-4 col-12 mb-4">
                <div class="glass-card-premium h-100 d-flex flex-column">
                    <div style="position: absolute; top: 0; left: 0; right: 0; height: 100px; background: linear-gradient(to bottom, rgba(59, 130, 246, 0.1) 0%, transparent 100%); pointer-events: none;"></div>
                    
                    <div class="d-flex align-items-center mb-4">
                        <div class="mr-3" style="width: 50px; height: 50px; background: rgba(255,255,255,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <x-asset-logo :symbol="$data->symbol" size="md" />
                        </div>
                        <div>
                            <h3 class="outfit font-weight-bold mb-0 text-white">{{ $data->symbol }}</h3>
                            <div class="small text-secondary">{{ $data->name }}</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="micro-label mb-1">Current Base Value</div>
                        <div class="hero-stat" style="color: #34d399;">${{ number_format($price, 2) }}</div>
                    </div>

                    @if(isset($stock_query))
                        <div class="mb-4 pt-4 border-top" style="border-color: rgba(255,255,255,0.1) !important;">
                            <div class="micro-label mb-1">Your Holdings</div>
                            <div class="hero-stat text-white" style="font-size: 2rem;">{{ number_format($stock_query->amount, 4) }} <span style="font-size: 1rem; opacity: 0.5;">Units</span></div>
                            <div class="mt-2 text-secondary small">Total Equity Value: <span style="color: #34d399; font-weight: bold;">${{ number_format($price * $stock_query->amount, 2) }}</span></div>
                        </div>
                    @else
                        <div class="mb-4 pt-4 border-top" style="border-color: rgba(255,255,255,0.1) !important;">
                            <div class="micro-label mb-1">Your Holdings</div>
                            <div class="hero-stat text-white" style="font-size: 2rem; opacity: 0.5;">0.0000</div>
                        </div>
                    @endif

                    <div class="mt-auto">
                        <form action="{{route('stocks.trade-post')}}" method="post" id="buyStockForm">
                            @csrf
                            <input type="hidden" name="name" value="{{$data->name}}">
                            <input type="hidden" name="symbol" value="{{$data->symbol}}">
                            <input type="hidden" name="price" value="{{$price}}">
                            <input type="hidden" name="id" value="{{$data->id}}">

                            <div class="form-group mb-3">
                                <label class="micro-label">Acquisition Capital (Units)</label>
                                <input type="number" name="amount" class="form-control" style="background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); color: white; height: 50px;" placeholder="Enter units to buy..." required step="0.0001">
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 btn-invest">
                                Buy {{ $data->symbol }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Chart and Liquidation -->
            <div class="col-xl-8 col-12 mb-4">
                <!-- ApexCharts Widget BEGIN -->
                <div class="glass-card-premium mb-4" style="padding: 0; overflow: hidden; height: 450px;">
                    <div id="stock_chart" style="height: 100%; width: 100%; padding: 15px;"></div>
                    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const symbol = "{{ $data->symbol }}";
                            const basePrice = {{ $price }};
                            
                            function renderChart(seriesData) {
                                var options = {
                                    series: [{
                                        name: symbol,
                                        data: seriesData
                                    }],
                                    chart: {
                                        type: 'area',
                                        height: '100%',
                                        width: '100%',
                                        background: 'transparent',
                                        toolbar: { show: false }
                                    },
                                    theme: { mode: 'dark' },
                                    colors: ['#34d399'],
                                    fill: {
                                        type: 'gradient',
                                        gradient: {
                                            shadeIntensity: 1,
                                            opacityFrom: 0.4,
                                            opacityTo: 0.05,
                                            stops: [0, 100]
                                        }
                                    },
                                    dataLabels: { enabled: false },
                                    stroke: { curve: 'smooth', width: 2 },
                                    xaxis: {
                                        type: 'datetime',
                                        axisBorder: { show: false },
                                        axisTicks: { show: false }
                                    },
                                    yaxis: {
                                        labels: {
                                            formatter: function (value) { return "$" + value.toFixed(2); }
                                        }
                                    },
                                    grid: {
                                        borderColor: 'rgba(255, 255, 255, 0.05)',
                                        strokeDashArray: 4,
                                    }
                                };
                                var chart = new ApexCharts(document.querySelector("#stock_chart"), options);
                                chart.render();
                            }

                            function generateMockData() {
                                let mockData = [];
                                let currentPrice = basePrice;
                                let now = new Date().getTime();
                                let dayMs = 24 * 60 * 60 * 1000;
                                for (let i = 30; i >= 0; i--) {
                                    mockData.push([now - (i * dayMs), parseFloat(currentPrice.toFixed(2))]);
                                    currentPrice = currentPrice * (1 + (Math.random() * 0.04 - 0.02));
                                }
                                return mockData.sort((a, b) => a[0] - b[0]);
                            }

                            fetch(`https://query1.finance.yahoo.com/v8/finance/chart/${symbol}?range=1mo&interval=1d`)
                                .then(response => {
                                    if (!response.ok) throw new Error('Network response was not ok');
                                    return response.json();
                                })
                                .then(data => {
                                    if (data.chart && data.chart.result && data.chart.result[0]) {
                                        const result = data.chart.result[0];
                                        const timestamps = result.timestamp;
                                        const closes = result.indicators.quote[0].close;
                                        let chartData = [];
                                        for(let i=0; i<timestamps.length; i++) {
                                            if (closes[i] !== null) {
                                                chartData.push([timestamps[i] * 1000, closes[i]]);
                                            }
                                        }
                                        renderChart(chartData);
                                    } else {
                                        renderChart(generateMockData());
                                    }
                                })
                                .catch(error => {
                                    console.error('Error fetching chart data:', error);
                                    renderChart(generateMockData());
                                });
                        });
                    </script>
                </div>
                <!-- ApexCharts Widget END -->

                <!-- Active Holdings / Liquidation -->
                <div class="glass-card-premium">
                    <h4 class="outfit font-weight-bold mb-4 text-white">Liquidation Operations</h4>
                    @if(isset($stock_query) && $stock_query->amount > 0)
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <p class="text-secondary mb-2">You currently hold <strong>{{ number_format($stock_query->amount, 4) }}</strong> units of {{ $data->symbol }}. Enter the amount you wish to sell.</p>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="number" class="form-control" id="sell_amount_vip" value="{{$stock_query->amount}}" max="{{$stock_query->amount}}" step="0.0001" style="background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); color: white; height: 50px;">
                                    <div class="input-group-append">
                                        <button class="btn btn-danger font-weight-bold" id="btnSellVip" style="text-transform: uppercase; letter-spacing: 1px;" data-id="{{$data->id}}" data-price="{{$price}}" data-max="{{$stock_query->amount}}">
                                            Liquidate
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="p-3 text-center" style="background: rgba(255,255,255,0.02); border-radius: 12px; border: 1px dashed rgba(255,255,255,0.1);">
                            <p class="text-secondary mb-0">You do not have any active holdings in this VIP security.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>

@push('js')
<script>
$(document).ready(function() {
    $('#buyStockForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                toastr.success(response.status || "Order executed successfully");
                setTimeout(function() { location.reload(); }, 1500);
            },
            error: function(xhr) {
                const error = xhr.responseJSON ? xhr.responseJSON.message : "An error occurred";
                toastr.error(error);
                submitBtn.prop('disabled', false).html('Buy {{ $data->symbol }}');
            }
        });
    });

    $('#btnSellVip').on('click', function(e) {
        e.preventDefault();
        const btn = $(this);
        const sid = btn.data('id');
        const price = btn.data('price');
        const maxAmount = parseFloat(btn.data('max'));
        const amount = parseFloat($('#sell_amount_vip').val());

        if (!amount || amount <= 0 || amount > maxAmount) {
            toastr.error('Invalid liquidation amount.');
            return;
        }

        if (confirm('Finalize liquidation of ' + amount + ' units?')) {
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
            
            $.ajax({
                url: "{{ route('stocks.sell') }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: sid,
                    amount: amount,
                    price: price
                },
                success: function(response) {
                    toastr.success(response.status || "Units liquidated successfully");
                    setTimeout(function() { location.reload(); }, 1500);
                },
                error: function(xhr) {
                    const error = xhr.responseJSON ? xhr.responseJSON.message : "An error occurred";
                    toastr.error(error);
                    btn.prop('disabled', false).html('Liquidate');
                }
            });
        }
    });
});
</script>
@endpush
@endsection
