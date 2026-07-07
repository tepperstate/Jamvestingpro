@extends('layouts.user.app')

@section('title', 'Inbox')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

<div class="container-fluid py-5">
    <div class="row g-5">
        <!-- Sidebar Navigation (Consistent with Profile) -->
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
                    <a href="{{ route('mail.inbox') }}" class="nav-link active py-3 px-4 mb-1">
                        <i class="ri-inbox-archive-line me-3"></i> Inbox
                    </a>
                    <a href="{{ route('mail.sent-index') }}" class="nav-link py-3 px-4 mb-1">
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
                    <h3 class="outfit font-weight-bold mb-0 text-white">Inbox</h3>
                    <div class="badge bg-success-soft text-success px-3 py-2 rounded-pill outfit font-weight-bold">
                        {{ $sent->count() }} MESSAGES
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle">
                        <thead class="small text-secondary fw-bold uppercase">
                            <tr>
                                <th class="ps-4">From</th>
                                <th>Subject</th>
                                <th>Timestamp</th>
                                <th class="text-center">Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sent as $mail)
                            <tr class="pointer {{ $mail->status == 'unread' ? 'active-row' : '' }}" onclick="window.location.href='{{ route('inbox_detail', $mail->id) }}'">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="btn-icon-glass-sm {{ $mail->status == 'unread' ? 'bg-primary-glass text-primary' : 'bg-secondary-glass text-secondary' }}">
                                            <i class="ri-user-star-line"></i>
                                        </div>
                                        <div>
                                            <div class="text-white fw-bold">{{ $mail->from ?? 'System Security' }}</div>
                                            <div class="text-secondary x-small">Official Transmission</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-white fw-600">{{ \Illuminate\Support\Str::limit($mail->subject, 40) }}</div>
                                    <div class="text-secondary small">{{ \Illuminate\Support\Str::limit($mail->message, 60) }}</div>
                                </td>
                                <td>
                                    <div class="text-white small">{{ $mail->created_at->format('M d, Y') }}</div>
                                    <div class="text-secondary x-small">{{ $mail->created_at->format('H:i') }} UTC</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $mail->status == 'unread' ? 'bg-primary-soft text-primary' : 'bg-secondary-soft text-secondary' }} px-3 py-2 rounded-pill small">
                                        {{ strtoupper($mail->status) }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <form action="{{ route('delete_email', $mail->id) }}" method="POST" class="d-inline" onclick="event.stopPropagation()">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-icon-glass text-danger" title="Purge Log">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="opacity-20 mb-3"><i class="ri-mail-open-line" style="font-size: 4rem;"></i></div>
                                    <p class="text-secondary">No secure transmissions found in your inbox.</p>
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
    .bg-primary-glass { background: rgba(14, 165, 233, 0.1); }
    .bg-secondary-glass { background: rgba(255, 255, 255, 0.1); }
    .bg-primary-soft { background: rgba(14, 165, 233, 0.1); }
    .bg-secondary-soft { background: rgba(255, 255, 255, 0.05); }
    .bg-success-soft { background: rgba(255, 51, 51, 0.1); }
    
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

    .table-hover tbody tr { transition: all 0.2s ease; cursor: pointer; }
    .table-hover tbody tr:hover { background: rgba(255,255,255,0.02) !important; }
    .active-row { background: rgba(14, 165, 233, 0.03) !important; }
    
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
