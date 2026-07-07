@extends('layouts.admin.app')
@section('content')
  <!-- Begin Page Content -->
  <div class="container-fluid">
      <!-- Page Heading -->
   <div class="d-sm-flex align-items-center justify-content-between mb-4">
      <h1 class="h3 mb-0 text-white font-weight-bold">{{$user->first_name}}</h1>
      <a href="{{ url()->previous() }}" class="d-none d-sm-inline-block btn btn-sm btn-outline-light shadow-sm"><i
         class="ri-arrow-left-line"></i> Back to Users</a>
   </div>
      <!-- Content Row -->
      <div class="row mb-4">
      <div class="col-lg-6 mt-4 mx-auto">
         <div style="background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: var(--radius-lg); backdrop-filter: blur(10px);" class="p-4 p-md-5 shadow-lg">
            <h5 class="text-white mb-1"><i class="ri-add-circle-line text-success mr-2"></i> Fund Account</h5>
            <p class='text-muted small mb-4'>Managing balance for <b>{{$user->first_name}} {{$user->last_name}}</b></p>
            
            <form id="fundForm" class="user" method='post' action="{{route('ad')}}" onsubmit="this.querySelector('button[type=submit]').disabled=true;">
               @csrf
               <input type="hidden" name="type" value="fund">
               <input type="hidden" name='user_id' value="{{$user->id}}">
               <input type="hidden" name='symbol' value="{{$amount->symbol}}">

               <div class="form-group mb-4">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                     <label class="text-light mb-0" style='font-size:14px;'>Amount to Fund</label>
                     <span class="badge badge-primary px-2 py-1" style="background: rgba(59, 130, 246, 0.2); color: #60a5fa;">Current: {{number_format($amount->amount, 2)}} {{$amount->symbol}}</span>
                  </div>
                  <div class="input-group">
                     <input type="number" step="0.000001" class="form-control" style='background: rgba(0,0,0,0.2); border: 1px solid var(--glass-border); color: #fff; padding: 12px 15px; border-radius: 8px;' name='amount' placeholder="Enter amount" required>
                     <div class="input-group-append">
                        <span class="input-group-text" style="background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-left: none; color: var(--text-muted); border-radius: 0 8px 8px 0;">{{$amount->symbol}}</span>
                     </div>
                  </div>
               </div>
               
               <div class="form-group mb-4">
                  <div class="custom-control custom-switch">
                     <input type="checkbox" class="custom-control-input" id="emailNotification" name="check" value="1" checked>
                     <label class="custom-control-label text-light" for="emailNotification" style="padding-top: 2px;">Send Email Notification</label>
                  </div>
               </div>
               
               <button type="submit" class="btn btn-success btn-block py-2" style="border-radius: 8px; font-weight: 600;">
                 <i class="ri-check-line mr-1"></i> Add Funds
               </button>
            </form>
         </div>
      </div>
   </div>
   </div>
@endsection
