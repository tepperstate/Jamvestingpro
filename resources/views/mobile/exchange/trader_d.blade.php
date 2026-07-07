@extends('layouts.user.app')
@section('title', 'Copy Trade History')
@section('content')

<style>
.mobile-copy-history-container {
    padding: 15px;
    background: #0b0e14;
    min-height: 100vh;
    padding-bottom: 90px;
}
.stat-card-mobile {
    background: rgba(16, 18, 27, 0.5);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 16px;
    padding: 15px;
    margin-bottom: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}
.stat-value {
    font-size: 1.5rem;
    font-weight: bold;
    font-family: 'Outfit', sans-serif;
}
.border-left-success { border-left: 3px solid #ff3333 !important; }
.border-left-danger { border-left: 3px solid #ef4444 !important; }
.border-left-primary { border-left: 3px solid #3b82f6 !important; }

/* DataTables mobile override */
.dataTables_wrapper .dataTables_length, 
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    color: #94a3b8 !important;
    font-size: 0.8rem;
    margin-bottom: 10px;
}
.dataTables_wrapper .dataTables_filter input {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    color: #fff;
    border-radius: 8px;
    padding: 5px 10px;
}
table.dataTable.table-dark {
    background: rgba(16, 18, 27, 0.5) !important;
}
.table-responsive {
    background: rgba(16, 18, 27, 0.5);
    border-radius: 16px;
    border: 1px solid rgba(255,255,255,0.05);
    padding: 10px;
}

/* Progress bar for ongoing trades */
.progress-container { 
    background-color: rgba(255,255,255,0.1); 
    border-radius: 10px; 
    overflow: hidden; 
    height: 6px; 
    width: 80px; 
    margin: 5px auto; 
}
.progress-bar { 
    background: #3b82f6; 
    height: 100%; 
    width: 0; 
    transition: width 1s linear; 
}

/* Modal styles override */
.popup {
    background: #10121b !important;
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 16px !important;
    width: 90% !important;
    max-width: 400px;
}
</style>

<div class="mobile-copy-history-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="text-white font-weight-bold mb-0" style="font-family: 'Outfit', sans-serif;">Mirror History</h4>
            <div class="small text-secondary">Your copied trades</div>
        </div>
        <div class="d-flex gap-2">
            <button onclick="exportTableToCSV()" class="btn btn-sm" style="background: rgba(255,255,255,0.1); color: #fff; border-radius: 8px;">
                <i class="ri-download-line"></i> CSV
            </button>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="row g-2 mb-4">
        <div class="col-12">
            <div class="stat-card-mobile border-left-primary text-center py-2">
                <div class="small text-secondary mb-1" style="font-size: 0.75rem;">Total Trades</div>
                <div class="stat-value text-white" id="total">0</div>
            </div>
        </div>
        <div class="col-6">
            <div class="stat-card-mobile border-left-success text-center py-2">
                <div class="small text-secondary mb-1" style="font-size: 0.75rem;">Trade Wins</div>
                <div class="stat-value text-success">$<span id="win">0.00</span></div>
            </div>
        </div>
        <div class="col-6">
            <div class="stat-card-mobile border-left-danger text-center py-2">
                <div class="small text-secondary mb-1" style="font-size: 0.75rem;">Trade Losses</div>
                <div class="stat-value text-danger">$<span id="loss">0.00</span></div>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table id="example" class="table table-dark table-hover mb-0" style="font-size: 0.8rem; white-space: nowrap;">
            <thead class="text-secondary font-weight-bold">
                <tr>
                    <th>S/N</th>
                    <th>Trader</th>
                    <th>Asset</th>
                    <th>Amount</th>
                    <th>P/L</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="get"></tbody>
        </table>
    </div>
</div>

<!-- Popup Overlay -->
<div id="overlay" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.8);z-index:9998;display:none;backdrop-filter: blur(5px);"></div>
<div class="popup glass-card" id="order_popup" style="position:fixed;z-index:9999;top:50%;left:50%;transform:translate(-50%,-50%);display:none;overflow:hidden;"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables required scripts would normally go here if not in layout -->

<script>
    function exportTableToCSV() {
        const rows = document.querySelectorAll('#example tr');
        let csvContent = "data:text/csv;charset=utf-8,";
        rows.forEach(row => {
            const cells = row.querySelectorAll('td, th');
            const rowContent = Array.from(cells).map(cell => cell.innerText.replace(/,/g, "")).join(",");
            csvContent += rowContent + "\n";
        });
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "copy_trade_history.csv");
        document.body.appendChild(link);
        link.click();
    }

    $(document).ready(function() {
      // Initialize DataTable but strip some columns for mobile
      var table = $('#example').DataTable({
        "processing": false,
        "serverSide": true,
        "ajax": {
            "url": "{{ route('datatable.data') }}", 
            "type": "GET",
        },
        "createdRow": function(row, data, dataIndex) {
            $(row).find('td:eq(0)').attr('data-label', 'S/N');
            $(row).find('td:eq(1)').attr('data-label', 'Trader');
            $(row).find('td:eq(2)').attr('data-label', 'Asset');
            $(row).find('td:eq(3)').attr('data-label', 'Amount');
            $(row).find('td:eq(4)').attr('data-label', 'P/L');
            $(row).find('td:eq(5)').attr('data-label', 'Status');
        },
        "scrollX": true,
        "columns": [
          { 
                "data": "sn",
                "render": function(data, type, row) {
                    return `${data}
                      <input type='hidden' id="end_date" value='${row.expire_date}'>
                      <input type='hidden' id="start_date" value='${row.created_at}'>`;
                }
            },
            { 
              "data": "trader_name",
              "render": function(data, type, row) {
                return `<div style="display: flex; align-items: center;">
                        <img style="width:24px; height:24px; margin-right:5px; border-radius:50%;" src="{{asset('storage/image/')}}/${row.trader_image.image}" alt="">
                      <span style="font-weight:bold;">${data}</span>
                    </div>`;
              }
            },
            { 
                "data": "asset",
                "render": function(data, type, row) {
                    return `<strong>${row.symbol}</strong>`;
                }
            },
            {
            "data": "amount",
            "render": function(data, type, row) {
                let numberValue = parseInt(data.replace(/[^0-9.-]+/g, ''));
                    return `$${parseFloat(numberValue).toFixed(2)}`;
                }
            },
            { 
                "data": "Total_Profit_Loss",
                "render": function(data, type, row) {
                    if (row.status === 'win') {
                        return `<div class="text-success fw-bold">+$${data.toFixed(2)}</div>`;
                    } else if (row.status === 'loss') {
                        return `<div class="text-danger fw-bold">-$${data}</div>`;
                    } else {
                        return `<div class="text-secondary">$0.00</div>`;
                    }
                }
            },
            { 
                "data": "status",
                "render": function(data, type, row) {
                    if (data === 'win') {
                        return `<div class="text-success text-center"><i class="ri-checkbox-circle-fill"></i></div>`;
                    } else if (data === 'loss') {
                        return `<div class="text-danger text-center"><i class="ri-close-circle-fill"></i></div>`;
                    } else {
                        return `<div class="text-center">
                            <div class='progress-container'><div class='progress-bar' id='progress-bar'></div></div>
                            <div id='show_timer' style='font-size:0.6rem;color:#94a3b8;'></div>
                        </div>`;
                    }
                }
            }
        ],
        "drawCallback": function(settings) {
          var data = table.rows().data();
          data.each(function(row, index) {
            updateTimers();
            if(row.modal == 'open') showModal(row);
            if (row.status === "pending") onTimerEnd();
            if(row.modal == 'open') {
              document.getElementById('close_pop').addEventListener('click', async () => await closeModal(row.id));
            }
          });
        }
      });
      
      setInterval(() => table.ajax.reload(null, false), 5000);
      get_trade_dats();
      setInterval(get_trade_dats, 5000);
    });

    async function get_trade_dats() {
      try {
        const res = await fetch("{{route('copy-trading.traders-details')}}");
        const data = await res.json();
        $("#total").text(data.data.count);
        $("#win").text(data.data.win.toFixed(2));
        $("#loss").text(Math.abs(data.data.loss).toFixed(2));
      } catch (err) {}
    }

    async function closeModal(id) {
      try {
        await fetch("/dashboard/close/modal/copy/"+id);
        document.getElementById('order_popup').style.display = 'none';
        document.getElementById('overlay').style.display = 'none';
      } catch (err) {}
    }
   
    async function showModal(trade) {
      const profitLoss = parseFloat(trade.p_l.toString().replace(/[^0-9.-]+/g, "")) || 0;
      const amount = parseFloat(trade.amount.toString().replace(/[^0-9.-]+/g, "")) || 0;
      const outcome = trade.status === 'win' ? 'Trade Won' : 'Trade Lost';
      const profitOrLoss = profitLoss - amount;
      const formattedProfitOrLoss = (trade.status === 'win' ? '+$' : '-$') + Math.abs(profitOrLoss).toFixed(2);

      let data = `
          <div>
              <div class="d-flex justify-content-between align-items-center p-3" style="background: rgba(255,255,255,0.05); border-bottom: 1px solid rgba(255,255,255,0.1);">
                  <span class="text-white font-weight-bold">Trade Result</span>
                  <span id="close_pop" style="color: #94a3b8; font-size: 1.5rem; cursor: pointer;">&times;</span>
              </div>
              <div class="d-flex justify-content-between p-3 small text-secondary">
                  <span>${trade.exchanges.name}</span>
                  <span class="text-white fw-bold">${trade.symbol}</span>
              </div>
              <img src="/assets/img/app.jpg" style="width:100%; height:120px; object-fit:cover;">
              <div class="d-flex justify-content-between p-3 fw-bold" style="background: rgba(0,0,0,0.3);">
                  <span class="${trade.status === 'win' ? 'text-success' : 'text-danger'}">${outcome}</span>
                  <span class="text-white">${formattedProfitOrLoss}</span>
              </div>
          </div>
      `;
      document.getElementById('order_popup').innerHTML = data;
      document.getElementById('order_popup').style.display = 'block';
      document.getElementById('overlay').style.display = 'block';
      document.getElementById('close_pop').onclick = function() {
          document.getElementById('order_popup').style.display = 'none';
          document.getElementById('overlay').style.display = 'none';
      };
    }

    async function updateTimers() {
        document.querySelectorAll('#get tr').forEach(timerElement => {
            let startDateString = timerElement.querySelector("#start_date")?.value;
            const expireDateString = timerElement.querySelector("#end_date")?.value;
            if(!startDateString || !expireDateString) return;

            if (startDateString.includes(".")) startDateString = startDateString.split(".")[0];
            const utcExpireDate = Date.parse(expireDateString + 'Z');
            const utcStartDate = Date.parse(startDateString + 'Z');
            const now = Date.now();

            const show = timerElement.querySelector("#show_timer");
            const progressBar = timerElement.querySelector('#progress-bar');
            if (isNaN(utcStartDate) || !show || !progressBar) return;

            const timeDiff = utcExpireDate - now;
            const totalDur = utcExpireDate - utcStartDate;
            const elapsed = now - utcStartDate;

            if (totalDur > 0 && timeDiff > 0) {
                let perc = Math.min(((elapsed / totalDur) * 100), 100);
                progressBar.style.width = perc.toFixed(0) + '%';
                
                const h = Math.floor(timeDiff / 3600000);
                const m = Math.floor((timeDiff % 3600000) / 60000);
                const s = Math.floor((timeDiff % 60000) / 1000);
                show.textContent = (h > 0 ? `${h}h ` : '') + `${m}m ${s}s`;
            }
        });
    }

    async function onTimerEnd() {
        try {
            await new Promise(res => setTimeout(res, 30000));
            await fetch("{{route('execute_result_after_time_for_copy_trade')}}");
        } catch (e) {}
    }
</script>
@endsection
