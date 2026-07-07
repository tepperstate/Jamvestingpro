@extends('layouts.admin.app')
@section('title', 'DeFi Yield Vaults & Staking')

@section('content')

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-5">
        <div>
            <h1 class="h2 mb-1 text-white gradient-text" style="font-weight: 800;">DeFi Yield Vaults & Staking</h1>
            <p class="text-muted mb-0">Monitor and manage user staking positions.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
             <div class="badge badge-info-glass px-4 py-2 border-0 satin-border" style="background: rgba(59, 130, 246, 0.05) !important;">
                <span class="text-white small font-weight-bold">ACTIVE VAULTS: {{ $stakings->total() }}</span>
            </div>
            <button onclick="window.location.reload()" class="btn btn-primary glass-panel border-0 px-4 py-2 satin-border" style="background: var(--accent-primary) !important; color: #ffffff !important; font-weight: 700;">
                <i data-lucide="refresh-cw" class="mr-2" style="width: 16px; display: inline-block; vertical-align: middle;"></i> Sync Data
            </button>
        </div>
    </div>

    <div class="glass-card satin-border overflow-hidden shadow-2xl">
        <div class="p-4 d-flex justify-content-between align-items-center" style="background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--glass-border);">
            <div>
                <h3 class="h5 text-white mb-1 font-weight-bold">Staking Oversight</h3>
                <p class="text-muted x-small mb-0">Manage active user staking contracts.</p>
            </div>
        </div>
        <div class="table-responsive">
            <table id="dataTable" class="table text-white">
                <thead>
                    <tr>
                        <th>USER</th>
                        <th>VAULT / APY</th>
                        <th>STAKED CAPITAL</th>
                        <th>YIELD EARNED</th>
                        <th>STATUS</th>
                        <th>DATES</th>
                        <th class="text-right">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stakings as $staking)
                        <tr id="row-{{ $staking->id }}">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar glass-panel mr-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; border-radius: 14px; background: rgba(59, 130, 246, 0.05); border: 1px solid rgba(59, 130, 246, 0.1);">
                                        {{ substr($staking->user?->first_name ?? 'U', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-weight-bold text-white h6 mb-0">{{ $staking->user?->first_name ?? 'Unknown' }} {{ $staking->user?->last_name ?? '' }}</div>
                                        <div class="text-muted x-small text-uppercase">USER#{{ $staking->user_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-white small font-weight-bold">{{ $staking->plan?->name ?? 'Unknown Plan' }}</div>
                                <div class="text-muted x-small">APY: {{ $staking->plan?->apy_percentage ?? '0' }}%</div>
                            </td>
                            <td>
                                <div class="text-white font-weight-bold">${{ number_format($staking->amount, 2) }}</div>
                                @if($staking->is_demo)
                                    <div class="badge badge-warning">Demo</div>
                                @endif
                            </td>
                            <td>
                                <div class="text-success small font-weight-bold">+${{ number_format($staking->earned, 2) }}</div>
                            </td>
                            <td>
                                @php
                                    $statusColor = match($staking->status) {
                                        'active' => 'success',
                                        'paused' => 'warning',
                                        'completed' => 'info',
                                        'withdrawn' => 'secondary',
                                        default => 'danger'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusColor }} text-white px-3 py-2" style="border-radius:8px; font-size:0.65rem; font-weight:800;">
                                    {{ strtoupper($staking->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="text-muted x-small">START: {{ \Illuminate\Support\Carbon::parse($staking->start_date)->format('M d, Y') }}</div>
                                <div class="text-muted x-small">END: {{ \Illuminate\Support\Carbon::parse($staking->end_date)->format('M d, Y') }}</div>
                            </td>
                            <td class="text-right">
                                <div class="d-flex justify-content-end gap-2">
                                    @if($staking->status == 'active')
                                        <button onclick="updateRecord({{ $staking->id }}, 'pause')" class="btn btn-sm glass-panel text-warning border-0" title="Pause Yield"><i data-lucide="pause-circle" style="width:16px"></i></button>
                                    @elseif($staking->status == 'paused')
                                        <button onclick="updateRecord({{ $staking->id }}, 'resume')" class="btn btn-sm glass-panel text-success border-0" title="Resume Yield"><i data-lucide="play-circle" style="width:16px"></i></button>
                                    @endif
                                    
                                    <button onclick="showEditModal({{ $staking->id }}, {{ $staking->earned }})" class="btn btn-sm glass-panel text-info border-0" title="Edit Earned Yield"><i data-lucide="edit-3" style="width:16px"></i></button>
                                    
                                    @if(!in_array($staking->status, ['completed', 'withdrawn']))
                                        <button onclick="updateRecord({{ $staking->id }}, 'force_complete')" class="btn btn-sm glass-panel text-white border-0" title="Force Complete & Refund"><i data-lucide="check-circle-2" style="width:16px"></i></button>
                                    @endif
                                    
                                    <button onclick="updateRecord({{ $staking->id }}, 'delete')" class="btn btn-sm glass-panel border-0 text-danger" title="Delete Contract"><i data-lucide="trash-2" style="width:16px"></i></button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3" style="background: rgba(255,255,255,0.01);">
            {{ $stakings->links() }}
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content glass-card satin-border text-white">
            <div class="modal-header border-0">
                <h5 class="modal-title font-weight-bold">Adjust Staking Yield</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="edit_id">
                <div class="form-group mb-4">
                    <label class="small text-muted mb-2">EARNED YIELD ($)</label>
                    <input type="number" step="0.01" id="edit_earned" class="form-control glass-panel text-white border-0 py-4 px-3" style="background: rgba(255,255,255,0.05);">
                    <p class="x-small text-info mt-1">This amount will be added to their capital upon completion.</p>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-link text-muted" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="saveRecord()" class="btn btn-primary px-4 py-2 satin-border" style="background: var(--accent-primary) !important; color: #ffffff !important; font-weight: 700;">Apply Update</button>
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
    function updateRecord(id, action) {
        if (action === 'delete' && !confirm('Are you sure you want to delete this record?')) return;
        if (action === 'force_complete' && !confirm('Are you sure you want to force complete this? The user will be refunded immediately.')) return;
        
        $.ajax({
            url: "{{ route('admin.staking.update') }}",
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

    function showEditModal(id, earned) {
        $('#edit_id').val(id);
        $('#edit_earned').val(earned);
        $('#editModal').modal('show');
    }

    function saveRecord() {
        const id = $('#edit_id').val();
        const earned = $('#edit_earned').val();

        $.ajax({
            url: "{{ route('admin.staking.update') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: id,
                action: 'edit',
                earned: earned
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
    });
</script>
@endsection

