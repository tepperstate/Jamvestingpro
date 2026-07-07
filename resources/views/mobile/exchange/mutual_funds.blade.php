@extends('layouts.user.app')
@section('title', 'Strategies')
@section('content')

<div class="mobile-container pb-5 px-3 mt-3">
    
    <!-- Portfolio Summary -->
    @if($userInvestments->count() > 0)
    <div class="glass-card-gold p-4 mb-4" style="border-radius: 24px; position: relative; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 80px; background: linear-gradient(to bottom, rgba(153, 0, 0, 0.1) 0%, transparent 100%); pointer-events: none;"></div>

        <div class="d-flex justify-content-between align-items-center mb-4 position-relative">
            <div>
                <h4 class="font-weight-bold mb-0 text-white" style="font-family: 'Outfit', sans-serif;">Strategies</h4>
                <div class="small text-secondary" style="font-size: 0.75rem;">Managed Mutual Fund Assets</div>
            </div>
            <div class="text-right">
                <span class="badge" style="background: rgba(153, 0, 0, 0.1); color: #990000; border-radius: 12px; font-size: 0.65rem;">
                    {{ $userInvestments->count() }} ACTIVE
                </span>
                <div class="small mt-1" style="color: #990000; font-weight: 600; font-size: 0.65rem;">
                    Drip in <span id="drip-countdown">60</span>s
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-6">
                <div class="small text-secondary mb-1" style="font-size: 0.7rem;">Total Invested</div>
                <div class="h4 text-white font-weight-bold mb-0">${{ number_format($totalInvested, 2) }}</div>
            </div>
            <div class="col-6">
                <div class="small text-secondary mb-1" style="font-size: 0.7rem;">Current Value</div>
                <div class="h4 text-gold font-weight-bold mb-0">${{ number_format($totalCurrent, 2) }}</div>
            </div>
            <div class="col-6">
                <div class="small text-secondary mb-1" style="font-size: 0.7rem;">Unrealized P/L</div>
                @php $totalPL = $totalCurrent - $totalInvested; @endphp
                <div class="h5 font-weight-bold mb-0 pl-total-val" data-pl="{{ $totalPL }}" style="color: {{ $totalPL >= 0 ? '#ff3333' : '#f43f5e' }};">
                    {{ $totalPL >= 0 ? '+' : '' }}${{ number_format($totalPL, 2) }}
                </div>
            </div>
            <div class="col-6">
                <div class="small text-secondary mb-1" style="font-size: 0.7rem;">Total Return</div>
                @php $returnPct = $totalInvested > 0 ? (($totalPL / $totalInvested) * 100) : 0; @endphp
                <div class="h5 font-weight-bold mb-0" style="color: {{ $returnPct >= 0 ? '#ff3333' : '#f43f5e' }};">
                    {{ $returnPct >= 0 ? '+' : '' }}{{ number_format($returnPct, 2) }}%
                </div>
            </div>
        </div>

        <h5 class="text-white font-weight-bold mb-3" style="font-family: 'Outfit', sans-serif; font-size: 1rem;">Active Positions</h5>
        <div class="d-flex flex-column gap-3">
            @foreach($userInvestments as $inv)
            @php
                $cv = $inv->units * $inv->fund->nav_price;
                $pl = $cv - $inv->amount;
                $category = strtolower($inv->fund->category ?? 'global');
            @endphp
            <div class="p-3 rounded" style="background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.05);">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('storage/image/mutual_fund_' . $category . '_logo.svg') }}" style="width: 24px; height: 24px; object-fit: contain; margin-right: 8px;">
                        <div>
                            <div class="font-weight-bold text-white" style="font-size: 0.85rem;">{{ $inv->fund->name }}</div>
                            <div class="small text-gold" style="font-size: 0.65rem;">{{ $inv->fund->symbol }}</div>
                        </div>
                    </div>
                    <button class="btn btn-sm redeem-btn-mobile" data-id="{{ $inv->id }}" data-name="{{ $inv->fund->name }}" data-value="{{ number_format($cv, 2) }}">
                        Redeem
                    </button>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <div>
                        <div class="small text-secondary" style="font-size: 0.65rem;">Invested</div>
                        <div class="text-white" style="font-size: 0.8rem;">${{ number_format($inv->amount, 2) }}</div>
                    </div>
                    <div>
                        <div class="small text-secondary" style="font-size: 0.65rem;">Market Val</div>
                        <div class="text-white" style="font-size: 0.8rem;">${{ number_format($cv, 2) }}</div>
                    </div>
                    <div class="text-right">
                        <div class="small text-secondary" style="font-size: 0.65rem;">P/L</div>
                        <div class="pl-val" data-pl="{{ $pl }}" style="font-size: 0.8rem; font-weight: bold; color: {{ $pl >= 0 ? '#ff3333' : '#f43f5e' }};">
                            {{ $pl >= 0 ? '+' : '' }}${{ number_format($pl, 2) }}
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Available Funds -->
    <h4 class="font-weight-bold text-white mb-3 mt-4" style="font-family: 'Outfit', sans-serif;">Available Funds</h4>
    <div class="d-flex flex-column gap-4 mb-4">
        @forelse($funds as $fund)
        @php
            $category = strtolower($fund->category ?? 'global');
            $logoImage = 'mutual_fund_' . $category . '_logo.svg';
        @endphp
        <div class="glass-card-gold overflow-hidden" style="border-radius: 20px;">
            <div class="position-relative" style="height: 80px; background: #000;">
                <img src="{{ asset('storage/image/' . $logoImage) }}" alt="{{ $fund->name }}" style="width: 100%; height: 100%; object-fit: cover; filter: contrast(1.1) brightness(1.1); opacity: 0.5;">
                <div class="position-absolute w-100 h-100 top-0 left-0" style="background: linear-gradient(to bottom, transparent 0%, #0a0b0e 100%);"></div>
                <div class="position-absolute px-3 w-100" style="bottom: 10px;">
                    <span class="badge" style="background: rgba(153, 0, 0, 0.2); color: #990000; font-size: 0.6rem; letter-spacing: 1px;">
                        {{ strtoupper($fund->category ?? 'GLOBAL') }}
                    </span>
                </div>
            </div>
            
            <div class="p-3">
                <h5 class="font-weight-bold text-white mb-1 text-truncate" style="font-size: 1rem;">
                    {{ str_replace(' ('.$fund->symbol.')', '', $fund->name) }}
                </h5>
                <div class="small text-secondary mb-3" style="font-size: 0.7rem;">
                    <span class="text-gold">{{ $fund->symbol }}</span> · {{ strtoupper($fund->risk_level) }} RISK
                </div>

                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom" style="border-color: rgba(255,255,255,0.05) !important;">
                    <div>
                        <div class="small text-secondary" style="font-size: 0.65rem;">NAV PRICE</div>
                        <div class="text-white font-weight-bold">${{ number_format($fund->nav_price, 2) }}</div>
                    </div>
                    <div class="text-right">
                        <div class="small text-secondary" style="font-size: 0.65rem;">MIN INVEST</div>
                        <div class="text-white font-weight-bold">${{ number_format($fund->min_investment) }}</div>
                    </div>
                </div>

                <div class="d-flex align-items-center" style="background: rgba(0,0,0,0.5); border-radius: 12px; border: 1px solid rgba(255,215,0,0.1); padding: 4px;">
                    <input type="number" class="invest-amount form-control text-white border-0 shadow-none bg-transparent" placeholder="${{ number_format($fund->min_investment) }}+" min="{{ $fund->min_investment }}" style="font-size: 0.85rem;">
                    <button class="btn btn-gold invest-btn rounded-pill px-3 py-2" data-id="{{ $fund->id }}" data-name="{{ $fund->name }}" data-min="{{ $fund->min_investment }}" style="font-size: 0.75rem; font-weight: bold; white-space: nowrap;">
                        Invest
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="glass-card-gold p-4 text-center">
            <i class="fa fa-landmark text-secondary mb-2" style="font-size: 32px;"></i>
            <p class="text-secondary small mb-0">No Funds Available</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mb-5">
        {{ $funds->links() }}
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
    .redeem-btn-mobile {
        background: rgba(244, 63, 94, 0.1);
        color: #f43f5e;
        border: 1px solid rgba(244, 63, 94, 0.2);
        font-size: 0.7rem;
        font-weight: bold;
        padding: 4px 12px;
        border-radius: 8px;
    }
</style>

@push('js')
<script>
$(document).ready(function(){
    // Invest
    $(document).on('click', '.invest-btn', function(){
        var btn = $(this);
        var amount = btn.closest('.glass-card-gold').find('.invest-amount').val();
        var fundId = btn.data('id');
        var min = btn.data('min');

        if(!amount || parseFloat(amount) < parseFloat(min)) {
            toastr.error('Minimum investment is $' + min);
            return;
        }

        btn.prop('disabled', true).text('...');

        $.ajax({
            url: "{{ route('user.mutual_fund.invest') }}",
            method: 'POST',
            data: { fund_id: fundId, amount: amount, _token: '{{ csrf_token() }}' },
            success: function(res) {
                if(res.error) toastr.error(res.error);
                else { toastr.success(res.status); setTimeout(() => location.reload(), 1500); }
            },
            error: function(xhr) {
                var err = xhr.responseJSON;
                toastr.error(err && err.error ? err.error : 'Investment failed.');
            },
            complete: function() { btn.prop('disabled', false).text('Invest'); }
        });
    });

    // Redeem
    $(document).on('click', '.redeem-btn-mobile', function(){
        var id = $(this).data('id');
        var name = $(this).data('name');
        if(!confirm('Redeem your investment in ' + name + '?')) return;

        var btn = $(this);
        btn.prop('disabled', true).text('...');

        $.ajax({
            url: "{{ route('user.mutual_fund.redeem') }}",
            method: 'POST',
            data: { id: id, _token: '{{ csrf_token() }}' },
            success: function(res) {
                toastr.success(res.status);
                setTimeout(() => location.reload(), 1500);
            },
            error: function() { 
                toastr.error('Redemption failed.'); 
                btn.prop('disabled', false).text('Redeem');
            }
        });
    });

    // Drip Profit
    if ($('.pl-val').length > 0 || $('.pl-total-val').length > 0) {
        let dripSeconds = 60;
        setInterval(() => {
            dripSeconds--;
            if(dripSeconds <= 0) {
                dripSeconds = 60;
                $('.pl-val, .pl-total-val').each(function() {
                    let el = $(this)[0];
                    let current = parseFloat(el.getAttribute('data-pl') || 0);
                    let dripAmount = $(this).hasClass('pl-total-val') ? ($('.pl-val').length * 0.25) + Math.random()*0.5 : Math.random()*0.5 + 0.01;
                    let newPl = current + dripAmount;
                    el.setAttribute('data-pl', newPl);
                    el.innerText = (newPl >= 0 ? '+' : '') + '$' + newPl.toFixed(2);
                    if (newPl >= 0) el.style.color = '#ff3333';
                });
            }
            $('#drip-countdown').text(dripSeconds);
        }, 1000);
    }
});
</script>
@endpush
@endsection
