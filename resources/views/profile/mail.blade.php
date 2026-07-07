@extends('layouts.user.app')

@section('title', 'Secure Dispatch')

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
                    <a href="{{ route('mail.home') }}" class="nav-link active py-3 px-4 mb-1">
                        <i class="ri-edit-line me-3"></i> Compose Message
                    </a>
                    <a href="{{ route('mail.inbox') }}" class="nav-link py-3 px-4 mb-1">
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
                    <h3 class="outfit font-weight-bold mb-0 text-white">New Message</h3>
                    <div class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill outfit font-weight-bold">
                        ENCRYPTED CHANNEL
                    </div>
                </div>
                
                <form action="{{ route('mail.sent') }}" method="POST">
                    @csrf
                    <div class="row g-4">
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label class="small text-secondary mb-2">To</label>
                                <input type="text" name="to" class="form-control premium-input" value="Admin" readonly style="background: rgba(255,255,255,0.02); cursor: not-allowed;">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label class="small text-secondary mb-2">Subject</label>
                                <input type="text" name="subject" class="form-control premium-input" placeholder="Enter dispatch subject..." required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label class="small text-secondary mb-2">Message</label>
                                <textarea name="message" class="form-control premium-input" rows="10" placeholder="Type your secure message here..." required style="resize: none;"></textarea>
                            </div>
                        </div>
                        <div class="col-12 mt-4 text-end">
                            <button type="submit" class="btn btn-premium px-5 py-3 outfit font-weight-bold">
                                <i class="ri-send-plane-fill me-2"></i> SEND MESSAGE
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
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
</style>

@if(session('status'))
    <script>
        $(document).ready(function() {
            toastr.success("{{ session('status') }}");
        });
    </script>
@endif

@endsection
