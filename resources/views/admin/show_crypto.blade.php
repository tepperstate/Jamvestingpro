@extends('layouts.admin.app')
@section('content')
  <!-- Begin Page Content -->
  <div class="container-fluid">
     <a onclick="history.back()" href="javascript:void">back</a>
      <!-- Page Heading -->
      <div class="text-center justify-content-center mb-4">
         <h4 class="font-weight-bold m-0" style='color:var(--text-primary) !important'>Edit crypto site</h4>
      </div>
      <!-- Content Row -->
      <div class="row mb-4">
         <div class='col-lg-12 mb-4'>
            <div class="glass-card border-0 mb-4 shadow p-2">
               <div class="card-body">
                  <div class='row'>
                     <div class='col-lg-3'>
                        <h4 class='font-weight-bold' style='color:var(--text-primary) !important'>Edit crypto sites</h4>
                     </div>
                     <div class='col-lg-9'>
                        <form method='post' action="{{route('edit_crypto')}}" id="submit" class='row' enctype="multipart/form-data">
                           @csrf
                           <div class='col-lg-4 mt-1'>
                              <label class='font-text'>Name</label>
                              <input type='text' class='form-control' name='name' id="name" value="{{$data->name}}">
                              <input type='hidden' class='form-control' name='id' id="name" value="{{$data->id}}">

                           </div>
                           <div class='col-lg-4 mt-1'>
                              <label class='font-text'>Url</label>
                              <input type='text' class='form-control' name='url' id="name" value="{{$data->url}}">

                           </div>
                           <div class='col-lg-4 mt-1'>
                              <label class='font-text'>image</label>
                              <input type='file' class='form-control'  name='image'>
                              <img class='img-fluid' src="{{asset('storage/image/'.$data->image)}}" style='height:50px;max-height:50px;border-radius:40%'>
                           </div>
                           <div class='col-lg-6 mt-1'>
                              <label class='font-text'>Min</label>
                              <input type='text' class='form-control' name='min' id="min" value="{{$data->min}}" required>
                           </div>
                           <div class='col-lg-6 mt-1'>
                              <label class='font-text'>Max</label>
                              <input type='text' class='form-control' name='max' id="min" value="{{$data->max}}" required>
                           </div>

                           <div class='col-lg-12'>

                             <input type="submit" style="margin-left:0" class='background button-btn' style='color:#fff'>
                           </div>
                        </form>
                     </div>
                  </div>
               </div>
            </div>
         </div>
       >
      </div>
   </div>
   @if(session('status'))
      <script>
         toastr.success("{{session('status')}}","success")
      </script>
    @endif
<script>
   $(document).ready(function() {
      $('#summernote').summernote({
         placeholder: 'enter assets description',
         tabsize: 2,
         height: 200,
         toolbar: [
          ['style', ['style']],
          ['font', ['bold', 'underline', 'clear']],
          ['color', ['color']],
          ['para', ['ul', 'ol', 'paragraph']],
          ['table', ['table']],
          ['insert', ['link']],
          ['view', ['fullscreen', 'codeview', 'help']]
        ]
      });

   });
</script>
@endsection
