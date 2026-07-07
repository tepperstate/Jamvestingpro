@extends('layouts.user.app')

@section('title', 'Transfer History')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
<!-- Google Fonts for Outfit -->
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">

<style>
    :root {
        --gold-primary: #990000;
        --gold-light: #f3e5ab;
        --gold-dark: #aa8529;
        --dark-bg: #0b0c10;
        --glass-bg: rgba(20, 22, 30, 0.6);
        --glass-border: rgba(153, 0, 0, 0.15);
    }
    
    body {
        background-color: var(--dark-bg);
        color: #fff;
        font-family: 'Outfit', sans-serif;
    }

    .mobile-container {
        padding: 16px;
        padding-bottom: 80px; /* space for bottom nav if any */
        max-width: 600px;
        margin: 0 auto;
    }

    .glass-panel {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 16px;
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
    }

    /* Summary Grid */
    .summary-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 24px;
    }

    .summary-card {
        padding: 16px 12px;
        text-align: center;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        position: relative;
        overflow: hidden;
    }

    .summary-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; height: 2px;
        background: linear-gradient(90deg, transparent, var(--gold-primary), transparent);
        opacity: 0.5;
    }

    .summary-label {
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: rgba(255,255,255,0.6);
        margin-bottom: 8px;
    }

    .summary-value {
        font-size: 1.25rem;
        font-weight: 800;
        color: #fff;
    }
    
    .text-gold { color: var(--gold-primary) !important; }

    /* Search Bar */
    .search-wrapper {
        position: relative;
        margin-bottom: 24px;
    }

    .search-input {
        width: 100%;
        background: rgba(0,0,0,0.3);
        border: 1px solid var(--glass-border);
        border-radius: 12px;
        padding: 12px 16px 12px 40px;
        color: #fff;
        font-family: 'Outfit', sans-serif;
        transition: all 0.3s ease;
    }

    .search-input:focus {
        outline: none;
        border-color: var(--gold-primary);
        box-shadow: 0 0 0 2px rgba(153, 0, 0, 0.2);
    }

    .search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gold-primary);
        opacity: 0.7;
    }

    /* Transaction List */
    .trx-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    .trx-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--gold-light);
        margin: 0;
    }

    .btn-export {
        background: transparent;
        border: 1px solid var(--gold-primary);
        color: var(--gold-primary);
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 0.75rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    
    .btn-export:active {
        background: rgba(153, 0, 0, 0.1);
    }

    .trx-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .trx-item {
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        transition: transform 0.2s;
        cursor: pointer;
    }
    
    .trx-item:active {
        transform: scale(0.98);
    }

    .trx-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .trx-icon-wrapper {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    
    .icon-deposit { background: rgba(255, 51, 51, 0.1); color: #ff3333; }
    .icon-withdraw { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

    .trx-info {
        flex-grow: 1;
        margin-left: 12px;
    }

    .trx-type {
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 2px;
        text-transform: capitalize;
    }

    .trx-id {
        font-size: 0.7rem;
        color: rgba(255,255,255,0.5);
        font-family: monospace;
        letter-spacing: 0.5px;
    }

    .trx-amount {
        text-align: right;
    }

    .amount-value {
        font-weight: 800;
        font-size: 1.05rem;
    }

    .amount-date {
        font-size: 0.65rem;
        color: rgba(255,255,255,0.5);
        margin-top: 4px;
    }

    .trx-bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 12px;
        border-top: 1px solid rgba(255,255,255,0.05);
    }

    .trx-address {
        font-size: 0.75rem;
        color: rgba(255,255,255,0.7);
        max-width: 60%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: flex;
        align-items: center;
    }

    .status-badge {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-success { background: rgba(255, 51, 51, 0.15); color: #ff3333; border: 1px solid rgba(255, 51, 51, 0.3); }
    .status-pending { background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); }
    .status-failed { background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }

    .empty-state {
        padding: 40px 20px;
        text-align: center;
    }

    .empty-icon {
        font-size: 3rem;
        color: var(--gold-primary);
        opacity: 0.3;
        margin-bottom: 16px;
    }

    .empty-text {
        color: rgba(255,255,255,0.6);
        font-size: 0.9rem;
    }
</style>

<div class="mobile-container">
    <!-- Header Summary Section -->
    <div class="summary-grid">
        <div class="glass-panel summary-card anime-card">
            <div class="summary-label">Total Deposits</div>
            <div class="summary-value text-success">${{ number_format($deposit ?? 0, 2) }}</div>
        </div>
        <div class="glass-panel summary-card anime-card">
            <div class="summary-label">Total Withdrawals</div>
            <div class="summary-value text-danger">${{ number_format($withdrawal ?? 0, 2) }}</div>
        </div>
        <div class="glass-panel summary-card anime-card">
            <div class="summary-label">Pending Requests</div>
            <div class="summary-value text-warning">{{ $orderCount ?? 0 }}</div>
        </div>
        <div class="glass-panel summary-card anime-card">
            <div class="summary-label">Net Flow</div>
            <div class="summary-value text-gold">${{ number_format(($deposit ?? 0) - ($withdrawal ?? 0), 2) }}</div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="search-wrapper anime-fade">
        <i class="ri-search-2-line search-icon"></i>
        <input type="text" id="ledger-search" class="search-input" placeholder="Filter transactions...">
    </div>

    <!-- Main Activity List -->
    <div class="trx-header anime-fade">
        <h5 class="trx-title">History</h5>
        <button class="btn-export"><i class="ri-download-2-line"></i> CSV</button>
    </div>

    <div class="trx-list" id="ledger-body">
        @forelse($transactions as $t)
        <div class="glass-panel trx-item anime-item" onclick="viewDetails('{{ $t->trx_id }}')">
            <div class="trx-top">
                <div class="trx-icon-wrapper {{ $t->type === 'deposit' ? 'icon-deposit' : 'icon-withdraw' }}">
                    <i class="ri-{{ $t->type === 'deposit' ? 'arrow-down-line' : 'arrow-up-line' }}"></i>
                </div>
                <div class="trx-info">
                    <div class="trx-type">{{ $t->type }}</div>
                    <div class="trx-id">{{ $t->trx_id }}</div>
                </div>
                <div class="trx-amount">
                    <div class="amount-value text-gold">${{ number_format($t->amount, 2) }}</div>
                    <div class="amount-date">{{ \Carbon\Carbon::parse($t->created_at)->format('d M, H:i') }}</div>
                </div>
            </div>
            <div class="trx-bottom">
                <div class="trx-address">
                    <i class="ri-wallet-3-line mr-1 opacity-50" style="margin-right: 4px;"></i> 
                    {{ $t->address ?? 'System Balance' }}
                </div>
                
                @php
                    $statusClass = [
                        'success' => 'status-success',
                        'confirmed' => 'status-success',
                        'pending' => 'status-pending',
                        'failed' => 'status-failed',
                        'cancelled' => 'status-failed',
                    ][strtolower($t->status)] ?? 'status-pending';
                @endphp
                <div class="status-badge {{ $statusClass }}">
                    {{ strtoupper($t->status) }}
                </div>
            </div>
        </div>
        @empty
        <div class="glass-panel empty-state anime-fade">
            <div class="empty-icon"><i class="ri-bubble-chart-line"></i></div>
            <div class="empty-text">No transactions found in this period.</div>
        </div>
        @endforelse
    </div>
</div>

<script>
    function viewDetails(trxId) {
        if (typeof toastr !== 'undefined') {
            toastr.info('Audit trail for ' + trxId + ' is being generated...');
        } else {
            alert('Audit trail for ' + trxId + ' is being generated...');
        }
    }
    
    document.getElementById('ledger-search').addEventListener('input', function() {
        let val = this.value.toLowerCase();
        let items = document.querySelectorAll('#ledger-body .trx-item');
        items.forEach(item => {
            let text = item.innerText.toLowerCase();
            item.style.display = text.includes(val) ? 'flex' : 'none';
        });
    });

    // Staggered Entrance Animation
    window.addEventListener('load', () => {
        if (typeof anime !== 'undefined') {
            anime({
                targets: '.anime-card',
                translateY: [20, 0],
                opacity: [0, 1],
                delay: anime.stagger(100),
                easing: 'easeOutExpo',
                duration: 800
            });

            anime({
                targets: '.anime-fade',
                opacity: [0, 1],
                translateY: [10, 0],
                delay: 400,
                easing: 'easeOutExpo',
                duration: 800
            });
            
            anime({
                targets: '.anime-item',
                translateX: [20, 0],
                opacity: [0, 1],
                delay: anime.stagger(80, {start: 500}),
                easing: 'easeOutExpo',
                duration: 800
            });
        }
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
@endsection
