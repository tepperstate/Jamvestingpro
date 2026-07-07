@extends('layouts.user.app')

@section('title', 'Secure Payment Storage')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

<div class="container-fluid py-5">
    

    <div class="row g-4">
        <!-- Crypto Wallets -->
        <div class="col-lg-6 payment-card-wrapper">
            <div class="glass-card p-5 h-100 shadow-lg" style="background: rgba(16, 18, 27, 0.4); backdrop-filter: blur(20px); border-radius: 32px; border: 1px solid rgba(255,255,255,0.05);">
                <div class="d-flex align-items-center gap-4 mb-5">
                    <div class="icon-box bg-primary-soft shadow-sm" style="width: 56px; height: 56px; border-radius: 16px; background: rgba(59, 130, 246, 0.1); display: flex; align-items: center; justify-content: center;">
                        <i class="ri-bit-coin-line h3 text-primary mb-0"></i>
                    </div>
                    <h4 class="outfit font-weight-bold mb-0 text-white">Digital Asset Wallets</h4>
                </div>

                <form id="crypto-payment-form">
                    @csrf
                    <div class="form-group mb-4">
                        <label class="small text-secondary mb-2">Bitcoin (BTC) Address</label>
                        <input type="text" name="btc" class="form-control premium-input" value="{{ auth()->user()->btc }}" placeholder="1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa">
                    </div>
                    <div class="form-group mb-4">
                        <label class="small text-secondary mb-2">Ethereum (ETH) Address</label>
                        <input type="text" name="eth" class="form-control premium-input" value="{{ auth()->user()->eth }}" placeholder="0x71C7656EC7ab88b098defB751B7401B5f6d8976F">
                    </div>
                    <div class="form-group mb-4">
                        <label class="small text-secondary mb-2">Tether (USDT - TRC20) Address</label>
                        <input type="text" name="usdt" class="form-control premium-input" value="{{ auth()->user()->usdt }}" placeholder="TR7NHqjuS2pSxyQXToOTTcynGLG1aB361B">
                    </div>
                    <div class="form-group mb-4">
                        <label class="small text-secondary mb-2">Solana (SOL) Address</label>
                        <input type="text" name="susd" class="form-control premium-input" value="{{ auth()->user()->susd }}" placeholder="GUMV...UX6M">
                    </div>
                    <button type="submit" class="btn btn-premium w-100 py-3 mt-2">Secure Encrypted Wallet</button>
                </form>
            </div>
        </div>

        <!-- Bank Details -->
        <div class="col-lg-6 payment-card-wrapper">
            <div class="glass-card p-5 h-100 shadow-lg" style="background: rgba(16, 18, 27, 0.4); backdrop-filter: blur(20px); border-radius: 32px; border: 1px solid rgba(255,255,255,0.05);">
                <div class="d-flex align-items-center gap-4 mb-5">
                    <div class="icon-box bg-success-soft shadow-sm" style="width: 56px; height: 56px; border-radius: 16px; background: rgba(255, 51, 51, 0.1); display: flex; align-items: center; justify-content: center;">
                        <i class="ri-bank-line h3 text-success mb-0"></i>
                    </div>
                    <h4 class="outfit font-weight-bold mb-0 text-white">Institutional Bank Relay</h4>
                </div>

                <form id="bank-payment-form">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="small text-secondary mb-2">Bank Name</label>
                                <input type="text" name="bank" class="form-control premium-input" value="{{ auth()->user()->bank }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="small text-secondary mb-2">Account Name</label>
                                <input type="text" name="account_name" class="form-control premium-input" value="{{ auth()->user()->account_name }}">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label class="small text-secondary mb-2">Account Number / IBAN</label>
                                <input type="text" name="account_number" class="form-control premium-input" value="{{ auth()->user()->account_number }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="small text-secondary mb-2">SWIFT / BIC Code</label>
                                <input type="text" name="bank_swift_code" class="form-control premium-input" value="{{ auth()->user()->bank_swift_code }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="small text-secondary mb-2">Routing Number</label>
                                <input type="text" name="routing" class="form-control premium-input" value="{{ auth()->user()->routing }}">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label class="small text-secondary mb-2">Bank Address</label>
                                <textarea name="bank_address" class="form-control premium-input" rows="2">{{ auth()->user()->bank_address }}</textarea>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-outline-premium w-100 py-3 mt-4">Save Fiat Channels</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-primary-soft { background: rgba(59, 130, 246, 0.1); }
    .bg-success-soft { background: rgba(255, 51, 51, 0.1); }
    .icon-box { width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; }
</style>

@endsection

@push('js')
<script>
    $('#crypto-payment-form').on('submit', function(e) {
        e.preventDefault();
        submitPayment("{{ route('update_payment') }}", new FormData(this));
    });

    $('#bank-payment-form').on('submit', function(e) {
        e.preventDefault();
        submitPayment("{{ route('update_payment_bank') }}", new FormData(this));
    });

    // Staggered Entrance Animation
    window.addEventListener('load', () => {
        if (typeof anime !== 'undefined') {
            anime({
                targets: '.payment-card-wrapper',
                translateY: [40, 0],
                opacity: [0, 1],
                delay: anime.stagger(200),
                easing: 'easeOutQuint',
                duration: 1000
            });
            
            anime({
                targets: '.premium-input',
                translateX: [-20, 0],
                opacity: [0, 1],
                delay: anime.stagger(50, {start: 600}),
                easing: 'easeOutQuint',
                duration: 600
            });
        }
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
@endpush
