@extends('layouts.admin.app')
@section('title', 'User Directory')

@section('content')

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-5">
        <div>
            <h1 class="h2 mb-1 text-white gradient-text" style="font-weight: 800;">User Directory</h1>
            <p class="text-muted mb-0">Manage all registered users and their platform access.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
             <div class="badge badge-success-glass px-4 py-2 border-0 satin-border" style="background: rgba(16, 185, 129, 0.1) !important; border: 1px solid rgba(16, 185, 129, 0.2) !important;">
                <span class="text-success small font-weight-bold" style="color: #34d399 !important;">ACTIVE ACCOUNTS: {{count($data)}}</span>
            </div>
            <button onclick="window.location.reload()" class="btn btn-primary glass-panel border-0 px-4 py-2 satin-border" style="background: rgba(59, 130, 246, 0.1) !important; border: 1px solid rgba(59, 130, 246, 0.2) !important; color: #60a5fa !important; font-weight: 700;">
                <i data-lucide="refresh-cw" class="mr-2" style="width: 16px; display: inline-block; vertical-align: middle;"></i> Refresh Table
            </button>
        </div>
    </div>

    <!-- Session Feedback -->
    @if(session('success'))
        <div class="alert alert-success-glass satin-border mb-4 d-flex align-items-center justify-content-between shadow-lg" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: 12px; padding: 1rem 1.5rem; backdrop-filter: blur(10px);">
            <div class="d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center mr-3 shadow-sm" style="width: 32px; height: 32px; background: rgba(16, 185, 129, 0.2) !important; border: 1px solid rgba(16, 185, 129, 0.5);">
                    <i data-lucide="check" class="text-success" style="width: 18px; color: #34d399 !important;"></i>
                </div>
                <span class="text-white font-weight-bold" style="text-shadow: 0 1px 2px rgba(0,0,0,0.5);">{{ session('success') }}</span>
            </div>
            <button type="button" class="close text-white" data-dismiss="alert" aria-label="Close" style="opacity: 0.5;">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger-glass satin-border mb-4 d-flex align-items-center justify-content-between" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 12px; padding: 1rem 1.5rem;">
            <div class="d-flex align-items-center">
                <div class="bg-danger rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 32px; height: 32px; background: #ef4444 !important;">
                    <i data-lucide="alert-circle" class="text-white" style="width: 18px;"></i>
                </div>
                <span class="text-white font-weight-bold">{{ session('error') }}</span>
            </div>
            <button type="button" class="close text-white" data-dismiss="alert" aria-label="Close" style="opacity: 0.5;">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Main User Registry -->
    <div class="glass-card satin-border overflow-hidden shadow-2xl">
        <div class="p-4 d-flex justify-content-between align-items-center" style="background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--glass-border);">
            <div>
                <h3 class="h5 text-white mb-1 font-weight-bold">User Management</h3>
                <p class="text-muted x-small mb-0">Active platform users with registered accounts.</p>
            </div>
            <div class="d-flex gap-2">
                 <button class="btn btn-sm glass-panel border-0 text-white px-3 py-2"><i data-lucide="filter" class="mr-1" style="width:14px; display:inline-block;"></i> Advanced Filter</button>
            </div>
        </div>
        <div class="table-responsive">
            <table id="example" class="table text-white">
                <thead>
                    <tr>
                        <th>USER</th>
                        <th>EMAIL ADDRESS</th>
                        <th>COUNTRY</th>
                        <th>VERIFICATION</th>
                        <th>ONBOARDED</th>
                        <th class="text-right">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $key => $datas)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar glass-panel mr-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; border-radius: 14px; background: rgba(255, 51, 51, 0.05); border: 1px solid rgba(255, 51, 51, 0.1);">
                                        {{ substr($datas->first_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-weight-bold text-white h6 mb-0">{{$datas->first_name}} {{$datas->last_name}}</div>
                                        <div class="text-muted x-small text-uppercase">USER#{{$datas->id}}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-white small">{{$datas->email}}</div>
                                <div class="text-muted x-small">KEY: {{$datas->show_password}}</div>
                            </td>
                            <td>
                                <div class="small d-flex align-items-center gap-1">
                                    <i data-lucide="map-pin" class="text-muted" style="width:12px"></i> {{$datas->country}}
                                </div>
                                <div class="text-muted x-small">{{$datas->phone}}</div>
                            </td>
                            <td>
                                @if($datas->email_verified == 1)
                                    <span class="badge px-3 py-2" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; border-radius: 8px; font-size: 0.7rem; font-weight: 800; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.2);">VERIFIED</span>
                                @else
                                    <span class="badge px-3 py-2" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; border-radius: 8px; font-size: 0.7rem; font-weight: 800; box-shadow: 0 2px 8px rgba(239, 68, 68, 0.2);">UNVERIFIED</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{$datas->created_at->format('M d, Y')}}</td>
                            <td class="text-right">
                                <div class="d-flex justify-content-end gap-2">
                                    <a title="Inspect Profile" class='btn btn-sm glass-panel text-white border-0' href='{{route("admin.user.single",$datas->id)}}'><i data-lucide="scan-eye" style="width:16px"></i></a>
                                    <a title="Login as User" target="_blank" rel="noopener noreferrer" class='btn btn-sm glass-panel border-0 text-info' href='{{route("loginUsernow",$datas->id)}}'><i data-lucide="shield-user" style="width:16px"></i></a>
                                    <a title="Delete User" onclick="return confirm('WARNING: This will permanently purge this user and ALL their financial records (Balances, Orders, Trades). This action is IRREVERSIBLE. Proceed?')" href='{{route("admin.user.delete",$datas->id)}}' class='btn btn-sm glass-panel border-0 text-danger'><i data-lucide="user-minus" style="width:16px"></i></a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-top" style="border-color: var(--glass-border) !important;">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="text-muted small">
                    Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of {{ $data->total() }} users
                </div>
                <div class="glass-pagination">
                    {{ $data->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Glass Pagination Styling */
    .glass-pagination .pagination { margin-bottom: 0; gap: 5px; }
    .glass-pagination .page-item .page-link {
        background: rgba(255,255,255,0.03);
        border: 1px solid var(--glass-border);
        color: var(--text-muted);
        border-radius: 8px;
        padding: 8px 16px;
        transition: all 0.2s;
    }
    .glass-pagination .page-item.active .page-link {
        background: var(--accent-primary) !important;
        color: white !important;
        border-color: var(--accent-primary);
    }
    .glass-pagination .page-item.disabled .page-link {
        background: rgba(255,255,255,0.01);
        color: rgba(255,255,255,0.1);
    }
    .glass-pagination .page-link:hover {
        background: rgba(255,255,255,0.1);
        color: white;
    }
    
    .x-small { font-size: 10px; }
    .tracking-wider { letter-spacing: 0.1em; }
    .gap-2 { gap: 0.5rem; }
    .avatar { font-family: 'Outfit', sans-serif; font-weight: 800; color: var(--accent-primary); font-size: 1.2rem; }
    #wrapper #content-wrapper #content { background: transparent !important; }
    
    .table-hover tbody tr { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
    .table-hover tbody tr:hover { 
        background: rgba(255,255,255,0.04) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        position: relative;
        z-index: 10;
    }
    .bg-success { background-color: #10b981 !important; }
    .bg-danger { background-color: #ef4444 !important; }
</style>

@if(session('status'))
    <script>toastr.success("{{session('status')}}","Success")</script>
@endif

<script>
    $(document).ready(function(){
        $('#example').DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "order": [[4, "desc"]],
            "info": false,
            "autoWidth": false,
            "responsive": true,
            "pageLength": 100,
            "language": {
                "search": "",
                "searchPlaceholder": "Search users..."
            }
        });
        $('.dataTables_filter input').addClass('glass-panel text-white border-0 px-4 py-2').css({'background': 'rgba(255,255,255,0.02)', 'width': '250px', 'border-radius': '12px'});
        lucide.createIcons();
        
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
    })
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
@endsection

