<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Screenshot Render - {{ ucfirst($type) }}</title>
    <!-- Add same CSS as platform for accurate screenshots -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-main: #060709;
            --accent-primary: #0ea5e9;
            --glass-bg: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.05);
        }
        body {
            margin: 0;
            padding: 0;
            background: var(--bg-main);
            color: #ffffff;
            font-family: 'Outfit', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .capture-zone {
            width: 100%;
            max-width: 800px;
            background: #000000;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 0 50px rgba(0,0,0,0.5);
            padding-bottom: 20px;
        }
        .header-strip {
            background: rgba(14, 165, 233, 0.1);
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(14, 165, 233, 0.2);
        }
        .table-responsive {
            padding: 10px 30px;
        }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
            font-size: 13px;
        }
        th {
            color: rgba(255, 255, 255, 0.4);
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 1px;
            padding: 15px;
        }
        td {
            padding: 15px;
            background: rgba(255, 255, 255, 0.03);
        }
        td:first-child { border-radius: 12px 0 0 12px; }
        td:last-child { border-radius: 0 12px 12px 0; }
        
        .watermark {
            text-align: center;
            padding: 15px;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.2);
            letter-spacing: 2px;
            font-weight: 800;
        }
    </style>
</head>
<body>
    <div class="capture-zone" id="captureZone">
        <div class="header-strip">
            <div>
                <h3 style="margin:0; font-weight:800; font-size:22px; color:var(--accent-primary); letter-spacing:0.5px">
                    <i class="ri-history-line"></i> {{ strtoupper($type) }} AUDIT
                </h3>
                <div style="font-size:11px; color:rgba(255,255,255,0.5); margin-top:5px; font-weight:600; letter-spacing:1px">
                    CLIENT: {{ strtoupper($user->first_name . ' ' . $user->last_name) }}
                </div>
            </div>
            <div style="text-align:right">
                <div style="font-size:24px; font-weight:900; color:#fff">P2B</div>
                <div style="font-size:10px; color:rgba(255,255,255,0.4); font-family:monospace">{{ $generated_at }}</div>
            </div>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        @if($type === 'trades')
                            <th style="text-align:left;">Asset</th>
                            <th style="text-align:center;">Type</th>
                            <th style="text-align:right;">Invest</th>
                            <th style="text-align:right;">Payout</th>
                            <th style="text-align:center;">Result</th>
                        @elseif($type === 'deposits')
                            <th style="text-align:left;">Method</th>
                            <th style="text-align:right;">Amount</th>
                            <th style="text-align:center;">Status</th>
                            <th style="text-align:right;">Date</th>
                        @elseif($type === 'withdrawals')
                            <th style="text-align:left;">Target Wallet</th>
                            <th style="text-align:right;">Amount</th>
                            <th style="text-align:center;">Status</th>
                            <th style="text-align:right;">Date</th>
                        @elseif($type === 'mutual_funds')
                            <th style="text-align:left;">Asset/Fund</th>
                            <th style="text-align:right;">Invested</th>
                            <th style="text-align:right;">Profit</th>
                            <th style="text-align:center;">Growth</th>
                            <th style="text-align:right;">Date</th>
                        @elseif($type === 'vip_stocks' || $type === 'stocks')
                            <th style="text-align:left;">Ticker</th>
                            <th style="text-align:right;">Valuation</th>
                            <th style="text-align:right;">Profit</th>
                            <th style="text-align:center;">Growth</th>
                            <th style="text-align:right;">Status</th>
                        @else
                            <th style="text-align:left;">Record</th>
                            <th style="text-align:center;">Details</th>
                            <th style="text-align:right;">Date</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $row)
                        <tr>
                            @if($type === 'trades')
                                @php
                                    $sym = explode('/', $row->symbol ?? 'BTC/USDT')[0];
                                    $status = strtolower($row->status ?? '');
                                    $isWin = $status === 'win';
                                    $isLoss = $status === 'loss';
                                    $badge = $isWin ? ['bg' => '#ff3333', 'shadow' => 'rgba(16,185,129,0.3)', 'text' => '#fff'] :
                                             ($isLoss ? ['bg' => '#ef4444', 'shadow' => 'rgba(239,68,68,0.3)', 'text' => '#fff'] : 
                                             ['bg' => '#f59e0b', 'shadow' => 'transparent', 'text' => '#000']);
                                @endphp
                                <td>
                                    <div style="display:flex;align-items:center;gap:12px">
                                        <img src="https://img.logokit.com/crypto/{{$sym}}?token=pk_frb53d906f2b73e352f31e" onerror="this.src='https://ui-avatars.com/api/?name={{$sym}}&background=000&color=fff'" style="width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,0.05);padding:3px;">
                                        <span style="font-weight:800;letter-spacing:0.5px">{{ $row->symbol ?? 'BTC/USDT' }}</span>
                                    </div>
                                </td>
                                <td style="text-align:center;">
                                    <span style="color:{{ (strtolower($row->type??'call') == 'call' || strtolower($row->type??'buy') == 'buy') ? '#ff3333' : '#ef4444' }}; font-weight:800; font-size:10px;">{{ strtoupper($row->type ?? 'CALL') }}</span>
                                </td>
                                <td style="text-align:right;font-weight:700">${{ number_format($row->amount ?? 0, 2) }}</td>
                                <td style="text-align:right;font-weight:800;color:{{ $isWin ? '#ff3333' : '#fff' }}">${{ number_format($row->payout ?? 0, 2) }}</td>
                                <td style="text-align:center;">
                                    <span style="padding:6px 12px;border-radius:8px;font-size:10px;font-weight:900;background:{{$badge['bg']}};color:{{$badge['text']}};box-shadow:0 0 15px {{$badge['shadow']}}">{{ strtoupper($status) }}</span>
                                </td>
                            @elseif($type === 'deposits')
                                @php
                                    $status = strtolower($row->status ?? '');
                                    $isApproved = in_array($status, ['success', '1', 'approved']);
                                @endphp
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px">
                                        <i class="ri-arrow-down-circle-line" style="color:#ff3333;font-size:1.2rem"></i>
                                        <span style="font-weight:600">{{ $row->type ?? 'Crypto' }}</span>
                                    </div>
                                </td>
                                <td style="text-align:right;font-weight:800">${{ number_format($row->amount ?? 0, 2) }}</td>
                                <td style="text-align:center;">
                                    <span style="padding:5px 10px;border-radius:6px;font-size:10px;font-weight:800;background:{{ $isApproved ? 'rgba(16,185,129,0.1)' : 'rgba(245,158,11,0.1)' }};color:{{ $isApproved ? '#ff3333' : '#f59e0b' }}">{{ $isApproved ? 'APPROVED' : 'PENDING' }}</span>
                                </td>
                                <td style="text-align:right;color:rgba(255,255,255,0.4);font-size:11px">{{ explode(' ', $row->created_at)[0] }}</td>
                            @elseif($type === 'withdrawals')
                                @php
                                    $status = strtolower($row->status ?? '');
                                    $isApproved = in_array($status, ['success', '1', 'approved']);
                                @endphp
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px">
                                        <i class="ri-arrow-up-circle-line" style="color:#ef4444;font-size:1.2rem"></i>
                                        <span style="font-weight:600;font-family:monospace;color:rgba(255,255,255,0.7)">{{ substr($row->wallet??'0x000',0,6).'...'.substr($row->wallet??'0x000',-4) }}</span>
                                    </div>
                                </td>
                                <td style="text-align:right;font-weight:800">${{ number_format($row->amount ?? 0, 2) }}</td>
                                <td style="text-align:center;">
                                    <span style="padding:5px 10px;border-radius:6px;font-size:10px;font-weight:800;background:{{ $isApproved ? 'rgba(16,185,129,0.1)' : 'rgba(245,158,11,0.1)' }};color:{{ $isApproved ? '#ff3333' : '#f59e0b' }}">{{ $isApproved ? 'COMPLETED' : 'PENDING' }}</span>
                                </td>
                                <td style="text-align:right;color:rgba(255,255,255,0.4);font-size:11px">{{ explode(' ', $row->created_at)[0] }}</td>
                            @elseif($type === 'vip_stocks' || $type === 'stocks' || $type === 'mutual_funds')
                                <td>
                                    <div style="display:flex;align-items:center;gap:12px">
                                        <span style="font-weight:800;letter-spacing:0.5px">{{ $row->asset ?? $row->fund ?? 'ASSET' }}</span>
                                    </div>
                                </td>
                                <td style="text-align:right;font-weight:700">${{ number_format($row->valuation ?? $row->invested ?? 0, 2) }}</td>
                                <td style="text-align:right;font-weight:800;color:#ff3333">+${{ number_format($row->profit ?? 0, 2) }}</td>
                                <td style="text-align:center;">
                                    <span style="color:#ff3333;font-weight:800;font-size:11px"><i class="ri-arrow-up-line"></i> {{ str_replace('+', '', $row->growth ?? '0%') }}</span>
                                </td>
                                <td style="text-align:right;">
                                    <span style="padding:4px 8px;border-radius:4px;font-size:9px;font-weight:800;background:rgba(255,255,255,0.1);color:#fff">{{ strtoupper($row->status ?? $row->type ?? 'ACTIVE') }}</span>
                                </td>
                            @else
                                <td>Record Entry</td>
                                <td>{{ json_encode($row) }}</td>
                                <td>{{ $row->created_at ?? '' }}</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="watermark">PROVENANCE VERIFIED BY {{ site()->name }}</div>
    </div>
</body>
</html>
