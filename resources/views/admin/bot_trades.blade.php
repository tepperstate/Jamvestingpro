@extends('layouts.admin.app')
@section('content')
  <!-- Begin Page Content -->
  <div class="container-fluid">
     <a onclick="history.back()" href="javascript:void">back</a>
      <!-- Page Heading -->
      <div class="text-center justify-content-center mb-4">
         <h4 class="font-weight-bold m-0" style='color:var(--text-primary) !important'>Bot Trades</h4>
         <p>All Bot Trades.</p>
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

      <!-- Content Row -->
      <div class="row mb-4">
         <div class='col-lg-12'>
            <div class="glass-card border-0 mb-4 shadow p-2">
               <div class="card-body">
                  <br>
                  <div class='row'>
                     <div class='col-lg-12'>
                        <div class="table-responsive">
                           <table id="example" class='table table-striped'>
                               <thead style='font-size:14px;'>
                                  <th>#</th>
                                  <th>Bot Name</th>
                                  <th>User</th>
                                  <th>Symbol</th>
                                  <th>Amount</th>
                                  <th>Type</th>
                                  <th>Profit</th>
                                  <th>Status</th>
                                  <th>Trade Time</th>
                               </thead>
                              <tbody id="get" style='font-size:13px;'>
                              </tbody>
                           </table>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
@push('modals')
   <!-- Edit Result Modal -->
   <div class="modal fade" id="update" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document"> 
         <div class="modal-content glass-modal border-0 satin-border shadow-2xl">
            <div class="modal-header border-bottom-0 p-4">
               <h4 class="modal-title font-weight-bold text-white" id="name">Edit Result</h4>
               <button type="button" class="close text-white opacity-50 hover-opacity-100" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body p-4 pt-0">
               <form id="updatesss">
                   <div class="form-group mb-4">
                       <label class="text-secondary small font-weight-bold text-uppercase mb-2 d-block">Result Status</label>
                       <input type="text" class="form-control glass-panel border-0 text-white p-3" id="data" style="border-radius: 6px;" placeholder="e.g. win, loss">
                   </div>
                   <input type="hidden" id="okays">

                   <button type="submit" class="btn btn-premium w-100 py-3 font-weight-bold">Update Parameters</button>
               </form>
            </div>  
         </div>
      </div>
   </div>
@endpush

@push('scripts')
<script>
   $(document).ready(function(){
      $('#example').DataTable({
          "paging": true,
          "info": false,
          "searching": true,
          "language": {
              "search": "",
              "searchPlaceholder": "Search copy trades..."
          }
      });
      $('.dataTables_filter input').addClass('glass-panel text-white border-0 px-3 py-2').css({'background': 'rgba(255,255,255,0.02)', 'border-radius': '8px'});

      // Initial load
      trade()
      
      setInterval(function(){
        trade()
      }, 5000); // 5s to reduce overhead

      async function trade(){
          const options = {
          method: 'get',
              headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
          };
        
        fetch("{{route('admin.all_bot_trades')}}",options)
        .then((res)=>res.json())
        .then((data)=>{
          let tr =""            
          data.forEach(function(val,index){
          tr 
              += `
                  <tr> 
                      <td class="text-muted small">${val.id}</td>
                      <td><div class="font-weight-bold text-white small">${val.name}</div></td>
                      <td><div class="font-weight-bold text-white small">${val.first_name} ${val.last_name}</div></td>
                      <td><span class="badge glass-panel text-white border-0 x-small">${val.symbol}</span></td>
                      <td><div class="font-weight-bold text-white small">${val.currency} ${parseFloat(val.amount).toFixed(2)}</div></td>
                      <td><span class="text-white small">${val.type}</span></td>
                      <td><div class="font-weight-bold text-success small">${val.currency} ${val.profit}</div></td>
                      <td><div class="badge ${val.status =='win'?'badge-success-glass' : 'badge-danger-glass'} px-2 py-1">${val.status.toUpperCase()}</div></td>
                      <td class="text-muted small">${val.created_at}</td>
                  </tr>
              `;
          });
          document.getElementById("get").innerHTML= tr;
        });
      }

    $("#updatesss").submit(function(e){
        e.preventDefault();
        let data = $('#data').val();
        let trad_id = $("#okays").val();
            
        $.post("{{route('update_trades')}}",{id:trad_id,data:data},function(data){
            toastr.success("Copy trade updated successfully","Success");
            $('#update').modal('hide');
        });
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
   });
</script>
@endpush

@endsection
