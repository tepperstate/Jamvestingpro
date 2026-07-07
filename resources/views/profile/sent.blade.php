@extends('layouts.user.app')

@section('title', 'Sent Messages')

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
                        <i class="ri-edit-line me-3"></i> Compose Message
                    </a>
                    <a href="{{ route('mail.inbox') }}" class="nav-link py-3 px-4 mb-1">
                        <i class="ri-inbox-archive-line me-3"></i> Inbox
                    </a>
                    <a href="{{ route('mail.sent-index') }}" class="nav-link active py-3 px-4 mb-1">
                        <i class="ri-send-plane-2-line me-3"></i> Sent Messages
                    </a>
                    <a href="{{ route('notiication') }}" class="nav-link py-3 px-4 mb-1">
                        <i class="ri-notification-3-line me-3"></i> System Alerts
                    </a>
                </div>

                <div class="mt-5 pt-5 border-top border-dark">
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
                    <h3 class="outfit font-weight-bold mb-0 text-white">Sent Messages</h3>
                    <div class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill outfit font-weight-bold">
                        {{ $sent->count() }} OUTBOUND
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle">
                        <thead class="small text-secondary fw-bold uppercase">
                            <tr>
                                <th class="ps-4">Recipient</th>
                                <th>Subject</th>
                                <th>Timestamp</th>
                                <th class="text-end pe-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sent as $mail)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="btn-icon-glass-sm bg-secondary-glass text-secondary">
                                            <i class="ri-user-received-line"></i>
                                        </div>
                                        <div>
                                            <div class="text-white fw-bold">{{ $mail->to ?? 'System Admin' }}</div>
                                            <div class="text-secondary x-small">Official Request</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-white fw-600">{{ \Illuminate\Support\Str::limit($mail->subject, 50) }}</div>
                                    <div class="text-secondary small">{{ \Illuminate\Support\Str::limit($mail->message, 80) }}</div>
                                </td>
                                <td>
                                    <div class="text-white small">{{ $mail->created_at->format('M d, Y') }}</div>
                                    <div class="text-secondary x-small">{{ $mail->created_at->format('H:i') }} UTC</div>
                                </td>
                                <td class="text-end pe-4">
                                    <span class="badge bg-secondary-soft text-secondary px-3 py-2 rounded-pill small">
                                        {{ strtoupper($mail->status ?? 'TRANSMITTED') }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="opacity-20 mb-3"><i class="ri-send-plane-line" style="font-size: 4rem;"></i></div>
                                    <p class="text-secondary">No outbound transmissions recorded.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-secondary-glass { background: rgba(255, 255, 255, 0.1); }
    .bg-primary-soft { background: rgba(14, 165, 233, 0.1); }
    .bg-secondary-soft { background: rgba(255, 255, 255, 0.05); }
    
    .nav-premium-vertical .nav-link {
        color: rgba(255, 255, 255, 0.5);
        border-radius: 16px;
        transition: all 0.3s ease;
        border: 1px solid transparent;
        text-align: left;
    }
    
    .nav-premium-vertical .nav-link:hover {
        background: rgba(255, 255, 255, 0.05);
        color: white;
    }
    
    .nav-premium-vertical .nav-link.active {
        background: rgba(14, 165, 233, 0.1) !important;
        color: #0ea5e9 !important;
        border: 1px solid rgba(14, 165, 233, 0.2) !important;
    }

    .table-hover tbody tr { transition: all 0.2s ease; }
    .table-hover tbody tr:hover { background: rgba(255,255,255,0.02) !important; }
    
    .btn-icon-glass-sm {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        border: 1px solid rgba(255,255,255,0.05);
    }
    
    .fw-600 { font-weight: 600; }
    .x-small { font-size: 0.7rem; }
</style>
@endsection
