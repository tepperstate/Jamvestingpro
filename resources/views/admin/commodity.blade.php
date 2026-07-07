@extends('layouts.admin.app')
@section('content')

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-5">
        <div>
            <h1 class="h2 mb-1 text-white gradient-text" style="font-weight: 800;">Hardware Asset Registry</h1>
            <p class="text-muted mb-0">Manage high-liquidity commodity clusters and physical asset proxies.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
             <button onclick="history.back()" class="btn btn-sm glass-panel px-4 text-white-50 border-0" style="background: rgba(255,255,255,0.05) !important;">
                <i data-lucide="chevron-left" class="mr-2" style="width:14px; display:inline-block;"></i> Back to Command
            </button>
        </div>
    </div>

    <!-- Registration Module -->
    <div class="row mb-5">
        <div class="col-lg-12">
            <div class="glass-card satin-border p-5 shadow-2xl">
                <div class="d-flex align-items-center gap-3 mb-5">
                    <div class="icon-box glass-panel p-3" style="background: rgba(245, 158, 11, 0.1) !important; border-radius: 16px;">
                        <i data-lucide="container" class="text-warning" style="width: 28px; height: 28px;"></i>
                    </div>
                    <div>
                        <h2 class="h4 mb-1 text-white gradient-text font-weight-bold">Add Commodity</h2>
                        <p class="text-muted small mb-0">Add a new commodity asset to the platform.</p>
                    </div>
                </div>
                
                <form method='post' action="{{route('commodity_post')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-5">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="text-muted small text-uppercase font-weight-bold tracking-wider mb-2">Resource Descriptor</label>
                                <input type='text' class='form-control glass-panel border-0 text-white px-3' name='name' placeholder="e.g. Gold Bullion" required>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="text-muted small text-uppercase font-weight-bold tracking-wider mb-2">Visual Signature (Logo)</label>
                                <div class="custom-file glass-panel" style="border: 1px solid var(--glass-border); border-radius: 12px; height: auto;">
                                    <input type="file" name="image" class="custom-file-input" id="commodityImage" required>
                                    <label class="custom-file-label text-muted border-0 bg-transparent py-3" for="commodityImage" style="height: auto;">Select Image File...</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="text-muted small text-uppercase font-weight-bold tracking-wider mb-2">Ticker Symbol</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text glass-panel border-0 text-muted px-3" style="border-radius: 12px 0 0 12px;"><i data-lucide="hash" style="width:14px"></i></span>
                                    </div>
                                    <input type="text" name="symbol" class="form-control glass-panel border-0 text-white" style="border-radius: 0 12px 12px 0 !important;" placeholder="XAUUSD, BRENT" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-12 mt-4 text-right">
                            <button type="submit" class="btn btn-primary px-5 py-3 satin-border font-weight-bold" style="background: var(--accent-primary) !important; color: #ffffff !important; border-radius: 16px; border: none; font-size: 1.1rem; box-shadow: 0 10px 30px var(--accent-glow);">
                                <i data-lucide="zap" class="mr-2" style="width: 18px; display: inline-block;"></i> Add Commodity
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Active Commodities Registry -->
    <div class="glass-card satin-border overflow-hidden shadow-xl">
        <div class="p-4 d-flex justify-content-between align-items-center" style="background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--glass-border);">
           <div>
               <h3 class="h5 text-white mb-1 font-weight-bold">All Commodities</h3>
               <p class="text-muted x-small mb-0">Live status of all verified assets across the platform.</p>
           </div>
           <div class="badge badge-warning-glass px-3 py-2">LIVE ASSETS: {{count($data)}}</div>
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
                            <td class="small font-weight-bold text-muted">CMD-{{++$key}}</td>
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
                                    <a class='btn btn-sm glass-panel border-0 text-danger' href="{{route('delete_commodity',$value->id)}}">
                                        <i data-lucide="trash-2" style="width:16px"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted"> <i data-lucide="package-search" class="mb-2 opacity-30"></i> <br> No commodities added yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if(session('status'))
    <script>toastr.success("{{session('status')}}","Success")</script>
@endif

<style>
    .x-small { font-size: 10px; }
    .gap-2 { gap: 0.5rem; }
    .badge-warning-glass { background: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 8px; font-weight: 700; font-size: 0.75rem; }
    .form-control:focus { background: rgba(255,255,255,0.05) !important; color: white !important; }
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

        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
    });
</script>
@endsection

