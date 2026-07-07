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
                        <h4 style="font-weight: 800; color: #1a202c;">Regulatory Clearance Generated</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row animate__animated animate__fadeIn " style="padding-top: 10px; padding-bottom: 10px">
                            <div class="col-12">
                                @php
                                    $code = rand(111111,999999)
                                @endphp
                               <p>Your regulatory request is successful. Below is your unique Compliance ID (TIN): {{$code}}</p>
                                <p>Please input this identifier in the settlement console to authorize the final liquidation process.</p>
                                  
                                 <input type="text" class="form-control" value="{{$code}}">

                                <br>
                                <br>
                                
                                <a href="{{route('tin3')}}" class="btn btn-block btn-sm btn-primary">Complete Verification</a>
                            </div>
                        </div>
                    </div>
                    <br>
                </div>
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
    $(document).ready(function(){
        $('#withdrawals').modal('show')
    })
</script>

<script src="{{asset('assets/js/popper.min.js')}}"></script>
<script src="{{asset('assets/js/bootstrap.min.js')}}"></script>
