@extends('layouts.admin.app')
@section('content')

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-5">
        <div>
            <h1 class="h2 mb-1 text-white gradient-text" style="font-weight: 800;">Trade Monitoring</h1>
            <p class="text-muted mb-0">Synchronized oversight of all live and finalized trade accounts across the grid.</p>
        </div>
        <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-3 w-100-mobile">
             <div class="glass-panel px-3 px-md-4 py-2 satin-border rounded-pill w-100-mobile text-center text-sm-left">
                <span class="text-white small font-weight-bold"><i data-lucide="activity" class="mr-2" style="width:14px; display:inline-block; vertical-align:middle;"></i> MONITOR ACTIVE</span>
            </div>
            <button onclick="window.location.reload()" class="btn btn-primary glass-panel border-0 px-4 py-2 satin-border w-100-mobile" style="background: var(--accent-primary) !important; color: #ffffff !important; font-weight: 700;">
                <i data-lucide="refresh-cw" class="mr-2" style="width: 16px; display: inline-block; vertical-align: middle;"></i> Sync Grid
            </button>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <ul class="nav nav-pills mb-4 gap-2" id="tradeTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link {{ Route::is('all_trade') ? 'active text-dark font-weight-bold shadow-sm' : 'glass-panel text-white opacity-75' }}" style="background: {{ Route::is('all_trade') ? 'var(--accent-primary)' : '' }}; border-radius: 6px; padding: 0.6rem 1.2rem;" href="{{ route('all_trade') }}">
                <i data-lucide="activity" class="mr-2" style="width:16px;"></i> Manual Execution
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Route::is('bot_trades_index') ? 'active text-dark font-weight-bold shadow-sm' : 'glass-panel text-white opacity-75' }}" style="background: {{ Route::is('bot_trades_index') ? 'var(--accent-primary)' : '' }}; border-radius: 6px; padding: 0.6rem 1.2rem;" href="{{ route('bot_trades_index') }}">
                <i data-lucide="cpu" class="mr-2" style="width:16px;"></i> Bot Trades
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Route::is('copy_trades_index') ? 'active text-dark font-weight-bold shadow-sm' : 'glass-panel text-white opacity-75' }}" style="background: {{ Route::is('copy_trades_index') ? 'var(--accent-primary)' : '' }}; border-radius: 6px; padding: 0.6rem 1.2rem;" href="{{ route('copy_trades_index') }}">
                <i data-lucide="users" class="mr-2" style="width:16px;"></i> CopyTrader Logs
            </a>
        </li>
    </ul>

    <!-- Main Trade Grid -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow p-4 glass-panel border-0 text-white" style="background: rgba(255,255,255,0.02);">
                <h5 class="mb-3 font-weight-bold"><i data-lucide="settings" class="mr-2" style="width:18px;"></i> Binary Trades Auto-Approve Configuration</h5>
                <form action="{{ route('admin.settings.trading.auto-approve') }}" method="POST" class="d-flex align-items-center flex-wrap gap-3">
                    @csrf
                    <input type="hidden" name="trades_auto_approve_submit" value="1">
                    
                    <div class="form-check form-switch me-4">
                        <input class="form-check-input" type="checkbox" id="trades_auto_approve" name="trades_auto_approve" {{ site()->trades_auto_approve ? 'checked' : '' }}>
                        <label class="form-check-label ms-2" for="trades_auto_approve">Enable Auto-Approval</label>
                    </div>

                    <div class="input-group" style="max-width: 250px;">
                        <span class="input-group-text border-0 text-white" style="background: rgba(255,255,255,0.1);">Target Profit</span>
                        <input type="number" step="0.01" class="form-control border-0 text-white" style="background: rgba(255,255,255,0.05);" name="trades_auto_win_percent" value="{{ site()->trades_auto_win_percent }}" placeholder="e.g. 20.00">
                        <span class="input-group-text border-0 text-white" style="background: rgba(255,255,255,0.1);">%</span>
                    </div>

                    <button type="submit" class="btn btn-primary glass-panel border-0 px-4" style="background: var(--accent-primary) !important; color: #ffffff !important; font-weight: 700;">Save Configuration</button>
                </form>
            </div>
        </div>
    </div>

    <div class="glass-card satin-border overflow-hidden shadow-2xl">
        <div class="p-4 d-flex justify-content-between align-items-center" style="background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--glass-border);">
           <div>
               <h3 class="h5 text-white mb-1 font-weight-bold">Execution Stack</h3>
               <p class="text-muted x-small mb-0">Real-time telemetry from architectural trading modules.</p>
           </div>
           <div id="stat-badges" class="d-flex gap-2">
                <button class="btn btn-xs glass-panel text-white border-0 px-3" onclick="exportTrades()">
                    <i data-lucide="download" class="mr-1" style="width:12px; display:inline-block; vertical-align:middle;"></i> Export ALL
                </button>
                <!-- Badges populated by JS -->
           </div>
        </div>
        <div id="source_wallets" class="table-responsive">
            <table class='table text-white mb-0' id="example">
                <thead>
                    <tr>
                        <th class="d-none d-lg-table-cell">RANK</th>
                        <th class="d-none d-xl-table-cell">HASH / ID</th>
                        <th>USER</th>
                        <th>ASSET</th>
                        <th>VOLUME</th>
                        <th class="d-none d-md-table-cell">RATE</th>
                        <th>STATUS</th>
                        <th class="d-none d-lg-table-cell">DATE</th>
                        <th class="text-right">OPS</th>
                    </tr>
                </thead>
                <tbody id="get">
                    <!-- Data injected via JS -->
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="spinner-border text-primary opacity-50" role="status"></div>
                            <p class="text-muted mt-3 x-small">Loading trades...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('modals')
<!-- Edit Result Modal -->
<div class="modal fade" id="update" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document"> 
        <div class="modal-content glass-modal satin-border shadow-2xl overflow-hidden" style="background: var(--bg-main) !important; border: 1px solid var(--glass-border) !important;">
            <div class="modal-header border-0 p-4" style="background: rgba(255,255,255,0.02);">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-box glass-panel p-2" style="background: rgba(139, 92, 246, 0.1) !important; border-radius: 6px;">
                        <i data-lucide="binary" class="text-primary" style="width: 20px; height: 20px;"></i>
                    </div>
                    <h4 class="modal-title font-weight-bold text-white mb-0">Outcome Injection</h4>
                </div>
                <button type="button" class="close text-white opacity-50 hover-opacity-100" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <form id="updatesss">
                    <div class="form-group mb-4">
                        <label class="text-secondary small font-weight-bold text-uppercase mb-2 d-block">Target Status</label>
                        <div class="d-flex gap-2">
                            <select class="form-control glass-panel border-0 text-white px-3 py-2" id="data" style="height: auto; border-radius: 6px;" required>
                                <option value="" disabled selected>Select Outcome...</option>
                                <option value="win">SYNERGY (WIN)</option>
                                <option value="loss">ENTROPY (LOSS)</option>
                                <option value="draw">NEUTRAL (DRAW/REFUND)</option>
                            </select>
                        </div>
                        <small class="text-muted mt-2 d-block">Setting a manual result will adjust the automatic settlement outcome.</small>
                    </div>

                    <div class="form-group mb-4">
                        <label class="text-secondary small font-weight-bold text-uppercase mb-2 d-block">Manual Strike Price</label>
                        <input type="text" class="form-control glass-panel border-0 text-white px-3 py-2" id="strike_rate_input" style="border-radius: 6px;" placeholder="Enter price level...">
                    </div>
                    <input type="hidden" id="okays">

                    <button type="submit" class="btn btn-premium w-100 py-3 font-weight-bold" style="border-radius: 6px;">
                        <i data-lucide="zap" class="mr-2" style="width: 18px; display: inline-block; vertical-align: middle;"></i> Execute Injection
                    </button>
                </form>
            </div>  
        </div>
    </div>
</div>
@endpush

@push('modals')
<!-- Override Modal -->
<div class="modal fade" id="overrideModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document"> 
        <div class="modal-content glass-modal satin-border shadow-2xl overflow-hidden" style="background: var(--bg-main) !important; border: 1px solid var(--glass-border) !important;">
            <div class="modal-header border-0 p-4" style="background: rgba(255,255,255,0.02);">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-box glass-panel p-2" style="background: rgba(234, 179, 8, 0.1) !important; border-radius: 6px;">
                        <i data-lucide="zap" class="text-warning" style="width: 20px; height: 20px;"></i>
                    </div>
                    <h4 class="modal-title font-weight-bold text-white mb-0">Adjust Trade Settlement</h4>
                </div>
                <button type="button" class="close text-white opacity-50 hover-opacity-100" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <form id="overrideForm">
                    <div class="form-group mb-4">
                        <label class="text-secondary small font-weight-bold text-uppercase mb-2 d-block">Settlement Adjustment</label>
                        <select class="form-control glass-panel border-0 text-white px-3 py-2" id="override_status" style="height: auto; border-radius: 6px;" required>
                            <option value="" disabled selected>Select Final Outcome...</option>
                            <option value="win">WIN (Profit)</option>
                            <option value="loss">LOSS (Deficit)</option>
                            <option value="draw">DRAW (Refund)</option>
                            <option value="auto">AUTO (Remove Adjustment)</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-4" id="earningPercentageWrapper">
                                <label class="text-secondary small font-weight-bold text-uppercase mb-2 d-block">Earning Percentage (%)</label>
                                <input type="number" step="0.01" min="-100" max="500" class="form-control glass-panel border-0 text-white px-3 py-2" id="override_percentage" style="border-radius: 6px;" placeholder="e.g. 50">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-4" id="overrideAmountWrapper">
                                <label class="text-secondary small font-weight-bold text-uppercase mb-2 d-block text-warning">OR Custom Amount ($)</label>
                                <input type="number" step="0.01" class="form-control glass-panel border-0 text-white px-3 py-2" id="override_amount" style="border-radius: 6px;" placeholder="e.g. 150.00">
                            </div>
                        </div>
                    </div>
                    <small class="text-muted mt-0 mb-4 d-block" id="calcPreview">Live P/L Calculation: $0.00</small>

                    <div class="form-group mb-4">
                        <label class="text-secondary small font-weight-bold text-uppercase mb-2 d-block">Reason / Audit Trail</label>
                        <textarea class="form-control glass-panel border-0 text-white px-3 py-2" id="override_reason" rows="3" style="border-radius: 6px;" placeholder="Required for compliance logging..." minlength="10" required></textarea>
                    </div>

                    <div class="alert alert-warning mb-4 py-2 px-3 small" style="background: rgba(234, 179, 8, 0.1); border: 1px solid rgba(234, 179, 8, 0.2); border-radius: 6px;">
                        <i data-lucide="alert-triangle" class="mr-1" style="width:14px; vertical-align:middle;"></i> This action immediately closes the trade and updates the user's balance.
                    </div>

                    <input type="hidden" id="override_trade_id">
                    <input type="hidden" id="override_trade_amount">

                    <button type="submit" class="btn btn-warning w-100 py-3 font-weight-bold text-dark" style="border-radius: 6px;">
                        <i data-lucide="lock" class="mr-2" style="width: 18px; display: inline-block; vertical-align: middle;"></i> Confirm Adjustment
                    </button>
                </form>
            </div>  
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
$(document).ready(function(){
    // Initial load
    trade();
    
    // Auto-update loop
    setInterval(function(){
        trade()
    }, 5000); // 5s interval to reduce overhead

    async function trade() {
        const options = {
            method: 'get',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        };
      
        fetch("{{route('all_trades')}}", options)
        .then((res) => res.json())
        .then((data) => {
            let tr = "";            
            data.forEach(function(val, index) {
                let createdAt = new Date(val.created_at);
                let formattedDate = createdAt.toLocaleDateString() + ' ' + createdAt.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                
                let statusBadge = val.status == 'win' ? '<div class="badge badge-win px-2 py-1">SYNERGY</div>' : 
                                 (val.status == 'loss' ? '<div class="badge badge-loss px-2 py-1">ENTROPY</div>' : 
                                 (val.status == 'draw' ? '<div class="badge badge-info px-2 py-1">NEUTRAL</div>' : 
                                 (val.approval_status == 'pending' ? '<div class="badge badge-warning px-2 py-1">PENDING APPROVAL</div>' : 
                                 '<div class="badge badge-pending px-2 py-1">CALCULATING</div>')));


                if (val.admin_status && val.status == 'pending') {
                    statusBadge += `<br><span class="x-small text-accent font-weight-bold" style="color:var(--accent-primary)">FORCED: ${val.admin_status.toUpperCase()}</span>`;
                } else if (val.outcome_preset && val.status == 'pending') {
                    statusBadge += `<br><span class="x-small text-info font-weight-bold">PRE-SET: ${val.outcome_preset.toUpperCase()}</span>`;
                }

                if (val.is_overridden) {
                    statusBadge += `<br><span class="x-small text-warning font-weight-bold"><i data-lucide="zap" style="width:10px"></i> OVERRIDDEN</span>`;
                }
                
                let typeBadge = val.type.toUpperCase() == 'CALL' ? '<span class="text-success font-weight-bold">↑ CALL</span>' : '<span class="text-danger font-weight-bold">↓ PUT</span>';

                tr += `
                   <tr> 
                      <td class="small font-weight-bold text-muted d-none d-lg-table-cell">EX-00${val.id}</td>
                      <td class="d-none d-xl-table-cell"><code class="text-white x-small opacity-75">${val.trade_id}</code></td>
                      <td><div class="font-weight-bold text-white small">${val.user.first_name} ${val.user.last_name.charAt(0)}.</div></td>
                      <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge glass-panel text-white border-0 x-small">${val.symbol}</span>
                            <span class="x-small text-muted d-none d-sm-inline">${val.exchanges.name}</span>
                        </div>
                      </td>
                      <td>
                        <div class="font-weight-bold text-white small">${val.user.currency}${parseFloat(val.amount).toFixed(0)}</div>
                        <div class="x-small text-muted">PL: ${val.p_l}</div>
                      </td>
                      <td class="d-none d-md-table-cell">
                        <div class="x-small">S: <span class="text-info strike-rate-display">${val.strike_rate}</span></div>
                        <div class="x-small text-muted">E: ${val.expire_time}</div>
                      </td>
                      <td>${statusBadge}</td>
                      <td class="small text-muted d-none d-lg-table-cell">${formattedDate}</td>
                      <td class="text-right">
                        ${val.approval_status == 'pending' ? `
                            <div class="d-flex gap-2 justify-content-end">
                                <form action="/admin/trades/approve/${val.id}" method="POST" class="d-inline">
                                    <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr('content')}">
                                    <button type="submit" class="btn btn-xs btn-success text-white font-weight-bold" style="border-radius: 4px;">Approve</button>
                                </form>
                                <form action="/admin/trades/reject/${val.id}" method="POST" class="d-inline">
                                    <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr('content')}">
                                    <button type="submit" class="btn btn-xs btn-danger text-white font-weight-bold" style="border-radius: 4px;">Reject</button>
                                </form>
                            </div>
                        ` : (val.status == 'pending' ? `
                            <button class="btn btn-xs glass-panel text-white border-0 px-3 hover-accent shadow-sm override-btn" 
                                data-id="${val.id}" 
                                data-amount="${val.amount}"
                                data-toggle="modal" 
                                data-target="#overrideModal"
                                style="border-radius: 4px;">
                                <i data-lucide="zap" style="width:12px; display:inline-block; vertical-align:middle; margin-right:4px;"></i> ADJUST
                            </button>
                        ` : `
                            <button class="btn btn-xs glass-panel text-muted border-0 px-3" disabled style="border-radius: 4px;">SETTLED</button>
                        `)}
                      </td>
                   </tr>
                `;
            });
            document.getElementById("get").innerHTML = tr;
            lucide.createIcons();
        });
    }

    $(document).on('click','.edit-trade',function(){
        $('#update').modal('show');
        $("#okays").val($(this).attr('did'));
        // Pre-fill strike rate if available
        let currentRate = $(this).closest('tr').find('.strike-rate-display').text();
        $("#strike_rate_input").val(currentRate);
    })

    $(document).on('click','.delete-trade',function(){
        let id = $(this).attr('did');
        if(confirm('Are you sure you want to close this trade?')){
            $.get("/admin/trades-delete/"+id, function(data){
                toastr.success("Trade closed successfully","Success")
                trade();
            })
        }
    })

    $("#updatesss").submit(function(e){
        e.preventDefault()
        let data = $('#data').val()
        let trad_id = $("#okays").val()
            
        $.post("{{route('update_trades')}}", {
            id: trad_id, 
            data: data,
            strike_rate: $('#strike_rate_input').val()
        }, function(data){
            toastr.success("Trade architectural parameters updated","Success")
            $('#update').modal('hide');
            trade();
        })
    })

    $(document).on('click','.override-trade',function(){
        $('#overrideModal').modal('show');
        $("#override_trade_id").val($(this).attr('did'));
        $("#override_trade_amount").val($(this).attr('d-amount'));
        $("#override_percentage").val('');
        $("#override_amount").val('');
        $("#override_reason").val('');
        $("#override_status").val('');
        $("#calcPreview").text('Live P/L Calculation: $0.00');
    });

    $("#override_percentage, #override_amount, #override_status").on('input change', function() {
        let amount = parseFloat($("#override_trade_amount").val() || 0);
        let perc = parseFloat($("#override_percentage").val() || 0);
        let override_amount = parseFloat($("#override_amount").val());
        let status = $("#override_status").val();
        
        let pl = 0;
        if (!isNaN(override_amount)) {
            if(status === 'win') pl = override_amount;
            else if (status === 'loss') pl = -Math.abs(override_amount);
            else pl = 0;
        } else {
            if(status === 'win') pl = (perc / 100) * amount;
            else if (status === 'loss') pl = -Math.abs((perc / 100) * amount);
            else pl = 0;
        }

        $("#calcPreview").text(`Live P/L Calculation: $${pl.toFixed(2)}`);
    });

    $("#overrideForm").submit(function(e){
        e.preventDefault();
        let trade_id = $("#override_trade_id").val();
        let data = {
            status: $("#override_status").val(),
            earningPercentage: $("#override_percentage").val() || 0,
            overrideAmount: $("#override_amount").val() || null,
            reason: $("#override_reason").val()
        };
            
        $.ajax({
            url: `/admin/trades/${trade_id}/override`,
            type: 'POST',
            data: data,
            success: function(res) {
                toastr.success("Trade successfully overridden", "Success");
                $('#overrideModal').modal('hide');
                trade();
            },
            error: function(err) {
                let msg = err.responseJSON?.error || err.responseJSON?.message || "Error overriding trade";
                toastr.error(msg, "Error");
            }
        });
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    lucide.createIcons();
});
</script>
@endpush

@endsection

