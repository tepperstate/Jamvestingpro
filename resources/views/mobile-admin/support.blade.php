@extends('mobile-admin.layouts.app')

@section('header')
<div class="flex items-center gap-3">
    <a href="{{ route('admin.mobile.menu') }}" style="color: white; text-decoration: none;">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
    </a>
    <div style="font-size: 18px; font-weight: 700;">Support Desk</div>
</div>
@endsection

@section('content')
<div style="font-size: 13px; font-weight:600; color: var(--text-secondary); margin-bottom: 16px; padding-left: 8px; text-transform: uppercase; letter-spacing: 0.05em;">
    {{ number_format($open_tickets) }} Open Tickets
</div>

<x-mobile.data-table emptyMessage="No support tickets found.">
    @foreach($tickets as $ticket)
        <x-mobile.data-table-row 
            title="{{ $ticket->subject ?? 'No Subject' }}"
            subtitle="{{ $ticket->user->first_name ?? 'Unknown' }} {{ $ticket->user->last_name ?? '' }}"
            status="{{ ucfirst($ticket->status ?? 'open') }}"
            statusColor="{{ ($ticket->status ?? 'open') == 'closed' ? 'secondary' : (($ticket->status ?? 'open') == 'open' ? 'warning' : 'primary') }}">
            
            <x-slot name="actions">
                <button onclick="openTicketSheet({{ $ticket->id }}, '{{ addslashes($ticket->subject ?? '') }}', '{{ $ticket->user->first_name ?? 'Unknown' }}', '{{ addslashes($ticket->message ?? '') }}', '{{ $ticket->status ?? 'open' }}')" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15); color: white; border-radius: 10px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                </button>
            </x-slot>

            <x-slot name="details">
                <div class="flex justify-between items-center" style="font-size: 12px; color: var(--text-secondary);">
                    <div>
                        <span style="opacity: 0.7;">Date:</span>
                        <span style="color: white; font-weight: 500;">{{ $ticket->created_at ? $ticket->created_at->format('M d, H:i') : 'Unknown' }}</span>
                    </div>
                </div>
            </x-slot>
        </x-mobile.data-table-row>
    @endforeach
</x-mobile.data-table>

<div style="margin-top: 16px;">
    {{ $tickets->links() }}
</div>
@endsection

@section('modals')
<x-mobile.bottom-sheet id="ticketActionSheet" title="Support Ticket">
    <div style="margin-bottom: 24px; text-align: center;">
        <h5 id="sheetTicketSubject" class="text-white mb-1" style="font-weight: 700; font-size: 20px;">Subject</h5>
        <div id="sheetTicketUser" style="color: var(--text-secondary); font-size: 14px;">User Name</div>
        <div class="mt-2"><span id="sheetTicketStatus" class="eyebrow-tag eyebrow-warning">Open</span></div>
    </div>
    
    <div class="mobile-bezel-outer mb-3">
        <div class="mobile-bezel-inner" style="padding: 16px; font-size: 14px; color: var(--text-primary); line-height: 1.5; background: rgba(0,0,0,0.4);" id="sheetTicketMessage">
            Message content goes here...
        </div>
    </div>
    
    <div class="flex flex-col gap-3">
        
        <form action="{{ route('admin.support_ticket.reply') }}" method="POST" id="ticketReplyForm" style="display: block; width: 100%;">
            @csrf
            <input type="hidden" name="ticket_id" id="ticketId" value="">
            <div class="input-group">
                <label>Admin Reply</label>
                <textarea name="reply" rows="3" style="width:100%; background: var(--input-bg); border: 1px solid var(--card-border); color: white; border-radius: 12px; padding: 12px; font-size: 15px;" placeholder="Type your response..."></textarea>
            </div>
            <button type="submit" class="btn">
                Send Reply
                <div class="btn-icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </div>
            </button>
        </form>
        
        <form action="{{ route('admin.support_ticket.status') }}" method="POST" id="ticketCloseForm" style="display: block; width: 100%; margin-top: 8px;">
            @csrf
            <input type="hidden" name="ticket_id" id="ticketCloseId" value="">
            <input type="hidden" name="status" value="closed">
            <button type="submit" class="btn" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1);">
                Close Ticket
                <div class="btn-icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </div>
            </button>
        </form>
    </div>
</x-mobile.bottom-sheet>

<script>
    function openTicketSheet(id, subject, user, message, status) {
        document.getElementById('sheetTicketSubject').innerText = subject;
        document.getElementById('sheetTicketUser').innerText = user;
        document.getElementById('sheetTicketMessage').innerText = message;
        
        document.getElementById('ticketId').value = id;
        document.getElementById('ticketCloseId').value = id;
        
        const statusEl = document.getElementById('sheetTicketStatus');
        statusEl.innerText = status.toUpperCase();
        if(status.toLowerCase() === 'closed') {
            statusEl.className = 'eyebrow-tag eyebrow-secondary';
            document.getElementById('ticketReplyForm').style.display = 'none';
            document.getElementById('ticketCloseForm').style.display = 'none';
        } else {
            statusEl.className = 'eyebrow-tag eyebrow-warning';
            document.getElementById('ticketReplyForm').style.display = 'block';
            document.getElementById('ticketCloseForm').style.display = 'block';
        }
        
        openBottomSheet('ticketActionSheet');
    }
</script>
@endsection
