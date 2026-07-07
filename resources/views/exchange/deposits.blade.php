@extends('layouts.user.app')

@section('title', 'Deposit History')

@section('content')
<style>
@media (max-width: 767.98px) {
    .mobile-cards-view {
        display: flex !important;
    }
}
</style>
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

<div class="container-fluid py-4">
    <div class="row mb-5" data-aos="fade-up">
        <div class="col-xl-8"></div>
        <div class="col-xl-4 text-xl-end d-flex align-items-center justify-content-xl-end">
            <a href="{{ route('deposit') }}" class="btn btn-premium px-4 py-3">
                <i class="ri-add-circle-line me-2"></i> New Deposit
            </a>
        </div>
    </div>

    <!-- Quick Deposit Method Cards -->
    <div class="row g-5 mb-5">
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
            <a href="{{ route('deposit') }}" class="text-decoration-none">
                <div class="method-card glass-card p-4 text-center h-100">
                    <div class="method-icon bg-warning-soft text-warning mb-3 mx-auto">
                        <i class="ri-bit-coin-line"></i>
                    </div>
                    <h5 class="outfit font-weight-bold mb-1 text-white">Crypto Wallets</h5>
                    <p class="text-secondary small mb-0">BTC, ETH, USDT — instant confirmation</p>
                </div>
            </a>
        </div>
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
            <div class="method-card glass-card p-4 text-center h-100" onclick="$('#cardModal').modal('show')">
                <div class="method-icon bg-danger-soft text-danger mb-3 mx-auto">
                    <i class="ri-bank-card-line"></i>
                </div>
                <h5 class="outfit font-weight-bold mb-1">Credit / Debit Card</h5>
                <p class="text-secondary small mb-0">Visa, Mastercard — instant funding</p>
            </div>
        </div>
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
            <div class="method-card glass-card p-4 text-center h-100" style="cursor:pointer" onclick="$('#wireModal').modal('show')">
                <div class="method-icon bg-info-soft text-info mb-3 mx-auto">
                    <i class="ri-bank-line"></i>
                </div>
                <h5 class="outfit font-weight-bold mb-1">Bank Wire</h5>
                <p class="text-secondary small mb-0">Submit request — receive wire instructions</p>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-5 mb-5">
        <div class="col-md-6" data-aos="zoom-in">
            <div class="glass-card p-4">
                <div class="small text-secondary mb-1">Total Funded Capital</div>
                <div class="h3 mb-0 outfit font-weight-bold text-success">${{ number_format($data->where('status', 'success')->sum('amount'), 2) }}</div>
            </div>
        </div>
        <div class="col-md-6" data-aos="zoom-in" data-aos-delay="100">
            <div class="glass-card p-4">
                <div class="small text-secondary mb-1">Awaiting Confirmation</div>
                <div class="h3 mb-0 outfit font-weight-bold text-warning">${{ number_format($data->where('status', 'pending')->sum('amount'), 2) }}</div>
            </div>
        </div>
    </div>

    <!-- History Table -->
    <style>
        .glass-ledger {
            background: rgba(10, 15, 30, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            overflow: hidden;
        }
        .table-premium {
            background: transparent !important;
            border-collapse: separate;
            border-spacing: 0 8px;
        }
        .table-premium thead th {
            background: rgba(255, 255, 255, 0.03);
            border: none !important;
            color: #94a3b8 !important;
            text-transform: uppercase;
            font-size: 0.65rem;
            letter-spacing: 1.5px;
            font-weight: 700;
            padding: 20px !important;
        }
        .table-premium tbody tr {
            background: rgba(255, 255, 255, 0.02);
            transition: all 0.3s ease;
        }
        .table-premium tbody tr:hover {
            background: rgba(255, 255, 255, 0.05);
            transform: scale(1.002);
        }
        .table-premium td {
            border: none !important;
            padding: 18px 20px !important;
            vertical-align: middle !important;
            color: #e2e8f0;
            font-size: 0.85rem;
        }
        .hash-link {
            color: #0ea5e9;
            text-decoration: none;
            transition: all 0.2s;
            font-family: monospace;
            background: rgba(14, 165, 233, 0.05);
            padding: 4px 8px;
            border-radius: 6px;
        }
        .hash-link:hover {
            color: #38bdf8;
            background: rgba(14, 165, 233, 0.1);
        }
        .status-badge {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            min-width: 90px;
            display: inline-block;
            text-align: center;
        }
    </style>

    <div class="row" data-aos="fade-up" data-aos-delay="200">
        <div class="col-12">
            <div class="glass-ledger p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 px-2">
                    <h5 class="outfit font-weight-bold mb-0 text-white">Funding History Ledger</h5>
                </div>
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-premium text-nowrap mb-0">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Reference</th>
                                <th>Network</th>
                                <th>Volume (USD)</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="outfit">
                            @forelse($data as $d)
                            <tr>
                                <td class="text-secondary small">{{ \Carbon\Carbon::parse($d->created_at)->format('d M, Y • H:i') }}</td>
                                <td><span class="hash-link">{{ $d->trx_id }}</span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="p-1 rounded bg-dark" style="border: 1px solid rgba(255,255,255,0.05);">
                                            <x-asset-logo :symbol="$d->pay_currency ?? 'USD'" size="20" />
                                        </div>
                                        <span class="font-weight-bold text-white">{{ strtoupper($d->pay_currency ?? 'USD') }}</span>
                                    </div>
                                </td>
                                <td class="font-weight-bold text-white">${{ number_format($d->amount, 2) }}</td>
                                <td class="text-center">
                                    @php
                                        $statusClass = [
                                            'success' => 'bg-success-soft text-success border border-success border-opacity-25',
                                            'pending' => 'bg-warning-soft text-warning border border-warning border-opacity-25',
                                            'failed' => 'bg-danger-soft text-danger border border-danger border-opacity-25',
                                        ][strtolower($d->status)] ?? 'bg-secondary-soft text-secondary';
                                    @endphp
                                    @if(strtolower($d->status) === 'pending')
                                    <div class="pending-progress">
                                        <div class="d-flex justify-content-between w-100"><span class="progress-label">Processing</span><span class="progress-pct">93%</span></div>
                                        <div class="progress-track"><div class="progress-fill"></div></div>
                                    </div>
                                    @else
                                    <span class="status-badge {{ $statusClass }}">
                                        {{ strtoupper($d->status) }}
                                    </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="ri-inbox-line display-4 d-block opacity-20 mb-3 text-white"></i>
                                    <span class="text-secondary">No inbound funding history identified.</span>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards View -->
                <div class="mobile-cards-view d-md-none flex-column gap-3 mt-3">
                    @forelse($data as $d)
                    <div class="glass-card p-3 ledger-mobile-card" style="background: rgba(16, 18, 27, 0.4); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px;">
                        @php
                            $statusClassText = [
                                'success' => 'text-success',
                                'pending' => 'text-warning',
                                'failed' => 'text-danger',
                            ][strtolower($d->status)] ?? 'text-secondary';
                        @endphp
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-secondary border-opacity-25">
                            <div class="d-flex align-items-center gap-2">
                                <div class="p-1 rounded bg-dark" style="border: 1px solid rgba(255,255,255,0.05);">
                                    <x-asset-logo :symbol="$d->pay_currency ?? 'USD'" size="20" />
                                </div>
                                <div>
                                    <div class="text-white fw-bold outfit ledger-search-target">{{ strtoupper($d->pay_currency ?? 'USD') }}</div>
                                    <div class="text-secondary" style="font-size: 10px;">{{ \Carbon\Carbon::parse($d->created_at)->format('d M, Y H:i') }}</div>
                                </div>
                            </div>
                            <span class="fw-bold small text-uppercase {{ $statusClassText }}" style="letter-spacing: 0.5px;">{{ $d->status }}</span>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-12">
                                <div class="text-secondary" style="font-size: 0.7rem;">Reference</div>
                                <div><span class="hash-link" style="font-size: 0.8rem;">{{ $d->trx_id }}</span></div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between pt-2">
                            <div class="text-secondary" style="font-size: 0.75rem;">Volume</div>
                            <div class="fw-bold text-white">${{ number_format($d->amount, 2) }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="ri-inbox-line display-4 d-block opacity-20 mb-3 text-white"></i>
                        <span class="text-secondary">No inbound funding history identified.</span>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

</div>

<!-- Credit Card Processing Modal -->
<div class="modal fade" id="cardModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass-card p-4" style="border: 1px solid var(--glass-border);">
            <div id="card-form-container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="outfit font-weight-bold mb-0">Credit Card Gateway</h4>
                    <button type="button" class="btn-close btn-close-white" data-dismiss="modal"></button>
                </div>
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <!-- Visual Card Preview -->
                        <div class="mb-4" style="position: relative; border-radius: 16px; overflow: hidden; min-height: 180px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                            <img src="https://businesspost.ng/wp-content/uploads/2023/11/Visa-Card-Linked-Offers.jpg" style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0;">
                            <div style="position: absolute; bottom: 40px; left: 20px; font-family: 'Courier New', monospace; font-size: 16px; letter-spacing: 2px; color: white; text-shadow: 1px 1px 2px rgba(0,0,0,0.8); z-index: 2;" id="modalCardPreviewNumber">
                                •••• •••• •••• ••••
                            </div>
                            <div style="position: absolute; bottom: 15px; left: 20px; display: flex; gap: 20px; text-shadow: 1px 1px 1px rgba(0,0,0,0.8); z-index: 2;">
                                <div>
                                    <div style="font-size: 7px; text-transform: uppercase; color: rgba(255,255,255,0.6);">Card Holder</div>
                                    <div class="font-weight-bold text-white uppercase" style="font-size: 10px;" id="modalCardPreviewName">YOUR NAME</div>
                                </div>
                                <div>
                                    <div style="font-size: 7px; text-transform: uppercase; color: rgba(255,255,255,0.6);">Expires</div>
                                    <div class="font-weight-bold text-white" style="font-size: 10px;" id="modalCardPreviewExpiry">MM/YY</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <form id="card-submit-form">
                            <div class="form-group mb-3">
                                <label class="small text-secondary mb-1">CARDHOLDER NAME</label>
                                <input type="text" class="form-control premium-input py-2" id="modalInputName" placeholder="As shown on card" required>
                            </div>
                            <div class="form-group mb-3">
                                <label class="small text-secondary mb-1">CARD NUMBER</label>
                                <input type="text" class="form-control premium-input py-2" id="modalInputNumber" placeholder="xxxx xxxx xxxx xxxx" maxlength="19" required>
                            </div>
                            <div class="row g-2">
                                <div class="col-7">
                                    <div class="form-group mb-3">
                                        <label class="small text-secondary mb-1">EXPIRY</label>
                                        <input type="text" class="form-control premium-input py-2" id="modalInputExpiry" placeholder="MM/YY" maxlength="5" required>
                                    </div>
                                </div>
                                <div class="col-5">
                                    <div class="form-group mb-3">
                                        <label class="small text-secondary mb-1">CVV</label>
                                        <input type="password" class="form-control premium-input py-2" placeholder="•••" maxlength="4" required>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-premium w-100 py-2 font-weight-bold mt-2">
                                <i class="ri-lock-2-line me-1"></i> Proceed to Payment
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div id="card-loading-container" style="display: none; height: 350px;" class="flex-column align-items-center justify-content-center text-center">
                <div class="spinner-box mb-4">
                    <div class="circle-border">
                        <div class="circle-core"></div>
                    </div>
                </div>
                <h4 class="outfit font-weight-bold mb-2">Processing Transaction</h4>
                <p class="text-secondary small mb-4">Verifying card details with issuing bank...</p>
                <div class="w-100 px-5">
                    <div class="progress" style="height: 6px; background: rgba(255,255,255,0.05); border-radius: 10px; overflow: hidden;">
                        <div id="card-progress-bar" class="progress-bar bg-primary progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <span class="text-secondary small" id="loading-status-text">Connecting to gateway...</span>
                        <span class="text-white small font-weight-bold" id="progress-percent">0%</span>
                    </div>
                </div>
            </div>

            <!-- Failure State -->
            <div id="card-failure-container" style="display: none; height: 350px;" class="flex-column align-items-center justify-content-center text-center">
                <div class="bg-danger-soft text-danger rounded-circle p-4 mb-4">
                    <i class="ri-error-warning-line" style="font-size: 4rem;"></i>
                </div>
                <h3 class="outfit font-weight-bold text-white mb-2">Authentication Failed</h3>
                <p class="text-secondary mb-5" style="max-width: 300px;">The issuing bank could not verify this card for high-frequency trading capital. Please use Crypto or Bank Wire.</p>
                <div class="d-flex gap-3">
                    <button class="btn btn-outline-light px-4" data-dismiss="modal">Close</button>
                    <button class="btn btn-premium px-4" onclick="switchMethod('crypto')">Try Crypto</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bank Wire Request Modal -->
<div class="modal fade" id="wireModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card p-4" style="border: 1px solid var(--glass-border);">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="outfit font-weight-bold mb-0">Bank Wire Request</h4>
                <button type="button" class="btn-close btn-close-white" data-dismiss="modal"></button>
            </div>
            <p class="text-secondary small mb-4">Submit your details below. Our team will contact you within 24 hours with wire transfer instructions.</p>

            <form id="wire-form">
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

                <button type="submit" class="btn btn-premium w-100 py-3 font-weight-bold" id="wire-submit-btn">
                    Submit Wire Request
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    .circle-border { width: 80px; height: 80px; padding: 3px; display: flex; justify-content: center; align-items: center; border-radius: 50%; background: linear-gradient(0deg, rgba(63, 249, 220, 0.1) 33%, var(--accent-primary) 100%); animation: spin .8s linear infinite; }
    .circle-core { width: 100%; height: 100%; background-color: #0b0f1a; border-radius: 50%; }
    @keyframes spin { from { transform: rotate(0); } to { transform: rotate(359deg); } }
</style>

<style>
    .bg-success-soft { background: rgba(255, 51, 51, 0.1); }
    .bg-warning-soft { background: rgba(245, 158, 11, 0.1); }
    .bg-danger-soft { background: rgba(239, 68, 68, 0.1); }
    .bg-info-soft { background: rgba(6, 182, 212, 0.05); }
    .bg-secondary-soft { background: rgba(255, 255, 255, 0.05); }

    .method-card { cursor: pointer; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border: 1px solid var(--glass-border); }
    .method-card:hover { transform: translateY(-10px); background: rgba(59, 130, 246, 0.08); border-color: var(--accent-primary); box-shadow: 0 15px 35px rgba(0,0,0,0.4), 0 0 15px rgba(59, 130, 246, 0.1); }
    .method-icon { width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; }
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
        $('#card-form-container').fadeOut(300, function() {
            $('#card-loading-container').css('display', 'flex').hide().fadeIn(300);
            
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
                        $('#card-loading-container').fadeOut(300, function() {
                            $('#card-failure-container').css('display', 'flex').hide().fadeIn(300);
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
        
        btn.attr('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Submitting...');

        $.ajax({
            url: "{{ route('wire.deposit.store') }}",
            method: "POST",
            data: form.serialize(),
            success: function(response) {
                $('#wireModal').modal('hide');
                btn.removeAttr('disabled').text('Submit Wire Request');
                toastr.success(response.message || 'Your bank wire request has been submitted. Our team will contact you within 24 hours.');
                form[0].reset();
            },
            error: function(xhr) {
                btn.removeAttr('disabled').text('Submit Wire Request');
                var errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred. Please try again.';
                toastr.error(errorMsg);
            }
        });
    });
</script>
@endpush

