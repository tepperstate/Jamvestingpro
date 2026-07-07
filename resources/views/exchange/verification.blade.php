@extends('layouts.user.app')
@section('title', 'Account Verification')
@section('content')
<div class="container-full mt-4" >
   <!-- Content Header (Page header) -->
   <div class="content-header" style="padding:15px 13px 0px">
      <h3>
         Account Verification
      </h3>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item"><a href="#">Settings</a></li>
		</ol>
   </div> 
   <section class="content" style="padding:15px 13px 0px">
      <div class="row">
         <div class="col-lg-12">
            <p>
               <i class="nav-icon fa fa-envelope" style="padding-left:10px; padding-right:5px;"></i>To complete your profile we need you to verify your email address to gain access to all our trading services.
            </p>
         </div>
         <br>
        <div class="col-xl-6 col-lg-6">
           <div class="box box-body" style="margin-right:8px; color: white; margin-left:8px; margin-bottom: 10px;">
              <img class="image12 img-fluid" src="{{asset('asset/email.gif')}}" alt="">
           </div>
         </div>
         <div class="col-xl-6 col-lg-6">
            <div class="box box-body" style="margin-right:8px;color:white;margin-left:8px;margin-bottom:10px;">
              <h1 style="color:lightgreen; font-weight:600; font-size: 28px; padding-bottom:5px;">Verify your Email</h1>
               <span style="font-size:22px">
               <span style="color: orange">
               An email verification is required to access our Trading Services.</span> <br><br> An email was sent to: <span style="color:orange;">{{ auth()->user() ? auth()->user()->email : 'your registered email' }}</span>   <br><br>  
               Check Your inbox to Verify Your Account. <br> <br>
               <!-- 3. Any other valid bill showing Your Address-->
               </span>
               <h5>Didnt get the email ?</h5>

               <form action="{{route('resend')}}" method="post">
                  @csrf

               <input type="submit" value="Resend Link" class="btn btn-primary  btn-rounded btn-md" name="emailverification">

               </form>

               <h5>Account already verified ?</h5>

               <a class="btn btn-primary  btn-rounded btn-md" href="{{route('dashboard.index')}}">Continue</a>
            </div>
         </div>
        </div>
         @if(session('status'))
            <script>
               toastr.success("{{session('status')}}",'successful')
            </script>
         @endif 
      </div>
   </div>
</section>
@endsection
