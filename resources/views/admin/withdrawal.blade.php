@extends('layouts.admin.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-white mb-1">Liquidity Extraction Center</h4>
            <p class="text-secondary mb-0">Managing architectural capital debits and validator payouts.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
             <div class="badge bg-danger-soft text-danger px-4 py-2 rounded-pill border-glass">
                <span class="small font-weight-bold">SECURITY GATEWAY ACTIVE</span>
            </div>
            <button onclick="window.location.reload()" class="btn btn-primary btn-sm rounded-pill px-4 shadow-lg font-weight-bold">
                <i class="ri-refresh-line me-1"></i> Refresh Secure Storage
            </button>
        </div>
    </div>

    <!-- Stats & Filters Bar -->
    <div class="row mb-4">
        <div class="col-lg-3">
             <div class="glass-card shadow-sm p-3 d-flex align-items-center border-glass">
                <div class="icon-box bg-success-soft rounded-pill p-3 me-3">
                    <i class="ri-bitcoin-line h4 mb-0 text-success"></i>
                </div>
                <div>
                    <div class="text-white font-weight-bold small text-uppercase">Crypto System</div>
                    <div class="text-secondary x-small">Primary Liquidity Path</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Withdrawal Registry -->
    <div class="glass-card p-0 overflow-hidden shadow-2xl">
        <div class="p-4 border-bottom border-glass d-flex justify-content-between align-items-center bg-black-soft">
           <div>
               <h5 class="outfit font-weight-bold text-white mb-0">Payout Authorization Stack</h5>
               <p class="text-secondary small mb-0 opacity-75">Outbound capital flows for validation.</p>
           </div>
           <span class="badge bg-danger-soft text-danger px-3 py-2 rounded-pill">{{ $data->whereIn('status',['pending', 'processing'])->count() }} Active Requests</span>
           <!-- Debug: Collection IDs: {{ $data->pluck('id')->implode(',') }} -->
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="example">
                <thead class="bg-black-soft text-secondary small text-uppercase">
                    <tr>
                        <th class="border-0 px-4 py-3 d-none d-lg-table-cell">Rank</th>
                        <th class="border-0 py-3">User & Identity</th>
                        <th class="border-0 py-3 d-none d-md-table-cell">System Address</th>
                        <th class="border-0 py-3">Asset & Volume</th>
                        <th class="border-0 py-3">Status</th>
                        <th class="border-0 py-3 d-none d-lg-table-cell">Timestamp</th>
                        <th class="border-0 px-4 py-3 text-end">Execution</th>
                    </tr>
                </thead>
                <tbody class="text-white border-glass font-text">
                    @forelse ($data as $key => $value)
                        <tr class="border-glass">
                            <td class="px-4 text-secondary small d-none d-lg-table-cell">WD-{{ $data->firstItem() + $key }}</td>
                            <td>
                                <div class="font-weight-bold text-white mb-0">{{ $value->user->first_name ?? 'NOT_FOUND' }}</div>
                                <span class="badge bg-primary-soft text-primary x-small text-uppercase">{{$value->type}}</span>
                            </td>
                            <td class="d-none d-md-table-cell">
                                <code class="text-info x-small tracking-wider opacity-75">{{$value->address}}</code>
                            </td>
                            <td>
                                <div class="text-danger font-weight-bold">${{number_format($value->amount,2)}}</div>
                            </td>
                            <td>
                                @if($value->status =='confirmed' || $value->status =='success')
                                    <span class="badge bg-success text-white px-3 py-2" style="border-radius:8px; font-size:0.7rem; font-weight:800; box-shadow: 0 2px 4px rgba(0,0,0,0.2);"><i class="ri-checkbox-circle-line me-1"></i> CONFIRMED</span>
                                @elseif($value->status =='pending')
                                    <span class="badge bg-warning text-dark px-3 py-2 animate-pulse" style="border-radius:8px; font-size:0.7rem; font-weight:800; box-shadow: 0 2px 4px rgba(0,0,0,0.2);"><i class="ri-time-line me-1"></i> PENDING</span>
                                @elseif($value->status =='processing')
                                    <span class="badge bg-info text-white px-3 py-2 animate-pulse" style="border-radius:8px; font-size:0.7rem; font-weight:800; box-shadow: 0 2px 4px rgba(0,0,0,0.2);"><i class="ri-loader-4-line me-1"></i> PROCESSING</span>
                                @else
                                    <span class="badge bg-danger text-white px-3 py-2" style="border-radius:8px; font-size:0.7rem; font-weight:800; box-shadow: 0 2px 4px rgba(0,0,0,0.2);"><i class="ri-close-circle-line me-1"></i> REVERSED</span>
                                @endif
                            </td>
                            <td class="small text-secondary d-none d-lg-table-cell">{{$value->created_at->format('M d, Y H:i')}}</td>
                            <td class="px-4 text-end">
                                <div class="d-flex justify-content-end align-items-center gap-2">
                                    <a href="{{ route('admin.history.withdrawal.edit', $value->id) }}" class="btn glass-panel text-info py-1 px-2 border-0" style="padding: 0.25rem 0.5rem; font-size: 0.8rem; border-radius: 8px;" title="Full Edit & Recalculate">
                                        <i class="ri-edit-line"></i>
                                    </a>
                                     @if($value->status =='pending' || $value->status =='processing')
                                         <button class="btn btn-sm btn-success rounded-pill px-3 py-1 action-btn-trigger" data-action="Approved" data-id="{{$value->id}}" data-source="{{ $value->source }}">
                                             <i class="ri-check-line me-1"></i> APPROVE
                                         </button>
                                         <button class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 action-btn-trigger" data-action="Reversal" data-id="{{$value->id}}" data-source="{{ $value->source }}">
                                             <i class="ri-arrow-go-back-line me-1"></i> REVERSE
                                         </button>
                                     @else
                                        <div class="text-secondary small opacity-50"><i class="ri-shield-check-fill me-1"></i> Protected</div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-secondary">
                                <i class="ri-inbox-unarchive-line h1 d-block mb-3 opacity-20"></i>
                                <span>No Payout Requests Found in the Registry</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-top border-glass bg-black-soft">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="text-secondary small">
                    Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of {{ $data->total() }} disbursement logs
                </div>
                <div class="glass-pagination">
                    {{ $data->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('modals')
<!-- Modal Redesign -->
<div class="modal fade" id="pipbuilder" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document"> 
        <div class="modal-content glass-card overflow-hidden shadow-2xl">
            <div class="modal-header border-bottom border-glass p-4 bg-black-soft">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-primary-soft rounded-pill p-2 me-3">
                        <i class="ri-fingerprint-line h4 mb-0 text-primary"></i>
                    </div>
                    <h5 class="outfit font-weight-bold text-white mb-0" id="name">Finalize Disbursement</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-dark-soft">
                <form method="post" action="{{route('withdrawal.updated')}}">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="user_id" id="user">
                    
                    <div class="form-group mb-4">
                        <label class="small text-secondary font-weight-bold text-uppercase mb-2 tracking-wider">Blockchain Transaction Hash</label>
                        <input type="text" name="hash" id="hash" class="form-control bg-black-soft border-glass text-white py-3 rounded-3" placeholder="0x... or TRX hash" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 py-3 font-weight-bold rounded-3 shadow-lg">
                        <i class="ri-send-plane-fill me-2"></i> CONFIRM DISBURSEMENT
                    </button>
                </form>   
            </div>
        </div>
    </div>
</div>
@endpush

<style>
    .bg-dark-soft { background: rgba(0,0,0,0.2) !important; }
    .bg-black-soft { background: rgba(0,0,0,0.3) !important; }
    .border-glass { border-color: rgba(255,255,255,0.05) !important; }
    .bg-primary-soft { background: rgba(59, 130, 246, 0.1); }
    .bg-success-soft { background: rgba(255, 51, 51, 0.1); }
    .bg-warning-soft { background: rgba(245, 158, 11, 0.1); }
    .bg-danger-soft { background: rgba(239, 68, 68, 0.1); }
    .table-hover tbody tr { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
    .table-hover tbody tr:hover { 
        background: rgba(255,255,255,0.04) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        position: relative;
        z-index: 10;
    }
    .x-small { font-size: 10px; }
    .tracking-wider { letter-spacing: 0.05em; }

    /* Glass Pagination Styling */
    .glass-pagination .pagination { margin-bottom: 0; gap: 5px; }
    .glass-pagination .page-item .page-link {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.1);
        color: rgba(255,255,255,0.6);
        border-radius: 8px;
        padding: 8px 16px;
        transition: all 0.2s;
        font-size: 0.8rem;
    }
    .glass-pagination .page-item.active .page-link {
        background: var(--accent-primary, #3b82f6) !important;
        color: white !important;
        border-color: var(--accent-primary, #3b82f6);
    }
    .glass-pagination .page-item.disabled .page-link {
        background: rgba(255,255,255,0.01);
        color: rgba(255,255,255,0.1);
    }
    .glass-pagination .page-link:hover {
        background: rgba(255,255,255,0.1);
        color: white;
    }
</style>

<script>
    $(document).ready(function(){
        $('#example').DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": false,
            "autoWidth": false,
            "responsive": true,
            "pageLength": 100
        });

        $(document).on("click",".scc",function(){
            $("#id").val($(this).attr('did'))
            $("#user").val($(this).attr('user'))
        });

        $(document).on('click',".action-btn-trigger",function(){
            let id = $(this).attr('data-id')
            let action = $(this).attr('data-action')
            let source = $(this).attr('data-source') || 'withdrawals'
            
            if(!id || !action) return;

            if(confirm(`Are you sure you want to ${action} this withdrawal?`)) {
                $.post("{{route('updatewithdrawals')}}", {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    action: action,
                    source: source
                }, function(data) {
                    if(data.status) toastr.success(data.status, "Vault Updated");
                    setTimeout(() => location.reload(), 800);
                }).fail(function() {
                    toastr.error("Transaction sync failure", "Error");
                });
            }
        });
        
        // Anime.js Entrance Animation
        if (typeof anime !== 'undefined') {
            anime({
                targets: '#example tbody tr',
                translateY: [30, 0],
                opacity: [0, 1],
                delay: anime.stagger(80, {start: 100}),
                easing: 'easeOutQuint',
                duration: 800
            });
        }
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
@endsection



