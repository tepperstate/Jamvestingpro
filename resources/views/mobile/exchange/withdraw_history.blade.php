@extends('layouts.user.app')
@section("content")
<style>
    .outfit { font-family: 'Outfit', sans-serif !important; }
    
    /* Mobile-First Premium Dark Theme */
    body {
        background-color: #0b0e14 !important;
        color: #ffffff;
    }

    .mobile-wrapper {
        min-height: 100vh;
        padding-bottom: 80px;
    }

    .header-section {
        padding: 30px 20px 20px;
        background: linear-gradient(180deg, rgba(153, 0, 0, 0.08) 0%, rgba(11, 14, 20, 0) 100%);
        border-bottom: 1px solid rgba(153, 0, 0, 0.1);
        margin-bottom: 24px;
        position: relative;
    }

    .header-section::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        width: 100%;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(153, 0, 0, 0.3), transparent);
    }

    .page-title {
        font-family: 'Outfit', sans-serif;
        font-size: 1.75rem;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 12px;
        letter-spacing: 0.5px;
    }
    
    .page-subtitle {
        color: #94a3b8;
        font-size: 0.9rem;
        margin: 0;
        font-weight: 300;
    }

    .glass-card {
        background: rgba(18, 22, 31, 0.65);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-top: 1px solid rgba(153, 0, 0, 0.2); /* Gold Accent */
        border-radius: 20px;
        margin-bottom: 20px;
        padding: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .glass-card:active {
        transform: scale(0.98);
    }

    .card-header-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        padding-bottom: 16px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .asset-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .asset-icon-wrapper {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: linear-gradient(135deg, rgba(0, 0, 0, 0.6), rgba(30, 30, 40, 0.8));
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(153, 0, 0, 0.25);
        box-shadow: inset 0 0 10px rgba(153, 0, 0, 0.05);
    }

    .asset-name {
        font-weight: 600;
        font-size: 1.15rem;
        color: #f8fafc;
        margin: 0;
        line-height: 1.2;
    }
    
    .sequence-no {
        font-size: 0.75rem;
        color: #64748b;
        font-family: monospace;
        letter-spacing: 0.5px;
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    
    .status-pending {
        background: rgba(153, 0, 0, 0.15);
        color: #990000; /* Gold */
        border: 1px solid rgba(153, 0, 0, 0.3);
    }
    
    .status-completed {
        background: rgba(255, 51, 51, 0.15);
        color: #4ade80;
        border: 1px solid rgba(255, 51, 51, 0.3);
    }

    .card-body-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .data-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .data-label {
        font-size: 0.75rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 500;
    }

    .data-value {
        font-size: 1rem;
        color: #e2e8f0;
        font-weight: 500;
    }

    .data-value.amount {
        color: #990000; /* Gold */
        font-weight: 700;
        font-size: 1.2rem;
        text-shadow: 0 2px 10px rgba(153, 0, 0, 0.2);
    }

    .hash-link {
        color: #990000;
        text-decoration: none;
        font-family: monospace;
        font-size: 0.85rem;
        background: rgba(153, 0, 0, 0.08);
        padding: 6px 12px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid rgba(153, 0, 0, 0.15);
        transition: all 0.2s ease;
    }
    
    .hash-link:active {
        background: rgba(153, 0, 0, 0.15);
    }

    .empty-state {
        text-align: center;
        padding: 50px 20px;
        background: rgba(18, 22, 31, 0.5);
        border-radius: 20px;
        border: 1px dashed rgba(153, 0, 0, 0.2);
        margin-top: 20px;
    }
    
    .empty-icon {
        font-size: 3.5rem;
        color: rgba(153, 0, 0, 0.4);
        margin-bottom: 16px;
        display: inline-block;
    }
</style>

<div class="mobile-wrapper outfit">
    <div class="header-section">
        <h2 class="page-title">
            <i class="ri-history-line" style="color: #990000;"></i>
            Withdrawals
        </h2>
        <p class="page-subtitle">Your capital dispatch ledger</p>
    </div>

    <div class="container px-3">
        @forelse ($data as $key => $val)
            <div class="glass-card">
                <div class="card-header-flex">
                    <div class="asset-info">
                        <div class="asset-icon-wrapper">
                            <x-asset-logo :symbol="$val->type" size="20" />
                        </div>
                        <div>
                            <h3 class="asset-name">{{ $val->type }}</h3>
                            <span class="sequence-no">#{{ str_pad(++$key, 4, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    </div>
                    <div>
                        <span class="status-badge {{ strtolower($val->status) === 'pending' ? 'status-pending' : 'status-completed' }}">
                            {{ $val->status }}
                        </span>
                    </div>
                </div>
                
                <div class="card-body-grid">
                    <div class="data-group">
                        <span class="data-label">Volume (USD)</span>
                        <span class="data-value amount">${{ number_format($val->amount, 2) }}</span>
                    </div>
                    <div class="data-group" style="text-align: right;">
                        <span class="data-label">Timestamp</span>
                        <span class="data-value" style="font-size: 0.9rem;">
                            {{ $val->created_at->format('M d, Y') }}<br>
                            <span style="color:#94a3b8; font-size: 0.8rem;">{{ $val->created_at->format('H:i') }}</span>
                        </span>
                    </div>
                    <div class="data-group" style="grid-column: span 2; margin-top: 4px;">
                        <span class="data-label">Registry Hash</span>
                        <div class="data-value mt-1">
                            @if($val->hash)
                                <a href="{{ str_contains($val->hash, 'http') ? $val->hash : '//' . $val->hash }}" target="_blank" class="hash-link">
                                    <i class="ri-links-line"></i> {{ substr($val->hash, 0, 18) }}...
                                </a>
                            @else
                                <span class="text-secondary opacity-50 italic small">Pending Registry...</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="ri-inbox-line empty-icon"></i>
                <h4 class="text-white mb-2 font-weight-bold">No History Found</h4>
                <p class="text-secondary small mb-0">You have no withdrawal records in the ledger yet.</p>
            </div>
        @endforelse
    </div>
</div>

@if(session('status'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if(typeof toastr !== 'undefined') {
            toastr.success("{{session('status')}}", 'successful');
        }
    });
</script>
@endif
@endsection
