@extends('layouts.admin.app')
@section('content')
  <!-- Begin Page Content -->
  <div class="container-fluid">
     <a onclick="history.back()" href="javascript:void">back</a>
      <!-- Page Heading -->
      <div class="text-center justify-content-center mb-4">
         <h4 class="font-weight-bold m-0" style='color:var(--text-primary) !important'>Edit Wallet</h4>
      </div>
      
      <div class="row mb-4">
         <div class='col-lg-12'>
            <div class="glass-card border-0 mb-4 shadow p-2">
               <div class="card-body">
                  <div class='row'>
                     <div class='col-lg-3'>
                        <h4 class='font-weight-bold' style='color:var(--text-primary) !important'>Edit Wallet</h4>
                      </p>
                     </div>
                     <div class='col-lg-9'>
                        <form method='post' action="{{route('edit_wallet_post')}}" id="submit" class='row' enctype="multipart/form-data">
                           @csrf
                           <div class='col-lg-4 mt-1'>
                              <label class='font-text'>Wallet Name</label>
                              <input type="hidden" name='id' value='{{$data->name}}'>
                              <input type='text' class='form-control' name='name' id="name" required value="{{$data->name}}">
                           </div>
                           <div class='col-lg-4 mt-1'>
                              <label class='font-text'>Wallet Image</label>
                              <input type='file' class='form-control' name='image' id="logo">
                              @error('image')<small style='color:red'>{{$message}}</small>@enderror
                              <img class='img-fluid' src="{{asset('storage/image/'.$data->image)}}" style='height:50px;max-height:50px;'>
                           </div>
                           <div class='col-lg-4 mt-1'>
                              <label class='font-text'>Wallet Symbol</label>
                              <input type='text' class='form-control' name='symbol' id="logo" required placeholder="BTC" value="{{$data->symbol}}">
                           </div>
                           <div class='col-lg-12 mt-1'>
                           <button id="button" class='background button-btn' style='color:#fff'>SAVE</button>
                           <div>
                        </form>
                     </div>
                     
                  </div>
               </div>
            </div>
         </div>
      </div>
      <br>
      <br>
     
      </div>
   @if(session('status'))
      <script>
         toastr.success("{{session('status')}}","success")
      </script>
    @endif

@endsection
