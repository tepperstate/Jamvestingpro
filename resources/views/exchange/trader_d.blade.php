@extends('layouts.user.app')
@section('title', 'Copy Trade History')

@section("content")
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-5" data-aos="fade-up">
        <div class="col-xl-8">
            <h1 class="outfit font-weight-bold mb-2">Copy Trade History</h1>
            <p class="text-secondary lead">Review your mirrored trade performance and portfolio impact.</p>
        </div>
        <div class="col-xl-4 d-flex justify-content-xl-end align-items-center gap-3" data-aos="fade-left">
            <button onclick="exportTableToPDF()" class="btn btn-outline-primary px-4 py-2">
                <i class="ri-file-pdf-line me-1"></i> Export PDF
            </button>
            <button onclick="exportTableToCSV()" class="btn btn-outline-secondary px-4 py-2">
                <i class="ri-file-excel-line me-1"></i> Export CSV
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-5 mb-5">
        <div class="col-md-4" data-aos="zoom-in" data-aos-delay="100">
            <div class="glass-card p-4 text-center h-100">
                <div class="small text-secondary mb-2">Total Trades</div>
                <div class="h2 outfit font-weight-bold mb-0" id="total">0</div>
            </div>
        </div>
        <div class="col-md-4" data-aos="zoom-in" data-aos-delay="200">
            <div class="glass-card p-4 text-center h-100" style="border-left: 3px solid var(--accent-success);">
                <div class="small text-secondary mb-2">Trade Wins</div>
                <div class="h2 outfit font-weight-bold mb-0 text-success">$<span id="win">0.00</span></div>
            </div>
        </div>
        <div class="col-md-4" data-aos="zoom-in" data-aos-delay="300">
            <div class="glass-card p-4 text-center h-100" style="border-left: 3px solid #ef4444;">
                <div class="small text-secondary mb-2">Trade Losses</div>
                <div class="h2 outfit font-weight-bold mb-0 text-danger">$<span id="loss">0.00</span></div>
            </div>
        </div>
    </div>

    <!-- Trade History Table -->
    <div class="row" data-aos="fade-up" data-aos-delay="400">
        <div class="col-12">
            <div class="glass-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="outfit font-weight-bold mb-0">Mirror Execution Log</h5>
                </div>
                <div id="areatoPrint" class="table-responsive">
                    <table id="example" class="table table-dark table-hover align-middle mb-0">
                        <thead class="small text-secondary uppercase font-weight-bold">
                            <tr>
                                <th>S/N</th>
                                <th>ID</th>
                                <th>Trade Type</th>
                                <th>Trader Name</th>
                                <th>Asset</th>
                                <th>Amount</th>
                                <th>Profit/Loss</th>
                                <th>Total Profit/Loss</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody id="get"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Popup Overlay -->
<div id="overlay" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);z-index:9998;display:none;"></div>
<div class="popup glass-card" id="order_popup" style="position:fixed;z-index:9999;width:500px;top:40%;left:50%;transform:translate(-50%,-50%);display:none;overflow:hidden;border-radius:16px;"></div>

<style>
    .progress-container { background-color: #111; border-radius: 10px; overflow: hidden; position: relative; height: 7px; width:100px; margin-bottom: 10px; }
    .progress-bar { background: var(--accent-primary); height: 100%; width: 0; transition: width 1s linear; }
    .image-stack { position: relative; width: 16px; height: 16px; margin-right: 8px; }
    .image-text-container { display: flex; align-items: center; }
    .stacked-image { position: absolute; top: 0; left: 0; width: 16px; height: 16px; object-fit: cover; border-radius: 4px; }
    @media (max-width:700px) { .popup { width: 92% !important; } }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    function exportTableToPDF() { location.href="{{route('copy_pdf')}}"; }
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
</script>
@if(session('status'))
  <script>
    toastr.success("{{session('status')}}",'successful')
  </script>
@endif

  <script>
    $(document).ready(function() {
      var table = $('#example').DataTable({
        "processing": false,
        "serverSide": true,
        "ajax": {
            "url": "{{ route('datatable.data') }}", 
            "type": "GET",
        },
        "columns": [
          { 
                "data": "sn",
                "render": function(data, type, row) {
                    return `${data}
                      <input type='hidden' id="time" value='${row.time}'>
                      <input type='hidden' id="end_date" value='${row.expire_date}'>
                      <input type='hidden' id="start_date" value='${row.created_at}'>`;
                }
            },
            
            { "data": "trade_id" },
            { 
              "data": "trade_type",
              "render": function(data, type, row) {
                  if (data === 'call') {
                      return `<div style="color: green; text-align: center;">Predicted a Raise</div>`;
                  } else {
                      return `<div style="color: red; text-align: center;">Predicted a Fall</div>`;
                  }
              }
            },

            { 
              "data": "trader_name",
              "render": function(data, type, row) {
                console.log(row)
                return `<div style="display: flex; align-items: center;">
                        <img style="width:30px; margin-right:10px;border-radius:10px;" src="{{asset('storage/image/')}}/${row.trader_image.image}" alt="Trader Image">

                      <span>${data}</span>
                      <img style="width:30px; margin-left: 5px;" src="{{asset('asset/verified.png')}}" alt="Verified">
                    </div>`;
              }
            },
            { 
                "data": "asset",
                "render": function(data, type, row) {
                    return `<div class="image-text-container">
                                <div class="image-stack">
                                    ${data.image1 ? `<img class="stacked-image" src="${data.image1}" alt="">` : ''}
                                    <img class="stacked-image" style="margin-left:8px;margin-top:10px" src="${data.image2}" alt="">
                                </div>
                                ${row.symbol}
                            </div>`;
                }
            },
            {
            "data": "amount",
            "render": function(data, type, row) {
                // Accessing the `amount` data directly
                let numberValue = parseInt(data.replace(/[^0-9.-]+/g, ''));

                    const amount = parseFloat(numberValue).toFixed(2); // Example: formatting the amount to 2 decimal places
                    return `USD ${amount}`; // Adjust display format as desired
                }
            },
            

            { 
                "data": "Total_Profit_Loss",
                "render": function(data, type, row) {
                    let formattedValue;
                    if (row.status === 'win') {
                        formattedValue = `USD +${data.toFixed(2)}`;
                        return `<div style="text-align:center;color:green">${formattedValue}</div>`;
                    } else if (row.status === 'loss') {
                        formattedValue = `USD -${data}`;
                        return `<div style="text-align:center;color:red">${formattedValue}</div>`;
                    } else {
                        // If status is neither 'win' nor 'loss'
                        return `<div style="text-align:center;color:white">USD 0.00</div>`;
                    }
                }
            },
            { 
                "data": "profit_loss",
                "render": function(data, type, row) {
                    if (row.status === 'win') {
                        return `<div style="color: green;text-align:center">USD + ${data.toFixed(2)}</div>`;
                    } else if (row.status === 'loss') {
                        return `<div style="color: red;text-align:center">USD - ${data.toFixed(2)}</div>`;
                    } else {
                        return `<div style='text-align:center'>USD 0</div>`;  // Default case when it's neither win nor loss
                    }
                }
            },

            { 
                "data": "status",
                "render": function(data, type, row) {
                    if (data === 'win') {
                        return `<div style="color: green;text-align:center"><i class='fa fa-angle-up' style='font-size:13px;'></i> win</div>`;
                    } else if (data === 'loss') {
                        return `<div style="color: red;text-align:center"> <i class='fa fa-angle-down' style='font-size:13px;'></i> loss</div>`;
                    } else {
                        return `<div style="display:flex;flex-direction: column; justify-content:center; align-items:center;">
                            <div class='progress-container'>
                                <div class='progress-bar' id='progress-bar'></div>
                            </div>
                            <div id='show_timer' style='color:white;'></div>
                        </div>`;
                    }
                }
            },
            {
                "data": "created_at",
                "render": function(data, type, row) {
                    // Parse the timestamp into a Date object
                    const date = new Date(data);
            
                    // Format the date as a human-readable string
                    const options = { 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric'

                    };
                    const formattedDate = date.toLocaleDateString("en-US", options); // You can adjust locale as needed
            
                    return `<div style="text-align: center;">${formattedDate}</div>`;
                }
            }
        ],
        "drawCallback": function(settings) {
          var data = table.rows().data(); // Get the data for all rows

          data.each(function(row, index) {
            updateTimers()
            if(row.modal =='open') {
              showModal(row);
            }
            if (row.status === "pending") {
                    onTimerEnd();
                 }
            if(row.modal == 'open') {
              document.getElementById('close_pop').addEventListener('click', async () => {
                await closeModal(row.id);
              });
            }
          });
        }
      });
      setInterval(function() {
        table.ajax.reload(null, false);
      }, 2000);//

    });
    get_trade_dats()

    setInterval(function() {
      get_trade_dats()
    }, 5000);
    

    async function get_trade_dats() {
      const options = {
        method: 'get',
        headers: {
          'Content-Type': 'application/json',
        },
      }

      try {
        const response = await fetch("{{route('copy-trading.traders-details')}}", options)

        const data = await response.json();

        $("#total").text(data.data.count)
        $("#win").text(data.data.win.toFixed(2))
        $("#loss").text(Math.abs(data.data.loss).toFixed(2))

      } catch (error) {
          console.log(error)
      }
    }
    async function closeModal(id) {
      const options = {
        method: 'get',
        headers: {
          'Content-Type': 'application/json',
        },
      }

      try {
        const response = await fetch("/dashboard/close/modal/copy/"+id, options)

        const data = await response.json();

        const modalElement = document.getElementById('order_popup');
        modalElement.style.display = 'none';

        const overlayElement = document.getElementById('overlay');
        overlayElement.style.display = 'none';

        document.getElementById('close_pop').textContent ='x'


      } catch (error) {
          console.log(error)
      }
    }
   
    async function showModal(trade) {
      // Extract numeric values from "USD 23" format
      const profitLoss = parseFloat(trade.p_l.toString().replace(/[^0-9.-]+/g, "")) || 0;
      const amount = parseFloat(trade.amount.toString().replace(/[^0-9.-]+/g, "")) || 0;

      const outcome = trade.status === 'win' ? 'Trade Won' : 'Trade Lost';

      // Calculate profit or loss without capital
      const profitOrLoss = profitLoss - amount;
      const formattedProfitOrLoss = (trade.status === 'win' ? '+$' : '-$') + Math.abs(profitOrLoss).toFixed(2);

      let data = `
          <div>
              <div style="background:rgb(39, 48, 66);display:flex;justify-content:space-between;align-items:center;padding:10px;font-size:18px;">
                  <span style="color:white">Trade Result</span>
                  <span id="close_pop" style="color:white;cursor:pointer;font-size:25px;">&times;</span>
              </div>
              <div style="background:#20363e;display:flex;justify-content:space-between;padding:10px;font-size:16px;">
                  <span style="cursor:pointer;color:white">${trade.exchanges.name}</span>
                  <span id="asset_result" style="color:white">${trade.symbol}</span>
              </div>
              <img src="/assets/img/app.jpg" alt="App image" style="width:100%;max-height:200px;object-fit:cover;">
              <div style="background:rgb(39, 48, 66);display:flex;justify-content:space-between;padding:15px;font-size:20px;font-weight:bold;">
                  <span id="trade_outcome" style="color:white;">${outcome}</span>
                  <span id="trade_amount" style="color:${trade.status === 'win' ? 'white' : 'white'};font-weight:bold;">
                      ${formattedProfitOrLoss}
                  </span>
              </div>
          </div>
      `;

      // Append modal content to the modal element
      const modalContentElement = document.getElementById('order_popup');
      modalContentElement.innerHTML = data;

      // Show the modal and overlay
      document.getElementById('order_popup').style.display = 'block';
      document.getElementById('overlay').style.display = 'block';

      // Close button functionality
      document.getElementById('close_pop').onclick = function() {
          modalContentElement.style.display = 'none';
          document.getElementById('overlay').style.display = 'none';
      };
  }

    function printDiv() {
      var divToPrint = document.getElementById('areatoPrint');
      newWin = window.open("");
      newWin.document.write(divToPrint.outerHTML);
      newWin.print();
      newWin.close();
    }

     async function updateTimers() {
        const timerElements = document.querySelectorAll('#get tr');

        timerElements.forEach(timerElement => {

        // Fetch date strings from the DOM
        let startDateString = timerElement.querySelector("#start_date").value;
        const expireDateString = timerElement.querySelector("#end_date").value;

        // Remove microseconds if present
        if (startDateString.includes(".")) {
            startDateString = startDateString.split(".")[0];
        }

        // Parse UTC dates with 'Z' to ensure UTC interpretation
        const utcExpireDate = Date.parse(expireDateString + 'Z');
        const utcStartDate = Date.parse(startDateString + 'Z');
        const now = Date.now(); // Current UTC time in milliseconds

        const show = timerElement.querySelector("#show_timer");
        const progressBar = timerElement.querySelector('#progress-bar');

        if (isNaN(utcStartDate)) {
          return; // Exit early if start date is invalid
        }

        const timeDifference = utcExpireDate - now;
        const totalDuration = utcExpireDate - utcStartDate;
        const elapsedDuration = now - utcStartDate;

        if (totalDuration > 0) {
            let progressPercentage = ((elapsedDuration / totalDuration) * 100);
            
            if (timeDifference > 0) {
                const hoursRemaining = Math.floor(timeDifference / (1000 * 60 * 60));
                const minutesRemaining = Math.floor((timeDifference % (1000 * 60 * 60)) / (1000 * 60));
                const secondsRemaining = Math.floor((timeDifference % (1000 * 60)) / 1000);

                progressPercentage = Math.min(progressPercentage, 100);
                progressBar.style.width = progressPercentage.toFixed(0) + '%';

                let timeString = "";
                if (hoursRemaining > 0) {
                    timeString += `${hoursRemaining}h : `;
                }
                timeString += `${minutesRemaining}m : ${secondsRemaining}s`;

                show.textContent = timeString;
            } else {
           
            }
        } else {
            
        }
       });
     } 


     async function onTimerEnd() {
        try {
           await new Promise(resolve => setTimeout(resolve, 30000));

            const response = await fetch("{{route('execute_result_after_time_for_copy_trade')}}", {
                method: 'get',
                headers: {
                    'Content-Type': 'application/json',
                },
            });
            const result = await response.json();
        } catch (error) {
            console.error("catch Error in executing trades:", error);
        }
      }

  </script>
@endsection
