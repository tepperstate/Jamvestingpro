@extends('layouts.admin.app')
@section('title', 'Growth Plan Oversight')

@section('content')

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-5">
        <div>
            <h1 class="h2 mb-1 text-white gradient-text" style="font-weight: 800;">Growth Plan Oversight</h1>
            <p class="text-muted mb-0">Monitor and manipulate active user investments and protocol performance.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
             <div class="badge badge-info-glass px-4 py-2 border-0 satin-border" style="background: rgba(59, 130, 246, 0.05) !important;">
                <span class="text-white small font-weight-bold">ACTIVE PROTOCOLS: {{ $investments->total() }}</span>
            </div>
            <button onclick="window.location.reload()" class="btn btn-primary glass-panel border-0 px-4 py-2 satin-border" style="background: var(--accent-primary) !important; color: #ffffff !important; font-weight: 700;">
                <i data-lucide="refresh-cw" class="mr-2" style="width: 16px; display: inline-block; vertical-align: middle;"></i> Sync Data
            </button>
        </div>
    </div>

    <!-- Main Investment Registry -->
    <div class="glass-card satin-border overflow-hidden shadow-2xl">
        <div class="p-4 d-flex justify-content-between align-items-center" style="background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--glass-border);">
            <div>
                <h3 class="h5 text-white mb-1 font-weight-bold">Investment Management</h3>
                <p class="text-muted x-small mb-0">Granular control over individual user growth outcomes.</p>
            </div>
        </div>
        <div class="table-responsive">
            <table id="investmentTable" class="table text-white">
                <thead>
                    <tr>
                        <th>USER</th>
                        <th>PROTOCOL / PACKAGE</th>
                        <th>CAPITAL</th>
                        <th>DAILY RATE (%)</th>
                        <th>TERM STATUS</th>
                        <th>DATES</th>
                        <th class="text-right">MANIPULATION</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($investments as $investment)
                        <tr id="row-{{ $investment->id }}">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar glass-panel mr-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; border-radius: 14px; background: rgba(59, 130, 246, 0.05); border: 1px solid rgba(59, 130, 246, 0.1);">
                                        {{ substr($investment->user?->first_name ?? 'U', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-weight-bold text-white h6 mb-0">{{ $investment->user?->first_name ?? 'Unknown' }} {{ $investment->user?->last_name ?? '' }}</div>
                                        <div class="text-muted x-small text-uppercase">USER#{{ $investment->user_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-white small font-weight-bold">{{ $investment->name }}</div>
                                <div class="text-muted x-small">PLAN ID: #{{ $investment->package_id }}</div>
                            </td>
                            <td>
                                <div class="text-white font-weight-bold">${{ number_format($investment->amount, 2) }}</div>
                                <div class="text-muted x-small">TOTAL PROFIT: ${{ number_format(($investment->amount * ($investment->perc / 100)), 2) }}</div>
                            </td>
                            <td>
                                <div class="text-info small font-weight-bold">{{ $investment->perc }}% Total</div>
                                <div class="text-muted x-small">({{ $investment->day > 0 ? number_format($investment->perc / $investment->day, 2) : '0.00' }}% Daily)</div>
                            </td>
                            <td>
                                @php
                                    $statusColor = match($investment->status) {
                                        'active' => 'success',
                                        'paused' => 'warning',
                                        'completed' => 'info',
                                        default => 'danger'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusColor }} text-white px-3 py-2" style="border-radius:8px; font-size:0.65rem; font-weight:800;">
                                    {{ strtoupper($investment->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="text-muted x-small">START: {{ \Illuminate\Support\Carbon::parse($investment->start_date)->format('M d, Y') }}</div>
                                <div class="text-muted x-small">END: {{ \Illuminate\Support\Carbon::parse($investment->end_date)->format('M d, Y') }}</div>
                            </td>
                            <td class="text-right">
                                <div class="d-flex justify-content-end gap-2">
                                    @if($investment->status == 'active')
                                        <button onclick="updateInvestment({{ $investment->id }}, 'pause')" class="btn btn-sm glass-panel text-warning border-0" title="Pause Growth"><i data-lucide="pause-circle" style="width:16px"></i></button>
                                    @elseif($investment->status == 'paused')
                                        <button onclick="updateInvestment({{ $investment->id }}, 'resume')" class="btn btn-sm glass-panel text-success border-0" title="Resume Growth"><i data-lucide="play-circle" style="width:16px"></i></button>
                                    @endif
                                    
                                    <button onclick="showEditModal({{ $investment->id }}, {{ $investment->perc }}, {{ $investment->day }})" class="btn btn-sm glass-panel text-info border-0" title="Edit Parameters"><i data-lucide="edit-3" style="width:16px"></i></button>
                                    
                                    @if($investment->status != 'completed')
                                        <button onclick="updateInvestment({{ $investment->id }}, 'force_complete')" class="btn btn-sm glass-panel text-white border-0" title="Force Success/Payout"><i data-lucide="check-circle-2" style="width:16px"></i></button>
                                    @endif
                                    
                                    <button onclick="updateInvestment({{ $investment->id }}, 'delete')" class="btn btn-sm glass-panel border-0 text-danger" title="Delete History"><i data-lucide="trash-2" style="width:16px"></i></button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3" style="background: rgba(255,255,255,0.01);">
            {{ $investments->links() }}
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content glass-card satin-border text-white">
            <div class="modal-header border-0">
                <h5 class="modal-title font-weight-bold">Rig Investment Parameters</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="edit_id">
                <div class="form-group mb-4">
                    <label class="small text-muted mb-2">TARGET TOTAL RETURN (%)</label>
                    <input type="number" step="0.01" id="edit_perc" class="form-control glass-panel text-white border-0 py-4 px-3" style="background: rgba(255,255,255,0.05);">
                    <p class="x-small text-info mt-1">This will change the final payout amount for this specific user.</p>
                </div>
                <div class="form-group mb-4">
                    <label class="small text-muted mb-2">REMAINING DAYS / DURATION</label>
                    <input type="number" id="edit_day" class="form-control glass-panel text-white border-0 py-4 px-3" style="background: rgba(255,255,255,0.05);">
                    <p class="x-small text-warning mt-1">Changing this adjusts the maturation date.</p>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-link text-muted" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="saveInvestment()" class="btn btn-primary px-4 py-2 satin-border" style="background: var(--accent-primary) !important; color: #ffffff !important; font-weight: 700;">Apply Rigging</button>
            </div>
        </div>
    </div>
</div>

<style>
    .x-small { font-size: 10px; }
    .avatar { font-family: 'Outfit', sans-serif; font-weight: 800; color: #3b82f6; font-size: 1.2rem; }
    #wrapper #content-wrapper #content { background: transparent !important; }
    .table-hover tbody tr:hover { 
        background: rgba(255,255,255,0.04) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    }
    .badge-success-glass { background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #34d399; }
    .badge-info-glass { background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2); color: #3b82f6; }
</style>

<script>
    function updateInvestment(id, action) {
        if (action === 'delete' && !confirm('Are you sure you want to delete this investment history record?')) return;
        
        $.ajax({
            url: "{{ route('admin.investments.update') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: id,
                action: action
            },
            success: function(res) {
                toastr.success(res.status);
                if (action === 'delete') {
                    $(`#row-${id}`).fadeOut();
                } else {
                    window.location.reload();
                }
            },
            error: function(err) {
                toastr.error(err.responseJSON.error || "An error occurred");
            }
        });
    }

    function showEditModal(id, perc, day) {
        $('#edit_id').val(id);
        $('#edit_perc').val(perc);
        $('#edit_day').val(day);
        $('#editModal').modal('show');
    }

    function saveInvestment() {
        const id = $('#edit_id').val();
        const perc = $('#edit_perc').val();
        const day = $('#edit_day').val();

        $.ajax({
            url: "{{ route('admin.investments.update') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: id,
                action: 'edit',
                perc: perc,
                day: day
            },
            success: function(res) {
                toastr.success(res.status);
                $('#editModal').modal('hide');
                window.location.reload();
            }
        });
    }

    $(document).ready(function(){
        lucide.createIcons();
        if (typeof anime !== 'undefined') {
            anime({
                targets: '#investmentTable tbody tr',
                translateY: [30, 0],
                opacity: [0, 1],
                delay: anime.stagger(50, {start: 100}),
                easing: 'easeOutQuint',
                duration: 800
            });
        }
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
@endsection

