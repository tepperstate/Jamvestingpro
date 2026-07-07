@extends('layouts.admin.app')
@section('title', 'Stock Asset Management')

@section('content')

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-5">
        <div>
            <h1 class="h2 mb-1 text-white gradient-text" style="font-weight: 800;">Equity Asset Management</h1>
            <p class="text-muted mb-0">Synchronize stock assets and global onboarding systems.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
             <button onclick="history.back()" class="btn btn-sm glass-panel px-4 text-white-50 border-0" style="background: rgba(255,255,255,0.05) !important;">
                <i data-lucide="chevron-left" class="mr-2" style="width:14px; display:inline-block;"></i> Back to Command
            </button>
        </div>
    </div>

    <!-- Configuration Modules -->
    <div class="row g-4 mb-5">
        <!-- Onboarding System Module -->
        <div class="col-lg-4">
            <div class="glass-card satin-border p-4 h-100 shadow-xl">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="icon-box glass-panel p-2" style="background: rgba(14, 165, 233, 0.1) !important; border-radius: 12px;">
                        <i data-lucide="megaphone" class="text-info" style="width: 20px; height: 20px;"></i>
                    </div>
                    <h3 class="h5 text-white mb-0 font-weight-bold">Welcome Message</h3>
                </div>
                <form method='post' action="{{route('welcome')}}">
                    @csrf
                    <div class="form-group mb-4">
                        <textarea name='message' id="summernote" class="form-control glass-panel border-0 text-white p-3">{{$message->message}}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-3 satin-border font-weight-bold" style="background: var(--accent-primary) !important; color: #ffffff !important; border-radius: 12px; border: none;">
                        <i data-lucide="save" class="mr-2" style="width:16px; display:inline-block;"></i> Update System
                    </button>
                </form>
            </div>
        </div>

        <!-- Autonomous Stock Discovery Agent (Smart Scan) -->
        <div class="col-lg-4">
            <div class="glass-card satin-border p-4 h-100 shadow-xl" style="background: linear-gradient(145deg, rgba(255,255,255,0.03) 0%, rgba(255,255,255,0.01) 100%);">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box glass-panel p-2" style="background: rgba(168, 85, 247, 0.1) !important; border-radius: 12px;">
                            <i data-lucide="bot" class="text-purple-400" style="width: 20px; height: 20px; color: #a855f7;"></i>
                        </div>
                        <h3 class="h5 text-white mb-0 font-weight-bold">Discovery Agent</h3>
                    </div>
                    <div id="scanIndicator" class="spinner-grow text-purple-400 spinner-grow-sm d-none" role="status"></div>
                </div>

                <div id="scanStatus" class="mb-4">
                    <div class="glass-panel p-3 text-center mb-0" style="background: rgba(0,0,0,0.2); border-radius: 12px; border: 1px dashed rgba(255,255,255,0.1);">
                        <div id="statusIcon" class="mb-2">
                            <i data-lucide="search" class="text-muted opacity-50" style="width: 32px; height: 32px;"></i>
                        </div>
                        <p id="statusText" class="text-muted x-small mb-0 font-weight-bold uppercase-tracking">Ready for Market Extraction</p>
                    </div>
                </div>

                <div id="extractionLog" class="mb-4 d-none" style="height: 100px; overflow-y: auto; background: rgba(0,0,0,0.3); border-radius: 12px; padding: 10px; font-family: 'Courier New', Courier, monospace;">
                    <div class="x-small text-success mb-1">> Initializing extraction systems...</div>
                </div>

                <button id="commenceScan" type="button" class="btn btn-outline-purple w-100 py-3 satin-border font-weight-bold transition-all" style="border-radius: 12px;">
                    <i data-lucide="zap" class="mr-2" style="width:16px; display:inline-block;"></i> COMMENCE SMART SCAN
                </button>
            </div>
        </div>

        <!-- Add Stock Module -->
        <div class="col-lg-4">
            <div class="glass-card satin-border p-4 h-100 shadow-xl">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="icon-box glass-panel p-2" style="background: rgba(255, 51, 51, 0.1) !important; border-radius: 12px;">
                        <i data-lucide="plus-square" class="text-success" style="width: 20px; height: 20px;"></i>
                    </div>
                    <h3 class="h5 text-white mb-0 font-weight-bold">Add Stock</h3>
                </div>
                <form method='post' action="{{route('admin.stocks.store')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted x-small text-uppercase font-weight-bold mb-2">Equity Name</label>
                            <input type='text' class='form-control glass-panel border-0 text-white px-3' name='name' placeholder="e.g. NVIDIA Corp" required>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted x-small text-uppercase font-weight-bold mb-2">Ticker Symbol</label>
                            <input type='text' class='form-control glass-panel border-0 text-white px-3' name='symbol' placeholder="NVDA" required>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted x-small text-uppercase font-weight-bold mb-2">Profit Percentage (%)</label>
                            <input type='number' step="any" class='form-control glass-panel border-0 text-white px-3' name='profit_percentage' placeholder="e.g. 5.5">
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted x-small text-uppercase font-weight-bold mb-2">Daily Gain (Value)</label>
                            <input type='number' step="any" class='form-control glass-panel border-0 text-white px-3' name='daily_gain' placeholder="e.g. 12.30">
                        </div>
                        <div class="col-md-6 mt-3">
                            <label class="text-muted x-small text-uppercase font-weight-bold mb-2">Buffer Percentage (%)</label>
                            <input type='number' step="0.01" class='form-control glass-panel border-0 text-white px-3' name='buffer_percent' placeholder="e.g. 20.00" value="20" required>
                        </div>
                        <div class="col-md-6 mt-3">
                            <label class="text-muted x-small text-uppercase font-weight-bold mb-2">Per Withdrawal (%)</label>
                            <input type='number' step="0.01" class='form-control glass-panel border-0 text-white px-3' name='per_withdrawal_percent' placeholder="e.g. 5.00" value="5" required>
                        </div>
                        <div class="col-12">
                            <label class="text-muted x-small text-uppercase font-weight-bold mb-2">Visual Signature (Logo)</label>
                            <div class="custom-file glass-panel" style="border: 1px solid var(--glass-border); border-radius: 12px;">
                                <input type="file" name="image" class="custom-file-input" id="stockImage" required>
                                <label class="custom-file-label text-muted border-0 bg-transparent py-2" for="stockImage">Choose file...</label>
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary w-100 py-3 satin-border font-weight-bold" style="background: var(--accent-primary) !important; color: #ffffff !important; border-radius: 12px; border: none;">
                                <i data-lucide="activity" class="mr-2" style="width:16px; display:inline-block;"></i> Add Stock
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Active Stocks Table -->
    <div class="glass-card satin-border overflow-hidden shadow-2xl">
        <div class="p-4 d-flex justify-content-between align-items-center" style="background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--glass-border);">
           <div>
               <h3 class="h5 text-white mb-1 font-weight-bold">All Stocks</h3>
               <p class="text-muted x-small mb-0">Active assets monitored by the platform.</p>
           </div>
           <div class="badge badge-success-glass px-3 py-2">LIVE ASSETS: {{count($data)}}</div>
        </div>
        <div class="table-responsive">
            <table class="table text-white" id="example">
                <thead>
                    <tr>
                        <th>RANK</th>
                        <th>NAME</th>
                        <th>VISUAL</th>
                        <th>SYMBOL</th>
                        <th class="text-right">COMMANDS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $key => $value )
                        <tr>
                            <td class="small font-weight-bold text-muted">STK-{{++$key}}</td>
                            <td class="font-weight-bold">{{$value->name}}</td> 
                            <td>
                                @php
                                    $symbol = $value->symbol;
                                    $s = strtoupper(preg_replace('/[^a-zA-Z]/', '', explode('/', $symbol)[0]));
                                @endphp
                                <x-asset-logo :symbol="$value->symbol" size="sm" />
                            </td>
                            <td><div class="badge badge-success-glass px-3 py-1">{{$value->symbol}}</div></td> 
                            <td class="text-right">
                                <div class="d-flex justify-content-end gap-2">
                                    <button data-toggle="modal" name="{{$value->name}}" sb="{{$value->symbol}}" data-profit="{{$value->profit_percentage}}" data-gain="{{$value->daily_gain}}" data-buffer="{{$value->buffer_percent}}" data-per="{{$value->per_withdrawal_percent}}" data-target="#edit_staolll" did="{{$value->id}}" class='btn btn-sm glass-panel text-white border-0 edit_stock'>
                                        <i data-lucide="edit-3" style="width:16px"></i>
                                    </button>
                                    <a class='btn btn-sm glass-panel border-0 text-danger' href="{{route('delete_stock',$value->id)}}">
                                        <i data-lucide="trash-2" style="width:16px"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted"> <i data-lucide="layers" class="mb-2 opacity-30"></i> <br> No stocks added yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('modals')
<!-- Edit Modal -->
<div class="modal fade" id="edit_staolll" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document"> 
        <div class="modal-content glass-modal satin-border shadow-2xl border-0">
            <div class="modal-header border-bottom-0 pb-0">
                <h4 class="modal-title font-weight-bold text-white">Edit Stock</h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 pt-0">
                <form method='post' action="{{route('edit_stock')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group mb-4">
                        <label class="text-muted x-small text-uppercase font-weight-bold mb-2">Equity Descriptor</label>
                        <input type="hidden" name='id' id="stock_id">
                        <input type='text' class='form-control glass-panel border-0 text-white px-3' name='name' id="names" required>
                    </div>
                    <div class="form-group mb-4">
                        <label class="text-muted x-small text-uppercase font-weight-bold mb-2">Visual Signature (Logo)</label>
                        <div class="custom-file glass-panel" style="border: 1px solid var(--glass-border); border-radius: 12px;">
                            <input type='file' class='custom-file-input' name='image' id="editImage">
                            <label class="custom-file-label text-muted border-0 bg-transparent py-2" for="editImage">Update Image...</label>
                        </div>
                    </div>
                    <div class="form-group mb-4">
                        <label class="text-muted x-small text-uppercase font-weight-bold mb-2">Ticker Symbol</label>
                        <input type='text' class='form-control glass-panel border-0 text-white px-3' name='symbol' id="sybbbd" required>
                    </div>
                    <div class="form-group mb-4">
                        <label class="text-muted x-small text-uppercase font-weight-bold mb-2">Profit Percentage (%)</label>
                        <input type='number' step="any" class='form-control glass-panel border-0 text-white px-3' name='profit_percentage' id="edit_profit">
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-muted x-small text-uppercase font-weight-bold mb-2">Daily Gain (Value)</label>
                        <input type='number' step="any" class='form-control glass-panel border-0 text-white px-3' name='daily_gain' id="edit_daily_gain">
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-muted x-small text-uppercase font-weight-bold mb-2">Buffer Percentage (%)</label>
                        <input type='number' step="0.01" class='form-control glass-panel border-0 text-white px-3' name='buffer_percent' id="edit_buffer" required>
                    </div>
                    <div class="form-group mb-5">
                        <label class="text-muted x-small text-uppercase font-weight-bold mb-2">Per Withdrawal (%)</label>
                        <input type='number' step="0.01" class='form-control glass-panel border-0 text-white px-3' name='per_withdrawal_percent' id="edit_per" required>
                    </div>
                    <button type="submit" class="btn btn-premium w-100 py-3">
                        Update Global Parameters
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endpush

@if(session('status'))
    <script>toastr.success("{{session('status')}}","Success")</script>
@endif

<style>
    .x-small { font-size: 10px; }
    .gap-2 { gap: 0.5rem; }
    .gap-3 { gap: 1rem; }
</style>

<script>
    $(document).ready(function() {
        $('#example').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": false,
            "autoWidth": false,
            "responsive": true,
            "pageLength": 10,
            "language": {
                "search": "",
                "searchPlaceholder": "Search assets..."
            }
        });
        $('.dataTables_filter input').addClass('glass-panel text-white border-0 px-4 py-2').css({'background': 'rgba(255,255,255,0.02)', 'width': '250px', 'border-radius': '12px'});
        lucide.createIcons();

        $(document).on("click",'.edit_stock',function(){
            $('#edit_profit').val($(this).data('profit'))
            $('#edit_daily_gain').val($(this).data('gain'))
            $('#edit_buffer').val($(this).data('buffer'))
            $('#edit_per').val($(this).data('per'))
            $('#edit_id').val($(this).attr('did'));
            $("#stock_id").val($(this).attr('did'));
            $("#sybbbd").val($(this).attr('sb'));
            $("#names").val($(this).attr('name'));
            $("#edit_profit").val($(this).attr('data-profit'));
            $("#edit_daily_gain").val($(this).attr('data-gain'));
        });

        $('#summernote').summernote({
            placeholder: 'Company mission statement...',
            tabsize: 2,
            height: 150,
            toolbar: [['style', ['bold', 'italic', 'underline', 'clear']], ['para', ['ul', 'ol', 'paragraph']], ['view', ['codeview']]]
        });

        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        // Smart Scan Logic
        $('#commenceScan').on('click', function() {
            const btn = $(this);
            const indicator = $('#scanIndicator');
            const statusText = $('#statusText');
            const extractionLog = $('#extractionLog');
            
            btn.prop('disabled', true).addClass('opacity-50 text-muted').html('<i data-lucide="refresh-cw" class="mr-2 animate-spin" style="width:16px; display:inline-block;"></i> PROBING MARKET...');
            indicator.removeClass('d-none');
            extractionLog.removeClass('d-none').html('');
            statusText.text('EXTRACTING INSTITUTIONAL DATA...');
            
            const logs = [
                "Establishing secure connection to public equity assets...",
                "Querying NYSE/NASDAQ ticker clusters...",
                "Decoding institutional market descriptors...",
                "Mapping volatility coefficients...",
                "Synchronizing meta-identities..."
            ];

            let logIndex = 0;
            const logInterval = setInterval(() => {
                if(logIndex < logs.length) {
                    extractionLog.append(`<div class="x-small text-purple-400 mb-1 opacity-75">> ${logs[logIndex]}</div>`);
                    extractionLog.scrollTop(extractionLog[0].scrollHeight);
                    logIndex++;
                } else {
                    clearInterval(logInterval);
                }
            }, 600);

            $.ajax({
                url: "{{ route('admin.stock.discovery') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    clearInterval(logInterval);
                    extractionLog.append(`<div class="x-small text-success mb-1 opacity-100">> COMPLETED: ${response.message}</div>`);
                    extractionLog.scrollTop(extractionLog[0].scrollHeight);
                    
                    statusText.html(`<span class="text-success">${response.count} ASSETS SYNCED</span>`);
                    indicator.addClass('d-none');
                    btn.html('<i data-lucide="check" class="mr-2" style="width:16px; display:inline-block;"></i> SCAN COMPLETED').removeClass('btn-outline-purple').addClass('btn-success');
                    
                    toastr.success(response.message, "Discovery Finished");
                    
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                },
                error: function() {
                    clearInterval(logInterval);
                    toastr.error("Failed to connect to discovery systems.", "Nexus Error");
                    btn.prop('disabled', false).removeClass('opacity-50').html('<i data-lucide="zap" class="mr-2" style="width:16px; display:inline-block;"></i> RETRY SCAN');
                }
            });
            lucide.createIcons();
        });
    });
</script>

<style>
    .animate-spin { animation: spin 2s linear infinite; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    .btn-outline-purple { border: 1px solid #a855f7; color: #a855f7; }
    .btn-outline-purple:hover { background: rgba(168, 85, 247, 0.1); color: #a855f7; }
    .text-purple-400 { color: #a855f7; }
    .uppercase-tracking { text-transform: uppercase; letter-spacing: 1px; }
</style>
@endsection


