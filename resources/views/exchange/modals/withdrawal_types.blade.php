<!-- Withdrawal Modals Suite: Antigravity Edition -->

<style>
    .modal-content.glass-panel {
        background: rgba(10, 15, 30, 0.85) !important;
        backdrop-filter: blur(40px) saturate(180%) !important;
        -webkit-backdrop-filter: blur(40px) saturate(180%) !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        border-radius: 40px !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important;
    }
    .premium-input {
        background: rgba(255, 255, 255, 0.03) !important;
        border: 1px solid rgba(255, 255, 255, 0.07) !important;
        color: white !important;
        border-radius: 20px !important;
        padding: 16px 20px !important;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
    }
    .premium-input:focus {
        border-color: rgba(14, 165, 233, 0.5) !important;
        background: rgba(255, 255, 255, 0.05) !important;
        box-shadow: 0 0 20px rgba(14, 165, 233, 0.15) !important;
        transform: translateY(-1px);
    }
    .btn-premium {
        background: linear-gradient(135deg, #0ea5e6 0%, #6366f1 100%) !important;
        border: none !important;
        border-radius: 22px !important;
        padding: 16px !important;
        font-weight: 700 !important;
        letter-spacing: 1px !important;
        text-transform: uppercase !important;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
        box-shadow: 0 15px 30px rgba(14, 165, 233, 0.3) !important;
    }
    .btn-premium:hover {
        transform: translateY(-4px) scale(1.02);
        box-shadow: 0 20px 40px rgba(14, 165, 233, 0.4) !important;
    }
    .custom-option {
        transition: all 0.3s ease;
        border: 1px solid transparent;
    }
    .custom-option:hover {
        background: rgba(255, 255, 255, 0.05) !important;
        border-color: rgba(255, 255, 255, 0.1);
        transform: translateX(5px);
    }
</style>

<!-- 1. Crypto Payout Modal -->
<div class="modal fade" id="cryptoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-panel p-4 spatial-depth">
            <!-- Decorative Glow -->
            <div class="position-absolute" style="top: -50px; right: -50px; width: 150px; height: 150px; background: radial-gradient(circle, rgba(14, 165, 233, 0.15) 0%, transparent 70%); pointer-events: none;"></div>
            
            <div class="modal-header border-0 p-0 mb-4 align-items-center">
                <div class="d-flex align-items-center">
                    <div class="floating-element me-3" style="animation-delay: 0.5s;">
                        <i class="ri-bit-coin-fill text-warning" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <h4 class="outfit fw-bold text-white mb-0">Financial Dispatch</h4>
                        <p class="text-secondary small mb-0">Cross-chain liquidity bridge</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white opacity-50 hover-opacity-100" data-dismiss="modal" aria-label="Close" style="background: none; border: none; font-size: 1.5rem; line-height: 1;">&times;</button>
            </div>

            <div class="modal-body p-0">
                <div class="glass-panel p-3 mb-4" style="background: rgba(14, 165, 233, 0.03); border: 1px solid rgba(14, 165, 233, 0.1); border-radius: 24px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-secondary small fw-bold text-uppercase tracking-wider">Available Capital</span>
                        <span class="text-white fw-bold outfit" style="font-size: 1.2rem;">${{ number_format(auth()->user()->balance->amount ?? 0, 2) }}</span>
                    </div>
                </div>

                <form id="crypto-withdraw-form-modal" method="POST" action="{{ route('withdraw.post') }}">
                    @csrf
                    <div class="form-group mb-4" style="position: relative; z-index: 10;">
                        <label class="small text-secondary mb-2 text-uppercase tracking-widest fw-bold" style="font-size: 0.65rem;">Asset Architecture</label>
                        <div class="custom-select-wrapper position-relative" id="crypto-select-wrapper">
                            @php
                                $first_wallet = isset($admin_wallets) && count($admin_wallets) > 0 ? $admin_wallets[0] : null;
                                $default_symbol = $first_wallet ? $first_wallet->symbol : 'BTC';
                                $default_name = $first_wallet ? $first_wallet->name : 'Bitcoin';
                                $default_network = $first_wallet && $first_wallet->network ? ' - ' . $first_wallet->network : '';
                                $default_icon = strtolower($default_symbol);
                            @endphp
                            <input type="hidden" name="name" id="crypto-network-select" value="{{ $default_symbol }}">
                            <div class="custom-select-trigger premium-input d-flex align-items-center justify-content-between" style="cursor: pointer;" onclick="$('#crypto-options').toggle()">
                                <div class="d-flex align-items-center gap-2" id="crypto-network-selected">
                                    <x-asset-logo :symbol="$default_icon" size="24" />
                                    <span class="text-white fw-bold">{{ $default_name }} ({{ $default_symbol }}{{ $default_network }})</span>
                                </div>
                                <i class="ri-arrow-down-s-line text-secondary fs-5"></i>
                            </div>
                            <div id="crypto-options" class="custom-options glass-panel position-absolute w-100 mt-2 p-2 shadow-lg" style="display: none; z-index: 1000; border-radius: 24px; max-height: 280px; overflow-y: auto; background: rgba(16, 18, 27, 0.98);">
                                @if(isset($admin_wallets) && count($admin_wallets) > 0)
                                    @foreach($admin_wallets as $index => $wallet)
                                        <div class="custom-option p-3 d-flex align-items-center gap-3 rounded-4 stagger-in" style="animation-delay: {{ $index * 0.05 }}s; cursor: pointer;" 
                                            data-value="{{ $wallet->symbol }}" 
                                            data-text="{{ $wallet->name }} ({{ $wallet->symbol }}{{ $wallet->network ? ' - '.$wallet->network : '' }})" 
                                            data-icon="{{ strtolower($wallet->symbol) }}">
                                            <x-asset-logo :symbol="$wallet->symbol" size="28" />
                                            <div class="d-flex flex-column">
                                                <span class="text-white fw-bold">{{ $wallet->name }} <span class="text-secondary small">({{ $wallet->symbol }})</span></span>
                                                @if($wallet->network)
                                                    <small class="text-primary fw-bold" style="font-size: 0.6rem; letter-spacing: 0.5px;">{{ $wallet->network }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="p-4 text-center">
                                        <i class="ri-error-warning-line text-warning mb-2" style="font-size: 1.5rem;"></i>
                                        <p class="text-secondary small mb-0">No active assets available for disbursement. Contact support.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="small text-secondary mb-2 text-uppercase tracking-widest fw-bold" style="font-size: 0.65rem;">Terminal Address</label>
                        <input type="text" name="address" class="form-control premium-input" placeholder="0x... or network identifier" required>
                    </div>

                    <div class="form-group mb-4">
                        <label class="small text-secondary mb-2 text-uppercase tracking-widest fw-bold" style="font-size: 0.65rem;">Volume (USD Equivalent)</label>
                        <div class="position-relative">
                            <span class="position-absolute start-0 top-50 translate-middle-y ps-3 text-secondary outfit fw-bold">$</span>
                            <input type="number" name="amount" id="crypto-amount-input" class="form-control premium-input ps-5" placeholder="0.00" step="any" required>
                        </div>
                        <div class="text-end mt-2">
                            <button type="button" class="btn btn-link text-primary p-0 fw-bold small text-decoration-none" style="font-size: 0.7rem; letter-spacing: 0.5px;" onclick="$('#crypto-amount-input').val({{ auth()->user()->balance->amount ?? 0 }})">MAX UTILIZATION</button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-premium w-100 py-3 shadow-glow">Authorize Dispatch</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- 2. Bank Settlement Modal -->
<div class="modal fade" id="bankModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-panel p-4 spatial-depth">
            <!-- Decorative Glow -->
            <div class="position-absolute" style="bottom: -50px; left: -50px; width: 150px; height: 150px; background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%); pointer-events: none;"></div>

            <div class="modal-header border-0 p-0 mb-4 align-items-center">
                <div class="d-flex align-items-center">
                    <div class="floating-element me-3">
                        <i class="ri-bank-fill text-info" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <h4 class="outfit fw-bold text-white mb-0">Fiat Settlement</h4>
                        <p class="text-secondary small mb-0">Institutional banking exit</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white opacity-50" data-dismiss="modal" aria-label="Close" style="background: none; border: none; font-size: 1.5rem; line-height: 1;">&times;</button>
            </div>

            <div class="modal-body p-0">
                @if(auth()->user()->bank)
                <div class="glass-panel p-4 mb-4" style="background: rgba(6, 182, 212, 0.03); border: 1px solid rgba(6, 182, 212, 0.1); border-radius: 28px;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box glass-panel" style="width: 54px; height: 54px; background: rgba(6, 182, 212, 0.1); border-radius: 18px; display: flex; align-items: center; justify-content: center; color: #06b6d4; font-size: 1.5rem;">
                            <i class="ri-community-fill"></i>
                        </div>
                        <div>
                            <div class="text-white fw-bold outfit fs-5">{{ auth()->user()->bank }}</div>
                            <div class="text-secondary small fw-medium">ID: ****{{ substr(auth()->user()->account_number, -4) }}</div>
                        </div>
                    </div>
                </div>
                <form id="bank-withdraw-form-modal" method="POST" action="{{ route('withdrawal.bank') }}">
                    @csrf
                    <div class="form-group mb-4 text-center">
                        <label class="small text-secondary mb-3 text-uppercase tracking-widest fw-bold" style="font-size: 0.65rem;">Settlement Quantum (USD)</label>
                        <div class="position-relative d-inline-block w-100">
                            <span class="position-absolute start-50 translate-middle-x mt-n4 text-primary outfit fw-bold" style="font-size: 0.8rem;">USD</span>
                            <input type="number" name="amount" class="form-control premium-input text-center outfit mb-0" placeholder="0.00" step="any" required style="font-size: 2.2rem; font-weight: 800; background: transparent !important; border: none !important; border-bottom: 2px solid rgba(255,255,255,0.05) !important; border-radius: 0 !important; padding: 10px !important;">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-premium w-100 py-3 shadow-glow mt-2">Execute Transfer</button>
                </form>
                @else
                <div class="text-center py-5">
                    <div class="mb-4 d-inline-block p-4 rounded-circle floating-element" style="background: rgba(245, 158, 11, 0.05); border: 1px solid rgba(245, 158, 11, 0.1);">
                        <i class="ri-bank-card-fill text-warning" style="font-size: 3.5rem;"></i>
                    </div>
                    <h5 class="text-white fw-bold outfit">Onboarding Incomplete</h5>
                    <p class="text-secondary small mb-4 opacity-75 px-4">Institutional settlement credentials are not configured. Please finalize your routing details in the security module.</p>
                    <a href="{{ route('profile') }}" class="btn btn-outline-primary px-5 py-3 rounded-pill fw-bold" style="border-width: 2px; transition: 0.3s;">
                        <i class="ri-shield-user-line me-2"></i> PROFILE MODULE
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- 3. Internal Transfer Modal -->
<div class="modal fade" id="transferModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-panel p-4 spatial-depth">
            <!-- Decorative Glow -->
            <div class="position-absolute" style="top: -30px; left: -30px; width: 120px; height: 120px; background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%); pointer-events: none;"></div>

            <div class="modal-header border-0 p-0 mb-4 align-items-center">
                <div class="d-flex align-items-center">
                    <div class="floating-element me-3" style="animation-delay: 1s;">
                        <i class="ri-user-shared-fill text-primary" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <h4 class="outfit fw-bold text-white mb-0">P2P Settlement</h4>
                        <p class="text-secondary small mb-0">Intra-platform capital dispatch</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white opacity-50" data-dismiss="modal" aria-label="Close" style="background: none; border: none; font-size: 1.5rem; line-height: 1;">&times;</button>
            </div>

            <div class="modal-body p-0">
                <form id="transfer-form-modal" method="POST" action="{{ route('withdrawal.transfer') }}">
                    @csrf
                    <div class="form-group mb-4">
                        <label class="small text-secondary mb-2 text-uppercase tracking-widest fw-bold" style="font-size: 0.65rem;">Recipient Node (Email)</label>
                        <input type="email" name="email" class="form-control premium-input" placeholder="node-identifier@p2b.com" required>
                    </div>
                    <div class="form-group mb-4">
                        <label class="small text-secondary mb-2 text-uppercase tracking-widest fw-bold" style="font-size: 0.65rem;">Transfer Volume (USD)</label>
                        <div class="position-relative">
                            <span class="position-absolute start-0 top-50 translate-middle-y ps-3 text-secondary outfit fw-bold">$</span>
                            <input type="number" name="amount" class="form-control premium-input ps-5" placeholder="0.00" step="any" required>
                        </div>
                    </div>
                    <div class="glass-panel p-3 mb-4 d-flex align-items-center gap-3" style="background: rgba(99, 102, 241, 0.05); border: 1px solid rgba(99, 102, 241, 0.1); border-radius: 20px;">
                        <i class="ri-flashlight-line text-primary fs-4 pulse-glow"></i>
                        <span class="text-secondary small fw-medium">Real-time internal settlement active. Zero latency execution.</span>
                    </div>
                    <button type="submit" class="btn btn-premium w-100 py-3 shadow-glow">Dispatch Capital</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- 4. Global Security Verification Modal -->
<div class="modal fade" id="security_modal_global" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-panel p-5 overflow-hidden spatial-depth" style="background: rgba(2, 6, 23, 0.95) !important; border: 1px solid rgba(59, 130, 246, 0.2) !important;">
            <div id="modal-sec-step-1">
                <div class="text-center mb-4">
                    <div class="d-inline-block p-4 rounded-circle mb-4 pulse-glow" style="background: rgba(59,130,246,0.05); border: 1px solid rgba(59,130,246,0.2);">
                        <i class="ri-shield-keyhole-line text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h3 class="outfit fw-bold text-white mb-2">Clearance Protocol</h3>
                    <p class="text-secondary small" id="global-code-label">Consensus required. Please input your <strong class="text-white">Security Identifier</strong>.</p>
                </div>
                <div class="form-group mb-5">
                    <input type="text" id="global-verify-code" class="form-control premium-input text-center" placeholder="••••••" style="font-size: 2.5rem; letter-spacing: 12px; font-weight: 900; background: rgba(0,0,0,0.3) !important; border-bottom: 2px solid rgba(59,130,246,0.3) !important; border-radius: 0 !important;">
                </div>
                <button class="btn btn-premium w-100 py-3 shadow-glow" onclick="validateGlobalCode()">Confirm Identity</button>
                <div class="text-center mt-4">
                    <button type="button" class="btn btn-link text-secondary btn-sm text-decoration-none fw-bold" data-dismiss="modal" style="letter-spacing: 1px;">ABORT TRANSACTION</button>
                </div>
            </div>
            
            <div id="modal-sec-step-questions" class="d-none">
                <div class="text-center mb-4">
                    <h4 class="outfit fw-bold text-white">Verification Challenge</h4>
                    <p class="text-secondary small">Cryptographic consensus required. Answer security queries.</p>
                </div>
                <form id="global-question-form">
                    @csrf
                    <div class="mb-4">
                        <label class="small text-secondary mb-2 text-uppercase tracking-widest fw-bold">{{ auth()->user()->security->question_one ?? 'Verification Query 1' }}</label>
                        <input type="text" name="answer_one" class="form-control premium-input" required>
                    </div>
                    <div class="mb-4">
                        <label class="small text-secondary mb-2 text-uppercase tracking-widest fw-bold">{{ auth()->user()->security->question_two ?? 'Verification Query 2' }}</label>
                        <input type="text" name="answer_two" class="form-control premium-input" required>
                    </div>
                    <div class="mb-5">
                        <label class="small text-secondary mb-2 text-uppercase tracking-widest fw-bold">{{ auth()->user()->security->question_three ?? 'Verification Query 3' }}</label>
                        <input type="text" name="answer_three" class="form-control premium-input" required>
                    </div>
                    <button type="submit" class="btn btn-premium w-100 py-3 shadow-glow">Unlock Settlement</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- 5. 2FA Verification Modal -->
<div class="modal fade" id="2fa_withdraw_modal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-panel p-5 text-center spatial-depth" style="background: rgba(10, 15, 30, 0.95) !important;">
            <div class="mb-5">
                <div class="icon-box mx-auto mb-4 pulse-glow" style="width: 80px; height: 80px; border-radius: 24px; display: flex; align-items: center; justify-content: center; background: rgba(14, 165, 233, 0.1); border: 1px solid rgba(14, 165, 233, 0.2);">
                    <i class="ri-shield-user-line text-primary" style="font-size: 2.5rem;"></i>
                </div>
                <h4 class="outfit fw-bold text-white mb-2">Authenticator Verification</h4>
                <p class="text-secondary small">Synchronize your terminal. Enter the 6-digit cryptographic sequence.</p>
            </div>
            
            <form id="2fa-withdraw-verify-form">
                <div class="form-group mb-5">
                    <input type="text" id="2fa_withdraw_code" class="form-control premium-input text-center" placeholder="000 000" maxlength="6" style="font-size: 2.5rem; letter-spacing: 8px; font-weight: 900; background: transparent !important; border: none !important; border-bottom: 2px solid rgba(14, 165, 233, 0.3) !important; border-radius: 0 !important;">
                </div>
                <button type="submit" class="btn btn-premium w-100 py-3 shadow-glow">Verify sequence</button>
                <div class="mt-4">
                    <button type="button" class="btn btn-link text-secondary btn-sm text-decoration-none fw-bold" data-dismiss="modal">CANCEL</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
<script>
(function waitForjQuery() {
    console.log("Antigravity: Checking for jQuery/AdminWallets...");
    console.log("AdminWallets Count: {{ isset($admin_wallets) ? count($admin_wallets) : 'undefined' }}");

    if (typeof $ === 'undefined' || typeof jQuery === 'undefined') {
        return setTimeout(waitForjQuery, 50);
    }
    if (typeof activeFlow === 'undefined') {
        var activeFlow = null;
        var currentCodeStep = 1;
        var pendingWithdrawData = null;
        var pendingWithdrawUrl = null;

        // AJAX Global Handlers
        function setupModalListeners() {
            $('#crypto-withdraw-form-modal').off('submit').on('submit', function(e) {
                e.preventDefault();
                activeFlow = 'crypto';
                pendingWithdrawUrl = "{{ route('withdraw.post') }}";
                pendingWithdrawData = new FormData(this);
                handleGlobalWithdrawal(pendingWithdrawUrl, pendingWithdrawData);
            });

            $('#bank-withdraw-form-modal').off('submit').on('submit', function(e) {
                e.preventDefault();
                activeFlow = 'bank';
                pendingWithdrawUrl = "{{ route('withdrawal.bank') }}";
                pendingWithdrawData = new FormData(this);
                handleGlobalWithdrawal(pendingWithdrawUrl, pendingWithdrawData);
            });

            $('#transfer-form-modal').off('submit').on('submit', function(e) {
                e.preventDefault();
                activeFlow = 'transfer';
                const formData = {
                    email: $(this).find('[name=email]').val(),
                    amount: $(this).find('[name=amount]').val(),
                    _token: "{{ csrf_token() }}"
                };
                handleTransfer(formData);
            });

            $('#2fa-withdraw-verify-form').on('submit', function(e) {
                e.preventDefault();
                const code = $('#2fa_withdraw_code').val();
                if (code.length < 6) return toastr.error('Invalid 6-digit code');

                if (activeFlow === 'transfer') {
                    pendingWithdrawData.two_fa_code = code;
                    handleTransfer(pendingWithdrawData);
                } else {
                    pendingWithdrawData.append('two_fa_code', code);
                    handleGlobalWithdrawal(pendingWithdrawUrl, pendingWithdrawData);
                }
            });

            $('#global-question-form').off('submit').on('submit', function(e) {
                e.preventDefault();
                fetch("{{ route('withdrawal.verify-security-question') }}", {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                    body: JSON.stringify({
                        question_one: $(this).find('[name=answer_one]').val(),
                        question_two: $(this).find('[name=answer_two]').val(),
                        question_three: $(this).find('[name=answer_three]').val(),
                        _token: "{{ csrf_token() }}"
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'success') {
                        toastr.success(data.message);
                        setTimeout(() => window.location.href = "{{ route('withdraw.history') }}", 1500);
                    } else {
                        toastr.error(data.message);
                    }
                });
            });
        }

        function handleTransfer(data) {
            fetch("{{ route('withdrawal.transfer') }}", {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(dataResponse => {
                if(dataResponse.two_fa_required) {
                    pendingWithdrawData = data;
                    $('.modal').modal('hide');
                    $('#2fa_withdraw_modal').modal('show');
                } else if(dataResponse.status === 'true') {
                    toastr.info(dataResponse.message);
                    $('.modal').modal('hide');
                    $('#modal-sec-step-1').addClass('d-none');
                    $('#modal-sec-step-questions').removeClass('d-none');
                    $('#security_modal_global').modal('show');
                } else {
                    toastr.error(dataResponse.message || dataResponse.error);
                }
            });
        }

        function handleGlobalWithdrawal(url, formData) {
            let form = activeFlow === 'bank' ? $('#bank-withdraw-form-modal') : $('#crypto-withdraw-form-modal');
            let btn = form.find('button[type="submit"]');
            let origText = btn.html();
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');

            fetch(url, {
                method: 'POST',
                headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                btn.prop('disabled', false).html(origText);
                if(data.two_fa_required) {
                    pendingWithdrawData = formData;
                    pendingWithdrawUrl = url;
                    $('.modal').modal('hide');
                    $('#2fa_withdraw_modal').modal('show');
                } else if(data.status === 'no_code') {
                    toastr.success(data.message || 'Withdrawal request submitted successfully');
                    setTimeout(() => window.location.href = "{{ route('withdraw.history') }}", 2000);
                } else if(data.on) {
                    $('.modal').modal('hide');
                    $('#modal-sec-step-1').removeClass('d-none');
                    $('#modal-sec-step-questions').addClass('d-none');
                    $('#global-code-label').html(`Please enter your <strong class="text-white">${data.on.label_one}</strong> to proceed.`);
                    $('#security_modal_global').modal('show');
                } else if(data.off) {
                    toastr.error('Withdrawals are currently disabled for your account. Please contact support.');
                } else {
                    toastr.error(data.message || data.error || 'Withdrawal failed');
                }
            })
            .catch(err => {
                btn.prop('disabled', false).html(origText);
                toastr.error('Network error occurred. Please try again.');
            });
        }

        function validateGlobalCode() {
            const code = $('#global-verify-code').val();
            let url = activeFlow === 'bank' ? "{{ route('checkforcode_one_bank') }}" : "{{ route('withdrawal.check-code-1') }}";
            
            fetch(url, {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                body: JSON.stringify({code, _token: "{{ csrf_token() }}"})
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'no_code' || data.status === true && !data.tax_code_check && !data.demorage_check) {
                    toastr.success('Authorization Successful');
                    setTimeout(() => window.location.href = "{{ route('withdraw.history') }}", 1500);
                } else if(data.status === true) {
                    let nextLabel = "";
                    if(data.tax_code_check === 'on' && currentCodeStep === 1) {
                        nextLabel = data.label_two;
                        currentCodeStep = 2;
                    } else if(data.demorage_check === 'on') {
                        nextLabel = data.label_three;
                        currentCodeStep = 3;
                    }

                    if(nextLabel) {
                        toastr.success('Stage Clearance Verified');
                        $('#global-verify-code').val('');
                        $('#global-code-label').html(`Verification advanced. Please enter your <strong class="text-white">${nextLabel}</strong> to proceed.`);
                    } else {
                        toastr.success('Authorization Successful');
                        window.location.href = "{{ route('withdraw.history') }}";
                    }
                } else {
                    toastr.error('Security Verification Failed.');
                }
            })
            .catch(err => {
                console.error("Verification error:", err);
                toastr.error('Connection timed out. Please verify your internet and try again.');
            });
        }

            // Custom Crypto Dropdown Logic
            $('#crypto-options .custom-option').on('click', function() {
                let val = $(this).data('value');
                let text = $(this).data('text');
                let icon = $(this).data('icon');
                
                $('#crypto-network-select').val(val);
                $('#crypto-network-selected').html(`
                    <img src="{{ url('/api/stock-logo') }}/${icon}" width="22" height="22" style="border-radius:50%;">
                    <span class="text-white">${text}</span>
                `);
                $('#crypto-options').hide();
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('#crypto-select-wrapper').length) {
                    $('#crypto-options').hide();
                }
            });

        $(document).ready(setupModalListeners);
    }
})();
</script>
@endpush

