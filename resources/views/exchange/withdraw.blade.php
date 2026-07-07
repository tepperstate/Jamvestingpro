@extends('layouts.user.app')

@section('title', 'Withdraw Funds')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

<div class="container py-4" data-aos="fade-up">
    <div class="row justify-content-center">
        <div class="col-xl-11">
            <div class="d-flex justify-content-between align-items-end mb-4 px-2">
                <div>
                    <h2 class="outfit font-weight-bold mb-1">Withdraw Funds</h2>
                    <p class="text-secondary small mb-0">Send your funds to your bank or crypto wallet.</p>
                </div>
                <a href="{{ route('withdraw.history') }}" class="glass-pill px-4 py-2 text-decoration-none small font-weight-bold text-primary" style="background: rgba(14, 165, 233, 0.05); border: 1px solid rgba(14, 165, 233, 0.1);">
                    <i class="ri-history-line me-1"></i> VIEW HISTORY
                </a>
            </div>

<style>
    :root {
        --accent-primary: #0ea5e9;
        --glass-bg: rgba(0, 0, 0, 0.4);
        --glass-border: rgba(255, 255, 255, 0.08);
    }

    .glass-card {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 24px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .bento-wd-item {
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .bento-wd-item:hover {
        transform: translateY(-8px);
        background: rgba(0, 0, 0, 0.6);
        border-color: var(--accent-primary);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4), 0 0 20px rgba(14, 165, 233, 0.1);
    }

    .wd-icon-wrapper {
        width: 64px;
        height: 64px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        position: relative;
        z-index: 1;
        transition: transform 0.4s ease;
    }

    .bento-wd-item:hover .wd-icon-wrapper {
        transform: scale(1.1) rotate(5deg);
    }

    .icon-glow {
        position: absolute;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        filter: blur(30px);
        opacity: 0.2;
        z-index: -1;
        transition: opacity 0.4s ease;
    }

    .bento-wd-item:hover .icon-glow {
        opacity: 0.4;
    }

    .badge-premium {
        font-size: 0.65rem;
        font-weight: 800;
        padding: 5px 12px;
        border-radius: 20px;
        letter-spacing: 1px;
        text-transform: uppercase;
        border: 1px solid rgba(255,255,255,0.1);
        background: rgba(255,255,255,0.05);
    }

    .glass-pill {
        backdrop-filter: blur(10px);
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .glass-pill:hover {
        background: rgba(14, 165, 233, 0.1) !important;
        transform: scale(1.05);
    }
</style>

<div class="container py-4" data-aos="fade-up">
    <div class="row justify-content-center">
        <div class="col-xl-11">
            <div class="d-flex justify-content-between align-items-end mb-5 px-2">
                <div>
                    <h2 class="outfit font-weight-bold mb-1 text-white" style="letter-spacing: -0.5px;">Withdraw Funds</h2>
                    <p class="text-secondary small mb-0 opacity-75">Send your funds to your bank or crypto wallet.</p>
                </div>
                <a href="{{ route('withdraw.history') }}" class="glass-pill px-4 py-2 text-decoration-none small font-weight-bold text-primary d-flex align-items-center" style="background: rgba(14, 165, 233, 0.05); border: 1px solid rgba(14, 165, 233, 0.1);">
                    <i class="ri-history-line me-2"></i> VIEW LEDGER
                </a>
            </div>

            <!-- Enhanced Bento Grid for Modern Payouts -->
            <div class="row g-4 mb-5">
                <!-- Card 1: Crypto -->
                <div class="col-lg-4 col-md-6">
                    <div class="glass-card p-4 h-100 bento-wd-item d-flex flex-column" data-toggle="modal" data-target="#cryptoModal">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div class="wd-icon-wrapper" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                                <div class="icon-glow" style="background: #f59e0b;"></div>
                                <i class="ri-bit-coin-fill"></i>
                            </div>
                            <span class="badge-premium" style="color: #f59e0b; border-color: rgba(245, 158, 11, 0.2);">INSTANT</span>
                        </div>
                        <h4 class="outfit font-weight-bold mb-2 text-white">Digital Assets</h4>
                        <p class="text-secondary small mb-4 opacity-75">Withdraw to global crypto addresses with lightning speed.</p>
                        <div class="mt-auto d-flex justify-content-between align-items-center pt-3 border-top border-secondary border-opacity-10">
                            <span class="small font-weight-bold text-muted uppercase tracking-wider" style="font-size: 0.6rem;">GAS FEES: MINIMAL</span>
                            <i class="ri-arrow-right-up-line text-primary"></i>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Bank -->
                <div class="col-lg-4 col-md-6">
                    <div class="glass-card p-4 h-100 bento-wd-item d-flex flex-column" data-toggle="modal" data-target="#bankModal">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div class="wd-icon-wrapper" style="background: rgba(6, 182, 212, 0.1); color: #06b6d4;">
                                <div class="icon-glow" style="background: #06b6d4;"></div>
                                <i class="ri-bank-fill"></i>
                            </div>
                            <span class="badge-premium" style="color: #06b6d4; border-color: rgba(6, 182, 212, 0.2);">SWIFT/SEPA</span>
                        </div>
                        <h4 class="outfit font-weight-bold mb-2 text-white">Fiat Settlement</h4>
                        <p class="text-secondary small mb-4 opacity-75">Direct wire transfer to your verified bank account.</p>
                        <div class="mt-auto d-flex justify-content-between align-items-center pt-3 border-top border-secondary border-opacity-10">
                            <span class="small font-weight-bold text-muted uppercase tracking-wider" style="font-size: 0.6rem;">ETA: 1-3 BUSINESS DAYS</span>
                            <i class="ri-arrow-right-up-line text-primary"></i>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Transfer -->
                <div class="col-lg-4 col-md-6 mx-auto">
                    <div class="glass-card p-4 h-100 bento-wd-item d-flex flex-column" data-toggle="modal" data-target="#transferModal">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div class="wd-icon-wrapper" style="background: rgba(99, 102, 241, 0.1); color: #6366f1;">
                                <div class="icon-glow" style="background: #6366f1;"></div>
                                <i class="ri-user-shared-fill"></i>
                            </div>
                            <span class="badge-premium" style="color: #6366f1; border-color: rgba(99, 102, 241, 0.2);">P2P ZERO-FEE</span>
                        </div>
                        <h4 class="outfit font-weight-bold mb-2 text-white">Internal Vault</h4>
                        <p class="text-secondary small mb-4 opacity-75">Transfer assets instantly to other authenticated platform users.</p>
                        <div class="mt-auto d-flex justify-content-between align-items-center pt-3 border-top border-secondary border-opacity-10">
                            <span class="small font-weight-bold text-muted uppercase tracking-wider" style="font-size: 0.6rem;">NETWORK: INTRA-LEDGER</span>
                            <i class="ri-arrow-right-up-line text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security & Status Info -->
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="glass-card p-4 h-100">
                        <h5 class="outfit font-weight-bold mb-4 text-white">Policy Enforcement</h5>
                        <div class="d-flex gap-3 mb-4 p-3 rounded-4" style="background: rgba(255, 51, 51, 0.05); border: 1px solid rgba(255, 51, 51, 0.1);">
                            <div class="text-success mt-1"><i class="ri-shield-check-fill" style="font-size: 1.5rem;"></i></div>
                            <div>
                                <div class="text-white font-weight-bold small">Bank-Grade Encryption</div>
                                <p class="text-secondary x-small mb-0 opacity-75" style="line-height: 1.5;">All withdrawals are secured by AES-256 multi-layer encryption and multi-factor consensus protocols.</p>
                            </div>
                        </div>
                        <div class="d-flex gap-3 p-3 rounded-4" style="background: rgba(245, 158, 11, 0.05); border: 1px solid rgba(245, 158, 11, 0.1);">
                            <div class="text-warning mt-1"><i class="ri-time-fill" style="font-size: 1.5rem;"></i></div>
                            <div>
                                <div class="text-white font-weight-bold small">Compliance Review Period</div>
                                <p class="text-secondary x-small mb-0 opacity-75" style="line-height: 1.5;">Typical processing period: 1-4 hours. Large withdrawals may require additional review.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="glass-card p-5 h-100 text-center d-flex flex-column justify-content-center align-items-center relative overflow-hidden">
                        <div class="absolute" style="top: -50px; right: -50px; width: 150px; height: 150px; background: radial-gradient(circle, rgba(14, 165, 233, 0.1) 0%, transparent 70%);"></div>
                        <div class="mb-4">
                            <div class="h1 outfit font-weight-bold text-white mb-1" style="letter-spacing: -2px; font-size: 3.5rem;">${{ number_format(auth()->user()->balance->amount ?? 0, 2) }}</div>
                            <div class="text-secondary small tracking-widest uppercase font-weight-bold" style="font-size: 0.7rem; opacity: 0.6; letter-spacing: 2px;">Available Balance</div>
                        </div>
                        <div class="d-flex gap-5 mt-4 justify-content-center w-100">
                             <div class="px-2">
                                 <div class="h4 outfit font-weight-bold text-success mb-0">${{ number_format(auth()->user()->balance->profit ?? 0, 2) }}</div>
                                 <div class="text-secondary x-small uppercase tracking-wider" style="font-size: 0.6rem; font-weight: 700; opacity: 0.5;">Net Profit</div>
                             </div>
                             <div style="width: 1px; height: 40px; background: linear-gradient(to bottom, transparent, rgba(255,255,255,0.1), transparent);"></div>
                             <div class="px-2">
                                 <div class="h4 outfit font-weight-bold text-info mb-0">${{ number_format(auth()->user()->balance->bonus ?? 0, 2) }}</div>
                                 <div class="text-secondary x-small uppercase tracking-wider" style="font-size: 0.6rem; font-weight: 700; opacity: 0.5;">Bonus Rewards</div>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('exchange.modals.withdrawal_types')

@endsection

