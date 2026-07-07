@extends('layouts.user.app')

@section('title', 'Transmission Detail')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

<div class="container-fluid py-5">
    <div class="row g-5">
        <!-- Sidebar Navigation -->
        <div class="col-xl-3">
            <div class="glass-card p-5 h-100 shadow-lg" style="background: rgba(16, 18, 27, 0.4); backdrop-filter: blur(20px); border-radius: 32px; border: 1px solid rgba(255,255,255,0.05);">
                <div class="text-center mb-5">
                    <div class="avatar-box mx-auto mb-4" style="width: 80px; height: 80px; background: rgba(59, 130, 246, 0.1); border-radius: 24px; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(59, 130, 246, 0.2);">
                        <i class="ri-mail-send-line text-primary" style="font-size: 2.5rem;"></i>
                    </div>
                    <h4 class="outfit font-weight-bold mb-1 text-white">Communications</h4>
                    <p class="text-secondary small mb-0 opacity-75">Secure Message Center</p>
                </div>

                <div class="nav flex-column nav-pills nav-premium-vertical gap-2">
                    <a href="{{ route('mail.home') }}" class="nav-link py-3 px-4 mb-1">
                        <i class="ri-edit-line me-3"></i> Compose Dispatch
                    </a>
                    <a href="{{ route('mail.inbox') }}" class="nav-link active py-3 px-4 mb-1">
                        <i class="ri-inbox-archive-line me-3"></i> Intelligence Inbox
                    </a>
                    <a href="{{ route('mail.sent-index') }}" class="nav-link py-3 px-4 mb-1">
                        <i class="ri-send-plane-2-line me-3"></i> Sent Transmissions
                    </a>
                    <a href="{{ route('notiication') }}" class="nav-link py-3 px-4 mb-1">
                        <i class="ri-notification-3-line me-3"></i> System Alerts
                    </a>
                </div>

                <div class="mt-5 pt-5 border-top border-dark">
                    <a href="{{ route('mail.inbox') }}" class="btn btn-outline-premium btn-sm w-100 py-3 rounded-pill">
                        <i class="ri-arrow-left-line me-1"></i> BACK TO INBOX
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-xl-9">
            <div class="glass-card p-5 h-100 shadow-lg" style="background: rgba(16, 18, 27, 0.4); backdrop-filter: blur(20px); border-radius: 32px; border: 1px solid rgba(255,255,255,0.05);">
                <div class="d-flex justify-content-between align-items-center mb-5 pb-4 border-bottom border-dark">
                    <div>
                        <h3 class="outfit font-weight-bold mb-1 text-white">{{ $data->subject }}</h3>
                        <div class="text-secondary small">Dispatch ID: #SEC-{{ str_pad($data->id, 5, '0', STR_PAD_LEFT) }}</div>
                    </div>
                    <div class="text-end">
                        <div class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill outfit font-weight-bold mb-2">
                            DECRYPTED MESSAGE
                        </div>
                        <div class="text-secondary x-small">{{ $data->created_at->format('M d, Y @ H:i') }} UTC</div>
                    </div>
                </div>

                <div class="thread-container mb-5">
                    @foreach($thread as $msg)
                        <!-- If message is FROM admin (sent_to = inbox), show it aligned left. If FROM user (sent_to = sent), show aligned right -->
                        @if($msg->sent_to == 'inbox')
                            <div class="message-bubble admin-msg mb-4 p-4 rounded-4" style="background: rgba(14, 165, 233, 0.05); border: 1px solid rgba(14, 165, 233, 0.1); width: 85%;">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="btn-icon-glass bg-primary-glass text-primary" style="width: 40px; height: 40px; font-size: 1.2rem;">
                                        <i class="ri-user-star-fill"></i>
                                    </div>
                                    <div>
                                        <div class="text-white fw-bold outfit">{{ $msg->from ?? 'Admin' }}</div>
                                        <div class="text-secondary x-small">{{ $msg->created_at->format('M d - H:i UTC') }}</div>
                                    </div>
                                </div>
                                <div class="message-content text-white-50 lh-lg" style="font-size: 1.05rem;">
                                    {!! nl2br(e($msg->message)) !!}
                                </div>
                            </div>
                        @else
                            <div class="message-bubble user-msg mb-4 p-4 rounded-4 ms-auto" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); width: 85%;">
                                <div class="d-flex align-items-center gap-3 mb-3 justify-content-end">
                                    <div class="text-end">
                                        <div class="text-white fw-bold outfit">You</div>
                                        <div class="text-secondary x-small">{{ $msg->created_at->format('M d - H:i UTC') }}</div>
                                    </div>
                                    <div class="btn-icon-glass bg-secondary-glass text-secondary" style="width: 40px; height: 40px; font-size: 1.2rem;">
                                        <i class="ri-user-smile-line"></i>
                                    </div>
                                </div>
                                <div class="message-content text-white lh-lg text-end" style="font-size: 1.05rem;">
                                    {!! nl2br(e($msg->message)) !!}
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="reply-box mt-5 pt-4 border-top border-dark">
                    <form action="{{ route('store_email') }}" method="POST">
                        @csrf
                        <input type="hidden" name="to" value="Admin">
                        <input type="hidden" name="subject" value="Re: {{ $data->subject }}">
                        <input type="hidden" name="thread_id" value="{{ $data->thread_id ?? $data->id }}">
                        <input type="hidden" name="reply_to_id" value="{{ $data->id }}">
                        
                        <div class="form-group mb-3">
                            <label class="small text-secondary mb-2 uppercase fw-bold">Quick Reply</label>
                            <textarea name="message" class="form-control premium-input p-3" rows="4" placeholder="Type your response here..." required style="background: rgba(0,0,0,0.2); color:white; border: 1px solid rgba(255,255,255,0.05); border-radius: 12px;"></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <button type="submit" class="btn btn-premium px-5 py-2 outfit font-weight-bold ms-auto">
                                <i class="ri-send-plane-fill me-2"></i> SEND REPLY
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-primary-glass { background: rgba(14, 165, 233, 0.1); }
    .bg-primary-soft { background: rgba(14, 165, 233, 0.1); }
    
    .nav-premium-vertical .nav-link {
        color: rgba(255, 255, 255, 0.5);
        border-radius: 16px;
        transition: all 0.3s ease;
        border: 1px solid transparent;
        text-align: left;
    }
    
    .nav-premium-vertical .nav-link.active {
        background: rgba(14, 165, 233, 0.1) !important;
        color: #0ea5e9 !important;
        border: 1px solid rgba(14, 165, 233, 0.2) !important;
    }
    
    .x-small { font-size: 0.7rem; }
    .uppercase { text-transform: uppercase; letter-spacing: 1px; }
</style>
@endsection
