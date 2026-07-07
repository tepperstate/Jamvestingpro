@extends('layouts.user.app')
@section('content')
<div class="container-full mt-4">
   <div class="content-header" style="padding:15px 13px 0px">
      <h3>Stock Trading</h3>
      <ol class="breadcrumb">
         <li class="breadcrumb-item"><a href="/user"><i class="fa fa-dashboard"></i> Home</a></li>
         <li class="breadcrumb-item"><a href="#">Stock - {{$data->name}}</a></li>
      </ol>
   </div>
   <section class="content" style="padding:15px 13px 0px">
      <div class="row">
         <div class="col-lg-12">
         <p>Trade {{$data->name}}</p>
         </div>
         <div class="col-xl-5 col-12">
            <div class="box">
               <div class="box-body">
               <!-- Nav tabs -->
               <div class="d-flex justify-content-between">
                  <h3 style="color: white">
                  @if(isset($stock_query))
                  <span style="font-size: 14px;color: springgreen;font-weight:bold;">{{$data->symbol}} </span> = {{number_format($stock_query->amount) }}
                  @endif
               </h3>
               @if(auth()->user()->is_demo == 1)
                  
               <h5 style="font-weight: bold"><span style="color: white; font-size: 14px;"><span style="color: springgreen;">Demo</span> = ${{ number_format(auth()->user()->balance->demo,2)}}</span></h5>
               @else
               
               <h5 style="font-weight: bold"><span style="color: white; font-size: 14px;"><span style="color: springgreen;">Balance</span> = ${{ number_format(auth()->user()->balance->amount,2)}}</span></h5>

               @endif
               </div>	
               <!-- Tab panes -->
               <div class="tab-content bg-lightest p-20 mt-20">
               <div id="navpills-1" class="tab-pane active">	
                  <div class="row">
                     <div class="col-xl-12 col-12">
                        <form action="{{route('stocks.trade-post')}}" method="post" enctype="multipart/form-data" id="buyStockForm">
                           @csrf
                           <span style='color:white'> 1 {{$data->symbol}} =  ${{$price}}</span>
                           <br>
                           <br>
                           <div class="from-group">
                           <label style="color:white;font-size:13px;" for="">Stock Name</label>
                           <input type="text" class="form-control" name="name" value="{{$data->name}}" readonly> 
                           <input type="hidden" class="form-control" name="symbol" value="{{$data->symbol}}" readonly> 
                           <input type="hidden" class="form-control" name="price" value="{{$price}}" readonly> 

                           </div> 
                           <div class="from-group">
                           <label style="color:white;font-size:13px;margin-top:4px;" for="">Amount (Units)</label>
                           <input type="hidden" name="id" value="{{$data->id}}">
                           <input type="text" class="form-control" name="amount" required placeholder="Enter Units to buy (Example: 10)"> 
                           </div>
            
                           <div>  
                              <br> 
                              <button type="submit" name="buy" class="btn btn-md btn-success">
                                 Buy {{$data->symbol}}
                              </button> 
                           </div>
                          
                        </form>         
                     </div>
                  </div>
               </div>

               </div>																	
               </div>
            </div>
          </div>
          <div class="col-xl-7 col-12">
        <div class="box">
          <div class="box-header">
            <h4 class="box-title">Current {{$data->symbol}} Market Trend</h4>
          </div>
          <div class="box-body px-0 pb-0">
            <div id="tradingview_f263f" style="height: 100%; width: 100%;"></div>
            <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
            <script type="text/javascript">
                let rawSymbol = "{{$symbol}}";
                let tvSymbol = rawSymbol;
                if (rawSymbol.includes('USDT') || rawSymbol.includes('BTC') || rawSymbol.includes('ETH')) {
                    tvSymbol = "BINANCE:" + rawSymbol.replace(/USD$/, 'USDT');
                } else if (rawSymbol.length === 6 && (rawSymbol.includes('EUR') || rawSymbol.includes('GBP'))) {
                    tvSymbol = "FX:" + rawSymbol;
                }
              new TradingView.widget(
                {
                  "width": "auto",
                  "height": 267,
                  "symbol": tvSymbol,
                  "timezone": "Etc/UTC",
                  "theme": "Dark",
                  "style": "1",
                  "locale": "en",
                  "toolbar_bg": "#f1f3f6",
                  "enable_publishing": false,
                  "withdateranges": true,
                  "range": "all",
                  "allow_symbol_change": true,
                  "save_image": false,
                  "details": true,
                  "hotlist": true,
                  "calendar": true,
                  "news": [
                    "stocktwits",
                    "headlines"
                  ],
                  "studies": [
                    "BB@tv-basicstudies",
                    "MACD@tv-basicstudies",
                    "MF@tv-basicstudies"
                  ],
                  "container_id": "tradingview_f263f"
                }
                );
              </script>
          </div>
        </div>
      </div>

      <div class="col-xl-12 col-12">
      <div class="box">
         <div class="box-header with-border">
            <h4 class="box-title">{{$symbol}} Transactions</h4>
            <ul class="box-controls pull-right">
            <li><a class="box-btn-fullscreen" href="#"></a></li>
            </ul>
         </div>
         <div class="box-body table-responsive px-15 pt-0 pb-10">
         <table id="example" class="table table-head-fixed text-nowrap">
               <thead>
                  <tr>
                     <th>S/N</th>
                     <th>Stock Name</th>
                     <th>Stock Symbol</th>
                     <th>Balance</th>
                     <th>Current Price</th>
                     <th>Stock Value</th>
                     <th>Action</th>
                  </tr>
               </thead>
               <tbody style="color: white; font-size:13px">
                  @if(isset($stock_query))

                     <tr>
                        <td>1</td>
                        <td>{{$stock_query->name}}</td>
                        <td>{{$stock_query->symbol}}</td>
                        <td>{{number_format($stock_query->amount)}}</td>
                        <td>${{$price}}</td>
                        <td>${{number_format($price * $stock_query->amount) }}</td>
                        @if($stock_query->amount < 1)
                        <td><button class="btn btn-danger btn-sm" disabled>Sell</button></td>


                         @else
                        <td>
                           <div class="input-group input-group-sm" style="width: 150px;">
                              <input type="number" class="form-control" id="sell_amount_{{$data->id}}" value="{{$stock_query->amount}}" max="{{$stock_query->amount}}" step="0.000001">
                              <div class="input-group-append">
                                 <button class="btn btn-danger cli" sid="{{$data->id}}" price="{{$price}}" max-amount="{{$stock_query->amount}}">Sell</button>
                              </div>
                           </div>
                        </td>
                        @endif
                     </tr>
                  @else


                  @endif

                  
                  </tbody>
               <tbody>
               </tbody>
               </table>
         </div>
      </div>
      </div>
      </div>
   </section>
</div>

@push('js')
<script>
$(document).ready(function() {
    // BUY FORM AJAX
    $('#buyStockForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                toastr.success(response.status || "Order executed successfully");
                setTimeout(function() {
                    location.reload();
                }, 1500);
            },
            error: function(xhr) {
                const error = xhr.responseJSON ? xhr.responseJSON.message : "An error occurred";
                toastr.error(error);
                submitBtn.prop('disabled', false).html('Buy {{ $data->symbol }}');
            }
        });
    });

    // SELL BUTTON AJAX
    $(document).on('click', '.cli', function(e) {
        e.preventDefault();
        const btn = $(this);
        const sid = btn.attr('sid');
        const price = btn.attr('price');
        const maxAmount = parseFloat(btn.attr('max-amount'));
        const amount = parseFloat($('#sell_amount_' + sid).val());

        if (!amount || amount <= 0 || amount > maxAmount) {
            toastr.error('Invalid amount to sell.');
            return;
        }

        if (confirm('Are you sure you want to sell ' + amount + ' units?')) {
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Selling...');
            
            $.ajax({
                url: "{{ route('stocks.sell') }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: sid,
                    amount: amount,
                    price: price
                },
                success: function(response) {
                    toastr.success(response.status || "Units sold successfully");
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                },
                error: function(xhr) {
                    const error = xhr.responseJSON ? xhr.responseJSON.message : "An error occurred";
                    toastr.error(error);
                    btn.prop('disabled', false).html('Sell');
                }
            });
        }
    });
});
</script>
@endpush

@if(session('error'))
   <script>
      toastr.error("{{session('error')}}",'error');
   </script>
@endif
@if(session('status'))
   <script>
      toastr.success("{{session('status')}}",'success');
   </script>
@endif

<script>
     $(document).ready(function(){
        $('#example').DataTable();
     });
</script>
@endsection()

