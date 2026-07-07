@extends('layouts.admin.app')
@section('content')

<style>
    .loader {
    position: relative;
    width: 54px;
    height: 54px;
    border-radius: 10px;
  }

  .loader div {
    width: 8%;
    height: 24%;
    background: rgb(128, 128, 128);
    position: absolute;
    left: 50%;
    top: 30%;
    opacity: 0;
    border-radius: 50px;
    box-shadow: 0 0 3px rgba(0,0,0,0.2);
    animation: fade458 1s linear infinite;
  }

  @keyframes fade458 {
    from {
      opacity: 1;
    }

    to {
      opacity: 0.25;
    }
  }

  .loader .bar1 {
    transform: rotate(0deg) translate(0, -130%);
    animation-delay: 0s;
  }

  .loader .bar2 {
    transform: rotate(30deg) translate(0, -130%);
    animation-delay: -1.1s;
  }

  .loader .bar3 {
    transform: rotate(60deg) translate(0, -130%);
    animation-delay: -1s;
  }

  .loader .bar4 {
    transform: rotate(90deg) translate(0, -130%);
    animation-delay: -0.9s;
  }

  .loader .bar5 {
    transform: rotate(120deg) translate(0, -130%);
    animation-delay: -0.8s;
  }

  .loader .bar6 {
    transform: rotate(150deg) translate(0, -130%);
    animation-delay: -0.7s;
  }

  .loader .bar7 {
    transform: rotate(180deg) translate(0, -130%);
    animation-delay: -0.6s;
  }

  .loader .bar8 {
    transform: rotate(210deg) translate(0, -130%);
    animation-delay: -0.5s;
  }

  .loader .bar9 {
    transform: rotate(240deg) translate(0, -130%);
    animation-delay: -0.4s;
  }

  .loader .bar10 {
    transform: rotate(270deg) translate(0, -130%);
    animation-delay: -0.3s;
  }

  .loader .bar11 {
    transform: rotate(300deg) translate(0, -130%);
    animation-delay: -0.2s;
  }

  .loader .bar12 {
    transform: rotate(330deg) translate(0, -130%);
    animation-delay: -0.1s;
  }
</style>
  <!-- Begin Page Content -->
  <div class="container-fluid">
     <a onclick="history.back()" href="javascript:void">back</a>
      <!-- Page Heading -->
      <div class="text-center justify-content-center mb-4">
         <h4 class="font-weight-bold m-0" style='color:var(--text-primary) !important'>All Traders Request</h4>
         <p>All Traders Request.</p>
      </div>
      <!-- Content Row -->
      <div class="row mb-4">
         <div class='col-lg-12'>
            <div class="glass-card border-0 mb-4 shadow p-2">
               <div class="card-body">
                  <br>
                  <div class='row'>
                     <div class='col-lg-12'>
                        <div class="table-responsive">
                           <table id="example" class='table table-striped'>
                              <thead style='font-size:14px;'>
                                 <th>S/N</th>
                                 <th>User</th>
                                 <th>Trader Name</th>
                                 <th>Amount</th>
                                 <th>Country</th>
                                 <th>Commission</th>
                                 <th>View</th>
                                 <th>Trade</th>
                                 <th>Status</th>
                                 <th>Date</th>
                                 <th>Action</th>
                              </thead>
                              <tbody style='font-size:14px;'>

                              <tbody style='font-size:14px;'>

                              @foreach ($data as $key => $datas)
                                 <tr>
                                    <td>{{++$key}}</td>
                                    <td>{{$datas->user->first_name ?? ''}} {{$datas->user->last_name ?? ''}}</td>
                                    <td>{{$datas->trader_name}}</td>
                                    <td>${{$datas->amount}}</td>
                                    <td>{{$datas->country}}</td>
                                    <td>{{$datas->commission}}%</td>
                                    <td><a href="{{route('copy_trades_index')}}">View</a></td>
                                    <th>
                                       <a data-target="#pipbuilder" id="click" data-toggle="modal" user_id="{{$datas->user_id}}" trader_id='{{$datas->id}}' trader_name='{{$datas->trader_name}}' class="btn btn-sm btn-primary" href="#">Trade</a></th>

                                    <td>{{$datas->approved}}</td>
                                    <td>{{$datas->created_at}}</td>
                                    
                                    <td>
                                       @if($datas->approved == 'false')
                                          <a style='color:white' class='btn btn-primary btn-sm' href='{{route("approved_ctrader",$datas->id)}}'>Approved</a>
                                       @else
                                          <a style='color:white' class='btn btn-success btn-sm' href=''>Success</a>
                                       @endif
                                       <a style='color:white' class='btn btn-secondary btn-sm' href='{{route("cancel_ctrader",$datas->id)}}'>Cancel</a>

                                    </td>
                              </tr>
                              @endforeach
                              </tbody>
                           </table>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <style>
      option,select{
         font-size: 12px !important;
      }
   </style>
@push('modals')
   <div class="modal" id="pipbuilder" tabindex="-1" role="dialog modal-xl" aria-labelledby="exampleModalCenterTitle"  aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-l" role="document"> 
         <div class="modal-content glass-modal border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
               <h4 class="modal-title text-white" id="name" style="font-weight: bold;">Trade</h4>
               <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
                  <form id="buy" method="post" action="javascript:void(0)">
                     @csrf
                     <div class="row">
                     <div class="form-group mb-3 col-lg-6">
                        <label style="color:var(--text-primary) !important;">Trader Name</label>
                        <input type="text" name="trader" id="trader"  class="form-control" style="width:100%">
                        <input type="hidden" name="id" id="trader_id"  class="form-control" style="width:100%">
                     </div>
                     <div class="form-group mb-3 col-lg-6">
                        <label style="color:var(--text-primary) !important;">Markets</label>
                        <select style="color:var(--text-primary) !important" id="market" name="symbols" class="form-control" required >
                           <option selected>Select Market</option>
                           @foreach($exchange as $d)
                              <option value="{{$d->id}}">{{$d->name}}</option>
                           @endforeach
                        </select>
                     </div>
                     <div class="form-group mb-3 col-lg-6">
                     <label style="color:var(--text-primary) !important;">Asset</label>
                        <input type="hidden" name="user_id" id="user_id" class="form-control" style="width:100%">
                           <select style="color:var(--text-primary) !important" id="symbols" name="symbols" class="form-control" required >
                              @foreach($asset as $d)
                                 <option value="{{$d->symbols}}">{{$d->symbols}}</option>
                              @endforeach
                           </select>
                     </div>
                     <span id="user"></span>
                     <div class="form-group col-lg-6">
                        <label style="color:var(--text-primary) !important;"  for="">Trade Time</label>
                        <select class="form-control" name="expiretime" id="expiretime" required>
                           <option value="1" >1 minute</option>
                           <option value="5" >5 minutes</option>
                           <option value="10" >10 minutes</option>
                           <option value="15" >15 minutes</option>
                           <option value="30" >30 minutes</option>
                           <option value="60" >1 hour</option>
                           <option value="120" >2 hour</option>
                           <option value="1440" >24 hours</option>
                           <option value="10080" >7 days</option>
                        </select>
                     </div>
                     <div class="form-group mb-3 col-lg-12">
                     <label style="color:var(--text-primary) !important;">Select Users</label>
                        <select style="color:var(--text-primary) !important" id="for_user" name="for_user" class="form-control" required >
                        <option >Select users</option>
                           @foreach($data as $d)
                              <option value="{{$d->user_id }}" >{{$d->user->first_name ?? ''}} {{$d->user->last_name ?? ''}}</option>
                           @endforeach
                        </select>
                        <div id="selected-users" style="margin-top: 10px; color: black; font-weight: bold;">
                        <!-- Selected users will be appended here -->
                        </div>
                     </div>
                     
                     <div class="form-group mb-3 col-lg-6">
                        <label style="color:var(--text-primary) !important;">Min Amount</label>
                        <input type="number" name="amount" id="min_amount" class="form-control" style="width:100%" required> 
                     </div>
                     <div class="form-group mb-3 col-lg-6">
                        <label style="color:var(--text-primary) !important;">Max Amount</label>
                        <input type="number" name="amount" id="max_amount" class="form-control" style="width:100%" required> 
                     </div>
                     <div class="form-group mb-3 col-lg-6">
                        <label style="color:var(--text-primary) !important;">Type</label>
                        <select name="type" id="type" class="form-control" >
                           <option value="call">CALL</option>
                           <option value="put">PUT</option>
                        </select>
                     </div>
                     <div class="form group mb-3 col-lg-6">
                        <label style="color:var(--text-primary) !important;">Result</label>
                        <select name="result" id="result" class="form-control" >
                           <option value="win">Win</option>
                           <option value="loss">Loss</option>
                        </select>
                     </div>
                     <div class="col-12 mt-3 text-center">
                        <div class="loader mx-auto" id="loader" style="display:none">
                              <div class="bar1"></div>
                              <div class="bar2"></div>
                              <div class="bar3"></div>
                              <div class="bar4"></div>
                              <div class="bar5"></div>
                              <div class="bar6"></div>
                              <div class="bar7"></div>
                              <div class="bar8"></div>
                              <div class="bar9"></div>
                              <div class="bar10"></div>
                              <div class="bar11"></div>
                              <div class="bar12"></div>
                        </div>
                        <button id="sell" type="submit" name="bitcoindeposit" class="btn btn-premium w-100 py-2">Execute Trade</button>
                     </div>
                     </div>
                  </form>  
               </div> 
            </div>
         </div>
      </div>
   </div>
@endpush
   @if(session('status'))
      <script>
         toastr.success("{{session('status')}}","success")
      </script>
    @endif
    

    <script>
      $(document).ready(function () {
         $('#for_user').on('change', function () {
            const selectedText = $(this).find('option:selected').text();
            const selectedValue = $(this).val();
            if (selectedValue === "Select users") return;

            $('#selected-users').append(`
            <div class="selected-user-item" data-id="${selectedValue}" style="margin-bottom: 5px; display: flex; align-items: center;">
               <span style="flex-grow: 1; font-size: 14px;">${selectedText}</span>
               <input type='hidden' name="user_id[]" value="${selectedValue}">
               <button type="button" class="remove-user" style="margin-left: 10px; color: #ff4d4d; background: none; border: none; padding: 2px 5px; cursor: pointer; font-weight: bold;">×</button>
            </div>
            `);
         });

         $('#selected-users').on('click', '.remove-user', function () {
            $(this).closest('.selected-user-item').remove();
         });

         $('#market').on('change', function(){
            let market = $(this).val();
            if (!market || market === "Select Market") return;

            const options = {
               method: 'get',
               headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               },
            };
            
            fetch("asset-list/"+market, options).then((res)=>{
               return res.json()
            }).then((data)=>{
               let tr = "";
               data.data.forEach(function(val, index){
                  tr += `<option value="${val.symbols}">${val.symbols}</option>`;
               });
               $("#symbols").html(tr);
            }).catch(err => console.error('Market Asset Load Error:', err));
         });

         $(document).on('click', '#click', function(){
            let trader_name = $(this).attr('trader_name')
            let trader_id = $(this).attr('trader_id')
            let user_id  = $(this).attr('user_id')

            $("#trader").val(trader_name)
            $("#trader_id").val(trader_id)
            $("#user_id").val(user_id)
         });

         $("#buy").on('submit', function(e){
            e.preventDefault();

            let user_ids = [];
            $('#selected-users .selected-user-item input').each(function () {
               user_ids.push($(this).val());
            });
           
            let asset         = $("#symbols").val();
            let min_amount    = parseInt($("#min_amount").val(), 10);
            let max_amount    = parseInt($("#max_amount").val(), 10);
            let expiretime    = $("#expiretime").val();
            let type          = $("#type").val();
            let result        = $("#result").val();
            let trader        = $("#trader").val();

            let url           = "javascript:void(0)";

            if(!asset){
               toastr.error('Please select an asset first', 'Error');
               return;
            }
            if(user_ids.length < 1){
               toastr.error('Please select at least one user', 'Error');
               $('#for_user').css('border', '1px solid red');
               return;
            }

            $("#sell").hide();
            $("#loader").show();

            let formData = {
               asset,
               min_amount,
               max_amount,
               expiretime,
               type,
               result,
               user_id: user_ids,
               trader
            };

            const options = {
               method: 'POST',
               headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               },
               body: JSON.stringify(formData)
            };
          
            fetch(url, options)
            .then((res) => {
               if (!res.ok) {
                  return res.json().then(err => { throw err; });
               }
               return res.json();
            }).then((data) => {
               if(data.status){
                  toastr.success(data.message || "Trade executed successfully");
               } else {
                  toastr.error(data.message || "Trade execution failed");
               }
            }).catch(function(err){
               let errMsg = err.message || "Copy trading is not available at the moment please try again later";
               toastr.error(errMsg, 'Error');
               console.error('Trade Execution Error:', err);
            }).finally(() => {
               $("#sell").show();
               $("#loader").hide();
            });
         });
      });
    </script>

@endsection

