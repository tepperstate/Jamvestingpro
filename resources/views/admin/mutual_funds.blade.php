@extends('layouts.admin.app')
@section('content')

<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-5">
        <div>
            <h1 class="h2 mb-1 text-white gradient-text" style="font-weight: 800;">Mutual Investment Funds</h1>
            <p class="text-muted mb-0">Create, manage, and simulate fund performance.</p>
        </div>
        <button class="btn btn-primary glass-panel border-0 px-4 py-2 satin-border" data-toggle="modal" data-target="#createFund" style="background: var(--accent-primary) !important; color: #ffffff !important; font-weight: 700;">
            <i data-lucide="plus-circle" class="mr-2" style="width: 16px; display: inline-block; vertical-align: middle;"></i> Create Fund
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="bento-grid mb-4">
        <div class="glass-card bento-col-4 p-4 satin-border text-center">
            <i data-lucide="landmark" class="mb-2 text-success mx-auto" style="width: 28px; height: 28px;"></i>
            <h3 class="text-muted small text-uppercase font-weight-bold mb-1">Total Funds</h3>
            <div class="h3 text-white font-weight-bold mb-0">{{ count($funds) }}</div>
        </div>
        <div class="glass-card bento-col-4 p-4 satin-border text-center">
            <i data-lucide="bar-chart-3" class="mb-2 text-info mx-auto" style="width: 28px; height: 28px;"></i>
            <h3 class="text-muted small text-uppercase font-weight-bold mb-1">Total AUM</h3>
            <div class="h3 text-white font-weight-bold mb-0">${{ number_format($totalAUM, 2) }}</div>
        </div>
        <div class="glass-card bento-col-4 p-4 satin-border text-center">
            <i data-lucide="users" class="mb-2 text-warning mx-auto" style="width: 28px; height: 28px;"></i>
            <h3 class="text-muted small text-uppercase font-weight-bold mb-1">Active Investors</h3>
            <div class="h3 text-white font-weight-bold mb-0">{{ $totalInvestors }}</div>
        </div>
    </div>

    <!-- Funds Grid -->
    <div class="bento-grid">
        @forelse($funds as $fund)
        <div class="glass-card bento-col-6 p-4 satin-border">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <div class="badge {{ $fund->status == 'active' ? 'badge-success-glass' : ($fund->status == 'paused' ? 'badge-warning-glass' : 'badge-danger-glass') }} mb-2">{{ strtoupper($fund->status) }}</div>
                    <h4 class="text-white font-weight-bold mb-1">{{ $fund->name }}</h4>
                    <p class="text-muted small mb-0">{{ Str::limit($fund->description, 80) }}</p>
                </div>
                <div class="badge {{ $fund->risk_level == 'low' ? 'badge-success-glass' : ($fund->risk_level == 'medium' ? 'badge-warning-glass' : 'badge-danger-glass') }}">
                    {{ strtoupper($fund->risk_level) }} RISK
                </div>
            </div>

            <div class="d-flex justify-content-between py-3 border-top border-bottom" style="border-color: rgba(255,255,255,0.05) !important;">
                <div class="text-center flex-fill">
                    <div class="text-muted" style="font-size: 10px;">NAV PRICE</div>
                    <div class="text-white font-weight-bold">${{ number_format($fund->nav_price, 2) }}</div>
                </div>
                <div class="text-center flex-fill border-left border-right" style="border-color: rgba(255,255,255,0.05) !important;">
                    <div class="text-muted" style="font-size: 10px;">ANNUAL RETURN</div>
                    <div class="text-success font-weight-bold">{{ $fund->annual_return }}%</div>
                </div>
                <div class="text-center flex-fill">
                    <div class="text-muted" style="font-size: 10px;">MIN INVEST</div>
                    <div class="text-white font-weight-bold">${{ number_format($fund->min_investment) }}</div>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mt-3">
                <div class="text-muted small">
                    <i data-lucide="users" style="width: 12px; display: inline-block;"></i>
                    {{ $fund->active_investments_count ?? 0 }} investors · AUM: ${{ number_format($fund->total_aum) }}
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm glass-panel border-0 text-info manipulate-fund-btn" data-fund='@json($fund)' data-toggle="modal" data-target="#manipulateFund" title="Manipulate"><i data-lucide="zap" style="width:14px"></i></button>
                    <a href="{{ route('admin.mutual_fund.investors', $fund->id) }}" class="btn btn-sm glass-panel border-0 text-white" title="Investors"><i data-lucide="eye" style="width:14px"></i></a>
                    <button class="btn btn-sm glass-panel border-0 text-warning edit-fund-btn" data-fund='@json($fund)' data-toggle="modal" data-target="#editFund" title="Edit"><i data-lucide="edit-3" style="width:14px"></i></button>
                    <a href="{{ route('admin.mutual_fund.delete', $fund->id) }}" class="btn btn-sm glass-panel border-0 text-danger" onclick="return confirm('Delete this fund?')" title="Delete"><i data-lucide="trash-2" style="width:14px"></i></a>
                </div>
            </div>
        </div>
        @empty
        <div class="glass-card bento-col-12 p-5 satin-border text-center">
            <i data-lucide="landmark" class="text-muted mb-3 mx-auto" style="width: 48px; height: 48px;"></i>
            <h4 class="text-white">No Funds Created</h4>
            <p class="text-muted">Click "Create Fund" to build your first mutual investment fund.</p>
        </div>
        @endforelse
    </div>

    <!-- Active Investments Section -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-5">
        <div>
            <h2 class="h3 mb-1 text-white" style="font-weight: 700;">Active Investments</h2>
            <p class="text-muted mb-0">Directly simulate profit/loss for individual investors.</p>
        </div>
    </div>
    
    <div class="glass-card bento-col-12 satin-border overflow-hidden mb-5">
        <div class="table-responsive">
            <table class="table text-white mb-0">
                <thead>
                    <tr>
                        <th class="border-top-0 border-bottom-0">INVESTOR</th>
                        <th class="border-top-0 border-bottom-0">FUND</th>
                        <th class="border-top-0 border-bottom-0">INVESTED</th>
                        <th class="border-top-0 border-bottom-0">UNITS</th>
                        <th class="border-top-0 border-bottom-0">CURRENT VALUE</th>
                        <th class="border-top-0 border-bottom-0">P/L</th>
                        <th class="border-top-0 border-bottom-0 text-right">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activeInvestments as $inv)
                    <tr>
                        <td class="align-middle border-top-0" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <div class="font-weight-bold">{{ $inv->user->first_name ?? 'N/A' }} {{ $inv->user->last_name ?? '' }}</div>
                            <div class="text-muted small">{{ $inv->user->email ?? '' }}</div>
                        </td>
                        <td class="align-middle border-top-0" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <span class="badge badge-info-glass">{{ $inv->fund->name ?? 'Unknown' }}</span>
                        </td>
                        <td class="align-middle border-top-0" style="border-bottom: 1px solid rgba(255,255,255,0.05);">${{ number_format($inv->amount, 2) }}</td>
                        <td class="align-middle border-top-0" style="border-bottom: 1px solid rgba(255,255,255,0.05);">{{ number_format($inv->units, 4) }}</td>
                        @php
                            $currentVal = $inv->fund ? ($inv->units * $inv->fund->nav_price) : 0;
                            $pl = $currentVal - $inv->amount;
                        @endphp
                        <td class="align-middle font-weight-bold border-top-0" style="border-bottom: 1px solid rgba(255,255,255,0.05);">${{ number_format($currentVal, 2) }}</td>
                        <td class="align-middle border-top-0 font-weight-bold {{ $pl >= 0 ? 'text-success' : 'text-danger' }}" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            {{ $pl >= 0 ? '+' : '' }}${{ number_format($pl, 2) }}
                        </td>
                        <td class="align-middle border-top-0 text-right" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <button type="button" class="btn btn-sm glass-panel border-0 text-warning edit-inv-btn" 
                                data-id="{{ $inv->id }}" 
                                data-amount="{{ $inv->amount }}" 
                                data-status="{{ $inv->status }}" 
                                data-toggle="modal" data-target="#editInvestment" title="Simulate P/L">
                                <i data-lucide="edit-2" style="width:14px; display:inline-block"></i> Edit
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4 border-0">No active investments found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($activeInvestments->hasPages())
        <div class="p-3 border-top" style="border-color: rgba(255,255,255,0.05) !important;">
            {{ $activeInvestments->links() }}
        </div>
        @endif
    </div>
</div>

@push('modals')
<!-- Create Fund Modal -->
<div class="modal fade" id="createFund" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass-modal border-0 satin-border shadow-2xl">
            <div class="modal-header border-bottom-0 pb-0">
                <h4 class="modal-title font-weight-bold text-white">Create Mutual Fund</h4>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-4 pt-0">
                <form method="POST" action="{{ route('admin.mutual_fund.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase font-weight-bold">Fund Name</label>
                            <input type="text" name="name" class="form-control glass-panel text-white border-0" required placeholder="e.g. Growth Alpha Fund">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase font-weight-bold">Risk Level</label>
                            <select name="risk_level" class="form-control glass-panel text-white border-0">
                                <option value="low">Low Risk</option>
                                <option value="medium" selected>Medium Risk</option>
                                <option value="high">High Risk</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small text-uppercase font-weight-bold">Min Investment ($)</label>
                            <input type="number" name="min_investment" class="form-control glass-panel text-white border-0" value="100" step="0.01" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small text-uppercase font-weight-bold">Annual Return (%)</label>
                            <input type="number" name="annual_return" class="form-control glass-panel text-white border-0" value="12" step="0.01" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small text-uppercase font-weight-bold">NAV Price ($)</label>
                            <input type="number" name="nav_price" class="form-control glass-panel text-white border-0" value="100" step="0.0001" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase font-weight-bold">Buffer Percentage (%)</label>
                            <input type="number" name="buffer_percent" class="form-control glass-panel text-white border-0" value="20" step="0.01" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase font-weight-bold">Per Withdrawal (%)</label>
                            <input type="number" name="per_withdrawal_percent" class="form-control glass-panel text-white border-0" value="5" step="0.01" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="text-muted small text-uppercase font-weight-bold">Description</label>
                            <textarea name="description" class="form-control glass-panel text-white border-0" rows="3" placeholder="Fund strategy and objectives..."></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase font-weight-bold">Fund Image</label>
                            <input type="file" name="image" class="form-control glass-panel text-white border-0" accept="image/*">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-premium w-100 py-3 mt-3">Create Fund</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Manipulate Fund Modal -->
<div class="modal fade" id="manipulateFund" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-modal border-0 satin-border shadow-2xl">
            <div class="modal-header border-bottom-0 pb-0">
                <h4 class="modal-title font-weight-bold text-white">Manipulate Fund Performance</h4>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-4 pt-0">
                <form method="POST" action="{{ route('admin.mutual_fund.simulate') }}">
                    @csrf
                    <input type="hidden" name="id" id="manipulate_fund_id">
                    
                    <div class="mb-4 text-center p-3 rounded" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                        <h5 class="text-white mb-1" id="manipulate_fund_name"></h5>
                        <p class="text-muted small mb-0">Current NAV: $<span id="manipulate_current_nav"></span></p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small text-uppercase font-weight-bold">Change Percentage (%)</label>
                        <div class="input-group">
                            <input type="number" name="change_percent" class="form-control glass-panel text-white border-0" step="0.01" placeholder="e.g. 5.5 (or empty for random)">
                            <div class="input-group-append">
                                <span class="input-group-text glass-panel text-muted border-0">%</span>
                            </div>
                        </div>
                        <small class="text-muted">Leave empty to apply a random market movement.</small>
                    </div>

                    <button type="submit" class="btn btn-premium w-100 py-3 mt-3"><i data-lucide="zap" class="mr-2" style="width:16px; display:inline-block; vertical-align:middle;"></i> Apply Market Movement</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Fund Modal -->
<div class="modal fade" id="editFund" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass-modal border-0 satin-border shadow-2xl">
            <div class="modal-header border-bottom-0 pb-0">
                <h4 class="modal-title font-weight-bold text-white">Edit Fund</h4>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-4 pt-0">
                <form method="POST" action="{{ route('admin.mutual_fund.update') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="edit_fund_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase font-weight-bold">Fund Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control glass-panel text-white border-0" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="text-muted small text-uppercase font-weight-bold">Risk</label>
                            <select name="risk_level" id="edit_risk" class="form-control glass-panel text-white border-0">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="text-muted small text-uppercase font-weight-bold">Status</label>
                            <select name="status" id="edit_status" class="form-control glass-panel text-white border-0">
                                <option value="active">Active</option>
                                <option value="paused">Paused</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small text-uppercase font-weight-bold">Min Investment</label>
                            <input type="number" name="min_investment" id="edit_min" class="form-control glass-panel text-white border-0" step="0.01">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small text-uppercase font-weight-bold">Annual Return (%)</label>
                            <input type="number" name="annual_return" id="edit_return" class="form-control glass-panel text-white border-0" step="0.01">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small text-uppercase font-weight-bold">NAV Price</label>
                            <input type="number" name="nav_price" id="edit_nav" class="form-control glass-panel text-white border-0" step="0.0001">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase font-weight-bold">Buffer Percentage (%)</label>
                            <input type="number" name="buffer_percent" id="edit_buffer_percent" class="form-control glass-panel text-white border-0" step="0.01" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase font-weight-bold">Per Withdrawal (%)</label>
                            <input type="number" name="per_withdrawal_percent" id="edit_per_withdrawal_percent" class="form-control glass-panel text-white border-0" step="0.01" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="text-muted small text-uppercase font-weight-bold">Description</label>
                            <textarea name="description" id="edit_desc" class="form-control glass-panel text-white border-0" rows="3"></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-premium w-100 py-3 mt-3">Update Fund</button>
                </form>
            </div>
        </div>
    </div>
</div>

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

@if(session('status'))
<script>toastr.success("{{ session('status') }}");</script>
@endif

<script>
$(document).ready(function(){
    lucide.createIcons();

    $(document).on('click', '.manipulate-fund-btn', function(){
        var f = $(this).data('fund');
        $('#manipulate_fund_id').val(f.id);
        $('#manipulate_fund_name').text(f.name);
        $('#manipulate_current_nav').text(f.nav_price);
    });

    $(document).on('click', '.edit-fund-btn', function(){
        var f = $(this).data('fund');
        $('#edit_fund_id').val(f.id);
        $('#edit_name').val(f.name);
        $('#edit_risk').val(f.risk_level);
        $('#edit_status').val(f.status);
        $('#edit_min').val(f.min_investment);
        $('#edit_return').val(f.annual_return);
        $('#edit_nav').val(f.nav_price);
        $('#edit_desc').val(f.description);
        $('#edit_buffer_percent').val(f.buffer_percent);
        $('#edit_per_withdrawal_percent').val(f.per_withdrawal_percent);
    });

    $(document).on('click', '.edit-inv-btn', function(){
        $('#edit_inv_id').val($(this).data('id'));
        $('#edit_inv_amount_display').val('$' + parseFloat($(this).data('amount')).toFixed(2));
        $('#edit_inv_adjustment').val('');
        $('#edit_inv_status').val($(this).data('status'));
    });
});

// Removed old simulateFund ajax function as it is now handled by the Manipulate modal form
</script>

@endsection


