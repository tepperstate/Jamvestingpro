@extends('layouts.admin.app')
@section('content')
<div class="container-fluid py-4" id="futures-hub">
    <!-- Header Area -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 text-white gradient-text font-weight-bold mb-1">Futures Pairs Hub</h1>
            <p class="text-muted small mb-0">Manage perpetual contracts, leverage pairs, and market data.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-warning px-4 py-2 d-flex align-items-center" id="btn-sync-binance-header" style="border-radius: 12px; font-weight: 600;">
                <i data-lucide="refresh-cw" class="mr-2" style="width: 18px;"></i> Sync from Binance
            </button>
            <button class="btn btn-primary px-4 py-2 d-flex align-items-center" data-toggle="modal" data-target="#addPairModal" style="background: var(--accent-primary) !important; color: #ffffff !important; border-radius: 12px; border: none; font-weight: 600;">
                <i data-lucide="plus" class="mr-2" style="width: 18px;"></i> Add Pair
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
                <h6 class="mb-0 text-white font-weight-bold"><span id="selected-count">0</span> Pairs Selected</h6>
                <p class="text-muted x-small mb-0">Choose an action to apply to all selected items.</p>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-secondary" id="btn-clear-selection" style="border-radius: 8px;">Cancel</button>
            <button class="btn btn-sm btn-danger d-flex align-items-center" id="btn-mass-delete" style="border-radius: 8px;">
                <i data-lucide="trash-2" class="mr-1" style="width: 16px;"></i> Mass Delete
            </button>
        </div>
    </div>

    @php
        // Dynamically compute quote assets for tabs
        $quotes = [];
        foreach($pairs as $pair) {
            $quote = $pair->quote_asset ?? 'USDT';
            if(!in_array($quote, $quotes)) {
                $quotes[] = $quote;
            }
        }
        // Sort: USDT first, then BUSD, then BTC, then ETH, then others alphabetically
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
                    <button class="nav-link active market-tab-btn" data-quote="ALL" type="button" role="tab" style="background: transparent; color: var(--text-muted); border: none; border-bottom: 2px solid transparent; padding: 10px 20px; font-weight: 500;">All Pairs</button>
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
            <div class="badge badge-success-glass px-3 py-2">TOTAL: {{count($pairs)}}</div>
        </div>

        <div class="table-responsive">
            <table class="table text-white table-hover align-middle" id="futures-table">
                 <thead style="background: rgba(0,0,0,0.2);">
                    <tr>
                       <th style="width: 40px;" class="text-center">
                           <div class="custom-control custom-checkbox">
                               <input type="checkbox" class="custom-control-input" id="checkAll">
                               <label class="custom-control-label" for="checkAll"></label>
                           </div>
                       </th>
                       <th>SYMBOL</th>
                       <th>MARK PRICE</th>
                       <th>FUNDING RATE</th>
                       <th>MAX LEVERAGE</th>
                       <th class="text-right">ACTIONS</th>
                    </tr>
                 </thead>
                 <tbody>
                    @forelse($pairs as $pair)
                        @php
                            $quote = $pair->quote_asset ?? 'USDT';
                        @endphp
                        <tr data-quote="{{ $quote }}" data-id="{{ $pair->id }}">
                            <td class="text-center">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input row-checkbox" id="check_{{$pair->id}}" value="{{$pair->id}}">
                                    <label class="custom-control-label" for="check_{{$pair->id}}"></label>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <x-asset-logo :symbol="$pair->symbol" size="sm" />
                                    <div>
                                        <div class="font-weight-bold text-white fs-6">{{$pair->symbol}}</div>
                                        <div class="text-muted x-small text-uppercase">ID-{{$pair->id}}</div>
                                    </div>
                                </div>
                            </td>
                            <td><div class="font-weight-bold text-white">${{number_format((float)$pair->mark_price, 4)}}</div></td>
                            <td><div class="text-warning font-weight-bold">{{ number_format((float)($pair->funding_rate * 100), 4) }}%</div></td>
                            <td><span class="badge badge-primary-glass px-2 py-1">{{ $pair->max_leverage }}x</span></td>
                            <td class="text-right">
                                <div class="d-flex justify-content-end gap-2">
                                    <form action="{{ url('admin/futures/pairs/delete/'.$pair->id) }}" method="GET" class="delete-pair-form">
                                        <button type="submit" title="Delete Pair" class="btn btn-sm glass-panel border-0 text-danger delete-btn">
                                            <i data-lucide="trash-2" style="width:16px"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted"> <i data-lucide="layers" class="mb-2 opacity-50" style="width:32px; height:32px;"></i> <br> No futures pairs available</td>
                        </tr>
                    @endforelse
                 </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Pair Modal -->
<div class="modal fade" id="addPairModal" tabindex="-1" role="dialog" aria-labelledby="addPairModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content glass-card satin-border" style="background: rgba(15,15,20,0.95);">
      <div class="modal-header border-bottom border-secondary p-4">
        <h5 class="modal-title text-white font-weight-bold d-flex align-items-center gap-2" id="addPairModalLabel">
            <i data-lucide="plus-circle" class="text-primary"></i> Add Futures Pair
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-4">
        <form action="{{ route('admin.futures.pairs.store') }}" method="POST">
            @csrf
            <div class="form-group mb-4">
                <label class="text-muted small text-uppercase font-weight-bold tracking-wider mb-2">Symbol</label>
                <input type="text" name="symbol" class="form-control glass-panel text-white border-0" placeholder="e.g., BTCUSDT" required>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="form-group mb-4">
                        <label class="text-muted small text-uppercase font-weight-bold tracking-wider mb-2">Base Asset</label>
                        <input type="text" name="base_asset" class="form-control glass-panel text-white border-0" placeholder="BTC" required>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group mb-4">
                        <label class="text-muted small text-uppercase font-weight-bold tracking-wider mb-2">Quote Asset</label>
                        <input type="text" name="quote_asset" class="form-control glass-panel text-white border-0" placeholder="USDT" required>
                    </div>
                </div>
            </div>
            <div class="form-group mb-4">
                <label class="text-muted small text-uppercase font-weight-bold tracking-wider mb-2">Max Leverage</label>
                <input type="number" name="max_leverage" class="form-control glass-panel text-white border-0" value="100" required>
            </div>
            <div class="form-group mb-4">
                <label class="text-muted small text-uppercase font-weight-bold tracking-wider mb-2">Funding Rate</label>
                <input type="number" step="0.0001" name="funding_rate" class="form-control glass-panel text-white border-0" value="0.0100">
            </div>
            <div class="form-group mb-4">
                <label class="text-muted small text-uppercase font-weight-bold tracking-wider mb-2">Maker Fee</label>
                <input type="number" step="0.0001" name="maker_fee" class="form-control glass-panel text-white border-0" value="0.0200">
            </div>
            <div class="form-group mb-4">
                <label class="text-muted small text-uppercase font-weight-bold tracking-wider mb-2">Taker Fee</label>
                <input type="number" step="0.0001" name="taker_fee" class="form-control glass-panel text-white border-0" value="0.0400">
            </div>
            <div class="form-group mb-4">
                <label class="text-muted small text-uppercase font-weight-bold tracking-wider mb-2">Status</label>
                <select name="status" class="form-control glass-panel text-white border-0">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary w-100 py-3 mt-3 font-weight-bold" style="border-radius: 12px; background: var(--accent-primary) !important; color: #ffffff !important; border:none;">
                Save Pair
            </button>
        </form>
      </div>
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
        var futuresTable = $('#futures-table').DataTable({
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
                "info": "Showing _START_ to _END_ of _TOTAL_ pairs",
                "infoEmpty": "No pairs available",
            },
            "drawCallback": function() {
                if (typeof lucide !== 'undefined') lucide.createIcons();
                updateMassActionUI();
            }
        });

        // 2. Custom Search Box Binding
        $('#custom-search').on('keyup', function() {
            futuresTable.search(this.value).draw();
        });

        // 3. Dynamic Tab Filtering via Custom Search Function
        let currentQuoteFilter = 'ALL';
        
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex, rowData, counter) {
                if(settings.nTable.id !== 'futures-table') return true;
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
            futuresTable.draw();
        });

        // 4. Mass Action Logic (Checkboxes)
        $('#checkAll').on('change', function() {
            let isChecked = $(this).is(':checked');
            // Only check visible rows on current page
            $('#futures-table tbody tr:visible .row-checkbox').prop('checked', isChecked);
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
                text: `You are about to delete ${selectedIds.length} pairs. This cannot be undone.`,
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
                        url: "{{ route('admin.futures.pairs.mass_delete') }}",
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

        // 6. Sync from Binance
        $('#btn-sync-binance-header').on('click', function(e) {
            e.preventDefault();
            const btn = $(this);
            const originalContent = btn.html();
            btn.prop('disabled', true).html('<i data-lucide="refresh-cw" class="spinning" style="width:18px; display:inline-block;"></i> Syncing...');
            lucide.createIcons();

            toastr.info("Connecting to Binance...");

            $.post("{{ route('admin.futures.pairs.sync_binance') }}", {
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

        // Delete Individual
        $('.delete-btn').on('click', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            Swal.fire({
                title: 'Delete Pair?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#475569',
                confirmButtonText: 'Yes, delete it',
                background: 'rgba(15, 15, 20, 0.95)',
                color: '#f1f5f9'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endsection


