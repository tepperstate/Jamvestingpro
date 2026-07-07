@extends('layouts.user.app')
@section('title', 'Confirm BTC Deposit')
@section('content')
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    /* Premium Dark Theme & Glassmorphism */
    .glass-card-premium {
        background: rgba(20, 20, 22, 0.65) !important;
        backdrop-filter: blur(24px);
        -webkit-backdrop-filter: blur(24px);
        border: 1px solid rgba(153, 0, 0, 0.15) !important;
        border-radius: 24px !important;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.05);
        transition: all 0.3s ease;
    }
    
    .micro-label {
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: rgba(255, 255, 255, 0.5);
        margin-bottom: 0.5rem;
    }
    
    /* Gold Accents */
    .text-gold {
        color: #990000 !important;
    }
    .bg-gold-soft {
        background: rgba(153, 0, 0, 0.1);
        border: 1px solid rgba(153, 0, 0, 0.2);
    }
    
    .btn-gold {
        background: linear-gradient(135deg, #990000 0%, #AA8014 100%);
        color: #111 !important;
        border: none;
        box-shadow: 0 8px 20px rgba(153, 0, 0, 0.25);
        transition: all 0.3s ease;
    }
    .btn-gold:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 25px rgba(153, 0, 0, 0.35);
    }
    
    .btn-gold-outline {
        background: rgba(153, 0, 0, 0.05);
        color: #990000 !important;
        border: 1px solid rgba(153, 0, 0, 0.4);
        transition: all 0.3s ease;
    }
    .btn-gold-outline:hover {
        background: rgba(153, 0, 0, 0.15);
        border-color: #990000;
    }
    
    /* Mobile-first Adjustments */
    @media (max-width: 768px) {
        .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }
        .glass-card-premium {
            border-radius: 20px !important;
            padding: 1.5rem !important;
        }
        .qr-container {
            width: 180px;
            height: 180px;
            padding: 0.75rem !important;
        }
        .qr-container img {
            width: 100% !important;
            height: 100% !important;
            object-fit: contain;
        }
        .address-box {
            flex-direction: column;
            gap: 12px;
            text-align: center;
            padding: 1rem !important;
        }
        .address-box code {
            font-size: 0.85rem;
            word-break: break-all;
        }
        .address-box button {
            width: 100%;
            padding: 0.6rem;
        }
    }
</style>

<div class="container py-4 py-md-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="glass-card-premium p-4 p-md-5 text-center mx-auto">
                <div class="d-flex align-items-center justify-content-center gap-3 mb-4">
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-gold-soft" style="width: 48px; height: 48px;">
                        <i class="ri-flashlight-line text-gold" style="font-size: 1.5rem;"></i>
                    </div>
                    <div class="text-start">
                        <h5 class="outfit font-weight-bold mb-0 text-white">Instant Confirmation</h5>
                        <span class="micro-label" style="font-size: 8px; letter-spacing: 0.5px;">Secure Deposit</span>
                    </div>
                </div>

                <h3 class="outfit font-weight-bold mb-4 text-white">Transfer <span class="text-gold">BTC</span></h3>

                <!-- QR Code Box -->
                <div class="qr-container mb-4 mx-auto bg-white d-inline-block rounded-4" style="box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                    @php
                        try {
                            $qrCode = base64_encode(SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(220)->errorCorrection('H')->generate($data->address ?? ''));
                            $qrCodeUrl = 'data:image/png;base64,' . $qrCode;
                        } catch (\Exception $e) {
                            $qrCodeUrl = null;
                        }
                    @endphp
                    @if($qrCodeUrl)
                        <img src="{{ $qrCodeUrl }}" style="width: 200px; height: 200px;" alt="QR Code">
                    @else
                        <img src="{{ asset('storage/image/'.$data->image) }}" onerror="this.src='{{ asset('assets/img/profit.svg') }}'" style="width: 200px; height: 200px;" alt="QR Code">
                    @endif
                </div>

                <div class="mb-4">
                    <p class="text-secondary small mb-3">Send precisely <strong class="text-white">{{ number_format($amount, 5) }} BTC</strong> to the designated BTC gateway address:</p>
                    <div class="glass-card address-box d-flex align-items-center justify-content-between" style="border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; background: rgba(0,0,0,0.4) !important;">
                        <code class="text-gold fw-bold mb-0 text-break text-start" id="address">{{ $data->address ?? 'Address Pending' }}</code>
                        <button class="btn btn-gold-outline btn-sm" id="copy" style="font-weight: 700; border-radius: 8px; white-space: nowrap;">
                            <i class="ri-file-copy-line"></i> Copy
                        </button>
                    </div>
                </div>

                <div class="p-3 mb-4 rounded-3 text-start" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(153, 0, 0, 0.1);">
                    <div class="d-flex gap-2">
                        <i class="ri-error-warning-line text-gold mt-1"></i>
                        <p class="text-secondary mb-0 small" style="line-height: 1.5;">
                            Ensure you transfer assets only via the supported Bitcoin network. Sending any other cryptographic asset may lead to irreversible loss.
                        </p>
                    </div>
                </div>

                <a href="{{ route('proof') }}" class="btn btn-gold btn-lg w-100 py-3 mt-2" style="font-weight: 800; border-radius: 14px; font-family: 'Outfit', sans-serif; letter-spacing: 0.5px;">
                    I HAVE MADE THE PAYMENT
                </a>
            </div>
        </div>
    </div>
</div>

<script>
  $("#copy").on('click', function(){
      let code = $("#address").text();
      navigator.clipboard.writeText(code).then(function(){
        toastr.success('BTC Address successfully copied to clipboard!');
      }).catch(function(err) {
        console.error('Failed to copy text:', err);
      });
  })
</script>
@endsection
