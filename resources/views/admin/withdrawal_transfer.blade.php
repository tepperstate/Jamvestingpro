@extends('layouts.admin.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-white mb-1">Internal Liquidity Flow</h4>
            <p class="text-secondary mb-0">Monitor and authorize intra-account ledger transfers.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
             <div class="badge bg-info-soft text-info px-4 py-2 rounded-pill border-glass">
                <span class="small font-weight-bold">TRANSFER SYSTEM: ACTIVE</span>
            </div>
            <button onclick="window.location.reload()" class="btn btn-primary btn-sm rounded-pill px-4 shadow-lg font-weight-bold">
                <i class="ri-refresh-line me-1"></i> Sync Registry
            </button>
        </div>
    </div>

    <!-- Main Transfer Registry -->
    <div class="glass-card p-0 overflow-hidden shadow-2xl">
        <div class="p-4 border-bottom border-glass d-flex justify-content-between align-items-center bg-black-soft">
           <div>
               <h5 class="outfit font-weight-bold text-white mb-0">Intra-Account Ledger</h5>
               <p class="text-secondary small mb-0 opacity-75">Internal asset migration between user sub-accounts.</p>
           </div>
           <span class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill">{{ count($data) }} Logged Transfers</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="example">
                <thead class="bg-black-soft text-secondary small text-uppercase">
                    <tr>
                        <th class="border-0 px-4 py-3">Rank</th>
                        <th class="border-0 py-3">Type</th>
                        <th class="border-0 py-3">Volume</th>
                        <th class="border-0 py-3">User Artifact</th>
                        <th class="border-0 py-3">Origin (From)</th>
                        <th class="border-0 py-3">Destination (To)</th>
                        <th class="border-0 py-3">Status</th>
                        <th class="border-0 px-4 py-3 text-end">Operation</th>
                    </tr>
                </thead>
                <tbody class="text-white border-glass font-text">
                    @forelse ($data as $key => $value)
                        <tr class="border-glass">
                            <td class="px-4 text-secondary small">TX-{{++$key}}</td>
                            <td>
                                <span class="badge bg-primary-soft text-primary x-small text-uppercase">{{$value->type}}</span>
                            </td>
                            <td>
                                <div class="text-success font-weight-bold">${{number_format($value->amount,2)}}</div>
                            </td>
                            <td>
                                <div class="font-weight-bold text-white mb-0">{{ $value->user->first_name ?? 'N/A' }}</div>
                                <div class="x-small text-secondary">UID: {{$value->user_id}}</div>
                            </td>
                            <td class="small text-secondary">{{ $value->user->email }}</td>
                            <td class="small text-info">{{ $value->address ?? 'INTERNAL_VAULT' }}</td>
                            <td>
                                @if($value->status =='Approved')
                                    <span class="badge bg-success-soft text-success px-3 py-1 rounded-pill small">FINALIZED</span>
                                @elseif($value->status =='pending')
                                    <span class="badge bg-warning-soft text-warning px-3 py-1 rounded-pill small">EVALUATING</span>
                                @else
                                    <span class="badge bg-danger-soft text-danger px-3 py-1 rounded-pill small">REVERSED</span>
                                @endif
                            </td>
                            <td class="px-4 text-end">
                                @if($value->status =='pending')
                                    <div class="d-flex justify-content-end">
                                        <select class="action-select bg-dark-soft border-glass text-white px-3 py-1 small rounded-pill" did="{{$value->id}}" style="outline: none; cursor: pointer;">
                                            <option selected disabled>EXECUTE</option>
                                            <option value="Approved">APPROVE</option>
                                            <option value="Reversal">REVERSE</option>
                                        </select>
                                    </div>
                                @else
                                    <div class="text-secondary small opacity-50"><i class="ri-checkbox-circle-fill me-1"></i> Recorded</div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-secondary">
                                <i class="ri-arrow-left-right-line h1 d-block mb-3 opacity-20"></i>
                                <span>No Internal Movements Detected</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .bg-dark-soft { background: rgba(0,0,0,0.2) !important; }
    .bg-black-soft { background: rgba(0,0,0,0.3) !important; }
    .border-glass { border-color: rgba(255,255,255,0.05) !important; }
    .bg-primary-soft { background: rgba(59, 130, 246, 0.1); }
    .bg-success-soft { background: rgba(255, 51, 51, 0.1); }
    .bg-info-soft { background: rgba(6, 182, 212, 0.1); }
    .bg-warning-soft { background: rgba(245, 158, 11, 0.1); }
    .bg-danger-soft { background: rgba(239, 68, 68, 0.1); }
    .table-hover tbody tr:hover { background: rgba(255,255,255,0.02); }
    .x-small { font-size: 10px; }
</style>

<script>
    $(document).ready(function(){
        $('#example').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": false,
            "autoWidth": false,
            "responsive": true,
            "pageLength": 10
        });

        $(document).on('change',".action-select",function(){
            let id = $(this).attr('did')
            let action = $(this).val()
            
            if(!id || !action) return;

            if(confirm(`Are you sure you want to ${action} this transfer?`)) {
                $.post("{{route('updateTransfer')}}", {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    action: action
                }, function(data) {
                    if(data.status) toastr.success(data.status, "Ledger Updated");
                    setTimeout(() => location.reload(), 800);
                }).fail(function() {
                    toastr.error("Sync process failure", "Error");
                });
            } else {
                $(this).val('EXECUTE');
            }
        });
    });
</script>
@endsection



