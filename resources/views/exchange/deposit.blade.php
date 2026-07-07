@extends('layouts.user.app')

@section('title', 'Deposit Funds')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-xl-9 col-lg-10">
            <!-- Progress Steps -->
            <div class="d-flex justify-content-center mb-5 gap-2 gap-md-4 progress-steps-mobile">
                <div class="step-item active" id="step1-indicator">
                    <div class="step-num">1</div>
                    <div class="step-label">Select</div>
                </div>
                <div class="step-line"></div>
                <div class="step-item" id="step2-indicator">
                    <div class="step-num">2</div>
                    <div class="step-label">Transfer</div>
                </div>
                <div class="step-line"></div>
                <div class="step-item" id="step3-indicator">
                    <div class="step-num">3</div>
                    <div class="step-label">Verify</div>
                </div>
            </div>
            <style>
                .step-item { display: flex; flex-direction: column; align-items: center; gap: 0.5rem; opacity: 0.4; transition: all 0.3s; }
                .step-item.active { opacity: 1; }
                .step-num { width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; font-weight: bold; border: 1px solid rgba(255,255,255,0.1); }
                .step-item.active .step-num { background: var(--accent-primary); border-color: var(--accent-primary); box-shadow: 0 0 20px rgba(59, 130, 246, 0.4); color: white; }
                .step-label { font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; }
                .step-item.active .step-label { color: white; }
                .step-line { width: 50px; height: 2px; background: rgba(255,255,255,0.1); align-self: center; margin-bottom: 25px; }
                @media (max-width: 576px) {
                    .progress-steps-mobile .step-label { font-size: 0.6rem; }
                    .progress-steps-mobile .step-num { width: 30px; height: 30px; font-size: 0.8rem; }
                    .progress-steps-mobile .step-line { margin-bottom: 20px; width: 30px; }
                }
            </style>

            <!-- Step 1: Select Method -->
            <div id="step-1" class="deposit-step">
                <div class="text-center mb-5">
                    <h2 class="outfit font-weight-bold">How would you like to fund?</h2>
                    <p class="text-secondary">Choose your preferred payment method to continue.</p>
                </div>

                @if($payment_settings->is_manual_crypto_enabled)
                <div class="row g-4 g-md-5 justify-content-center mb-5" data-aos="fade-up">
                    <div class="col-12"><h6 class="text-uppercase small text-secondary fw-bold letter-spacing-2 mb-3">Manual Crypto Deposit</h6></div>
                    @php
                        $coinColors = [
                            'BTC' => 'rgba(247, 147, 26, 0.15)',
                            'ETH' => 'rgba(98, 126, 234, 0.15)',
                            'USDT' => 'rgba(38, 161, 123, 0.15)',
                            'SOL' => 'rgba(20, 241, 149, 0.15)',
                            'BNB' => 'rgba(243, 186, 47, 0.15)',
                            'DOGE' => 'rgba(186, 159, 51, 0.15)',
                            'XMR' => 'rgba(255, 102, 0, 0.15)',
                            'TRX' => 'rgba(235, 0, 41, 0.15)',
                        ];
                        $coinGlows = [
                            'BTC' => 'rgba(247, 147, 26, 0.3)',
                            'ETH' => 'rgba(98, 126, 234, 0.3)',
                            'USDT' => 'rgba(38, 161, 123, 0.3)',
                            'SOL' => 'rgba(20, 241, 149, 0.3)',
                            'BNB' => 'rgba(243, 186, 47, 0.3)',
                            'DOGE' => 'rgba(186, 159, 51, 0.3)',
                            'XMR' => 'rgba(255, 102, 0, 0.3)',
                            'TRX' => 'rgba(235, 0, 41, 0.3)',
                        ];
                    @endphp
                    @foreach($admin_wallets as $index => $wallet)
                        @php
                            $symbol = strtoupper($wallet->symbol);
                            $bgColor = $coinColors[$symbol] ?? 'rgba(59, 130, 246, 0.15)';
                            $glowColor = $coinGlows[$symbol] ?? 'rgba(59, 130, 246, 0.25)';
                            $logoUrl = app(\App\Services\AssetLogoService::class)->getLogoUrl($wallet->symbol);
                        @endphp
                        <div class="col-6 col-md-4 mb-3" data-aos="zoom-in-up" data-aos-delay="{{ 50 * ($index + 1) }}">
                            <div class="method-card glass-card p-4 text-center h-100 d-flex flex-column align-items-center justify-content-center" onclick="selectMethod('{{ $wallet->symbol }}', '{{ $wallet->name }}', '{{ $wallet->address }}', '{{ $wallet->qr_code }}')">
                                <div class="method-icon-container mb-3" style="background: {{ $bgColor }}; box-shadow: 0 0 20px {{ $glowColor }}, inset 0 0 10px rgba(255,255,255,0.05);">
                                    <div class="coin-glow" style="background: radial-gradient(circle, {{ $glowColor }} 0%, transparent 70%);"></div>
                                    <img src="{{ $logoUrl }}" 
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" 
                                         class="coin-logo-premium"
                                         alt="{{ $wallet->symbol }}" style="width: 42px; height: 42px;">
                                    <div class="coin-fallback-premium outfit" style="display: none; width: 42px; height: 42px; align-items: center; justify-content: center; background: rgba(255,255,255,0.05); border-radius: 50%; font-size: 0.9rem; font-weight: 800; color: white; border: 1px solid rgba(255,255,255,0.1);">
                                        {{ substr($wallet->symbol, 0, 1) }}
                                    </div>
                                </div>
                                <h6 class="outfit font-weight-bold mb-1 text-white">{{ $wallet->name }}</h6>
                                <p class="text-secondary x-small mb-0 opacity-75 fw-bold">{{ $wallet->network ?? ($wallet->symbol . ' Network') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                @endif

                @if($payment_settings->is_nowpayments_enabled || $payment_settings->is_oxapay_enabled)
                <div class="row g-4 g-md-5 justify-content-center mb-5" data-aos="fade-up">
                    <div class="col-12"><h6 class="text-uppercase small text-secondary fw-bold letter-spacing-2 mb-3">Automated Crypto Gateways</h6></div>
                    
                    @if($payment_settings->is_nowpayments_enabled)
                    <div class="col-md-6 mb-3">
                        <div class="method-card glass-card p-4 d-flex align-items-center gap-4 h-100" onclick="$('#nowPaymentsModal').modal('show')">
                            <div class="method-icon-container text-white" style="background: rgba(43, 190, 108, 0.15); box-shadow: 0 0 15px rgba(43, 190, 108, 0.2);">
                                <i class="ri-bit-coin-line" style="font-size: 1.8rem; color: #2bbe6c;"></i>
                            </div>
                            <div class="text-start">
                                <h5 class="outfit font-weight-bold mb-1 text-white">NowPayments Crypto</h5>
                                <p class="text-secondary small mb-0">Pay with 50+ Cryptocurrencies instantly</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($payment_settings->is_oxapay_enabled)
                    <div class="col-md-6 mb-3">
                        <div class="method-card glass-card p-4 d-flex align-items-center gap-4 h-100" onclick="$('#oxaPayModal').modal('show')">
                            <div class="method-icon-container text-white" style="background: rgba(94, 92, 230, 0.15); box-shadow: 0 0 15px rgba(94, 92, 230, 0.2);">
                                <i class="ri-exchange-crypto-line" style="font-size: 1.8rem; color: #5e5ce6;"></i>
                            </div>
                            <div class="text-start">
                                <h5 class="outfit font-weight-bold mb-1 text-white">OxaPay Crypto</h5>
                                <p class="text-secondary small mb-0">Secure web3 crypto payments</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Fiat Category Row -->
                <div class="row g-4 g-md-5 justify-content-center" data-aos="fade-up" data-aos-delay="300">
                    <div class="col-12"><h6 class="text-uppercase small text-secondary fw-bold letter-spacing-2 mb-3">Fiat & Instant</h6></div>
                    <div class="col-md-6">
                        <div class="method-card glass-card p-4 d-flex align-items-center gap-4 h-100" onclick="$('#wireModalMain').modal('show')">
                            <div class="method-icon-container bg-info-soft text-info" style="box-shadow: 0 0 15px rgba(6, 182, 212, 0.2);">
                                <i class="ri-bank-line" style="font-size: 1.8rem;"></i>
                            </div>
                            <div class="text-start">
                                <h5 class="outfit font-weight-bold mb-1 text-white">Bank Wire</h5>
                                <p class="text-secondary small mb-0">Submit request — receive wire instructions</p>
                            </div>
                        </div>
                    </div>
                    @if($payment_settings->is_nowpayments_card_enabled)
                    <div class="col-md-6">
                        <div class="method-card glass-card p-4 d-flex align-items-center gap-4 h-100" onclick="$('#ccDepositModal').modal('show')">
                            <div class="method-icon-container bg-danger-soft text-danger" style="box-shadow: 0 0 15px rgba(239, 68, 68, 0.2);">
                                <i class="ri-bank-card-line" style="font-size: 1.8rem;"></i>
                            </div>
                            <div class="text-start">
                                <h5 class="outfit font-weight-bold mb-1 text-white">Credit / Debit Card</h5>
                                <p class="text-secondary small mb-0">Instant funding via secure gateway</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                </div>
            </div>

            <!-- Step 2: Transfer Funds -->
            <div id="step-2" class="deposit-step d-none">
                <div class="glass-card p-3 p-md-5 text-center" style="box-shadow: inset 0 1px 0 rgba(255,255,255,0.1), 0 10px 30px rgba(0,0,0,0.3);">
                    <div class="position-relative d-flex align-items-center justify-content-center mb-4">
                        <button class="btn btn-sm btn-link text-secondary position-absolute start-0" onclick="goToStep(1)">
                            <i class="ri-arrow-left-line"></i> Back
                        </button>
                        <h4 class="outfit font-weight-bold mb-0">Send <span id="selected-method-name">Bitcoin</span></h4>
                    </div>
                    
                    <div class="qr-container mb-5 mx-auto p-4 bg-white d-inline-block rounded-4" style="border: 4px solid rgba(255,255,255,0.95);">
                        <div id="qr-mount" style="width: 180px; height: 180px;">
                            <!-- QR Code SVG/HTML injected here -->
                        </div>
                    </div>

                    <div class="address-box glass-card bg-dark p-3 mb-5 d-flex align-items-center justify-content-between">
                        <code class="text-light h6 mb-0 text-break text-start" style="font-family: monospace; letter-spacing: 0.5px;" id="wallet-address">Loading...</code>
                        <button class="btn btn-primary btn-sm ms-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;" onclick="copyAddress()">
                            <i class="ri-file-copy-line" style="font-size: 1.2rem;"></i>
                        </button>
                    </div>

                    <div class="alert bg-warning-soft text-warning border border-warning rounded-3 p-3 d-flex align-items-start gap-2 mb-5 text-start">
                        <i class="ri-alert-line mt-1"></i>
                        <div class="small">Please send only <span class="text-white font-weight-bold" id="selected-currency">BTC</span> to this address. Sending any other asset may result in permanent loss.</div>
                    </div>

                    <div class="form-group mb-5 text-start position-relative">
                        <label class="small text-secondary mb-2">Amount to Deposit</label>
                        <div class="position-relative">
                            <input type="number" id="deposit-amount" class="form-control text-start h4 py-2 mb-0 ps-3 pe-5" placeholder="0.00" step="0.00000001" style="background: rgba(0,0,0,0.2); border: 1px solid var(--glass-border); border-radius: 12px; color: var(--accent-gold);">
                            <span class="position-absolute end-0 top-50 translate-middle-y me-3 text-secondary fw-bold" id="deposit-coin-label">BTC</span>
                        </div>
                          <div class="mt-3">
                              <div id="deposit-usd-display">
                                  <div class="d-flex flex-column gap-2 text-start px-4 py-3" style="background: rgba(0,0,0,0.2); border-radius: 8px;">
                                      <div class="d-flex align-items-center gap-2">
                                          <span class="text-muted">≈</span>
                                          <img src="https://flagcdn.com/w40/us.png" style="width: 24px; border-radius: 2px;">
                                          <span class="text-white fw-bold">USD $ 0.00</span>
                                      </div>
                                  </div>
                              </div>
                          </div>
                        <input type="hidden" id="deposit-amount-usd">
                    </div>

                    <button class="btn btn-success btn-lg w-100 py-3 mb-2" style="background: #10b981; border: none; font-weight: 600;" onclick="initiateCryptoDeposit()">
                        Generate Deposit Request
                    </button>
                    <p class="text-secondary small mt-2 mb-0" id="confirmation-estimate">Estimated confirmation time: 10-30 minutes</p>
                </div>
            </div>

            <!-- Step 3: Wait for payment -->
            <div id="step-3" class="deposit-step d-none">
                <div class="glass-card p-4 p-md-5 mx-auto" style="max-width: 500px;">
                    <div class="text-center mb-4">
                        <h4 class="outfit font-weight-bold">Awaiting Payment</h4>
                        <p class="text-secondary">Please send the exact amount below. The system will detect it automatically.</p>
                    </div>

                    <div class="premium-confirm-card p-4 mb-4" style="background: rgba(0, 0, 0, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 20px; box-shadow: inset 0 1px 0 rgba(255,255,255,0.05), 0 10px 25px rgba(0,0,0,0.4);">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3" style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                            <div class="d-flex align-items-center gap-2">
                                <i class="ri-bank-card-2-line text-secondary" style="font-size: 1.1rem;"></i>
                                <span class="text-secondary small font-weight-bold">Deposit Method</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="font-weight-bold text-white outfit" id="confirm-method-name">Bitcoin</span>
                                <span class="badge bg-primary-soft text-primary font-weight-bold" id="confirm-method-symbol">BTC</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3" style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                            <div class="d-flex align-items-center gap-2">
                                <i class="ri-wallet-3-line text-secondary" style="font-size: 1.1rem;"></i>
                                <span class="text-secondary small font-weight-bold">EXACT Amount to Send</span>
                            </div>
                            <span class="font-weight-bold h4 mb-0 text-white outfit text-warning" id="confirm-exact-crypto">0.00</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3" style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                            <div class="d-flex align-items-center gap-2">
                                <i class="ri-money-dollar-circle-line text-secondary" style="font-size: 1.1rem;"></i>
                                <span class="text-secondary small font-weight-bold">Equivalent USD</span>
                            </div>
                            <span class="font-weight-bold text-white outfit" id="confirm-amount-usd">$0.00</span>
                        </div>
                        <div class="d-flex flex-column mb-1">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="ri-map-pin-line text-secondary" style="font-size: 1.1rem;"></i>
                                <span class="text-secondary small font-weight-bold">Address</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <code class="text-light small mb-0 text-break text-start" style="font-family: monospace;" id="confirm-wallet-address">Loading...</code>
                                <button class="btn btn-outline-secondary btn-sm ms-2" onclick="copyAddressFromConfirm()">Copy</button>
                            </div>
                        </div>
                    </div>

                    <div class="alert bg-warning-soft text-warning border border-warning rounded-3 p-3 d-flex align-items-start gap-2 mb-4 text-start">
                        <i class="ri-alert-line mt-1"></i>
                        <div class="small">
                            <strong>CRITICAL:</strong> You MUST send the exact <span id="confirm-exact-crypto-alert">0.00</span> amount shown above. Our automated system uses this exact amount to identify and credit your account. Sending a different amount will result in lost funds.
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <div class="spinner-grow text-success spinner-grow-sm me-2" role="status"></div>
                        <span class="text-secondary small">Scanning blockchain... You may leave this page.</span>
                    </div>
                    
                    <div class="mt-4 text-center">
                         <a href="{{ route('deposit.history') }}" class="btn btn-premium w-100 py-3">View History</a>
                    </div>
                </div>
            </div>
            <!-- Deposit History Section -->
            <div class="mt-5 pt-5" data-aos="fade-up">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="outfit font-weight-bold mb-0">Deposit History</h4>
                    <span class="badge bg-primary-soft text-primary">{{ $history->count() }} Transactions</span>
                </div>
                
                <div class="glass-card overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle mb-0">
                            <thead class="small text-secondary uppercase font-weight-bold">
                                <tr>
                                    <th class="px-4 py-3">Timestamp</th>
                                    <th>Reference</th>
                                    <th>Network</th>
                                    <th>Amount</th>
                                    <th class="text-center px-4">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($history as $d)
                                <tr>
                                    <td class="px-4 py-3 small text-secondary">{{ \Carbon\Carbon::parse($d->created_at)->format('d M, Y H:i') }}</td>
                                    <td><code class="text-primary">{{ $d->trx_id }}</code></td>
                                    <td><span class="badge bg-secondary-soft text-white px-2 py-1">{{ strtoupper($d->pay_currency ?? 'USD') }}</span></td>
                                    <td class="font-weight-bold outfit">${{ number_format($d->amount, 2) }}</td>
                                    <td class="text-center px-4">
                                        @php
                                            $statusClass = [
                                                'success' => 'bg-success-soft text-success',
                                                'pending' => 'bg-warning-soft text-warning',
                                                'failed' => 'bg-danger-soft text-danger',
                                            ][strtolower($d->status)] ?? 'bg-secondary-soft text-secondary';
                                        @endphp
                                        @if(strtolower($d->status) === 'pending')
                                        <div class="pending-progress">
                                            <div class="d-flex justify-content-between w-100"><span class="progress-label">Processing</span><span class="progress-pct">93%</span></div>
                                            <div class="progress-track"><div class="progress-fill"></div></div>
                                        </div>
                                        @else
                                        <span class="badge {{ $statusClass }} px-3 py-1 rounded-pill" style="font-size: 0.65rem;">
                                            {{ strtoupper($d->status) }}
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center py-5 text-secondary">No funding history available.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .glass-card { 
        background: rgba(255, 255, 255, 0.03); 
        backdrop-filter: blur(15px) saturate(180%); 
        border: 1px solid rgba(255, 255, 255, 0.08); 
        border-radius: 28px;
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        position: relative;
        z-index: 1;
    }
    
    .method-card { 
        cursor: pointer; 
        transition: all 0.5s cubic-bezier(0.165, 0.84, 0.44, 1); 
        border: 1px solid rgba(255, 255, 255, 0.05); 
        position: relative;
        overflow: hidden;
        will-change: transform, box-shadow;
    }
    .method-card::before {
        content: '';
        position: absolute;
        top: -50%; left: -50%; width: 200%; height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
        opacity: 0;
        transition: 0.5s;
        pointer-events: none;
    }
    .method-card:hover { 
        transform: translateY(-15px) scale(1.03); 
        background: rgba(255, 255, 255, 0.07); 
        border-color: rgba(255, 255, 255, 0.2); 
        box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.6); 
        z-index: 10;
    }
    .method-card:hover::before { opacity: 1; transform: translate(10%, 10%); }

    .method-icon-container { 
        width: 72px; 
        height: 72px; 
        border-radius: 22px; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        transition: 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
    }
    
    .method-card:hover .method-icon-container {
        transform: rotateY(180deg) scale(1.1);
    }

    .coin-logo-premium {
        width: 46px; 
        height: 46px; 
        object-fit: contain; 
        filter: drop-shadow(0 8px 16px rgba(0,0,0,0.4));
        animation: float 4s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }

    .letter-spacing-2 { letter-spacing: 2px; }
    
    .bg-warning-soft { background: rgba(245, 158, 11, 0.15); }
    .bg-primary-soft { background: rgba(59, 130, 246, 0.15); }
    .bg-success-soft { background: rgba(255, 51, 51, 0.15); }
    .bg-info-soft { background: rgba(6, 182, 212, 0.15); }
    .bg-danger-soft { background: rgba(239, 68, 68, 0.15); }

    .border-dashed { border: 2px dashed var(--glass-border); transition: 0.3s; cursor: pointer; }
    .border-dashed:hover { border-color: var(--accent-primary); background: rgba(59, 130, 246, 0.05); }
    
    .premium-input { background: rgba(0,0,0,0.2); border: 1px solid var(--glass-border); border-radius: 12px; color: white; }
    .premium-input:focus { background: rgba(0,0,0,0.3); border-color: var(--accent-primary); box-shadow: none; }
    
    svg { max-width: 100%; height: auto; }
</style>

<!-- Bank Wire Request Modal -->
<div class="modal fade" id="wireModalMain" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card p-4" style="border: 1px solid var(--glass-border);">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="outfit font-weight-bold mb-0">Bank Wire Request</h4>
                <button type="button" class="btn-close btn-close-white" data-dismiss="modal"></button>
            </div>
            <p class="text-secondary small mb-4">Submit your details below. Our team will contact you within 24 hours with wire transfer instructions.</p>

            <form id="wire-form-main">
                @csrf
                <div class="form-group mb-3">
                    <label class="small text-secondary mb-2">Full Name</label>
                    <input type="text" class="form-control premium-input" value="{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}" name="full_name" required>
                </div>
                <div class="form-group mb-3">
                    <label class="small text-secondary mb-2">Email Address</label>
                    <input type="email" class="form-control premium-input" value="{{ auth()->user()->email }}" name="email" required>
                </div>
                <div class="form-group mb-3">
                    <label class="small text-secondary mb-2">Phone Number</label>
                    <input type="text" class="form-control premium-input" value="{{ auth()->user()->phone }}" name="phone" required>
                </div>
                <div class="form-group mb-3">
                    <label class="small text-secondary mb-2">Deposit Amount (USD)</label>
                    <input type="number" class="form-control premium-input" name="amount" placeholder="e.g. 5000" min="100" required>
                </div>
                <div class="form-group mb-4">
                    <label class="small text-secondary mb-2">Additional Notes (optional)</label>
                    <textarea class="form-control premium-input" name="notes" rows="2" placeholder="Country, preferred bank, etc."></textarea>
                </div>
                <div class="alert bg-info-soft text-info small border-0 mb-4">
                    <i class="ri-information-line me-1"></i> Our treasury team will review your request and send wire instructions to your registered email.
                </div>
                <button type="submit" class="btn btn-premium w-100 py-3 font-weight-bold" id="wire-submit-main">
                    Submit Wire Request
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Credit Card Deposit Modal -->
<div class="modal fade" id="ccDepositModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card p-4" style="border: 1px solid var(--glass-border);">
            <div id="cc-form-container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="outfit font-weight-bold mb-0">Secure Card Deposit</h4>
                    <button type="button" class="btn-close btn-close-white" data-dismiss="modal"></button>
                </div>
                
                <!-- Simple Preview -->
                <div class="mb-4 p-3 rounded-3" style="background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border);">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 60px; height: 38px; background: linear-gradient(135deg, #1e293b 0%, #000000 100%); border: 1px solid rgba(255,255,255,0.1); position: relative; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.3); flex-shrink: 0;">
                            <div style="position: absolute; top: 8px; left: 8px; width: 12px; height: 10px; background: #e2e8f0; border-radius: 2px; opacity: 0.8;"></div>
                            <div style="position: absolute; bottom: 8px; right: 8px; display: flex;">
                                <div style="width: 14px; height: 14px; background: #ef4444; border-radius: 50%; opacity: 0.8; margin-right: -6px; z-index: 1;"></div>
                                <div style="width: 14px; height: 14px; background: #eab308; border-radius: 50%; opacity: 0.8; z-index: 2;"></div>
                            </div>
                        </div>
                        <div>
                            <div class="small fw-bold">Premium Gateway</div>
                            <div class="x-small text-secondary">PCI-DSS Compliant Infrastructure</div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('deposit.pay') }}" method="POST">
                    @csrf
                    <input type="hidden" name="gateway_name" value="nowpayments_card">
                    <input type="hidden" name="method" value="USD">
                    <div class="form-group mb-4">
                        <label class="small text-secondary mb-2">Deposit Amount (USD)</label>
                        <input type="number" class="form-control premium-input" name="depositamount" placeholder="Minimum $50" min="50" required>
                    </div>
                    <button type="submit" class="btn btn-premium w-100 py-3 font-weight-bold">
                        Proceed to Secure Checkout
                    </button>
                </form>
            </div>

            <!-- Loading State -->
            <div id="cc-loading-container" class="d-none py-5 text-center">
                <div class="mb-4">
                    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
                </div>
                <h5 class="outfit font-weight-bold mb-2">Processing Transaction</h5>
                <p class="text-secondary small mb-4">Synchronizing with secure bank gateway...</p>
                
                <div class="progress mb-3" style="height: 10px; background: rgba(255,255,255,0.05); border-radius: 99px;">
                    <div id="cc-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%; background: var(--accent-primary);"></div>
                </div>
                <div class="text-primary small font-weight-bold"><span id="cc-progress-text">0</span>% Complete</div>
            </div>
        </div>
    </div>
</div>

<!-- NowPayments Crypto Modal -->
<div class="modal fade" id="nowPaymentsModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card p-4" style="border: 1px solid var(--glass-border);">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="outfit font-weight-bold mb-0">NowPayments Crypto</h4>
                <button type="button" class="btn-close btn-close-white" data-dismiss="modal"></button>
            </div>
            <div class="mb-4 p-3 rounded-3" style="background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border);">
                <div class="small fw-bold text-success mb-1">Pay with 50+ Cryptocurrencies</div>
                <div class="x-small text-secondary">You will be redirected to the secure NowPayments gateway to complete your deposit.</div>
            </div>
            <form action="{{ route('deposit.pay') }}" method="POST">
                @csrf
                <input type="hidden" name="gateway_name" value="nowpayments">
                <input type="hidden" name="method" value="BTC"> <!-- Default, NowPayments lets them choose -->
                <div class="form-group mb-4">
                    <label class="small text-secondary mb-2">Deposit Amount (USD)</label>
                    <input type="number" class="form-control premium-input" name="depositamount" placeholder="Minimum $50" min="50" required>
                </div>
                <button type="submit" class="btn btn-premium w-100 py-3 font-weight-bold">
                    Proceed to Payment
                </button>
            </form>
        </div>
    </div>
</div>

<!-- OxaPay Crypto Modal -->
<div class="modal fade" id="oxaPayModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card p-4" style="border: 1px solid var(--glass-border);">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="outfit font-weight-bold mb-0">OxaPay Crypto</h4>
                <button type="button" class="btn-close btn-close-white" data-dismiss="modal"></button>
            </div>
            <div class="mb-4 p-3 rounded-3" style="background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border);">
                <div class="small fw-bold text-primary mb-1">Secure Web3 Crypto Payments</div>
                <div class="x-small text-secondary">You will be redirected to OxaPay to securely process your crypto deposit.</div>
            </div>
            <form action="{{ route('deposit.pay') }}" method="POST">
                @csrf
                <input type="hidden" name="gateway_name" value="oxapay">
                <input type="hidden" name="method" value="USDT">
                <div class="form-group mb-4">
                    <label class="small text-secondary mb-2">Deposit Amount (USD)</label>
                    <input type="number" class="form-control premium-input" name="depositamount" placeholder="Minimum $50" min="50" required>
                </div>
                <button type="submit" class="btn btn-premium w-100 py-3 font-weight-bold">
                    Proceed to Payment
                </button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('js')
<script>
    let selectedMethod = null;
    let currentStep = 1;
    let currentCoinPriceUsd = 0;
    
    // User Local Currency logic
    const userCountry = "{{ auth()->user()->country ?? 'United States' }}".trim().toLowerCase();
    let userCurrencyCode = 'USD';
    let userCurrencyRate = 1.0;
    let userCurrencyFlagHtml = '<img src="https://flagcdn.com/w40/us.png" style="width:22px; border-radius:2px;" class="align-baseline">';

    const countryMap = {
        'afghanistan': { code: 'AFN', cca2: 'af' },
        'albania': { code: 'ALL', cca2: 'al' },
        'algeria': { code: 'DZD', cca2: 'dz' },
        'argentina': { code: 'ARS', cca2: 'ar' },
        'australia': { code: 'AUD', cca2: 'au' },
        'austria': { code: 'EUR', cca2: 'at' },
        'bangladesh': { code: 'BDT', cca2: 'bd' },
        'belgium': { code: 'EUR', cca2: 'be' },
        'brazil': { code: 'BRL', cca2: 'br' },
        'canada': { code: 'CAD', cca2: 'ca' },
        'china': { code: 'CNY', cca2: 'cn' },
        'colombia': { code: 'COP', cca2: 'co' },
        'egypt': { code: 'EGP', cca2: 'eg' },
        'france': { code: 'EUR', cca2: 'fr' },
        'germany': { code: 'EUR', cca2: 'de' },
        'india': { code: 'INR', cca2: 'in' },
        'indonesia': { code: 'IDR', cca2: 'id' },
        'iran': { code: 'IRR', cca2: 'ir' },
        'iraq': { code: 'IQD', cca2: 'iq' },
        'italy': { code: 'EUR', cca2: 'it' },
        'japan': { code: 'JPY', cca2: 'jp' },
        'kenya': { code: 'KES', cca2: 'ke' },
        'malaysia': { code: 'MYR', cca2: 'my' },
        'mexico': { code: 'MXN', cca2: 'mx' },
        'morocco': { code: 'MAD', cca2: 'ma' },
        'myanmar': { code: 'MMK', cca2: 'mm' },
        'nepal': { code: 'NPR', cca2: 'np' },
        'netherlands': { code: 'EUR', cca2: 'nl' },
        'nigeria': { code: 'NGN', cca2: 'ng' },
        'pakistan': { code: 'PKR', cca2: 'pk' },
        'philippines': { code: 'PHP', cca2: 'ph' },
        'poland': { code: 'PLN', cca2: 'pl' },
        'russia': { code: 'RUB', cca2: 'ru' },
        'saudi arabia': { code: 'SAR', cca2: 'sa' },
        'south africa': { code: 'ZAR', cca2: 'za' },
        'south korea': { code: 'KRW', cca2: 'kr' },
        'spain': { code: 'EUR', cca2: 'es' },
        'sri lanka': { code: 'LKR', cca2: 'lk' },
        'sweden': { code: 'SEK', cca2: 'se' },
        'switzerland': { code: 'CHF', cca2: 'ch' },
        'thailand': { code: 'THB', cca2: 'th' },
        'turkey': { code: 'TRY', cca2: 'tr' },
        'uganda': { code: 'UGX', cca2: 'ug' },
        'ukraine': { code: 'UAH', cca2: 'ua' },
        'united arab emirates': { code: 'AED', cca2: 'ae' },
        'united kingdom': { code: 'GBP', cca2: 'gb' },
        'united states': { code: 'USD', cca2: 'us' },
        'vietnam': { code: 'VND', cca2: 'vn' },
        'yemen': { code: 'YER', cca2: 'ye' },
        'zimbabwe': { code: 'ZWL', cca2: 'zw' }
    };

    // Fast robust local lookup
    if (countryMap[userCountry]) {
        userCurrencyCode = countryMap[userCountry].code;
        userCurrencyFlagHtml = '<img src="https://flagcdn.com/w40/' + countryMap[userCountry].cca2 + '.png" style="width:22px; border-radius:2px;" class="align-baseline">';
        
        if (userCurrencyCode !== 'USD') {
            $.get('https://open.er-api.com/v6/latest/USD')
                .done(function(rates) {
                    if (rates && rates.rates && rates.rates[userCurrencyCode]) {
                        userCurrencyRate = rates.rates[userCurrencyCode];
                        $('#deposit-amount').trigger('input');
                    }
                });
        }
    }

    function selectMethod(symbol, name, address, qrBase64) {
        selectedMethod = { symbol, name, address };
        $('#selected-method-name').text(name);
        $('#selected-currency').text(symbol);
        $('#deposit-coin-label').text(symbol);
        $('#wallet-address').text(address);
        
        // Reset amounts
        $('#deposit-amount').val('');
        $('#deposit-amount-usd').val('');
        $('#deposit-usd-display').html('<div class="d-flex flex-column gap-2 text-start px-4 py-3" style="background: rgba(0,0,0,0.2); border-radius: 8px;"><div class="d-flex align-items-center gap-2"><span class="text-muted">≈</span><img src="https://flagcdn.com/w40/us.png" style="width: 24px; border-radius: 2px;"><span class="text-white fw-bold">USD $ 0.00</span></div></div>');

        // Estimate confirmation time based on coin
        let estTime = '10-30 minutes';
        if (['BTC'].includes(symbol)) estTime = '30-60 minutes';
        else if (['ETH', 'USDT'].includes(symbol)) estTime = '5-15 minutes';
        else if (['SOL', 'TRX', 'BNB', 'DOGE'].includes(symbol)) estTime = '1-5 minutes';
        else if (['XMR'].includes(symbol)) estTime = '15-30 minutes';
        $('#confirmation-estimate').text('Estimated confirmation time: ' + estTime);

        // Fetch live price for the selected coin
        if (symbol && symbol.toUpperCase() !== 'USD' && symbol.toUpperCase() !== 'USDT') {
            $('#deposit-usd-display').text('Fetching live price...');
            let fetchSymbol = symbol.toUpperCase();
            
            $.get('https://api.coinbase.com/v2/exchange-rates?currency=' + fetchSymbol)
                .done(function(res) {
                    if (res && res.data && res.data.rates && res.data.rates.USD) {
                        currentCoinPriceUsd = parseFloat(res.data.rates.USD);
                        $('#deposit-usd-display').html('<div class="d-flex flex-column gap-2 text-start px-4 py-3" style="background: rgba(0,0,0,0.2); border-radius: 8px;"><div class="d-flex align-items-center gap-2"><span class="text-muted">≈</span><img src="https://flagcdn.com/w40/us.png" style="width: 24px; border-radius: 2px;"><span class="text-white fw-bold">USD $ ' + currentCoinPriceUsd.toLocaleString(undefined, {minimumFractionDigits: 2}) + '</span></div></div>');
                        
                        // Force update if the user already typed something
                        $('#deposit-amount').trigger('input');
                    }
                })
                .fail(function() {
                    $('#deposit-usd-display').text('Price unavailable. Please enter USD manually or retry.');
                    currentCoinPriceUsd = 0; 
                });
        } else {
            currentCoinPriceUsd = 1.0;
        }

        // Decode base64 QR code and inject SVG
        try {
            const qrSvg = atob(qrBase64);
            $('#qr-mount').html(qrSvg);
        } catch (e) {
            console.error('QR Decode Error:', e);
            $('#qr-mount').html('<p class="text-danger small">Error loading QR</p>');
        }
        
        goToStep(2);
    }

    // Auto currency conversion logic
    $('#deposit-amount').on('input', function() {
        let coinAmount = parseFloat($(this).val());
        if (!isNaN(coinAmount) && coinAmount > 0) {
            let usdValue = coinAmount * currentCoinPriceUsd;
            let displayString = '<div class="d-flex flex-column gap-2 text-start px-4 py-3" style="background: rgba(0,0,0,0.2); border-radius: 8px;"><div class="d-flex align-items-center gap-2"><span class="text-muted">≈</span><img src="https://flagcdn.com/w40/us.png" style="width: 24px; border-radius: 2px;"><span class="text-white fw-bold">USD $' + usdValue.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</span></div>';
            
            // Show local currency if it's different
            if (userCurrencyCode !== 'USD' && userCurrencyRate > 0) {
                let localValue = usdValue * userCurrencyRate;
                displayString += '<div class="d-flex align-items-center gap-2"><span class="text-muted">≈</span>' + userCurrencyFlagHtml + '<span class="text-secondary fw-bold">' + userCurrencyCode + ' $' + localValue.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</span></div>';
            }
            displayString += '</div>';

            $('#deposit-amount-usd').val(usdValue.toFixed(2));
            $('#deposit-usd-display').html(displayString);
        } else {
            $('#deposit-amount-usd').val('');
            $('#deposit-usd-display').html('<div class="d-flex flex-column gap-2 text-start px-4 py-3" style="background: rgba(0,0,0,0.2); border-radius: 8px;"><div class="d-flex align-items-center gap-2"><span class="text-muted">≈</span><img src="https://flagcdn.com/w40/us.png" style="width: 24px; border-radius: 2px;"><span class="text-white fw-bold">USD $ 0.00</span></div></div>');
        }
    });

    function goToStep(step) {
        $('.deposit-step').addClass('d-none');
        $(`#step-${step}`).removeClass('d-none');
        
        $('.step-item').removeClass('active');
        for(let i=1; i<=step; i++) {
            $(`#step${i}-indicator`).addClass('active');
        }
        
        if(step === 3) {
            // We do not just blindly go to step 3. 
            // initiateCryptoDeposit handles the transition after the API call.
        }
        
        currentStep = step;
    }

    function initiateCryptoDeposit() {
        let amtUsd = $('#deposit-amount-usd').val();
        if(!amtUsd || amtUsd <= 0) {
            toastr.error('Please enter a valid amount before confirming');
            return;
        }

        const btn = $(event.currentTarget);
        const originalText = btn.html();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Generating Request...');

        $.ajax({
            url: "{{ route('deposit.initiate-crypto') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                amount_usd: amtUsd,
                symbol: selectedMethod.symbol
            },
            success: function(response) {
                if(response.success) {
                    $('#confirm-method-name').text(selectedMethod.name);
                    $('#confirm-method-symbol').text(selectedMethod.symbol);
                    $('#confirm-amount-usd').text('$' + parseFloat(response.data.amount_usd).toLocaleString(undefined, {minimumFractionDigits:2}));
                    
                    $('#confirm-exact-crypto').text(response.data.exact_crypto_amount + ' ' + selectedMethod.symbol);
                    $('#confirm-exact-crypto-alert').text(response.data.exact_crypto_amount + ' ' + selectedMethod.symbol);
                    $('#confirm-wallet-address').text(response.data.address);
                    
                    $('.deposit-step').addClass('d-none');
                    $(`#step-3`).removeClass('d-none');
                    
                    $('.step-item').removeClass('active');
                    $(`#step1-indicator`).addClass('active');
                    $(`#step2-indicator`).addClass('active');
                    $(`#step3-indicator`).addClass('active');
                    
                    toastr.success("Deposit request generated successfully.");
                } else {
                    toastr.error(response.message || "Failed to generate deposit request.");
                }
            },
            error: function(xhr) {
                let msg = 'Error processing request.';
                if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                toastr.error(msg);
            },
            complete: function() {
                btn.prop('disabled', false).html(originalText);
            }
        });
    }

    function copyAddress() {
        const addr = $('#wallet-address').text();
        navigator.clipboard.writeText(addr);
        toastr.success('Address copied to clipboard');
    }

    function copyAddressFromConfirm() {
        const addr = $('#confirm-wallet-address').text();
        navigator.clipboard.writeText(addr);
        toastr.success('Address copied to clipboard');
    }

    // Removed file upload logic as we now use automated tracking

    // Bank Wire Form Handler
    $('#wire-form-main').on('submit', function(e) {
        e.preventDefault();
        var btn = $('#wire-submit-main');
        btn.attr('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Submitting...');
        setTimeout(function() {
            $('#wireModalMain').modal('hide');
            btn.removeAttr('disabled').text('Submit Wire Request');
            toastr.success('Your bank wire request has been submitted. Our team will contact you within 24 hours with wire instructions.');
            $('#wire-form-main')[0].reset();
        }, 1500);
    });
</script>
@endpush

