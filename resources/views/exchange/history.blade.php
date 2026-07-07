@extends('layouts.user.app')

@section('title', 'Transfer History')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

<div class="container-fluid py-4">
    <!-- Header Summary Section -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="glass-card p-4 text-center shadow-lg" style="background: rgba(16, 18, 27, 0.4); backdrop-filter: blur(16px); border-radius: 20px; border: 1px solid rgba(255,255,255,0.05);">
                <div class="small text-secondary text-uppercase tracking-wider mb-2">Total Deposits</div>
                <div class="h2 outfit font-weight-bold text-success">${{ number_format($deposit ?? 0, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4 text-center shadow-lg" style="background: rgba(16, 18, 27, 0.4); backdrop-filter: blur(16px); border-radius: 20px; border: 1px solid rgba(255,255,255,0.05);">
                <div class="small text-secondary text-uppercase tracking-wider mb-2">Total Withdrawals</div>
                <div class="h2 outfit font-weight-bold text-danger">${{ number_format($withdrawal ?? 0, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4 text-center shadow-lg" style="background: rgba(16, 18, 27, 0.4); backdrop-filter: blur(16px); border-radius: 20px; border: 1px solid rgba(255,255,255,0.05);">
                <div class="small text-secondary text-uppercase tracking-wider mb-2">Pending Requests</div>
                <div class="h2 outfit font-weight-bold text-warning">{{ $orderCount ?? 0 }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4 text-center shadow-lg" style="background: rgba(16, 18, 27, 0.4); backdrop-filter: blur(16px); border-radius: 20px; border: 1px solid rgba(255,255,255,0.05);">
                <div class="small text-secondary text-uppercase tracking-wider mb-2">Net Flow</div>
                <div class="h2 outfit font-weight-bold text-primary">${{ number_format(($deposit ?? 0) - ($withdrawal ?? 0), 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Main Activity Table -->
    <div class="row">
        <div class="col-12">
            <div class="glass-card p-4" style="background: rgba(16, 18, 27, 0.4); backdrop-filter: blur(16px); border-radius: 24px; border: 1px solid rgba(255,255,255,0.05);">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-dark">
                    <h5 class="outfit font-weight-bold mb-0 text-white">Transfer History</h5>
                    <div class="d-flex gap-2">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <span class="input-group-text bg-transparent border-end-0 text-secondary border-secondary opacity-50"><i class="ri-search-2-line"></i></span>
                            <input type="text" id="ledger-search" class="form-control premium-input border-start-0 border-secondary opacity-50 text-white" placeholder="Filter transactions..." style="background: rgba(255,255,255,0.02);">
                        </div>
                        <button class="btn btn-sm btn-outline-premium px-3"><i class="ri-download-2-line"></i> Export CSV</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle">
                        <thead class="small text-secondary uppercase font-weight-bold">
                            <tr>
                                <th>Timestamp</th>
                                <th>Transaction ID</th>
                                <th>Type</th>
                                <th>Source/Dest</th>
                                <th>Amount</th>
                                <th class="text-center">Status</th>
                                <th class="text-end">Proof/Action</th>
                            </tr>
                        </thead>
                        <tbody id="ledger-body">
                            @forelse($transactions as $t)
                            <tr>
                                <td class="small">
                                    <div class="text-white">{{ \Carbon\Carbon::parse($t->created_at)->format('d M, Y') }}</div>
                                    <div class="text-secondary" style="font-size: 10px;">{{ \Carbon\Carbon::parse($t->created_at)->format('H:i:s') }}</div>
                                </td>
                                <td><code class="text-primary">{{ $t->trx_id }}</code></td>
                                <td>
                                    <span class="d-flex align-items-center gap-2">
                                        <i class="ri-{{ $t->type === 'deposit' ? 'arrow-down-circle-line text-success' : 'arrow-up-circle-line text-danger' }} h5 mb-0"></i>
                                        <span class="text-capitalize">{{ $t->type }}</span>
                                    </span>
                                </td>
                                <td>
                                    <span class="small">{{ $t->address ?? 'System Balance' }}</span>
                                </td>
                                <td class="font-weight-bold outfit">
                                    ${{ number_format($t->amount, 2) }}
                                </td>
                                <td class="text-center">
                                    @php
                                        $statusBg = [
                                            'success' => 'bg-success',
                                            'confirmed' => 'bg-success',
                                            'pending' => 'bg-warning',
                                            'failed' => 'bg-danger',
                                            'cancelled' => 'bg-danger',
                                        ][strtolower($t->status)] ?? 'bg-secondary';
                                        
                                        $statusText = [
                                            'success' => 'text-white',
                                            'confirmed' => 'text-white',
                                            'pending' => 'text-dark',
                                            'failed' => 'text-white',
                                            'cancelled' => 'text-white',
                                        ][strtolower($t->status)] ?? 'text-white';
                                    @endphp
                                    @if(strtolower($t->status) === 'pending')
                                    <div class="pending-progress">
                                        <div class="d-flex justify-content-between w-100"><span class="progress-label">Processing</span><span class="progress-pct">93%</span></div>
                                        <div class="progress-track"><div class="progress-fill"></div></div>
                                    </div>
                                    @else
                                    <span class="badge {{ $statusBg }} {{ $statusText }} px-3 py-2" style="border-radius: 8px; font-size: 0.7rem; font-weight: 800; box-shadow: 0 2px 4px rgba(0,0,0,0.2); min-width: 90px;">
                                        {{ strtoupper($t->status) }}
                                    </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-xs btn-outline-secondary" onclick="viewDetails('{{ $t->trx_id }}')">
                                        <i class="ri-eye-line"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="opacity-20 mb-3"><i class="ri-bubble-chart-line" style="font-size: 3rem;"></i></div>
                                    <div class="text-secondary">No transactions found in this period.</div>
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

<style>
    .bg-success-soft { background: rgba(255, 51, 51, 0.1); }
    .bg-warning-soft { background: rgba(245, 158, 11, 0.1); }
    .bg-danger-soft { background: rgba(239, 68, 68, 0.1); }
    .bg-secondary-soft { background: rgba(255, 255, 255, 0.05); }
    
    .table-hover tbody tr { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .table-hover tbody tr:hover { 
        background: rgba(255,255,255,0.04) !important;
        transform: translateY(-2px);
    }
    .btn-xs { padding: 4px 8px; font-size: 0.75rem; border-radius: 6px; }
    .btn-outline-premium { 
        border: 1px solid rgba(59, 130, 246, 0.5);
        color: #3b82f6;
        transition: all 0.3s;
    }
    .btn-outline-premium:hover {
        background: rgba(59, 130, 246, 0.1);
        border-color: #3b82f6;
        color: #fff;
    }
</style>

<script>
    function viewDetails(trxId) {
        toastr.info('Audit trail for ' + trxId + ' is being generated...');
    }
    
    document.getElementById('ledger-search').onkeyup = function() {
        let val = this.value.toLowerCase();
        let rows = document.querySelectorAll('#ledger-body tr');
        rows.forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(val) ? '' : 'none';
        });
    };

    // Staggered Entrance Animation
    window.addEventListener('load', () => {
        if (typeof anime !== 'undefined') {
            anime({
                targets: '#ledger-body tr',
                translateY: [20, 0],
                opacity: [0, 1],
                delay: anime.stagger(50),
                easing: 'easeOutQuint',
                duration: 800
            });
            
            anime({
                targets: '.col-md-3 .glass-card',
                scale: [0.95, 1],
                opacity: [0, 1],
                delay: anime.stagger(100),
                easing: 'easeOutQuint',
                duration: 600
            });
        }
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
@endsection
