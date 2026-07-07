@extends('layouts.user.app')

@section('title', 'Copy Trading History')

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
                <div class="small text-secondary text-uppercase tracking-wider mb-2">Total Capital Allocated</div>
                <div class="h2 outfit font-weight-bold text-white">
                    ${{ number_format($totalCapital ?? 0, 2) }}
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4 text-center shadow-lg" style="background: rgba(16, 18, 27, 0.4); backdrop-filter: blur(16px); border-radius: 20px; border: 1px solid rgba(255,255,255,0.05);">
                <div class="small text-secondary text-uppercase tracking-wider mb-2">Total Net Profit</div>
                <div class="h2 outfit font-weight-bold {{ $totalPL >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ $totalPL >= 0 ? '+' : '' }}${{ number_format($totalPL ?? 0, 2) }}
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4 text-center shadow-lg" style="background: rgba(16, 18, 27, 0.4); backdrop-filter: blur(16px); border-radius: 20px; border: 1px solid rgba(255,255,255,0.05);">
                <div class="small text-secondary text-uppercase tracking-wider mb-2">Active Copy Orders</div>
                <div class="h2 outfit font-weight-bold text-warning">{{ $activeOrders->count() ?? 0 }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4 text-center shadow-lg" style="background: rgba(16, 18, 27, 0.4); backdrop-filter: blur(16px); border-radius: 20px; border: 1px solid rgba(255,255,255,0.05);">
                <div class="small text-secondary text-uppercase tracking-wider mb-2">Win Rate</div>
                <div class="h2 outfit font-weight-bold text-primary">
                    @php
                        $winCount = $orders->where('profit', '>', 0)->count();
                        $orderCount = $orders->count();
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
                    <h5 class="outfit font-weight-bold mb-0 text-white d-none d-md-block">Copy Trading History & Performance</h5>
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
                        <div class="input-group input-group-sm" style="width: 280px;">
                            <span class="input-group-text bg-transparent border-end-0 text-secondary border-secondary opacity-50"><i class="ri-filter-3-line"></i></span>
                            <input type="text" id="ledger-search" class="form-control premium-input border-start-0 border-secondary opacity-50 text-white" placeholder="Search parameters..." style="background: rgba(255,255,255,0.02);">
                        </div>
                        <div class="badge bg-primary-soft text-primary px-3 py-2" style="border-radius: 8px;">
                            <i class="ri-file-copy-line me-1"></i> {{ auth()->user()->is_demo ? 'DEMO ENVIRONMENT' : 'REAL ENVIRONMENT' }}
                        </div>
                    </div>
                </div>

                <div class="table-responsive d-none d-md-block">
                    <table class="table table-dark table-hover align-middle">
                        <thead class="small text-secondary uppercase font-weight-bold">
                            <tr>
                                <th>Trader</th>
                                <th>Date</th>
                                <th>Capital</th>
                                <th>Profit</th>
                                <th class="text-end">Status</th>
                            </tr>
                        </thead>
                        <tbody id="ledger-body">
                            @forelse($orders as $t)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary-soft rounded-circle d-flex align-items-center justify-content-center text-primary me-3" style="width: 32px; height: 32px;">
                                            <i class="ri-user-star-line" style="font-size: 1rem;"></i>
                                        </div>
                                        <div class="text-white fw-bold outfit ledger-search-target">{{ $t->trader_name ?? 'Expert Trader' }}</div>
                                    </div>
                                </td>
                                <td class="text-secondary align-middle">{{ \Carbon\Carbon::parse($t->created_at)->format('d M, Y H:i') }}</td>
                                <td class="text-white fw-bold align-middle">${{ number_format($t->amount, 2) }}</td>
                                <td class="fw-bold {{ ($t->profit ?? 0) >= 0 ? 'text-success' : 'text-danger' }} align-middle">
                                    {{ ($t->profit ?? 0) >= 0 ? '+' : '' }}${{ number_format($t->profit ?? 0, 2) }}
                                </td>
                                <td class="align-middle text-end">
                                    @php
                                        $status = strtolower($t->status);
                                        $statusClass = $status === 'completed' || $status === 'closed' ? 'text-success' : ($status === 'pending' || $status === 'running' || $status === 'active' ? 'text-warning' : 'text-danger');
                                        $statusIcon = $status === 'completed' || $status === 'closed' ? 'checkbox-circle' : ($status === 'pending' || $status === 'running' || $status === 'active' ? 'time-line' : 'close-circle');
                                    @endphp
                                    <div class="d-inline-flex align-items-center gap-1 {{ $statusClass }}">
                                        <i class="ri-{{ $statusIcon }}-line"></i>
                                        <span class="fw-bold small text-uppercase" style="letter-spacing: 0.5px;">{{ $status }}</span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="opacity-20 mb-3"><i class="ri-file-copy-line" style="font-size: 3rem;"></i></div>
                                    <div class="text-secondary">Your copy trading history is currently empty.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards View -->
                <div class="mobile-cards-view d-md-none flex-column gap-3">
                    @forelse($orders as $t)
                    <div class="glass-card p-3 ledger-mobile-card" style="background: rgba(16, 18, 27, 0.4); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px;">
                        @php
                            $status = strtolower($t->status);
                            $statusClass = $status === 'completed' || $status === 'closed' ? 'text-success' : ($status === 'pending' || $status === 'running' || $status === 'active' ? 'text-warning' : 'text-danger');
                            $statusIcon = $status === 'completed' || $status === 'closed' ? 'checkbox-circle' : ($status === 'pending' || $status === 'running' || $status === 'active' ? 'time-line' : 'close-circle');
                        @endphp
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-secondary border-opacity-25">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary-soft rounded-circle d-flex align-items-center justify-content-center text-primary" style="width: 32px; height: 32px;">
                                    <i class="ri-user-star-line"></i>
                                </div>
                                <div>
                                    <div class="text-white fw-bold outfit ledger-search-target">{{ $t->trader_name ?? 'Expert Trader' }}</div>
                                    <div class="text-secondary" style="font-size: 10px;">{{ \Carbon\Carbon::parse($t->created_at)->format('d M, Y H:i') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <div class="text-secondary" style="font-size: 0.7rem;">Capital Allocated</div>
                                <div class="text-white fw-bold">${{ number_format($t->amount, 2) }}</div>
                            </div>
                            <div class="col-6 text-end">
                                <div class="text-secondary" style="font-size: 0.7rem;">Current Profit</div>
                                <div class="fw-bold {{ ($t->profit ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ ($t->profit ?? 0) >= 0 ? '+' : '' }}${{ number_format($t->profit ?? 0, 2) }}
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
                        <div class="opacity-20 mb-2"><i class="ri-file-copy-line" style="font-size: 2rem;"></i></div>
                        <div class="text-secondary small">Your copy trading history is empty.</div>
                    </div>
                    @endforelse
                </div>

                @if(method_exists($orders, 'links'))
                <div class="mt-4 d-flex justify-content-center">
                    {{ $orders->links('pagination::bootstrap-4') }}
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

