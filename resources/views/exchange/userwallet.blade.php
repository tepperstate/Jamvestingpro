@extends('layouts.user.app')

@section('title', 'Wallet')

@section('content')
<style>
    /* Custom Toggle Switch for Wallet Page */
    .switch {
      position: relative;
      display: inline-block;
      width: 36px;
      height: 20px;
    }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider {
      position: absolute;
      cursor: pointer;
      top: 0; left: 0; right: 0; bottom: 0;
      background-color: var(--admin-border, #333);
      transition: .4s;
      border-radius: 20px;
    }
    .slider:before {
      position: absolute;
      content: "";
      height: 14px;
      width: 14px;
      left: 3px;
      bottom: 3px;
      background-color: white;
      transition: .4s;
      border-radius: 50%;
    }
    input:checked + .slider { background-color: var(--accent-primary); }
    input:checked + .slider:before { transform: translateX(16px); }
    
    .obscured-balance {
        filter: blur(5px);
        user-select: none;
    }
    
    /* Gap polyfills for Bootstrap 4 */
    .d-flex { display: flex !important; }
    .gap-1 { gap: 0.25rem !important; }
    .gap-2 { gap: 0.5rem !important; }
    .gap-3 { gap: 1rem !important; }
    .gap-4 { gap: 1.5rem !important; }
    
    /* Table */
    .crypto-table { width: 100%; color: var(--admin-text-main, #fff); border-collapse: collapse; }
    .crypto-table th { color: var(--admin-text-muted, #9ca3af); font-weight: 500; font-size: 0.85rem; padding-bottom: 16px; border-bottom: 1px solid var(--admin-border, #333); text-align: left; }
    .crypto-table td { padding: 16px 0; border-bottom: 1px solid var(--admin-border, #333); vertical-align: middle; }
    .crypto-table tr:last-child td { border-bottom: none; }
    
    /* Tabs */
    .custom-tabs { border-bottom: 1px solid var(--admin-border, #333); display: flex; gap: 24px; margin-bottom: 24px; }
    .custom-tab { color: var(--admin-text-muted, #9ca3af); font-weight: 500; padding-bottom: 12px; cursor: pointer; border-bottom: 2px solid transparent; transition: all 0.2s; }
    .custom-tab.active { color: var(--accent-primary, #3b82f6); border-bottom: 2px solid var(--accent-primary, #3b82f6); }
    .custom-tab:hover:not(.active) { color: var(--admin-text-main, #fff); }
    
    /* Stat Boxes & Balances */
    .wallet-balance-amount { font-size: 2.2rem; font-weight: 700; color: #fff; margin-right: 12px; }
    .hide-price-btn { background: rgba(255,255,255,0.05); border: 1px solid var(--admin-border, #333); border-radius: 4px; color: var(--admin-text-muted, #9ca3af); font-size: 0.75rem; padding: 4px 8px; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; }
    .stat-box { display: flex; align-items: center; gap: 16px; }
    .stat-label { color: var(--admin-text-muted, #9ca3af); font-size: 0.85rem; display: flex; align-items: center; gap: 8px; }
    .stat-amount { color: #fff; font-weight: 600; font-size: 1rem; }
    
    /* Action Links */
    .action-link { text-decoration: none; font-size: 0.85rem; margin-right: 16px; display: inline-flex; align-items: center; gap: 4px; color: #f3c736 !important; }
    .action-link:hover { text-decoration: none; opacity: 0.8; }
    
    /* Glassmorphism */
    .glass-card-premium {
        background: rgba(20, 20, 25, 0.45);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    }
    
    /* Buttons */
    .btn-premium {
        background: #f3c736;
        color: #000;
        font-weight: 600;
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        transition: 0.2s;
    }
    .btn-premium:hover { background: #dcb32e; color: #000; }
    .btn-outline-primary {
        border-color: #f3c736; color: #f3c736;
    }
    .btn-outline-primary:hover {
        background: #f3c736; color: #000;
    }
    
    .search-box { position: relative; }
    .search-box i { position: absolute; left: 12px; top: 10px; color: #9ca3af; }
    .search-box input { background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); color: #fff; border-radius: 8px; padding-left: 36px; }
    .search-box input:focus { background: rgba(0,0,0,0.3); border-color: #f3c736; color: #fff; box-shadow: none; }
    
    /* Utility Classes */
    .gap-1 { gap: 0.25rem !important; }
    .gap-2 { gap: 0.5rem !important; }
    .gap-3 { gap: 1rem !important; }
    .gap-4 { gap: 1.5rem !important; }
</style>

<div class="pt-4">
    <div class="container-fluid px-4">
        
        <div class="row">
            <!-- Left Column: Wallet Hero -->
            <div class="col-xl-8 col-lg-7 mb-4">
                <div class="glass-card-premium p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h4 class="mb-1 text-white font-weight-bold">Wallet</h4>
                            <div class="text-muted">Update {{ now()->format('d/m/Y \a\t h:i A') }}</div>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm d-flex align-items-center gap-2">
                                <i data-lucide="edit-2" style="width:14px;height:14px;"></i> Edit
                            </button>
                            <button class="btn btn-outline-primary btn-sm d-flex align-items-center gap-2">
                                <i data-lucide="plus" style="width:14px;height:14px;"></i> Add New Wallet
                            </button>
                        </div>
                    </div>
                    
                    <div class="row align-items-end mt-4">
                        <div class="col-md-6 mb-4 mb-md-0">
                            <div class="text-muted d-flex align-items-center gap-2 mb-2">
                                <i data-lucide="wallet" style="width:16px;height:16px;"></i> Wallet Balance
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="wallet-balance-amount balance-val">${{ number_format($totalCryptoUsd, 2) }}</span>
                                <button class="hide-price-btn" id="toggleBalanceBtn">
                                    <span>Hide Price</span> <i data-lucide="eye-off" style="width:12px;height:12px;" id="toggleBalanceIcon"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="d-flex flex-column gap-3">
                                <div class="d-flex justify-content-between align-items-center border-bottom pb-2" style="border-color: var(--border-color)!important;">
                                    <div class="stat-label">
                                        <i data-lucide="arrow-down-to-line" style="width:16px;height:16px;color:#3b82f6;"></i> Total Deposited
                                    </div>
                                    <div class="stat-amount">
                                        ${{ number_format($total_deposited ?? 0, 2) }}
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="stat-label">
                                        <i data-lucide="arrow-up-from-line" style="width:16px;height:16px;color:#3b82f6;"></i> Total Withdrawals
                                    </div>
                                    <div class="stat-amount">
                                        ${{ number_format($total_withdrawn ?? 0, 2) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column: Quick Deposit/Payment -->
            <div class="col-xl-4 col-lg-5 mb-4">
                <div class="glass-card-premium p-4">
                    <h5 class="text-white mb-4 font-weight-bold">Select Currency and Payment</h5>
                    
                    <div class="mb-3">
                        <label class="text-muted mb-2 d-block">Select Currency</label>
                        <select class="form-control">
                            <option value="USD">$ USD</option>
                            <option value="EUR">€ EUR</option>
                            <option value="GBP">£ GBP</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="text-muted mb-2 d-block">Amount</label>
                        <input type="text" class="form-control" placeholder="$0.00" value="">
                    </div>
                    
                    <div class="mb-4">
                        <label class="text-muted mb-3 d-block">Choose Payment method:</label>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="d-flex align-items-center gap-2 mb-0" style="cursor:pointer;">
                                <input type="radio" name="payment_method" value="crypto" checked style="accent-color: var(--accent-primary);">
                                <span>Crypto Deposit</span>
                            </label>
                            <div class="d-flex gap-1">
                                <i data-lucide="bitcoin" style="color:#f59e0b;width:20px;height:20px;"></i>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <label class="d-flex align-items-center gap-2 mb-0 text-muted" style="cursor:pointer;">
                                <input type="radio" name="payment_method" value="card" style="accent-color: var(--accent-primary);">
                                <span>Credit & Debit Card</span>
                            </label>
                            <div class="d-flex gap-1 text-muted">
                                <i data-lucide="credit-card" style="width:20px;height:20px;"></i>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" class="btn-premium w-100 mt-2" id="addMoneyBtn">
                        <i class="ri-wallet-3-line mr-2"></i> Add money
                    </button>
                </div>
            </div>
        </div>

        <!-- Bottom Row: Transaction History / Coin Wallet -->
        <div class="row">
            <div class="col-12">
                <div class="glass-card-premium p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
                        <h5 class="text-white font-weight-bold mb-3 mb-md-0">Transaction History</h5>
                        
                        <div class="search-box">
                            <i data-lucide="search" style="width:16px;height:16px;"></i>
                            <input type="text" id="coinSearch" class="form-control pl-5" placeholder="Search by date or name">
                        </div>
                    </div>
                    
                    <div class="custom-tabs mt-4 d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-4">
                            <div class="custom-tab" id="tab-history" onclick="switchTab('history')">Wallet History</div>
                            <div class="custom-tab active" id="tab-coins" onclick="switchTab('coins')">Coin Wallet</div>
                        </div>
                        <div class="d-flex align-items-center gap-4">
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-muted">Hide empty balances</span>
                                <label class="switch mb-0">
                                  <input type="checkbox" id="hideEmptyBalancesToggle">
                                  <span class="slider"></span>
                                </label>
                            </div>
                            <div class="d-flex align-items-center gap-2 text-muted" style="cursor:pointer;">
                                <i data-lucide="calendar" style="width:14px;height:14px;"></i> Month <i data-lucide="chevron-down" style="width:14px;height:14px;"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Coin Wallet Tab Content -->
                    <div id="content-coins" style="display:block;">
                        <div class="table-responsive">
                            <table class="crypto-table">
                                <thead>
                                    <tr>
                                        <th>Pair <i data-lucide="chevron-up" style="width:12px;height:12px;opacity:0.5;"></i></th>
                                        <th>Avbl. Balance <i data-lucide="chevron-up" style="width:12px;height:12px;opacity:0.5;"></i></th>
                                        <th>Locked <i data-lucide="chevron-down" style="width:12px;height:12px;opacity:0.5;"></i></th>
                                        <th>Amount <i data-lucide="chevron-up" style="width:12px;height:12px;opacity:0.5;"></i></th>
                                        <th>Action <i data-lucide="chevron-down" style="width:12px;height:12px;opacity:0.5;"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($coins as $coin)
                                    @php
                                        $wallet = $userWallets->where('coin_symbol', $coin->symbol)->first();
                                        $balance = $wallet ? $wallet->balance : 0;
                                        $price = $prices[$coin->symbol] ?? 0;
                                        $usdValue = $balance * $price;
                                        $isEmpty = $balance <= 0;
                                    @endphp
                                    <tr class="coin-row" data-empty="{{ $isEmpty ? 'true' : 'false' }}" data-name="{{ strtolower($coin->name) }} {{ strtolower($coin->symbol) }}">
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <x-asset-logo :symbol="$coin->symbol" size="sm" />
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="text-white font-weight-bold">{{ $coin->name }}</span>
                                                    <span class="text-muted" style="background: rgba(255,255,255,0.05); padding: 2px 6px; border-radius: 4px; font-size: 0.7rem; color: var(--accent-primary);">{{ $coin->symbol }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="balance-val">{{ number_format($balance, 6) }}</span></td>
                                        <td>0</td>
                                        <td><span class="balance-val">${{ number_format($usdValue, 2) }}</span></td>
                                        <td>
                                            <a href="#" class="action-link text-primary" data-toggle="modal" data-target="#depositModal">Deposit <i data-lucide="external-link" style="width:12px;height:12px;"></i></a>
                                            <a href="#" class="action-link text-primary" data-toggle="modal" data-target="#withdrawModal" onclick="$('#withdrawCoinSelect').val('{{$coin->symbol}}')">Withdraw <i data-lucide="external-link" style="width:12px;height:12px;"></i></a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <span class="text-muted" id="assetsCount">{{ $coins->count() }} assets</span>
                            <div class="d-flex gap-2" id="pagination-container">
                                <!-- JS Pagination here -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Wallet History Tab Content -->
                    <div id="content-history" style="display:none;">
                        <div class="table-responsive mt-3">
                            <table class="crypto-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Asset</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // Merge deposits and withdrawals for a unified history view
                                        $unified_history = collect();
                                        foreach($recent_deposits as $d) {
                                            $unified_history->push((object)[
                                                'date' => $d->created_at,
                                                'type' => 'Deposit',
                                                'asset' => $d->pay_currency ?? 'USD',
                                                'amount' => '+' . number_format($d->amount, 6),
                                                'amount_class' => 'text-success',
                                                'status' => $d->status
                                            ]);
                                        }
                                        foreach($recent_withdrawals as $w) {
                                            $unified_history->push((object)[
                                                'date' => $w->created_at,
                                                'type' => 'Withdrawal',
                                                'asset' => $w->type ?? 'USD',
                                                'amount' => '-' . number_format($w->amount, 6),
                                                'amount_class' => 'text-danger',
                                                'status' => $w->status
                                            ]);
                                        }
                                        $unified_history = $unified_history->sortByDesc('date')->take(10);
                                    @endphp
                                    
                                    @forelse($unified_history as $tx)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($tx->date)->format('d M, Y H:i') }}</td>
                                        <td>{{ $tx->type }}</td>
                                        <td>{{ $tx->asset }}</td>
                                        <td class="{{ $tx->amount_class }}">{{ $tx->amount }}</td>
                                        <td>
                                            @if(in_array(strtolower($tx->status), ['completed', 'approved']))
                                                <span style="color: #ff3333; font-size: 0.85rem;">{{ ucfirst($tx->status) }}</span>
                                            @elseif(strtolower($tx->status) == 'pending')
                                                <span style="color: #f59e0b; font-size: 0.85rem;">{{ ucfirst($tx->status) }}</span>
                                            @else
                                                <span style="color: #ef4444; font-size: 0.85rem;">{{ ucfirst($tx->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No recent history found.</td>
                                    </tr>
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

<!-- Modals -->
<!-- Deposit Modal -->
<div class="modal fade" id="depositModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content glass-card-premium">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title font-weight-bold text-white">Deposit Crypto</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-4">Select an asset network to reveal your deposit address.</p>
                
                <ul class="nav nav-pills mb-3 gap-2" id="depositTabs" role="tablist" style="overflow-x: auto; flex-wrap: nowrap; padding-bottom: 10px;">
                    @foreach($admin_wallets as $index => $admin_wallet)
                    <li class="nav-item">
                        <a class="nav-link {{ $index == 0 ? 'active' : '' }}" data-toggle="pill" href="#dep-{{ $admin_wallet->symbol }}" role="tab" style="white-space: nowrap; background: transparent; border: 1px solid var(--border-color); color: var(--text-muted); border-radius: 6px; padding: 6px 12px; margin-right: 8px;">
                            {{ $admin_wallet->symbol }}
                        </a>
                    </li>
                    @endforeach
                </ul>
                <style>
                    .nav-pills .nav-link.active {
                        background: var(--accent-blue) !important;
                        color: #fff !important;
                        border-color: var(--accent-blue) !important;
                    }
                </style>
                
                <div class="tab-content">
                    @foreach($admin_wallets as $index => $admin_wallet)
                    <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}" id="dep-{{ $admin_wallet->symbol }}" role="tabpanel">
                        <div class="text-center mb-4 mt-3">
                            <div class="bg-white p-2 d-inline-block rounded mb-3" style="box-shadow: 0 4px 12px rgba(0,0,0,0.3);">
                                {!! $admin_wallet->qr_code !!}
                            </div>
                            <h6 class="text-white">{{ $admin_wallet->name }} Network</h6>
                        </div>
                        
                        <div class="mb-3">
                            <label class="text-muted mb-2">Deposit Address</label>
                            <div class="input-group">
                                <input type="text" class="form-control text-white border-right-0" value="{{ $admin_wallet->address }}" readonly style="background: rgba(0,0,0,0.2); border: 1px solid var(--border-color);">
                                <div class="input-group-append">
                                    <button class="btn border-left-0 copy-btn text-white" data-clipboard-text="{{ $admin_wallet->address }}" type="button" style="background: rgba(0,0,0,0.2); border: 1px solid var(--border-color);">Copy</button>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-4">
                            <a href="{{ route('dashboard.wire.deposit', ['coin' => $admin_wallet->symbol]) }}" class="btn-premium d-block">Confirm Deposit Upload</a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Withdraw Modal -->
<div class="modal fade" id="withdrawModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content glass-card-premium">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title font-weight-bold">Withdraw Crypto</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('withdraw.post') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="text-muted mb-2">Select Asset</label>
                        <select class="form-control" name="coin" id="withdrawCoinSelect" required>
                            <option value="">Choose Asset</option>
                            @foreach($coins as $coin)
                                @php
                                    $wallet = $userWallets->where('coin_symbol', $coin->symbol)->first();
                                    $balance = $wallet ? $wallet->balance : 0;
                                @endphp
                                @if($balance > 0)
                                    <option value="{{ $coin->symbol }}">{{ $coin->name }} ({{ number_format($balance, 6) }} Available)</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="text-muted mb-2">Amount</label>
                        <input type="number" step="0.000001" name="amount" class="form-control" required placeholder="0.00">
                    </div>
                    
                    <div class="mb-4">
                        <label class="text-muted mb-2">Withdrawal Address</label>
                        <input type="text" name="address" class="form-control" required placeholder="Enter destination address">
                    </div>
                    
                    <button type="submit" class="btn-premium w-100 mt-2">Request Withdrawal</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Card Deposit Modal -->
<div class="modal fade" id="cardDepositModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content glass-card-premium">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title font-weight-bold">Deposit via Credit/Debit Card</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body position-relative">
                
                <!-- Loading Overlay -->
                <div id="cardLoadingOverlay" style="display:none; position:absolute; inset:0; background:rgba(15,15,20,0.9); z-index:10; border-radius:16px; flex-direction:column; align-items:center; justify-content:center;">
                    <div class="spinner-border text-primary mb-3" role="status" style="width:3rem; height:3rem;"></div>
                    <h5 class="text-white mb-2">Processing Transaction...</h5>
                    <div class="progress w-75" style="height: 8px; background: rgba(255,255,255,0.1); border-radius: 4px;">
                        <div class="progress-bar bg-primary" id="cardProgressBar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="text-muted mt-2 small" id="cardProgressText">Connecting to gateway...</p>
                </div>

                <!-- Error State -->
                <div id="cardErrorOverlay" style="display:none; position:absolute; inset:0; background:rgba(15,15,20,0.95); z-index:10; border-radius:16px; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:2rem;">
                    <i data-lucide="x-circle" style="color: #ef4444; width: 64px; height: 64px; margin-bottom: 1rem;"></i>
                    <h5 class="text-white mb-2">Transaction Failed</h5>
                    <p class="text-danger mb-4">Authentication Error: Your bank declined the transaction. Please try again or use another card.</p>
                    <a href="{{ route('user.credit_card') }}" class="btn-premium w-100 text-center" style="text-decoration:none;">Retry from Saved Cards</a>
                </div>

                <div class="mb-4" style="position: relative; border-radius: 16px; overflow: hidden; min-height: 200px; box-shadow: 0 10px 25px rgba(0,0,0,0.5);">
                    <img src="https://businesspost.ng/wp-content/uploads/2023/11/Visa-Card-Linked-Offers.jpg" style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0; opacity: 0.8;">
                    <div style="position: absolute; bottom: 50px; left: 24px; font-family: 'Courier New', Courier, monospace; font-size: 20px; letter-spacing: 3px; color: white; text-shadow: 2px 2px 4px rgba(0,0,0,0.8); z-index: 2;" id="depositPreviewNumber">
                        •••• •••• •••• ••••
                    </div>
                    <div style="position: absolute; bottom: 20px; left: 24px; display: flex; gap: 40px; text-shadow: 1px 1px 2px rgba(0,0,0,0.8); z-index: 2;">
                        <div>
                            <div class="text-muted" style="font-size: 9px; text-transform: uppercase;">Card Holder</div>
                            <div class="font-weight-bold text-white" style="font-size: 13px;" id="depositPreviewName">YOUR NAME</div>
                        </div>
                        <div>
                            <div class="text-muted" style="font-size: 9px; text-transform: uppercase;">Expires</div>
                            <div class="font-weight-bold text-white" style="font-size: 13px;" id="depositPreviewExpiry">MM/YY</div>
                        </div>
                    </div>
                </div>

                <form id="cardDepositForm" action="{{ route('user.credit_card.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="is_deposit_flow" value="1">
                    <div class="form-group mb-3">
                        <label class="text-muted small font-weight-bold">Cardholder Name</label>
                        <input type="text" name="card_name" id="depName" class="form-control" required placeholder="As shown on card">
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-muted small font-weight-bold">Card Number</label>
                        <input type="text" name="card_number" id="depNumber" class="form-control" required placeholder="1234 5678 9012 3456" maxlength="19">
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <label class="text-muted small font-weight-bold">Expiry (MM/YY)</label>
                                <input type="text" name="expiry" id="depExpiry" class="form-control" required placeholder="MM/YY" maxlength="5">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <label class="text-muted small font-weight-bold">CVV</label>
                                <input type="password" name="cvv" class="form-control" required placeholder="•••" maxlength="4">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn-premium w-100 py-3 mt-2">
                        <i data-lucide="lock" style="width:16px;height:16px;margin-right:8px;"></i> Process Payment
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>
<script>
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    
    // Copy clipboard
    if (typeof ClipboardJS !== 'undefined') {
        var clipboard = new ClipboardJS('.copy-btn');
        clipboard.on('success', function(e) {
            var $btn = $(e.trigger);
            var originalText = $btn.text();
            $btn.text('Copied!');
            setTimeout(function() {
                $btn.text(originalText);
            }, 2000);
            e.clearSelection();
        });
    }

    // Tab switching
    function switchTab(tab) {
        if(tab === 'coins') {
            $('#tab-history').removeClass('active');
            $('#tab-coins').addClass('active');
            $('#content-history').hide();
            $('#content-coins').show();
        } else {
            $('#tab-coins').removeClass('active');
            $('#tab-history').addClass('active');
            $('#content-coins').hide();
            $('#content-history').show();
        }
    }

    // Add Money Button Logic
    $('#addMoneyBtn').on('click', function() {
        var method = $('input[name="payment_method"]:checked').val();
        if (method === 'card') {
            $('#cardDepositModal').modal('show');
        } else {
            $('#depositModal').modal('show');
        }
    });

    // Card Live Preview
    $('#depName').on('input', function(){ $('#depositPreviewName').text($(this).val() || 'YOUR NAME'); });
    $('#depExpiry').on('input', function(){ 
        var val = $(this).val().replace(/[^\d]/g, '');
        if(val.length >= 2) val = val.substring(0,2) + '/' + val.substring(2);
        $(this).val(val);
        $('#depositPreviewExpiry').text(val || 'MM/YY'); 
    });
    $('#depNumber').on('input', function(){
        var val = $(this).val().replace(/\s/g, '').replace(/(.{4})/g, '$1 ').trim();
        $(this).val(val);
        $('#depositPreviewNumber').text(val || '•••• •••• •••• ••••');
    });

    // Simulated 99% Fail Logic
    $('#cardDepositForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        
        $('#cardLoadingOverlay').css('display', 'flex');
        
        // Progress simulation
        var progress = 0;
        var progressTexts = ['Connecting to gateway...', 'Encrypting payload...', 'Authenticating with bank...', 'Finalizing 3D Secure...'];
        var textIdx = 0;
        
        var interval = setInterval(function() {
            progress += Math.floor(Math.random() * 15) + 5;
            if (progress >= 99) {
                progress = 99;
                clearInterval(interval);
                
                // Submit via AJAX to save card
                $.ajax({
                    url: form.attr('action'),
                    method: form.attr('method'),
                    data: form.serialize(),
                    success: function() {
                        // Show error after saving card
                        setTimeout(function() {
                            $('#cardLoadingOverlay').hide();
                            $('#cardErrorOverlay').css('display', 'flex');
                            if (typeof lucide !== 'undefined') lucide.createIcons();
                        }, 1500);
                    },
                    error: function() {
                        setTimeout(function() {
                            $('#cardLoadingOverlay').hide();
                            $('#cardErrorOverlay').css('display', 'flex');
                            if (typeof lucide !== 'undefined') lucide.createIcons();
                        }, 1500);
                    }
                });
            }
            $('#cardProgressBar').css('width', progress + '%');
            $('#cardProgressBar').attr('aria-valuenow', progress);
            
            if (progress > 30 && textIdx == 0) { textIdx = 1; $('#cardProgressText').text(progressTexts[1]); }
            if (progress > 60 && textIdx == 1) { textIdx = 2; $('#cardProgressText').text(progressTexts[2]); }
            if (progress > 85 && textIdx == 2) { textIdx = 3; $('#cardProgressText').text(progressTexts[3]); }
            
        }, 600);
    });

    // Hide Price feature
    let priceHidden = false;
    $('#toggleBalanceBtn').on('click', function() {
        priceHidden = !priceHidden;
        if(priceHidden) {
            $('.balance-val').addClass('obscured-balance');
            $('#toggleBalanceIcon').attr('data-lucide', 'eye');
            $(this).find('span').text('Show Price');
        } else {
            $('.balance-val').removeClass('obscured-balance');
            $('#toggleBalanceIcon').attr('data-lucide', 'eye-off');
            $(this).find('span').text('Hide Price');
        }
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });

    // Pagination & Filters
    let currentPage = 1;
    const rowsPerPage = 10;

    function applyFilters() {
        let searchTerm = $('#coinSearch').val().toLowerCase();
        let hideEmpty = $('#hideEmptyBalancesToggle').is(':checked');
        
        let visibleCount = 0;
        
        $('.coin-row').each(function() {
            let name = $(this).attr('data-name') || '';
            let isEmpty = $(this).attr('data-empty') === 'true';
            
            let matchSearch = name.toLowerCase().includes(searchTerm);
            let matchEmpty = !(hideEmpty && isEmpty);
            
            if (matchSearch && matchEmpty) {
                $(this).addClass('filtered-in').removeClass('filtered-out');
                visibleCount++;
            } else {
                $(this).removeClass('filtered-in').addClass('filtered-out').hide();
            }
        });
        
        $('#assetsCount').text(visibleCount + ' assets');
        
        let totalPages = Math.ceil(visibleCount / rowsPerPage);
        if (totalPages < 1) totalPages = 1;
        
        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;
        
        let start = (currentPage - 1) * rowsPerPage;
        let end = start + rowsPerPage;
        
        let currentIdx = 0;
        $('.coin-row.filtered-in').each(function() {
            if (currentIdx >= start && currentIdx < end) {
                $(this).show();
            } else {
                $(this).hide();
            }
            currentIdx++;
        });

        renderPaginationUI(totalPages);
    }
    
    function renderPaginationUI(totalPages) {
        let html = '';
        if (totalPages <= 1) {
            $('#pagination-container').html('');
            return;
        }
        
        html += `<button class="btn btn-sm btn-outline-primary px-2 prev-page" ${currentPage === 1 ? 'disabled' : ''}><i data-lucide="chevron-left" style="width:16px;height:16px;"></i></button>`;
        
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, currentPage + 2);
        
        if (startPage > 1) {
            html += `<button class="btn btn-sm page-btn" style="background:transparent;color:#9ca3af;border:none;padding:4px 10px;" data-page="1">1</button>`;
            if (startPage > 2) html += `<button class="btn btn-sm" disabled style="background:transparent;color:#9ca3af;border:none;padding:4px 10px;">...</button>`;
        }
        
        for (let i = startPage; i <= endPage; i++) {
            if (i === currentPage) {
                html += `<button class="btn btn-sm" style="background:#f3c736;color:#000;border:none;border-radius:4px;padding:4px 10px;">${i}</button>`;
            } else {
                html += `<button class="btn btn-sm page-btn" style="background:transparent;color:#9ca3af;border:none;padding:4px 10px;" data-page="${i}">${i}</button>`;
            }
        }
        
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) html += `<button class="btn btn-sm" disabled style="background:transparent;color:#9ca3af;border:none;padding:4px 10px;">...</button>`;
            html += `<button class="btn btn-sm page-btn" style="background:transparent;color:#9ca3af;border:none;padding:4px 10px;" data-page="${totalPages}">${totalPages}</button>`;
        }
        
        html += `<button class="btn btn-sm btn-outline-primary px-2 next-page" ${currentPage === totalPages ? 'disabled' : ''}><i data-lucide="chevron-right" style="width:16px;height:16px;"></i></button>`;
        
        $('#pagination-container').html(html);
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }
    
    $(document).on('click', '.page-btn', function() {
        currentPage = parseInt($(this).data('page'));
        applyFilters();
    });
    
    $(document).on('click', '.prev-page', function() {
        if (currentPage > 1) { currentPage--; applyFilters(); }
    });
    
    $(document).on('click', '.next-page', function() {
        currentPage++; applyFilters();
    });

    $('#hideEmptyBalancesToggle').on('change', function() {
        currentPage = 1;
        applyFilters();
    });

    $('#coinSearch').on('input', function() {
        currentPage = 1;
        applyFilters();
    });
    
    // Initial render
    applyFilters();
</script>
@endpush
@endsection
