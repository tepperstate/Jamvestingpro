<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="{{ asset('new/vendor_components/bootstrap/dist/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('new/vendor_components/animate/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('new/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('new/css/skin_color.css') }}">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 20px;
            background-color: #f8f9fa;
            color: #343a40;
        }

        .header {
            margin-bottom: 20px;
            padding: 20px;
            background-color: #007bff;
            color: #fff;
            border-radius: 8px;
            text-align: center;
            position: relative;
        }

        .header img {
            max-width: 150px;
            height: auto;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 24px;
            margin: 0;
        }

        .info {
            /* background-color: #fff; */
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .info span {
            display: block;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .table {
            width: 100%;
            margin-bottom: 30px; /* Space between tables */
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .table thead {
            background-color: #007bff;
            color: #fff;
            font-size: 16px;
        }

        .table thead th {
            padding: 12px 15px;
            text-align: center;
        }

        .table tbody tr {
            font-size: 14px;
            color: #495057;
        }

        .table tbody tr:nth-child(odd) {
            background-color: #f8f9fa;
        }

        .table tbody tr:nth-child(even) {
            background-color: #fff;
        }

        .table tbody td {
            padding: 10px 15px;
            text-align: center;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }

        .section-header {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #343a40;
        }
    </style>
</head>
<body>
    <div class="header">
        <img style="width:130px" src="{{ asset('storage/image/' . 'logo_dark.svg?v=2') }}" alt="Logo">
        <h1>Transaction History</h1>
    </div>


    <div class="section-header">Overall Summary</div>
    <table id="summary" class="table table-bordered">
        <thead>
            <tr>
                <th>Total Deposit</th>
                <th>Total Withdrawal</th>
                <th>Total Trades</th>
                <th>Total Wins</th>
                <th>Total Loss</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>${{ number_format($data['deposit'], 2) }}</td>
                <td>${{ number_format($data['withdrawal'], 2) }}</td>
                <td>{{ number_format($data['count'], 2) }}</td>
                <td>${{ number_format($data['win'], 2) }}</td>
                <td>${{ number_format($data['loss'], 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-header">Deposit History</div>
    <table id="deposits" class="table table-bordered">
        <thead>
            <tr>
                <th>SN</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($deposits as $key => $d)
                <tr>
                    <td>{{ ++$key }}</td>
                    <td>${{ number_format($d->amount, 2) }}</td>
                    <td>{{ $d->pay_currency }}</td>
                    <td>{{ $d->status }}</td>
                    <td>{{ $d->created_at }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No deposit records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-header">Withdrawal History</div>
    <table id="withdrawals" class="table table-bordered">
        <thead>
            <tr>
                <th>SN</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($withdrawals as $key => $d)
                <tr>
                    <td>{{ ++$key }}</td>
                    <td>${{ number_format($d->amount, 2) }}</td>
                    <td>{{ $d->type }}</td>
                    <td>{{ $d->status }}</td>
                    <td>{{ $d->created_at }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No withdrawal records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Generated on {{ now()->format('Y-m-d H:i:s') }}</p>
        <p>Thank you for using our service!</p>
    </div>
</body>
</html>
