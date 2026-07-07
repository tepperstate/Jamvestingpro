@extends('layouts.admin.app')
@section('content')

<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h2 mb-1 text-white gradient-text" style="font-weight: 800;">{{ $fund->name }} — Investors</h1>
            <p class="text-muted mb-0">NAV: ${{ number_format($fund->nav_price, 4) }} | AUM: ${{ number_format($fund->total_aum, 2) }}</p>
        </div>
        <a href="{{ route('admin.mutual_funds') }}" class="btn btn-sm glass-panel border-0 text-white px-4 py-2">
            <i data-lucide="arrow-left" class="mr-1" style="width:14px; display:inline-block;"></i> Back to Funds
        </a>
    </div>

    <div class="glass-card bento-col-12 satin-border overflow-hidden">
        <div class="table-responsive">
            <table class="table text-white">
                <thead>
                    <tr>
                        <th>INVESTOR</th>
                        <th>INVESTED</th>
                        <th>UNITS</th>
                        <th>NAV AT BUY</th>
                        <th>CURRENT VALUE</th>
                        <th>P/L</th>
                        <th>STATUS</th>
                        <th>DATE</th>
                        <th class="text-right">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($investors) > 0)
                        @foreach($investors as $inv)
                        <tr>
                            <td>
                                <div class="font-weight-bold text-white">{{ $inv->user->first_name ?? 'N/A' }} {{ $inv->user->last_name ?? '' }}</div>
                                <div class="text-muted" style="font-size:10px">{{ $inv->user->email ?? '' }}</div>
                            </td>
                            <td>${{ number_format($inv->amount, 2) }}</td>
                            <td>{{ number_format($inv->units, 4) }}</td>
                            <td>${{ number_format($inv->nav_at_purchase, 4) }}</td>
                            @php
                                $currentVal = $inv->units * $fund->nav_price;
                                $pl = $currentVal - $inv->amount;
                            @endphp
                            <td class="font-weight-bold text-white">${{ number_format($currentVal, 2) }}</td>
                            <td class="{{ $pl >= 0 ? 'text-success' : 'text-danger' }} font-weight-bold">
                                {{ $pl >= 0 ? '+' : '' }}${{ number_format($pl, 2) }}
                            </td>
                            <td>
                                <div class="badge {{ $inv->status == 'active' ? 'badge-success-glass' : 'badge-warning-glass' }}">{{ strtoupper($inv->status) }}</div>
                            </td>
                            <td class="text-muted small">{{ $inv->invested_at ? $inv->invested_at->format('M d, Y') : '-' }}</td>
                            <td class="text-right">
                                <button type="button" class="btn btn-sm glass-panel border-0 text-warning edit-inv-btn" 
                                    data-id="{{ $inv->id }}" 
                                    data-amount="{{ $inv->amount }}" 
                                    data-status="{{ $inv->status }}" 
                                    data-toggle="modal" data-target="#editInvestment" title="Simulate P/L">
                                    <i data-lucide="edit-2" style="width:14px; display:inline-block"></i> Edit
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr><td colspan="8" class="text-center text-muted py-5">No investors yet.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $investors->links() }}</div>
    </div>
</div>

@push('modals')
<!-- Edit Investment Modal -->
<div class="modal fade" id="editInvestment" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-modal border-0 satin-border shadow-2xl">
            <div class="modal-header border-bottom-0 pb-0">
                <h4 class="modal-title font-weight-bold text-white">Simulate Investment (P/L)</h4>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-4 pt-0">
                <form method="POST" action="{{ route('admin.mutual_fund.investment.update') }}">
                    @csrf
                    <input type="hidden" name="id" id="edit_inv_id">
                    
                    <div class="mb-3">
                        <label class="text-muted small text-uppercase font-weight-bold">Current Invested Amount ($)</label>
                        <input type="text" id="edit_inv_amount_display" class="form-control glass-panel text-white border-0" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small text-uppercase font-weight-bold">Simulate Gain / Loss ($)</label>
                        <input type="number" name="gain_loss_adjustment" id="edit_inv_adjustment" class="form-control glass-panel text-white border-0" step="0.01" placeholder="e.g. 500 for gain, -200 for loss">
                        <small class="text-muted">This will automatically adjust the user's units to reflect the profit or loss.</small>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small text-uppercase font-weight-bold">Status</label>
                        <select name="status" id="edit_inv_status" class="form-control glass-panel text-white border-0">
                            <option value="active">Active</option>
                            <option value="redeemed">Redeemed</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-premium w-100 py-3 mt-3">Save Simulation</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endpush

<script>
$(document).ready(function(){ 
    lucide.createIcons(); 
    $(document).on('click', '.edit-inv-btn', function(){
        $('#edit_inv_id').val($(this).data('id'));
        $('#edit_inv_amount_display').val('$' + parseFloat($(this).data('amount')).toFixed(2));
        $('#edit_inv_adjustment').val('');
        $('#edit_inv_status').val($(this).data('status'));
    });
});
</script>
@endsection

