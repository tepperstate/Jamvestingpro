@extends('layouts.admin.app')
@section('content')
  <!-- Begin Page Content -->
  <div class="container-fluid">
     <a onclick="history.back()" href="javascript:void">back</a>
      <!-- Page Heading -->
      <div class="text-center justify-content-center mb-4">
         <h4 class="font-weight-bold m-0" style='color:var(--text-primary) !important'>All Trading Assets</h4>
         <p>These are asset that are avalible for trade by the client.</p>
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
                                 <th>Symbol</th>
                                 <th>Percentage win or loss(%)</th>
                                 <th>Win or Loss</th>
                                 <th>Action</th>
                              </thead>
                              <tbody style='font-size:14px;'>

                              @foreach ($data as $datas)
                                 <tr>
                                    <td>{{$datas->symbols}}</td>
                                    <td>{{$datas->percentage}}</td>
                                    <td>{{$datas->profits}}</td>
                                    <td>
                                       <a class='btn btn-sm btn-primary' href='single/{{$datas->id}}/{{$datas->exchanges_id}}'>edit</a>
                                       <a class='btn btn-sm btn-danger' href='delete/{{$datas->id}}/{{$datas->exchanges_id}}'>delete</a>

                                    </td>
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



      $.ajaxSetup({
         headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
      });

   })
</script>
@endsection
