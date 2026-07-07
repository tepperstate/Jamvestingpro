@extends('layouts.user.app')

@section('content')
<style>
.mobile-p2p-exchange-container {
    padding: 15px;
    background: #0b0e14;
    min-height: 100vh;
    padding-bottom: 90px;
}
.glass-card-mobile {
    background: rgba(16, 18, 27, 0.6);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 215, 0, 0.15); /* Gold accent */
    border-radius: 16px;
    padding: 15px;
    margin-bottom: 15px;
}
.btn-gold {
    background: linear-gradient(135deg, #ffd700, #ffaa00);
    color: #000;
    border: none;
    border-radius: 10px;
    font-weight: 700;
    box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
}
.p2p-merchant-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(59, 130, 246, 0.2);
    color: #3b82f6;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    border: 1px solid rgba(59, 130, 246, 0.3);
}
.payment-badge {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #94a3b8;
    font-size: 0.65rem;
    padding: 3px 8px;
    border-radius: 6px;
    margin-right: 4px;
    margin-bottom: 4px;
    display: inline-block;
}
.btn-buy { background: rgba(255, 51, 51, 0.15); color: #ff3333; border: 1px solid rgba(255, 51, 51, 0.3); }
.btn-sell { background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }

/* Modal Styles for Mobile */
.modal-content.glass-modal {
    background: rgba(20, 24, 36, 0.95);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 215, 0, 0.2);
    border-radius: 20px;
}
.form-control-glass {
    background: rgba(0,0,0,0.3);
    border: 1px solid rgba(255,255,255,0.1);
    color: #fff;
    border-radius: 10px;
}
.form-control-glass:focus {
    background: rgba(0,0,0,0.4);
    border-color: #ffd700;
    color: #fff;
    box-shadow: none;
}
</style>

<div class="mobile-p2p-exchange-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="text-white font-weight-bold mb-0" style="font-family: 'Outfit', sans-serif;"><i class="ri-exchange-funds-line text-primary me-2"></i> P2P</h4>
            <p class="text-secondary small mb-0">Buy and sell crypto</p>
        </div>
        <button class="btn btn-gold btn-sm px-3" data-toggle="modal" data-target="#createListingModal">
            <i class="ri-add-line"></i> Post
        </button>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-pills mb-3" id="p2p-tabs" role="tablist" style="background: rgba(255,255,255,0.05); border-radius: 12px; padding: 5px;">
        <li class="nav-item" style="flex: 1; text-align: center;">
            <a class="nav-link active py-2" id="offers-tab" data-toggle="pill" href="#offers" role="tab" style="border-radius: 8px; font-weight: bold; font-size: 0.85rem;">Offers</a>
        </li>
        <li class="nav-item" style="flex: 1; text-align: center;">
            <a class="nav-link py-2" id="orders-tab" data-toggle="pill" href="#orders" role="tab" style="border-radius: 8px; font-weight: bold; font-size: 0.85rem;">My Orders</a>
        </li>
    </ul>

    <div class="tab-content" id="p2p-tabContent">
        <!-- Active Listings Tab -->
        <div class="tab-pane fade show active" id="offers" role="tabpanel">
            @forelse($listings as $listing)
                <div class="glass-card-mobile">
                    <div class="d-flex justify-content-between align-items-start mb-3 border-bottom pb-2" style="border-color: rgba(255,255,255,0.05) !important;">
                        <div class="d-flex align-items-center">
                            <div class="p2p-merchant-avatar me-2">
                                {{ substr($listing->user->first_name ?? 'U', 0, 1) }}
                            </div>
                            <div>
                                <div class="fw-bold text-white" style="font-size: 0.9rem;">{{ $listing->user->first_name ?? 'Merchant' }}</div>
                                <div class="text-muted" style="font-size: 0.65rem;">{{ $listing->total_trades }} orders | {{ $listing->completion_rate }}%</div>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge {{ $listing->type == 'buy' ? 'bg-success' : 'bg-danger' }}">{{ $listing->type == 'buy' ? 'Buy' : 'Sell' }}</span>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-5">
                            <div class="text-secondary" style="font-size: 0.7rem;">Asset</div>
                            <div class="text-white fw-bold">{{ $listing->asset }}</div>
                        </div>
                        <div class="col-7 text-end">
                            <div class="text-secondary" style="font-size: 0.7rem;">Price</div>
                            <div class="text-white fw-bold fs-6">{{ number_format($listing->price, 2) }} <span class="small">{{ $listing->currency }}</span></div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-5">
                            <div class="text-secondary" style="font-size: 0.7rem;">Available</div>
                            <div class="text-white" style="font-size: 0.8rem;">{{ number_format($listing->amount, 4) }} {{ $listing->asset }}</div>
                        </div>
                        <div class="col-7 text-end">
                            <div class="text-secondary" style="font-size: 0.7rem;">Limits</div>
                            <div class="text-white" style="font-size: 0.8rem;">{{ number_format($listing->min_order, 2) }} - {{ number_format($listing->max_order, 2) }} {{ $listing->currency }}</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        @if($listing->payment_methods)
                            @foreach($listing->payment_methods as $pm)
                                <span class="payment-badge">{{ $pm }}</span>
                            @endforeach
                        @endif
                    </div>

                    @if($listing->user_id !== auth()->id())
                        <button class="btn w-100 py-2 {{ $listing->type == 'sell' ? 'btn-buy' : 'btn-sell' }}" 
                                onclick="openOrderModal({{ $listing->id }}, '{{ $listing->type }}', '{{ $listing->asset }}', {{ $listing->price }}, {{ $listing->min_order }}, {{ $listing->max_order }})">
                            {{ $listing->type == 'sell' ? 'Buy' : 'Sell' }} {{ $listing->asset }}
                        </button>
                    @else
                        <div class="text-center w-100 py-2 rounded" style="background: rgba(255,255,255,0.05); color: #fff;">
                            Your Listing
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="ri-exchange-funds-line text-secondary" style="font-size: 3rem; opacity: 0.5;"></i>
                    <p class="text-secondary mt-2">No active listings available.</p>
                </div>
            @endforelse
        </div>

        <!-- My Orders Tab -->
        <div class="tab-pane fade" id="orders" role="tabpanel">
            @if(isset($myOrders))
                @forelse($myOrders as $order)
                    <div class="glass-card-mobile">
                        <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2" style="border-color: rgba(255,255,255,0.05) !important;">
                            <div class="text-white fw-bold">Order #{{ $order->id }}</div>
                            <span class="badge {{ $order->status == 'completed' ? 'bg-success' : ($order->status == 'cancelled' ? 'bg-danger' : 'bg-warning') }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                        
                        <div class="row mb-2">
                            <div class="col-6">
                                <div class="text-secondary" style="font-size: 0.7rem;">Role</div>
                                <div class="text-white">{{ $order->buyer_id == auth()->id() ? 'Buyer' : 'Seller' }}</div>
                            </div>
                            <div class="col-6 text-end">
                                <div class="text-secondary" style="font-size: 0.7rem;">Asset</div>
                                <div class="text-white">{{ $order->listing->asset ?? 'N/A' }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <div class="text-secondary" style="font-size: 0.7rem;">Amount</div>
                                <div class="text-white">{{ number_format($order->amount, 4) }}</div>
                            </div>
                            <div class="col-6 text-end">
                                <div class="text-secondary" style="font-size: 0.7rem;">Total Fiat</div>
                                <div class="text-primary fw-bold">${{ number_format($order->total_fiat, 2) }}</div>
                            </div>
                        </div>

                        <a href="{{ route('user.p2p.chat', $order->id) }}" class="btn w-100 py-2" style="background: rgba(14, 165, 233, 0.2); border: 1px solid rgba(14, 165, 233, 0.4); color: #0ea5e9;">
                            <i class="ri-chat-3-line"></i> Open Chat
                        </a>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="ri-shopping-cart-line text-secondary" style="font-size: 3rem; opacity: 0.5;"></i>
                        <p class="text-secondary mt-2">You have no active orders.</p>
                    </div>
                @endforelse
            @endif
        </div>
    </div>
</div>

<!-- Create Listing Modal (Mobile) -->
<div class="modal fade" id="createListingModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-modal text-white">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold" style="color: #ffd700;">Create P2P Listing</h5>
                <button type="button" class="btn-close btn-close-white" data-dismiss="modal"></button>
            </div>
            <form action="{{ route('user.p2p.create') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-6 mb-2">
                            <label class="small text-secondary mb-1">I want to</label>
                            <select name="type" class="form-select form-control-glass">
                                <option value="buy">Buy Crypto</option>
                                <option value="sell">Sell Crypto</option>
                            </select>
                        </div>
                        <div class="col-6 mb-2">
                            <label class="small text-secondary mb-1">Asset</label>
                            <input type="text" name="asset" class="form-control form-control-glass" placeholder="USDT" required>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-6 mb-2">
                            <label class="small text-secondary mb-1">Fiat Currency</label>
                            <input type="text" name="currency" class="form-control form-control-glass" value="USD" required>
                        </div>
                        <div class="col-6 mb-2">
                            <label class="small text-secondary mb-1">Price</label>
                            <input type="number" step="0.01" name="price" class="form-control form-control-glass" required>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="small text-secondary mb-1">Total Amount to Trade</label>
                        <input type="number" step="0.0001" name="amount" class="form-control form-control-glass" required>
                    </div>
                    <div class="row g-2">
                        <div class="col-6 mb-2">
                            <label class="small text-secondary mb-1">Min Order (Fiat)</label>
                            <input type="number" step="1" name="min_order" class="form-control form-control-glass" required>
                        </div>
                        <div class="col-6 mb-2">
                            <label class="small text-secondary mb-1">Max Order (Fiat)</label>
                            <input type="number" step="1" name="max_order" class="form-control form-control-glass" required>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="small text-secondary mb-1">Payment Methods</label>
                        <select name="payment_methods[]" multiple class="form-select form-control-glass" style="height: 80px;" required>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="PayPal">PayPal</option>
                            <option value="Zelle">Zelle</option>
                            <option value="CashApp">CashApp</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="small text-secondary mb-1">Terms (Optional)</label>
                        <textarea name="terms" class="form-control form-control-glass" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-gold w-100 py-2">Post Listing</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Place Order Modal (Mobile) -->
<div class="modal fade" id="placeOrderModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-modal text-white">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold" id="orderModalTitle" style="color: #ffd700;">Place Order</h5>
                <button type="button" class="btn-close btn-close-white" data-dismiss="modal"></button>
            </div>
            <form action="{{ route('user.p2p.order') }}" method="POST">
                @csrf
                <input type="hidden" name="listing_id" id="modal_listing_id">
                <div class="modal-body">
                    <div class="p-3 mb-3" style="background: rgba(255,255,255,0.05); border-radius: 10px;">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-secondary small">Price:</span>
                            <strong class="text-white" id="modal_price">0</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-secondary small">Limits:</span>
                            <span class="text-white small" id="modal_limits">0 - 0</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="small text-secondary mb-1">Amount to Trade (Crypto)</label>
                        <input type="number" step="0.0001" name="amount" id="modal_amount" class="form-control form-control-glass py-2" required oninput="calcFiat()">
                    </div>
                    <div class="mb-2">
                        <label class="small text-secondary mb-1">You will pay/receive (Fiat)</label>
                        <div class="input-group">
                            <span class="input-group-text border-0" style="background: rgba(0,0,0,0.3); color: #fff;">$</span>
                            <input type="text" id="modal_fiat_total" class="form-control form-control-glass border-start-0" readonly>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-gold w-100 py-2" id="orderModalBtn">Confirm Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentPrice = 0;
    function openOrderModal(id, type, asset, price, min, max) {
        document.getElementById('modal_listing_id').value = id;
        currentPrice = price;
        document.getElementById('modal_price').innerText = price;
        document.getElementById('modal_limits').innerText = min + " - " + max;
        
        let action = type === 'sell' ? 'Buy' : 'Sell';
        document.getElementById('orderModalTitle').innerText = action + ' ' + asset;
        document.getElementById('orderModalBtn').innerText = action + ' Now';
        $('#placeOrderModal').modal('show');
    }
    
    function calcFiat() {
        let amt = document.getElementById('modal_amount').value;
        if(amt && currentPrice) {
            document.getElementById('modal_fiat_total').value = (amt * currentPrice).toFixed(2);
        } else {
            document.getElementById('modal_fiat_total').value = '0.00';
        }
    }
</script>
@endsection
