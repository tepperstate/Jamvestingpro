@extends('layouts.user.app')
@section('content')
<div class="container-full  mt-4">
  <div class="content-header" style="padding:15px 13px 0px">
    <h3>Withdrawal</h3>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/user"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="#">Withdrawal</a></li>
    </ol>
  </div>
  <section class="content" style="padding:15px 13px 0px">
    <div class="row">
      <div class="col-lg-12">
        <div class="box my_card">
          <div class="box-body">
            <p>{{site()->name}} stands out as the premier trading platform designed specifically for long-term investments in the financial markets. Our platform introduces unprecedented opportunities for trading currencies, assets, and commodities. Unlike conventional platforms such as Binance, Coinbase, Robinhood, and Etoro, {{site()->name}}  takes pride in being a market savior through our dedicated long-term trading policy. Discover the diverse withdrawal levels outlined below, and initiate the process by clicking on "Unlock." Unleash the potential of long-term trading with {{site()->name}} ..</p>
          </div>
        </div>
        <div class="box my_card">
          <div class="box-body">
            <p>Withdrawal Levels Available</p>
            <hr>
            @if(session('status_2'))
                 <p class="alert alert-warning" style="color:black">{{session('status_2')}}, upgrade <a class="btn btn-sm btn-primary" href="{{route('upgrade')}}">here</a></p>
            @endif
            @if(session('status_3'))
                 <p class="alert alert-warning" style="color:black">{{session('status_3')}}, upgrade <a class="btn btn-sm btn-primary" href="{{route('upgrade')}}">here</a></p>
            @endif
            @if(session('status_4'))
                 <p class="alert alert-warning" style="color:black">{{session('status_4')}}, upgrade <a class="btn btn-sm btn-primary" href="{{route('upgrade')}}">here</a></p>
            @endif
            <div>
              @forelse ($data as $key => $d )
                <p>{{++$key}} &nbsp; &nbsp;   ${{number_format($d->min)}}  - ${{number_format($d->max)}} &nbsp; &nbsp;     {{$d->plan}}  &nbsp; &nbsp; 
                @if(auth()->user()->level  == $d->plan)  <a style="font-size:14px;" class="btn btn-success btn-sm " href="{{route('withdraw')}}">Withdraw</a> @else 
                <a style="font-size:14px;" class="btn btn-primary btn-sm " href="{{route('unlock',$d->plan)}}">Unlock</a> @endif</p>
              @empty
              @endforelse
            </div>
           
          </div>
        </div>
      </div>
      <div class="col-lg-12 mb-4">
        @if(auth()->guard('web')->user()->balance->bonus > 0)
          <p style="padding-top: 10px; padding-bottom:10px;" class='bg-success pl-3 pr-3'>Congratulation &#127863; &#127856; &#127870; !! <br> You'av have earned a bonus of ${{number_format(auth()->guard('web')->user()->balance->bonus)}} <br> <a  data-toggle="modal" data-target="#pipbuilder" class='mt-2 btn btn-primary btn-sm' href="#">Withdraw</a></p>
        @endif
      </div>
    </div>
  </section>
</div> 
<div class="modal fade" id="pipbuilder" tabindex="-1" role="dialog modal-xl" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-l" role="document"> 
    <div class="modal-content ">
      <div class="modal-header">
        <h4 class="modal-title" id="name" style="font-weight: bold;">Withdraw Bonus</h4>
      </div>
      <div class="modal-body">
      <span class="mb-4">Bonus Balance : ${{number_format(auth()->guard('web')->user()->balance->bonus)}}</span>
      <br>
      <br>
        <form method="post" action="{{route('withdraw.withdraw_bonus')}}">
          @csrf

          <div class="form group mb-3">
            <label style="color:white;">Wallet Type</label>
              <select  class="form-control" name="type" required>
                <option>Bitcoin</option>
                <option>USDT(erc)</option>
                <option>ETH</option>

              </select>
          </div>

          <div class="form group mb-3">
              <label style="color:white;">Wallet Address</label>
              <input type="text" name="address" id="amount" class="form-control" placeholder="address" required>
          </div>

          <div class="form group mb-3">
              <label style="color:white;">Amount</label>
              <input type="number" step="any" name="amount" id="amount" class="form-control" placeholder="Amount" required>
          </div>
          <div>
              <button type="submit" name="bitcoindeposit" class="btn btn-success btn-sm">Submit</button>
          </div>
        </form>   
      </div>
      <div class="modal-footer ">
      <button class="btn btn-success" style="padding:0px 7px; font-weight: bold; font-size:15px;" data-dismiss="modal">X</button>
      </div>
    </div>
  </div>
 </div>
@if(session('status_3'))
   <script>
      toastr.error("{{session('status_3')}}",'status_3')
   </script>
  @endif
  @if(session('status'))
   <script>
      toastr.success("{{session('status')}}",'status')
   </script>
  @endif
@endsection
       
