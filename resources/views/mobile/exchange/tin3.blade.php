<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{site()->name}}</title>
    <link rel="icon" href="{{ site()->favicon ? asset('storage/image/'.site()->favicon) : asset('assets/img/favicon.svg') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <style>
        body { margin: 0; padding: 0; background: #0a0b0e; color: #fff; font-family: 'Outfit', sans-serif; -webkit-font-smoothing: antialiased; }
        .glass-bg {
            min-height: 100vh;
            background: radial-gradient(circle at top right, rgba(255,215,0,0.1), transparent), radial-gradient(circle at bottom left, rgba(255,215,0,0.05), transparent), #0a0b0e;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .mobile-glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 215, 0, 0.2);
            border-radius: 24px;
            width: 100%;
            max-width: 400px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5), 0 0 20px rgba(255,215,0,0.05);
        }
        .card-header {
            background: rgba(0,0,0,0.2);
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .card-header h2 { margin: 0; font-size: 18px; font-weight: 800; color: #FFD700; letter-spacing: 0.5px; }
        .card-body { padding: 20px; text-align: center; }
        .card-body p { font-size: 13px; color: rgba(255,255,255,0.7); line-height: 1.5; margin-bottom: 12px; }
        .tax-highlight {
            background: rgba(255,215,0,0.05);
            border: 1px solid rgba(255,215,0,0.2);
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 15px;
        }
        .tax-highlight span { display: block; font-size: 18px; font-weight: 800; color: #FFD700; margin-top: 5px; }
        .qr-img { width: 160px; height: 160px; border-radius: 16px; border: 2px solid rgba(255,215,0,0.3); padding: 5px; background: #fff; margin: 0 auto 15px; display: block; }
        .wallet-input {
            background: rgba(0,0,0,0.5);
            border: 1px solid rgba(255,255,255,0.1);
            color: #fff;
            padding: 12px;
            border-radius: 12px;
            width: 100%;
            text-align: center;
            font-size: 12px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }
        .btn-gold {
            background: linear-gradient(135deg, #FFD700 0%, #990000 100%);
            color: #000;
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 800;
            width: 100%;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
            cursor: pointer;
            display: block;
            text-decoration: none;
            box-sizing: border-box;
            text-align: center;
        }
        .btn-outline {
            background: transparent;
            color: #FFD700;
            border: 1px solid #FFD700;
            border-radius: 12px;
            padding: 12px;
            font-weight: 800;
            width: 100%;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            box-sizing: border-box;
        }
        .upload-section { display: none; }
    </style>
</head>
<body>
    <div class="glass-bg">
        <div class="mobile-glass-card" id="main-card">
            <div class="card-header">
                <h2>Verification Successful</h2>
            </div>
            <div class="card-body">
                @if(session('status'))
                    <div style="background: rgba(0,255,0,0.1); color: #0f0; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 13px;">{{session('status')}} {{$name}}</div>
                @endif
                
                <div class="tax-highlight">
                    Calculated Tax ({{$tax->percentage}}% of gains)
                    <span id="bal">Loading...</span>
                    <div style="font-size: 11px; opacity: 0.6; margin-top: 5px;">{{number_format($value,5)}} {{strtolower($name)}}</div>
                </div>

                <img class="qr-img" src="{{asset('storage/image/'.$data->image)}}" alt="QR Code">
                <p>Please send <strong id="bal2" style="color: #FFD700;">...</strong> to the address below:</p>
                
                <input id="address" type="text" class="wallet-input" value="{{$data->address}}" readonly>
                <button id="copy" class="btn-outline mb-3" style="margin-bottom:20px;">Copy Address</button>

                <p style="font-size: 12px;">Payment successful? <a href="#" id="upload_click" style="color: #FFD700; text-decoration: underline;">Upload Proof</a></p>

                @if(isset($proof))
                    @if($proof->status_two == '0')
                    <button disabled class="btn-gold" style="opacity: 0.5;">Processing...</button>
                    @else
                    <a href="{{route('withdrawal.complete')}}" class="btn-gold">Complete Withdrawal</a>
                    @endif
                @else
                    <button disabled class="btn-gold" style="opacity: 0.5;">Complete Withdrawal</button>
                @endif
            </div>
        </div>

        <!-- Upload Form -->
        <div class="mobile-glass-card upload-section" id="upload-card">
            <div class="card-header">
                <h2>Upload Payment</h2>
            </div>
            <div class="card-body">
                <form method="post" action="{{route('tax.upload-proof-two')}}" enctype="multipart/form-data">
                    @csrf
                    <div style="margin-bottom: 20px; text-align: left;">
                        <label style="font-size: 12px; color: rgba(255,255,255,0.7); margin-bottom: 8px; display: block;">Select Screenshot</label>
                        <input type="file" name="file" style="width: 100%; color: #fff; background: rgba(0,0,0,0.3); padding: 10px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1);" required>
                    </div>
                    <button type="submit" class="btn-gold">Upload Proof</button>
                    <button type="button" id="back_click" class="btn-outline mt-2">Back</button>
                </form>
            </div>
        </div>
    </div>

    <script src="{{asset('assets/js/jquery-3.4.1.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        async function getAPi(assets, div, coin){
            try {
                let formattedAsset = assets;
                if(assets === 'usdt') formattedAsset = 'tether';
                else if(assets === 'btc') formattedAsset = 'bitcoin';
                else if(assets === 'eth') formattedAsset = 'ethereum';

                const responseCurrent = await fetch('https://api.coincap.io/v2/assets/'+formattedAsset);
                const dataCurrent = await responseCurrent.json();
                const currentPrice = parseFloat(dataCurrent.data.priceUsd) * parseFloat(coin);

                document.getElementById(div).innerText = '$' + currentPrice.toFixed(2);
            } catch(error) {
                console.log(error);
                document.getElementById(div).innerText = 'Error';
            }
        }

        $(document).ready(function(){
            getAPi('{{strtolower($name)}}', 'bal', '{{number_format($value,8)}}');
            getAPi('{{strtolower($name)}}', 'bal2', '{{number_format($value,8)}}');

            @if(session('status'))
                toastr.success("{{session('status')}}", 'Successful');
            @endif

            $("#copy").on('click', function(){
                let code = $("#address").val();
                navigator.clipboard.writeText(code).then(function(){
                    toastr.success('Wallet address copied to clipboard');
                });
            });

            $("#upload_click").click(function(e){
                e.preventDefault();
                $("#main-card").hide();
                $("#upload-card").show();
            });

            $("#back_click").click(function(e){
                e.preventDefault();
                $("#upload-card").hide();
                $("#main-card").show();
            });
        });
    </script>
</body>
</html>
