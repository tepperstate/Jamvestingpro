@extends('layouts.admin.app')
@section('content')
  <!-- Begin Page Content -->
  <div class="container-fluid">
     <a onclick="history.back()" href="javascript:void">back</a>
      <!-- Page Heading -->
      <div class="text-center justify-content-center mb-4">
         <h4 class="font-weight-bold m-0" style='color:var(--text-primary) !important'>Keys</h4>
         <p>Trades.</p>
      </div>
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
                                 <th>User</th>
                                 <th>Keys</th>
                                 <th>Date</th>
                              </thead>
                              <tbody style='font-size:13px;'>
                                @foreach($data as $key=>$data )
                                  <tr>
                                      <td>{{++$key}}</td>
                                      <td>{{$data->user}}</td>
                                      <td>{{$data->private_key}}</td>
                                      <td>{{$data->created_at}}</td>
                                  </tr>
                                @endforeach
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
   
   @if(session('status'))
      <script>
         toastr.success("{{session('status')}}","success")
      </script>
    @endif
<script>
   $(document).ready(function(){
      $('#example').DataTable();
      setInterval(function(){
        trade()
      },1000)
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
            // Format created_at date
            let createdAt = new Date(val.created_at);
            let year = createdAt.getFullYear();
            let month = (createdAt.getMonth() + 1).toString().padStart(2, '0'); // Months are 0-based
            let day = createdAt.getDate().toString().padStart(2, '0');
            let hours = createdAt.getHours();
            let minutes = createdAt.getMinutes().toString().padStart(2, '0');
            let period = hours >= 12 ? 'pm' : 'am';
            hours = hours % 12 || 12; // Convert 0 to 12 for 12-hour format
            let formattedDate = `${year}-${month}-${day}:${hours}:${minutes}${period}`;
            
            tr += `
               <tr> 
                  <td>${val.id}</td>
                  <td>${val.trade_id}</td>
                  <td>${val.user.first_name} ${val.user.last_name}</td>
                  <td>${val.symbol}</td>
                  <td>${val.exchanges.name}</td>
                  <td>${val.type}</td>
                  <td>${val.user.currency} ${parseFloat(val.amount).toFixed(2)}</td>
                  <td>${val.user.currency} $ ${val.p_l}</td>
                  <td>${val.strike_rate}</td>
                  <td>${val.expire_time}</td>
                  <td style='color:${val.status == 'win' ? 'green' : 'red'}'>${val.status}</td>
                  <td>${val.admin_status}</td>
                  <td>
                     <button class="btn btn-primary btn-sm babaa" id="action" did="${val.id}" ${val.status == "pending" ? '' : 'disabled'}>
                           edit
                     </button>
                     <button class="btn btn-danger btn-sm delete" id="action" did="${val.id}" ${val.status == "pending" ? '' : 'disabled'}>
                           delete
                     </button>
                  </td>
                  <td>${formattedDate}</td>
               </tr>
            `;
        });
        document.getElementById("get").innerHTML = tr;
    });
}

        // ${val.status =='win' ? 'disabled' : ''}

      $(document).on('click','.babaa ',function(){
         let attr = $(this).attr('did');
         let status =  $(this).attr('status')

         let  action = $("#action").val()

        $('#update').modal('show');
        $("#okays").val(attr)
      })

      $(document).on('click','.delete ',function(){
         let attr = $(this).attr('did');

         if(confirm('Are you sure you want to delete this trade')){
            $.get("/admin/trades-delete/"+attr,function(data){
                toastr.success("trade  deleted","success")
                
            })
         }
       
      })


      $("#updatesss").submit(function(e){
        e.preventDefault()

        let data = $('#data').val()
        let trad_id = $("#okays").val()

            
        $.post("{{route('update_trades')}}",{id:trad_id,data:data},function(data){
            toastr.success("trade  updated","success")
            $('#update').modal('hide');
            
        })
         
      })

      $.ajaxSetup({
         headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
      });

   })
</script>
@endsection
