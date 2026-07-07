@extends('layouts.user.app')

@section('title', 'Investing History')

@section('content')
<style>
@media (max-width: 767.98px) {
    .mobile-cards-view {
        display: flex !important;
    }
}
</style>
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

<div class="container-fluid py-4">
    <!-- Analysis Summary Section -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="glass-card p-4 text-center shadow-lg" style="background: rgba(16, 18, 27, 0.4); backdrop-filter: blur(16px); border-radius: 20px; border: 1px solid rgba(255,255,255,0.05);">
                <div class="small text-secondary text-uppercase tracking-wider mb-2">Total Yield (NET)</div>
                <div class="h2 outfit font-weight-bold {{ $win >= 0 ? 'text-success' : 'text-danger' }}">
                    ${{ number_format($win ?? 0, 2) }}
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4 text-center shadow-lg" style="background: rgba(16, 18, 27, 0.4); backdrop-filter: blur(16px); border-radius: 20px; border: 1px solid rgba(255,255,255,0.05);">
                <div class="small text-secondary text-uppercase tracking-wider mb-2">Maximum Drawdown</div>
                <div class="h2 outfit font-weight-bold text-danger">-${{ number_format($loss ?? 0, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4 text-center shadow-lg" style="background: rgba(16, 18, 27, 0.4); backdrop-filter: blur(16px); border-radius: 20px; border: 1px solid rgba(255,255,255,0.05);">
                <div class="small text-secondary text-uppercase tracking-wider mb-2">Trade Frequency</div>
                <div class="h2 outfit font-weight-bold text-warning">{{ $orderCount ?? 0 }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4 text-center shadow-lg" style="background: rgba(16, 18, 27, 0.4); backdrop-filter: blur(16px); border-radius: 20px; border: 1px solid rgba(255,255,255,0.05);">
                <div class="small text-secondary text-uppercase tracking-wider mb-2">Success Velocity</div>
                <div class="h2 outfit font-weight-bold text-primary">
                    @php 
                        $winCount = $transactions->where('status', 'win')->count();
                        $rate = $orderCount > 0 ? ($winCount / $orderCount) * 100 : 0;
                    @endphp
                    {{ number_format($rate, 1) }}%
                </div>
            </div>
        </div>
    </div>

    <!-- Execution Ledger -->
    <div class="row">
        <div class="col-12">
            <div class="glass-card p-4" style="background: rgba(16, 18, 27, 0.4); backdrop-filter: blur(16px); border-radius: 24px; border: 1px solid rgba(255,255,255,0.05);">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-dark">
                    <h5 class="outfit font-weight-bold mb-0 text-white d-none d-md-block">Investing History</h5>
                    <ul class="nav nav-pills gap-2" id="history-tabs" style="border-color: rgba(255,255,255,0.05) !important; margin: 0;">
                        <li class="nav-item">
                            <a class="nav-link px-3 py-1 {{ request()->routeIs('trades.history') ? 'active bg-primary text-white font-weight-bold' : 'text-secondary' }}" href="{{ route('trades.history') }}" style="border-radius: 8px; font-size: 0.75rem; letter-spacing: 0.5px; background: {{ request()->routeIs('trades.history') ? '' : 'rgba(255,255,255,0.05)' }};">MANUAL</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3 py-1 {{ request()->routeIs('copy-trading.history') ? 'active bg-primary text-white font-weight-bold' : 'text-secondary' }}" href="{{ route('copy-trading.history') }}" style="border-radius: 8px; font-size: 0.75rem; letter-spacing: 0.5px; background: {{ request()->routeIs('copy-trading.history') ? '' : 'rgba(255,255,255,0.05)' }};">
                                COPY
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3 py-1 {{ request()->routeIs('bots.history') ? 'active bg-primary text-white font-weight-bold' : 'text-secondary' }}" href="{{ auth()->user()->hasFeature('bot_trading') ? route('bots.history') : route('user.upgrade') }}" style="border-radius: 8px; font-size: 0.75rem; letter-spacing: 0.5px; background: {{ request()->routeIs('bots.history') ? '' : 'rgba(255,255,255,0.05)' }};">
                                BOT @unless(auth()->user()->hasFeature('bot_trading'))<i class="ri-lock-line ms-1"></i>@endunless
                            </a>
                        </li>
                    </ul>
                    <div class="d-flex gap-3 align-items-center">
                        <a href="{{ route('export.trade_history') }}" class="btn btn-sm px-3 py-2" style="background: linear-gradient(135deg, #ff3333, #059669); color:#fff; border-radius: 10px; font-size: 11px; font-weight: 800; letter-spacing: 0.5px; border:none;">
                            <i class="ri-file-pdf-2-line me-1"></i> EXPORT PDF
                        </a>
                        <div class="input-group input-group-sm" style="width: 280px;">
                            <span class="input-group-text bg-transparent border-end-0 text-secondary border-secondary opacity-50"><i class="ri-filter-3-line"></i></span>
                            <input type="text" id="ledger-search" class="form-control premium-input border-start-0 border-secondary opacity-50 text-white" placeholder="Search parameters..." style="background: rgba(255,255,255,0.02);">
                        </div>
                        <div class="badge bg-primary-soft text-primary px-3 py-2" style="border-radius: 8px;">
                            <i class="ri-database-2-line me-1"></i> {{ auth()->user()->is_demo ? 'DEMO ENVIRONMENT' : 'REAL ENVIRONMENT' }}
                        </div>
                    </div>
                </div>

                <div class="table-responsive d-none d-md-block">
                    <table class="table table-dark table-hover align-middle">
                        <thead class="small text-secondary uppercase font-weight-bold">
                            <tr>
                                <th>Timestamp</th>
                                <th>Security / Asset</th>
                                <th>Side</th>
                                <th>Volume (Units)</th>
                                <th>Leverage</th>
                                <th>Exposure ($)</th>
                                <th class="text-center">Outcome</th>
                                <th class="text-end">Resulting P/L</th>
                            </tr>
                        </thead>
                        <tbody id="ledger-body">
                            @forelse($transactions as $t)
                            <tr>
                                <td class="small">
                                    <div class="text-white fw-600">{{ \Carbon\Carbon::parse($t->created_at)->format('d M, Y') }}</div>
                                    <div class="text-secondary" style="font-size: 10px;">{{ \Carbon\Carbon::parse($t->created_at)->format('H:i:s') }} UTC</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-4">
                                        @php
                                            $assetSymbol = !empty($t->symbol) && $t->symbol !== 'N/A' ? $t->symbol : (!empty($t->asset->symbols) && $t->asset->symbols !== 'N/A' ? $t->asset->symbols : 'Unknown Asset');
                                            $exchangeName = !empty($t->exchanges->name) ? $t->exchanges->name : 'Market';
                                        @endphp
                                        <x-asset-logo :symbol="$assetSymbol" size="sm" />
                                        <div>
                                            <div class="text-white fw-bold outfit">{{ $assetSymbol }}</div>
                                            <div class="text-secondary" style="font-size: 10px;">{{ $exchangeName }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $isBuy = strtolower($t->type ?? 'buy') === 'buy' || strtolower($t->types ?? 'buy') === 'buy';
                                    @endphp
                                    <span class="badge {{ $isBuy ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger' }} px-3 py-2" style="border-radius: 6px;">
                                        {{ strtoupper($isBuy ? 'LONG' : 'SHORT') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-white">{{ number_format($t->amount / ($t->unit ?? 1), 4) }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-soft text-secondary px-2 py-1">×{{ $t->leverage ?? '1' }}</span>
                                </td>
                                <td class="fw-bold outfit text-white">
                                    ${{ number_format($t->amount, 2) }}
                                </td>
                                <td class="text-center">
                                    @php
                                        $status = strtolower($t->status);
                                        $statusClass = $status === 'win' ? 'text-success' : ($status === 'loss' ? 'text-danger' : 'text-warning');
                                        $statusIcon = $status === 'win' ? 'checkbox-circle' : ($status === 'loss' ? 'close-circle' : 'time-line');
                                    @endphp
                                    <div class="d-flex align-items-center justify-content-center gap-2 {{ $statusClass }}">
                                        <i class="ri-{{ $statusIcon }}-line h6 mb-0"></i>
                                        <span class="fw-bold small text-uppercase" style="letter-spacing: 1px;">{{ $status }}</span>
                                    </div>
                                </td>
                                <td class="text-end outfit fw-bold">
                                    <span class="{{ $status === 'win' ? 'text-success' : ($status === 'loss' ? 'text-danger' : 'text-white') }}">
                                        {{ $status === 'win' ? '+' : ($status === 'loss' ? '-' : '') }}${{ number_format(abs($t->p_l ?? 0), 2) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="opacity-20 mb-3"><i class="ri-bar-chart-2-line" style="font-size: 3rem;"></i></div>
                                    <div class="text-secondary">Your trading history is currently empty. Start executing trades to populate your journal.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards View -->
                <div class="mobile-cards-view d-md-none flex-column gap-3">
                    @forelse($transactions as $t)
                    <div class="glass-card p-3 ledger-mobile-card" style="background: rgba(16, 18, 27, 0.4); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px;">
                        @php
                            $assetSymbol = !empty($t->symbol) && $t->symbol !== 'N/A' ? $t->symbol : (!empty($t->asset->symbols) && $t->asset->symbols !== 'N/A' ? $t->asset->symbols : 'Unknown Asset');
                            $exchangeName = !empty($t->exchanges->name) ? $t->exchanges->name : 'Market';
                            $isBuy = strtolower($t->type ?? 'buy') === 'buy' || strtolower($t->types ?? 'buy') === 'buy';
                            $status = strtolower($t->status);
                            $statusClass = $status === 'win' ? 'text-success' : ($status === 'loss' ? 'text-danger' : 'text-warning');
                            $statusIcon = $status === 'win' ? 'checkbox-circle' : ($status === 'loss' ? 'close-circle' : 'time-line');
                        @endphp
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-secondary border-opacity-25">
                            <div class="d-flex align-items-center gap-2">
                                <x-asset-logo :symbol="$assetSymbol" size="sm" />
                                <div>
                                    <div class="text-white fw-bold outfit ledger-search-target">{{ $assetSymbol }}</div>
                                    <div class="text-secondary" style="font-size: 10px;">{{ \Carbon\Carbon::parse($t->created_at)->format('d M, Y H:i') }}</div>
                                </div>
                            </div>
                            <span class="badge {{ $isBuy ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger' }} px-2 py-1" style="border-radius: 4px; font-size: 0.7rem;">
                                {{ strtoupper($isBuy ? 'LONG' : 'SHORT') }}
                            </span>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <div class="text-secondary" style="font-size: 0.7rem;">Exposure</div>
                                <div class="text-white fw-bold">${{ number_format($t->amount, 2) }} <span class="text-secondary fw-normal">×{{ $t->leverage ?? '1' }}</span></div>
                            </div>
                            <div class="col-6 text-end">
                                <div class="text-secondary" style="font-size: 0.7rem;">Resulting P/L</div>
                                <div class="fw-bold {{ $status === 'win' ? 'text-success' : ($status === 'loss' ? 'text-danger' : 'text-white') }}">
                                    {{ $status === 'win' ? '+' : ($status === 'loss' ? '-' : '') }}${{ number_format(abs($t->p_l ?? 0), 2) }}
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between pt-2">
                            <div class="text-secondary" style="font-size: 0.75rem;">Status</div>
                            <div class="d-flex align-items-center gap-1 {{ $statusClass }}">
                                <i class="ri-{{ $statusIcon }}-line"></i>
                                <span class="fw-bold small text-uppercase" style="font-size: 0.75rem;">{{ $status }}</span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <div class="opacity-20 mb-2"><i class="ri-bar-chart-2-line" style="font-size: 2rem;"></i></div>
                        <div class="text-secondary small">Your trading history is empty.</div>
                    </div>
                    @endforelse
                </div>

                <div class="mt-4 d-flex justify-content-center">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-success-soft { background: rgba(255, 51, 51, 0.1) !important; }
    .bg-danger-soft { background: rgba(239, 68, 68, 0.1) !important; }
    .bg-warning-soft { background: rgba(245, 158, 11, 0.1) !important; }
    .bg-primary-soft { background: rgba(14, 165, 233, 0.1) !important; }
    .bg-secondary-soft { background: rgba(255, 255, 255, 0.05) !important; }
    
    .table-hover tbody tr { transition: all 0.2s ease-in-out; }
    .table-hover tbody tr:hover { 
        background: rgba(255,255,255,0.03) !important;
        cursor: pointer;
    }
    
    .fw-600 { font-weight: 600; }
    
    .pagination { gap: 5px; }
    .page-link { 
        background: rgba(255,255,255,0.05) !important; 
        border: 1px solid rgba(255,255,255,0.1) !important; 
        color: #94a3b8 !important;
        border-radius: 8px !important;
        padding: 8px 16px;
    }
    .page-item.active .page-link {
        background: var(--accent-primary) !important;
        color: white !important;
        border-color: var(--accent-primary) !important;
    }
</style>

<script>
    document.getElementById('ledger-search').onkeyup = function() {
        let val = this.value.toLowerCase();
        let rows = document.querySelectorAll('#ledger-body tr');
        rows.forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(val) ? '' : 'none';
        });
        
        let cards = document.querySelectorAll('.ledger-mobile-card');
        cards.forEach(card => {
            card.style.display = card.innerText.toLowerCase().includes(val) ? '' : 'none';
        });
    };
</script>
@endsection

