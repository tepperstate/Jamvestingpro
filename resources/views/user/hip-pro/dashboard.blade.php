@extends('layouts.user.app')

@section('title', 'HIP Pro | Institutional Dashboard')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

<div class="container-fluid py-4" style="background: radial-gradient(circle at top right, rgba(118, 75, 162, 0.1), transparent 50%);">
    <!-- Header -->
    <div class="row mb-5" data-aos="fade-up">
        <div class="col-xl-12">
            <div class="d-inline-block px-3 py-1 mb-3 rounded-pill" style="background: rgba(255, 215, 0, 0.1); border: 1px solid rgba(255, 215, 0, 0.3);">
                <i class="ri-vip-diamond-fill" style="color: gold;"></i> <span class="text-warning small font-weight-bold letter-spacing-1">DIAMOND EXCLUSIVE</span>
            </div>
            <h1 class="h2 outfit font-weight-bold text-white mb-2">Hybrid Institutional Portfolio (HIP)</h1>
            <p class="text-secondary">Access sophisticated trading vehicles bridging the gap between advisor-led subdomains and institutional trading infrastructure.</p>
        </div>
    </div>

    <!-- Vehicles Navigation / Tabs -->
    <div class="nav-pills-custom mb-5" data-aos="fade-up" data-aos-delay="100">
        <ul class="nav nav-pills gap-3" id="hip-tabs" role="tablist">
            @foreach($vehicles as $vehicleType => $plans)
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ $loop->first ? 'active' : '' }} glass-tab-btn px-4 py-3" 
                   id="tab-{{ Str::slug($vehicleType) }}" 
                   data-toggle="pill" 
                   href="#content-{{ Str::slug($vehicleType) }}" 
                   role="tab">
                    <span class="font-weight-bold outfit">{{ $vehicleType }}</span>
                </a>
            </li>
            @endforeach
        </ul>
    </div>

    <!-- Vehicles Content -->
    <div class="tab-content" id="hip-tab-content">
        @foreach($vehicles as $vehicleType => $plans)
        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="content-{{ Str::slug($vehicleType) }}" role="tabpanel">
            
            <div class="mb-4">
                <h3 class="h4 outfit font-weight-bold text-white mb-1">{{ $vehicleType }}</h3>
                <p class="text-secondary small">Select your institutional tier to deploy capital into this vehicle.</p>
            </div>

            <div class="row g-4">
                @foreach($plans as $plan)
                <div class="col-xl-4 col-lg-4 col-md-6 mb-4 hip-plan-card">
                    <div class="glass-card h-100 position-relative overflow-hidden" style="background: rgba(16, 18, 27, 0.6); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.05); border-radius: 24px; transition: all 0.4s ease;">
                        
                        <!-- Premium Glow Effect -->
                        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: radial-gradient(circle at 50% 0%, rgba(118, 75, 162, 0.15), transparent 60%); pointer-events: none;"></div>

                        <div class="p-4 position-relative z-index-1">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <span class="badge" style="background: rgba(255,255,255,0.1); color: #fff; padding: 8px 16px; border-radius: 8px;">
                                    Tier {{ $plan->tier_level }}
                                </span>
                                <i class="ri-shield-star-line text-secondary h4 mb-0"></i>
                            </div>

                            <div class="mb-4">
                                <div class="small text-secondary mb-1">Minimum Allocation</div>
                                <div class="h3 font-weight-bold text-white mb-0">${{ number_format($plan->min_investment) }}</div>
                            </div>

                            <div class="mb-4 pt-4 border-top" style="border-color: rgba(255,255,255,0.05) !important;">
                                <div class="small text-secondary mb-2">Smart Logic Strategy</div>
                                <p class="text-white small mb-0" style="opacity: 0.9;">{{ $plan->smart_logic_description }}</p>
                            </div>

                        </div>
                        
                        <div class="p-4 mt-auto border-top" style="border-color: rgba(255,255,255,0.02) !important;">
                            <button class="btn w-100 py-3 font-weight-bold" 
                                    style="background: linear-gradient(90deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px; border: none;"
                                    onclick="deployCapital('{{ $vehicleType }}', {{ $plan->tier_level }}, {{ $plan->min_investment }})">
                                Deploy Capital
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

        </div>
        @endforeach
    </div>

</div>

<style>
    .glass-tab-btn {
        background: rgba(255,255,255,0.03) !important;
        border: 1px solid rgba(255,255,255,0.05) !important;
        color: var(--text-secondary) !important;
        border-radius: 12px !important;
        transition: all 0.3s ease;
    }
    .glass-tab-btn:hover {
        background: rgba(255,255,255,0.08) !important;
        color: white !important;
    }
    .glass-tab-btn.active {
        background: rgba(118, 75, 162, 0.2) !important;
        border-color: rgba(118, 75, 162, 0.5) !important;
        color: white !important;
        box-shadow: 0 0 20px rgba(118, 75, 162, 0.2);
    }
    .hip-plan-card .glass-card:hover {
        transform: translateY(-10px);
        border-color: rgba(118, 75, 162, 0.4) !important;
        box-shadow: 0 20px 40px rgba(0,0,0,0.4), 0 0 40px rgba(118, 75, 162, 0.1) !important;
    }
</style>

@endsection

@push('js')
<script>
    function deployCapital(vehicle, tier, minInvestment) {
        let amount = prompt(`Enter capital to deploy into ${vehicle} Tier ${tier}\n(Minimum: $${minInvestment.toLocaleString()})`);
        
        if (amount === null) return; // User cancelled
        
        amount = parseFloat(amount);
        
        if (isNaN(amount) || amount < minInvestment) {
            toastr.error(`Invalid amount. Minimum deployment is $${minInvestment.toLocaleString()}`);
            return;
        }

        // Add loading state logic here if desired

        fetch(`/dashboard/hip-pro/deploy/${encodeURIComponent(vehicle)}/${tier}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'application/json'
            },
            body: JSON.stringify({ amount: amount })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                toastr.success(data.message);
                setTimeout(() => window.location.reload(), 2000);
            } else {
                toastr.error(data.message || 'Error deploying capital');
            }
        })
        .catch(err => {
            console.error(err);
            toastr.error('A network error occurred.');
        });
    }

    // Anime.js entrance animation
    window.addEventListener('load', () => {
        if (typeof anime !== 'undefined') {
            anime({
                targets: '.hip-plan-card',
                translateY: [40, 0],
                opacity: [0, 1],
                delay: anime.stagger(100, {start: 300}),
                easing: 'easeOutQuint',
                duration: 1000
            });
        }
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
@endpush

