@extends('layouts.admin.app')
@section('content')
  <!-- Begin Page Content -->
  <div class="container-fluid">
      <!-- Page Heading -->
      <a onclick="history.back()" href="javascript:void">back</a>
      <div class="text-center justify-content-center mb-4">
         <h4 class="font-weight-bold m-0" style='color:var(--text-primary) !important'>Edit Asset</h4>
         <p>These are asset that are avalible for trade by the client.</p>
      </div>
      <!-- Content Row -->
      <div class="row mb-4">
         <div class='col-lg-12'>
            <div class="glass-card border-0 mb-4 shadow p-2">
               <div class="card-body">
                  <br>
                  <div class='row'>
                     <div class='col-lg-3'>
                        <h4 class='font-weight-bold' style='color:var(--text-primary) !important'>Edit Asset</h4>
                        <p>These are asset that are avalible for trade by the client</p>
                     </div>
                     <div class='col-lg-9'>
                        <form method='post' action="{{route('add.edit')}}" id="submit" class='row' enctype="multipart/form-data">
                           @csrf
                           <div class='col-lg-4 mt-1'>
                              <label class='font-text'>Asset Symbols</label>
                              <input type='text' class='form-control' name='name' id="name" value='{{$data->symbols}}'>
                              <input type="hidden" name='exc_id' value='{{$data->exchanges_id}}'>
                              <input type="hidden" name='id' value='{{$data->id}}'>
                           </div>
                           <div class='col-lg-4 mt-1'>
                              <label class='font-text'>Mirror Coin Symbol</label>
                              <input type='text' class='form-control' name='mirror_symbol' value='{{$data->mirror_symbol ?? ''}}' placeholder="e.g. BTCUSDT">
                           </div>
                           <div class='col-lg-4 mt-1'>
                              <label class='font-text'>Win/Loss</label>
                              <input type='text' class='form-control' name='win' id="location" required value='{{$data->profits}}'>
                           </div>
                           <div class='col-lg-4 mt-1'>
                              <label class='font-text'>Percentage(win)</label>
                              <input type='number' class='form-control' name='per' id="location" required value='{{$data->percentage}}'>
                           </div>
                           <div class='col-lg-4 mt-1'>
                              <label class='font-text'>Percentage(loss)</label>
                              <input type='number' class='form-control' name='loss' id="location" required value='{{$data->loss_percentage}}'>
                           </div>
                           <div class='col-lg-4 mt-1'>
                              <label class='font-text'>Asset Image 1</label>
                              <input type='file' class='form-control' name='image1' id="min" >
                              <img class="stacked-image" style="width:25px; height:25px;margin-left:10px;margin-top:10px" src="{{ asset('storage/image/'.$data->image1) }}" alt="">
                              @error('image1')<small style='color:red'>{{$message}}</small>@enderror
                           </div>

                           <div class='col-lg-4 mt-1'>
                              <label class='font-text'>Asset Image 2</label>
                              <input type='file' class='form-control' name='image2' id="min">
                              <img class="stacked-image" style="width:25px; height:25px;margin-left:10px;margin-top:10px" src="{{ asset('storage/image/'.$data->image2) }}" alt="">
                              @error('image2')<small style='color:red'>{{$message}}</small>@enderror

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
   $(document).ready(function(){

      $('#summernote').summernote({
         placeholder: 'enter assets description',
         value:'helo',
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

      $('#summernote2').summernote({
         placeholder: 'enter assets description two',
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

   })
</script>
@endsection
