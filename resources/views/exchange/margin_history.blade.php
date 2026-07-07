@extends('layouts.user.app')

@section('title', 'Margin Trading History')

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
        <div class="col-md-4">
            <div class="glass-card p-4 text-center shadow-lg" style="background: rgba(16, 18, 27, 0.4); backdrop-filter: blur(16px); border-radius: 20px; border: 1px solid rgba(255,255,255,0.05);">
                <div class="small text-secondary text-uppercase tracking-wider mb-2">Total Yield (NET)</div>
                <div class="h2 outfit font-weight-bold {{ $positions->sum('realized_pnl') >= 0 ? 'text-success' : 'text-danger' }}">
                    ${{ number_format($positions->sum('realized_pnl'), 2) }}
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card p-4 text-center shadow-lg" style="background: rgba(16, 18, 27, 0.4); backdrop-filter: blur(16px); border-radius: 20px; border: 1px solid rgba(255,255,255,0.05);">
                <div class="small text-secondary text-uppercase tracking-wider mb-2">Trade Frequency</div>
                <div class="h2 outfit font-weight-bold text-warning">{{ $positions->count() }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card p-4 text-center shadow-lg" style="background: rgba(16, 18, 27, 0.4); backdrop-filter: blur(16px); border-radius: 20px; border: 1px solid rgba(255,255,255,0.05);">
                <div class="small text-secondary text-uppercase tracking-wider mb-2">Win Rate</div>
                <div class="h2 outfit font-weight-bold text-primary">
                    @php 
                        $winCount = $positions->where('realized_pnl', '>', 0)->count();
                        $rate = $positions->count() > 0 ? ($winCount / $positions->count()) * 100 : 0;
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
                    <h5 class="outfit font-weight-bold mb-0 text-white d-none d-md-block">Margin History</h5>
                    <ul class="nav nav-pills gap-2" id="history-tabs" style="border-color: rgba(255,255,255,0.05) !important; margin: 0;">
                        <li class="nav-item">
                            <a class="nav-link px-3 py-1 bg-primary text-white font-weight-bold" href="{{ route('margin.history') }}" style="border-radius: 8px; font-size: 0.75rem; letter-spacing: 0.5px;">HISTORY</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3 py-1 text-secondary" href="{{ route('margin.trade') }}" style="border-radius: 8px; font-size: 0.75rem; letter-spacing: 0.5px; background: rgba(255,255,255,0.05);">TRADE</a>
                        </li>
                    </ul>
                    <div class="d-flex gap-3 align-items-center">
                        <div class="input-group input-group-sm" style="width: 280px;">
                            <span class="input-group-text bg-transparent border-end-0 text-secondary border-secondary opacity-50"><i class="ri-filter-3-line"></i></span>
                            <input type="text" id="ledger-search" class="form-control premium-input border-start-0 border-secondary opacity-50 text-white" placeholder="Search parameters..." style="background: rgba(255,255,255,0.02);">
                        </div>
                    </div>
                </div>

                <div class="table-responsive d-none d-md-block">
                    <table class="table table-dark table-hover align-middle">
                        <thead class="small text-secondary uppercase font-weight-bold">
                            <tr>
                                <th>Timestamp</th>
                                <th>Symbol</th>
                                <th>Direction</th>
                                <th>Leverage</th>
                                <th>Margin</th>
                                <th>Entry Price</th>
                                <th class="text-center">Status</th>
                                <th class="text-end">Realized PnL</th>
                            </tr>
                        </thead>
                        <tbody id="ledger-body">
                            @forelse($positions as $t)
                            <tr>
                                <td class="small">
                                    <div class="text-white fw-600">{{ \Carbon\Carbon::parse($t->created_at)->format('d M, Y') }}</div>
                                    <div class="text-secondary" style="font-size: 10px;">{{ \Carbon\Carbon::parse($t->created_at)->format('H:i:s') }} UTC</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        @php $sym = $t->MarginPair->symbol ?? 'N/A'; @endphp
                                        <img src="{{ \App\Services\AssetLogoService::getLogoUrl($sym, 'crypto', '') }}" onerror="this.onerror=null; this.src='/assets/img/profit.svg';" style="width: 28px; height: 28px; border-radius: 50%;">
                                        <div>
                                            <div class="text-white fw-bold outfit">{{ $sym }}</div>
                                            <div class="text-secondary" style="font-size: 10px;">{{ $t->trade_id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($t->direction == 'long')
                                        <span class="badge bg-success text-white px-3 py-2" style="border-radius: 6px;">LONG</span>
                                    @else
                                        <span class="badge bg-danger text-white px-3 py-2" style="border-radius: 6px;">SHORT</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary-soft text-secondary px-2 py-1">{{ $t->leverage }}x</span>
                                </td>
                                <td class="fw-bold outfit text-white">
                                    ${{ number_format($t->collateral, 2) }}
                                </td>
                                <td class="fw-bold outfit text-white">
                                    ${{ number_format($t->entry_price, 2) }}
                                </td>
                                <td class="text-center">
                                    @php
                                        $status = strtolower($t->status);
                                        $statusClass = $status === 'liquidated' ? 'text-danger' : 'text-success';
                                    @endphp
                                    <div class="d-flex align-items-center justify-content-center gap-2 {{ $statusClass }}">
                                        <span class="fw-bold small text-uppercase" style="letter-spacing: 1px;">{{ $status }}</span>
                                    </div>
                                </td>
                                <td class="text-end outfit fw-bold">
                                    <span class="{{ $t->realized_pnl >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $t->realized_pnl >= 0 ? '+' : '' }}${{ number_format($t->realized_pnl, 2) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="opacity-20 mb-3"><i class="ri-bar-chart-2-line" style="font-size: 3rem;"></i></div>
                                    <div class="text-secondary">Your Margin history is currently empty.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards View -->
                <div class="mobile-cards-view d-md-none flex-column gap-3">
                    @forelse($positions as $t)
                    <div class="glass-card p-3 ledger-mobile-card" style="background: rgba(16, 18, 27, 0.4); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px;">
                        @php
                            $status = strtolower($t->status);
                            $statusClass = $status === 'liquidated' ? 'text-danger' : 'text-success';
                            $sym = $t->MarginPair->symbol ?? 'N/A';
                        @endphp
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-secondary border-opacity-25">
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ \App\Services\AssetLogoService::getLogoUrl($sym, 'crypto', '') }}" onerror="this.onerror=null; this.src='/assets/img/profit.svg';" style="width: 28px; height: 28px; border-radius: 50%;">
                                <div>
                                    <div class="text-white fw-bold outfit ledger-search-target">{{ $sym }}</div>
                                    <div class="text-secondary" style="font-size: 10px;">{{ \Carbon\Carbon::parse($t->created_at)->format('d M, Y H:i') }}</div>
                                </div>
                            </div>
                            @if($t->direction == 'long')
                                <span class="badge bg-success text-white px-2 py-1" style="border-radius: 4px; font-size: 0.7rem;">LONG</span>
                            @else
                                <span class="badge bg-danger text-white px-2 py-1" style="border-radius: 4px; font-size: 0.7rem;">SHORT</span>
                            @endif
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <div class="text-secondary" style="font-size: 0.7rem;">Margin</div>
                                <div class="text-white fw-bold">${{ number_format($t->collateral, 2) }}</div>
                            </div>
                            <div class="col-6 text-end">
                                <div class="text-secondary" style="font-size: 0.7rem;">Entry Price</div>
                                <div class="text-white fw-bold">${{ number_format($t->entry_price, 2) }}</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between pt-2">
                            <div class="text-secondary" style="font-size: 0.75rem;">Realized PnL</div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-secondary-soft text-secondary px-2 py-1">{{ $t->leverage }}x</span>
                                <span class="fw-bold {{ $t->realized_pnl >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $t->realized_pnl >= 0 ? '+' : '' }}${{ number_format($t->realized_pnl, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <div class="opacity-20 mb-2"><i class="ri-bar-chart-2-line" style="font-size: 2rem;"></i></div>
                        <div class="text-secondary small">Your Margin history is empty.</div>
                    </div>
                    @endforelse
                </div>
                
                @if(method_exists($positions, 'links'))
                <div class="mt-4 d-flex justify-content-center">
                    {{ $positions->links('pagination::bootstrap-4') }}
                </div>
                @endif


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


