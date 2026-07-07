@extends('layouts.user.app')

@section('title', 'Deposit History')

@section('content')
<style>
    :root {
        --dark-bg: #0b0f19;
        --gold-primary: #990000;
        --gold-light: #f3e5ab;
        --gold-dark: #aa8c2c;
        --glass-bg: rgba(20, 25, 40, 0.6);
        --glass-border: rgba(153, 0, 0, 0.15);
        --text-muted: #8b9bb4;
    }
    
    body {
        background-color: var(--dark-bg);
        color: #fff;
    }

    .glass-card {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }

    .gold-text {
        color: var(--gold-primary);
    }
    
    .gold-gradient-text {
        background: linear-gradient(135deg, var(--gold-light), var(--gold-primary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .btn-gold {
        background: linear-gradient(135deg, var(--gold-primary), var(--gold-dark));
        color: #111 !important;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
    }

    .btn-gold:hover, .btn-gold:focus {
        background: linear-gradient(135deg, var(--gold-light), var(--gold-primary));
        box-shadow: 0 0 15px rgba(153, 0, 0, 0.4);
        transform: translateY(-2px);
    }
    
    .btn-gold-outline {
        background: transparent;
        color: var(--gold-primary) !important;
        border: 1px solid var(--gold-primary);
        border-radius: 12px;
        font-weight: 600;
    }

    .method-icon-gold {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        background: rgba(153, 0, 0, 0.1);
        color: var(--gold-primary);
        margin: 0 auto 12px;
        border: 1px solid rgba(153, 0, 0, 0.2);
    }

    /* Form Inputs */
    .premium-input {
        background: rgba(10, 15, 25, 0.7) !important;
        border: 1px solid rgba(153, 0, 0, 0.2) !important;
        color: #fff !important;
        border-radius: 10px;
    }
    .premium-input:focus {
        border-color: var(--gold-primary) !important;
        box-shadow: 0 0 0 0.25rem rgba(153, 0, 0, 0.1) !important;
    }
    .premium-input::placeholder {
        color: rgba(255,255,255,0.3) !important;
    }

    .ledger-mobile-card {
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
    }
    .ledger-mobile-card:hover {
        background: rgba(25, 30, 45, 0.8);
        border-left-color: var(--gold-primary);
    }
    
    .status-badge {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .status-success { background: rgba(255, 51, 51, 0.1); color: #ff3333; border: 1px solid rgba(255, 51, 51, 0.3); }
    .status-pending { background: rgba(153, 0, 0, 0.1); color: var(--gold-primary); border: 1px solid rgba(153, 0, 0, 0.3); }
    .status-failed { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }
    
    .hash-link {
        color: var(--gold-light);
        text-decoration: none;
        font-family: monospace;
        font-size: 0.8rem;
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

<div class="container-fluid py-3 px-3">
    <!-- Header Area -->
    <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-down">
        <div>
            <h4 class="outfit font-weight-bold mb-0 text-white">Deposit</h4>
            <span class="small" style="color: var(--text-muted);">Add funds to your portfolio</span>
        </div>
        <a href="{{ route('deposit') }}" class="btn btn-gold btn-sm px-3 py-2 d-flex align-items-center">
            <i class="ri-add-line me-1"></i> New
        </a>
    </div>

    <!-- Stats Overview -->
    <div class="row g-3 mb-4">
        <div class="col-6" data-aos="fade-right">
            <div class="glass-card p-3 text-center h-100">
                <div class="small" style="color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Funded</div>
                <div class="h5 mb-0 mt-1 outfit font-weight-bold gold-gradient-text">${{ number_format($data->where('status', 'success')->sum('amount'), 2) }}</div>
            </div>
        </div>
        <div class="col-6" data-aos="fade-left">
            <div class="glass-card p-3 text-center h-100">
                <div class="small" style="color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Pending</div>
                <div class="h5 mb-0 mt-1 outfit font-weight-bold text-white">${{ number_format($data->where('status', 'pending')->sum('amount'), 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Quick Methods -->
    <h6 class="outfit font-weight-bold text-white mb-3" data-aos="fade-up">Funding Methods</h6>
    <div class="row g-3 mb-4" data-aos="fade-up" data-aos-delay="100">
        <div class="col-12">
            <a href="{{ route('deposit') }}" class="text-decoration-none">
                <div class="glass-card p-3 d-flex align-items-center">
                    <div class="method-icon-gold mb-0 me-3 flex-shrink-0">
                        <i class="ri-bit-coin-line"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="outfit font-weight-bold mb-1 text-white">Crypto Wallet</h6>
                        <p class="mb-0" style="font-size: 0.75rem; color: var(--text-muted);">BTC, ETH, USDT • Instant</p>
                    </div>
                    <i class="ri-arrow-right-s-line" style="color: var(--text-muted);"></i>
                </div>
            </a>
        </div>
        <div class="col-12">
            <div class="glass-card p-3 d-flex align-items-center" onclick="$('#cardModal').modal('show')">
                <div class="method-icon-gold mb-0 me-3 flex-shrink-0">
                    <i class="ri-bank-card-line"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="outfit font-weight-bold mb-1 text-white">Credit/Debit Card</h6>
                    <p class="mb-0" style="font-size: 0.75rem; color: var(--text-muted);">Visa, Mastercard • Instant</p>
                </div>
                <i class="ri-arrow-right-s-line" style="color: var(--text-muted);"></i>
            </div>
        </div>
        <div class="col-12">
            <div class="glass-card p-3 d-flex align-items-center" onclick="$('#wireModal').modal('show')">
                <div class="method-icon-gold mb-0 me-3 flex-shrink-0">
                    <i class="ri-bank-line"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="outfit font-weight-bold mb-1 text-white">Bank Wire</h6>
                    <p class="mb-0" style="font-size: 0.75rem; color: var(--text-muted);">Wire Transfer • 1-3 Days</p>
                </div>
                <i class="ri-arrow-right-s-line" style="color: var(--text-muted);"></i>
            </div>
        </div>
    </div>

    <!-- History List -->
    <h6 class="outfit font-weight-bold text-white mb-3" data-aos="fade-up" data-aos-delay="200">Recent Transactions</h6>
    <div class="d-flex flex-column gap-3 pb-5" data-aos="fade-up" data-aos-delay="300">
        @forelse($data as $d)
            @php
                $statusClass = [
                    'success' => 'status-success',
                    'pending' => 'status-pending',
                    'failed' => 'status-failed',
                ][strtolower($d->status)] ?? 'bg-secondary text-white';
            @endphp
            <div class="glass-card p-3 ledger-mobile-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-1 rounded bg-dark border border-secondary border-opacity-25 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                            <x-asset-logo :symbol="$d->pay_currency ?? 'USD'" size="16" />
                        </div>
                        <span class="text-white fw-bold outfit">{{ strtoupper($d->pay_currency ?? 'USD') }}</span>
                    </div>
                    <span class="status-badge {{ $statusClass }}">
                        {{ strtoupper($d->status) }}
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-end mt-3">
                    <div>
                        <div style="font-size: 0.7rem; color: var(--text-muted);">{{ \Carbon\Carbon::parse($d->created_at)->format('d M, Y H:i') }}</div>
                        <div class="mt-1"><span class="hash-link">{{ $d->trx_id }}</span></div>
                    </div>
                    <div class="text-end">
                        <div style="font-size: 0.7rem; color: var(--text-muted);">Amount</div>
                        <div class="fw-bold text-white">${{ number_format($d->amount, 2) }}</div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 glass-card">
                <i class="ri-inbox-line display-4 d-block opacity-25 mb-2 gold-text"></i>
                <span style="color: var(--text-muted); font-size: 0.9rem;">No inbound funding history identified.</span>
            </div>
        @endforelse
    </div>
</div>

<!-- Credit Card Processing Modal -->
<div class="modal fade" id="cardModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content glass-card p-3 p-md-4" style="border: 1px solid var(--gold-primary);">
            <div id="card-form-container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="outfit font-weight-bold mb-0 gold-text">Card Gateway</h5>
                    <button type="button" class="btn-close btn-close-white" data-dismiss="modal"></button>
                </div>
                
                <!-- Visual Card Preview -->
                <div class="mb-4" style="position: relative; border-radius: 14px; overflow: hidden; min-height: 180px; box-shadow: 0 10px 20px rgba(0,0,0,0.5); border: 1px solid rgba(153,0,0,0.3);">
                    <!-- Dark gold gradient for the card background -->
                    <div style="width: 100%; height: 100%; position: absolute; top: 0; left: 0; background: linear-gradient(135deg, #1f1c18, #4d3a0f);"></div>
                    <div style="position: absolute; top: 20px; right: 20px; color: var(--gold-primary); font-size: 24px;">
                        <i class="ri-visa-line"></i>
                    </div>
                    <div style="position: absolute; top: 20px; left: 20px; color: var(--gold-primary); font-size: 28px; opacity: 0.8;">
                        <i class="ri-rfid-line"></i>
                    </div>
                    <div style="position: absolute; bottom: 45px; left: 20px; font-family: 'Courier New', monospace; font-size: 16px; letter-spacing: 2px; color: #fff; z-index: 2;" id="modalCardPreviewNumber">
                        •••• •••• •••• ••••
                    </div>
                    <div style="position: absolute; bottom: 15px; left: 20px; display: flex; gap: 20px; z-index: 2;">
                        <div>
                            <div style="font-size: 7px; text-transform: uppercase; color: var(--gold-primary);">Card Holder</div>
                            <div class="font-weight-bold text-white text-uppercase" style="font-size: 10px;" id="modalCardPreviewName">YOUR NAME</div>
                        </div>
                        <div>
                            <div style="font-size: 7px; text-transform: uppercase; color: var(--gold-primary);">Expires</div>
                            <div class="font-weight-bold text-white" style="font-size: 10px;" id="modalCardPreviewExpiry">MM/YY</div>
                        </div>
                    </div>
                </div>
                
                <form id="card-submit-form">
                    <div class="form-group mb-3">
                        <label class="small mb-1" style="color: var(--text-muted); font-size: 0.75rem;">CARDHOLDER NAME</label>
                        <input type="text" class="form-control premium-input py-2" id="modalInputName" placeholder="As shown on card" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="small mb-1" style="color: var(--text-muted); font-size: 0.75rem;">CARD NUMBER</label>
                        <input type="text" class="form-control premium-input py-2" id="modalInputNumber" placeholder="xxxx xxxx xxxx xxxx" maxlength="19" required>
                    </div>
                    <div class="row g-2">
                        <div class="col-7">
                            <div class="form-group mb-3">
                                <label class="small mb-1" style="color: var(--text-muted); font-size: 0.75rem;">EXPIRY</label>
                                <input type="text" class="form-control premium-input py-2" id="modalInputExpiry" placeholder="MM/YY" maxlength="5" required>
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="form-group mb-3">
                                <label class="small mb-1" style="color: var(--text-muted); font-size: 0.75rem;">CVV</label>
                                <input type="password" class="form-control premium-input py-2" placeholder="•••" maxlength="4" required>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-gold w-100 py-3 mt-2">
                        <i class="ri-lock-2-line me-1"></i> Proceed to Payment
                    </button>
                </form>
            </div>

            <!-- Loading State -->
            <div id="card-loading-container" style="display: none; height: 350px;" class="flex-column align-items-center justify-content-center text-center">
                <div class="spinner-box mb-4">
                    <div class="circle-border">
                        <div class="circle-core"></div>
                    </div>
                </div>
                <h5 class="outfit font-weight-bold mb-2 gold-text">Processing Transaction</h5>
                <p class="small mb-4" style="color: var(--text-muted);">Verifying card details with issuing bank...</p>
                <div class="w-100 px-4">
                    <div class="progress" style="height: 6px; background: rgba(255,255,255,0.05); border-radius: 10px; overflow: hidden;">
                        <div id="card-progress-bar" class="progress-bar" role="progressbar" style="width: 0%; background: var(--gold-primary); transition: width 0.3s ease;"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <span class="small" style="color: var(--text-muted); font-size: 0.7rem;" id="loading-status-text">Connecting to gateway...</span>
                        <span class="small font-weight-bold gold-text" id="progress-percent">0%</span>
                    </div>
                </div>
            </div>

            <!-- Failure State -->
            <div id="card-failure-container" style="display: none; height: 350px;" class="flex-column align-items-center justify-content-center text-center">
                <div class="bg-danger-soft text-danger rounded-circle p-3 mb-3 border border-danger">
                    <i class="ri-error-warning-line" style="font-size: 3rem;"></i>
                </div>
                <h5 class="outfit font-weight-bold text-white mb-2">Authentication Failed</h5>
                <p class="mb-4" style="color: var(--text-muted); font-size: 0.85rem;">The issuing bank could not verify this card for high-frequency trading capital. Please use Crypto or Bank Wire.</p>
                <div class="d-flex gap-2 w-100">
                    <button class="btn btn-gold-outline flex-fill" data-dismiss="modal">Close</button>
                    <button class="btn btn-gold flex-fill" onclick="switchMethod('crypto')">Try Crypto</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bank Wire Request Modal -->
<div class="modal fade" id="wireModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content glass-card p-3 p-md-4" style="border: 1px solid var(--gold-primary);">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="outfit font-weight-bold mb-0 gold-text">Bank Wire Request</h5>
                <button type="button" class="btn-close btn-close-white" data-dismiss="modal"></button>
            </div>
            <p class="small mb-4" style="color: var(--text-muted); font-size: 0.8rem;">Submit your details below. Our team will contact you within 24 hours with wire transfer instructions.</p>

            <form id="wire-form">
                @csrf
                <div class="form-group mb-3">
                    <label class="small mb-1" style="color: var(--text-muted); font-size: 0.75rem;">FULL NAME</label>
                    <input type="text" class="form-control premium-input py-2" value="{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}" name="full_name" required>
                </div>
                <div class="form-group mb-3">
                    <label class="small mb-1" style="color: var(--text-muted); font-size: 0.75rem;">EMAIL ADDRESS</label>
                    <input type="email" class="form-control premium-input py-2" value="{{ auth()->user()->email }}" name="email" required>
                </div>
                <div class="form-group mb-3">
                    <label class="small mb-1" style="color: var(--text-muted); font-size: 0.75rem;">PHONE NUMBER</label>
                    <input type="text" class="form-control premium-input py-2" value="{{ auth()->user()->phone }}" name="phone" required>
                </div>
                <div class="form-group mb-3">
                    <label class="small mb-1" style="color: var(--text-muted); font-size: 0.75rem;">DEPOSIT AMOUNT (USD)</label>
                    <input type="number" class="form-control premium-input py-2" name="amount" placeholder="e.g. 5000" min="100" required>
                </div>
                <div class="form-group mb-4">
                    <label class="small mb-1" style="color: var(--text-muted); font-size: 0.75rem;">ADDITIONAL NOTES (OPTIONAL)</label>
                    <textarea class="form-control premium-input py-2" name="notes" rows="2" placeholder="Country, preferred bank, etc."></textarea>
                </div>

                <div class="p-3 rounded mb-4" style="background: rgba(153, 0, 0, 0.1); border: 1px solid rgba(153, 0, 0, 0.2);">
                    <div class="d-flex align-items-start gap-2">
                        <i class="ri-information-line gold-text mt-1"></i>
                        <span class="small" style="color: var(--text-muted); font-size: 0.75rem;">Our treasury team will review your request and send wire instructions to your registered email.</span>
                    </div>
                </div>

                <button type="submit" class="btn btn-gold w-100 py-3 font-weight-bold" id="wire-submit-btn">
                    Submit Wire Request
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    .circle-border { width: 60px; height: 60px; padding: 3px; display: flex; justify-content: center; align-items: center; border-radius: 50%; background: linear-gradient(0deg, rgba(153, 0, 0, 0.1) 33%, var(--gold-primary) 100%); animation: spin .8s linear infinite; }
    .circle-core { width: 100%; height: 100%; background-color: var(--dark-bg); border-radius: 50%; }
    @keyframes spin { from { transform: rotate(0); } to { transform: rotate(359deg); } }
</style>

@endsection

@push('js')
<script>
    // Credit Card Simulation
    $('#modalInputName').on('input', function(){ $('#modalCardPreviewName').text($(this).val() || 'YOUR NAME'); });
    $('#modalInputExpiry').on('input', function(){ 
        var val = $(this).val().replace(/[^\d]/g, '');
        if(val.length >= 2) val = val.substring(0,2) + '/' + val.substring(2);
        $(this).val(val);
        $('#modalCardPreviewExpiry').text(val || 'MM/YY'); 
    });
    $('#modalInputNumber').on('input', function(){
        var val = $(this).val().replace(/\s/g, '').replace(/(.{4})/g, '$1 ').trim();
        $(this).val(val);
        $('#modalCardPreviewNumber').text(val || '•••• •••• •••• ••••');
    });

    $('#card-submit-form').on('submit', function(e) {
        e.preventDefault();
        $('#card-form-container').fadeOut(200, function() {
            $('#card-loading-container').css('display', 'flex').hide().fadeIn(200);
            
            var progress = 0;
            var interval = setInterval(function() {
                progress += Math.floor(Math.random() * 5) + 1;
                if (progress > 100) progress = 100;
                
                $('#card-progress-bar').css('width', progress + '%');
                $('#progress-percent').text(progress + '%');
                
                if (progress < 30) $('#loading-status-text').text('Connecting to gateway...');
                else if (progress < 70) $('#loading-status-text').text('Verifying card metadata...');
                else if (progress < 95) $('#loading-status-text').text('Exchanging security tokens...');
                else $('#loading-status-text').text('Finishing up...');

                if (progress >= 100) {
                    clearInterval(interval);
                    setTimeout(function() {
                        $('#card-loading-container').fadeOut(200, function() {
                            $('#card-failure-container').css('display', 'flex').hide().fadeIn(200);
                        });
                    }, 1000);
                }
            }, 100);
        });
    });

    function switchMethod(method) {
        $('#cardModal').modal('hide');
        if (method === 'crypto') {
            window.location.href = "{{ route('deposit') }}";
        }
    }

    $('#wire-form').on('submit', function(e) {
        e.preventDefault();
        var btn = $('#wire-submit-btn');
        var form = $(this);
        
        btn.attr('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Submitting...');

        $.ajax({
            url: "{{ route('wire.deposit.store') }}",
            method: "POST",
            data: form.serialize(),
            success: function(response) {
                $('#wireModal').modal('hide');
                btn.removeAttr('disabled').text('Submit Wire Request');
                if(typeof toastr !== 'undefined') {
                    toastr.success(response.message || 'Your bank wire request has been submitted. Our team will contact you within 24 hours.');
                } else {
                    alert(response.message || 'Your bank wire request has been submitted. Our team will contact you within 24 hours.');
                }
                form[0].reset();
            },
            error: function(xhr) {
                btn.removeAttr('disabled').text('Submit Wire Request');
                var errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred. Please try again.';
                if(typeof toastr !== 'undefined') {
                    toastr.error(errorMsg);
                } else {
                    alert(errorMsg);
                }
            }
        });
    });
</script>
@endpush
