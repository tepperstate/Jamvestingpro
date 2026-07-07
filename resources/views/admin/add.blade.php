@extends('layouts.admin.app')
@section('content')
<div class="container-fluid py-4" id="markets-hub">
    <!-- Header Area -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 text-white gradient-text font-weight-bold mb-1">Spot Assets Hub</h1>
            <p class="text-muted small mb-0">Manage all trading assets, profits, and platform liquidity.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-warning px-4 py-2 d-flex align-items-center" id="btn-sync-binance-header" style="border-radius: 12px; font-weight: 600;">
                <i data-lucide="refresh-cw" class="mr-2" style="width: 18px;"></i> Sync from Binance
            </button>
            <button class="btn btn-primary px-4 py-2 d-flex align-items-center" data-toggle="modal" data-target="#addAssetModal" style="background: var(--accent-primary) !important; color: #ffffff !important; border-radius: 12px; border: none; font-weight: 600;">
                <i data-lucide="plus" class="mr-2" style="width: 18px;"></i> Add Asset
            </button>
        </div>
    </div>

    <!-- Mass Action Sticky Bar (Hidden by default) -->
    <div id="mass-action-bar" class="glass-card satin-border p-3 mb-4 d-none align-items-center justify-content-between shadow-2xl" style="position: sticky; top: 20px; z-index: 1000; border-left: 4px solid var(--danger);">
        <div class="d-flex align-items-center gap-3">
            <div class="icon-box glass-panel p-2" style="background: rgba(239, 68, 68, 0.1) !important; border-radius: 10px;">
                <i data-lucide="check-square" class="text-danger" style="width: 20px; height: 20px;"></i>
            </div>
            <div>
                <h6 class="mb-0 text-white font-weight-bold"><span id="selected-count">0</span> Assets Selected</h6>
                <p class="text-muted x-small mb-0">Choose an action to apply to all selected items.</p>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-secondary" id="btn-clear-selection" style="border-radius: 8px;">Cancel</button>
            <button class="btn btn-sm btn-info d-flex align-items-center" id="btn-mass-edit-pl" style="border-radius: 8px;" data-toggle="modal" data-target="#massEditPLModal">
                <i data-lucide="edit-2" class="mr-1" style="width: 16px;"></i> Mass Edit P&L
            </button>
            <button class="btn btn-sm btn-danger d-flex align-items-center" id="btn-mass-delete" style="border-radius: 8px;">
                <i data-lucide="trash-2" class="mr-1" style="width: 16px;"></i> Mass Delete
            </button>
        </div>
    </div>

    @php
        // Dynamically compute base markets (quote assets)
        $quotes = [];
        foreach($data as $asset) {
            $symbol = $asset->symbols;
            if (str_contains($symbol, '/')) {
                $quote = explode('/', $symbol)[1];
            } else {
                if(str_contains($symbol, 'USDT')) $quote = 'USDT';
                elseif(str_contains($symbol, 'BTC')) $quote = 'BTC';
                elseif(str_contains($symbol, 'ETH')) $quote = 'ETH';
                else $quote = 'OTHER';
            }
            if(!in_array($quote, $quotes)) {
                $quotes[] = $quote;
            }
        }
        // Sort: USDT first, then BTC, then ETH, then others alphabetically
        usort($quotes, function($a, $b) {
            $order = ['USDT' => 1, 'BUSD' => 2, 'USDC' => 3, 'BTC' => 4, 'ETH' => 5];
            $wa = $order[$a] ?? 99;
            $wb = $order[$b] ?? 99;
            if ($wa == $wb) return strcmp($a, $b);
            return $wa - $wb;
        });
    @endphp

    <!-- Markets Layout -->
    <div class="glass-card satin-border overflow-hidden shadow-xl">
        <!-- Tabs Header -->
        <div class="p-0 border-bottom border-secondary" style="background: rgba(255,255,255,0.01);">
            <ul class="nav nav-tabs px-4 pt-3 border-0" id="marketTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active market-tab-btn" data-quote="ALL" type="button" role="tab" style="background: transparent; color: var(--text-muted); border: none; border-bottom: 2px solid transparent; padding: 10px 20px; font-weight: 500;">All Assets</button>
                </li>
                @foreach($quotes as $q)
                <li class="nav-item" role="presentation">
                    <button class="nav-link market-tab-btn" data-quote="{{ $q }}" type="button" role="tab" style="background: transparent; color: var(--text-muted); border: none; border-bottom: 2px solid transparent; padding: 10px 20px; font-weight: 500;">{{ $q }} Market</button>
                </li>
                @endforeach
            </ul>
        </div>

        <div class="p-4 d-flex justify-content-between align-items-center" style="background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--glass-border);">
            <div class="position-relative">
                <i data-lucide="search" class="position-absolute text-muted" style="width: 18px; top: 50%; transform: translateY(-50%); left: 15px;"></i>
                <input type="text" id="custom-search" class="form-control glass-panel text-white border-0" placeholder="Search pairs..." style="padding-left: 45px; width: 300px; border-radius: 12px; background: rgba(0,0,0,0.2);">
            </div>
            <div class="badge badge-success-glass px-3 py-2">TOTAL: {{count($data)}}</div>
        </div>

        <div class="table-responsive">
            <table class="table text-white table-hover align-middle" id="assets-table">
                 <thead style="background: rgba(0,0,0,0.2);">
                    <tr>
                       <th style="width: 40px;" class="text-center">
                           <div class="custom-control custom-checkbox">
                               <input type="checkbox" class="custom-control-input" id="checkAll">
                               <label class="custom-control-label" for="checkAll"></label>
                           </div>
                       </th>
                       <th>ASSET PAIR</th>
                       <th>QUOTE</th>
                       <th>PROFIT %</th>
                       <th>LOSS %</th>
                       <th>FIXED PROFIT</th>
                       <th class="text-right">ACTIONS</th>
                    </tr>
                 </thead>
                 <tbody>
                    @forelse($data as $datas)
                        @php
                            $symbol = $datas->symbols;
                            if (str_contains($symbol, '/')) {
                                $quote = explode('/', $symbol)[1];
                            } else {
                                $quote = str_contains($symbol, 'USDT') ? 'USDT' : (str_contains($symbol, 'BTC') ? 'BTC' : (str_contains($symbol, 'ETH') ? 'ETH' : 'OTHER'));
                            }
                        @endphp
                        <tr data-quote="{{ $quote }}" data-id="{{ $datas->id }}">
                            <td class="text-center">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input row-checkbox" id="check_{{$datas->id}}" value="{{$datas->id}}">
                                    <label class="custom-control-label" for="check_{{$datas->id}}"></label>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <x-asset-logo :symbol="$symbol" size="sm" />
                                    <div>
                                        <div class="font-weight-bold text-white fs-6">{{$symbol}}</div>
                                        <div class="text-muted x-small text-uppercase">ID-{{$datas->id}} • {{$datas->type ?? 'SPOT'}}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge badge-primary-glass px-2 py-1">{{ $quote }}</span></td>
                            <td><div class="text-success font-weight-bold">+{{$datas->percentage}}%</div></td>
                            <td><div class="text-danger font-weight-bold">-{{$datas->loss_percentage}}%</div></td>
                            <td><div class="font-weight-bold text-white">${{number_format((float)$datas->profits, 2)}}</div></td>
                            <td class="text-right">
                                <div class="d-flex justify-content-end gap-2">
                                    <button title="Sync Logo" class="btn btn-sm glass-panel text-success border-0 sync-single-logo" data-symbol="{{$symbol}}">
                                        <i data-lucide="refresh-cw" style="width:16px"></i>
                                    </button>
                                    <a title="Edit Asset" class='btn btn-sm glass-panel text-white border-0' href='{{url("admin/add/single/".$datas->id."/".$datas->exchanges_id)}}'>
                                        <i data-lucide="edit-3" style="width:16px"></i>
                                    </a>
                                    <a title="Delete Asset" class='btn btn-sm glass-panel border-0 text-danger admin-delete-btn' href='{{url("admin/delete/".$datas->id."/".$datas->exchanges_id)}}'>
                                        <i data-lucide="trash-2" style="width:16px"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted"> <i data-lucide="layers" class="mb-2 opacity-50" style="width:32px; height:32px;"></i> <br> No assets added yet</td>
                        </tr>
                    @endforelse
                 </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Mass Edit P&L Modal -->
<div class="modal fade" id="massEditPLModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
    <div class="modal-content glass-card satin-border" style="background: rgba(15, 15, 20, 0.95); border: 1px solid var(--glass-border);">
      <div class="modal-header border-bottom border-secondary p-4">
        <h5 class="modal-title text-white font-weight-bold">Mass Edit Profit & Loss</h5>
        <button type="button" class="btn-close btn-close-white" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div class="form-group mb-4">
            <label class="text-muted small text-uppercase font-weight-bold tracking-wider mb-2">New Profit Percentage (%)</label>
            <input type="number" step="0.01" id="mass_profit_percentage" class="form-control glass-panel text-white border-0" placeholder="e.g. 10.5">
        </div>
        <div class="form-group mb-4">
            <label class="text-muted small text-uppercase font-weight-bold tracking-wider mb-2">New Loss Percentage (%)</label>
            <input type="number" step="0.01" id="mass_loss_percentage" class="form-control glass-panel text-white border-0" placeholder="e.g. 5.0">
        </div>
        <button type="button" id="btn-confirm-mass-edit-pl" class="btn btn-info w-100 py-3 font-weight-bold" style="border-radius: 12px; border:none;">
            Apply to Selected
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Add Asset Modal -->
<div class="modal fade" id="addAssetModal" tabindex="-1" aria-labelledby="addAssetModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
    <div class="modal-content glass-card satin-border" style="background: rgba(15, 15, 20, 0.95); border: 1px solid var(--glass-border);">
      <div class="modal-header border-bottom border-secondary p-4">
        <h5 class="modal-title text-white font-weight-bold" id="addAssetModalLabel">Manually Add Asset</h5>
        <button type="button" class="btn-close btn-close-white" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('add.post') }}" method="POST" enctype="multipart/form-data" class="p-4">
        @csrf
        <div class="form-group mb-4">
            <label class="text-muted small text-uppercase font-weight-bold tracking-wider mb-2">Exchange / Data Source</label>
            <select name="exchanges_id" class="form-control glass-panel text-white" required>
                @foreach($cat as $ex)
                    <option value="{{ $ex->id }}" class="text-dark">{{ $ex->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group mb-4">
            <label class="text-muted small text-uppercase font-weight-bold tracking-wider mb-2">Ticker Symbol</label>
            <input type="text" name="symbol" class="form-control glass-panel text-white border-0" placeholder="e.g., BTC/USDT" required>
        </div>
        <div class="form-group mb-4">
            <label class="text-muted small text-uppercase font-weight-bold tracking-wider mb-2">Asset Name</label>
            <input type="text" name="name" class="form-control glass-panel text-white border-0" placeholder="Bitcoin" required>
        </div>
        <div class="form-group mb-4">
            <label class="text-muted small text-uppercase font-weight-bold tracking-wider mb-2">Mirror Coin Symbol (Optional)</label>
            <input type="text" name="mirror_symbol" class="form-control glass-panel text-white border-0" placeholder="e.g., BTCUSDT">
            <small class="text-muted">If provided, the chart and live price will mirror this exact Binance coin.</small>
        </div>
        <div class="row">
            <div class="col-6">
                <div class="form-group mb-4">
                    <label class="text-muted small text-uppercase font-weight-bold tracking-wider mb-2">Profit Rate (%)</label>
                    <input type="number" step="0.01" name="percentage" class="form-control glass-panel text-white border-0" placeholder="10.5" required>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group mb-4">
                    <label class="text-muted small text-uppercase font-weight-bold tracking-wider mb-2">Loss Rate (%)</label>
                    <input type="number" step="0.01" name="loss_percentage" class="form-control glass-panel text-white border-0" placeholder="5.0" required>
                </div>
            </div>
        </div>
        <div class="form-group mb-4">
            <label class="text-muted small text-uppercase font-weight-bold tracking-wider mb-2">Fixed Profit ($)</label>
            <input type="number" step="0.01" name="profit" class="form-control glass-panel text-white border-0" placeholder="0.00" required>
            <input type="hidden" name="type" value="assets">
        </div>
        <div class="form-group mb-4">
            <label class="text-muted small text-uppercase font-weight-bold tracking-wider mb-2">Primary Image</label>
            <input type="file" name="image1" class="form-control glass-panel text-white border-0 p-2" accept="image/*">
        </div>
        <div class="form-group mb-4">
            <label class="text-muted small text-uppercase font-weight-bold tracking-wider mb-2">Secondary Image</label>
            <input type="file" name="image2" class="form-control glass-panel text-white border-0 p-2" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary w-100 py-3 mt-3 font-weight-bold" style="border-radius: 12px; background: var(--accent-primary) !important; color: #ffffff !important; border:none;">
            Save Asset
        </button>
    </form>
  </div>
</div>

<!-- Smart Import Offcanvas -->
<div class="offcanvas offcanvas-end glass-card satin-border" tabindex="-1" id="importAgentOffcanvas" aria-labelledby="importAgentLabel" style="width: 450px; background: rgba(15,15,20,0.95); border-left: 1px solid var(--glass-border);">
  <div class="offcanvas-header border-bottom border-secondary p-4">
    <h5 class="offcanvas-title text-white font-weight-bold d-flex align-items-center gap-2" id="importAgentLabel">
        <i data-lucide="bot" class="text-primary"></i> Smart Import Agent
    </h5>
    <button type="button" class="btn-close btn-close-white text-reset" data-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body p-4">
      <p class="text-muted small mb-4">Synchronize mass ticker feeds from Binance or run an autonomous institutional discovery scan.</p>
      
      <div class="form-group mb-4">
          <label class="text-muted small text-uppercase font-weight-bold tracking-wider mb-2">Target Exchange Cluster</label>
          <select id="discovery-cat" class="form-control glass-panel text-white" required>
              @foreach($cat as $ex)
                  <option value="{{ $ex->id }}" class="text-dark">{{ $ex->name }} Exchange</option>
              @endforeach
          </select>
      </div>
      <div class="row mb-4">
          <div class="col-6">
              <label class="text-muted x-small text-uppercase font-weight-bold mb-2">Yield Target (%)</label>
              <input type="number" id="discovery-per" class="form-control glass-panel text-white border-0" value="85.00">
          </div>
          <div class="col-6">
              <label class="text-muted x-small text-uppercase font-weight-bold mb-2">Loss Target (%)</label>
              <input type="number" id="discovery-loss" class="form-control glass-panel text-white border-0" value="85.00">
          </div>
      </div>

      <div class="glass-panel p-3 mb-4 satin-border" style="border-radius: 12px; background: rgba(0,0,0,0.2) !important; min-height: 120px;">
          <div id="scan-status" class="text-muted small">
              <div class="d-flex align-items-center gap-2 mb-2">
                  <i data-lucide="circle-dashed" class="spinning" style="width:14px"></i>
                  <span>Awaiting command...</span>
              </div>
          </div>
          <div id="discovery-log" class="mt-2 x-small text-info opacity-75" style="max-height: 80px; overflow-y: auto;"></div>
      </div>

      <div class="d-flex flex-column gap-3">
          <button id="start-scan" class="btn btn-primary py-3 font-weight-bold" style="border-radius: 12px; background: var(--accent-primary) !important; color: #ffffff !important; border:none;">
              <i data-lucide="zap" class="mr-2" style="width: 18px; display: inline-block;"></i> Run Discovery Scan
          </button>
          <button id="sync-binance" class="btn btn-warning py-3 font-weight-bold" style="border-radius: 12px; border:none;">
              <i data-lucide="download" class="mr-2" style="width: 18px; display: inline-block;"></i> Sync from Binance
          </button>
          <button id="sync-all-logos" class="btn btn-success py-3 font-weight-bold" style="background: #ff3333 !important; color: #fff !important; border-radius: 12px; border:none;">
              <i data-lucide="image" class="mr-2" style="width: 18px; display: inline-block;"></i> Auto-Download Missing Logos
          </button>
      </div>
  </div>
</div>

<style>
    .x-small { font-size: 10px; }
    .tracking-wider { letter-spacing: 0.1em; }
    .shadow-2xl { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); }
    
    .nav-tabs .nav-link {
        transition: all 0.3s ease;
        opacity: 0.6;
    }
    .nav-tabs .nav-link:hover {
        opacity: 1;
        color: white !important;
    }
    .nav-tabs .nav-link.active {
        opacity: 1;
        color: var(--accent-primary) !important;
        border-bottom: 2px solid var(--accent-primary) !important;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .spinning {
        animation: spin 2s linear infinite;
    }
    .scan-active .spinning {
        animation: spin 0.8s linear infinite;
        color: var(--accent-primary) !important;
    }
    
    /* Modal dark mode fixes */
    .modal-backdrop {
        background-color: rgba(0,0,0,0.8);
    }
</style>

<script>
    $(document).ready(function(){
        // Ensure Lucide icons are initialized
        if (typeof lucide !== 'undefined') lucide.createIcons();

        // 1. Initialize DataTable (with DOM manipulation for hiding default search)
        var assetsTable = $('#assets-table').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "pageLength": 15,
            "dom": '<"top">rt<"bottom"ip><"clear">', // Hides default search box
            "language": {
                "info": "Showing _START_ to _END_ of _TOTAL_ assets",
                "infoEmpty": "No assets available",
            },
            "drawCallback": function() {
                if (typeof lucide !== 'undefined') lucide.createIcons();
                bindSyncLogos();
                updateMassActionUI();
            }
        });

        // 2. Custom Search Box Binding
        $('#custom-search').on('keyup', function() {
            assetsTable.search(this.value).draw();
        });

        // 3. Dynamic Tab Filtering via Custom Search Function
        let currentQuoteFilter = 'ALL';
        
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex, rowData, counter) {
                if(settings.nTable.id !== 'assets-table') return true;
                if(currentQuoteFilter === 'ALL') return true;
                
                // Get the quote from the row data attribute
                let row = $(settings.aoData[dataIndex].nTr);
                let quote = row.data('quote');
                
                return quote === currentQuoteFilter;
            }
        );

        $('.market-tab-btn').on('click', function(e) {
            e.preventDefault();
            $('.market-tab-btn').removeClass('active');
            $(this).addClass('active');
            
            currentQuoteFilter = $(this).data('quote');
            assetsTable.draw();
        });

        // 4. Mass Action Logic (Checkboxes)
        $('#checkAll').on('change', function() {
            let isChecked = $(this).is(':checked');
            // Only check visible rows on current page
            $('#assets-table tbody tr:visible .row-checkbox').prop('checked', isChecked);
            updateMassActionUI();
        });

        $(document).on('change', '.row-checkbox', function() {
            updateMassActionUI();
        });

        $('#btn-clear-selection').on('click', function() {
            $('.row-checkbox, #checkAll').prop('checked', false);
            updateMassActionUI();
        });

        function updateMassActionUI() {
            let selectedCount = $('.row-checkbox:checked').length;
            $('#selected-count').text(selectedCount);
            
            if(selectedCount > 0) {
                $('#mass-action-bar').removeClass('d-none').addClass('d-flex');
            } else {
                $('#mass-action-bar').addClass('d-none').removeClass('d-flex');
                $('#checkAll').prop('checked', false);
            }
        }

        // 5. Mass Delete Action
        $('#btn-mass-delete').on('click', function() {
            let selectedIds = [];
            $('.row-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if(selectedIds.length === 0) return;

            Swal.fire({
                title: 'Mass Delete?',
                text: `You are about to delete ${selectedIds.length} assets. This cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#475569',
                confirmButtonText: 'Yes, delete all',
                background: 'rgba(15, 15, 20, 0.95)',
                color: '#f1f5f9'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Deleting...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading() },
                        background: 'rgba(15, 15, 20, 0.95)',
                        color: '#f1f5f9'
                    });

                    // Send AJAX request
                    $.ajax({
                        url: "{{ route('admin.asset.mass_delete') }}",
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            ids: selectedIds
                        },
                        success: function(res) {
                            if(res.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: res.message,
                                    background: 'rgba(15, 15, 20, 0.95)',
                                    color: '#f1f5f9'
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire('Error', res.message || 'Failed to delete items.', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Server communication failed.', 'error');
                        }
                    });
                }
            });
        });

        // 5.5 Mass Edit P&L Logic
        $('#btn-confirm-mass-edit-pl').on('click', function() {
            let selectedIds = [];
            $('.row-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });

            let profitPercentage = $('#mass_profit_percentage').val();
            let lossPercentage = $('#mass_loss_percentage').val();

            if(selectedIds.length === 0) return;
            if(!profitPercentage && !lossPercentage) {
                toastr.error("Please provide at least one percentage to update.");
                return;
            }

            const btn = $(this);
            const originalText = btn.html();
            btn.prop('disabled', true).html('Updating...');

            $.ajax({
                url: "{{ route('admin.asset.mass_edit_pl') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    ids: selectedIds,
                    profit_percentage: profitPercentage,
                    loss_percentage: lossPercentage
                },
                success: function(res) {
                    btn.prop('disabled', false).html(originalText);
                    if(res.status) {
                        $('#massEditPLModal').modal('hide');
                        Swal.fire({
                            title: 'Success!',
                            text: res.message || 'Assets updated successfully.',
                            icon: 'success',
                            background: 'rgba(15, 15, 20, 0.95)',
                            color: '#f1f5f9'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Error', res.message || 'Failed to update items.', 'error');
                    }
                },
                error: function() {
                    btn.prop('disabled', false).html(originalText);
                    Swal.fire('Error', 'Server communication failed.', 'error');
                }
            });
        });

        // 6. Smart Import & Sync Scripts (Preserved logic)
        $('#start-scan').on('click', function() {
            const btn = $(this);
            const statusBox = $('#scan-status');
            const logBox = $('#discovery-log');
            const catId = $('#discovery-cat').val();
            const catName = $('#discovery-cat option:selected').text();
            
            btn.prop('disabled', true).addClass('scan-active');
            statusBox.html(`
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i data-lucide="refresh-cw" class="spinning" style="width:14px"></i>
                    <span class="text-white">Scanning Cluster: ${catName}...</span>
                </div>
            `);
            logBox.prepend(`<div class="mb-1"> [${new Date().toLocaleTimeString()}] - Initializing smart extraction agent...</div>`);
            lucide.createIcons();

            setTimeout(() => {
                logBox.prepend(`<div class="mb-1"> [${new Date().toLocaleTimeString()}] - Propping institutional feeders for ${catName}...</div>`);
                
                $.post("{{ route('admin.asset.discovery') }}", {
                    _token: "{{ csrf_token() }}",
                    cat: catId,
                    percentage: $('#discovery-per').val(),
                    loss_percentage: $('#discovery-loss').val()
                }, function(res) {
                    if(res.status) {
                        statusBox.html(`
                            <div class="d-flex align-items-center gap-2 mb-2 text-success">
                                <i data-lucide="check-circle" style="width:14px"></i>
                                <span>Discovery Successful: ${res.count} symbols injected.</span>
                            </div>
                        `);
                        logBox.prepend(`<div class="mb-1 text-success"> [${new Date().toLocaleTimeString()}] - ${res.message}</div>`);
                        toastr.success(res.message);
                        
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    }
                }).fail(function() {
                    btn.prop('disabled', false).removeClass('scan-active');
                    statusBox.html(`<span class="text-danger">Extraction process failed. Signal lost.</span>`);
                    toastr.error("Autonomous scan failed.");
                });
            }, 1500);
        });

        $('#sync-binance').on('click', function(e) {
            e.preventDefault();
            const btn = $(this);
            const originalContent = btn.html();
            btn.prop('disabled', true).html('<i data-lucide="refresh-cw" class="spinning" style="width:18px; display:inline-block;"></i> Syncing...');
            lucide.createIcons();

            toastr.info("Connecting to Binance...");

            $.post("{{ route('admin.asset.sync_binance') }}", {
                _token: "{{ csrf_token() }}"
            }, function(res) {
                btn.prop('disabled', false).html(originalContent);
                lucide.createIcons();
                if(res.status) {
                    toastr.success(res.message);
                    setTimeout(() => window.location.reload(), 2000);
                } else {
                    toastr.error(res.message || "Failed to sync.");
                }
            }).fail(function() {
                btn.prop('disabled', false).html(originalContent);
                lucide.createIcons();
                toastr.error("Binance sync failed.");
            });
        });

        $('#sync-all-logos').on('click', function() {
            const btn = $(this);
            const originalContent = btn.html();
            btn.prop('disabled', true).html('<i data-lucide="refresh-cw" class="spinning" style="width:18px"></i>');
            lucide.createIcons();

            toastr.info("Initializing global logo synchronization...");

            $.post("{{ route('admin.asset.sync_logos') }}", {
                _token: "{{ csrf_token() }}"
            }, function(res) {
                btn.prop('disabled', false).html(originalContent);
                lucide.createIcons();
                if(res.status) {
                    toastr.success(res.message);
                    setTimeout(() => window.location.reload(), 2000);
                }
            }).fail(function() {
                btn.prop('disabled', false).html(originalContent);
                lucide.createIcons();
                toastr.error("Logo sync failed.");
            });
        });

        function bindSyncLogos() {
            $('.sync-single-logo').off('click').on('click', function() {
                const btn = $(this);
                const symbol = btn.data('symbol');
                const originalContent = btn.html();
                
                btn.prop('disabled', true).addClass('spinning');
                
                $.post("{{ route('admin.asset.sync_logos') }}", {
                    _token: "{{ csrf_token() }}",
                    symbol: symbol
                }, function(res) {
                    btn.prop('disabled', false).removeClass('spinning');
                    if(res.status) {
                        toastr.success(`Identity synced for ${symbol}`);
                        setTimeout(() => window.location.reload(), 1000);
                    }
                }).fail(function() {
                    btn.prop('disabled', false).removeClass('spinning');
                    toastr.error("Sync failed.");
                });
            });
        }
    });
</script>
@endsection


