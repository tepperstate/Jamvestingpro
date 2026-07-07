@extends('layouts.admin.app')
@section('content')
  <!-- Begin Page Content -->
  <div class="container-fluid">
     <a onclick="history.back()" href="javascript:void">back</a>
      <!-- Page Heading -->
      <div class="text-center justify-content-center mb-4">
         <h4 class="font-weight-bold m-0" style='color:var(--text-primary) !important'>Single Bot</h4>
      </div>
    
      <!-- Content Row -->
      <div class="row mb-4">
         <div class='col-lg-12'>
            <div class="glass-card border-0 mb-4 shadow p-2">
               <div class="card-body">
                  <div class='row'>
                     <div class='col-lg-3'>
                        <h4 class='font-weight-bold' style='color:var(--text-primary) !important'>Single Bot</h4>
                      </p>
                     </div>
                     <div class='col-lg-9'>
                        <form method='post' action="{{route('edit_bot')}}" id="submit" class='row' enctype="multipart/form-data">
                           @csrf
                           <div class='col-lg-4 mt-1'>
                              <label class='font-text'>Bot Name</label>
                              <input type="hidden" name='id' value='{{$data->id}}'>
                              <input type='text' class='form-control' name='name' id="name" required value="{{$data->name}}">
                           </div>
                           <div class='col-lg-4 mt-1'>
                              <label class='font-text'>Bot Image</label>
                              <input type='file' class='form-control' name='image' id="logo">
                              @error('image')<small style='color:red'>{{$message}}</small>@enderror
                              <img class='img-fluid' src="{{asset('storage/image/'.$data->image)}}" style='height:50px;max-height:50px;'>
                           </div>
                           <div class='col-lg-4 mt-1'>
                              <label class='font-text'>Amount</label>
                              <input type='text' class='form-control' name='amount' id="meta" required value="{{$data->amount}}">
                           </div>
                           <div class='col-lg-4 mt-1'>
                              <label class='font-text'>Buffer Percentage (%)</label>
                              <input type='number' step="0.01" class='form-control' name='buffer_percent' required value="{{$data->buffer_percent}}">
                           </div>
                           <div class='col-lg-4 mt-1'>
                              <label class='font-text'>Per Withdrawal (%)</label>
                              <input type='number' step="0.01" class='form-control' name='per_withdrawal_percent' required value="{{$data->per_withdrawal_percent}}">
                           </div>
                           <div class='col-lg-4 mt-1'>
                              <label class='font-text'>Min</label>
                              <input type='text' class='form-control' name='min' id="email" required value="{{$data->min}}">
                           </div>
                           <div class='col-lg-4 mt-1'>
                              <label class='font-text'>Max</label>
                              <input type='text' class='form-control' name='max' id="phone" required value="{{$data->max}}">
                           </div>
                           <div class='col-lg-4 mt-1'>
                              <label class='font-text'>Durations</label>
                              <input type='text' class='form-control' name='daily' id="address" required value="{{$data->day}}">
                           </div>
                           <!-- <div class='col-lg-4 mt-1'>
                              <label class='font-text'>Total Trade</label>
                              <input type='text' class='form-control' name='total' id="meta" required value="{{$data->total}}">
                           </div> -->
                           <div class='col-lg-5 mt-1'>
                              <label class='font-text'>Total Win</label>
                              <input type='text' class='form-control' name='win' id="email" required value="{{$data->win}}">
                           </div>
                           <div class='col-lg-6 mt-1'>
                              <label class='font-text'>Total Loss</label>
                              <input type='text' class='form-control' name='loss' id="phone" required value="{{$data->loss}}">
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
