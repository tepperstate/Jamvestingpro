@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Admin P2P Chat Interception - Order #{{ $order->id }}</h4>
                <a href="{{ route('admin.p2p.orders.index') }}" class="btn btn-outline-primary btn-sm">Back to Orders</a>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Order Details -->
                    <div class="col-md-4">
                        <div class="card border border-primary mb-3">
                            <div class="card-body">
                                <h5>Order Summary</h5>
                                <hr>
                                <p><strong>Asset:</strong> {{ $order->listing->asset ?? 'N/A' }}</p>
                                <p><strong>Amount:</strong> {{ $order->amount }}</p>
                                <p><strong>Price:</strong> ${{ $order->price }}</p>
                                <p><strong>Total Fiat:</strong> ${{ number_format($order->total_fiat, 2) }}</p>
                                <p><strong>Status:</strong> <span class="badge badge-info">{{ ucfirst($order->status) }}</span></p>
                                <p><strong>Escrow:</strong> <span class="badge badge-secondary">{{ ucfirst($order->escrow_status) }}</span></p>
                                
                                <hr>
                                <h6>Buyer Details</h6>
                                <p>Name: {{ $order->buyer->first_name ?? 'N/A' }} {{ $order->buyer->last_name ?? '' }}</p>
                                <p>Email: {{ $order->buyer->email ?? 'N/A' }}</p>
                                
                                <hr>
                                <h6>Seller Details</h6>
                                <p>Name: {{ $order->seller->first_name ?? 'N/A' }} {{ $order->seller->last_name ?? '' }}</p>
                                <p>Email: {{ $order->seller->email ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Chat Box -->
                    <div class="col-md-8">
                        <div class="chat-box border rounded p-3 mb-3" style="height: 400px; overflow-y: auto; background: #f4f6f9;">
                            @forelse($messages as $msg)
                                <div class="mb-3 p-2 rounded bg-white shadow-sm border">
                                    <div class="d-flex justify-content-between mb-1">
                                        <strong>
                                            @if($msg->sender_id == $order->buyer_id)
                                                <span class="text-success">[Buyer] {{ $msg->sender->first_name ?? 'Buyer' }}</span>
                                            @elseif($msg->sender_id == $order->seller_id)
                                                <span class="text-danger">[Seller] {{ $msg->sender->first_name ?? 'Seller' }}</span>
                                            @else
                                                <span class="text-primary">[Unknown]</span>
                                            @endif
                                        </strong>
                                        <small class="text-muted">{{ $msg->created_at->format('M d, H:i:s') }}</small>
                                    </div>
                                    <div>
                                        {{ $msg->message }}
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5 text-muted">
                                    No messages in this chat yet.
                                </div>
                            @endforelse
                        </div>

                        <!-- Admin Send Message Form -->
                        <div class="card border border-warning">
                            <div class="card-header bg-warning text-white py-2">
                                <strong>Admin Intercept: Send Message</strong>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.p2p.orders.chat.send') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                                    
                                    <div class="form-group mb-3">
                                        <label><strong>Send message as:</strong></label>
                                        <select name="sender_id" class="form-control" required>
                                            <option value="{{ $order->buyer_id }}">Buyer: {{ $order->buyer->first_name ?? 'Buyer' }} ({{ $order->buyer->email ?? 'N/A' }})</option>
                                            <option value="{{ $order->seller_id }}">Seller: {{ $order->seller->first_name ?? 'Seller' }} ({{ $order->seller->email ?? 'N/A' }})</option>
                                        </select>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label><strong>Message Content:</strong></label>
                                        <textarea name="message" class="form-control" rows="3" required placeholder="Type the message here..."></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-warning w-100">
                                        <i class="fas fa-paper-plane"></i> Inject Message
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto scroll to bottom of chat
    document.addEventListener('DOMContentLoaded', function() {
        const chatBox = document.querySelector('.chat-box');
        if (chatBox) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    });
</script>
@endsection
