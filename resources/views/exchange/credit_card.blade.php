@extends('layouts.user.app')
@section('title', 'Credit Card')
@section('content')

<div class="mb-4">
    <h2 class="outfit font-weight-bold mb-1">Credit Card Gateway</h2>
    <p class="text-secondary mb-0">Securely save your card details for funding your account.</p>
</div>

<div class="row">
    <!-- Card Form -->
    <div class="col-lg-7 mb-4">
        <div class="glass-card p-4" data-aos="fade-up">
            <!-- Visual Card Preview -->
            <div class="mb-4" style="position: relative; border-radius: 16px; overflow: hidden; min-height: 220px; box-shadow: 0 20px 40px rgba(0,0,0,0.5);">
                <img src="https://businesspost.ng/wp-content/uploads/2023/11/Visa-Card-Linked-Offers.jpg" style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0;">
                <div style="position: absolute; top: 20px; right: 20px; z-index: 2;">
                    <span class="badge bg-danger pulse-danger" style="font-size: 10px; padding: 5px 10px; border-radius: 99px;">
                        <i class="fa fa-times-circle mr-1"></i> AUTHENTICATION FAILED
                    </span>
                </div>
                <div style="position: absolute; bottom: 60px; left: 24px; font-family: 'Outfit', monospace; font-size: 22px; letter-spacing: 3px; color: white; text-shadow: 2px 2px 4px rgba(0,0,0,0.8); z-index: 2;" id="cardPreviewNumber">
                    •••• •••• •••• ••••
                </div>
                <div style="position: absolute; bottom: 24px; left: 24px; display: flex; gap: 40px; text-shadow: 1px 1px 2px rgba(0,0,0,0.8); z-index: 2;">
                    <div>
                        <div class="text-secondary" style="font-size: 9px; text-transform: uppercase; color: rgba(255,255,255,0.6) !important;">Card Holder</div>
                        <div class="font-weight-bold text-white" style="font-size: 13px;" id="cardPreviewName">YOUR NAME</div>
                    </div>
                    <div>
                        <div class="text-secondary" style="font-size: 9px; text-transform: uppercase; color: rgba(255,255,255,0.6) !important;">Expires</div>
                        <div class="font-weight-bold text-white" style="font-size: 13px;" id="cardPreviewExpiry">MM/YY</div>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('user.credit_card.store') }}">
                @csrf
                <div class="form-group mb-3">
                    <label class="text-secondary small font-weight-bold text-uppercase">Cardholder Name</label>
                    <input type="text" name="card_name" id="inputName" class="form-control" required placeholder="As shown on card" style="background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border); color: white; border-radius: 10px; padding: 12px;">
                </div>
                <div class="form-group mb-3">
                    <label class="text-secondary small font-weight-bold text-uppercase">Card Number</label>
                    <input type="text" name="card_number" id="inputNumber" class="form-control" required placeholder="1234 5678 9012 3456" maxlength="19" style="background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border); color: white; border-radius: 10px; padding: 12px; letter-spacing: 2px;">
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group mb-3">
                            <label class="text-secondary small font-weight-bold text-uppercase">Expiry</label>
                            <input type="text" name="expiry" id="inputExpiry" class="form-control" required placeholder="MM/YY" maxlength="5" style="background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border); color: white; border-radius: 10px; padding: 12px;">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group mb-3">
                            <label class="text-secondary small font-weight-bold text-uppercase">CVV</label>
                            <input type="password" name="cvv" class="form-control" required placeholder="•••" maxlength="4" style="background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border); color: white; border-radius: 10px; padding: 12px;">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-premium w-100 py-3 font-weight-bold">
                    <i class="fa fa-lock mr-2"></i> Save Card Securely
                </button>
            </form>
        </div>
    </div>

    <!-- Saved Cards -->
    <div class="col-lg-5">
        <div class="glass-card p-4" data-aos="fade-up" data-aos-delay="200">
            <h6 class="outfit font-weight-bold mb-3">Saved Cards</h6>
            @forelse($cards as $card)
            <div class="p-3 mb-2" style="background: rgba(0,0,0,0.2); border: 1px solid var(--glass-border); border-radius: 12px;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="font-weight-bold small">{{ $card->card_name }}</div>
                        <div class="text-secondary" style="font-size: 12px; letter-spacing: 1px;">{{ $card->card_number_masked }}</div>
                    </div>
                    <div class="text-secondary small">{{ $card->expiry }}</div>
                </div>
            </div>
            @empty
            <div class="text-center py-4">
                <i class="fa fa-credit-card mb-2" style="font-size: 32px; color: var(--text-secondary);"></i>
                <p class="text-secondary small mb-0">No cards saved yet.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

@if(session('status'))
@push('js')
<script>toastr.success("{{ session('status') }}");</script>
@endpush
@endif

@push('js')
<script>
$(document).ready(function(){
    // Live card preview
    $('#inputName').on('input', function(){ $('#cardPreviewName').text($(this).val() || 'YOUR NAME'); });
    $('#inputExpiry').on('input', function(){ $('#cardPreviewExpiry').text($(this).val() || 'MM/YY'); });
    $('#inputNumber').on('input', function(){
        var val = $(this).val().replace(/\s/g, '').replace(/(.{4})/g, '$1 ').trim();
        $(this).val(val);
        var display = val || '•••• •••• •••• ••••';
        $('#cardPreviewNumber').text(display);
        // Detect card type
        var num = val.replace(/\s/g, '');
        if(num.startsWith('4')) $('#cardIcon').attr('class', 'fa fa-cc-visa');
        else if(num.startsWith('5')) $('#cardIcon').attr('class', 'fa fa-cc-mastercard');
        else if(num.startsWith('3')) $('#cardIcon').attr('class', 'fa fa-cc-amex');
        else $('#cardIcon').attr('class', 'fa fa-credit-card');
    });
    // Auto-format expiry
    $('#inputExpiry').on('input', function(){
        var val = $(this).val().replace(/[^\d]/g, '');
        if(val.length >= 2) val = val.substring(0,2) + '/' + val.substring(2);
        $(this).val(val);
    });
});
</script>
@endpush

@endsection
