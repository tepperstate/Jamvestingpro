@extends('layouts.admin.app')
@section('content')
  <!-- Begin Page Content -->
  <div class="container-fluid">
      <!-- Page Heading -->
   <div class="d-sm-flex align-items-center justify-content-between mb-4">
      <h1 class="h3 mb-0 text-white">{{$user->first_name}}</h1>
      <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm"><i
         class="fas fa-download fa-sm text-white-50"></i> user</a>
   </div>
      <!-- Content Row -->
      <div class="row mb-4">
      <div class="col-lg-6 mt-4 mx-auto">
         <p class='auth-title'><b>Note:</b> {{$user->first_name}} account</span>
         <div class="border">
            <div class="p-0">
               <br>
               <div class="p-4 pb-6">
                  <form id="submit" class="user" method='post' action="{{route('fund_referral')}}">
                     <p>${{number_format($amount)}}</p>
                     <div class="form-group">
                        @csrf
                        <label style='font-size:14px;'>Fund amount</label>
                        <input type="hidden" name="type" value="fund">
                        <input type="number" style='outline:none;width:100%;border:none;border-bottom:1px solid #8992A4;background:inherit' name='amount' required>
                        <input type="hidden" name='user_id' value="{{$user->id}}">
                     </div>
                     <br>
                     <button id="button" type="submit" class="btn mb-4 btn-success btn-sm ">
                       Fund
                     </button>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>
      </div>
   </div>
@endsection
