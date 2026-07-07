@extends('layouts.admin.app')
@section('title', 'Screenshot Generator')
@section('content')
<div class="container-fluid">
    <a onclick="history.back()" href="javascript:void(0)">back</a>
    <div class="text-center mb-4">
        <h4 class="font-weight-bold m-0" style="color:var(--text-primary) !important">History Screenshot Generator</h4>
        <p class="text-muted mt-1">Generate branded screenshots of user histories</p>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="glass-card border-0 shadow p-4 mb-4">
                <h6 class="font-weight-bold mb-3" style="color:var(--accent-primary)">
                    <i data-lucide="settings" style="width:16px;vertical-align:middle"></i> Configuration
                </h6>
                <div class="form-group mb-3">
                    <label class="font-text">Select Existing User (Optional)</label>
                    <select class="form-control" id="userId">
                        <option value="">-- Manual Entry Mode --</option>
                        @foreach($users as $u)
                        <option value="{{$u->id}}">{{$u->first_name}} {{$u->last_name}} ({{$u->email}})</option>
                        @endforeach
                    </select>
                </div>
                <div id="manualInputSection">
                    <div class="form-group mb-3">
                        <label class="font-text">Manual Full Name</label>
                        <input type="text" class="form-control" id="manualName" placeholder="e.g. Satoshi Nakamoto">
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label class="font-text">History Type</label>
                    <select class="form-control" id="historyType">
                        <option value="trades">Trades</option>
                        <option value="deposits">Deposits</option>
                        <option value="withdrawals">Withdrawals</option>
                        <option value="signals">Signals</option>
                        <option value="mutual_funds">Mutual Funds</option>
                        <option value="vip_stocks">VIP Stocks</option>
                        <option value="stocks">Stocks Profits</option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label class="font-text">Manual Report Date (Optional)</label>
                    <input type="text" class="form-control" id="manualDate" placeholder="YYYY-MM-DD HH:MM:SS">
                    <small class="text-muted">Format: 2024-03-29 14:30:00</small>
                </div>
                <div class="form-group mb-4">
                    <label class="font-text">Entries: <span id="countVal" class="text-primary font-weight-bold">10</span></label>
                    <input type="range" class="custom-range" id="entryCount" min="1" max="50" value="10" oninput="document.getElementById('countVal').innerText=this.value">
                </div>
                <div id="tradeSpecificSettings" class="row mb-3">
                    <div class="col-6">
                        <label class="font-text x-small text-success">WIN TARGET</label>
                        <input type="number" class="form-control" id="winCount" value="6">
                    </div>
                    <div class="col-6">
                        <label class="font-text x-small text-danger">LOSS TARGET</label>
                        <input type="number" class="form-control" id="lossCount" value="4">
                    </div>
                </div>

                <div id="signalSpecificSettings" class="row mb-3" style="display:none">
                    <div class="col-6">
                        <label class="font-text x-small text-success">BUY COUNT</label>
                        <input type="number" class="form-control" id="buyCount" value="5">
                    </div>
                    <div class="col-6">
                        <label class="font-text x-small text-danger">SELL COUNT</label>
                        <input type="number" class="form-control" id="sellCount" value="5">
                    </div>
                </div>

                <div id="amountRangeSettings" class="row mb-3" style="display:none">
                    <div class="col-6">
                        <label class="font-text x-small text-primary">MIN AMOUNT</label>
                        <input type="number" class="form-control" id="minAmount" value="1000">
                    </div>
                    <div class="col-6">
                        <label class="font-text x-small text-primary">MAX AMOUNT</label>
                        <input type="number" class="form-control" id="maxAmount" value="5000">
                    </div>
                </div>
                <button class="btn btn-primary btn-block" id="generateBtn">
                    <i data-lucide="camera" style="width:14px;vertical-align:middle"></i> Generate Preview
                </button>
                <hr style="border-color:var(--glass-border)">
                <button class="btn btn-success btn-block" id="downloadPng" disabled>
                    <i data-lucide="download" style="width:14px;vertical-align:middle"></i> Download PNG
                </button>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="glass-card border-0 shadow p-4 mb-4">
                <h6 class="font-weight-bold mb-3" style="color:var(--accent-primary)">Preview</h6>
                <div id="previewArea" style="background: #060709; border-radius: 20px; padding: 30px; min-height: 400px; display:flex; align-items:center; justify-content:center;">
                    <div id="emptyState" class="text-center">
                        <i data-lucide="camera" style="width:48px;height:48px;color:var(--text-muted)"></i>
                        <p class="text-muted mt-3">Configure data and click Generate</p>
                    </div>
                    <div id="captureZone" style="display:none; width:100%; font-family: 'Outfit', sans-serif; background: #060709;">
                        <div style="text-align:center;padding: 40px 20px; border-bottom:1px solid rgba(255,255,255,0.05);margin-bottom:30px;">
                            <div class="logo-bg-premium" style="display: flex; width: 100%; justify-content: center; align-items: center; margin-bottom: 20px; background: transparent;">
                                <img src="{{asset('assets/img/favicon.svg')}}" alt="{{site()->name}}" style="width: 100%; height: 100%; object-fit: contain; transform: scale(1.0);">
                            </div>
                            <h4 style="color:#fff; font-weight: 800; margin-top: 15px; text-transform: uppercase; letter-spacing: 2px;" id="captureTitle">Report</h4>
                            <p style="color:rgba(255,255,255,0.4);font-size:12px;margin:0" id="captureSubtitle">Premium Architectural Ledger</p>
                        </div>
                        <div id="captureBody" style="padding: 0 10px;"></div>
                        <div style="text-align:center;padding: 30px 20px; border-top:1px solid rgba(255,255,255,0.05);margin-top:30px;">
                            <p style="color:rgba(255,255,255,0.2);font-size:11px;margin:0; text-transform: uppercase; letter-spacing: 1px;">AUTHENTICATED SESSION TOKEN: {{strtoupper(bin2hex(random_bytes(8)))}}</p>
                            <p style="color:rgba(255,255,255,0.15);font-size:10px;margin-top:5px">Generated at <span id="captureTimestamp"></span> UTC — Digital Audit Finalized</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
const csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;

document.getElementById('userId').addEventListener('change', function() {
    document.getElementById('manualInputSection').style.display = this.value ? 'none' : 'block';
});

document.getElementById('historyType').addEventListener('change', function() {
    const type = this.value;
    document.getElementById('tradeSpecificSettings').style.display = (type === 'trades') ? 'flex' : 'none';
    document.getElementById('signalSpecificSettings').style.display = (type === 'signals') ? 'flex' : 'none';
    document.getElementById('amountRangeSettings').style.display = (type === 'deposits' || type === 'withdrawals' || type === 'trades' || type === 'mutual_funds' || type === 'vip_stocks' || type === 'stocks') ? 'flex' : 'none';
});

// Initial state
const initialType = document.getElementById('historyType').value;
document.getElementById('tradeSpecificSettings').style.display = (initialType === 'trades') ? 'flex' : 'none';
document.getElementById('signalSpecificSettings').style.display = (initialType === 'signals') ? 'flex' : 'none';
document.getElementById('amountRangeSettings').style.display = (initialType === 'trades' || initialType === 'deposits' || initialType === 'withdrawals') ? 'flex' : 'none';

document.getElementById('generateBtn').addEventListener('click', async function() {
    const userId = document.getElementById('userId').value;
    const manualName = document.getElementById('manualName').value;
    const type = document.getElementById('historyType').value;
    const count = document.getElementById('entryCount').value;
    const wins = document.getElementById('winCount').value;
    const losses = document.getElementById('lossCount').value;
    const buyCount = document.getElementById('buyCount').value;
    const sellCount = document.getElementById('sellCount').value;
    const minAmount = document.getElementById('minAmount').value;
    const maxAmount = document.getElementById('maxAmount').value;

    this.disabled = true; this.innerHTML = '<i class="ri-loader-4-line ri-spin"></i> GENERATING...';

    try {
        const res = await fetch("{{route('admin.screenshot.generate')}}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ 
                user_id: userId, 
                manual_username: manualName, 
                type: type, 
                count: count,
                wins: wins,
                losses: losses,
                buy_count: buyCount,
                sell_count: sellCount,
                min_amount: minAmount,
                max_amount: maxAmount,
                manual_date: document.getElementById('manualDate').value
            })
        });
        const result = await res.json();
        renderPreview(result);
        document.getElementById('downloadPng').disabled = false;
    } catch(e) {
        toastr.error('Neural network link failed');
    }
    this.disabled = false; this.innerHTML = '<i data-lucide="camera" style="width:14px;vertical-align:middle"></i> Generate Preview';
    if(typeof lucide!=='undefined') lucide.createIcons();
});

function renderPreview(result) {
    document.getElementById('emptyState').style.display = 'none';
    document.getElementById('captureZone').style.display = 'block';
    document.getElementById('captureTitle').textContent = result.type + ' Audit';
    document.getElementById('captureSubtitle').textContent = 'CLIENT: ' + (result.user.first_name + ' ' + result.user.last_name).toUpperCase();
    document.getElementById('captureTimestamp').textContent = result.generated_at;

    let html = '<div class="table-responsive"><table style="width:100%;border-collapse:separate;border-spacing:0 8px;font-size:13px;color:#fff">';
    html += '<thead><tr style="color:rgba(255,255,255,0.4); text-transform: uppercase; font-size: 11px; letter-spacing: 1px;">';

    if (result.type === 'trades') {
        html += '<th style="padding:15px;text-align:left;">Asset</th><th style="padding:15px;text-align:center;">Type</th><th style="padding:15px;text-align:right;">Invest</th><th style="padding:15px;text-align:right;">Payout</th><th style="padding:15px;text-align:center;">Result</th>';
    } else if (result.type === 'deposits') {
        html += '<th style="padding:15px;text-align:left;">Method</th><th style="padding:15px;text-align:right;">Amount</th><th style="padding:15px;text-align:center;">Status</th><th style="padding:15px;text-align:right;">Date</th>';
    } else if (result.type === 'withdrawals') {
        html += '<th style="padding:15px;text-align:left;">Target Wallet</th><th style="padding:15px;text-align:right;">Amount</th><th style="padding:15px;text-align:center;">Status</th><th style="padding:15px;text-align:right;">Date</th>';
    } else if (result.type === 'mutual_funds') {
        html += '<th style="padding:15px;text-align:left;">Asset/Fund</th><th style="padding:15px;text-align:right;">Invested</th><th style="padding:15px;text-align:right;">Profit</th><th style="padding:15px;text-align:center;">Growth</th><th style="padding:15px;text-align:right;">Date</th>';
    } else if (result.type === 'vip_stocks' || result.type === 'stocks') {
        html += '<th style="padding:15px;text-align:left;">Ticker</th><th style="padding:15px;text-align:right;">Valuation</th><th style="padding:15px;text-align:right;">Profit</th><th style="padding:15px;text-align:center;">Growth</th><th style="padding:15px;text-align:right;">Status</th>';
    } else {
        html += '<th style="padding:15px;text-align:left;">Pair</th><th style="padding:15px;text-align:center;">Direction</th><th style="padding:15px;text-align:right;">Entry</th><th style="padding:15px;text-align:center;">Status</th>';
    }
    html += '</tr></thead><tbody>';

    result.data.forEach((row, i) => {
        html += '<tr style="background:rgba(255,255,255,0.03); border-radius: 12px;">';
        if (result.type === 'trades') {
            const sym = (row.symbol||'BTC/USDT').split('/')[0].toUpperCase();
            const status = (row.status||'').toLowerCase();
            const badgeClass = status === 'win' ? 'background:#ff3333;box-shadow: 0 0 15px rgba(16,185,129,0.3)' : (status === 'loss' ? 'background:#ef4444;box-shadow: 0 0 15px rgba(239,68,68,0.3)' : 'background:#f59e0b;color:#000');
            
            html += `<td style="padding:15px; border-radius: 12px 0 0 12px;">
                        <div style="display:flex;align-items:center;gap:12px">
                            <img src="https://img.logokit.com/crypto/${sym}?token=pk_frb53d906f2b73e352f31e" onerror="this.src='https://ui-avatars.com/api/?name=${sym}&background=000&color=fff'" style="width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,0.05);padding:3px;">
                            <span style="font-weight:800;letter-spacing:0.5px">${row.symbol||'BTC/USDT'}</span>
                        </div>
                     </td>
                     <td style="padding:15px;text-align:center;">
                        <span style="color:${(row.type === 'call' || row.type === 'buy') ? '#ff3333' : '#ef4444'}; font-weight:800; font-size:10px;">${(row.type||'CALL').toUpperCase()}</span>
                     </td>
                     <td style="padding:15px;text-align:right;font-weight:700">$${Number(row.amount||0).toLocaleString(undefined,{minimumFractionDigits:2})}</td>
                     <td style="padding:15px;text-align:right;font-weight:800;color:${status==='win'?'#ff3333':'#fff'}">$${Number(row.payout||0).toLocaleString(undefined,{minimumFractionDigits:2})}</td>
                     <td style="padding:15px;text-align:center;border-radius: 0 12px 12px 0;">
                        <span style="padding:6px 12px;border-radius:8px;font-size:10px;font-weight:900;${badgeClass}">${status.toUpperCase()}</span>
                     </td>`;
        } else if (result.type === 'deposits') {
            const status = (row.status||'').toString().toLowerCase();
            const isApproved = status === 'success' || status === '1' || status === 'approved';
            html += `<td style="padding:15px;border-radius: 12px 0 0 12px;">
                        <div style="display:flex;align-items:center;gap:10px">
                            <i class="ri-arrow-down-circle-line" style="color:#ff3333;font-size:1.2rem"></i>
                            <span style="font-weight:600">${row.type||'Crypto'}</span>
                        </div>
                     </td>
                     <td style="padding:15px;text-align:right;font-weight:800">$${Number(row.amount||0).toLocaleString()}</td>
                     <td style="padding:15px;text-align:center;">
                        <span style="padding:5px 10px;border-radius:6px;font-size:10px;font-weight:800;background:${isApproved?'rgba(16,185,129,0.1)':'rgba(245,158,11,0.1)'};color:${isApproved?'#ff3333':'#f59e0b'}">${isApproved?'APPROVED':'PENDING'}</span>
                     </td>
                     <td style="padding:15px;text-align:right;color:rgba(255,255,255,0.4);font-size:11px;border-radius: 0 12px 12px 0;">${row.created_at.split(' ')[0]}</td>`;
        } else if (result.type === 'withdrawals') {
            const status = (row.status||'').toString().toLowerCase();
            const isApproved = status === 'success' || status === '1' || status === 'approved';
            html += `<td style="padding:15px;border-radius: 12px 0 0 12px;">
                        <div style="display:flex;align-items:center;gap:10px">
                            <i class="ri-arrow-up-circle-line" style="color:#ef4444;font-size:1.2rem"></i>
                            <span style="font-size:11px;color:rgba(255,255,255,0.6)">${row.wallet?row.wallet.substring(0,12)+'...': 'MAIN_WALLET'}</span>
                        </div>
                     </td>
                     <td style="padding:15px;text-align:right;font-weight:800">$${Number(row.amount||0).toLocaleString()}</td>
                     <td style="padding:15px;text-align:center;">
                        <span style="padding:5px 10px;border-radius:6px;font-size:10px;font-weight:800;background:${isApproved?'rgba(16,185,129,0.1)':'rgba(239,68,68,0.1)'};color:${isApproved?'#ff3333':'#ef4444'}">${isApproved?'SUCCESS':'PROCESSING'}</span>
                     </td>
                     <td style="padding:15px;text-align:right;color:rgba(255,255,255,0.4);font-size:11px;border-radius: 0 12px 12px 0;">${row.created_at.split(' ')[0]}</td>`;
        } else if (result.type === 'mutual_funds') {
             html += `<td style="padding:15px;border-radius: 12px 0 0 12px;">
                        <div style="display:flex;align-items:center;gap:10px">
                            <i class="ri-pie-chart-2-line" style="color:#a855f7;font-size:1.2rem"></i>
                            <span style="font-weight:800">${row.fund||'PORTFOLIO'}</span>
                        </div>
                      </td>
                      <td style="padding:15px;text-align:right;font-weight:700">$${Number(row.invested||0).toLocaleString()}</td>
                      <td style="padding:15px;text-align:right;font-weight:800;color:#ff3333">+$${Number(row.profit||0).toLocaleString()}</td>
                      <td style="padding:15px;text-align:center;"><span style="color:#ff3333;font-weight:900;font-size:10px">${row.growth}</span></td>
                      <td style="padding:15px;text-align:right;color:rgba(255,255,255,0.4);font-size:11px;border-radius: 0 12px 12px 0;">${row.created_at.split(' ')[0]}</td>`;
        } else if (result.type === 'vip_stocks' || result.type === 'stocks') {
             const sym = (row.asset||'BTC').toUpperCase();
             html += `<td style="padding:15px;border-radius: 12px 0 0 12px;">
                        <div style="display:flex;align-items:center;gap:12px">
                            <img src="https://img.logokit.com/ticker/${sym}?token=pk_frb53d906f2b73e352f31e" onerror="this.src='https://ui-avatars.com/api/?name=${sym}&background=000&color=fff'" style="width:32px;height:32px;border-radius:8px;background:rgba(255,255,255,0.05);padding:3px;">
                            <span style="font-weight:800;letter-spacing:0.5px">${sym}</span>
                        </div>
                      </td>
                      <td style="padding:15px;text-align:right;font-weight:800">$${Number(row.payout || row.valuation || 0).toLocaleString()}</td>
                      <td style="padding:15px;text-align:right;font-weight:800;color:#ff3333">+$${Number(row.profit||0).toLocaleString()}</td>
                      <td style="padding:15px;text-align:center;"><span style="color:#ff3333;font-weight:900;font-size:10px">${row.growth}</span></td>
                      <td style="padding:15px;text-align:right;border-radius: 0 12px 12px 0;"><span style="padding:5px 10px;border-radius:6px;font-size:10px;font-weight:800;background:rgba(16,185,129,0.1);color:#ff3333">SETTLED</span></td>`;
        } else {
             html += `<td style="padding:15px;border-radius: 12px 0 0 12px;font-weight:800;color:var(--accent-primary)">${row.pair||'N/A'}</td>
                      <td style="padding:15px;text-align:center;font-weight:800;color:${row.action==='BUY'?'#ff3333':'#ef4444'}">${row.action||'N/A'}</td>
                      <td style="padding:15px;text-align:right;font-weight:600">${row.entry||'N/A'}</td>
                      <td style="padding:15px;text-align:center;border-radius: 0 12px 12px 0;"><span style="color:#ff3333;font-size:10px;font-weight:900">ACTIVE</span></td>`;
        }
        html += '</tr>';
    });

    if (result.data.length === 0) {
        html += '<tr><td colspan="5" style="padding:50px;text-align:center;color:rgba(255,255,255,0.2);font-style:italic">Ledger verification failed: No matching nodal records found</td></tr>';
    }
    html += '</tbody></table></div>';
    document.getElementById('captureBody').innerHTML = html;
}

document.getElementById('downloadPng').addEventListener('click', function() {
    const zone = document.getElementById('captureZone');
    this.innerHTML = '<i class="ri-loader-4-line ri-spin"></i> EXPORTING...';
    html2canvas(zone, { backgroundColor: '#060709', scale: 3, useCORS: true }).then(canvas => {
        const link = document.createElement('a');
        link.download = '{{ site()->name }}-HISTORY-AUDIT-' + Date.now() + '.png';
        link.href = canvas.toDataURL('image/png', 1.0);
        link.click();
        this.innerHTML = '<i data-lucide="download" style="width:14px;vertical-align:middle"></i> Download PNG';
        if(typeof lucide!=='undefined') lucide.createIcons();
    });
});
</script>
<script>if(typeof lucide!=='undefined') lucide.createIcons();</script>
@endsection
