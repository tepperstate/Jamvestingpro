@extends('layouts.user.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-comments me-2"></i> Chat for Order #{{ $order->order_id }}
                    </h5>
                    <a href="{{ route('user.p2p') }}" class="btn btn-sm btn-outline-secondary">Back to P2P</a>
                </div>
                <div class="card-body p-0">
                    <div class="chat-container p-4" style="height: 400px; overflow-y: auto; background: #f8f9fa;">
                        @forelse($messages as $msg)
                            <div class="d-flex mb-3 {{ $msg->sender_id == auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">
                                <div class="message-box p-3 rounded" style="max-width: 75%; background: {{ $msg->sender_id == auth()->id() ? '#dcf8c6' : '#ffffff' }}; border: 1px solid #e0e0e0;">
                                    <div class="text-muted small mb-1">
                                        <strong>{{ $msg->sender->first_name ?? 'User' }}</strong> - {{ $msg->created_at->format('M d, H:i') }}
                                    </div>
                                    <div class="message-content" style="color: #333;">
                                        {{ $msg->message }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-inbox fa-3x mb-3 text-light"></i>
                                <p>No messages yet. Start the conversation!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="card-footer bg-white p-3">
                    <form action="{{ route('user.p2p.chat.send') }}" method="POST">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <div class="input-group">
                            <input type="text" name="message" class="form-control" placeholder="Type your message here..." required autocomplete="off">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-paper-plane"></i> Send
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-body">
                    <h6 class="text-muted mb-3">Order Details</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Amount:</strong> {{ $order->amount }} {{ $order->listing->asset }}</p>
                            <p class="mb-1"><strong>Price:</strong> ${{ $order->price }}</p>
                            <p class="mb-0"><strong>Total Fiat:</strong> ${{ number_format($order->total_fiat, 2) }}</p>
                        </div>
                        <div class="col-md-6 text-md-right">
                            <p class="mb-1"><strong>Status:</strong> <span class="badge badge-info">{{ ucfirst($order->status) }}</span></p>
                            <p class="mb-1"><strong>Escrow:</strong> <span class="badge badge-secondary">{{ ucfirst($order->escrow_status) }}</span></p>
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
        const chatContainer = document.querySelector('.chat-container');
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    });
</script>
@endsection
