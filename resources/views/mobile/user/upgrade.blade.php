@extends('layouts.user.app')
@section('title', 'Upgrade Plan')
@section('content')
<style>
    .mobile-glass-container { padding: 15px; padding-bottom: 80px; font-family: 'Outfit', sans-serif; background: #0a0b0e; color: #fff; min-height: 100vh; }
    .header-section { text-align: center; margin-bottom: 30px; margin-top: 10px; }
    .header-title { font-size: 26px; font-weight: 800; color: #FFD700; margin-bottom: 5px; }
    
    .plan-card { background: rgba(255,255,255,0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; overflow: hidden; margin-bottom: 25px; position: relative; }
    .plan-card.current { border-color: #ff3333; box-shadow: 0 0 20px rgba(255, 51, 51, 0.1); }
    .plan-card.restricted { border-color: rgba(245, 158, 11, 0.4); }
    
    .plan-img { width: 100%; height: 120px; object-fit: cover; opacity: 0.6; }
    .plan-body { padding: 20px; position: relative; }
    
    .plan-icon { width: 50px; height: 50px; background: #0a0b0e; border: 1px solid rgba(255,215,0,0.3); border-radius: 50%; display: flex; align-items: center; justify-content: center; position: absolute; top: -25px; left: 20px; color: #FFD700; font-size: 20px; }
    
    .plan-title { font-size: 20px; font-weight: 800; margin-top: 15px; margin-bottom: 5px; }
    .plan-price { font-size: 24px; font-weight: 800; color: #FFD700; margin-bottom: 15px; }
    
    .feature-list { list-style: none; padding: 0; margin: 0 0 20px 0; }
    .feature-list li { font-size: 13px; color: rgba(255,255,255,0.7); margin-bottom: 8px; display: flex; align-items: center; }
    .feature-list li i { color: #FFD700; margin-right: 8px; }
    
    .btn-gold { background: linear-gradient(135deg, #FFD700, #990000); color: #000; border: none; padding: 15px; border-radius: 12px; font-weight: 800; width: 100%; text-transform: uppercase; display: block; text-align: center; text-decoration: none; }
    .btn-disabled { background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.5); padding: 15px; border-radius: 12px; font-weight: 800; width: 100%; text-transform: uppercase; border: none; }
    
    .badge-restricted { position: absolute; top: 10px; right: 10px; background: rgba(245, 158, 11, 0.2); color: #f59e0b; font-size: 10px; font-weight: 800; padding: 5px 10px; border-radius: 8px; border: 1px solid rgba(245, 158, 11, 0.3); }
</style>

<div class="mobile-glass-container">
    <div class="header-section">
        <h1 class="header-title">Elevate Strategy</h1>
        <p style="font-size: 13px; color: rgba(255,255,255,0.6);">Unlock priority flows & premium assets.</p>
    </div>

    @foreach($packages as $package)
    <div class="plan-card {{ $user->package_id == $package->id ? 'current' : '' }} {{ $package->is_restricted ? 'restricted' : '' }}">
        <img src="{{ url('storage/image/' . ($package->image ?? 'default_investment.png')) }}" class="plan-img">
        @if($package->is_restricted)
            <div class="badge-restricted"><i class="ri-lock-line"></i> APPROVAL REQUIRED</div>
        @endif
        
        <div class="plan-body">
            <div class="plan-icon">
                <i class="{{ $package->is_restricted ? 'ri-shield-star-line' : 'ri-vip-diamond-line' }}"></i>
            </div>
            
            <h3 class="plan-title">{{ $package->name }}</h3>
            <div class="plan-price">${{ number_format($package->amount) }}</div>
            
            <ul class="feature-list">
                <li><i class="ri-check-line"></i> Daily Limit: {{ $package->daily_trade }} Trades</li>
                <li><i class="ri-check-line"></i> Min Deposit: ${{ number_format($package->min_deposit) }}</li>
                @if(isset($package->features) && is_countable($package->features))
                    @foreach($package->features as $feature)
                    <li><i class="ri-star-line"></i> {{ ucwords(str_replace('_', ' ', $feature)) }}</li>
                    @endforeach
                @endif
            </ul>
            
            @if($user->package_id == $package->id)
                <button class="btn-disabled">CURRENT PLAN</button>
            @elseif($user->package && $package->amount < $user->package->amount)
                <button class="btn-disabled">ALREADY ACTIVE</button>
            @elseif($package->is_restricted)
                @if($user->basic_plan_approved == 0)
                    <button class="btn-gold request-btn" onclick="requestBasicAccess(this)">
                        REQUEST ACCESS
                    </button>
                @elseif($user->basic_plan_approved == 2)
                    <button class="btn-disabled" style="color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3);">REQUEST PENDING</button>
                @elseif($user->basic_plan_approved == 1)
                    <a href="{{ route('deposit') }}" class="btn-gold">UPGRADE NOW</a>
                @endif
            @else
                <a href="{{ route('deposit') }}" class="btn-gold">UPGRADE NOW</a>
            @endif
        </div>
    </div>
    @endforeach
</div>

@push('js')
<script>
    function requestBasicAccess(btn) {
        $(btn).prop('disabled', true).html('SUBMITTING...');
        $.ajax({
            url: "{{ route('user.request_basic_plan') }}",
            method: "POST",
            data: { _token: "{{ csrf_token() }}" },
            success: function(data) {
                if (data.status) {
                    toastr.success('Request submitted.');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    toastr.info(data.message);
                    $(btn).prop('disabled', false).html('REQUEST ACCESS');
                }
            },
            error: function() {
                toastr.error('Network error.');
                $(btn).prop('disabled', false).html('REQUEST ACCESS');
            }
        });
    }
</script>
@endpush
@endsection
