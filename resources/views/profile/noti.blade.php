@extends('layouts.user.app')

@section('title', 'System Alerts')

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

                <div class="mt-5 pt-5 border-top border-dark text-center">
                    <a href="{{ route('profile') }}" class="btn btn-outline-premium btn-sm w-100 py-3 rounded-pill">
                        <i class="ri-arrow-left-line me-1"></i> BACK TO PROFILE
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-xl-9">
            <div class="glass-card p-5 h-100 shadow-lg" style="background: rgba(16, 18, 27, 0.4); backdrop-filter: blur(20px); border-radius: 32px; border: 1px solid rgba(255,255,255,0.05);">
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <h3 class="outfit font-weight-bold mb-0 text-white">Neural System Alerts</h3>
                    <div class="badge bg-warning-soft text-warning px-3 py-2 rounded-pill outfit font-weight-bold">
                        {{ $data->count() }} UNREAD
                    </div>
                </div>

                <div class="alert-feed">
                    @forelse($data as $noti)
                    <div class="glass-card mb-4 p-4 pointer hover-lift transition" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 20px;" onclick="window.location.href='{{ route('noti_detail', $noti->id) }}'">
                        <div class="d-flex align-items-start gap-4">
                            <div class="btn-icon-glass bg-warning-glass text-warning" style="width: 48px; height: 48px; font-size: 1.4rem; flex-shrink: 0;">
                                <i class="ri-notification-badge-line"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="outfit font-weight-bold text-white mb-0">{{ $noti->title }}</h5>
                                    <span class="text-secondary small">{{ $noti->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-secondary small mb-0 lh-base">{{ \Illuminate\Support\Str::limit($noti->message, 150) }}</p>
                            </div>
                            <div class="ms-3">
                                <i class="ri-arrow-right-s-line text-secondary h4"></i>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <div class="opacity-20 mb-3"><i class="ri-notification-off-line" style="font-size: 4rem;"></i></div>
                        <p class="text-secondary">No new system alerts detected. All systems nominal.</p>
                    </div>
                    @endforelse
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

    .hover-lift:hover {
        transform: translateY(-3px);
        border-color: rgba(245, 158, 11, 0.2) !important;
        background: rgba(245, 158, 11, 0.03) !important;
    }
    .transition { transition: all 0.2s ease; }
</style>
@endsection
