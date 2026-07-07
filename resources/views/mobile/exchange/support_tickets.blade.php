@extends('layouts.user.app')
@section('title', 'Help & Support')
@section('content')
<style>
    .mobile-glass-container {
        padding: 15px;
        padding-bottom: 80px;
        font-family: 'Outfit', sans-serif;
    }
    .page-title {
        font-size: 24px;
        font-weight: 800;
        color: #fff;
        margin-bottom: 5px;
    }
    .page-subtitle {
        font-size: 13px;
        color: rgba(255,255,255,0.6);
        margin-bottom: 20px;
    }
    .btn-gold {
        background: linear-gradient(135deg, #FFD700 0%, #990000 100%);
        color: #000;
        border: none;
        border-radius: 12px;
        padding: 12px 20px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        box-shadow: 0 5px 15px rgba(255,215,0,0.2);
    }
    .ticket-card {
        background: rgba(255,255,255,0.03);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255,215,0,0.1);
        border-radius: 16px;
        padding: 15px;
        margin-bottom: 15px;
    }
    .badge-status {
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }
    .status-open { background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }
    .status-in-progress { background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); }
    .status-closed { background: rgba(255, 51, 51, 0.15); color: #ff3333; border: 1px solid rgba(255, 51, 51, 0.3); }
    
    .reply-box {
        background: rgba(255,215,0,0.05);
        border-left: 3px solid #FFD700;
        border-radius: 0 12px 12px 0;
        padding: 12px;
        margin-top: 15px;
    }
    .form-glass {
        background: rgba(0,0,0,0.3);
        border: 1px solid rgba(255,255,255,0.1);
        color: #fff;
        border-radius: 12px;
        padding: 12px;
        width: 100%;
        margin-bottom: 15px;
    }
    .form-glass:focus { border-color: #FFD700; outline: none; }
    
    /* Custom Modal for mobile */
    #newTicketModal {
        display: none;
        position: fixed; inset: 0; z-index: 9999;
        background: rgba(0,0,0,0.8);
        backdrop-filter: blur(5px);
        align-items: flex-end;
    }
    .modal-bottom-sheet {
        background: #111318;
        width: 100%;
        border-radius: 24px 24px 0 0;
        border-top: 1px solid rgba(255,215,0,0.2);
        padding: 20px;
        animation: slideUp 0.3s ease-out;
    }
    @keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }
</style>

<div class="mobile-glass-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Support</h1>
            <div class="page-subtitle mb-0">We are here to help 24/7</div>
        </div>
        <button class="btn-gold" onclick="document.getElementById('newTicketModal').style.display='flex'">
            <i class="ri-add-line"></i> New
        </button>
    </div>

    @if(count($tickets) > 0)
        @foreach($tickets as $ticket)
        <div class="ticket-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <span class="badge-status {{ $ticket->status == 'open' ? 'status-open' : ($ticket->status == 'in-progress' ? 'status-in-progress' : 'status-closed') }}">
                        {{ $ticket->status }}
                    </span>
                    <span class="badge-status" style="background: rgba(255,255,255,0.05); color: #ccc; margin-left: 5px;">
                        {{ $ticket->priority }}
                    </span>
                </div>
                <div style="font-size: 11px; color: rgba(255,255,255,0.5);">{{ $ticket->created_at->diffForHumans() }}</div>
            </div>
            <h5 style="font-weight: 700; font-size: 15px; margin-bottom: 8px;">#{{ $ticket->id }} - {{ $ticket->subject }}</h5>
            <p style="font-size: 13px; color: rgba(255,255,255,0.7); margin-bottom: 0;">{{ \Illuminate\Support\Str::limit($ticket->message, 100) }}</p>

            @if($ticket->admin_reply)
            <div class="reply-box">
                <div style="font-size: 11px; font-weight: 800; color: #FFD700; margin-bottom: 5px; text-transform: uppercase;">
                    <i class="ri-customer-service-2-fill"></i> Support Team
                </div>
                <p style="font-size: 13px; color: #fff; margin-bottom: 0; white-space: pre-wrap;">{{ $ticket->admin_reply }}</p>
            </div>
            @endif
        </div>
        @endforeach
        
        @if($tickets->hasPages())
        <div class="mt-3">{{ $tickets->links() }}</div>
        @endif
    @else
        <div class="text-center py-5">
            <i class="ri-customer-service-2-line" style="font-size: 50px; color: rgba(255,215,0,0.3); margin-bottom: 15px; display: block;"></i>
            <h4 style="font-weight: 700;">No Queries</h4>
            <p style="font-size: 13px; color: rgba(255,255,255,0.5);">You have no active support tickets.</p>
        </div>
    @endif
</div>

<!-- Bottom Sheet Modal -->
<div id="newTicketModal">
    <div class="modal-bottom-sheet">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 style="font-weight: 800; margin: 0; color: #FFD700;">Create Ticket</h4>
            <i class="ri-close-line" style="font-size: 24px; color: #fff; cursor: pointer;" onclick="document.getElementById('newTicketModal').style.display='none'"></i>
        </div>
        <form method="POST" action="{{ route('user.support_ticket.store') }}">
            @csrf
            <label style="font-size: 11px; font-weight: 800; color: rgba(255,255,255,0.6); text-transform: uppercase;">Subject</label>
            <input type="text" name="subject" class="form-glass" required placeholder="Issue summary">
            
            <label style="font-size: 11px; font-weight: 800; color: rgba(255,255,255,0.6); text-transform: uppercase;">Priority</label>
            <select name="priority" class="form-glass">
                <option value="low">Low</option>
                <option value="medium" selected>Medium</option>
                <option value="high">High</option>
                <option value="urgent">Urgent</option>
            </select>
            
            <label style="font-size: 11px; font-weight: 800; color: rgba(255,255,255,0.6); text-transform: uppercase;">Message</label>
            <textarea name="message" class="form-glass" rows="4" required placeholder="Describe your problem..."></textarea>
            
            <button type="submit" class="btn-gold mt-2" style="width: 100%;">Submit Ticket</button>
        </form>
    </div>
</div>

@push('js')
<script>
    @if(session('status'))
        toastr.success("{{ session('status') }}");
    @endif
</script>
@endpush
@endsection
