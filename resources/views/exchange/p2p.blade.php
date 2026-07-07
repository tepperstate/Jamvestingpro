@extends('layouts.user.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2 class="text-white mb-0"><i class="ri-exchange-funds-line text-primary me-2"></i> P2P Trading</h2>
            <p class="text-muted">Buy and sell crypto directly with other users</p>
        </div>
        <div class="col-md-6 text-md-end">
            <button class="btn btn-primary" data-toggle="modal" data-target="#createListingModal">
                <i class="ri-add-line"></i> Create Listing
            </button>
        </div>
    </div>

    <!-- Active Listings -->
    <div class="card glass-card">
        <div class="card-header border-bottom border-dark">
            <h5 class="mb-0">Active Offers</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table text-white table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Merchant</th>
                            <th>Type</th>
                            <th>Asset</th>
                            <th>Price</th>
                            <th>Available</th>
                            <th>Limits</th>
                            <th>Payment</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($listings as $listing)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                                            {{ substr($listing->user->first_name ?? 'U', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $listing->user->first_name ?? 'Merchant' }}</div>
                                            <small class="text-muted">{{ $listing->total_trades }} orders | {{ $listing->completion_rate }}%</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($listing->type == 'buy')
                                        <span class="badge bg-success">Buy</span>
                                    @else
                                        <span class="badge bg-danger">Sell</span>
                                    @endif
                                </td>
                                <td>{{ $listing->asset }}</td>
                                <td class="fw-bold fs-5">{{ number_format($listing->price, 2) }} {{ $listing->currency }}</td>
                                <td>{{ number_format($listing->amount, 4) }} {{ $listing->asset }}</td>
                                <td>
                                    <small>{{ number_format($listing->min_order, 2) }} - {{ number_format($listing->max_order, 2) }} {{ $listing->currency }}</small>
                                </td>
                                <td>
                                    @if($listing->payment_methods)
                                        @foreach($listing->payment_methods as $pm)
                                            <span class="badge bg-dark border text-muted">{{ $pm }}</span>
                                        @endforeach
                                    @endif
                                </td>
                                <td>
                                    @if($listing->user_id !== auth()->id())
                                        <button class="btn btn-sm {{ $listing->type == 'sell' ? 'btn-success' : 'btn-danger' }}" 
                                                onclick="openOrderModal({{ $listing->id }}, '{{ $listing->type }}', '{{ $listing->asset }}', {{ $listing->price }}, {{ $listing->min_order }}, {{ $listing->max_order }})">
                                            {{ $listing->type == 'sell' ? 'Buy' : 'Sell' }} {{ $listing->asset }}
                                        </button>
                                    @else
                                        <span class="badge bg-secondary">Your Listing</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">No active listings available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    <div class="card glass-card mt-4">
        <div class="card-header border-bottom border-dark">
            <h5 class="mb-0">My Orders</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table text-white table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Role</th>
                            <th>Asset</th>
                            <th>Amount</th>
                            <th>Total Fiat</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($myOrders))
                        @forelse($myOrders as $order)
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->buyer_id == auth()->id() ? 'Buyer' : 'Seller' }}</td>
                                <td>{{ $order->listing->asset ?? 'N/A' }}</td>
                                <td>{{ number_format($order->amount, 4) }}</td>
                                <td>${{ number_format($order->total_fiat, 2) }}</td>
                                <td>
                                    <span class="badge {{ $order->status == 'completed' ? 'bg-success' : ($order->status == 'cancelled' ? 'bg-danger' : 'bg-warning') }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('user.p2p.chat', $order->id) }}" class="btn btn-sm btn-info">
                                        <i class="ri-chat-3-line"></i> Chat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">You have no active orders.</td>
                            </tr>
                        @endforelse
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Listing Modal -->
<div class="modal fade" id="createListingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title">Create P2P Listing</h5>
                <button type="button" class="btn-close btn-close-white" data-dismiss="modal"></button>
            </div>
            <form action="{{ route('user.p2p.create') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>I want to</label>
                            <select name="type" class="form-select bg-dark text-white border-secondary">
                                <option value="buy">Buy Crypto</option>
                                <option value="sell">Sell Crypto</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Asset</label>
                            <input type="text" name="asset" class="form-control bg-dark text-white border-secondary" placeholder="e.g. USDT" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Fiat Currency</label>
                            <input type="text" name="currency" class="form-control bg-dark text-white border-secondary" value="USD" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Price</label>
                            <input type="number" step="0.01" name="price" class="form-control bg-dark text-white border-secondary" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Total Amount to Trade</label>
                        <input type="number" step="0.0001" name="amount" class="form-control bg-dark text-white border-secondary" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Min Order (Fiat)</label>
                            <input type="number" step="1" name="min_order" class="form-control bg-dark text-white border-secondary" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Max Order (Fiat)</label>
                            <input type="number" step="1" name="max_order" class="form-control bg-dark text-white border-secondary" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Payment Methods (Hold Ctrl to select multiple)</label>
                        <select name="payment_methods[]" multiple class="form-select bg-dark text-white border-secondary" required>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="PayPal">PayPal</option>
                            <option value="Zelle">Zelle</option>
                            <option value="CashApp">CashApp</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Terms (Optional)</label>
                        <textarea name="terms" class="form-control bg-dark text-white border-secondary" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="submit" class="btn btn-primary w-100">Post Listing</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Place Order Modal -->
<div class="modal fade" id="placeOrderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title" id="orderModalTitle">Place Order</h5>
                <button type="button" class="btn-close btn-close-white" data-dismiss="modal"></button>
            </div>
            <form action="{{ route('user.p2p.order') }}" method="POST">
                @csrf
                <input type="hidden" name="listing_id" id="modal_listing_id">
                <div class="modal-body">
                    <div class="alert alert-info bg-dark border border-secondary text-white">
                        Price: <strong id="modal_price">0</strong> <br>
                        Limits: <span id="modal_limits">0 - 0</span>
                    </div>
                    <div class="mb-3">
                        <label>Amount to Trade (Crypto)</label>
                        <input type="number" step="0.0001" name="amount" id="modal_amount" class="form-control bg-dark text-white border-secondary" required oninput="calcFiat()">
                    </div>
                    <div class="mb-3">
                        <label>You will pay/receive (Fiat)</label>
                        <input type="text" id="modal_fiat_total" class="form-control bg-dark text-white border-secondary" readonly>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="submit" class="btn btn-success w-100" id="orderModalBtn">Confirm</button>
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
        document.getElementById('orderModalBtn').innerText = action;
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

