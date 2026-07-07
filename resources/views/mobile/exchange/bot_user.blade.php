@extends('layouts.user.app')
@section('content')

<style>
/* Glassmorphism Mobile Theme for Bot User */
.mobile-bot-user-container {
    padding: 15px;
    background: #0b0e14;
    min-height: 100vh;
    color: #e2e8f0;
    padding-bottom: 80px;
}
.glass-card-mobile {
    background: rgba(16, 18, 27, 0.6);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 215, 0, 0.15); /* Gold accent border */
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
}
.gold-text {
    color: #ffd700;
}
.gold-gradient-text {
    background: linear-gradient(135deg, #ffd700, #ffaa00);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.form-control-glass {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 215, 0, 0.2);
    color: #fff;
    border-radius: 10px;
    padding: 12px 15px;
}
.form-control-glass:focus {
    background: rgba(255, 255, 255, 0.08);
    border-color: #ffd700;
    color: #fff;
    box-shadow: 0 0 0 0.2rem rgba(255, 215, 0, 0.25);
}
.form-control-glass:disabled {
    background: rgba(255, 255, 255, 0.02);
    color: #94a3b8;
}
.btn-gold {
    background: linear-gradient(135deg, #ffd700, #ffaa00);
    color: #000;
    border: none;
    border-radius: 12px;
    font-weight: 700;
    padding: 12px;
    box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
}
.btn-gold:hover {
    color: #000;
    opacity: 0.9;
}
.btn-stop-bot {
    background: rgba(220, 53, 69, 0.2);
    color: #ff4d4d;
    border: 1px solid rgba(220, 53, 69, 0.4);
    border-radius: 12px;
    font-weight: 700;
    padding: 12px;
}
.history-item {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    padding: 15px;
    margin-bottom: 12px;
}
.status-pulse {
    box-shadow: 0 0 0 0 rgba(255, 51, 51, 0.7);
    animation: pulse-mobile 2s infinite;
}
@keyframes pulse-mobile {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 51, 51, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(255, 51, 51, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 51, 51, 0); }
}
.diagnostic-box-mobile {
    background: rgba(0, 0, 0, 0.5);
    border-radius: 10px;
    border: 1px solid rgba(255, 215, 0, 0.1);
    padding: 15px;
    margin: 15px 0;
}
.log-line {
    font-family: 'Courier New', Courier, monospace;
    font-size: 11px;
    line-height: 1.6;
    color: #a0aec0;
}
</style>

<div class="mobile-bot-user-container">
    <div class="d-flex align-items-center mb-4">
        <a href="javascript:history.back()" class="text-white me-3" style="font-size: 1.5rem;">
            <i class="ri-arrow-left-s-line"></i>
        </a>
        <h4 class="mb-0 gold-gradient-text font-weight-bold" style="font-family: 'Outfit', sans-serif;">Deploy Bot</h4>
    </div>

    @forelse ($data as $datas)
    <div class="glass-card-mobile">
        <form>
            @csrf
            <div class="form-group mb-3">
                <label class="small text-secondary mb-1">Bot Name</label>
                <input type="text" class="form-control form-control-glass bot-name" value="{{$datas->name}}" disabled>
            </div>
            
            <input type="hidden" name="id" class="form-control bot-id" value="{{$datas->bot_id}}">

            <div class="form-group mb-3">
                <label class="small text-secondary mb-1">Select Assets (Multiple)</label>
                <select class="form-control form-control-glass bot-symbols" name="symbols[]" multiple required style="height: 100px;">
                    @forelse ($b as $c)
                        <option value="{{$c->symbols}}">{{$c->symbols}}</option>
                    @empty
                    @endforelse
                </select>
            </div>

            <div class="form-group mb-3">
                <label class="small text-secondary mb-1">Trade Type</label>
                <select class="form-control form-control-glass bot-type" name="type" required>
                    <option>Buy</option>
                    <option>Sell</option>
                </select>
            </div>

            <div class="form-group mb-3">
                <label class="small text-secondary mb-1">Amount (Per Asset)</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-0 text-white" style="position: absolute; z-index: 10; left: 10px; top: 12px;">$</span>
                    <input type="number" name="amount" class="form-control form-control-glass bot-amount" required value="{{$datas->min}}" style="padding-left: 30px;">
                </div>
            </div>

            <div class="form-group mb-4 p-3 rounded" style="background: rgba(255, 215, 0, 0.05); border: 1px solid rgba(255, 215, 0, 0.2);">
                <div class="form-check form-switch d-flex align-items-center">
                    <input class="form-check-input bot-autorenew" type="checkbox" id="autoRenew-{{$datas->bot_id}}" style="width: 2.5em; height: 1.25em; margin-top: 0; margin-right: 10px;">
                    <label class="form-check-label text-white small" for="autoRenew-{{$datas->bot_id}}">Enable Auto-Renew</label>
                </div>
                <div class="small text-secondary mt-2" style="font-size: 0.75rem;">
                    Bot will automatically restart with the same parameters and deduct balance upon expiration.
                </div>
            </div>

            <div class="bot-status-container"> 
                @if(isset($user_bot->id))
                    <div class="d-flex align-items-center mb-2">
                        <div class="status-pulse bg-success" style="width: 10px; height: 10px; border-radius: 50%; margin-right: 10px;"></div>
                        <span class="text-success small fw-bold tracking-wider">BOT IS RUNNING</span>
                    </div>
                    
                    <div class="diagnostic-box-mobile">
                        <p class="text-muted small mb-2 text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;"><i class="ri-terminal-box-line me-1"></i> Diagnostic Stream</p>
                        <div id="diagnostic-log-{{$datas->bot_id}}" class="diagnostic-log" style="height: 80px; overflow-y: hidden;">
                            <div class="log-line">> Initializing handshakes...</div>
                            <div class="log-line">> Syncing liquidity...</div>
                            <div class="log-line">> Awaiting market pulse...</div>
                        </div>
                    </div>
                    
                    <button type="button" class="btn w-100 btn-stop-bot btn-stop py-3">
                        <i class="ri-stop-circle-line me-2"></i> Stop Bot
                    </button>
                    <p class="mt-3 text-info small text-center"><i class="fa fa-spinner fa-spin me-1"></i> Active & generating results...</p>
                @else
                    <button type="button" class="btn btn-gold w-100 btn-start py-3 mb-2">
                        <i class="ri-play-circle-line me-2"></i> Start Bot
                    </button>
                    <div class="text-center">
                        <img class="bot-loader" style="display:none; width: 40px; margin: 10px auto;" src="{{asset('loading.gif')}}">
                    </div>
                    <button type="button" class="btn w-100 btn-stop-bot btn-stop py-3" style="display:none">
                        <i class="ri-stop-circle-line me-2"></i> Stop Bot
                    </button>
                @endif
            </div> 
            <div class="mt-3 bot-message text-center small"></div>
        </form>
    </div>
    @empty
    <div class="glass-card-mobile text-center py-5">
        <i class="ri-robot-2-line" style="font-size: 3rem; color: rgba(255,255,255,0.2);"></i>
        <p class="text-secondary mt-3 mb-0">No active bot found.</p>
    </div>
    @endforelse

    <div class="mt-5 mb-3">
        <h5 class="gold-text font-weight-bold" style="font-family: 'Outfit', sans-serif;">Trade History</h5>
    </div>
    
    <div id="bot-history-container">
        @forelse ($result as $key => $d)
        <div class="history-item">
            <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2" style="border-color: rgba(255,255,255,0.05) !important;">
                <div>
                    <strong class="text-white">{{$d->name}}</strong>
                    <span class="badge bg-dark border border-secondary text-secondary ms-2" style="font-size: 0.65rem;">{{$d->symbol}}</span>
                </div>
                <div class="small text-secondary">{{ \Illuminate\Support\Carbon::parse($d->created_at)->format('d M y') }}</div>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="small text-secondary">Volume</div>
                    <div class="text-white fw-bold">${{number_format($d->amount)}}</div>
                </div>
                <div class="text-end">
                    <div class="small text-secondary">P/L</div>
                    <div class="{{ $d->status == 'win' ? 'text-success' : 'text-danger' }} fw-bold">
                        ${{number_format($d->profit)}}
                    </div>
                </div>
                <div class="text-end">
                    <div class="small text-secondary">Status</div>
                    <div class="{{ $d->status == 'win' ? 'text-success' : 'text-danger' }} small text-uppercase fw-bold">
                        {{$d->status}}
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-4">
            <p class="text-secondary small">No trading history available.</p>
        </div>
        @endforelse
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const logEntries = [
        "Analyzing market volatility...",
        "Scanning opportunities...",
        "Adjusting risk parameters...",
        "Calculating EMA crossover...",
        "Validating order depth...",
        "Optimizing gas fees...",
        "Monitoring whale wallets...",
        "Awaiting buy signal...",
        "Executing micro-hedging...",
        "Updating internal ledger..."
    ];

    function updateDiagnosticLogs() {
        $(".diagnostic-log").each(function() {
            const $log = $(this);
            const randomEntry = logEntries[Math.floor(Math.random() * logEntries.length)];
            const $newLine = $(`<div class="log-line">> ${randomEntry}</div>`).css({ opacity: 0 });
            
            $log.append($newLine);
            $newLine.animate({ opacity: 1 }, 500);
            
            if ($log.children().length > 4) {
                $log.children().first().animate({ opacity: 0 }, 500, function() {
                    $(this).remove();
                });
            }
        });
    }

    $(document).ready(function(){
        setInterval(function(){
            trade();
        }, 5000);

        setInterval(updateDiagnosticLogs, 4000);

        function thousands_separators(num){
            var num_parts = num.toString().split(".");
            num_parts[0] = num_parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            return num_parts.join(".");
        }

        function date_time(data){
            const date = new Date(data);
            return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: '2-digit' });
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
            
            fetch("/dashboard/bot/user/json/"+id, options)
            .then((res)=>res.json())
            .then((data)=>{
                let html = "";
                if(data.data.length === 0) {
                    html = '<div class="text-center py-4"><p class="text-secondary small">No trading history available.</p></div>';
                } else {
                    data.data.forEach(function(value){
                        let plClass = value.status == 'win' ? 'text-success' : 'text-danger';
                        html += `
                        <div class="history-item">
                            <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2" style="border-color: rgba(255,255,255,0.05) !important;">
                                <div>
                                    <strong class="text-white">${value.name}</strong>
                                    <span class="badge bg-dark border border-secondary text-secondary ms-2" style="font-size: 0.65rem;">${value.symbol}</span>
                                </div>
                                <div class="small text-secondary">${date_time(value.created_at)}</div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="small text-secondary">Volume</div>
                                    <div class="text-white fw-bold">$${thousands_separators(value.amount)}</div>
                                </div>
                                <div class="text-end">
                                    <div class="small text-secondary">P/L</div>
                                    <div class="${plClass} fw-bold">$${thousands_separators(value.profit)}</div>
                                </div>
                                <div class="text-end">
                                    <div class="small text-secondary">Status</div>
                                    <div class="${plClass} small text-uppercase fw-bold">${value.status}</div>
                                </div>
                            </div>
                        </div>`;
                    });
                }
                $("#bot-history-container").html(html);
            }).catch(err=>console.log(err));
        }

        $(".btn-start").click(function(e){
            e.preventDefault();
            let $form = $(this).closest("form");
            let symbol = $form.find(".bot-symbols").val();
            let name    = $form.find(".bot-name").val();
            let amount  = $form.find(".bot-amount").val();
            let type    = $form.find(".bot-type").val();
            let id      = $form.find(".bot-id").val();
            let is_auto_renew = $form.find(".bot-autorenew").is(':checked') ? 1 : 0;

            if(!symbol || symbol.length === 0){
                toastr.warning('Please select at least one asset', 'Required');
                return;
            }

            if(amount == ""){
                toastr.warning('Please enter an amount', 'Required');
                return;
            }
            
            if(confirm('Start this bot? Total cost will be $' + (amount * symbol.length).toFixed(2))){
                let data = { id, symbol, name, amount, type, is_auto_renew };
                const options = {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    body: JSON.stringify(data),
                };
                
                fetch("{{route('bots.start')}}", options)
                .then((res)=>res.json())
                .then((data)=>{
                    if(data.message && !data.status){
                        toastr.error(data.message, 'Warning');
                    } else {
                        toastr.success("Bot activated!", 'Success');
                        $form.find(".btn-start").hide();
                        $form.find(".bot-loader").show();
                        $form.find(".bot-message").html('<div class="text-success mt-2 small"><i class="fa fa-check-circle"></i> Active</div>');
                        setTimeout(() => location.reload(), 2000);
                    }
                }).catch(err => {
                    toastr.error("Failed to start bot.", 'Error');
                });
            }
        });

        $(".btn-stop").click(function(e){
            e.preventDefault();
            let $form = $(this).closest("form");
            let id    = $form.find(".bot-id").val();

            if(confirm('Stop this bot?')){
                $form.find(".btn-stop").prop("disabled", true).html('<i class="ri-loader-4-line fa-spin me-2"></i> Stopping...');
                let data = { id };
                const options = {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    body: JSON.stringify(data),
                };
                fetch("{{route('bots.stop')}}", options)
                .then(res => res.json())
                .then(function(data){
                    if(data.status){
                        toastr.info("Bot stopped.", 'Stopped');
                        setTimeout(() => location.reload(), 1500);
                    }
                }).catch(err => {
                    toastr.error("Failed to stop bot.", 'Error');
                    $form.find(".btn-stop").prop("disabled", false).html('<i class="ri-stop-circle-line me-2"></i> Stop Bot');
                });
            }
        });
    });
</script>

@if(session('error'))
<script>toastr.error("{{session('error')}}",'error')</script>
@endif
@if(session('status'))
<script>toastr.success("{{session('status')}}",'successful')</script>
@endif
@endsection
