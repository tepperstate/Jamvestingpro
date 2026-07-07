 @extends('layouts.user.app')
@section('content')

<div class="container-fluid mt-4">
   <div class="padding-top">
      <div class="row">

         <div class="col-lg-11">
            <div class="row">
               @forelse ($data as $datas)
               <div class="col-lg-4">
                  <div class="box my_card pb-3" style="min-height: 560px;">
                     <div class="card-header border-0">
                        <div class="d-flex justify-content-between">
                        </div>
                     </div>
                     <div class="box-body pb-3">
                        <form>
                           @csrf
                           <div class="form-group">
                              <label style="color:white" for="">Bots Name</label>
                               <input type="text" style="padding:15px" class="form-control bot-name" value="{{$datas->name}}"disabled>
                            </div>
                            <div class="form-group">
                            <input type="hidden" name="id" class="form-control bot-id" value="{{$datas->bot_id}}">

                              <label style="color:white" for="">Select Assets (Hold Ctrl/Cmd to select multiple)</label>
                               <select style="padding:15px; height: 120px;" class="form-control bot-symbols" name="symbols[]" multiple required>
                                 @forelse ($b as $c)
                                    <option value="{{$c->symbols}}">{{$c->symbols}}</option>
                                 @empty
                                    
                                 @endforelse
                              </select>
                           </div>
                           <div class="form-group">
                              <label style="color:white" for="">Trade Type</label>
                               <select style="padding:15px" class="form-control bot-type" name="type" required>
                                 <option>Buy</option>
                                 <option>Sell</option>
                              </select>
                           </div>

                           <div class="form-group">
                              <label style="color:white" for="">Amount (Per Asset)</label>
                               <input style="padding:15px" type="number" name="amount" class="form-control bot-amount" required placeholder="$" value="{{$datas->min}}">
                           </div>

                           <div class="form-group mb-4 p-3 rounded-3 border" style="background: rgba(59, 130, 246, 0.05); border-color: rgba(59, 130, 246, 0.2) !important;">
                              <div class="form-check form-switch d-flex align-items-center gap-3">
                                 <input class="form-check-input bot-autorenew" type="checkbox" id="autoRenew-{{$datas->bot_id}}" style="width: 2.5em; height: 1.25em; cursor: pointer;">
                                 <label class="form-check-label text-white fw-bold" for="autoRenew-{{$datas->bot_id}}" style="cursor: pointer;">Enable Set & Forget (Auto-Renew)</label>
                              </div>
                              <small class="text-info d-block mt-2"><i class="ri-information-line"></i> Bot will automatically restart with the same parameters and automatically re-deduct balance upon expiration.</small>
                           </div>
                           
                            <style>
                               p,a,li{
                                  color:white;
                               }
                               .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter, .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_processing, .dataTables_wrapper .dataTables_paginate,.dataTables_wrapper .dataTables_paginate .paginate_button{
                                  color: white !important;
                               }
                               .status-indicator-pulse { 
                                  box-shadow: 0 0 0 0 rgba(255, 51, 51, 0.7);
                                  animation: pulse 2s infinite;
                               }
                               @keyframes pulse {
                                  0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 51, 51, 0.7); }
                                  70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(255, 51, 51, 0); }
                                  100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 51, 51, 0); }
                               }
                            </style>

                            <div> 
                               @if(isset($user_bot->id) )
                                  <div class="d-flex align-items-center gap-3 mb-3">
                                     <div class="status-indicator-pulse bg-success" style="width: 12px; height: 12px; border-radius: 50%;"></div>
                                     <span class="text-success fw-bold outfit">BOT IS RUNNING</span>
                                  </div>
                                  <div class="diagnostic-box mt-3 mb-4 p-3" style="background: rgba(0,0,0,0.3); border-radius: 10px; border: 1px solid rgba(255,255,255,0.05);">
                                     <p class="x-small text-muted text-uppercase mb-2" style="font-size: 10px; letter-spacing: 1px;"><i class="ri-terminal-box-line me-1"></i> Diagnostic Stream</p>
                                     <div id="diagnostic-log-{{$datas->bot_id}}" class="diagnostic-log text-white-50" style="height: 100px; overflow-y: hidden; font-family: 'Courier New', Courier, monospace; font-size: 11px; line-height: 1.5;">
                                        <div class="log-line">> Initializing security handshakes...</div>
                                        <div class="log-line">> Syncing with exchange liquidity...</div>
                                        <div class="log-line">> Awaiting next market pulse...</div>
                                     </div>
                                  </div>
                                   <button type="submit" name="buy" class="btn btn-md btn-danger w-100 py-3 btn-stop">
                                      <i class="ri-stop-circle-line me-2"></i> Stop Bot
                                   </button>
                                @else
                                   <button name="buy" class="btn btn-md btn-success w-100 py-3 mb-3 btn-start">
                                      <i class="ri-play-circle-line me-2"></i> Start Bot
                                   </button>
                                   <img class="bot-loader" style="display:none" width="60px;" src="{{asset('loading.gif')}}">
                                   <button type="submit" name="buy" class="btn btn-md btn-danger w-100 py-3 btn-stop" style="display:none">
                                      <i class="ri-stop-circle-line me-2"></i> Stop Bot
                                   </button>
                               @endif
                            </div> 
                           @if(isset($user_bot))
                              <p class="mt-4 text-info"><i class="fa fa-spinner fa-spin"></i> Bot is active and generating results...</p>
                           @endif
                            <p class="mt-4 bot-message"></p>
                           
                        </form>
                     </div>
                  </div>
               </div>
               @empty
               <div class="col-lg-12">
                  <div class="alert alert-info">No active bot found.</div>
               </div>
               @endforelse
               <div class="col-lg-8">
                  <div class="box my_card" style="min-height: 566px; ">
                     <div class="card-header border-0">
                        <h3 class="card-title" style="color: white;">My Bot Trade History</h3>
                     </div>
                     <div id="areaToPrint" class="box-body">
                        <div class="card-body table-responsive p-0" style="height: 452px;"> 
                        <table id="example" class="table table-head-fixed text-nowrap">
                        <thead>
                           <tr>
                              <th>S/N</th>
                              <th>Bot</th>
                              <th>Symbol</th>
                              <th>Volume</th>
                              <th>Profit/Loss</th>
                              <th>Status</th>
                              <th>Date Traded</th>
                           </tr>
                        </thead>
                        <tbody id="bot" style="color: white; font-size:13px">
                           @forelse ($result as $key => $d )
                              <tr>
                                 <td>{{++$key}}</td>
                                 <td>{{$d->name}}</td>
                                 <td>{{$d->symbol}}</td>
                                 <td>${{number_format($d->amount)}}</td>
                                 @if($d->status == 'win')
                                    <td class="text-success">${{number_format($d->profit)}}</td>
                                 @else
                                 <td class="text-danger">${{number_format($d->profit)}}</td>
                                 @endif
                              
                                 @if($d->status == 'win')
                                    <td class="text-success">{{$d->status}}</td>
                                 @else
                                 <td class="text-danger">{{$d->status}}</td>
                                 @endif
                                 <td>{{\Illuminate\Support\Carbon::parse($d->created_at)->format('Y-m-d')}}</td>
                              </tr>
                           @empty
                              
                           @endforelse
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
</div>

<script>
    // Live Diagnostic Simulation
    const logEntries = [
        "Analyzing market volatility index...",
        "Scanning markets for opportunities...",
        "Adjusting risk parameters based on sentiment...",
        "Calculating predictive EMA crossover...",
        "Validating order depth on layer 2...",
        "Optimizing gas fees for transaction relay...",
        "Monitoring whale wallet movements...",
        "Refreshing exchange API credentials...",
        "Awaiting favorable buy signal...",
        "Executing micro-hedging strategy...",
        "Verifying trade slippage tolerance...",
        "Updating internal ledger balance..."
    ];

    function updateDiagnosticLogs() {
        $(".diagnostic-log").each(function() {
            const $log = $(this);
            const randomEntry = logEntries[Math.floor(Math.random() * logEntries.length)];
            const $newLine = $(`<div class="log-line">> ${randomEntry}</div>`).css({ opacity: 0 });
            
            $log.append($newLine);
            $newLine.animate({ opacity: 1 }, 500);
            
            if ($log.children().length > 6) {
                $log.children().first().animate({ opacity: 0 }, 500, function() {
                    $(this).remove();
                });
            }
        });
    }

    $(document).ready(function(){
        $('#example').DataTable();
        
        setInterval(function(){
            trade()
        }, 5000);

        setInterval(updateDiagnosticLogs, 4000);

        function thousands_separators(num){
            var num_parts = num.toString().split(".");
            num_parts[0] = num_parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            return num_parts.join(".");
        }
        
        function trade(){
            let id = $(".bot-id").first().val(); 
            if(!id) return;

            const options = {
                method: 'get',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            };
            
            fetch("/dashboard/bot/user/json/"+id,options)
            .then((res)=>res.json())
            .then((data)=>{
                let tr =""
                data.data.forEach(function(value,index){
                    tr += `
                        <tr>
                            <td>${++index}</td>
                            <td>${value.name}</td>
                            <td>${value.symbol}</td>
                            <td>$${thousands_separators(value.amount)}</td>
                            ${value.status == 'win' ? "<td class='text-success' style='font-weight:bold'>$"+thousands_separators(value.profit)+"</td>" :  "<td class='text-danger' style='font-weight:bold'>$"+thousands_separators(value.profit)+"</td>"}
                            ${value.status == 'win' ? "<td class='text-success' style='font-weight:bold'>"+value.status+"</td>" :  "<td class='text-danger' style='font-weight:bold'>"+value.status+"</td>"}
                            <td>${date_time(value.created_at)}</td>
                        </tr>
                    `
                })
                $("#bot").html(tr)
            }).catch(err=>console.log(err))
        }

        function date_time(data){
            const date = new Date(data);
            return date.toISOString().slice(0, 10);
        }

        $(".btn-start").click(async function(e){
            e.preventDefault()
            let $form = $(this).closest("form")
            let symbol = $form.find(".bot-symbols").val()
            let name    = $form.find(".bot-name").val()
            let amount  = $form.find(".bot-amount").val()
            let type    = $form.find(".bot-type").val()
            let id      = $form.find(".bot-id").val()
            let is_auto_renew = $form.find(".bot-autorenew").is(':checked') ? 1 : 0;

            if(!symbol || symbol.length === 0){
                toastr.warning('Please select at least one asset', 'Required');
                return;
            }

            if(amount == ""){
                toastr.warning('Please enter an amount', 'Required');
                return;
            }
            if(confirm('Are you sure you want to start this bot? Total cost will be $' + (amount * symbol.length).toFixed(2))){
                let data ={ id, symbol, name, amount, type, is_auto_renew }
                const options = {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    body: JSON.stringify(data),
                };
                
                fetch("{{route('bots.start')}}",options)
                .then((res)=>res.json())
                .then((data)=>{
                    if(data.message && !data.status){
                        toastr.error(data.message, 'Warning');
                    }else{
                        toastr.success("Bot has been activated successfully!", 'Success');
                        $form.find(".btn-start").hide()
                        $form.find(".bot-loader").show()
                        $form.find(".bot-message").html('<div class="alert alert-success mt-4"><i class="fa fa-check-circle"></i> Bot is now active.</div>')
                        setTimeout(() => location.reload(), 2000);
                    }
                }).catch(err => {
                    toastr.error("Failed to start bot. Please try again.", 'Error');
                })
            }
        })

        $(".btn-stop").click(function(e){
            e.preventDefault()
            let $form = $(this).closest("form")
            let id    = $form.find(".bot-id").val()

            if(confirm('Are you sure you want to stop this bot?')){
                $form.find(".btn-stop").prop("disabled",true).text("Stopping...")
                let data ={ id }
                const options = {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    body: JSON.stringify(data),
                };
                fetch("{{route('bots.stop')}}",options)
                .then(res => res.json())
                .then(function(data){
                    if(data.status){
                        toastr.info("Bot has been stopped.", 'Stopped');
                        setTimeout(() => location.reload(), 1500);
                    }
                }).catch(err => {
                    toastr.error("Failed to stop bot.", 'Error');
                    $form.find(".btn-stop").prop("disabled",false).text("Stop Bot")
                })
            }
        })
    });
</script>

@if(session('error'))
<script>toastr.error("{{session('error')}}",'error')</script>
@endif
@if(session('status'))
<script>toastr.success("{{session('status')}}",'successful')</script>
@endif
@endsection()
