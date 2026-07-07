@extends('layouts.user.app')
@section('title', 'Premium Stocks')
@section('content')
<style>
    .mobile-glass-container { padding: 15px; padding-bottom: 80px; font-family: 'Outfit', sans-serif; background: #0a0b0e; color: #fff; min-height: 100vh; }
    .glass-card { background: rgba(255,255,255,0.03); backdrop-filter: blur(16px); border: 1px solid rgba(255,215,0,0.15); border-radius: 20px; padding: 20px; margin-bottom: 20px; overflow: hidden; position: relative; }
    .gold-accent { color: #FFD700; }
    .btn-gold { background: linear-gradient(135deg, #FFD700 0%, #990000 100%); color: #000; border: none; border-radius: 12px; padding: 15px; font-weight: 800; width: 100%; text-transform: uppercase; margin-top: 15px; }
    
    .portfolio-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px; }
    .portfolio-stat { background: rgba(0,0,0,0.3); padding: 12px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05); }
    .stat-label { font-size: 10px; color: rgba(255,255,255,0.5); text-transform: uppercase; font-weight: 800; margin-bottom: 4px; }
    .stat-value { font-size: 18px; font-weight: 800; color: #fff; }

    .position-item { background: rgba(255,255,255,0.02); border-left: 2px solid #FFD700; border-radius: 12px; padding: 12px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; }
    
    .stock-card { background: #000; border: 1px solid rgba(255,215,0,0.2); border-radius: 20px; overflow: hidden; margin-bottom: 20px; }
    .stock-header { height: 100px; position: relative; background: url('{{ asset('storage/image/default_investment.png') }}') center/cover; }
    .stock-header::after { content: ''; position: absolute; inset: 0; background: linear-gradient(to bottom, transparent, #000); }
    .stock-logo-wrap { position: absolute; bottom: -20px; left: 20px; z-index: 2; width: 50px; height: 50px; background: rgba(255,255,255,0.1); backdrop-filter: blur(5px); border-radius: 12px; border: 1px solid rgba(255,215,0,0.3); display: flex; justify-content: center; align-items: center; }
    .stock-body { padding: 30px 20px 20px; }
    .input-invest { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff; border-radius: 8px; padding: 10px; width: 100%; text-align: center; margin-bottom: 10px; }
</style>

<div class="mobile-glass-container">
    @if(!$isUnlocked)
    <!-- Locked State -->
    <div class="glass-card text-center" style="margin-top: 20px;">
        <i class="ri-lock-2-fill gold-accent" style="font-size: 50px; margin-bottom: 15px;"></i>
        <h3 style="font-weight: 800; font-size: 20px; margin-bottom: 10px;">Premium Access</h3>
        <p style="font-size: 13px; color: rgba(255,255,255,0.6); margin-bottom: 20px;">Requires a cumulative deposit of <strong class="gold-accent">$150,000.00</strong> to unlock VIP equities.</p>
        
        @php $progress = min(100, ($totalDeposits / 150000) * 100); @endphp
        <div style="text-align: left; margin-bottom: 20px;">
            <div class="d-flex justify-content-between mb-2" style="font-size: 11px; font-weight: 800;">
                <span style="color: rgba(255,255,255,0.5);">Progress</span>
                <span class="gold-accent">${{ number_format($totalDeposits, 0) }} / $150k</span>
            </div>
            <div style="background: rgba(255,255,255,0.1); height: 8px; border-radius: 4px; overflow: hidden;">
                <div style="width: {{ $progress }}%; height: 100%; background: #FFD700; border-radius: 4px;"></div>
            </div>
        </div>
        
        <a href="{{ route('deposits') }}" class="btn-gold" style="text-decoration: none; display: block;">Increase Capital</a>
    </div>
    @else
    <!-- Unlocked State -->
    <h2 style="font-size: 24px; font-weight: 800; margin-bottom: 15px;">VIP Portfolio</h2>
    
    <div class="glass-card">
        <div class="portfolio-grid">
            <div class="portfolio-stat">
                <div class="stat-label">Value</div>
                <div class="stat-value gold-accent">${{ number_format($totalEquity, 2) }}</div>
            </div>
            <div class="portfolio-stat">
                <div class="stat-label">Buying Power</div>
                <div class="stat-value" style="color: #34d399;">${{ number_format($currentBalance, 2) }}</div>
            </div>
        </div>
        
        <h5 style="font-size: 14px; font-weight: 800; margin-bottom: 10px; color: rgba(255,255,255,0.6);">Positions</h5>
        @forelse($portfolio as $pos)
        <div class="position-item">
            <div>
                <div style="font-weight: 800; font-size: 15px;">{{ $pos->symbol }}</div>
                <div style="font-size: 11px; color: rgba(255,255,255,0.5);">{{ number_format($pos->units, 4) }} Shares</div>
            </div>
            <div class="text-right">
                <div style="font-weight: 800; color: #34d399;">${{ number_format($pos->units * $pos->buy, 2) }}</div>
                <a href="{{ route('stocks.trade-single', $pos->stock_id) }}" style="font-size: 10px; color: #FFD700; font-weight: 800; text-transform: uppercase;">Manage</a>
            </div>
        </div>
        @empty
        <p style="font-size: 12px; color: rgba(255,255,255,0.5); text-align: center;">No active positions.</p>
        @endforelse
    </div>

    <h3 style="font-size: 18px; font-weight: 800; margin-bottom: 15px; margin-top: 30px;">Market</h3>
    @foreach($vipStocks as $stock)
    <div class="stock-card">
        <div class="stock-header">
            <div class="stock-logo-wrap">
                <x-asset-logo :symbol="$stock->symbol" size="sm" />
            </div>
            <span style="position: absolute; top: 10px; right: 10px; background: rgba(255,215,0,0.2); color: #FFD700; font-size: 9px; font-weight: 800; padding: 4px 8px; border-radius: 6px;">VIP</span>
        </div>
        <div class="stock-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h5 style="font-weight: 800; font-size: 16px; margin: 0;">{{ str_replace(' (Class A)', '', $stock->name) }}</h5>
                    <div style="font-size: 11px; color: #3b82f6; font-weight: 800;">{{ $stock->symbol }}</div>
                </div>
                <div style="font-weight: 800; font-size: 18px; color: #ff3333;">${{ number_format($stock->buy, 2) }}</div>
            </div>
            
            <input type="number" class="input-invest stock-amount" placeholder="Shares Amount">
            <button class="btn-gold buy-vip-btn" data-id="{{ $stock->id }}" style="margin-top: 5px; padding: 12px;">Buy Shares</button>
        </div>
    </div>
    @endforeach

    <div class="mt-4 text-center">{{ $vipStocks->links() }}</div>

    @push('js')
    <script>
    $(document).on('click', '.buy-vip-btn', function(){
        var btn = $(this);
        var amount = btn.parent().find('.stock-amount').val();
        var stockId = btn.data('id');
        if(!amount || amount <= 0) { toastr.error('Invalid amount'); return; }
        
        btn.prop('disabled', true).html('Processing...');
        $.ajax({
            url: "{{ route('stocks.trade-post') }}",
            method: 'POST',
            data: { id: stockId, amount: amount, _token: '{{ csrf_token() }}' },
            success: function(res) {
                if(res.error) toastr.error(res.error);
                else { toastr.success(res.status || 'Success'); setTimeout(() => location.reload(), 1500); }
            },
            error: function(xhr) { toastr.error('Failed'); },
            complete: function() { btn.prop('disabled', false).html('Buy Shares'); }
        });
    });
    </script>
    @endpush
    @endif
</div>
@endsection
