@extends('layouts.user.app')
@section('content')
<div class="container-full mt-4">
   <div class="content-header" style="padding:15px 13px 0px">
    <h3>Deposit</h3>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/user"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="#">Bank Deposit</a></li>
    </ol>
  </div>
   <section class="content" style="padding:15px 13px 0px">
      <div class="row">
         <div class="col-lg-12">
         @if(isset($data->message))
            <marquee class='bg-primary' style="padding-top: 10px; padding-bottom:10px;" behavior="" direction="">{{$data->message}}</marquee>
         @endif
         <p>Select any of our convenient methods of making deposit to your account</p>
         </div>
         <div class="col-xs-12 col-sm-6">
            <a href="{{route('cash_app')}}">
            <div class="box">
               <div class="box box-body pull-up d-flex justify-content-center align-items-center"style='min-height:240px;' >
                  <img style="width: 100px;" src="{{asset('asset/cash.jpg')}}" alt="">
                  <h3 style="font-weight: bold;color:white;"> Cash App</h3>
               </div>
            </div>
            </a>
         </div>
         <div class="col-xs-12 col-sm-6">
            <a href="{{route('bank_app')}}">
               <div class="box">
                  <div class="box box-body pull-up d-flex justify-content-center align-items-center" style='min-height:240px;' >
                     <img style="width: 100px; height:77px;" src="https://e7.pngegg.com/pngimages/382/83/png-clipart-bank-transfer-logo-wire-transfer-electronic-funds-transfer-bank-payment-computer-icons-bank-text-rectangle.png" alt="">
                     <h3 style="font-weight: bold;color:white;">Bank Transfer</h3>
                  </div>
               </div>
            </a>
         </div>
      </div>
   </section>
</div>

@endsection
