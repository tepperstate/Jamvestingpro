@extends('layouts.admin.app')
@section('title', 'Support Resolution Center')

@section('content')

<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-5">
        <div>
            <h1 class="h2 mb-1 text-white gradient-text" style="font-weight: 800;">Support Tickets</h1>
            <p class="text-muted mb-0">Manage user support requests.</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="bento-grid mb-4">
        <div class="glass-card bento-col-4 p-4 satin-border text-center">
            <div class="badge badge-danger-glass mb-2 px-3 py-1">OPEN</div>
            <div class="h3 text-white font-weight-bold">{{ $openCount }}</div>
        </div>
        <div class="glass-card bento-col-4 p-4 satin-border text-center">
            <div class="badge badge-warning-glass mb-2 px-3 py-1">IN PROGRESS</div>
            <div class="h3 text-white font-weight-bold">{{ $inProgressCount }}</div>
        </div>
        <div class="glass-card bento-col-4 p-4 satin-border text-center">
            <div class="badge badge-success-glass mb-2 px-3 py-1">CLOSED</div>
            <div class="h3 text-white font-weight-bold">{{ $closedCount }}</div>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="glass-card satin-border overflow-hidden">
        <div class="table-responsive">
            <table id="ticketTable" class="table text-white">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>USER</th>
                        <th>SUBJECT</th>
                        <th>PRIORITY</th>
                        <th>STATUS</th>
                        <th>DATE</th>
                        <th class="text-right">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                    <tr>
                        <td class="text-muted small">#{{ $ticket->id }}</td>
                        <td>
                            <div class="font-weight-bold text-white small">{{ $ticket->user->first_name ?? 'N/A' }} {{ $ticket->user->last_name ?? '' }}</div>
                            <div class="text-muted" style="font-size:10px">{{ $ticket->user->email ?? '' }}</div>
                        </td>
                        <td class="text-white small">{{ Str::limit($ticket->subject, 40) }}</td>
                        <td>
                            <div class="badge {{ $ticket->priority == 'urgent' ? 'badge-danger-glass' : ($ticket->priority == 'high' ? 'badge-warning-glass' : 'badge-success-glass') }}">
                                {{ strtoupper($ticket->priority) }}
                            </div>
                        </td>
                        <td>
                            <div class="badge {{ $ticket->status == 'open' ? 'badge-danger-glass' : ($ticket->status == 'in-progress' ? 'badge-warning-glass' : 'badge-success-glass') }}">
                                {{ strtoupper($ticket->status) }}
                            </div>
                        </td>
                        <td class="text-muted small">{{ $ticket->created_at->format('M d, Y H:i') }}</td>
                        <td class="text-right">
                            <button class="btn btn-sm glass-panel border-0 text-info view-ticket"
                                data-id="{{ $ticket->id }}"
                                data-subject="{{ $ticket->subject }}"
                                data-message="{{ $ticket->message }}"
                                data-reply="{{ $ticket->admin_reply }}"
                                data-status="{{ $ticket->status }}"
                                data-user="{{ ($ticket->user->first_name ?? '') . ' ' . ($ticket->user->last_name ?? '') }}"
                                data-toggle="modal" data-target="#ticketModal">
                                <i data-lucide="message-square" style="width:14px"></i>
                            </button>
                            <button class="btn btn-sm glass-panel border-0 text-success close-ticket" data-id="{{ $ticket->id }}" title="Close">
                                <i data-lucide="check-circle" style="width:14px"></i>
                            </button>
                            <a href="{{ route('admin.support_ticket.delete', $ticket->id) }}" class="btn btn-sm glass-panel border-0 text-danger" onclick="return confirm('Delete?')">
                                <i data-lucide="trash-2" style="width:14px"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $tickets->links() }}</div>
    </div>
</div>

@push('modals')
<!-- Ticket Detail / Reply Modal -->
<div class="modal fade" id="ticketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass-modal border-0 satin-border shadow-2xl">
            <div class="modal-header border-bottom-0 pb-0">
                <h4 class="modal-title font-weight-bold text-white" id="modal_subject">Ticket</h4>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-4 pt-0">
                <div class="mb-3">
                    <label class="text-muted small text-uppercase font-weight-bold">From</label>
                    <div class="text-white" id="modal_user"></div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small text-uppercase font-weight-bold">Message</label>
                    <div class="glass-panel p-3 text-white small" id="modal_message" style="white-space: pre-wrap;"></div>
                </div>
                <div class="mb-3" id="existing_reply_box" style="display:none">
                    <label class="text-muted small text-uppercase font-weight-bold">Previous Admin Reply</label>
                    <div class="glass-panel p-3 text-success small" id="modal_reply" style="white-space: pre-wrap;"></div>
                </div>
                <form method="POST" action="{{ route('admin.support_ticket.reply') }}">
                    @csrf
                    <input type="hidden" name="id" id="reply_ticket_id">
                    <label class="text-muted small text-uppercase font-weight-bold">Your Reply</label>
                    <textarea name="admin_reply" class="form-control glass-panel text-white border-0 mb-3" rows="4" placeholder="Type your response..." required></textarea>
                    <button type="submit" class="btn btn-premium w-100 py-3">Send Reply</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endpush

@if(session('status'))
<script>toastr.success("{{ session('status') }}");</script>
@endif

<script>
$(document).ready(function(){
    lucide.createIcons();

    $(document).on('click', '.view-ticket', function(){
        $('#modal_subject').text($(this).data('subject'));
        $('#modal_user').text($(this).data('user'));
        $('#modal_message').text($(this).data('message'));
        $('#reply_ticket_id').val($(this).data('id'));
        var reply = $(this).data('reply');
        if(reply) {
            $('#existing_reply_box').show();
            $('#modal_reply').text(reply);
        } else {
            $('#existing_reply_box').hide();
        }
    });

    $(document).on('click', '.close-ticket', function(){
        var id = $(this).data('id');
        $.ajax({
            url: "{{ route('admin.support_ticket.status') }}",
            method: 'POST',
            data: { id: id, status: 'closed', _token: '{{ csrf_token() }}' },
            success: function(res) {
                toastr.success('Ticket closed.');
                setTimeout(() => location.reload(), 800);
            }
        });
    });
});
</script>
@endsection

