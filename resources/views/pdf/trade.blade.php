<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Trade Summary</title>
  <style>
    /* General Styling */
    body {
      font-family: 'Arial', sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f4f4f4;
    }
    .container {
      width: 90%;
      margin: auto;
      padding: 20px;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    h1, h4 {
      text-align: center;
      color: #333;
    }

    /* Summary Cards - Flexbox Layout */
    .summary-cards {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }
    .card {
      flex: 1; /* Ensures each card takes equal space */
      margin: 0 10px; /* Add spacing between cards */
      text-align: center;
      padding: 15px;
      border-radius: 8px;
      color: #fff;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      margin-top: 7px;
    }
    .card p {
      margin: 5px 0;
    }
    .total-trade {
      background: #007bff;
    }
    .trade-wins {
      background: #28a745;
    }
    .trade-loss {
      background: #dc3545;
    }

    /* Table Styling */
    table {
      width: 100%;
      border-collapse: collapse;
      margin: 20px 0;
    }
    thead {
      background: #080d13;
      color: #fff;
    }
    thead th {
      padding: 10px;
      font-size: 12px;
    }
    tbody tr {
      font-size: 12px;
    }
    tbody td {
      text-align: center;
      padding: 10px;
      border: 1px solid #ddd;
    }
    tbody tr:nth-child(even) {
      background: #f9f9f9;
    }
    tbody tr:nth-child(odd) {
      background: #fff;
    }

    /* Stacked Images */
    .image-text-container {
      display: flex;
      align-items: center;
    }
    .image-stack {
      display: flex;
      align-items: center;
    }
    .image-stack img {
      height: 20px;
      width: 20px;
      margin-right: 5px;
    }

    /* Footer */
    .footer {
      text-align: center;
      font-size: 12px;
      margin-top: 20px;
      color: #555;
    }
  </style>
</head>
<body>
  @php
   $imagePath = public_path('assets/img/logo_dark.svg?v=2');
   $base64Image = base64_encode(file_get_contents($imagePath));

@endphp
  <div class="container">
    <center>
      <!-- <img style="width:90px" src="{{public_path('assets/img/logo_dark.svg?v=2')}}" alt="Logo"> -->
      <img style="width:180px" src="data:image/png;base64,{{ $base64Image }}" alt="logo">
    </center>
    <h1>Trade Summary</h1>

    <!-- Summary Cards -->
    <div class="summary-cards">
      <div class="card total-trade">
        <p style="font-size: 18px; font-weight: bold;">{{ number_format($count) }}</p>
        <p style="font-size: 14px;">Total Trade</p>
      </div>
      <div class="card trade-wins">
        <p style="font-size: 18px; font-weight: bold;">USD {{ number_format($win, 2) }}</p>
        <p style="font-size: 14px;">Total Trade Wins (Manual Trade)</p>
      </div>
      <div class="card trade-loss">
        <p style="font-size: 18px; font-weight: bold;">USD {{ number_format($loss, 2) }}</p>
        <p style="font-size: 14px;">Total Trade  Loss (Manual Trade)</p>
      </div>
    </div>
    <div class="card trade-wins">
        <p style="font-size: 18px; font-weight: bold;">USD {{ number_format($win_copy, 2) }}</p>
        <p style="font-size: 14px;">Total Trade Wins (Copy Trade)</p>
      </div>
      <div class="card trade-loss">
        <p style="font-size: 18px; font-weight: bold;">USD {{ number_format($loss_copy, 2) }}</p>
        <p style="font-size: 14px;">Total Trade  Loss (Copy Trade)</p>
      </div>
    </div>

    <!-- Trade History Table -->
    <h4>Trade History</h4>
    <table>
      <thead>
        <tr>
          <th>S/N</th>
          <th>Order Type</th>
          <th>Market</th>
          <th>Asset</th>
          <th>Amount</th>
          <th>Profit/Loss</th>
          <th>Status</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($trade as $key => $va)
        <tr>
          <td>{{ ++$key }}</td>
          @if($va->type === 'call')
          <td style="color:green">Predicted A Rise</td>
          @else
          <td style="color:red">Predicted A Fall</td>
          @endif
          <td>{{ $va->exchanges->name ?? '' }}</td>
          <td>
            <div class="image-text-container">
              <div class="image-stack">
              </div>
              {{ $va->symbol }}
            </div>
          </td>
          <td>USD {{ number_format($va->amount, 2) }}</td>
          @if($va->status == 'pending')
          <td class="text-warning">USD {{ number_format($va->p_l, 2) }}</td>
          @elseif($va->status == "win")
          <td style="color:green">USD +{{ number_format($va->p_l - $va->amount, 2) }}</td>
          @else
          <td style="color:red">USD -{{ number_format($va->p_l, 2) }}</td>
          @endif
          <td>{{ $va->status }}</td>
          <td>{{ $va->created_at->format('d M, Y') }}</td>
        </tr>
        @empty
        <tr>
          <td colspan="11">No trades found</td>
        </tr>
        @endforelse
      </tbody>
    </table>

    <div class="footer">
      <p>Generated on {{ date('d M, Y') }}</p>
    </div>
  </div>
</body>
</html>
