@extends('layouts.user.app')
@section('title', 'Premium Stocks')
@section('content')

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

.stock-amount::-webkit-inner-spin-button, 
.stock-amount::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }

.vip-bento-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 30px;
    margin-top: 24px;
}
</style>

@if(!$isUnlocked)
<!-- VIP Access Restricted -->
<div class="d-flex align-items-center justify-content-center" style="min-height: 70vh;">
    <div class="glass-card p-5 text-center" style="max-width: 520px;" data-aos="fade-up">
        <div style="width: 80px; height: 80px; border-radius: 24px; background: rgba(245, 158, 11, 0.05); border: 1px solid rgba(245, 158, 11, 0.2); display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; box-shadow: 0 0 30px rgba(245, 158, 11, 0.1);">
            <i class="ri-lock-2-fill" style="font-size: 32px; color: #f59e0b;"></i>
        </div>
        <h3 class="outfit font-weight-bold mb-2">Premium Access Required</h3>
        <p class="text-secondary mb-4">The VIP Stock Portfolio requires a minimum cumulative deposit of <strong class="text-white">$150,000.00</strong> to unlock exclusive high-value equities.</p>

        <!-- Qualification Progress -->
        @php $progress = min(100, ($totalDeposits / 150000) * 100); @endphp
        <div class="mb-4">
            <div class="d-flex justify-content-between mb-2">
                <span class="text-secondary small font-weight-bold uppercase" style="letter-spacing: 1px;">Qualification Progress</span>
                <span class="font-weight-bold" style="color: var(--accent-primary); font-size: 12px;">${{ number_format($totalDeposits, 0) }} / $150k</span>
            </div>
            <div style="background: rgba(255,255,255,0.05); border-radius: 99px; height: 10px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <div style="width: {{ $progress }}%; height: 100%; background: linear-gradient(90deg, #f59e0b, #ef4444); border-radius: 99px; transition: width 1s ease; box-shadow: 0 0 15px rgba(245, 158, 11, 0.3);"></div>
            </div>
            <div class="text-secondary mt-2 small">{{ number_format($progress, 1) }}% of capital requirement met</div>
        </div>

        <a href="{{ route('deposits') }}" class="btn btn-premium w-100 py-3 font-weight-bold">
            <i class="ri-add-circle-line mr-2"></i> Increase Capital to Qualify
        </a>
    </div>
</div>

@else
<!-- VIP Portfolio Unlocked -->
<!-- VIP Portfolio Unlocked -->
<div class="mb-5" data-aos="fade-up">
    <!-- Premium Portfolio Card Mirroring Mutual Funds -->
    <div class="glass-card-premium mb-5" style="padding: 2rem; border-radius: 24px; position: relative; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 100px; background: linear-gradient(to bottom, rgba(59, 130, 246, 0.05) 0%, transparent 100%); pointer-events: none;"></div>

        <div class="d-flex justify-content-between align-items-center mb-5 position-relative">
            <div>
                <h4 class="outfit font-weight-bold mb-1" style="color: var(--text-primary); letter-spacing: -0.5px;">Premium VIP Portfolio</h4>
                <div class="small" style="color: rgba(255,255,255,0.4); font-weight: 600;">High-Net-Worth Equity Positions</div>
            </div>
            <span class="badge" style="background: rgba(52, 211, 153, 0.1); color: #34d399; padding: 8px 16px; border-radius: 12px; font-weight: 800; font-size: 11px; letter-spacing: 1px; border: 1px solid rgba(52, 211, 153, 0.2);">
                {{ $portfolio->count() }} ACTIVE POSITIONS
            </span>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-6 col-lg-3">
                <div class="micro-label mb-2">Portfolio Value</div>
                <div class="hero-stat" style="color: var(--text-primary); font-size: 1.8rem;">${{ number_format($totalEquity, 2) }}</div>
            </div>
            <div class="col-6 col-lg-3 border-left" style="border-color: rgba(255,255,255,0.08) !important;">
                <div class="micro-label mb-2">Buying Power</div>
                <div class="hero-stat" style="color: #34d399; font-size: 1.8rem;">${{ number_format($currentBalance, 2) }}</div>
            </div>
            <div class="col-6 col-lg-3 border-left" style="border-color: rgba(255,255,255,0.08) !important;">
                <div class="micro-label mb-2">Total Exposure</div>
                <div class="hero-stat" style="color: var(--text-primary); font-size: 1.8rem;">{{ number_format($portfolio->count()) }} Securities</div>
            </div>
            <div class="col-6 col-lg-3 border-left" style="border-color: rgba(255,255,255,0.08) !important;">
                <div class="micro-label mb-2">Status</div>
                <div class="hero-stat" style="color: #f59e0b; font-size: 1.5rem;">VIP ELITE</div>
            </div>
        </div>

        @if($portfolio->count() > 0)
        <!-- Portfolio Table Refined -->
        <div class="table-responsive mt-3">
            <table class="table mb-0" style="border-collapse: separate; border-spacing: 0 12px;">
                <thead>
                    <tr>
                        <th class="micro-label border-0 pb-3" style="font-size: 10px; opacity: 0.5;">SECURITY</th>
                        <th class="micro-label border-0 pb-3" style="font-size: 10px; opacity: 0.5;">SIZE</th>
                        <th class="micro-label border-0 pb-3" style="font-size: 10px; opacity: 0.5;">AVG PRICE</th>
                        <th class="micro-label border-0 pb-3" style="font-size: 10px; opacity: 0.5;">MKT VALUE</th>
                        <th class="micro-label border-0 pb-3 text-right" style="font-size: 10px; opacity: 0.5;">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($portfolio as $pos)
                    <tr class="portfolio-row" style="background: rgba(255,255,255,0.03); border-radius: 12px; transition: all 0.3s ease;">
                        <td class="align-middle py-3 border-0" style="border-radius: 12px 0 0 12px; padding-left: 1.5rem;">
                            <div class="d-flex align-items-center">
                                <div class="mr-3" style="width: 32px; height: 32px; background: rgba(59, 130, 246, 0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <x-asset-logo :symbol="$pos->symbol" size="sm" />
                                </div>
                                <div>
                                    <div class="font-weight-bold portfolio-fund-name" style="color: var(--text-primary); font-size: 0.95rem;">{{ $pos->symbol }}</div>
                                    <div class="small text-secondary" style="font-size: 0.7rem; font-weight: 600; opacity: 0.6;">{{ $pos->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="align-middle py-3 border-0 font-weight-bold" style="color: rgba(255,255,255,0.8); font-size: 0.95rem;">{{ number_format($pos->units, 4) }} Shares</td>
                        <td class="align-middle py-3 border-0 small font-weight-bold" style="color: rgba(255,255,255,0.5);">${{ number_format($pos->buy, 2) }}</td>
                        <td class="align-middle py-3 border-0 font-weight-bold" style="color: #34d399; font-size: 0.95rem;">${{ number_format($pos->units * $pos->buy, 2) }}</td>
                        <td class="align-middle py-3 border-0 text-right" style="border-radius: 0 12px 12px 0; padding-right: 1.5rem;">
                            <a href="{{ route('stocks.trade-single', $pos->stock_id) }}" class="btn btn-sm btn-outline-primary" style="font-size: 0.7rem; font-weight: 800; border-radius: 8px; text-transform: uppercase;">Manage</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-4 text-center mt-3" style="background: rgba(255,255,255,0.02); border-radius: 16px; border: 1px dashed rgba(255,255,255,0.1);">
            <p class="text-secondary mb-0 small uppercase font-weight-bold" style="letter-spacing: 1px;">No premium positions currently held.</p>
        </div>
        @endif
    </div>
</div>

<!-- VIP Stock Grid Full-Bleed Redesign -->
<div class="vip-bento-grid mb-5" data-aos="fade-up">
    @forelse($vipStocks as $stock)
    <div class="glass-card-premium h-100" style="overflow: hidden; padding: 0;">
        <div class="card-hero-banner" style="position: relative; height: 140px; width: 100%; overflow: hidden; background: #000000;">
            <!-- VIP Premium Background -->
            <img src="{{ asset('storage/image/default_investment.png') }}" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.6; filter: brightness(0.7) contrast(1.1); transition: transform 0.6s ease; z-index: 1;">
            
            <div class="hero-gradient" style="position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(0, 0, 0,0) 0%, rgba(0, 0, 0,0.6) 40%, rgba(0, 0, 0,0.98) 100%); z-index: 2;"></div>
            
            <!-- Asset Logo Floating Center -->
            <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; z-index: 3;">
                <div style="width: 80px; height: 80px; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border-radius: 20px; border: 1px solid rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; box-shadow: 0 0 30px rgba(0,0,0,0.5);">
                    <x-asset-logo :symbol="$stock->symbol" size="md" />
                </div>
            </div>

            <div style="position: absolute; top: 1.25rem; right: 1.25rem; z-index: 10;">
                <span style="background: rgba(245, 158, 11, 0.2); color: #f59e0b; font-size: 9px; font-weight: 900; padding: 6px 12px; border-radius: 8px; text-transform: uppercase; letter-spacing: 1.5px; border: 1px solid rgba(245, 158, 11, 0.3); backdrop-filter: blur(8px);">
                    VIP Premium
                </span>
            </div>
        </div>

        <div class="card-body-premium" style="padding: 1.5rem; padding-top: 0.5rem; position: relative; z-index: 10; display: flex; flex-direction: column;">
            <div class="mb-4">
                <h5 class="outfit font-weight-bold mb-1" style="color: var(--text-primary); font-size: 1.2rem; letter-spacing: -0.5px;">
                    {{ str_replace(' (Class A)', '', $stock->name) }}
                </h5>
                <div class="small" style="color: rgba(255,255,255,0.3); font-weight: 700; font-size: 0.75rem;">
                    <span style="color: #3b82f6;">{{ $stock->symbol }}</span> · {{ $stock->volume }} VOLUME
                </div>
            </div>

            <div class="mb-4">
                <div class="micro-label">Share Price</div>
                <div class="hero-stat" style="color: #ff3333;">${{ number_format($stock->buy, 2) }}</div>
            </div>

            <div class="invest-input-wrapper mt-auto" style="background: rgba(0, 0, 0, 0.5); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 16px; display: flex; align-items: center; overflow: hidden;">
                <input type="number" class="stock-amount" placeholder="Number of Shares" style="background: transparent; border: none; color: white; padding: 0 1.25rem; flex-grow: 1; height: 50px; font-weight: 700; font-size: 0.9rem; width: 50%;">
                <button class="buy-vip-btn" data-id="{{ $stock->id }}" style="background: #3b82f6; color: white; border: none; font-weight: 900; padding: 0 1.25rem; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1.5px; height: 50px; white-space: nowrap; transition: all 0.3s ease;">
                    Buy Shares
                </button>
            </div>
        </div>
    </div>
    @empty
    <div class="glass-card p-5 text-center col-12">
        <i class="ri-inbox-line style='font-size: 3rem; color: var(--text-muted); opacity: 0.5; mb-3 d-block'"></i>
        <p class="text-secondary">No VIP securities currently listed for trade.</p>
    </div>
    @endforelse
</div>

<div class="pagination-wrapper mt-5 d-flex justify-content-center">
    {{ $vipStocks->links() }}
</div>

@push('js')
<script>
$(document).on('click', '.buy-vip-btn', function(){
    var btn = $(this);
    var amount = btn.closest('.glass-card-premium').find('.stock-amount').val();
    var stockId = btn.data('id');
    if(!amount || amount <= 0) { 
        toastr.error('Please enter a valid investment amount.'); 
        return; 
    }
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
    $.ajax({
        url: "{{ route('stocks.trade-post') }}",
        method: 'POST',
        data: { id: stockId, amount: amount, _token: '{{ csrf_token() }}' },
        success: function(res) {
            if(res.error) toastr.error(res.error);
            else if(res.message) toastr.error(res.message);
            else { 
                toastr.success(res.status || 'Investment successful. Portfolio updated.'); 
                setTimeout(() => location.reload(), 1500); 
            }
        },
        error: function(xhr) { 
            var msg = 'Investment execution failed.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }
            toastr.error(msg); 
        },
        complete: function() { btn.prop('disabled', false).text('Buy Shares'); }
    });
});
</script>
@endpush
@endif

@endsection
