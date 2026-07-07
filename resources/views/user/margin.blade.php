@extends('layouts.user.app')
@section('content')
<style>
    .glass-panel {
        background: rgba(19, 23, 34, 0.6);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,0.05);
        border-radius: 12px;
    }
    .asset-item {
        cursor: pointer;
        transition: all 0.2s ease;
        padding: 12px 16px;
        border-bottom: 1px solid rgba(255,255,255,0.03);
    }
    .asset-item:hover, .asset-item.active {
        background: rgba(255, 51, 51, 0.1);
        border-left: 3px solid #ff3333;
    }
    .custom-input {
        background: rgba(0,0,0,0.3) !important;
        border: 1px solid rgba(255,255,255,0.1) !important;
        color: #fff !important;
    }
    .custom-input:focus {
        border-color: #ff3333 !important;
        box-shadow: 0 0 0 0.25rem rgba(255, 51, 51, 0.25) !important;
    }
    .table-dark-glass {
        background: transparent;
        color: #fff;
    }
    .table-dark-glass th {
        background: rgba(255,255,255,0.05);
        border-bottom: 1px solid rgba(255,255,255,0.1);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
    }
    .table-dark-glass td {
        border-bottom: 1px solid rgba(255,255,255,0.05);
        vertical-align: middle;
    }
</style>

<div class="container-fluid mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0" style="color: #fff; text-shadow: 0 2px 4px rgba(0,0,0,0.5);">Margin Trading</h2>
        <div>
            <span class="badge bg-primary text-uppercase px-3 py-2" style="letter-spacing: 1px; font-weight: 600;">Max Leverage: <span id="max-leverage-display">{{ $pairs->first()->max_leverage ?? 5 }}</span>x</span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Asset Selector -->
        <div class="col-lg-3">
            <div class="glass-panel h-100 d-flex flex-column" style="max-height: 500px;">
                <div class="p-3 border-bottom border-secondary border-opacity-25">
                    <h6 class="mb-0 text-uppercase fw-bold text-white-50" style="letter-spacing: 1px;">Trading Pairs</h6>
                </div>
                <div class="overflow-auto flex-grow-1" style="scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.2) transparent;">
                    @foreach($pairs as $index => $pair)
                        <div class="asset-item {{ $index === 0 ? 'active' : '' }}" 
                             onclick="selectAsset('{{ $pair->id }}', '{{ $pair->symbol }}', '{{ $pair->max_leverage }}', this)">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ \App\Services\AssetLogoService::getLogoUrl($pair->symbol, 'crypto', '') }}" 
                                         onerror="this.onerror=null; this.src='/assets/img/profit.svg';" 
                                         style="width: 28px; height: 28px; border-radius: 50%; background: rgba(255,255,255,0.05); padding: 2px; object-fit: contain;">
                                    <div>
                                        <h6 class="mb-0 fw-bold text-white">{{ $pair->symbol }}</h6>
                                        <small class="text-white-50">{{ $pair->max_leverage }}x Max</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="d-block text-white" style="font-family: monospace;">${{ number_format($pair->mark_price, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="col-lg-6">
            <div class="glass-panel p-2 h-100">
                <div id="tradingview_chart" style="height: 100%; min-height: 480px; border-radius: 8px; overflow: hidden;"></div>
            </div>
        </div>

        <!-- Order Form -->
        <div class="col-lg-3">
            <div class="glass-panel h-100 p-4">
                <h5 class="text-white mb-4 fw-bold pb-2 border-bottom border-secondary border-opacity-25">Place Order</h5>
                <form action="{{ route('user.margin.trade') }}" method="POST" id="marginForm">
                    @csrf
                    <input type="hidden" name="pair_id" id="selected_pair_id" value="{{ $pairs->first()->id ?? '' }}">
                    
                    <div class="mb-4">
                        <label class="text-white-50 small text-uppercase fw-bold mb-2" style="letter-spacing: 1px;">Selected Asset</label>
                        <h4 id="selected_asset_name" class="text-white mb-0 fw-bold">{{ $pairs->first()->symbol ?? 'N/A' }}</h4>
                    </div>

                    <div class="form-group mb-4">
                        <label class="text-white-50 small text-uppercase fw-bold mb-2" style="letter-spacing: 1px;">Collateral (USD)</label>
                        <div class="input-group">
                            <span class="input-group-text border-0" style="background: rgba(0,0,0,0.5); color: #fff;">$</span>
                            <input type="number" name="amount" class="form-control custom-input" placeholder="0.00" step="0.01" min="1" required>
                        </div>
                    </div>
                    
                    <div class="form-group mb-4">
                        <label class="text-white-50 small text-uppercase fw-bold mb-2 d-flex justify-content-between" style="letter-spacing: 1px;">
                            <span>Leverage</span>
                            <span id="leverage_val_display" class="text-white">5x</span>
                        </label>
                        <input type="range" name="leverage" id="leverage_slider" class="form-range" min="1" max="{{ $pairs->first()->max_leverage ?? 5 }}" value="5" oninput="document.getElementById('leverage_val_display').innerText = this.value + 'x'">
                    </div>
                    
                    <div class="row g-2 mt-4">
                        <div class="col-6">
                            <button type="submit" name="direction" value="long" class="btn btn-success w-100 fw-bold py-2" style="border-radius: 8px;">
                                <i class="fas fa-arrow-up me-1"></i> LONG
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="submit" name="direction" value="short" class="btn btn-danger w-100 fw-bold py-2" style="border-radius: 8px;">
                                <i class="fas fa-arrow-down me-1"></i> SHORT
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Trading History -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="glass-panel p-4">
                <h5 class="text-white mb-4 fw-bold pb-2 border-bottom border-secondary border-opacity-25">Trading History</h5>
                <div class="table-responsive">
                    <table class="table table-dark-glass table-borderless mb-0">
                        <thead>
                            <tr>
                                <th>Position ID</th>
                                <th>Asset</th>
                                <th>Direction</th>
                                <th>Leverage</th>
                                <th>Collateral</th>
                                <th>Entry Price</th>
                                <th>Size</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($positions as $pos)
                            <tr>
                                <td class="text-white-50">{{ $pos->trade_id }}</td>
                                <td>
                                    @php $posSymbol = optional($pos->marginPair)->symbol ?? 'N/A'; @endphp
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ \App\Services\AssetLogoService::getLogoUrl($posSymbol, 'crypto', '') }}" 
                                             onerror="this.onerror=null; this.src='/assets/img/profit.svg';" 
                                             style="width: 24px; height: 24px; border-radius: 50%; background: rgba(255,255,255,0.05); padding: 2px; object-fit: contain;">
                                        <span class="fw-bold text-white">{{ $posSymbol }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if($pos->direction == 'long')
                                        <span class="badge bg-success bg-opacity-25 text-success px-2 py-1"><i class="fas fa-arrow-up me-1"></i>LONG</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-25 text-danger px-2 py-1"><i class="fas fa-arrow-down me-1"></i>SHORT</span>
                                    @endif
                                </td>
                                <td>{{ $pos->leverage }}x</td>
                                <td class="text-white">${{ number_format($pos->collateral, 2) }}</td>
                                <td class="text-white" style="font-family: monospace;">${{ number_format($pos->entry_price, 2) }}</td>
                                <td class="text-white-50">{{ number_format($pos->quantity, 4) }}</td>
                                <td>
                                    @if($pos->status == 'open')
                                        <span class="badge bg-warning text-dark">OPEN</span>
                                    @else
                                        <span class="badge bg-secondary text-uppercase">{{ $pos->status }}</span>
                                    @endif
                                </td>
                                <td class="text-white-50 small">{{ $pos->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-white-50">
                                    <i class="fas fa-box-open fa-2x mb-3 opacity-50"></i>
                                    <p class="mb-0">No margin positions found.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
<script type="text/javascript">
    let tvWidget = null;
    
    function initChart(symbol) {
        // Map common crypto symbols to Binance format if they don't have an exchange prefix
        let formattedSymbol = symbol;
        if (!formattedSymbol.includes(':')) {
            formattedSymbol = "BINANCE:" + formattedSymbol;
        }

        if (tvWidget) {
            tvWidget = null;
            document.getElementById('tradingview_chart').innerHTML = '';
        }

        tvWidget = new TradingView.widget({
            "autosize": true,
            "symbol": formattedSymbol,
            "interval": "D",
            "timezone": "Etc/UTC",
            "theme": "dark",
            "style": "1",
            "locale": "en",
            "enable_publishing": false,
            "backgroundColor": "#131722",
            "gridColor": "rgba(255, 255, 255, 0.06)",
            "hide_top_toolbar": false,
            "hide_legend": false,
            "save_image": false,
            "container_id": "tradingview_chart"
        });
    }

    function selectAsset(id, symbol, maxLeverage, element) {
        // Update active styling
        document.querySelectorAll('.asset-item').forEach(el => el.classList.remove('active'));
        if (element) element.classList.add('active');

        // Update form values
        document.getElementById('selected_pair_id').value = id;
        document.getElementById('selected_asset_name').innerText = symbol;
        
        // Update leverage max
        const slider = document.getElementById('leverage_slider');
        slider.max = maxLeverage;
        if (parseInt(slider.value) > parseInt(maxLeverage)) {
            slider.value = maxLeverage;
            document.getElementById('leverage_val_display').innerText = maxLeverage + 'x';
        }
        document.getElementById('max-leverage-display').innerText = maxLeverage;

        // Reload chart
        initChart(symbol);
    }

    // Initialize with first pair on load
    document.addEventListener("DOMContentLoaded", function() {
        const initialSymbol = "{{ $pairs->first()->symbol ?? 'BTCUSDT' }}";
        initChart(initialSymbol);
    });
</script>
@endsection


