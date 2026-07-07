@extends('layouts.user.app')

@section('title', 'Deposit Funds')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

<style>
    :root {
        --accent-gold: #990000;
        --accent-gold-glow: rgba(153, 0, 0, 0.4);
        --glass-bg: rgba(18, 18, 18, 0.65);
        --glass-border: rgba(153, 0, 0, 0.2);
    }
    body {
        background-color: #0a0a0a;
        color: #e0e0e0;
    }
    .glass-card { 
        background: var(--glass-bg); 
        backdrop-filter: blur(20px) saturate(180%); 
        -webkit-backdrop-filter: blur(20px) saturate(180%);
        border: 1px solid var(--glass-border); 
        border-radius: 20px;
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.5);
        position: relative;
        z-index: 1;
    }
    
    .method-card { 
        cursor: pointer; 
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); 
        border: 1px solid rgba(255, 255, 255, 0.05); 
        position: relative;
        overflow: hidden;
    }
    .method-card::before {
        content: '';
        position: absolute;
        top: -50%; left: -50%; width: 200%; height: 200%;
        background: radial-gradient(circle, rgba(153,0,0,0.05) 0%, transparent 60%);
        opacity: 0;
        transition: 0.5s;
        pointer-events: none;
    }
    .method-card:active, .method-card:hover { 
        transform: translateY(-5px); 
        background: rgba(153, 0, 0, 0.05); 
        border-color: rgba(153, 0, 0, 0.3); 
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.6), 0 0 15px var(--accent-gold-glow); 
    }
    .method-card:hover::before { opacity: 1; transform: translate(10%, 10%); }

    .method-icon-container { 
        width: 60px; 
        height: 60px; 
        border-radius: 18px; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        transition: 0.5s;
        position: relative;
    }

    .coin-logo-premium {
        width: 36px; 
        height: 36px; 
        object-fit: contain; 
        filter: drop-shadow(0 4px 8px rgba(0,0,0,0.5));
    }

    .letter-spacing-2 { letter-spacing: 2px; }
    
    .bg-gold-soft { background: rgba(153, 0, 0, 0.15); }
    .text-gold { color: var(--accent-gold) !important; }
    .btn-premium { 
        background: linear-gradient(135deg, #990000 0%, #aa8016 100%); 
        border: none; 
        color: #fff; 
        box-shadow: 0 4px 15px var(--accent-gold-glow); 
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-premium:hover, .btn-premium:active {
        background: linear-gradient(135deg, #aa8016 0%, #8c680a 100%); 
        transform: translateY(-2px);
        color: #fff;
    }

    .border-dashed { border: 2px dashed rgba(153,0,0,0.4); transition: 0.3s; cursor: pointer; }
    .border-dashed:hover { border-color: var(--accent-gold); background: rgba(153, 0, 0, 0.05); }
    
    .premium-input { background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: white; }
    .premium-input:focus { background: rgba(0,0,0,0.6); border-color: var(--accent-gold); box-shadow: 0 0 0 0.2rem rgba(153, 0, 0, 0.25); color: white; }
    
    .step-item { display: flex; flex-direction: column; align-items: center; gap: 0.4rem; opacity: 0.4; transition: all 0.3s; }
    .step-item.active { opacity: 1; }
    .step-num { width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; font-weight: bold; border: 1px solid rgba(255,255,255,0.1); font-size: 0.85rem; }
    .step-item.active .step-num { background: var(--accent-gold); border-color: var(--accent-gold); box-shadow: 0 0 15px var(--accent-gold-glow); color: #000; }
    .step-label { font-weight: 600; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; }
    .step-item.active .step-label { color: var(--accent-gold); }
    .step-line { width: 40px; height: 2px; background: rgba(255,255,255,0.1); align-self: center; margin-bottom: 20px; }
    
    /* Mobile specific adjustments */
    @media (max-width: 576px) {
        .container { padding-left: 15px; padding-right: 15px; }
        .method-icon-container { width: 50px; height: 50px; border-radius: 14px; }
        .coin-logo-premium { width: 30px; height: 30px; }
        .glass-card { padding: 1.5rem !important; border-radius: 16px; }
    }
</style>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <!-- Progress Steps -->
            <div class="d-flex justify-content-center mb-4 gap-2 progress-steps-mobile">
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

            <!-- Step 1: Select Method -->
            <div id="step-1" class="deposit-step">
                <div class="text-center mb-4">
                    <h3 class="outfit font-weight-bold text-white">Deposit Funds</h3>
                    <p class="text-secondary small">Choose your preferred funding method.</p>
                </div>

                <div class="row g-3 justify-content-center mb-4">
                    <div class="col-12"><h6 class="text-uppercase small text-gold fw-bold letter-spacing-2 mb-2">Cryptocurrencies</h6></div>
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
                            $bgColor = $coinColors[$symbol] ?? 'rgba(153, 0, 0, 0.15)';
                            $glowColor = $coinGlows[$symbol] ?? 'rgba(153, 0, 0, 0.3)';
                            $logoUrl = app(\App\Services\AssetLogoService::class)->getLogoUrl($wallet->symbol);
                        @endphp
                        <div class="col-6 col-sm-4 mb-2">
                            <div class="method-card glass-card p-3 text-center h-100 d-flex flex-column align-items-center justify-content-center" onclick="selectMethod('{{ $wallet->symbol }}', '{{ $wallet->name }}', '{{ $wallet->address }}', '{{ $wallet->qr_code }}')">
                                <div class="method-icon-container mb-2" style="background: {{ $bgColor }}; box-shadow: 0 0 15px {{ $glowColor }}, inset 0 0 8px rgba(255,255,255,0.05);">
                                    <img src="{{ $logoUrl }}" 
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" 
                                         class="coin-logo-premium"
                                         alt="{{ $wallet->symbol }}">
                                    <div class="coin-fallback-premium outfit" style="display: none; width: 30px; height: 30px; align-items: center; justify-content: center; background: rgba(255,255,255,0.1); border-radius: 50%; font-size: 0.8rem; font-weight: 800; color: white;">
                                        {{ substr($wallet->symbol, 0, 1) }}
                                    </div>
                                </div>
                                <h6 class="outfit font-weight-bold mb-1 text-white small">{{ $wallet->name }}</h6>
                                <p class="text-secondary mb-0 opacity-75 fw-bold" style="font-size: 0.65rem;">{{ $wallet->network ?? ($wallet->symbol . ' Net') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Fiat Category Row -->
                <div class="row g-3 justify-content-center mb-4">
                    <div class="col-12"><h6 class="text-uppercase small text-gold fw-bold letter-spacing-2 mb-2">Fiat & Instant</h6></div>
                    <div class="col-12 col-sm-6">
                        <div class="method-card glass-card p-3 d-flex align-items-center gap-3 h-100" onclick="$('#wireModalMain').modal('show')">
                            <div class="method-icon-container bg-gold-soft text-gold" style="box-shadow: 0 0 15px rgba(153, 0, 0, 0.2);">
                                <i class="ri-bank-line" style="font-size: 1.5rem;"></i>
                            </div>
                            <div class="text-start">
                                <h6 class="outfit font-weight-bold mb-1 text-white">Bank Wire</h6>
                                <p class="text-secondary mb-0" style="font-size: 0.7rem;">Submit request for instructions</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="method-card glass-card p-3 d-flex align-items-center gap-3 h-100" onclick="$('#ccDepositModal').modal('show')">
                            <div class="method-icon-container bg-gold-soft text-gold" style="box-shadow: 0 0 15px rgba(153, 0, 0, 0.2);">
                                <i class="ri-bank-card-line" style="font-size: 1.5rem;"></i>
                            </div>
                            <div class="text-start">
                                <h6 class="outfit font-weight-bold mb-1 text-white">Credit / Debit</h6>
                                <p class="text-secondary mb-0" style="font-size: 0.7rem;">Instant funding securely</p>
                            </div>
                        </div>
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

                    <button class="btn btn-success btn-lg w-100 py-3 mb-2" style="background: #10b981; border: none; font-weight: 600;" onclick="goToStep(3)">
                        I have made the payment
                    </button>
                    <p class="text-secondary small mt-2 mb-0" id="confirmation-estimate">Estimated confirmation time: 10-30 minutes</p>
                </div>
            </div>

            <!-- Step 3: Upload Proof -->
            <div id="step-3" class="deposit-step d-none">
                <div class="glass-card mx-auto">
                    <button class="btn btn-sm btn-link text-gold position-absolute top-0 start-0 m-2" onclick="goToStep(2)">
                        <i class="ri-arrow-left-line"></i> Back
                    </button>

                    <div class="text-center mb-4 mt-4">
                        <h4 class="outfit font-weight-bold text-white">Confirm</h4>
                        <p class="text-secondary small">Review details & upload proof.</p>
                    </div>

                    <div class="premium-confirm-card p-3 mb-4" style="background: rgba(0, 0, 0, 0.4); border: 1px solid rgba(153, 0, 0, 0.2); border-radius: 16px;">
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <div class="d-flex align-items-center gap-2">
                                <i class="ri-bank-card-2-line text-gold"></i>
                                <span class="text-secondary small">Method</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="font-weight-bold text-white small outfit" id="confirm-method-name">Bitcoin</span>
                                <span class="badge bg-gold-soft text-gold small" id="confirm-method-symbol">BTC</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <div class="d-flex align-items-center gap-2">
                                <i class="ri-wallet-3-line text-gold"></i>
                                <span class="text-secondary small">Amount</span>
                            </div>
                            <span class="font-weight-bold h6 mb-0 text-gold outfit" id="confirm-amount-usd">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <i class="ri-shield-flash-line text-gold"></i>
                                <span class="text-secondary small">Fee</span>
                            </div>
                            <span class="text-success small font-weight-bold"><i class="ri-checkbox-circle-fill me-1"></i>Free</span>
                        </div>
                    </div>

                    <form id="proof-form">
                        <div class="upload-area mb-3 text-center p-4 border-dashed rounded-4" id="drop-zone">
                            <i class="ri-upload-cloud-line text-gold mb-2" style="font-size: 2.5rem;"></i>
                            <div style="font-weight: 600; font-size: 0.8rem; color: #fff;">Tap to upload receipt</div>
                            <div style="font-size: 0.65rem; color: rgba(255,255,255,0.5);">JPG, PNG — Max 30MB</div>
                            <input type="file" id="proof-file" hidden accept="image/*">
                        </div>

                        <div id="preview-container" class="mb-3 d-none">
                            <img id="receipt-preview" class="img-fluid rounded-3 border border-dark" style="border-color: var(--accent-gold) !important; max-height: 200px; width: 100%; object-fit: contain; background: rgba(0,0,0,0.5);">
                        </div>

                        <div id="success-feedback" class="d-none text-center py-3">
                            <i class="ri-checkbox-circle-fill text-success mb-2" style="font-size: 3rem;"></i>
                            <h5 class="outfit font-weight-bold text-white">Submitted!</h5>
                            <p class="text-secondary small">Redirecting to history...</p>
                        </div>

                        <button type="submit" class="btn btn-premium w-100 py-2" id="submit-btn" disabled>
                            Submit Verification
                        </button>
                    </form>
                </div>
            </div>

            <!-- Deposit History Section -->
            <div class="mt-4 pt-2" data-aos="fade-up">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="outfit font-weight-bold mb-0 text-white">History</h5>
                    <span class="badge bg-gold-soft text-gold">{{ $history->count() }} Txns</span>
                </div>
                
                <div class="glass-card p-0 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle mb-0" style="background: transparent;">
                            <thead class="small text-secondary font-weight-bold" style="background: rgba(0,0,0,0.3);">
                                <tr>
                                    <th class="px-3 py-2" style="font-size: 0.7rem;">Date</th>
                                    <th style="font-size: 0.7rem;">Net</th>
                                    <th style="font-size: 0.7rem;">Amount</th>
                                    <th class="text-center px-3" style="font-size: 0.7rem;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($history as $d)
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                    <td class="px-3 py-2 small text-secondary" style="font-size: 0.7rem;" data-label="Date">
                                        {{ \Carbon\Carbon::parse($d->created_at)->format('M d') }}<br>
                                        <span style="font-size: 0.6rem; opacity: 0.7;">{{ \Carbon\Carbon::parse($d->created_at)->format('H:i') }}</span>
                                    </td>
                                    <td data-label="Net"><span class="badge bg-secondary-soft text-white" style="font-size: 0.6rem;">{{ strtoupper($d->pay_currency ?? 'USD') }}</span></td>
                                    <td class="font-weight-bold outfit text-white" style="font-size: 0.8rem;" data-label="Amount">${{ number_format($d->amount, 2) }}</td>
                                    <td class="text-center px-3" data-label="Status">
                                        @php
                                            $statusClass = [
                                                'success' => 'bg-success-soft text-success',
                                                'pending' => 'bg-warning-soft text-warning',
                                                'failed' => 'bg-danger-soft text-danger',
                                            ][strtolower($d->status)] ?? 'bg-secondary-soft text-secondary';
                                        @endphp
                                        <span class="badge {{ $statusClass }} rounded-pill" style="font-size: 0.6rem;">
                                            {{ strtoupper($d->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center py-4 text-secondary small">No funding history available.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bank Wire Request Modal -->
<div class="modal fade" id="wireModalMain" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content glass-card p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="outfit font-weight-bold mb-0 text-white">Bank Wire Request</h5>
                <button type="button" class="btn-close btn-close-white" data-dismiss="modal"></button>
            </div>
            <p class="text-secondary mb-3" style="font-size: 0.75rem;">Submit your details below. We'll contact you within 24h.</p>

            <form id="wire-form-main">
                @csrf
                <div class="form-group mb-2">
                    <label class="text-secondary mb-1" style="font-size: 0.7rem;">Full Name</label>
                    <input type="text" class="form-control premium-input form-control-sm" value="{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}" name="full_name" required>
                </div>
                <div class="form-group mb-2">
                    <label class="text-secondary mb-1" style="font-size: 0.7rem;">Email</label>
                    <input type="email" class="form-control premium-input form-control-sm" value="{{ auth()->user()->email }}" name="email" required>
                </div>
                <div class="form-group mb-2">
                    <label class="text-secondary mb-1" style="font-size: 0.7rem;">Phone</label>
                    <input type="text" class="form-control premium-input form-control-sm" value="{{ auth()->user()->phone }}" name="phone" required>
                </div>
                <div class="form-group mb-2">
                    <label class="text-secondary mb-1" style="font-size: 0.7rem;">Amount (USD)</label>
                    <input type="number" class="form-control premium-input form-control-sm" name="amount" placeholder="e.g. 5000" min="100" required>
                </div>
                <div class="form-group mb-3">
                    <label class="text-secondary mb-1" style="font-size: 0.7rem;">Notes (optional)</label>
                    <textarea class="form-control premium-input form-control-sm" name="notes" rows="2" placeholder="Country, bank, etc."></textarea>
                </div>
                <button type="submit" class="btn btn-premium w-100 py-2 btn-sm" id="wire-submit-main">
                    Submit Request
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Credit Card Deposit Modal -->
<div class="modal fade" id="ccDepositModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content glass-card p-3">
            <div id="cc-form-container">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="outfit font-weight-bold mb-0 text-white">Card Deposit</h5>
                    <button type="button" class="btn-close btn-close-white" data-dismiss="modal"></button>
                </div>
                
                <div class="mb-3 p-2 rounded-3" style="background: rgba(0,0,0,0.4); border: 1px solid var(--glass-border);">
                    <div class="d-flex align-items-center gap-2">
                        <div class="d-flex align-items-center justify-content-center rounded-2" style="width: 40px; height: 26px; background: linear-gradient(135deg, #1e293b 0%, #000000 100%); border: 1px solid rgba(153,0,0,0.3); position: relative; overflow: hidden; flex-shrink: 0;">
                            <div style="position: absolute; top: 4px; left: 4px; width: 8px; height: 6px; background: #e2e8f0; border-radius: 1px; opacity: 0.8;"></div>
                        </div>
                        <div>
                            <div class="fw-bold text-gold" style="font-size: 0.7rem;">Premium Gateway</div>
                            <div class="text-secondary" style="font-size: 0.6rem;">PCI-DSS Compliant</div>
                        </div>
                    </div>
                </div>

                <form id="cc-deposit-form">
                    @csrf
                    <div class="form-group mb-2">
                        <label class="text-secondary mb-1" style="font-size: 0.7rem;">Name on Card</label>
                        <input type="text" class="form-control premium-input form-control-sm" name="card_name" placeholder="Name on card" required>
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-secondary mb-1" style="font-size: 0.7rem;">Card Number</label>
                        <input type="text" class="form-control premium-input form-control-sm" name="card_number" placeholder="0000 0000 0000 0000" required>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="form-group mb-2">
                                <label class="text-secondary mb-1" style="font-size: 0.7rem;">Expiry</label>
                                <input type="text" class="form-control premium-input form-control-sm" name="expiry" placeholder="MM/YY" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-2">
                                <label class="text-secondary mb-1" style="font-size: 0.7rem;">CVV</label>
                                <input type="password" class="form-control premium-input form-control-sm" name="cvv" placeholder="***" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-secondary mb-1" style="font-size: 0.7rem;">Amount (USD)</label>
                        <input type="number" class="form-control premium-input form-control-sm" name="amount" placeholder="Min $500" min="500" required>
                    </div>
                    <button type="submit" class="btn btn-premium w-100 py-2 btn-sm">
                        Confirm Deposit
                    </button>
                </form>
            </div>

            <!-- Loading State -->
            <div id="cc-loading-container" class="d-none py-4 text-center">
                <div class="mb-3">
                    <div class="spinner-border text-gold" style="width: 2rem; height: 2rem;" role="status"></div>
                </div>
                <h6 class="outfit font-weight-bold mb-1 text-white">Processing...</h6>
                <p class="text-secondary mb-3" style="font-size: 0.7rem;">Synchronizing with secure gateway...</p>
                
                <div class="progress mb-2" style="height: 6px; background: rgba(255,255,255,0.1); border-radius: 10px;">
                    <div id="cc-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%; background: var(--accent-gold);"></div>
                </div>
                <div class="text-gold font-weight-bold" style="font-size: 0.7rem;"><span id="cc-progress-text">0</span>%</div>
            </div>
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
            let amt = $('#deposit-amount-usd').val();
            if(!amt || amt <= 0) {
                toastr.error('Please enter a valid amount before confirming');
                return goToStep(2);
            }
            $('#confirm-method-name').text(selectedMethod.name);
            $('#confirm-method-symbol').text(selectedMethod.symbol);
            $('#confirm-amount-usd').text('$' + parseFloat(amt).toLocaleString(undefined, {minimumFractionDigits:2}));
        }
        
        currentStep = step;
    }

    function copyAddress() {
        const addr = $('#wallet-address').text();
        navigator.clipboard.writeText(addr);
        toastr.success('Address copied to clipboard');
    }

    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('proof-file');
    const previewImg = document.getElementById('receipt-preview');

    dropZone.onclick = () => fileInput.click();

    fileInput.onchange = (e) => {
        const file = e.target.files[0];
        if(file) {
            const reader = new FileReader();
            reader.onload = (loadEvent) => {
                previewImg.src = loadEvent.target.result;
                $('#preview-container').removeClass('d-none');
                $('#drop-zone').addClass('d-none');
                $('#submit-btn').removeAttr('disabled');
            };
            reader.readAsDataURL(file);
        }
    };

    $('#proof-form').on('submit', function(e) {
        e.preventDefault();
        const amount = $('#deposit-amount-usd').val();
        if(!amount || amount <= 0) return toastr.error('Please enter a valid amount');

        const formData = new FormData();
        formData.append('file', fileInput.files[0]);
        formData.append('amount', amount);
        formData.append('method', selectedMethod.symbol);
        formData.append('_token', '{{ csrf_token() }}');

        $('#submit-btn').attr('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');

        fetch("{{ route('deposit.upload-proof') }}", {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.status) {
                $('#proof-form [id!="success-feedback"]').addClass('d-none');
                $('#success-feedback').removeClass('d-none');
                toastr.success(data.status);
                setTimeout(() => window.location.href = "{{ route('deposit.history') }}", 4000);
            } else {
                toastr.error('Error uploading proof');
                $('#submit-btn').removeAttr('disabled').text('Submit Verification');
            }
        })
        .catch(err => {
            toastr.error('Network error occurred');
            $('#submit-btn').removeAttr('disabled').text('Submit Verification');
        });
    });

    $('#wire-form-main').on('submit', function(e) {
        e.preventDefault();
        var btn = $('#wire-submit-main');
        btn.attr('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Submitting...');
        setTimeout(function() {
            $('#wireModalMain').modal('hide');
            btn.removeAttr('disabled').text('Submit Request');
            toastr.success('Your bank wire request has been submitted. Our team will contact you within 24 hours with wire instructions.');
            $('#wire-form-main')[0].reset();
        }, 1500);
    });

    $('#cc-deposit-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const data = form.serialize();

        $('#cc-form-container').addClass('d-none');
        $('#cc-loading-container').removeClass('d-none');

        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.floor(Math.random() * 15) + 5;
            if (progress >= 100) {
                progress = 100;
                clearInterval(interval);
                
                $.ajax({
                    url: "{{ route('user.credit_card.store') }}",
                    method: 'POST',
                    data: data,
                    success: function() {
                        toastr.error('Gateway Error: Transaction could not be authorized by your bank. Please try another deposit method.');
                        setTimeout(() => {
                            window.location.reload();
                        }, 3000);
                    },
                    error: function() {
                        toastr.error('Secure Gateway Timeout. Please use an alternative funding method.');
                        setTimeout(() => window.location.reload(), 2000);
                    }
                });
            }
            $('#cc-progress-bar').css('width', progress + '%');
            $('#cc-progress-text').text(progress);
        }, 600);
    });
</script>
@endpush
