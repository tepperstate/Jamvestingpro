@extends('layouts.user.app')
@section('title', 'Retirement')

@section('content')
<div class="mobile-container pb-5 px-3 mt-3">
    <!-- Header -->
    <div class="mb-4 text-center">
        <h1 class="h3 font-weight-bold text-white mb-2" style="font-family: 'Outfit', sans-serif;">Retirement (IRA)</h1>
        <p class="text-secondary small mb-0">Maximize your long-term wealth with flexible tax-advantaged accounts.</p>
    </div>

    <!-- Active IRA Accounts -->
    @if($accounts->count() > 0)
    <h4 class="font-weight-bold text-white mb-3" style="font-family: 'Outfit', sans-serif;">Active IRA Accounts</h4>
    <div class="d-flex flex-column gap-3 mb-5">
        @foreach($accounts as $a)
        <div class="glass-card-gold p-4" style="border-radius: 20px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="text-white font-weight-bold mb-0" style="font-size: 1rem;">{{ $a->plan?->name ?? 'N/A' }}</h5>
                <span class="badge" style="background: rgba(153,0,0,0.1); color: #990000; padding: 4px 10px; border-radius: 8px;">
                    {{ strtoupper($a->status) }}
                </span>
            </div>
            
            <div class="mb-3">
                <div class="small text-secondary" style="font-size: 0.7rem;">Total Balance</div>
                <div class="h3 text-gold font-weight-bold mb-0">${{ number_format($a->balance, 2) }}</div>
            </div>

            <div class="d-flex justify-content-between pt-3 border-top" style="border-color: rgba(255,255,255,0.05) !important;">
                <div>
                    <div class="small text-secondary" style="font-size: 0.65rem;">Your Contrib.</div>
                    <div class="text-white font-weight-bold" style="font-size: 0.85rem;">${{ number_format($a->employee_contributions, 2) }}</div>
                </div>
                <div class="text-right">
                    <div class="small text-secondary" style="font-size: 0.65rem;">Employer Match</div>
                    <div class="text-white font-weight-bold" style="font-size: 0.85rem;">${{ number_format($a->employer_contributions, 2) }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- IRA Plans -->
    <h4 class="font-weight-bold text-white mb-3" style="font-family: 'Outfit', sans-serif;">Available Plans</h4>
    <div class="d-flex flex-column gap-4 mb-4">
        @foreach($plans as $plan)
        <div class="glass-card-gold p-4 position-relative overflow-hidden" style="border-radius: 24px;">
            <div style="position: absolute; top: -30px; right: -30px; width: 100px; height: 100px; background: radial-gradient(circle, rgba(153,0,0,0.2), transparent); border-radius: 50%;"></div>
            
            <div class="mb-3">
                <h4 class="font-weight-bold text-white mb-1" style="font-size: 1.1rem;">{{ $plan->name }}</h4>
                <div class="small text-gold">Tier {{ $plan->tier }}</div>
            </div>

            <div class="p-3 mb-4 rounded" style="background: rgba(0,0,0,0.3); border: 1px solid rgba(153,0,0,0.1);">
                <div class="d-flex justify-content-between mb-2 pb-2 border-bottom" style="border-color: rgba(255,255,255,0.05) !important;">
                    <span class="small text-secondary">Employer Match</span>
                    <span class="text-gold font-weight-bold">{{ $plan->employer_match_pct }}%</span>
                </div>
                <div class="d-flex justify-content-between mb-2 pb-2 border-bottom" style="border-color: rgba(255,255,255,0.05) !important;">
                    <span class="small text-secondary">Vesting Schedule</span>
                    <span class="text-white small">{{ ucfirst($plan->vesting_schedule) }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="small text-secondary">Max Contribution</span>
                    <span class="text-white small">${{ number_format($plan->max_contribution) }}/yr</span>
                </div>
            </div>

            <button class="btn btn-gold w-100 py-3 rounded-pill fw-bold shadow" onclick="contribute('{{ $plan->id }}','{{ $plan->name }}','{{ $plan->min_contribution }}','{{ $plan->max_contribution }}')">
                Contribute Now
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
function contribute(id, name, min, max) {
    let amount = prompt(`Contribute to ${name} (Min: $${min}, Max: $${max}):`, min);
    if (amount && parseFloat(amount) >= parseFloat(min)) {
        fetch("{{ route('user.retirement.contribute') }}", {
            method: 'POST', 
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':"{{ csrf_token() }}"},
            body: JSON.stringify({id, amount})
        })
        .then(r => r.json().then(d => ({ok: r.ok, data: d})))
        .then(({ok, data}) => {
            if(ok && data.status) {
                toastr.success(data.status);
                setTimeout(() => window.location.reload(), 1500);
            } else {
                toastr.error(data.error || 'Failed');
            }
        })
        .catch(() => toastr.error('Network error'));
    }
}
</script>
@endpush
@endsection
