@extends('layouts.user.app')
@section('title', 'Rewards')
@section('content')
<style>
    .mobile-glass-container {
        padding: 15px;
        padding-bottom: 80px;
        font-family: 'Outfit', sans-serif;
    }
    .page-title {
        font-size: 24px;
        font-weight: 800;
        color: #fff;
        margin-bottom: 5px;
    }
    .page-subtitle {
        font-size: 13px;
        color: rgba(255,255,255,0.6);
        margin-bottom: 20px;
    }
    .glass-card {
        background: rgba(255,255,255,0.03);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255,215,0,0.1);
        border-radius: 16px;
        padding: 15px;
        margin-bottom: 15px;
    }
    .form-glass {
        background: rgba(0,0,0,0.4);
        border: 1px solid rgba(255,255,255,0.1);
        color: #FFD700;
        border-radius: 12px;
        padding: 15px;
        width: 100%;
        margin-bottom: 15px;
        text-align: center;
        font-weight: 800;
        font-size: 16px;
        text-transform: uppercase;
        letter-spacing: 2px;
    }
    .form-glass:focus { border-color: #FFD700; outline: none; }
    .btn-gold {
        background: linear-gradient(135deg, #FFD700 0%, #990000 100%);
        color: #000;
        border: none;
        border-radius: 12px;
        padding: 15px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        width: 100%;
        box-shadow: 0 5px 15px rgba(255,215,0,0.2);
    }
    .task-item {
        background: rgba(0,0,0,0.2);
        border: 1px solid rgba(255,255,255,0.05);
        border-radius: 12px;
        padding: 12px;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .task-badge {
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
    }
    .badge-done { background: rgba(255, 51, 51, 0.15); color: #ff3333; }
    .badge-pending { background: rgba(245, 158, 11, 0.15); color: #f59e0b; text-decoration: none; }
    .badge-action { background: rgba(59, 130, 246, 0.15); color: #3b82f6; text-decoration: none; }
    
    .history-item {
        background: rgba(255,255,255,0.02);
        border-radius: 12px;
        padding: 12px;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>

<div class="mobile-glass-container">
    <div class="text-center mb-4 pt-2">
        <div style="font-size: 40px; margin-bottom: 10px;">🎁</div>
        <h1 class="page-title">Rewards Hub</h1>
        <div class="page-subtitle">Redeem promo codes & track bonuses</div>
    </div>

    <!-- Promo Code -->
    <div class="glass-card">
        <h5 style="font-weight: 800; color: #FFD700; margin-bottom: 15px; font-size: 16px;">
            <i class="ri-coupon-3-line"></i> Promo Code
        </h5>
        <form id="redeemForm" method="POST" action="{{ route('user.coupon.redeem') }}">
            @csrf
            <input type="text" name="code" class="form-glass" placeholder="ENTER CODE" required>
            <button type="submit" class="btn-gold">
                <span class="btn-text">Redeem Code</span>
            </button>
        </form>

        @if($balance)
        <div class="mt-3 p-3" style="background: rgba(255, 51, 51, 0.1); border: 1px solid rgba(255, 51, 51, 0.2); border-radius: 12px;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div style="font-size: 11px; color: rgba(255,255,255,0.6); font-weight: 800; text-transform: uppercase;">Bonus Balance</div>
                    <div style="font-size: 24px; font-weight: 800; color: #ff3333;">${{ number_format($balance->bonus_balance ?? 0, 2) }}</div>
                </div>
                <div style="font-size: 24px;">💎</div>
            </div>
        </div>
        @endif
    </div>

    <!-- Tasks -->
    <div class="glass-card">
        <h5 style="font-weight: 800; color: #FFD700; margin-bottom: 15px; font-size: 16px;">
            <i class="ri-trophy-line"></i> Quests
        </h5>
        
        <div class="task-item">
            <div>
                <strong style="font-size: 13px;">📧 Verify Email</strong>
                <div style="font-size: 11px; color: rgba(255,255,255,0.5);">Confirm address</div>
            </div>
            <span class="task-badge badge-done">Done</span>
        </div>
        
        <div class="task-item">
            <div>
                <strong style="font-size: 13px;">🪪 Identity (KYC)</strong>
                <div style="font-size: 11px; color: rgba(255,255,255,0.5);">Submit documents</div>
            </div>
            @if($user->kyc_status == 1)
                <span class="task-badge badge-done">Done</span>
            @else
                <a href="{{ route('verification') }}" class="task-badge badge-pending">Pending</a>
            @endif
        </div>
        
        <div class="task-item">
            <div>
                <strong style="font-size: 13px;">💰 First Deposit</strong>
                <div style="font-size: 11px; color: rgba(255,255,255,0.5);">Fund account</div>
            </div>
            <a href="{{ route('deposits') }}" class="task-badge badge-action">Deposit</a>
        </div>
    </div>

    <!-- History -->
    <div class="glass-card">
        <h5 style="font-weight: 800; color: #FFD700; margin-bottom: 15px; font-size: 16px;">
            <i class="ri-history-line"></i> Injection Logs
        </h5>
        @forelse($redemptions as $r)
        <div class="history-item">
            <div>
                <div style="font-weight: 800; color: #60a5fa; font-size: 13px;">{{ $r->coupon->code ?? 'PURGED_HEX' }}</div>
                <div style="font-size: 10px; color: rgba(255,255,255,0.4);">{{ $r->created_at->format('M d | H:i') }}</div>
            </div>
            <div class="text-right">
                <div style="font-weight: 800; color: #ff3333; font-size: 14px;">+${{ number_format($r->bonus_credited, 2) }}</div>
                <div style="font-size: 9px; color: rgba(255,255,255,0.4); text-transform: uppercase;">Credited</div>
            </div>
        </div>
        @empty
        <div class="text-center py-4 opacity-50">
            <p style="font-size: 12px;">No historical injections found.</p>
        </div>
        @endforelse
    </div>
</div>

@push('js')
<script>
    $(document).ready(function() {
        $('#redeemForm').submit(function(e) {
            e.preventDefault();
            var $btn = $(this).find('button[type="submit"]');
            var $btnText = $btn.find('.btn-text');
            var originalText = $btnText.text();
            
            $btnText.text('PROCESSING...');
            $btn.prop('disabled', true);
            
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function(response) {
                    $btnText.text(originalText);
                    $btn.prop('disabled', false);
                    if(response.success) {
                        toastr.success(response.message);
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    $btnText.text(originalText);
                    $btn.prop('disabled', false);
                    let msg = 'Invalid promo code!';
                    if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    toastr.error(msg);
                }
            });
        });
    });
</script>
@endpush
@endsection
