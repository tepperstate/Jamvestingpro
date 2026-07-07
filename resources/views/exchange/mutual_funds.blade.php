@extends('layouts.user.app')
@section('title', 'Strategies')
@section('content')



<!-- Portfolio Summary -->
@if($userInvestments->count() > 0)
<div class="glass-card-premium mb-5" data-aos="fade-up" style="padding: 2rem; border-radius: 24px; position: relative; overflow: hidden;">
    <!-- Subtle Gradient Background for the card name -->
    <div style="position: absolute; top: 0; left: 0; right: 0; height: 100px; background: linear-gradient(to bottom, rgba(var(--accent-primary-rgb), 0.05) 0%, transparent 100%); pointer-events: none;"></div>

    <div class="d-flex justify-content-between align-items-center mb-5 position-relative">
        <div>
            <h4 class="outfit font-weight-bold mb-1" style="color: var(--text-primary); letter-spacing: -0.5px;">Investment Strategies</h4>
            <div class="small" style="color: rgba(255,255,255,0.4); font-weight: 600;">Managed Mutual Fund Assets</div>
        </div>
        <div class="text-right">
            <span class="badge mb-2" style="background: rgba(255, 51, 51, 0.1); color: #ff3333; padding: 8px 16px; border-radius: 12px; font-weight: 800; font-size: 11px; letter-spacing: 1px; border: 1px solid rgba(255, 51, 51, 0.2);">
                {{ $userInvestments->count() }} ACTIVE POSITIONS
            </span>
            <div class="drip-timer small" style="color: var(--card-accent); font-weight: 700; font-size: 0.75rem;">
                <i class="fa fa-tint"></i> Next Profit Drip in <span id="drip-countdown">60</span>s
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-6 col-lg-3">
            <div class="micro-label mb-2">Total Invested</div>
            <div class="hero-stat" style="color: var(--text-primary); font-size: 1.8rem;">${{ number_format($totalInvested, 2) }}</div>
        </div>
        <div class="col-6 col-lg-3 border-left" style="border-color: rgba(255,255,255,0.08) !important;">
            <div class="micro-label mb-2">Current Value</div>
            <div class="hero-stat" style="color: #ff3333; font-size: 1.8rem;">${{ number_format($totalCurrent, 2) }}</div>
        </div>
        <div class="col-6 col-lg-3 border-left" style="border-color: rgba(255,255,255,0.08) !important;">
            <div class="micro-label mb-2">Unrealized P/L</div>
            @php $totalPL = $totalCurrent - $totalInvested; @endphp
            <div class="hero-stat pl-total-cell" style="color: {{ $totalPL >= 0 ? '#ff3333' : '#f43f5e' }}; font-size: 1.8rem;">
                <span class="pl-total-val" data-pl="{{ $totalPL }}">{{ $totalPL >= 0 ? '+' : '' }}${{ number_format($totalPL, 2) }}</span>
            </div>
        </div>
        <div class="col-6 col-lg-3 border-left" style="border-color: rgba(255,255,255,0.08) !important;">
            <div class="micro-label mb-2">Total Return</div>
            @php $returnPct = $totalInvested > 0 ? (($totalPL / $totalInvested) * 100) : 0; @endphp
            <div class="hero-stat" style="color: {{ $returnPct >= 0 ? '#ff3333' : '#f43f5e' }}; font-size: 1.8rem;">
                {{ $returnPct >= 0 ? '+' : '' }}{{ number_format($returnPct, 2) }}%
            </div>
        </div>
    </div>

    <!-- Active Investments Table Refined -->
    <div class="table-responsive">
        <table class="table mb-0" style="border-collapse: separate; border-spacing: 0 12px;">
            <thead>
                <tr>
                    <th class="micro-label border-0 pb-3" style="font-size: 10px; opacity: 0.5;">FUND NAME</th>
                    <th class="micro-label border-0 pb-3" style="font-size: 10px; opacity: 0.5;">INVESTED</th>
                    <th class="micro-label border-0 pb-3" style="font-size: 10px; opacity: 0.5;">UNITS</th>
                    <th class="micro-label border-0 pb-3" style="font-size: 10px; opacity: 0.5;">MKT VALUE</th>
                    <th class="micro-label border-0 pb-3" style="font-size: 10px; opacity: 0.5;">P/L</th>
                    <th class="micro-label border-0 pb-3 text-right" style="font-size: 10px; opacity: 0.5;">MANAGE</th>
                </tr>
            </thead>
            <tbody id="portfolioBody">
                @foreach($userInvestments as $inv)
                @php
                    $cv = $inv->units * $inv->fund->nav_price;
                    $pl = $cv - $inv->amount;
                    $category = strtolower($inv->fund->category ?? 'global');
                @endphp
                <tr class="portfolio-row" style="background: rgba(255,255,255,0.03); border-radius: 12px; transition: all 0.3s ease;">
                    <td class="align-middle py-3 border-0" style="border-radius: 12px 0 0 12px; padding-left: 1.5rem;">
                        <div class="d-flex align-items-center">
                            <div class="mr-3" style="width: 32px; height: 32px; background: rgba(var(--card-accent-rgb), 0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <img src="{{ asset('storage/image/mutual_fund_' . $category . '_logo.svg') }}" style="width: 20px; height: 20px; object-fit: contain;">
                            </div>
                            <div>
                                <div class="font-weight-bold portfolio-fund-name" style="color: var(--text-primary); font-size: 0.95rem;">{{ $inv->fund->name }}</div>
                                <div class="small accent-text" style="font-size: 0.7rem; font-weight: 800; opacity: 0.6;">{{ $inv->fund->symbol }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="align-middle py-3 border-0 font-weight-bold" style="color: rgba(255,255,255,0.8); font-size: 0.95rem;">${{ number_format($inv->amount, 2) }}</td>
                    <td class="align-middle py-3 border-0 small font-weight-bold" style="color: rgba(255,255,255,0.5);">{{ number_format($inv->units, 4) }}</td>
                    <td class="align-middle py-3 border-0 font-weight-bold" style="color: var(--text-primary); font-size: 0.95rem;">${{ number_format($cv, 2) }}</td>
                    <td class="align-middle py-3 border-0 pl-cell" style="color: {{ $pl >= 0 ? '#ff3333' : '#f43f5e' }}; font-weight: 800; font-size: 0.95rem;">
                        <span class="pl-val" data-pl="{{ $pl }}">{{ $pl >= 0 ? '+' : '' }}${{ number_format($pl, 2) }}</span>
                    </td>
                    <td class="align-middle py-3 border-0 text-right" style="border-radius: 0 12px 12px 0; padding-right: 1.5rem;">
                        <button class="redeem-btn-premium" data-id="{{ $inv->id }}" data-name="{{ $inv->fund->name }}" data-value="{{ number_format($cv, 2) }}">
                            Redeem
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Available Funds -->
<h5 class="outfit font-weight-bold mb-3">Available Funds</h5>
<div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-3 gy-4 mb-5" data-aos="fade-up" data-aos-delay="200">
    @forelse($funds as $fund)
    @php
        $category = strtolower($fund->category ?? 'global');
        $fundClass = 'fund-' . $category;
        $logoImage = 'mutual_fund_' . $category . '_logo.svg';
        
        // High-Fidelity Deterministic Sparkline
        $seed = crc32($fund->name);
        mt_srand($seed);
        $points = [];
        $curr = 40;
        for($i=0; $i<=12; $i++) {
            $curr += mt_rand(-10, 15);
            $points[] = ($i * 8.33) . ',' . (100 - $curr);
        }
        $path = "M " . implode(" L ", $points);
    @endphp
    <div class="col mb-4">
        <div class="glass-card-premium h-100 {{ $fundClass }}" style="overflow: hidden; padding: 0;">
            <div class="card-hero-banner" style="position: relative; height: 100px; width: 100%; overflow: hidden; background: #000000;">
                <img src="{{ asset('storage/image/' . $logoImage) }}" alt="{{ $fund->name }}" style="width: 100%; height: 100%; object-fit: cover; filter: contrast(1.1) brightness(1.1); transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1); z-index: 1;">
                <div class="hero-gradient" style="position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(0, 0, 0,0) 0%, rgba(0, 0, 0,0.6) 40%, rgba(0, 0, 0,0.98) 100%); z-index: 2;"></div>
                
                <div class="watchlist-toggle" title="Add to Watchlist" style="position: absolute; top: 0.75rem; right: 0.75rem; background: rgba(0, 0, 0, 0.4); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.15); color: rgba(255, 255, 255, 0.7); width: 28px; height: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease; z-index: 10; font-size: 0.8rem;">
                    <i class="far fa-star"></i>
                </div>

                <div class="sparkline-container" style="position: absolute; bottom: 0; left: 0; right: 0; height: 50px; z-index: 5;">
                    <svg viewBox="0 0 100 100" preserveAspectRatio="none" class="sparkline-svg">
                        <path d="{{ $path }}" class="sparkline-path"></path>
                    </svg>
                </div>
            </div>

            <div class="card-body-premium" style="padding: 1rem; padding-top: 0.5rem; position: relative; z-index: 10; display: flex; flex-direction: column; flex: 1;">
                <div class="mb-3">
                    <span class="badge mb-2 accent-bg-soft accent-text" style="padding: 4px 8px; border-radius: 6px; font-size: 8px; font-weight: 900; letter-spacing: 1px; border: 1px solid rgba(var(--card-accent-rgb), 0.3);">
                        {{ strtoupper($fund->category ?? 'GLOBAL') }}
                    </span>
                    <h5 class="outfit font-weight-bold mb-1 text-truncate" style="color: var(--text-primary); font-size: 1.05rem; letter-spacing: -0.2px;">
                        {{ str_replace(' ('.$fund->symbol.')', '', $fund->name) }}
                    </h5>
                    <div class="small" style="color: rgba(255,255,255,0.3); font-weight: 700; font-size: 0.7rem;">
                        <span class="accent-text">{{ $fund->symbol }}</span> · {{ strtoupper($fund->risk_level) }} RISK
                    </div>
                </div>



                <div class="stat-grid py-2 border-top" style="border-color: rgba(255,255,255,0.08) !important; margin-bottom: 0.5rem;">
                    <div>
                        <div class="micro-label" style="font-size: 0.65rem;">NAV PRICE</div>
                        <div class="font-weight-bold" style="color: var(--text-primary); font-size: 0.9rem;">${{ number_format($fund->nav_price, 2) }}</div>
                    </div>
                    <div class="text-right">
                        <div class="micro-label" style="font-size: 0.65rem;">MIN INVEST</div>
                        <div class="font-weight-bold" style="color: var(--text-primary); font-size: 0.9rem;">${{ number_format($fund->min_investment) }}</div>
                    </div>
                </div>

                <div class="invest-input-wrapper" style="background: rgba(0, 0, 0, 0.5); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; display: flex; align-items: center; overflow: hidden; margin-top: auto;">
                    <input type="number" class="invest-amount" placeholder="${{ number_format($fund->min_investment) }}+" min="{{ $fund->min_investment }}" style="background: transparent; border: none; color: white; padding: 0 0.75rem; flex-grow: 1; height: 42px; font-weight: 700; font-size: 0.85rem; width: 50%;">
                    <button class="invest-btn" data-id="{{ $fund->id }}" data-name="{{ $fund->name }}" data-min="{{ $fund->min_investment }}" style="background: var(--card-accent); color: #0c1220; border: none; font-weight: 900; padding: 0 1rem; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; height: 42px; white-space: nowrap;">
                        Invest Now
                    </button>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="glass-card p-5 text-center w-100">
            <i class="fa fa-landmark mb-3" style="font-size: 48px; color: var(--text-secondary);"></i>
            <h5 class="outfit">No Funds Available</h5>
            <p class="text-secondary">Mutual investment funds will be available soon.</p>
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center mt-4 mb-5">
    <div class="pagination-wrapper glass-card p-2 px-4" style="border-radius: 99px;">
        {{ $funds->links() }}
    </div>
</div>

@push('js')
<script>
$(document).ready(function(){
    // Invest
    $(document).on('click', '.invest-btn', function(){
        var btn = $(this);
        var amount = btn.closest('.glass-card-premium').find('.invest-amount').val();
        var fundId = btn.data('id');
        var min = btn.data('min');

        if(!amount || parseFloat(amount) < parseFloat(min)) {
            toastr.clear();
            toastr.error('Minimum investment is $' + min);
            return;
        }

        btn.prop('disabled', true).text('Processing...');

        $.ajax({
            url: "{{ route('user.mutual_fund.invest') }}",
            method: 'POST',
            data: { fund_id: fundId, amount: amount, _token: '{{ csrf_token() }}' },
            success: function(res) {
                if(res.error) { toastr.error(res.error); }
                else { toastr.success(res.status); setTimeout(() => location.reload(), 1500); }
            },
            error: function(xhr) {
                var err = xhr.responseJSON;
                toastr.clear();
                toastr.error(err && err.error ? err.error : 'Investment failed.');
            },
            complete: function() { btn.prop('disabled', false).text('Invest Now'); }
        });
    });

    // Redeem
    $(document).on('click', '.redeem-btn-premium', function(){
        var id = $(this).data('id');
        var name = $(this).data('name');
        if(!confirm('Redeem your investment in ' + name + '?')) return;

        var btn = $(this);
        btn.prop('disabled', true).text('Working...');

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
});
</script>

<script type="module">
import { animate } from "https://cdn.jsdelivr.net/npm/motion@11.11.13/+esm";

$(document).ready(function(){
    if ($('.pl-val').length > 0 || $('.pl-total-val').length > 0) {
        let dripSeconds = 60;
        
        setInterval(() => {
            dripSeconds--;
            if(dripSeconds <= 0) {
                dripSeconds = 60;
                
                // Animate each active position P/L
                $('.pl-val').each(function() {
                    let el = $(this)[0];
                    let current = parseFloat(el.getAttribute('data-pl') || 0);
                    let dripAmount = (Math.random() * 0.5) + 0.01; // Drip a small random profit
                    let newPl = current + dripAmount;
                    el.setAttribute('data-pl', newPl);

                    animate(0, 1, {
                        duration: 1,
                        onUpdate: (progress) => {
                            let val = current + (newPl - current) * progress;
                            el.innerText = (val >= 0 ? '+' : '') + '$' + val.toFixed(2);
                            if (val >= 0) el.parentElement.style.color = '#ff3333';
                        }
                    });
                });

                // Animate total P/L
                $('.pl-total-val').each(function() {
                    let el = $(this)[0];
                    let current = parseFloat(el.getAttribute('data-pl') || 0);
                    // Drip amount is sum of individual drips approximately, but we'll just do a larger drip
                    let totalDrip = ($('.pl-val').length * 0.25) + (Math.random() * 0.5); 
                    let newPl = current + totalDrip;
                    el.setAttribute('data-pl', newPl);

                    animate(0, 1, {
                        duration: 1,
                        onUpdate: (progress) => {
                            let val = current + (newPl - current) * progress;
                            el.innerText = (val >= 0 ? '+' : '') + '$' + val.toFixed(2);
                            if (val >= 0) el.parentElement.style.color = '#ff3333';
                        }
                    });
                });
            }
            $('#drip-countdown').text(dripSeconds);
        }, 1000);
    }
});
</script>
@endpush

<style>
.glass-card-premium { transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); border: 1px solid rgba(255,255,255,0.08); background: #000000 !important; backdrop-filter: blur(20px); }
.glass-card-premium:hover { transform: translateY(-8px); border-color: var(--card-accent) !important; box-shadow: 0 20px 40px rgba(0,0,0,0.4), 0 0 20px rgba(var(--card-accent-rgb), 0.2); }
.glass-card-premium:hover .card-hero-banner img { transform: scale(1.15) translateY(-5px); }

.portfolio-row:hover { background: rgba(255,255,255,0.06) !important; }
.portfolio-fund-name { transition: color 0.3s ease; }
.portfolio-row:hover .portfolio-fund-name { color: var(--card-accent) !important; }

.redeem-btn-premium { 
    background: rgba(244, 63, 94, 0.1); 
    color: #f43f5e; 
    border: 1px solid rgba(244, 63, 94, 0.2);
    font-weight: 800; 
    padding: 0.5rem 1.25rem; 
    border-radius: 12px;
    text-transform: uppercase; 
    font-size: 0.75rem; 
    letter-spacing: 1px;
    transition: all 0.3s ease;
}
.redeem-btn-premium:hover { 
    background: #f43f5e; 
    color: white; 
    box-shadow: 0 0 20px rgba(244, 63, 94, 0.4);
}

.row.g-4 { margin-top: -1.5rem; margin-bottom: 2rem; }
.row.g-4 > .col { padding-top: 1.5rem; padding-bottom: 1.5rem; }
.invest-amount::-webkit-inner-spin-button, 
.invest-amount::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
</style>
@endsection
