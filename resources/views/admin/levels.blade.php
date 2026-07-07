@extends('layouts.admin.app')
@section('content')
  <!-- Begin Page Content -->
  <div class="container-fluid">
     <a onclick="history.back()" href="javascript:void">back</a>
      <!-- Page Heading -->
      <div class="text-center justify-content-center mb-4">
         <h4 class="font-weight-bold m-0" style='color:var(--text-primary) !important'>All Withdrawal Levels</h4>
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
                                  <th>S/N</th>
                                 <th>Level</th>
                                 <th>Min</th>
                                 <th>Max</th>
                                 <th>Actions</th>
                              </thead>
                              <tbody style='font-size:14px;'>

                              @foreach ($data as $key => $datas)
                                 <tr>
                                    <td>{{++$key}}</td>
                                    <td>{{$datas->plan}}</td>
                                    <td>${{number_format($datas->min)}}</td>
                                    <td>${{number_format($datas->max)}}</td>
                                    
                                    <td>
                                       <a plan="{{$datas->plan}}" min="{{$datas->min}}" max="{{$datas->max}}" did="{{$datas->id}}" data-toggle="modal" data-target="#bonus" class='btn btn-sm btn-primary' href=''>edit</a>
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

@push('modals')
   <div class="modal fade" id="bonus" tabindex="-1" role="dialog modal-xl" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-l" role="document"> 
      <div class="modal-content glass-modal border-0 shadow-lg">
         <div class="modal-header border-0 pb-0">
            <h4 class="modal-title text-white" id="name" style="font-weight: bold;">Edit Level</h4>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <form method="post" action="{{route('edit_level.levels')}}">
               @csrf
               <label style="color:var(--text-primary) !important;">Level Name</label>
               <div class="form-group mb-3">
                  <input type="text" name="plan" id="plan"  class="form-control" style="width:100%" required>
               </div>

               <label style="color:var(--text-primary) !important;">Min</label>
               <div class="form-group mb-3">
                  <input type="hidden" name="level_id" id="level_id">
                  <input type="number" name="min" id="min" class="form-control" style="width:100%" required>
               </div>

               <label style="color:var(--text-primary) !important;">Max</label>
               <div class="form-group mb-3">
                  <input type="number" name="max"  id="max" class="form-control" style="width:100%" required>
               </div>
               <div>
                  <button type="submit" name="bitcoindeposit" class="btn btn-premium w-100 py-2">Update Level</button>
               </div>
            </form>   
         </div>
      </div>
      </div>
   </div>
@endpush
   @if(session('status'))
      <script>
         toastr.success("{{session('status')}}","success")
      </script>
    @endif
<script>
   $(document).ready(function(){
      $('#example').DataTable();

      $(document).on('click','.btn-primary',function(){
         let attr = $(this).attr('did');
         let plan = $(this).attr('plan')
         let min = $(this).attr('min')
         let max = $(this).attr('max')

         $("#plan").val(plan)
         $("#min").val(min)
         $("#max").val(max)
         $("#level_id").val(attr)

      })

      $.ajaxSetup({
         headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
      });

   })
</script>
@endsection

