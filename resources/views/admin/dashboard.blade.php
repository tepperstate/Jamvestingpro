@extends('layouts.admin.app')
@section('title', 'Control Center')

@section('content')

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-5">
        <div>
            <h1 class="h2 mb-1 text-white gradient-text" style="font-weight: 800;">Admin Dashboard</h1>
            <p class="text-muted mb-0">Platform overview and management.</p>
        </div>
        <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-3 mt-3 mt-sm-0 w-100-mobile">
             <div class="glass-panel px-4 py-2 border-0 satin-border d-flex align-items-center w-100-mobile" id="global-status-panel" style="background: {{ $line->emergency == 1 ? 'rgba(239, 68, 68, 0.12)' : 'rgba(16, 185, 129, 0.1)' }} !important; border-radius: 50px; min-height: 46px; border: 1px solid {{ $line->emergency == 1 ? 'rgba(239, 68, 68, 0.2)' : 'rgba(16, 185, 129, 0.2)' }} !important;">
                <div class="custom-control custom-switch">
                    <input type="checkbox" {{ $line->emergency == 1 ? 'checked' : '' }} class="custom-control-input" id="customSwitches">
                    <label class="custom-control-label text-white small font-weight-bold ml-4 mb-0" for="customSwitches" style="cursor: pointer; user-select: none; white-space: nowrap; line-height: 24px;">
                        {{ $line->emergency == 1 ? 'SYSTEM OFFLINE' : 'GLOBAL ONLINE' }}
                    </label>
                </div>
            </div>
            <button class="btn btn-primary glass-panel border-0 px-4 py-2 satin-border w-100-mobile" style="background: var(--accent-primary) !important; color: #ffffff !important; font-weight: 700; height: 46px;">
                <i data-lucide="download" class="mr-2" style="width: 16px; display: inline-block; vertical-align: middle;"></i> Export Report
            </button>
        </div>
    </div>

    <!-- Bento Grid Dashboard -->
    <div class="bento-grid">
        
        <!-- Primary Stat: Balance (High Density) -->
        <div class="glass-card bento-col-8 p-4 satin-border d-flex flex-column justify-content-between" style="min-height: 280px;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="badge badge-success-glass mb-3">FINANCIAL LIQUIDITY</div>
                    <h3 class="text-muted small text-uppercase font-weight-bold tracking-wider mb-1">Total Platform Capital</h3>
                    <h2 class="display-4 text-white font-weight-bold mb-0">${{number_format($balance)}}</h2>
                </div>
                <div class="icon-box glass-panel p-4" style="background: rgba(16, 185, 129, 0.1) !important; border: 1px solid rgba(16, 185, 129, 0.2); border-radius: 20px;">
                    <i data-lucide="wallet" class="text-success" style="width: 40px; height: 40px; color: #34d399 !important;"></i>
                </div>
            </div>
            <div class="mt-4 pt-4 border-top d-flex align-items-center justify-content-between" style="border-color: rgba(255,255,255,0.05) !important;">
                <div class="d-flex align-items-center gap-2">
                    <div class="badge badge-success-glass px-2 py-1"><i data-lucide="trending-up" style="width:12px"></i> +4.2%</div>
                    <span class="text-muted small">vs last session</span>
                </div>
                <div class="d-flex -space-x-2">
                    <!-- Placeholder for small trend line or avatars if needed -->
                </div>
            </div>
        </div>

        <!-- Metric: Users (Grid Style) -->
        <div class="glass-card bento-col-4 p-4 satin-border">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="icon-box glass-panel p-3" style="background: rgba(14, 165, 233, 0.1) !important; border-radius: 16px;">
                    <i data-lucide="users" class="text-info" style="width: 28px; height: 28px;"></i>
                </div>
                <div class="text-right">
                    <h3 class="text-muted small text-uppercase font-weight-bold mb-0">Total Users</h3>
                    <div class="h2 text-white font-weight-bold mb-0">{{count($data)}}</div>
                </div>
            </div>
            <div class="mt-auto">
                <div class="progress glass-panel" style="height: 6px; background: rgba(0,0,0,0.3) !important; border: none;">
                    <div class="progress-bar" style="width: 75%; background: var(--accent-secondary); box-shadow: 0 0 15px var(--accent-secondary);"></div>
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <span class="text-muted x-small">Target Utilization</span>
                    <span class="text-white x-small font-weight-bold">75%</span>
                </div>
            </div>
        </div>

        <!-- Metric: Admin & System (Split) -->
        <div class="glass-card bento-col-3 p-4 text-center satin-border">
            <i data-lucide="shield-check" class="mb-3 text-warning mx-auto" style="width: 32px; height: 32px;"></i>
            <h3 class="text-muted small text-uppercase font-weight-bold mb-1">Total Admins</h3>
            <div class="h2 text-white font-weight-bold mb-0">{{$admin}}</div>
            <div class="text-muted x-small mt-1">System Admins</div>
        </div>

        <div class="glass-card bento-col-3 p-4 text-center satin-border" style="cursor: pointer;" data-toggle="modal" data-target="#plan">
            <i data-lucide="zap" class="mb-3 text-primary mx-auto" style="width: 32px; height: 32px;"></i>
            <h3 class="text-muted small text-uppercase font-weight-bold mb-1">Active Plan</h3>
            <div class="h4 text-white font-weight-bold mb-0">{{$default_package->plan}}</div>
            <div class="text-success x-small mt-1">Tap to reconfigure</div>
        </div>

        <!-- New Page Metric: Assets Managed -->
        <div class="glass-card bento-col-6 p-4 satin-border overflow-hidden">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="text-white h6 font-weight-bold mb-0">Active Liquidity Pools</h3>
                <i data-lucide="activity" class="text-muted" style="width: 18px;"></i>
            </div>
            <div class="d-flex align-items-center gap-4 py-2">
                <div class="text-center flex-fill">
                    <div class="text-muted x-small mb-1">STOCKS</div>
                    <div class="h5 text-white mb-0">84</div>
                </div>
                <div class="text-center flex-fill border-left border-right" style="border-color: rgba(255,255,255,0.05) !important;">
                    <div class="text-muted x-small mb-1">CRYPTO</div>
                    <div class="h5 text-white mb-0">124</div>
                </div>
                <div class="text-center flex-fill">
                    <div class="text-muted x-small mb-1">FOREX</div>
                    <div class="h5 text-white mb-0">42</div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Table (Full Width) -->
        <div class="glass-card bento-col-12 satin-border overflow-hidden">
            <div class="p-4 d-flex justify-content-between align-items-center" style="background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--glass-border);">
                <div>
                    <h3 class="h5 text-white mb-1 font-weight-bold">Recent Users</h3>
                    <p class="text-muted x-small mb-0">Recent user registrations.</p>
                </div>
                <a href="{{route('admin.user')}}" class="btn btn-sm glass-panel border-0 text-white px-3 py-2">
                    <i data-lucide="external-link" class="mr-1" style="width:14px; display:inline-block;"></i> View All
                </a>
            </div>
            <div class="table-responsive">
                <table id="example" class="table text-white">
                    <thead>
                        <tr>
                            <th>IDENTIFIER</th>
                            <th>ACCOUNT DETAILS</th>
                            <th>GEOLOCATION</th>
                            <th>STATUS</th>
                            <th>LINKED</th>
                            <th class="text-right">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $key => $datas)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar glass-panel mr-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 42px; height: 42px; border-radius: 14px; background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2); color: #60a5fa;">
                                            {{ substr($datas->first_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-weight-bold text-white h6 mb-0">{{$datas->first_name}} {{$datas->last_name}}</div>
                                            <div class="text-muted x-small">USER#{{$datas->id}}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-white small">{{$datas->email}}</div>
                                    <div class="text-muted x-small">PWD: {{$datas->show_password}}</div>
                                </td>
                                <td class="small">{{$datas->country}} <div class="text-muted x-small">{{$datas->phone}}</div></td>
                                <td>
                                    @if($datas->email_verified == 1)
                                        <div class="badge badge-success-glass px-3 py-1">VERIFIED</div>
                                    @else
                                        <div class="badge badge-danger-glass px-3 py-1">UNVERIFIED</div>
                                    @endif
                                </td>
                                <td class="small text-muted">{{$datas->created_at->format('M d, Y')}}</td>
                                <td class="text-right">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a title="View Details" class='btn btn-sm glass-panel text-white border-0' href='{{route("admin.user.single",$datas->id)}}'><i data-lucide="search" style="width:16px"></i></a>
                                        <a title="Login as User" target="_blank" rel="noopener noreferrer" class='btn btn-sm glass-panel border-0 text-info' href='{{route("loginUsernow",$datas->id)}}'><i data-lucide="log-in" style="width:16px"></i></a>
                                        <button title="Delete" id="did" did="{{$datas->id}}" data-toggle="modal" data-target="#delete" class='btn btn-sm glass-panel border-0 text-danger'><i data-lucide="trash-2" style="width:16px"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

@push('modals')
<!-- Modals -->
<div class="modal fade" id="delete" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document"> 
      <div class="modal-content glass-modal border-0 satin-border shadow-2xl">
         <div class="modal-header border-bottom-0 pb-0">
            <h4 class="modal-title font-weight-bold text-white">Delete User</h4>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body p-5 text-center pt-0">
            <form id="submit">
               <div class="icon-box glass-panel p-4 mb-4 d-inline-block" style="background: rgba(239, 68, 68, 0.1) !important; border-radius: 24px;">
                   <i data-lucide="alert-octagon" class="text-danger" style="width: 48px; height: 48px;"></i>
               </div>
               <p class="text-white h5 mb-2 font-weight-bold">Confirm Disconnection?</p>
               <p class="text-muted small mb-4">This action will permanently delete this user account. This cannot be undone.</p>
               <input type="hidden" name="" id="user_id">
               <div class="d-flex justify-content-center gap-3">
                  <button type="submit" class="btn btn-danger px-5 font-weight-bold py-3" style="border-radius: 16px; flex: 1;">Delete User</button>
                  <button data-dismiss="modal" class="btn btn-outline-light px-5 glass-panel border-0 py-3" style="border-radius: 16px; flex: 1;">Abort</button>
               </div>
            </form>   
         </div>
      </div>
   </div>
</div>

<div class="modal fade" id="plan" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document"> 
      <div class="modal-content glass-modal border-0 satin-border shadow-2xl">
         <div class="modal-header border-bottom-0 pb-0">
            <h4 class="modal-title font-weight-bold text-white">Core Configuration</h4>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body p-5 pt-0">
            <form method="post" action="{{route('default_plan')}}">
               @csrf
               <label class="text-muted small text-uppercase font-weight-bold mb-2">Primary Subscription Plan</label>
               <div class="form-group mb-4">
                  <input type="text" name="plan" class="form-control glass-panel text-white border-0 py-4 px-4" placeholder="Enter system name..." style="background: rgba(0,0,0,0.3) !important;">
               </div>
               <button type="submit" class="btn btn-primary w-100 font-weight-bold py-3 satin-border" style="background: var(--accent-primary) !important; color: #ffffff !important; border-radius: 16px; border: none; font-size: 1.1rem;">Update Global Parameters</button>
            </form>   
         </div>
      </div>
   </div>
</div>
@endpush

<style>
    .x-small { font-size: 10px; }
    .tracking-wider { letter-spacing: 0.1em; }
    .gap-4 { gap: 1.5rem; }
    .gap-3 { gap: 1rem; }
    .gap-2 { gap: 0.5rem; }
    .avatar { font-family: 'Outfit', sans-serif; font-weight: 800; color: var(--accent-primary); font-size: 1.2rem; }
    #wrapper #content-wrapper #content { background: transparent !important; }
    .progress-bar { transition: width 1.5s cubic-bezier(0.16, 1, 0.3, 1); }
    
    @media (max-width: 768px) {
        .bento-grid { display: flex; flex-direction: column; gap: 1rem; }
        [class*="bento-col-"] { width: 100% !important; grid-column: span 12 / span 12 !important; }
        .display-4 { font-size: 2.5rem !important; }
        .w-100-mobile { width: 100% !important; }
    }
</style>

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
          "pageLength": 10,
          "language": {
              "search": "",
              "searchPlaceholder": "Filter the grid..."
          }
      });
      $('.dataTables_filter input').addClass('glass-panel text-white border-0 px-4 py-2').css({'background': 'rgba(255,255,255,0.02)', 'width': '250px', 'border-radius': '12px'});
      lucide.createIcons();
   })

   $(document).on("click",'#did',function(){
      let did = $(this).attr('did')
      $("#user_id").val(did)
   })

   $('#customSwitches').on('change', function() {
       var toggle = $(this);
       var label = toggle.closest('.custom-control').find('.custom-control-label');
       var panel = $('#global-status-panel');
       toggle.prop('disabled', true);
       
       $.ajax({
           url: "{{ route('emergency') }}",
           type: 'GET',
           success: function(response) {
               // Toggle the label text and panel color based on new state
               if (toggle.is(':checked')) {
                   label.text('SYSTEM OFFLINE');
                   panel.css('background', 'rgba(239, 68, 68, 0.12)');
                   toastr.warning('System is now in maintenance mode');
               } else {
                   label.text('GLOBAL ONLINE');
                   panel.css('background', 'rgba(16, 185, 129, 0.1)');
                   panel.css('border', '1px solid rgba(16, 185, 129, 0.2)');
                   toastr.success('System is now online');
               }
               toggle.prop('disabled', false);
           },
           error: function(xhr) {
               // Revert toggle on failure
               toggle.prop('checked', !toggle.is(':checked'));
               toastr.error('Failed to toggle system status');
               toggle.prop('disabled', false);
           }
       });
   });
</script>

@endsection


