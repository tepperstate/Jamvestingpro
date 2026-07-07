@extends('layouts.user.app')
@section('title', 'Help & Support')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-5" data-aos="fade-up">
    <div>
        <h1 class="display-6 outfit font-weight-bold mb-1 text-white">Help & Support</h1>
        <p class="text-secondary mb-0 lead">Have a question? Our team is here to help you 24/7.</p>
    </div>
    <button class="btn btn-premium btn-lg px-4 shadow-lg" data-toggle="modal" data-target="#newTicket">
        <i class="ri-add-line me-2"></i> Open New Query
    </button>
</div>

<!-- Tickets List -->
<div class="glass-card overflow-hidden shadow-lg mb-5" style="background: rgba(16, 18, 27, 0.4); backdrop-filter: blur(16px); border-radius: 24px; border: 1px solid rgba(255,255,255,0.05);" data-aos="fade-up">
    @if(count($tickets) > 0)
        @foreach($tickets as $ticket)
        <div class="ticket-row p-4 border-bottom" style="border-color: rgba(255,255,255,0.05) !important;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="badge {{ $ticket->status == 'open' ? 'bg-danger' : ($ticket->status == 'in-progress' ? 'bg-warning text-dark' : 'bg-success') }} px-3 py-2" style="border-radius: 8px; font-size: 0.65rem; font-weight: 800; min-width: 80px; letter-spacing: 0.05em;">
                            {{ strtoupper($ticket->status) }}
                        </span>
                        <span class="badge bg-secondary-glass text-secondary px-3 py-2" style="background: rgba(255,255,255,0.05); border-radius: 8px; font-size: 0.65rem; font-weight: 700;">
                            PRIORITY: {{ strtoupper($ticket->priority) }}
                        </span>
                    </div>
                    <h5 class="outfit font-weight-bold mb-2 text-white">#TICKET-{{ $ticket->id }} — {{ $ticket->subject }}</h5>
                    <p class="text-secondary mb-0" style="font-size: 0.95rem;">{{ \Illuminate\Support\Str::limit($ticket->message, 150) }}</p>
                </div>
                <div class="text-right text-secondary small opacity-75">
                    <i class="ri-time-line me-1"></i> {{ $ticket->created_at->diffForHumans() }}
                </div>
            </div>

            @if($ticket->admin_reply)
            <div class="mt-4 p-4 shadow-sm" style="background: rgba(59, 130, 246, 0.04); border: 1px solid rgba(59, 130, 246, 0.1); border-radius: 16px;">
                <div class="d-flex align-items-center mb-3 gap-2">
                    <div class="icon-box bg-primary-soft" style="width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: rgba(59, 130, 246, 0.1);">
                        <i class="ri-customer-service-2-fill text-primary" style="font-size: 14px;"></i>
                    </div>
                    <span class="small font-weight-bold text-primary text-uppercase tracking-wider">Support Team</span>
                    <span class="text-secondary x-small opacity-50">· {{ $ticket->replied_at ? $ticket->replied_at->diffForHumans() : 'Recently' }}</span>
                </div>
                <p class="mb-0 text-white opacity-90" style="font-size: 0.9rem; white-space: pre-wrap; line-height: 1.6;">{{ $ticket->admin_reply }}</p>
            </div>
            @endif
        </div>
        @endforeach
    @else
        <div class="p-5 text-center py-5">
            <i class="ri-ticket-2-line mb-4 d-block opacity-20" style="font-size: 80px; color: var(--text-secondary);"></i>
            <h4 class="outfit text-white">No Active Queries</h4>
            <p class="text-secondary lead mx-auto" style="max-width: 400px;">Our analysts are standing by. Open a new ticket if you require assistance.</p>
        </div>
    @endif

    @if($tickets->hasPages())
    <div class="p-3 border-top border-secondary border-opacity-10">{{ $tickets->links() }}</div>
    @endif
</div>

<!-- New Ticket Modal -->
<div class="modal fade" id="newTicket" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-0" style="background: var(--bg-card);">
            <div class="modal-header border-bottom" style="border-color: var(--glass-border) !important;">
                <h5 class="modal-title outfit font-weight-bold">Submit a Ticket</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-4">
                <form method="POST" action="{{ route('user.support_ticket.store') }}">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="text-secondary small font-weight-bold text-uppercase">Subject</label>
                        <input type="text" name="subject" class="form-control" required placeholder="Brief description of your issue" style="background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border); color: white; border-radius: 10px; padding: 12px;">
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-secondary small font-weight-bold text-uppercase">Priority</label>
                        <select name="priority" class="form-control" style="background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border); color: white; border-radius: 10px; padding: 12px;">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-secondary small font-weight-bold text-uppercase">Message</label>
                        <textarea name="message" class="form-control" rows="5" required placeholder="Describe your issue in detail..." style="background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border); color: white; border-radius: 10px; padding: 12px;"></textarea>
                    </div>
                    <button type="submit" class="btn btn-premium w-100 py-3 font-weight-bold">Submit Ticket</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    @if(session('status'))
        toastr.success("{{ session('status') }}");
    @endif
    
    // Staggered Entrance Animation
    window.addEventListener('load', () => {
        if (typeof anime !== 'undefined') {
            anime({
                targets: '.ticket-row',
                translateX: [20, 0],
                opacity: [0, 1],
                delay: anime.stagger(100),
                easing: 'easeOutQuint',
                duration: 800
            });
        }
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
@endpush
@endsection

