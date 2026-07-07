@extends('layouts.admin.app')
@section('content')
  <!-- Begin Page Content -->
  <div class="container-fluid">
     <a onclick="history.back()" href="javascript:void">back</a>
      <!-- Page Heading -->
      <div class="text-center justify-content-center mb-4">
         <h4 class="font-weight-bold m-0" style='color:var(--text-primary) !important'>All Copy Traders</h4>
         <p>All Copy Traders.</p>
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
                                 <th>Name</th>
                                 <th>Country</th>
                                 <th>Roi</th>
                                 <th>Trade amount</th>
                                 <th>Win or Loss</th>
                                 <th>(Min Profit / Max Profit)</th>
                                 <th>(Min loss / Max loss)</th>
                                 <th>Traders</th>
                                 <th>Action</th>
                              </thead>
                              @foreach ($data as $datas)
                                 <tbody style='font-size:14px;'>
                                    <td>{{$datas->name}}</td>
                                    <td>{{$datas->country}}</td>
                                    <td>{{$datas->percentage}}</td>
                                    <td>${{number_format($datas->amount)}}</td>
                                    <td>{{$datas->win}}</td>
                                    <td>{{number_format($datas->min_win)}} {{number_format($datas->max_win)}}</td>
                                    <td>{{number_format($datas->min_loss)}} {{number_format($datas->max_loss)}}</td>

                                    <td>{{$datas->total_copier}} traders</td>
                                    <td>
                                       <a class='btn btn-sm btn-primary' href="{{route('add.copy_show',$datas->id)}}">edit</a>
                                       <a class='btn btn-sm btn-danger' did="{{$datas->id}}" href="javascipt:void(0)">delete</a>
                                    </td>
                                 </tbody>
                              @endforeach
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

      $(document).on('click','.btn-danger',function(){
         let attr = $(this).attr('did');


         if(confirm('Are sure you want to proceed')){
            
            $.post("{{route('add.copy_delete')}}",{id:attr,status:status},function(data){
               
               toastr.success("assets status deleted","success")
               setTimeout(function(){
                  location.reload()
               },3000)
            })
         }
      })

      $.ajaxSetup({
         headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
      });
   })
</script>
@endsection
