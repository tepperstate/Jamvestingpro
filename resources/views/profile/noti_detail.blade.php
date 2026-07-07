@extends('layouts.user.app')

@section('title', 'Alert Detail')

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
                    <a href="{{ route('mail.inbox') }}" class="nav-link py-3 px-4 mb-1">
                        <i class="ri-inbox-archive-line me-3"></i> Intelligence Inbox
                    </a>
                    <a href="{{ route('mail.sent-index') }}" class="nav-link py-3 px-4 mb-1">
                        <i class="ri-send-plane-2-line me-3"></i> Sent Transmissions
                    </a>
                    <a href="{{ route('notiication') }}" class="nav-link active py-3 px-4 mb-1">
                        <i class="ri-notification-3-line me-3"></i> System Alerts
                    </a>
                </div>

                <div class="mt-5 pt-5 border-top border-dark">
                    <a href="{{ route('notiication') }}" class="btn btn-outline-premium btn-sm w-100 py-3 rounded-pill">
                        <i class="ri-arrow-left-line me-1"></i> BACK TO ALERTS
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-xl-9">
            <div class="glass-card p-5 h-100 shadow-lg" style="background: rgba(16, 18, 27, 0.4); backdrop-filter: blur(20px); border-radius: 32px; border: 1px solid rgba(255,255,255,0.05);">
                <div class="d-flex justify-content-between align-items-center mb-5 pb-4 border-bottom border-dark">
                    <div>
                        <h3 class="outfit font-weight-bold mb-1 text-white">{{ $data->title }}</h3>
                        <div class="text-secondary small">System Alert Log #{{ $data->id }}</div>
                    </div>
                    <div class="text-end">
                        <div class="badge bg-warning-soft text-warning px-3 py-2 rounded-pill outfit font-weight-bold mb-2">
                            URGENT NOTIFICATION
                        </div>
                        <div class="text-secondary x-small">{{ $data->created_at->format('M d, Y @ H:i') }} UTC</div>
                    </div>
                </div>

                <div class="alert-status-card mb-5 d-flex align-items-center gap-4 p-4 rounded-4" style="background: rgba(245, 158, 11, 0.05); border: 1px solid rgba(245, 158, 11, 0.1);">
                    <div class="btn-icon-glass bg-warning-glass text-warning" style="width: 50px; height: 50px; font-size: 1.5rem;">
                        <i class="ri-notification-badge-fill"></i>
                    </div>
                    <div>
                        <div class="text-secondary x-small uppercase fw-bold mb-1">Status</div>
                        <div class="text-white fw-bold outfit h5 mb-0">Acknowledgement Recorded</div>
                    </div>
                </div>

                <div class="message-content text-white-50 lh-lg p-4 rounded-4" style="background: rgba(0,0,0,0.2); min-height: 200px; font-size: 1.05rem;">
                    {!! nl2br(e($data->message)) !!}
                </div>

                <div class="mt-5 pt-4 text-end">
                    <a href="{{ route('notiication') }}" class="btn btn-premium px-5 py-2">
                        <i class="ri-check-double-line me-2"></i> ACKNOWLEDGE & CLOSE
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-warning-glass { background: rgba(245, 158, 11, 0.1); }
    .bg-warning-soft { background: rgba(245, 158, 11, 0.1); }
    
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
