<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Trade History Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1a1a2e;
            background: #ffffff;
            font-size: 11px;
            line-height: 1.4;
        }
        .header {
            background: linear-gradient(135deg, #0a0a1a 0%, #16213e 100%);
            color: #ffffff;
            padding: 30px 40px;
            text-align: center;
        }
        .header h1 {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 10px;
            color: rgba(255,255,255,0.5);
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .meta-bar {
            background: #f8f9fc;
            padding: 15px 40px;
            border-bottom: 1px solid #e8ecf4;
            display: flex;
            justify-content: space-between;
        }
        .meta-bar .item { }
        .meta-bar .label {
            font-size: 8px;
            text-transform: uppercase;
            color: #9ca3af;
            letter-spacing: 1px;
            font-weight: 600;
        }
        .meta-bar .value {
            font-size: 12px;
            font-weight: 700;
            color: #1a1a2e;
        }
        .summary-row {
            padding: 20px 40px;
            border-bottom: 1px solid #e8ecf4;
        }
        .summary-row table {
            width: 100%;
        }
        .summary-row td {
            text-align: center;
            padding: 10px;
        }
        .summary-row .num {
            font-size: 20px;
            font-weight: 800;
        }
        .summary-row .desc {
            font-size: 8px;
            text-transform: uppercase;
            color: #9ca3af;
            letter-spacing: 1px;
        }
        .green { color: #ff3333; }
        .red { color: #ef4444; }
        .blue { color: #3b82f6; }

        .content { padding: 20px 40px; }
        .content h3 {
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 12px;
            color: #1a1a2e;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        table.trades {
            width: 100%;
            border-collapse: collapse;
        }
        table.trades thead th {
            background: #f1f5f9;
            padding: 8px 10px;
            text-align: left;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            font-weight: 700;
            border-bottom: 2px solid #e2e8f0;
        }
        table.trades tbody td {
            padding: 8px 10px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 10px;
        }
        table.trades tbody tr:nth-child(even) {
            background: #fafbfd;
        }
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-win { background: #d1fae5; color: #065f46; }
        .badge-loss { background: #fee2e2; color: #991b1b; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-draw { background: #e0e7ff; color: #3730a3; }

        .footer {
            background: #f8f9fc;
            padding: 15px 40px;
            text-align: center;
            border-top: 1px solid #e8ecf4;
            margin-top: 20px;
        }
        .footer p {
            font-size: 8px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $siteName }}</h1>
        <p>Trade History Report &mdash; Confidential</p>
    </div>

    <table style="width:100%;border-collapse:collapse;">
        <tr>
            <td style="background:#f8f9fc;padding:15px 40px;border-bottom:1px solid #e8ecf4;width:33%;">
                <div class="label" style="font-size:8px;text-transform:uppercase;color:#9ca3af;letter-spacing:1px;font-weight:600;">Account Holder</div>
                <div class="value" style="font-size:12px;font-weight:700;color:#1a1a2e;">{{ $userName }}</div>
            </td>
            <td style="background:#f8f9fc;padding:15px 40px;border-bottom:1px solid #e8ecf4;width:33%;text-align:center;">
                <div class="label" style="font-size:8px;text-transform:uppercase;color:#9ca3af;letter-spacing:1px;font-weight:600;">Report Date</div>
                <div class="value" style="font-size:12px;font-weight:700;color:#1a1a2e;">{{ $reportDate }}</div>
            </td>
            <td style="background:#f8f9fc;padding:15px 40px;border-bottom:1px solid #e8ecf4;width:33%;text-align:right;">
                <div class="label" style="font-size:8px;text-transform:uppercase;color:#9ca3af;letter-spacing:1px;font-weight:600;">Total Trades</div>
                <div class="value" style="font-size:12px;font-weight:700;color:#1a1a2e;">{{ $totalTrades }}</div>
            </td>
        </tr>
    </table>

    <div class="summary-row">
        <table>
            <tr>
                <td>
                    <div class="num green">${{ number_format($totalProfit, 2) }}</div>
                    <div class="desc">Total Profit</div>
                </td>
                <td>
                    <div class="num red">${{ number_format($totalLoss, 2) }}</div>
                    <div class="desc">Total Loss</div>
                </td>
                <td>
                    <div class="num blue">${{ number_format($netPL, 2) }}</div>
                    <div class="desc">Net P/L</div>
                </td>
                <td>
                    <div class="num" style="color:#8b5cf6;">{{ $winRate }}%</div>
                    <div class="desc">Win Rate</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="content">
        <h3>Transaction Ledger</h3>
        <table class="trades">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Trade ID</th>
                    <th>Asset</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Strike Price</th>
                    <th>P/L</th>
                    <th>Duration</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($trades as $index => $trade)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="font-weight:700;">{{ $trade->trade_id }}</td>
                    <td style="font-weight:700;">{{ $trade->symbol }}</td>
                    <td style="color:{{ $trade->type === 'call' ? '#ff3333' : '#ef4444' }};font-weight:700;">
                        {{ strtoupper($trade->type) }}
                    </td>
                    <td style="font-weight:600;">${{ number_format($trade->amount, 2) }}</td>
                    <td>{{ $trade->strike_rate ?: 'N/A' }}</td>
                    <td style="font-weight:700;color:{{ $trade->status === 'win' ? '#ff3333' : ($trade->status === 'loss' ? '#ef4444' : '#64748b') }}">
                        @if($trade->status === 'win')
                            +${{ number_format($trade->p_l, 2) }}
                        @elseif($trade->status === 'loss')
                            -${{ number_format($trade->p_l, 2) }}
                        @else
                            $0.00
                        @endif
                    </td>
                    <td>{{ $trade->expire_time }}</td>
                    <td>
                        <span class="badge badge-{{ $trade->status }}">{{ strtoupper($trade->status) }}</span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($trade->created_at)->format('M d, Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>This document is system-generated and does not require a physical signature.</p>
        <p>Report Reference: {{ strtoupper(bin2hex(random_bytes(8))) }} &mdash; Generated {{ now()->toDateTimeString() }} UTC</p>
        <p>&copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.</p>
    </div>
</body>
</html>
