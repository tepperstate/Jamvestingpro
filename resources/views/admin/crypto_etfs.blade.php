@extends('layouts.admin.app')
@section('title', 'Crypto ETFs Management')

@section('content')

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-5">
        <div>
            <h1 class="h2 mb-1 text-white gradient-text" style="font-weight: 800;">Crypto ETFs Oversight</h1>
            <p class="text-muted mb-0">Configure ETF portfolios and manipulate individual user ROI.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
             <div class="badge badge-info-glass px-4 py-2 border-0 satin-border" style="background: rgba(59, 130, 246, 0.05) !important;">
                <span class="text-white small font-weight-bold">ACTIVE ETF POSITIONS: {{ $investments->total() }}</span>
            </div>
        </div>
    </div>

    <!-- ETF Plans Configuration -->
    <div class="glass-card satin-border overflow-hidden shadow-2xl mb-5">
        <div class="p-4 d-flex justify-content-between align-items-center" style="background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--glass-border);">
            <div>
                <h3 class="h5 text-white mb-1 font-weight-bold">Global ETF Portfolios Configuration</h3>
                <p class="text-muted x-small mb-0">Adjust baseline ROI, duration, and minimum capital requirements.</p>
            </div>
            <div class="d-flex gap-2">
                <form action="{{ route('admin.crypto_etfs.auto_populate') }}" method="POST" class="d-inline" onsubmit="return confirm('Auto-populate real Crypto ETFs from the API provider?');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-info satin-border mr-2" style="font-weight: 600;">
                        <i data-lucide="download-cloud" style="width:16px; margin-right: 4px;"></i> Auto-Populate APIs
                    </button>
                </form>
                <form action="{{ route('admin.crypto_etfs.refresh_logos') }}" method="POST" class="d-inline" onsubmit="return confirm('Refresh all ETF logos from the API provider?');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-warning satin-border mr-2" style="font-weight: 600;">
                        <i data-lucide="refresh-cw" style="width:16px; margin-right: 4px;"></i> Refresh Logos
                    </button>
                </form>
                <button class="btn btn-sm btn-primary satin-border" data-toggle="modal" data-target="#createPlanModal" style="background: var(--accent-primary) !important; color: #ffffff !important; font-weight: 600;">
                    <i data-lucide="plus" style="width:16px; margin-right: 4px;"></i> Add Plan
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table text-white mb-0">
                <thead>
                    <tr>
                        <th class="border-0">ASSET</th>
                        <th class="border-0">PORTFOLIO TIER</th>
                        <th class="border-0">MIN CAPITAL</th>
                        <th class="border-0">ROI (%)</th>
                        <th class="border-0">DURATION (DAYS)</th>
                        <th class="border-0 text-right">MANIPULATION</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($plans as $plan)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $plan->logo }}" alt="{{ $plan->ticker }}" class="rounded-circle mr-2" style="width: 32px; height: 32px; object-fit: cover; background: white; padding: 2px;">
                                    <span class="badge badge-primary">{{ $plan->ticker ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="text-white font-weight-bold">{{ $plan->name }}</div>
                                <div class="text-muted x-small">PACKAGE ID: #{{ $plan->id }}</div>
                            </td>
                            <td>
                                <div class="text-white font-weight-bold">${{ number_format($plan->amount, 2) }}</div>
                                <div class="text-muted x-small">Auto-upgrade: ${{ number_format($plan->min_deposit ?? $plan->amount, 2) }}</div>
                            </td>
                            <td>
                                <div class="text-success small font-weight-bold">{{ $plan->perc }}% Total</div>
                            </td>
                            <td>
                                <div class="text-white small">{{ $plan->day }} Days</div>
                            </td>
                            <td class="text-right">
                                <button onclick="showEditPlanModal({{ $plan->id }}, '{{ addslashes($plan->name) }}', '{{ $plan->ticker }}', '{{ $plan->logo_url }}', {{ $plan->amount }}, {{ $plan->min_deposit ?? $plan->amount }}, {{ $plan->perc }}, {{ $plan->day }})" class="btn btn-sm glass-panel text-info border-0" title="Edit Parameters">
                                    <i data-lucide="edit-3" style="width:16px"></i>
                                </button>
                                <form action="{{ route('admin.crypto_etfs.plan.delete', $plan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this ETF plan?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm glass-panel text-danger border-0" title="Delete Plan">
                                        <i data-lucide="trash-2" style="width:16px"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- User Active ETF Portfolios -->
    <div class="glass-card satin-border overflow-hidden shadow-2xl">
        <div class="p-4 d-flex justify-content-between align-items-center" style="background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--glass-border);">
            <div>
                <h3 class="h5 text-white mb-1 font-weight-bold">Active User Portfolios</h3>
                <p class="text-muted x-small mb-0">Granular control over individual user growth outcomes.</p>
            </div>
        </div>
        <div class="table-responsive">
            <table id="investmentTable" class="table text-white mb-0">
                <thead>
                    <tr>
                        <th class="border-0">USER</th>
                        <th class="border-0">PORTFOLIO</th>
                        <th class="border-0">CAPITAL</th>
                        <th class="border-0">USER ROI (%)</th>
                        <th class="border-0">STATUS</th>
                        <th class="border-0">DATES</th>
                        <th class="border-0 text-right">MANIPULATION</th>
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
                            </td>
                            <td>
                                <div class="text-muted small font-weight-bold">Base: ${{ number_format($investment->amount, 2) }}</div>
                                <div class="text-success font-weight-bold" style="font-size: 1.1rem;">Now: ${{ number_format($investment->current_value ?? $investment->amount, 2) }}</div>
                                <div class="text-muted x-small">TARGET PROFIT: ${{ number_format(($investment->amount * ($investment->perc / 100)), 2) }}</div>
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
                                        <button onclick="updateInvestment({{ $investment->id }}, 'pause')" class="btn btn-sm glass-panel text-warning border-0" title="Pause Yield"><i data-lucide="pause-circle" style="width:16px"></i></button>
                                    @elseif($investment->status == 'paused')
                                        <button onclick="updateInvestment({{ $investment->id }}, 'resume')" class="btn btn-sm glass-panel text-success border-0" title="Resume Yield"><i data-lucide="play-circle" style="width:16px"></i></button>
                                    @endif
                                    
                                    <button onclick="showEditInvestmentModal({{ $investment->id }}, {{ $investment->perc }}, {{ $investment->day }})" class="btn btn-sm glass-panel text-info border-0" title="Edit Parameters"><i data-lucide="edit-3" style="width:16px"></i></button>
                                    
                                    @if($investment->status != 'completed')
                                        <button onclick="updateInvestment({{ $investment->id }}, 'force_complete')" class="btn btn-sm glass-panel text-white border-0" title="Force Success/Payout"><i data-lucide="check-circle-2" style="width:16px"></i></button>
                                    @endif
                                    
                                    <button onclick="updateInvestment({{ $investment->id }}, 'delete')" class="btn btn-sm glass-panel border-0 text-danger" title="Delete Portfolio Record"><i data-lucide="trash-2" style="width:16px"></i></button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-top border-glass" style="background: rgba(255,255,255,0.01);">
            {{ $investments->links() }}
        </div>
    </div>
</div>

<!-- Create Plan Modal -->
<div class="modal fade" id="createPlanModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content glass-card satin-border text-white">
            <div class="modal-header border-0">
                <h5 class="modal-title font-weight-bold">Create ETF Portfolio</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.crypto_etfs.plan.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="small text-muted mb-2">PORTFOLIO NAME</label>
                        <input type="text" name="name" class="form-control glass-panel text-white border-0 py-3 px-3" style="background: rgba(255,255,255,0.05);" required>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <label class="small text-muted mb-2">TICKER (e.g. IBIT)</label>
                                <input type="text" name="ticker" class="form-control glass-panel text-white border-0 py-3 px-3" style="background: rgba(255,255,255,0.05);">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <label class="small text-muted mb-2">LOGO URL (Optional)</label>
                                <input type="text" name="logo_url" class="form-control glass-panel text-white border-0 py-3 px-3" style="background: rgba(255,255,255,0.05);" placeholder="Auto-resolved if empty">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <label class="small text-muted mb-2">MIN CAPITAL ($)</label>
                                <input type="number" step="0.01" name="amount" class="form-control glass-panel text-white border-0 py-3 px-3" style="background: rgba(255,255,255,0.05);" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <label class="small text-muted mb-2">AUTO-UPGRADE ($)</label>
                                <input type="number" step="0.01" name="min_deposit" class="form-control glass-panel text-white border-0 py-3 px-3" style="background: rgba(255,255,255,0.05);">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <label class="small text-muted mb-2">ROI / RETURN (%)</label>
                                <input type="number" step="0.01" name="perc" class="form-control glass-panel text-white border-0 py-3 px-3" style="background: rgba(255,255,255,0.05);" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <label class="small text-muted mb-2">DURATION (DAYS)</label>
                                <input type="number" name="day" class="form-control glass-panel text-white border-0 py-3 px-3" style="background: rgba(255,255,255,0.05);" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-link text-muted" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 py-2 satin-border" style="background: var(--accent-primary) !important; color: #ffffff !important; font-weight: 700;">Create Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Plan Modal -->
<div class="modal fade" id="editPlanModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content glass-card satin-border text-white">
            <div class="modal-header border-0">
                <h5 class="modal-title font-weight-bold">Configure ETF Portfolio</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="plan_id">
                <div class="form-group mb-3">
                    <label class="small text-muted mb-2">PORTFOLIO NAME</label>
                    <input type="text" id="plan_name" class="form-control glass-panel text-white border-0 py-3 px-3" style="background: rgba(255,255,255,0.05);">
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group mb-3">
                            <label class="small text-muted mb-2">TICKER</label>
                            <input type="text" id="plan_ticker" class="form-control glass-panel text-white border-0 py-3 px-3" style="background: rgba(255,255,255,0.05);">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group mb-3">
                            <label class="small text-muted mb-2">LOGO URL</label>
                            <input type="text" id="plan_logo_url" class="form-control glass-panel text-white border-0 py-3 px-3" style="background: rgba(255,255,255,0.05);">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group mb-3">
                            <label class="small text-muted mb-2">MIN CAPITAL ($)</label>
                            <input type="number" step="0.01" id="plan_amount" class="form-control glass-panel text-white border-0 py-3 px-3" style="background: rgba(255,255,255,0.05);">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group mb-3">
                            <label class="small text-muted mb-2">AUTO-UPGRADE ($)</label>
                            <input type="number" step="0.01" id="plan_min_deposit" class="form-control glass-panel text-white border-0 py-3 px-3" style="background: rgba(255,255,255,0.05);">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group mb-3">
                            <label class="small text-muted mb-2">ROI / RETURN (%)</label>
                            <input type="number" step="0.01" id="plan_perc" class="form-control glass-panel text-white border-0 py-3 px-3" style="background: rgba(255,255,255,0.05);">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group mb-3">
                            <label class="small text-muted mb-2">DURATION (DAYS)</label>
                            <input type="number" id="plan_day" class="form-control glass-panel text-white border-0 py-3 px-3" style="background: rgba(255,255,255,0.05);">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-link text-muted" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="savePlan()" class="btn btn-primary px-4 py-2 satin-border" style="background: var(--accent-primary) !important; color: #ffffff !important; font-weight: 700;">Save Global Plan</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Investment Rigging Modal -->
<div class="modal fade" id="editInvestmentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content glass-card satin-border text-white">
            <div class="modal-header border-0">
                <h5 class="modal-title font-weight-bold">Rig Individual Yield</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="inv_id">
                <div class="form-group mb-4">
                    <label class="small text-muted mb-2">TARGET TOTAL RETURN (%)</label>
                    <input type="number" step="0.01" id="inv_perc" class="form-control glass-panel text-white border-0 py-4 px-3" style="background: rgba(255,255,255,0.05);">
                    <p class="x-small text-info mt-1">This will change the final payout amount for this specific user's ETF.</p>
                </div>
                <div class="form-group mb-4">
                    <label class="small text-muted mb-2">REMAINING DAYS / DURATION</label>
                    <input type="number" id="inv_day" class="form-control glass-panel text-white border-0 py-4 px-3" style="background: rgba(255,255,255,0.05);">
                    <p class="x-small text-warning mt-1">Changing this adjusts the maturation date.</p>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-link text-muted" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="saveInvestmentRig()" class="btn btn-primary px-4 py-2 satin-border" style="background: var(--accent-primary) !important; color: #ffffff !important; font-weight: 700;">Apply Rigging</button>
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
    // Plan Configuration Modal
    function showEditPlanModal(id, name, ticker, logo_url, amount, min_deposit, perc, day) {
        $('#plan_id').val(id);
        $('#plan_name').val(name);
        $('#plan_ticker').val(ticker);
        $('#plan_logo_url').val(logo_url);
        $('#plan_amount').val(amount);
        $('#plan_min_deposit').val(min_deposit);
        $('#plan_perc').val(perc);
        $('#plan_day').val(day);
        $('#editPlanModal').modal('show');
    }

    function savePlan() {
        const id = $('#plan_id').val();
        
        $.ajax({
            url: "{{ route('admin.crypto_etfs.plan.update') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: id,
                name: $('#plan_name').val(),
                ticker: $('#plan_ticker').val(),
                logo_url: $('#plan_logo_url').val(),
                amount: $('#plan_amount').val(),
                min_deposit: $('#plan_min_deposit').val(),
                perc: $('#plan_perc').val(),
                day: $('#plan_day').val()
            },
            success: function(res) {
                toastr.success(res.status);
                $('#editPlanModal').modal('hide');
                setTimeout(() => window.location.reload(), 1000);
            },
            error: function(err) {
                toastr.error("An error occurred");
            }
        });
    }

    // Individual Investment Manipulation Modal & Actions
    function updateInvestment(id, action) {
        if (action === 'delete' && !confirm('Are you sure you want to delete this ETF record?')) return;
        
        $.ajax({
            url: "{{ route('admin.crypto_etfs.investment.update') }}",
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
                    setTimeout(() => window.location.reload(), 1000);
                }
            },
            error: function(err) {
                toastr.error(err.responseJSON.error || "An error occurred");
            }
        });
    }

    function showEditInvestmentModal(id, perc, day) {
        $('#inv_id').val(id);
        $('#inv_perc').val(perc);
        $('#inv_day').val(day);
        $('#editInvestmentModal').modal('show');
    }

    function saveInvestmentRig() {
        const id = $('#inv_id').val();
        const perc = $('#inv_perc').val();
        const day = $('#inv_day').val();

        $.ajax({
            url: "{{ route('admin.crypto_etfs.investment.update') }}",
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
                $('#editInvestmentModal').modal('hide');
                setTimeout(() => window.location.reload(), 1000);
            }
        });
    }

    $(document).ready(function(){
        if(typeof lucide !== 'undefined') { lucide.createIcons(); }
        if (typeof anime !== 'undefined') {
            anime({
                targets: 'table tbody tr',
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

