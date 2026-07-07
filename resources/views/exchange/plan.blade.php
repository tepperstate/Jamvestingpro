@extends('layouts.user.app')

@section('title', 'Membership Plans')

@section('content')
<div class="container-fluid py-5">
    <div class="text-center mb-5" data-aos="fade-up">
        <h1 class="display-4 outfit font-weight-bold text-white mb-3">Membership Plans</h1>
        <p class="text-secondary mx-auto" style="max-width: 600px;">Choose the plan that fits your goals. Higher tiers unlock more features, bigger instant deposits, and premium market access.</p>
    </div>

    <div class="row g-4 justify-content-center">
        @php
            $feature_map = [
                'basic_trading' => ['label' => 'Standard Trade Room', 'icon' => 'ri-exchange-line'],
                'high_leverage' => ['label' => 'High Leverage (1:100)', 'icon' => 'ri-flashlight-line'],
                'vip_stocks' => ['label' => 'Premium Stocks Access', 'icon' => 'ri-vip-crown-2-line'],
                'mutual_funds' => ['label' => 'Investment Strategies', 'icon' => 'ri-bank-line'],
                'advanced_controls' => ['label' => 'AI Risk Management', 'icon' => 'ri-robot-line'],
            ];
        @endphp

        @foreach ($data as $d)
        @php
            $is_popular = (stripos($d->name, 'Pro') !== false || stripos($d->name, 'Classic') !== false);
            $features = json_decode($d->features) ?? [];
            $threshold = $d->min_deposit > 0 ? $d->min_deposit : $d->amount;
        @endphp
        <div class="col-xl-3 col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
            <div class="premium-tier-card h-100 {{ $is_popular ? 'popular' : '' }}">
                <div class="card-header-img-wrapper">
                    @php
                        $pkgImg = 'vanguard_package_starter_1779670980696.png';
                        if (stripos($d->name, 'starter') !== false) {
                            $pkgImg = 'vanguard_package_starter_1779670980696.png';
                        } elseif (stripos($d->name, 'pro') !== false) {
                            $pkgImg = 'vanguard_package_professional_photo_1779671423471.png';
                        } elseif (stripos($d->name, 'vip') !== false || stripos($d->name, 'classic') !== false) {
                            $pkgImg = 'vanguard_package_vip_photo_1779671450802.png';
                        }
                    @endphp
                    <img src="{{ asset('assets/images/' . $pkgImg) }}" 
                         onerror="this.style.display='none';" 
                         alt="{{ $d->name }}" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.6;">
                    <div class="card-header-overlay"></div>
                </div>
                
                @if($is_popular)
                <div class="popular-tag">MOST POPULAR</div>
                @endif
                
                <div class="card-body-premium pt-2">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h3 class="outfit font-weight-bold text-white mb-1" style="font-size: 1.25rem;">{{ $d->name }}</h3>
                            <div class="tier-status small">
                                @if($d->id == auth()->user()->package_id)
                                <span class="text-success" style="font-size: 10px;"><i class="ri-checkbox-circle-fill me-1"></i> ACTIVE TIER</span>
                                @else
                                <span class="text-secondary" style="font-size: 10px;">AVAILABLE</span>
                                @endif
                            </div>
                        </div>
                        <div class="tier-icon-box" style="width: 32px; height: 32px; font-size: 1rem;">
                            <i class="{{ $loop->first ? 'ri-seedling-line' : ($loop->last ? 'ri-government-line' : 'ri-shield-flash-line') }}"></i>
                        </div>
                    </div>

                    <div class="price-box mb-4">
                        <div class="small text-secondary mb-1" style="font-size: 10px;">MINIMUM DEPOSIT</div>
                        <div class="d-flex align-items-baseline">
                            <span class="h3 outfit font-weight-bold text-white mb-0">${{ number_format($threshold) }}</span>
                        </div>
                    </div>

                    <div class="features-list mb-5 flex-grow-1">
                        @foreach($features as $f_key)
                            @if(isset($feature_map[$f_key]))
                            <div class="feature-item d-flex align-items-center mb-2">
                                <i class="{{ $feature_map[$f_key]['icon'] }} text-primary me-2" style="font-size: 14px;"></i>
                                <span class="text-white small" style="font-size: 12px;">{{ $feature_map[$f_key]['label'] }}</span>
                            </div>
                            @endif
                        @endforeach
                    </div>

                    <div class="mt-auto">
                        @if($d->id == auth()->user()->package_id)
                        <button class="btn btn-premium w-100 py-3 disabled opacity-50" style="background: rgba(16, 18, 27, 0.2); border: 1px solid var(--accent-success); color: var(--accent-success);">
                            ACTIVE LEVEL
                        </button>
                        @else
                        <form method="post" action="{{ route('upgrade.post') }}">
                            @csrf
                            <input type="hidden" name="name" value="{{ $d->name }}">
                            <button type="submit" class="btn btn-premium w-100 py-3 {{ $is_popular ? '' : 'btn-outline-premium' }}">
                                {{ $threshold <= (DB::table('deposits')->whereUserId(auth()->id())->sum('amount')) ? 'ACTIVATE' : 'UPGRADE' }}
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
    .premium-tier-card {
        background: rgba(0, 0, 0, 0.6) !important;
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.05) !important;
        border-radius: 24px;
        position: relative;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
    }

    .premium-tier-card:hover {
        transform: translateY(-10px);
        border-color: rgba(220, 38, 38, 0.3) !important;
        box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4);
    }

    .premium-tier-card.popular {
        border-color: rgba(220, 38, 38, 0.5) !important;
        background: rgba(0, 0, 0, 0.8) !important;
    }

    .card-header-img-wrapper {
        position: relative;
        height: 140px;
        width: 100%;
        background: linear-gradient(135deg, rgba(220, 38, 38, 0.2) 0%, rgba(0, 0, 0, 0.8) 100%);
        overflow: hidden;
    }

    .card-header-overlay {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background: linear-gradient(to bottom, transparent 30%, rgba(0, 0, 0, 0.95) 100%);
    }

    .card-body-premium {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .card-glow {
        position: absolute;
        width: 150px;
        height: 150px;
        background: radial-gradient(circle, rgba(220, 38, 38, 0.15) 0%, transparent 70%);
        top: -50px;
        right: -50px;
        z-index: 0;
        pointer-events: none;
    }

    .popular-tag {
        position: absolute;
        top: 20px;
        right: -40px;
        background: rgba(220, 38, 38, 0.8);
        color: white;
        font-size: 0.65rem;
        font-weight: 900;
        padding: 5px 40px;
        transform: rotate(45deg);
        z-index: 10;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }

    .tier-icon-box {
        width: 48px;
        height: 48px;
        background: rgba(255,255,255,0.03);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: rgba(220, 38, 38, 0.8);
        border: 1px solid rgba(255,255,255,0.05);
    }

    .ls-1 { letter-spacing: 1px; }

    .btn-outline-premium {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.1);
        color: white !important;
    }
    
    .btn-outline-premium:hover {
        background: rgba(220, 38, 38, 0.8);
        border-color: rgba(220, 38, 38, 0.8);
    }

    .feature-item i { font-size: 1.1rem; }
</style>
@endsection
