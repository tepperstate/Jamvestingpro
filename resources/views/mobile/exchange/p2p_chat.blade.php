@extends('layouts.user.app')
@section('content')

<style>
.mobile-chat-container {
    background: #0b0e14;
    height: 100vh;
    display: flex;
    flex-direction: column;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1050; /* Above navs if necessary */
}
.chat-header {
    background: rgba(16, 18, 27, 0.95);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(255,215,0,0.15);
    padding: 15px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    z-index: 10;
}
.chat-body {
    flex: 1;
    overflow-y: auto;
    padding: 15px;
    background: #0b0e14;
}
.chat-footer {
    background: rgba(16, 18, 27, 0.95);
    backdrop-filter: blur(10px);
    border-top: 1px solid rgba(255,215,0,0.15);
    padding: 15px;
}
.message-bubble {
    max-width: 80%;
    padding: 12px 15px;
    border-radius: 18px;
    margin-bottom: 10px;
    font-size: 0.9rem;
    position: relative;
    word-wrap: break-word;
}
.msg-sent {
    background: rgba(255, 215, 0, 0.15);
    border: 1px solid rgba(255, 215, 0, 0.3);
    color: #fff;
    border-bottom-right-radius: 4px;
    margin-left: auto;
}
.msg-received {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #fff;
    border-bottom-left-radius: 4px;
    margin-right: auto;
}
.msg-time {
    font-size: 0.65rem;
    color: rgba(255,255,255,0.5);
    margin-top: 4px;
    display: block;
}
.msg-sent .msg-time { text-align: right; }
.msg-received .msg-time { text-align: left; }

.chat-input-wrapper {
    display: flex;
    gap: 10px;
}
.chat-input {
    flex: 1;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,215,0,0.2);
    color: #fff;
    border-radius: 20px;
    padding: 10px 15px;
}
.chat-input:focus {
    background: rgba(255,255,255,0.08);
    border-color: #ffd700;
    color: #fff;
    outline: none;
}
.btn-send {
    background: linear-gradient(135deg, #ffd700, #ffaa00);
    color: #000;
    border: none;
    border-radius: 50%;
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.order-info-banner {
    background: rgba(14, 165, 233, 0.1);
    border: 1px solid rgba(14, 165, 233, 0.3);
    border-radius: 12px;
    padding: 10px;
    margin-bottom: 20px;
}
</style>

<div class="mobile-chat-container">
    <div class="chat-header">
        <a href="{{ route('user.p2p') }}" class="text-white text-decoration-none" style="font-size: 1.5rem;">
            <i class="ri-arrow-left-s-line"></i>
        </a>
        <div class="text-center">
            <h6 class="mb-0 text-white font-weight-bold" style="font-family: 'Outfit', sans-serif;">Order #{{ $order->order_id }}</h6>
            <span class="badge {{ $order->status == 'completed' ? 'bg-success' : 'bg-warning' }}" style="font-size: 0.6rem;">{{ ucfirst($order->status) }}</span>
        </div>
        <div style="width: 24px;"></div> <!-- Spacer for center alignment -->
    </div>

    <div class="chat-body" id="chat-body">
        <div class="order-info-banner text-center mb-4">
            <div class="small text-info mb-1">Trading <strong>{{ $order->amount }} {{ $order->listing->asset ?? 'Asset' }}</strong></div>
            <div class="text-white fw-bold">Total: ${{ number_format($order->total_fiat, 2) }}</div>
        </div>

        @forelse($messages as $msg)
            <div class="message-bubble {{ $msg->sender_id == auth()->id() ? 'msg-sent' : 'msg-received' }}">
                @if($msg->sender_id != auth()->id())
                    <div class="small fw-bold mb-1" style="color: #ffd700; font-size: 0.7rem;">{{ $msg->sender->first_name ?? 'User' }}</div>
                @endif
                {{ $msg->message }}
                <span class="msg-time">{{ $msg->created_at->format('H:i') }}</span>
            </div>
        @empty
            <div class="text-center py-5">
                <i class="ri-chat-smile-3-line text-secondary" style="font-size: 3rem; opacity: 0.5;"></i>
                <p class="text-secondary small mt-3">Start the conversation with the trader.</p>
            </div>
        @endforelse
    </div>

    <div class="chat-footer">
        <form action="{{ route('user.p2p.chat.send') }}" method="POST">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order->id }}">
            <div class="chat-input-wrapper">
                <input type="text" name="message" class="chat-input" placeholder="Type a message..." required autocomplete="off">
                <button type="submit" class="btn-send shadow-sm">
                    <i class="ri-send-plane-fill fs-5"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatBody = document.getElementById('chat-body');
        if (chatBody) {
            chatBody.scrollTop = chatBody.scrollHeight;
        }
        
        // Hide standard mobile bottom navigation if present (assuming layout wrapper)
        const bottomNav = document.querySelector('.mobile-bottom-nav');
        if(bottomNav) bottomNav.style.display = 'none';
    });
</script>
@endsection
