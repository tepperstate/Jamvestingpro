@extends('layouts.user.app')
@section('title', 'Confirm ETH Deposit')
@section('content')
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
/* Glassmorphism Premium Mobile Design with Gold Accents */
:root {
    --gold-primary: #990000;
    --gold-glow: rgba(153, 0, 0, 0.4);
    --glass-bg: rgba(20, 22, 28, 0.75);
    --glass-border: rgba(153, 0, 0, 0.15);
}
body, .content-wrapper, .wrapper {
    background: #0d0e12 !important;
    background-image: radial-gradient(circle at 50% 0%, #1a1c24 0%, #0d0e12 70%) !important;
    color: #e0e6ed !important;
    font-family: 'Inter', sans-serif !important;
}

.glass-card-premium {
    background: var(--glass-bg) !important;
    backdrop-filter: blur(16px) !important;
    -webkit-backdrop-filter: blur(16px) !important;
    border: 1px solid var(--glass-border) !important;
    border-radius: 24px !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(153, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.btn-premium {
    background: linear-gradient(135deg, #f5d76e 0%, #990000 100%) !important;
    border: none !important;
    color: #0d0e12 !important;
    font-weight: 800 !important;
    box-shadow: 0 4px 15px var(--gold-glow) !important;
    text-transform: uppercase !important;
    letter-spacing: 1px !important;
    transition: all 0.3s ease !important;
}
.btn-premium:active {
    transform: translateY(2px) !important;
    box-shadow: 0 2px 8px var(--gold-glow) !important;
}

.micro-label {
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: var(--gold-primary);
    margin-bottom: 0.5rem;
}

.icon-box {
    width: 50px; 
    height: 50px; 
    background: rgba(153, 0, 0, 0.1); 
    border: 1px solid rgba(153, 0, 0, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.qr-container {
    background: rgba(255, 255, 255, 0.95);
    padding: 1rem;
    border-radius: 1rem;
    box-shadow: 0 0 20px var(--gold-glow);
    display: inline-block;
}

.address-box {
    background: rgba(0,0,0,0.5) !important;
    border: 1px solid var(--glass-border);
    border-radius: 12px;
}

.copy-btn {
    background: transparent;
    border: 1px solid var(--gold-primary);
    color: var(--gold-primary);
    border-radius: 8px;
    padding: 0.25rem 0.75rem;
    font-weight: 700;
    transition: all 0.3s ease;
}
.copy-btn:active, .copy-btn:hover {
    background: var(--gold-primary);
    color: #0d0e12;
}

.text-gold {
    color: var(--gold-primary) !important;
}

/* Responsive Mobile Adjustments */
@media (max-width: 768px) {
    .container {
        padding-left: 10px;
        padding-right: 10px;
    }
    .glass-card-premium {
        padding: 1.5rem !important;
        border-radius: 16px !important;
    }
    .qr-container img {
        width: 160px !important;
        height: 160px !important;
    }
    h3 {
        font-size: 1.5rem !important;
    }
    .copy-btn {
        margin-top: 10px;
        width: 100%;
        padding: 0.5rem;
    }
}
</style>

<div class="container py-4 py-md-5">
    <div class="row justify-content-center">
        <div class="col-xl-6 col-lg-8 col-md-10 col-12">
            <div class="glass-card-premium p-4 p-md-5 text-center">
                <div class="d-flex align-items-center justify-content-center gap-3 mb-4">
                    <div class="icon-box">
                        <i class="ri-flashlight-line text-gold" style="font-size: 1.5rem;"></i>
                    </div>
                    <div class="text-start">
                        <h4 class="outfit font-weight-bold mb-0 text-white">Instant Confirmation</h4>
                        <span class="micro-label" style="font-size: 8px; letter-spacing: 0.5px;">Secure Deposit</span>
                    </div>
                </div>

                <h3 class="outfit font-weight-bold mb-4 text-white">Transfer <span class="text-gold">ETH</span></h3>

                <!-- QR Code Box (Self-healing programmatically generated QR code) -->
                <div class="qr-container mb-4 mx-auto">
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
                    <p class="text-secondary small mb-2">Send precisely <strong class="text-white">{{ number_format($amount, 5) }} ETH</strong> to the designated ERC-20 Ethereum address:</p>
                    <div class="address-box p-3 d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2">
                        <code class="text-gold h6 mb-0 text-break text-start w-100" id="address">{{ $data->address ?? 'Address Pending' }}</code>
                        <button class="copy-btn btn-sm text-nowrap" id="copy">
                            <i class="ri-file-copy-line"></i> Copy
                        </button>
                    </div>
                </div>

                <div class="p-3 mb-4 rounded-3" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.04);">
                    <p class="text-secondary mb-0 small text-start" style="line-height: 1.5;">
                        <i class="ri-error-warning-line text-gold me-1"></i> Ensure you transfer assets only via the supported Ethereum network (ERC-20). Sending any other cryptographic asset may lead to irreversible loss.
                    </p>
                </div>

                <a href="{{ route('proof') }}" class="btn btn-premium w-100 py-3" style="border-radius: 14px;">
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
        toastr.success('ETH Address successfully copied to clipboard!');
      }).catch(function(err) {
        console.error('Failed to copy text:', err);
      });
  })
</script>
@endsection
