@extends('layouts.admin.app')
@section('title', 'Financial Gateway')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-white mb-1">Capital Inflow Gateway</h4>
            <p class="text-secondary mb-0">Review and authorize incoming capital execution.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
             <div class="badge bg-success-soft text-success px-4 py-2 rounded-pill border-glass">
                <span class="small font-weight-bold">LIQUIDITY STATUS: OPTIMAL</span>
            </div>
            <button onclick="window.location.reload()" class="btn btn-primary btn-sm rounded-pill px-4 shadow-lg font-weight-bold">
                <i class="ri-refresh-line me-1"></i> Sync Ledger
            </button>
        </div>
    </div>

    <!-- Main Deposit Registry -->
    <div class="glass-card p-0 overflow-hidden shadow-2xl">
        <div class="p-4 border-bottom border-glass d-flex justify-content-between align-items-center bg-black-soft">
           <div>
               <h5 class="outfit font-weight-bold text-white mb-0">Transaction Authentication Stack</h5>
               <p class="text-secondary small mb-0 opacity-75">Incoming flows pending system validation.</p>
           </div>
           <span class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill">{{ count($data) }} Total Records</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="example">
                <thead class="bg-black-soft text-secondary small text-uppercase">
                    <tr>
                        <th class="border-0 px-4 py-3 d-none d-lg-table-cell">Rank</th>
                        <th class="border-0 py-3">User & Identity</th>
                        <th class="border-0 py-3">Asset & Volume</th>
                        <th class="border-0 py-3">Status</th>
                        <th class="border-0 py-3 d-none d-lg-table-cell">Timestamp</th>
                        <th class="border-0 px-4 py-3 text-end">Execution</th>
                    </tr>
                </thead>
                <tbody class="text-white border-glass font-text">
                    @forelse ($data as $key => $value)
                        <tr class="border-glass">
                            <td class="px-4 text-secondary d-none d-lg-table-cell">DP-{{++$key}}</td>
                            <td>
                                <div class="font-weight-bold text-white mb-0">{{$value->name}}</div>
                                <div class="x-small text-secondary tracking-wider">{{$value->trx_id ?? 'NO_HASH'}}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-info-soft text-info me-2">{{$value->pay_currency}}</span>
                                    <span class="text-success font-weight-bold">${{number_format($value->amount, 2)}}</span>
                                </div>
                            </td>
                            <td>
                                @if($value->status =='success')
                                    <span class="badge bg-success text-white px-3 py-2" style="border-radius:8px; font-size:0.7rem; font-weight:800; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">AUTHORIZED</span>
                                @elseif($value->status =='pending')
                                    <span class="badge bg-warning text-dark px-3 py-2" style="border-radius:8px; font-size:0.7rem; font-weight:800; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">EVALUATING</span>
                                @else
                                    <span class="badge bg-danger text-white px-3 py-2" style="border-radius:8px; font-size:0.7rem; font-weight:800; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">REJECTED</span>
                                @endif
                            </td>
                            <td class="small text-secondary d-none d-lg-table-cell">{{$value->created_at->format('M d, Y H:i')}}</td>
                            <td class="px-4 text-end">
                                <div class="d-flex justify-content-end align-items-center gap-2">
                                    <a href="{{ route('admin.history.deposit.edit', $value->id) }}" class="btn glass-panel text-info py-1 px-2 border-0" style="padding: 0.25rem 0.5rem; font-size: 0.8rem; border-radius: 8px;" title="Full Edit & Recalculate">
                                        <i class="ri-edit-line"></i>
                                    </a>
                                    @if($value->status =='success' || $value->status == 'failed')
                                        <div class="text-{{ $value->status == 'success' ? 'success' : 'danger' }} small"><i class="ri-checkbox-circle-fill me-1"></i> Finalized</div>
                                    @else
                                        <div>
                                            <select class="action-select bg-dark-soft border-glass text-white px-3 py-1 small rounded-pill" did="{{$value->id}}" style="outline: none; cursor: pointer;">
                                                <option selected disabled>SELECT ACTION</option>
                                                <option value="Approved">AUTHORIZE</option>
                                                <option value="Reject">REJECT</option>
                                                <option value="Delete">DELETE</option>
                                            </select>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-secondary">
                                <i class="ri-inbox-archive-line h1 d-block mb-3 opacity-20"></i>
                                <span>No Capital Flows Detected in the Current Block</span>
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
    .table-hover tbody tr { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
    .table-hover tbody tr:hover { 
        background: rgba(255,255,255,0.04) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        position: relative;
        z-index: 10;
    }
    .x-small { font-size: 10px; }
</style>

<!-- Override Deposit Modal -->
<div class="modal fade" id="overrideDepositModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content glass-card-premium border-0" style="background: rgba(30, 30, 35, 0.95); box-shadow: 0 10px 40px rgba(0,0,0,0.5);">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title font-weight-bold text-white">Approve Deposit</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pt-2">
                <p class="text-secondary small mb-4">You can approve the requested amount, or override it by entering a custom USD amount below. This amount will be credited to the user's balance and trade history.</p>
                <div class="form-group mb-3">
                    <label class="text-secondary small font-weight-bold mb-2">Original Requested Amount</label>
                    <div class="form-control" id="modal-requested-amount" readonly style="background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.05); color: #fff;"></div>
                </div>
                <div class="form-group mb-4">
                    <label class="text-secondary small font-weight-bold mb-2">Override Amount (USD)</label>
                    <input type="number" id="modal-custom-amount" class="form-control text-white" placeholder="Leave empty to use original" step="0.01" style="background: rgba(0,0,0,0.4); border: 1px solid rgba(59, 130, 246, 0.5);">
                    <small class="text-info mt-1 d-block"><i class="ri-information-line"></i> If provided, this amount completely overrides the user's requested amount.</small>
                </div>
                <input type="hidden" id="modal-tx-id">
                <input type="hidden" id="modal-tx-action">
                <button type="button" class="btn btn-primary w-100 py-2 font-weight-bold" id="confirmDepositApprovalBtn">Confirm & Approve</button>
            </div>
        </div>
    </div>
</div>

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
            let id = $(this).attr('did');
            let action = $(this).val();
            let selectElement = $(this);
            
            if(!id || !action) return;

            if (action === 'Approved') {
                // Find the amount column in the table row
                let row = $(this).closest('tr');
                let amountText = row.find('td:nth-child(4)').text().trim(); // Amount is typically column 4
                
                $('#modal-tx-id').val(id);
                $('#modal-tx-action').val(action);
                $('#modal-requested-amount').text(amountText);
                $('#modal-custom-amount').val(''); // Reset
                
                $('#overrideDepositModal').modal('show');
                selectElement.val('SELECT ACTION'); // Reset dropdown visually while modal is open
            } else {
                // Reject or other actions
                if(confirm(`Are you sure you want to ${action} this transaction?`)) {
                    processDepositAction(id, action, null);
                } else {
                    selectElement.val('SELECT ACTION');
                }
            }
        });

        $('#confirmDepositApprovalBtn').click(function() {
            let id = $('#modal-tx-id').val();
            let action = $('#modal-tx-action').val();
            let customAmount = $('#modal-custom-amount').val();
            
            $('#confirmDepositApprovalBtn').prop('disabled', true).html('<i class="spinner-border spinner-border-sm"></i> Processing...');
            processDepositAction(id, action, customAmount);
        });

        function processDepositAction(id, action, customAmount) {
            let payload = {
                _token: "{{ csrf_token() }}",
                id: id,
                action: action
            };
            if (customAmount && customAmount.trim() !== '') {
                payload.custom_amount = customAmount;
            }

            $.post("{{route('updatedeposit')}}", payload, function(data) {
                $('#overrideDepositModal').modal('hide');
                if(data.status) toastr.success(data.status, "Ledger Updated");
                setTimeout(() => location.reload(), 800);
            }).fail(function() {
                $('#overrideDepositModal').modal('hide');
                $('#confirmDepositApprovalBtn').prop('disabled', false).text('Confirm & Approve');
                toastr.error("Sync process failure", "Error");
            });
        }
        
        // Anime.js Entrance Animation matches TradeHistoryTable
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



