@extends('layouts.user.app')

@section('title', 'Withdraw Funds')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

<style>
    :root {
        --gold-primary: #FFD700;
        --gold-secondary: #F59E0B;
        --glass-bg: rgba(20, 20, 20, 0.6);
        --glass-border: rgba(255, 215, 0, 0.15);
        --glass-highlight: rgba(255, 215, 0, 0.05);
    }

    body {
        background-color: #0f0f0f;
        color: #fff;
    }

    .glass-card-mobile {
        background: var(--glass-bg);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease;
    }

    .glass-card-mobile:active {
        transform: scale(0.98);
        border-color: rgba(255, 215, 0, 0.4);
    }

    .wd-icon-gold {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.2), rgba(245, 158, 11, 0.05));
        color: var(--gold-primary);
        border: 1px solid rgba(255, 215, 0, 0.2);
        box-shadow: inset 0 0 10px rgba(255, 215, 0, 0.1);
    }

    .badge-gold {
        font-size: 0.6rem;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 12px;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        border: 1px solid rgba(255, 215, 0, 0.3);
        background: rgba(255, 215, 0, 0.1);
        color: var(--gold-primary);
    }

    .glass-pill-mobile {
        background: var(--glass-highlight);
        border: 1px solid var(--glass-border);
        backdrop-filter: blur(10px);
        border-radius: 30px;
        color: var(--gold-primary);
    }

    .text-gold {
        color: var(--gold-primary) !important;
    }

    .balance-glow {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 150px;
        height: 150px;
        background: radial-gradient(circle, rgba(255, 215, 0, 0.15) 0%, transparent 70%);
        z-index: 0;
        pointer-events: none;
    }
</style>

<div class="container-fluid py-4 px-3" data-aos="fade-in">
    <!-- Header Area -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="outfit font-weight-bold mb-0 text-white" style="font-size: 1.75rem;">Withdraw</h2>
            <p class="small mb-0 text-white-50">Select payout method</p>
        </div>
        <a href="{{ route('withdraw.history') }}" class="glass-pill-mobile px-3 py-2 text-decoration-none small font-weight-bold d-flex align-items-center">
            <i class="ri-history-line me-1"></i> Ledger
        </a>
    </div>

    <!-- Balance Card -->
    <div class="glass-card-mobile p-4 mb-4 position-relative overflow-hidden text-center">
        <div class="balance-glow"></div>
        <div class="position-relative z-index-1">
            <div class="text-white-50 small tracking-widest uppercase font-weight-bold mb-1" style="font-size: 0.7rem; letter-spacing: 2px;">Available Balance</div>
            <div class="h1 outfit font-weight-bold text-white mb-3" style="font-size: 2.8rem; letter-spacing: -1px;">${{ number_format(auth()->user()->balance->amount ?? 0, 2) }}</div>
            
            <div class="d-flex justify-content-between align-items-center px-2 py-2" style="background: rgba(0,0,0,0.3); border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">
                <div class="text-center w-50">
                    <div class="text-white-50 x-small uppercase tracking-wider mb-1" style="font-size: 0.6rem;">Net Profit</div>
                    <div class="h5 outfit font-weight-bold text-gold mb-0">${{ number_format(auth()->user()->balance->profit ?? 0, 2) }}</div>
                </div>
                <div style="width: 1px; height: 30px; background: rgba(255,255,255,0.1);"></div>
                <div class="text-center w-50">
                    <div class="text-white-50 x-small uppercase tracking-wider mb-1" style="font-size: 0.6rem;">Bonus</div>
                    <div class="h5 outfit font-weight-bold text-white mb-0">${{ number_format(auth()->user()->balance->bonus ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payout Methods -->
    <h6 class="text-white-50 uppercase tracking-wider mb-3 font-weight-bold" style="font-size: 0.75rem; letter-spacing: 1px;">Withdrawal Methods</h6>
    <div class="d-flex flex-column gap-3 mb-4">
        
        <!-- Crypto -->
        <div class="glass-card-mobile p-3" data-toggle="modal" data-target="#cryptoModal">
            <div class="d-flex align-items-center">
                <div class="wd-icon-gold me-3">
                    <i class="ri-bit-coin-fill"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h5 class="outfit font-weight-bold mb-0 text-white" style="font-size: 1.1rem;">Digital Assets</h5>
                        <span class="badge-gold">Instant</span>
                    </div>
                    <p class="text-white-50 small mb-0" style="font-size: 0.8rem;">Global crypto addresses</p>
                </div>
                <div class="ms-2">
                    <i class="ri-arrow-right-s-line text-gold" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>

        <!-- Bank -->
        <div class="glass-card-mobile p-3" data-toggle="modal" data-target="#bankModal">
            <div class="d-flex align-items-center">
                <div class="wd-icon-gold me-3" style="filter: hue-rotate(330deg);">
                    <i class="ri-bank-fill"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h5 class="outfit font-weight-bold mb-0 text-white" style="font-size: 1.1rem;">Fiat Settlement</h5>
                        <span class="badge-gold" style="color: #e2e8f0; border-color: rgba(226, 232, 240, 0.3); background: rgba(226, 232, 240, 0.1);">1-3 Days</span>
                    </div>
                    <p class="text-white-50 small mb-0" style="font-size: 0.8rem;">Direct wire transfer</p>
                </div>
                <div class="ms-2">
                    <i class="ri-arrow-right-s-line text-gold" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>

        <!-- Transfer -->
        <div class="glass-card-mobile p-3" data-toggle="modal" data-target="#transferModal">
            <div class="d-flex align-items-center">
                <div class="wd-icon-gold me-3" style="filter: hue-rotate(280deg);">
                    <i class="ri-user-shared-fill"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h5 class="outfit font-weight-bold mb-0 text-white" style="font-size: 1.1rem;">Internal Vault</h5>
                        <span class="badge-gold" style="color: #a78bfa; border-color: rgba(167, 139, 250, 0.3); background: rgba(167, 139, 250, 0.1);">Zero Fee</span>
                    </div>
                    <p class="text-white-50 small mb-0" style="font-size: 0.8rem;">P2P Instant Transfer</p>
                </div>
                <div class="ms-2">
                    <i class="ri-arrow-right-s-line text-gold" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>

    </div>

    <!-- Security Info -->
    <div class="glass-card-mobile p-3 mb-4 border-0" style="background: rgba(255, 215, 0, 0.03);">
        <div class="d-flex align-items-start gap-3 mb-3">
            <div class="text-success mt-1"><i class="ri-shield-check-fill" style="font-size: 1.25rem;"></i></div>
            <div>
                <div class="text-white font-weight-bold" style="font-size: 0.9rem;">Bank-Grade Security</div>
                <p class="text-white-50 mb-0" style="font-size: 0.75rem; line-height: 1.4;">Secured by AES-256 encryption and multi-factor consensus protocols.</p>
            </div>
        </div>
        <div class="d-flex align-items-start gap-3">
            <div class="text-gold mt-1"><i class="ri-time-fill" style="font-size: 1.25rem;"></i></div>
            <div>
                <div class="text-white font-weight-bold" style="font-size: 0.9rem;">Compliance Review</div>
                <p class="text-white-50 mb-0" style="font-size: 0.75rem; line-height: 1.4;">Typical processing period: 1-4 hours.</p>
            </div>
        </div>
    </div>
</div>

@include('exchange.modals.withdrawal_types')

@endsection
