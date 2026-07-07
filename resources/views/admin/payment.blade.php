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
                        <h4 class='font-weight-bold' style='color:var(--text-primary) !important'>Gateway Settings</h4>
                        <p class="text-secondary small">Enable or disable payment methods across the platform.</p>
                     </div>
                     <div class='col-lg-9'>
                        <form method='post' action="{{route('payment.updated')}}" id="submit" class='row' >
                           @csrf
                           
                           <!-- Toggles -->
                           <div class="col-12 mb-4 p-3 rounded" style="background: rgba(255,255,255,0.05);">
                              <h5 class="mb-3" style="color:var(--text-primary)">Active Payment Gateways</h5>
                              
                              <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" role="switch" name="is_manual_crypto_enabled" id="manual_crypto" {{ $data[0]->is_manual_crypto_enabled ? 'checked' : '' }}>
                                <label class="form-check-label font-text" for="manual_crypto">Manual Crypto Deposits (Direct to Wallet + Auto-Scanner)</label>
                              </div>
                              
                              <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" role="switch" name="is_nowpayments_enabled" id="nowpayments_crypto" {{ $data[0]->is_nowpayments_enabled ? 'checked' : '' }}>
                                <label class="form-check-label font-text" for="nowpayments_crypto">NowPayments (Crypto API)</label>
                              </div>
                              
                              <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" role="switch" name="is_nowpayments_card_enabled" id="nowpayments_card" {{ $data[0]->is_nowpayments_card_enabled ? 'checked' : '' }}>
                                <label class="form-check-label font-text" for="nowpayments_card">NowPayments (Fiat/Card via API)</label>
                              </div>
                              
                              <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" role="switch" name="is_oxapay_enabled" id="oxapay_crypto" {{ $data[0]->is_oxapay_enabled ? 'checked' : '' }}>
                                <label class="form-check-label font-text" for="oxapay_crypto">OxaPay (Crypto API)</label>
                              </div>
                           </div>

                           <hr class="w-100 bg-secondary" style="opacity: 0.2;">

                           <!-- Legacy CoinPayments -->
                           <div class="col-12 mt-2"><h5 class="font-weight-bold" style="color:var(--text-primary)">Legacy CoinPayments (Deprecated)</h5></div>
                           <div class='col-lg-6 mt-1'>
                              <label class='font-text'>Private Key</label>
                              <input type='text' class='form-control' name='private' value="{{$data[0]->private_key}}">
                           </div>
                           <div class='col-lg-6 mt-1'>
                              <label class='font-text'>Public Key</label>
                              <input type='text' class='form-control' name='public' value="{{$data[0]->public_key}}">
                           </div>
                           <div class='col-lg-6 mt-1'>
                              <label class='font-text'>COINPAYMENT_MARCHANT_ID</label>
                              <input type='text' class='form-control' name='merchant_id' value="{{$data[0]->marchant_id}}">
                           </div>
                           <div class='col-lg-6 mt-1'>
                              <label class='font-text'>COINPAYMENT_IPN_SECRET</label>
                              <input type='text' class='form-control' name='ipn_secret' value="{{$data[0]->ipn_secret}}">
                           </div>

                           <hr class="w-100 mt-4 bg-secondary" style="opacity: 0.2;">
                           
                           <!-- NowPayments Settings -->
                           <div class="col-12 mt-2"><h5 class="font-weight-bold" style="color:var(--text-primary)">NowPayments API Credentials</h5></div>
                           <div class='col-lg-6 mt-1'>
                              <label class='font-text'>NowPayments API Key</label>
                              <input type='text' class='form-control' name='nowpayments_api_key' value="{{$data[0]->nowpayments_api_key}}">
                           </div>
                           <div class='col-lg-6 mt-1'>
                              <label class='font-text'>NowPayments IPN Secret</label>
                              <input type='text' class='form-control' name='nowpayments_ipn_secret' value="{{$data[0]->nowpayments_ipn_secret}}">
                           </div>

                           <hr class="w-100 mt-4 bg-secondary" style="opacity: 0.2;">

                           <!-- OxaPay Settings -->
                           <div class="col-12 mt-2"><h5 class="font-weight-bold" style="color:var(--text-primary)">OxaPay Credentials</h5></div>
                           <div class='col-lg-12 mt-1'>
                              <label class='font-text'>OxaPay Merchant ID</label>
                              <input type='text' class='form-control' name='oxapay_merchant_id' value="{{$data[0]->oxapay_merchant_id}}">
                           </div>

                           <div class="col-12 mt-4 text-right">
                              <button id="button" class='btn btn-success btn-lg px-5' style='color:#fff'>SAVE SETTINGS</button>
                           </div>
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
