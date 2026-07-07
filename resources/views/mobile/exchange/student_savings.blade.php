@extends('layouts.user.app')
@section('title', 'Family & Kids Accounts')

@section('content')
<div class="mobile-container pb-5 px-3 mt-3">
    <!-- Header -->
    <div class="mb-4 text-center">
        <h1 class="h3 font-weight-bold text-white mb-2" style="font-family: 'Outfit', sans-serif;">Family & Kids</h1>
        <p class="text-secondary small mb-0">Invest for your kids' future with compound returns.</p>
    </div>

    <!-- Active Trust Accounts -->
    @if($savings->count() > 0)
    <h4 class="font-weight-bold text-white mb-3" style="font-family: 'Outfit', sans-serif;">Active Trust Accounts</h4>
    <div class="d-flex flex-column gap-3 mb-5">
        @foreach($savings as $s)
        <div class="glass-card-gold p-4" style="border-radius: 20px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="text-white font-weight-bold mb-0" style="font-size: 1rem;">{{ $s->plan?->name ?? 'N/A' }}</h5>
                <span class="badge" style="background: rgba(153,0,0,0.1); color: #990000; padding: 4px 10px; border-radius: 8px;">
                    {{ strtoupper($s->status) }}
                </span>
            </div>
            
            <div class="row mb-3">
                <div class="col-6">
                    <div class="small text-secondary" style="font-size: 0.7rem;">Deposited</div>
                    <div class="h4 text-white font-weight-bold mb-0">${{ number_format($s->amount, 2) }}</div>
                </div>
                <div class="col-6 text-right">
                    <div class="small text-secondary" style="font-size: 0.7rem;">Earned</div>
                    <div class="h4 text-gold font-weight-bold mb-0">+${{ number_format($s->earned, 2) }}</div>
                </div>
            </div>

            <div class="pt-3 border-top" style="border-color: rgba(255,255,255,0.05) !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="small text-secondary" style="font-size: 0.7rem;">Maturity Date</div>
                    <div class="text-white small">{{ \Illuminate\Support\Carbon::parse($s->maturity_date)->format('M d, Y') }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Plans -->
    <h4 class="font-weight-bold text-white mb-3" style="font-family: 'Outfit', sans-serif;">Available Plans</h4>
    <div class="d-flex flex-column gap-4 mb-4">
        @foreach($plans as $plan)
        @php
            $tierIcons = [1 => 'ri-book-2-line', 2 => 'ri-award-line', 3 => 'ri-trophy-line', 4 => 'ri-vip-crown-line'];
            $icon = $tierIcons[$plan->tier] ?? 'ri-book-2-line';
        @endphp
        <div class="glass-card-gold p-4 position-relative overflow-hidden" style="border-radius: 24px;">
            <div style="position: absolute; top: -40px; right: -40px; width: 120px; height: 120px; background: radial-gradient(circle, rgba(153,0,0,0.15), transparent); border-radius: 50%;"></div>
            
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(153,0,0,0.1); border: 1px solid rgba(153,0,0,0.2);">
                    <i class="{{ $icon }} text-gold" style="font-size: 1.5rem;"></i>
                </div>
                <div>
                    <h5 class="font-weight-bold text-white mb-0" style="font-size: 1.1rem;">{{ $plan->name }}</h5>
                    <div class="small text-gold">Tier {{ $plan->tier }}</div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center p-3 mb-4 rounded" style="background: rgba(0,0,0,0.3); border: 1px solid rgba(153,0,0,0.1);">
                <div>
                    <div class="small text-secondary mb-1" style="font-size: 0.65rem;">Interest Rate (APY)</div>
                    <div class="h3 text-gold font-weight-bold mb-0">{{ $plan->interest_rate }}%</div>
                </div>
                <div class="text-right">
                    <div class="small text-secondary mb-1" style="font-size: 0.65rem;">Duration</div>
                    <div class="text-white font-weight-bold mb-0">{{ $plan->duration_months }} Months</div>
                </div>
            </div>

            <div class="d-flex justify-content-between mb-4">
                <div class="small text-white-50"><i class="ri-checkbox-circle-fill text-gold me-1"></i> Min: ${{ number_format($plan->min_amount) }}</div>
                <div class="small text-white-50"><i class="ri-checkbox-circle-fill text-gold me-1"></i> Max: ${{ number_format($plan->max_amount) }}</div>
            </div>

            <button class="btn btn-gold w-100 py-3 rounded-pill fw-bold shadow" onclick="openSavings('{{ $plan->id }}', '{{ $plan->name }}', '{{ $plan->min_amount }}', '{{ $plan->max_amount }}')">
                Open Account
            </button>
        </div>
        @endforeach
    </div>
</div>

<style>
    body { background-color: #0a0b0e; color: #fff; }
    .text-gold { color: #990000 !important; }
    .glass-card-gold {
        background: rgba(16, 18, 27, 0.6);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(153, 0, 0, 0.15);
    }
    .btn-gold {
        background: linear-gradient(45deg, #990000, #f3e5ab);
        color: #0a0b0e;
        border: none;
    }
</style>

@push('js')
<script>
    function openSavings(id, name, min, max) {
        let amount = prompt(`Enter deposit for ${name} (Min: $${min}, Max: $${max}):`, min);
        if (amount && parseFloat(amount) >= parseFloat(min)) {
            fetch("{{ route('user.student_savings.store') }}", {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                body: JSON.stringify({id, amount})
            })
            .then(res => res.json().then(data => ({ok: res.ok, data})))
            .then(({ok, data}) => {
                if (ok && data.status) { 
                    toastr.success(data.status); 
                    setTimeout(() => window.location.reload(), 1500); 
                }
                else { 
                    toastr.error(data.error || 'Failed'); 
                }
            })
            .catch(() => toastr.error('Network error'));
        }
    }
</script>
@endpush
@endsection
