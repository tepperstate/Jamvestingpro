@extends('layouts.admin.app')
@section('content')
  <!-- Begin Page Content -->
  <div class="container-fluid">
     <a onclick="history.back()" href="javascript:void">back</a>
      <!-- Page Heading -->
      <div class="text-center justify-content-center mb-4">
         <h4 class="font-weight-bold m-0" style='color:var(--text-primary) !important'>Payment Settings</h4>
      </div>
      <!-- Content Row -->
      <div class="row mb-4">
         <div class='col-lg-12 mb-3'>
            <div class="glass-card border-0 mb-4 shadow p-2">
               <div class="card-body">
                  <div class='row'>
                     <div class='col-lg-3'>
                        <h4 class='font-weight-bold' style='color:var(--text-primary) !important'>Edit Payment Settings</h4>
                     </div>
                     <div class='col-lg-9'>
                        <form method='post' action="{{route('payment.updated')}}" id="submit" class='row' >
                           @csrf
                           <div class='col-lg-6 mt-1'>
                              <label class='font-text'>Private Key</label>
                              <input type='text' class='form-control' name='private' id="name" value="{{$data[0]->private_key}}" required>
                           </div>
                           <div class='col-lg-6 mt-1'>
                              <label class='font-text'>Public Key</label>
                              <input type='text' class='form-control' name='public' id="email" value="{{$data[0]->public_key}}" required>
                           </div>
                           <div class='col-lg-6 mt-1'>
                              <label class='font-text'>COINPAYMENT_MARCHANT_ID</label>
                              <input type='text' class='form-control' name='merchant_id' id="password"  value="{{$data[0]->marchant_id}}"required>
                           </div>
                           <div class='col-lg-6 mt-1'>
                              <label class='font-text'>COINPAYMENT_IPN_SECRET</label>
                              <input type='text' class='form-control' name='ipn_secret' id="password" value="{{$data[0]->ipn_secret}}" required>
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
@endsection
