@extends('layouts.admin.app')
@section('content')
  <!-- Begin Page Content -->
  <div class="container-fluid">
      <!-- Page Heading -->
      <a onclick="history.back()" href="javascript:void">back</a>
      <div class="text-center justify-content-center mb-4">
         <h4 class="font-weight-bold m-0" style='color:var(--text-primary) !important'>Edit Asset</h4>
      </div>
      <!-- Content Row -->
      <div class="row mb-4">
         <div class='col-lg-12'>
            <div class="glass-card border-0 mb-4 shadow p-2">
               <div class="card-body">
                  <br>
                  <div class='row'>
                     <div class='col-lg-3'>
                        <h4 class='font-weight-bold' style='color:var(--text-primary) !important'>Edit Category</h4>
                     </div>
                     <div class='col-lg-9'>
                        <form method='post' action="{{route('editCat')}}" id="submit" class='row' enctype="multipart/form-data">
                           @csrf
                           <div class='col-lg-5 mt-1'>
                              <label class='font-text'>Category</label>
                              <input type='text' class='form-control' name='name' id="name" value='{{$data->name}}'>
                              <input type="hidden" name='id' value='{{$data->id}}'>
                           </div>
                           
                           <div class='col-lg-12'>
                            </div>
                           <button id="button" class='background button-btn' style='color:#fff'>SAVE</button>
                        </form>
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

</script>
@endsection
