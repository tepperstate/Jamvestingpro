@extends('layouts.admin.app')
@section('content')
  <!-- Begin Page Content -->
  <div class="container-fluid">
      <!-- Page Heading -->
      <a onclick="history.back()" href="javascript:void">back</a>
      <div class="text-center justify-content-center mb-4">
         <h4 class="font-weight-bold m-0" style='color:var(--text-primary) !important'>Edit Vidoes</h4>
      </div>
      <!-- Content Row -->
      <div class="row mb-4">
         <div class='col-lg-12'>
            <div class="glass-card border-0 mb-4 shadow p-2">
               <div class="card-body">
                  <br>
                  <div class='row'>
                     <div class='col-lg-3'>
                        <h4 class='font-weight-bold' style='color:var(--text-primary) !important'>Edit Vidoes</h4>
                     </div>
                     <div class='col-lg-9'>
                        <form method='post' action="{{route('upload_videos')}}" id="submit" class='row' enctype="multipart/form-data">
                           @csrf
                           <div class='col-lg-6 mt-1'>
                              <label class='font-text'>Name</label>
                              <input type='text' class='form-control' name='title' id="name" value='{{$data->title}}'>
                              <input type="hidden" name='id' value='{{$data->id}}'>
                           </div>
                           <div class='col-lg-6 mt-1'>
                              <label class='font-text'>YouTube Video ID<</label>
                              <input type='text' class='form-control' name='link' id="cat" required value='{{$data->vidoes}}'>
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
   </div>
   @if(session('status'))
      <script>
         toastr.success("{{session('status')}}","success")
      </script>
    @endif
<script>
   $(document).ready(function(){

   })
</script>
@endsection
