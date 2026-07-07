@extends('layouts.user.app')
@section('title', 'Rewards')
@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="glass-card p-4 text-center" style="background: linear-gradient(135deg, rgba(16,185,129,0.1), rgba(59,130,246,0.08));">
                <h4 class="font-weight-bold mb-2" style="color:var(--text-primary)">🎁 Rewards</h4>
                <p class="text-muted mb-0">Redeem promo codes and earn bonuses on your account</p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Coupon Redemption -->
        <div class="col-lg-5 mb-4">
            <div class="glass-card p-4">
                <h5 class="font-weight-bold mb-3" style="color:var(--accent-primary)">
                    <i class="ri-coupon-3-line"></i> Redeem Promo Code
                </h5>
                <form id="redeemForm" method="POST" action="{{ route('user.coupon.redeem') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" style="color:var(--text-secondary); font-size:0.85rem">Enter your promo code</label>
                        <input type="text" name="code" class="form-control" placeholder="e.g. WELCOME50" required
                               style="background:rgba(255,255,255,0.04); border:1px solid var(--glass-border); border-radius:12px; padding:14px 16px; color:var(--text-primary); font-size:1.1rem; text-transform:uppercase; letter-spacing:2px; text-align:center; font-weight:700;">
                    </div>
                    <button type="submit" class="btn btn-block" style="background:linear-gradient(135deg, #ff3333, #059669); color:white; border:none; border-radius:12px; padding:14px; font-weight:700; font-size:1rem; text-transform:uppercase; letter-spacing:1px;">
                        <i class="ri-gift-line"></i> <span class="btn-text">Redeem Code</span>
                    </button>
                </form>

                @if($balance)
                <div class="mt-4 p-3" style="background:rgba(16,185,129,0.06); border:1px solid rgba(16,185,129,0.15); border-radius:12px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small style="color:var(--text-secondary)">Trading Bonus Balance</small>
                            <h4 class="mb-0 font-weight-bold" style="color:#ff3333">${{ number_format($balance->bonus_balance ?? 0, 2) }}</h4>
                        </div>
                        <div style="font-size:2rem">💎</div>
                    </div>
                    <small style="color:var(--text-muted); font-size:0.75rem">Bonus funds are for trading only and cannot be withdrawn directly.</small>
                </div>
                @endif
            </div>

            <!-- Reward Tasks -->
            <div class="glass-card p-4 mt-4">
                <h5 class="font-weight-bold mb-3" style="color:var(--accent-primary)">
                    <i class="ri-trophy-line"></i> Available Rewards
                </h5>
                <div class="p-3 mb-3" style="background:rgba(59,130,246,0.06); border:1px solid rgba(59,130,246,0.12); border-radius:12px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong style="color:var(--text-primary)">📧 Verify Email</strong>
                            <p class="mb-0" style="font-size:0.8rem; color:var(--text-secondary)">Confirm your email address</p>
                        </div>
                        <span class="badge" style="background:rgba(16,185,129,0.15); color:#ff3333; padding:6px 12px; border-radius:8px; font-size:11px">✅ Done</span>
                    </div>
                </div>
                <div class="p-3 mb-3" style="background:rgba(59,130,246,0.06); border:1px solid rgba(59,130,246,0.12); border-radius:12px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong style="color:var(--text-primary)">🪪 Complete KYC</strong>
                            <p class="mb-0" style="font-size:0.8rem; color:var(--text-secondary)">Submit your identity documents</p>
                        </div>
                        @if($user->kyc_status == 1)
                        <span class="badge" style="background:rgba(16,185,129,0.15); color:#ff3333; padding:6px 12px; border-radius:8px; font-size:11px">✅ Done</span>
                        @else
                        <a href="{{ route('verification') }}" class="badge" style="background:rgba(245,158,11,0.15); color:#f59e0b; padding:6px 12px; border-radius:8px; font-size:11px; text-decoration:none">⏳ Pending</a>
                        @endif
                    </div>
                </div>
                <div class="p-3 mb-3" style="background:rgba(59,130,246,0.06); border:1px solid rgba(59,130,246,0.12); border-radius:12px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong style="color:var(--text-primary)">💰 First Deposit</strong>
                            <p class="mb-0" style="font-size:0.8rem; color:var(--text-secondary)">Fund your account to unlock trading</p>
                        </div>
                        <a href="{{ route('deposits') }}" class="badge" style="background:rgba(59,130,246,0.15); color:#3b82f6; padding:6px 12px; border-radius:8px; font-size:11px; text-decoration:none">Deposit →</a>
                    </div>
                </div>
                <div class="p-3" style="background:rgba(139,92,246,0.06); border:1px solid rgba(139,92,246,0.12); border-radius:12px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong style="color:var(--text-primary)">👥 Refer a Friend</strong>
                            <p class="mb-0" style="font-size:0.8rem; color:var(--text-secondary)">Earn bonus when friends sign up</p>
                        </div>
                        <span class="badge" style="background:rgba(139,92,246,0.15); color:#8b5cf6; padding:6px 12px; border-radius:8px; font-size:11px">Coming Soon</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Redemption History -->
        <div class="col-lg-7 mb-4">
            <div class="glass-card p-4 shadow-2xl">
                <h5 class="font-weight-bold mb-3 text-white">
                    <i class="ri-history-line text-primary mr-2"></i> Injection Logs
                </h5>
                @forelse($redemptions as $r)
                <div class="d-flex justify-content-between align-items-center p-3 mb-2 satin-border" style="background:rgba(255,255,255,0.02); border-radius:12px;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box glass-panel p-2" style="background: rgba(59, 130, 246, 0.1); border-radius: 10px;">
                            <i class="ri-ticket-2-line text-primary"></i>
                        </div>
                        <div>
                            <code style="color:#60a5fa; font-size:14px; font-weight:700">{{ $r->coupon->code ?? 'PURGED_HEX' }}</code>
                            <p class="mb-0 x-small text-muted">{{ $r->created_at->format('M d, Y | H:i') }} UTC</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-success font-weight-bold h6 mb-0">+${{ number_format($r->bonus_credited, 2) }}</span>
                        <div class="x-small text-muted text-uppercase tracking-wider">Bonus Credited</div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5 opacity-50">
                    <div style="font-size:3rem; margin-bottom:1rem">🎫</div>
                    <p class="text-muted">No historical injections found in your ledger.</p>
                </div>
                @endforelse
            </div>
        </div>
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
            
            $btnText.text('Processing...');
            $btn.prop('disabled', true);
            
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    $btnText.text(originalText);
                    $btn.prop('disabled', false);
                    
                    if(response.success) {
                        if (typeof toastr !== 'undefined') toastr.success(response.message, "Success");
                        else alert(response.message);
                        
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        if (typeof toastr !== 'undefined') toastr.error(response.message, "Error");
                        else alert(response.message);
                    }
                },
                error: function(xhr) {
                    $btnText.text(originalText);
                    $btn.prop('disabled', false);
                    
                    let msg = 'Invalid promo code! Please enter a valid code.';
                    if(xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    
                    if (typeof toastr !== 'undefined') toastr.error(msg, "Error");
                    else alert(msg);
                }
            });
        });
    });
</script>
@endpush
@endsection
