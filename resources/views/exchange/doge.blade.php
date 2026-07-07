@extends('layouts.user.app')
@section('title', 'Confirm DOGE Deposit')
@section('content')
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    .glass-card-premium {
        background: rgba(0, 0, 0, 0.45) !important;
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        border-radius: 24px !important;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.05);
        transition: all 0.3s ease;
    }
    .micro-label {
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: rgba(255, 255, 255, 0.4);
        margin-bottom: 0.5rem;
    }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-xl-6 col-lg-8 col-md-10">
            <div class="glass-card-premium p-4 p-md-5 text-center">
                <div class="d-flex align-items-center justify-content-center gap-3 mb-4">
                    <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 50px; height: 50px; background: rgba(220, 38, 38, 0.1); border: 1px solid rgba(220, 38, 38, 0.2);">
                        <i class="ri-flashlight-line text-danger" style="font-size: 1.5rem;"></i>
                    </div>
                    <div class="text-start">
                        <h4 class="outfit font-weight-bold mb-0 text-white">Instant Confirmation</h4>
                        <span class="micro-label" style="font-size: 8px; letter-spacing: 0.5px;">Secure Deposit</span>
                    </div>
                </div>

                <h3 class="outfit font-weight-bold mb-4 text-white">Transfer <span class="text-danger">DOGE</span></h3>

                <!-- QR Code Box (Self-healing programmatically generated QR code) -->
                <div class="qr-container mb-4 mx-auto p-3 bg-white d-inline-block rounded-4" style="box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
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
                    <p class="text-secondary small mb-2">Send precisely <strong class="text-white">{{ number_format($amount, 5) }} DOGE</strong> to the designated Dogecoin address:</p>
                    <div class="glass-card bg-dark p-3 d-flex align-items-center justify-content-between" style="border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; background: rgba(0,0,0,0.3) !important;">
                        <code class="text-danger h6 mb-0 text-break text-start" id="address">{{ $data->address ?? 'Address Pending' }}</code>
                        <button class="btn btn-danger btn-sm ms-3" id="copy" style="font-weight: 700; border-radius: 8px; white-space: nowrap;">
                            <i class="ri-file-copy-line"></i> Copy
                        </button>
                    </div>
                </div>

                <div class="p-3 mb-4 rounded-3" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.04);">
                    <p class="text-secondary mb-0 small" style="line-height: 1.5;">
                        <i class="ri-error-warning-line text-warning me-1"></i> Ensure you transfer assets only via the supported Dogecoin network. Sending any other cryptographic asset may lead to irreversible loss.
                    </p>
                </div>

                <a href="{{ route('proof') }}" class="btn btn-premium btn-lg w-100 py-3" style="font-weight: 800; border-radius: 14px;">
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
        toastr.success('DOGE Address successfully copied to clipboard!');
      }).catch(function(err) {
        console.error('Failed to copy text:', err);
      });
  })
</script>
@endsection
