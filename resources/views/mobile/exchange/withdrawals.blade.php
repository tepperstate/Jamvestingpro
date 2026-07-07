@extends('layouts.user.app')

@section('title', 'Withdrawal History')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

<style>
    :root {
        --gold-primary: #990000;
        --gold-light: #F3E5AB;
        --gold-dark: #AA8000;
        --glass-bg: rgba(20, 22, 31, 0.65);
        --glass-border: rgba(153, 0, 0, 0.15);
        --glass-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
    }
    
    body {
        background-color: #0B0E14;
        color: #ffffff;
    }

    .mobile-container {
        padding: 16px;
        padding-bottom: 80px;
        font-family: 'Inter', sans-serif;
    }

    .outfit {
        font-family: 'Outfit', sans-serif;
    }

    /* Glassmorphism Classes */
    .glass-panel {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        box-shadow: var(--glass-shadow);
    }

    /* Header Section */
    .header-section {
        margin-bottom: 24px;
        position: relative;
    }
    
    .header-section h2 {
        font-size: 1.75rem;
        background: linear-gradient(135deg, #fff 0%, var(--gold-light) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 4px;
    }

    .btn-gold {
        background: linear-gradient(135deg, var(--gold-primary) 0%, var(--gold-dark) 100%);
        color: #000;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        padding: 14px 20px;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 15px rgba(153, 0, 0, 0.2);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-gold:active {
        transform: scale(0.98);
    }

    /* Stats Cards */
    .stats-scroll {
        display: flex;
        gap: 12px;
        overflow-x: auto;
        padding-bottom: 8px;
        margin-bottom: 24px;
        scrollbar-width: none;
    }
    
    .stats-scroll::-webkit-scrollbar {
        display: none;
    }

    .stat-card {
        min-width: 150px;
        padding: 16px;
        flex-shrink: 0;
        position: relative;
        overflow: hidden;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, var(--gold-primary), transparent);
        opacity: 0.3;
    }

    .stat-label {
        font-size: 0.75rem;
        color: #8B92A5;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 1.25rem;
        font-weight: 700;
    }

    /* List Cards */
    .tx-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .tx-card {
        padding: 16px;
        transition: transform 0.2s ease;
        position: relative;
    }
    
    .tx-card::after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 20px;
        padding: 1px;
        background: linear-gradient(135deg, rgba(153,0,0,0.2), rgba(255,255,255,0.02));
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        pointer-events: none;
    }

    .tx-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .tx-icon-wrap {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: rgba(153, 0, 0, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--gold-primary);
        font-size: 1.2rem;
    }

    .tx-info h4 {
        margin: 0;
        font-size: 1rem;
        font-weight: 600;
        color: #fff;
    }

    .tx-info p {
        margin: 0;
        font-size: 0.75rem;
        color: #8B92A5;
    }

    .tx-amount {
        text-align: right;
    }

    .tx-amount .val {
        font-size: 1.1rem;
        font-weight: 700;
        color: #fff;
        margin-bottom: 2px;
    }

    .tx-amount .status {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    .status-confirmed { color: #ff3333; }
    .status-pending { color: var(--gold-primary); }
    .status-cancelled, .status-failed { color: #EF4444; }

    .tx-details {
        padding-top: 12px;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    
    .detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.8rem;
    }
    
    .detail-label { color: #8B92A5; }
    .detail-val { 
        color: #E2E8F0;
        max-width: 60%;
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
    }

    .empty-state {
        padding: 40px 20px;
        text-align: center;
        color: #8B92A5;
    }
    
    .empty-state i {
        font-size: 3rem;
        color: rgba(153, 0, 0, 0.3);
        margin-bottom: 16px;
    }
</style>

<div class="mobile-container">
    <div class="header-section">
        <h2 class="outfit font-weight-bold">Electronic Ledger</h2>
        <p class="text-secondary small">Monitor your transaction history and settlement status.</p>
    </div>

    <button class="btn-gold mb-4" data-toggle="modal" data-target="#cryptoModal">
        <i class="ri-add-circle-line"></i> NEW WITHDRAWAL
    </button>

    <div class="stats-scroll">
        <div class="glass-panel stat-card">
            <div class="stat-label">Lifetime Withdrawals</div>
            <div class="stat-value outfit" style="color: #EF4444;">-${{ number_format($data->where('status', 'confirmed')->sum('amount'), 2) }}</div>
        </div>
        <div class="glass-panel stat-card">
            <div class="stat-label">Pending Processing</div>
            <div class="stat-value outfit" style="color: var(--gold-primary);">${{ number_format($data->where('status', 'pending')->sum('amount'), 2) }}</div>
        </div>
        <div class="glass-panel stat-card">
            <div class="stat-label">Avg Payout Time</div>
            <div class="stat-value outfit" style="color: #3B82F6;">1.2h</div>
        </div>
    </div>

    <div class="tx-list">
        <h6 class="text-secondary mb-2 outfit" style="font-size: 0.85rem; letter-spacing: 0.5px;">RECENT TRANSACTIONS</h6>
        
        @forelse($data as $w)
            @php
                $statusLower = strtolower($w->status);
                $statusClassText = [
                    'confirmed' => 'status-confirmed',
                    'pending' => 'status-pending',
                    'cancelled' => 'status-cancelled',
                    'failed' => 'status-failed',
                ][$statusLower] ?? 'text-secondary';
                
                $iconClass = 'ri-arrow-right-up-line';
                if($statusLower === 'pending') $iconClass = 'ri-time-line';
                if($statusLower === 'cancelled' || $statusLower === 'failed') $iconClass = 'ri-close-circle-line';
            @endphp
            
            <div class="glass-panel tx-card">
                <div class="tx-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="tx-icon-wrap">
                            <i class="{{ $iconClass }}"></i>
                        </div>
                        <div class="tx-info">
                            <h4 class="outfit">{{ strtoupper($w->type) }}</h4>
                            <p>{{ \Carbon\Carbon::parse($w->created_at)->format('d M, Y H:i') }}</p>
                        </div>
                    </div>
                    <div class="tx-amount">
                        <div class="val outfit">-${{ number_format($w->amount, 2) }}</div>
                        <div class="status {{ $statusClassText }}">{{ strtoupper($w->status) }}</div>
                    </div>
                </div>
                
                <div class="tx-details">
                    <div class="detail-row">
                        <span class="detail-label">Reference</span>
                        <span class="detail-val" style="color: var(--gold-primary);">{{ $w->trx_id }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Destination</span>
                        <span class="detail-val">{{ $w->address }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="glass-panel empty-state">
                <i class="ri-file-list-3-line"></i>
                <h5 class="outfit text-white mb-2">No Withdrawals Found</h5>
                <p class="mb-0 text-sm">Your transaction history is currently empty.</p>
            </div>
        @endforelse
    </div>
</div>

@include('exchange.modals.withdrawal_types')
@endsection
