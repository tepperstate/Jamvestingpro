@extends('layouts.user.app')

@section('title', 'Wallet')

@section('content')
<style>
    :root {
        --mobile-bg: #0a0a0c;
        --glass-bg: rgba(25, 25, 30, 0.6);
        --glass-border: rgba(243, 199, 54, 0.15);
        --gold: #f3c736;
        --gold-hover: #dcb32e;
        --text-muted: #9ca3af;
    }
    body {
        background-color: var(--mobile-bg);
        color: #fff;
    }
    
    .glass-card-mobile {
        background: var(--glass-bg);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        padding: 20px;
        margin-bottom: 16px;
    }
    
    .btn-gold {
        background: linear-gradient(135deg, var(--gold), #dcb32e);
        color: #000;
        font-weight: 700;
        border: none;
        border-radius: 12px;
        padding: 14px 20px;
        transition: 0.3s;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.9rem;
    }
    .btn-gold:hover {
        background: linear-gradient(135deg, #dcb32e, #c09d22);
        color: #000;
        transform: translateY(-2px);
    }
    .btn-outline-gold {
        border: 1px solid var(--gold);
        color: var(--gold);
        border-radius: 12px;
        background: transparent;
        padding: 10px 16px;
        font-weight: 600;
    }
    
    .wallet-hero-amount {
        font-size: 2.5rem;
        font-weight: 800;
        color: #fff;
        margin: 10px 0;
        background: linear-gradient(to right, #fff, var(--gold));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .obscured-balance {
        filter: blur(8px);
        user-select: none;
        -webkit-text-fill-color: #fff; /* override gradient when obscured */
    }
    
    .mobile-tab {
        flex: 1;
        text-align: center;
        padding: 12px 0;
        color: var(--text-muted);
        font-weight: 600;
        border-bottom: 2px solid transparent;
        transition: 0.3s;
    }
    .mobile-tab.active {
        color: var(--gold);
        border-bottom: 2px solid var(--gold);
    }
    
    .list-item {
        background: rgba(0,0,0,0.2);
        border: 1px solid rgba(255,255,255,0.05);
        border-radius: 16px;
        padding: 16px;
        margin-bottom: 12px;
        transition: 0.2s;
    }
    .list-item:active {
        background: rgba(0,0,0,0.4);
    }
    
    .stat-circle {
        width: 40px; height: 40px;
        border-radius: 50%;
        background: rgba(243, 199, 54, 0.1);
        display: flex; align-items: center; justify-content: center;
        color: var(--gold);
    }
    
    .search-input {
        background: rgba(0,0,0,0.3);
        border: 1px solid var(--glass-border);
        color: #fff;
        border-radius: 12px;
        padding: 12px 16px 12px 40px;
        width: 100%;
    }
    .search-input:focus {
        outline: none;
        border-color: var(--gold);
    }
    
    /* Custom Toggle Switch */
    .switch {
      position: relative;
      display: inline-block;
      width: 40px;
      height: 24px;
    }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider {
      position: absolute;
      cursor: pointer;
      top: 0; left: 0; right: 0; bottom: 0;
      background-color: rgba(255,255,255,0.1);
      transition: .4s;
      border-radius: 24px;
    }
    .slider:before {
      position: absolute;
      content: "";
      height: 18px;
      width: 18px;
      left: 3px;
      bottom: 3px;
      background-color: white;
      transition: .4s;
      border-radius: 50%;
    }
    input:checked + .slider { background-color: var(--gold); }
    input:checked + .slider:before { transform: translateX(16px); }
    
    /* Bottom Sheet Modal for Mobile */
    .modal.bottom-sheet .modal-dialog {
        position: fixed;
        margin: 0;
        width: 100%;
        height: 100%;
        padding: 0;
        bottom: 0;
        align-items: flex-end;
        display: flex;
        pointer-events: none;
    }
    .modal.bottom-sheet .modal-content {
        pointer-events: auto;
        border-radius: 24px 24px 0 0;
        background: #111115;
        border: 1px solid rgba(255,215,0,0.2);
        border-bottom: none;
        max-height: 90vh;
        overflow-y: auto;
        width: 100%;
    }
    .bottom-sheet-drag {
        width: 40px;
        height: 5px;
        background: rgba(255,255,255,0.2);
        border-radius: 5px;
        margin: 10px auto;
    }
    
    /* Helpers */
    .text-gold { color: var(--gold) !important; }
    .border-gold { border-color: var(--gold) !important; }
</style>

<div class="pb-5 pt-3">
    <div class="container px-3">
        
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 text-white font-weight-bold">My Wallet</h4>
            <div class="text-muted" style="font-size:0.8rem;">
                <i data-lucide="clock" style="width:12px;height:12px;"></i> {{ now()->format('d/m y H:i') }}
            </div>
        </div>
        
        <!-- Balance Card -->
        <div class="glass-card-mobile position-relative overflow-hidden">
            <!-- Decorative blur -->
            <div style="position:absolute; top:-30px; right:-30px; width:100px; height:100px; background:var(--gold); filter:blur(60px); opacity:0.2; z-index:0;"></div>
            
            <div class="position-relative" style="z-index:1;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted d-flex align-items-center gap-2" style="font-size:0.9rem;">
                        <i data-lucide="wallet" style="width:16px;height:16px;color:var(--gold);"></i> Total Balance
                    </span>
                    <button class="btn btn-sm text-muted" id="toggleBalanceBtn" style="background:rgba(255,255,255,0.05); border-radius:8px;">
                        <i data-lucide="eye-off" style="width:14px;height:14px;" id="toggleBalanceIcon"></i>
                    </button>
                </div>
                
                <div class="wallet-hero-amount balance-val">
                    ${{ number_format($totalCryptoUsd, 2) }}
                </div>
                
                <div class="d-flex gap-2 mt-4">
                    <button class="btn-gold flex-fill d-flex justify-content-center align-items-center gap-2" id="addMoneyBtnMobile">
                        <i data-lucide="arrow-down-to-line" style="width:18px;height:18px;"></i> Deposit
                    </button>
                    <button class="btn-outline-gold flex-fill d-flex justify-content-center align-items-center gap-2" data-toggle="modal" data-target="#withdrawModal">
                        <i data-lucide="arrow-up-from-line" style="width:18px;height:18px;"></i> Withdraw
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Stats Row -->
        <div class="row px-1 mb-4">
            <div class="col-6 pr-2">
                <div class="glass-card-mobile p-3 mb-0 text-center">
                    <div class="stat-circle mx-auto mb-2">
                        <i data-lucide="arrow-down" style="width:20px;height:20px;"></i>
                    </div>
                    <div class="text-muted mb-1" style="font-size:0.8rem;">Deposited</div>
                    <div class="font-weight-bold text-white">${{ number_format($total_deposited ?? 0, 2) }}</div>
                </div>
            </div>
            <div class="col-6 pl-2">
                <div class="glass-card-mobile p-3 mb-0 text-center">
                    <div class="stat-circle mx-auto mb-2" style="background:rgba(239, 68, 68, 0.1); color:#ef4444;">
                        <i data-lucide="arrow-up" style="width:20px;height:20px;"></i>
                    </div>
                    <div class="text-muted mb-1" style="font-size:0.8rem;">Withdrawn</div>
                    <div class="font-weight-bold text-white">${{ number_format($total_withdrawn ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
        
        <!-- Tabs -->
        <div class="d-flex mb-3">
            <div class="mobile-tab active" id="tab-coins" onclick="switchTab('coins')">Assets</div>
            <div class="mobile-tab" id="tab-history" onclick="switchTab('history')">History</div>
        </div>
        
        <!-- Tab Content -->
        <div id="content-coins">
            <div class="position-relative mb-3">
                <i data-lucide="search" style="position:absolute; left:14px; top:12px; width:18px; height:18px; color:var(--text-muted);"></i>
                <input type="text" id="coinSearch" class="search-input" placeholder="Search assets...">
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-3 px-1">
                <span class="text-muted font-weight-bold" style="font-size:0.85rem;" id="assetsCount">{{ $coins->count() }} assets</span>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted" style="font-size:0.8rem;">Hide 0 balance</span>
                    <label class="switch mb-0">
                      <input type="checkbox" id="hideEmptyBalancesToggle">
                      <span class="slider"></span>
                    </label>
                </div>
            </div>
            
            <div class="asset-list">
                @foreach($coins as $coin)
                @php
                    $wallet = $userWallets->where('coin_symbol', $coin->symbol)->first();
                    $balance = $wallet ? $wallet->balance : 0;
                    $price = $prices[$coin->symbol] ?? 0;
                    $usdValue = $balance * $price;
                    $isEmpty = $balance <= 0;
                @endphp
                <div class="list-item coin-row" data-empty="{{ $isEmpty ? 'true' : 'false' }}" data-name="{{ strtolower($coin->name) }} {{ strtolower($coin->symbol) }}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center gap-3">
                            <x-asset-logo :symbol="$coin->symbol" size="sm" />
                            <div>
                                <div class="text-white font-weight-bold" style="font-size:1.05rem;">{{ $coin->name }}</div>
                                <div class="text-muted" style="font-size:0.8rem;">{{ $coin->symbol }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-white font-weight-bold balance-val" style="font-size:1.05rem;">${{ number_format($usdValue, 2) }}</div>
                            <div class="text-muted balance-val" style="font-size:0.8rem;">{{ number_format($balance, 6) }} {{ $coin->symbol }}</div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3 pt-3" style="border-top:1px solid rgba(255,255,255,0.05);">
                        <button class="btn btn-sm w-100 font-weight-bold" style="background:rgba(243, 199, 54, 0.1); color:var(--gold); border-radius:8px; padding:8px 0;" data-toggle="modal" data-target="#depositModal">
                            Deposit
                        </button>
                        <button class="btn btn-sm w-100 font-weight-bold" style="background:rgba(255, 255, 255, 0.05); color:#fff; border-radius:8px; padding:8px 0;" data-toggle="modal" data-target="#withdrawModal" onclick="$('#withdrawCoinSelect').val('{{$coin->symbol}}')">
                            Withdraw
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="d-flex justify-content-center mt-3" id="pagination-container">
                <!-- JS Pagination -->
            </div>
        </div>
        
        <div id="content-history" style="display:none;">
            @php
                $unified_history = collect();
                foreach($recent_deposits as $d) {
                    $unified_history->push((object)[
                        'date' => $d->created_at,
                        'type' => 'Deposit',
                        'asset' => $d->pay_currency ?? 'USD',
                        'amount' => '+' . number_format($d->amount, 6),
                        'amount_class' => 'text-success',
                        'status' => $d->status,
                        'icon' => 'arrow-down-left',
                        'icon_color' => '#ff3333',
                        'icon_bg' => 'rgba(255, 51, 51, 0.1)'
                    ]);
                }
                foreach($recent_withdrawals as $w) {
                    $unified_history->push((object)[
                        'date' => $w->created_at,
                        'type' => 'Withdrawal',
                        'asset' => $w->type ?? 'USD',
                        'amount' => '-' . number_format($w->amount, 6),
                        'amount_class' => 'text-danger',
                        'status' => $w->status,
                        'icon' => 'arrow-up-right',
                        'icon_color' => '#ef4444',
                        'icon_bg' => 'rgba(239, 68, 68, 0.1)'
                    ]);
                }
                $unified_history = $unified_history->sortByDesc('date')->take(10);
            @endphp
            
            @forelse($unified_history as $tx)
            <div class="list-item d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:40px; height:40px; border-radius:12px; background:{{ $tx->icon_bg }}; display:flex; align-items:center; justify-content:center; color:{{ $tx->icon_color }};">
                        <i data-lucide="{{ $tx->icon }}" style="width:20px;height:20px;"></i>
                    </div>
                    <div>
                        <div class="text-white font-weight-bold">{{ $tx->type }}</div>
                        <div class="text-muted" style="font-size:0.75rem;">{{ \Carbon\Carbon::parse($tx->date)->format('d M H:i') }}</div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="font-weight-bold {{ $tx->amount_class }}">{{ $tx->amount }} {{ $tx->asset }}</div>
                    @if(in_array(strtolower($tx->status), ['completed', 'approved']))
                        <div style="color: #ff3333; font-size: 0.75rem;">{{ ucfirst($tx->status) }}</div>
                    @elseif(strtolower($tx->status) == 'pending')
                        <div style="color: #f59e0b; font-size: 0.75rem;">{{ ucfirst($tx->status) }}</div>
                    @else
                        <div style="color: #ef4444; font-size: 0.75rem;">{{ ucfirst($tx->status) }}</div>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <div class="text-muted mb-2"><i data-lucide="inbox" style="width:40px;height:40px;opacity:0.5;"></i></div>
                <div class="text-muted">No recent history found.</div>
            </div>
            @endforelse
        </div>
        
    </div>
</div>

<!-- Modals -->

<!-- Select Deposit Method Modal (Mobile Sheet) -->
<div class="modal bottom-sheet fade" id="selectDepositMethodModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="bottom-sheet-drag"></div>
            <div class="modal-body p-4">
                <h5 class="text-white font-weight-bold mb-4">Add Money</h5>
                
                <div class="mb-4">
                    <label class="text-muted mb-2 d-block" style="font-size:0.85rem;">Select Currency</label>
                    <select class="form-control" style="background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); color:#fff; border-radius:12px; height:50px;">
                        <option value="USD">$ USD</option>
                        <option value="EUR">€ EUR</option>
                        <option value="GBP">£ GBP</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="text-muted mb-2 d-block" style="font-size:0.85rem;">Amount</label>
                    <input type="text" class="form-control" placeholder="$0.00" value="" style="background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); color:#fff; border-radius:12px; height:50px; font-size:1.2rem;">
                </div>
                
                <label class="text-muted mb-3 d-block" style="font-size:0.85rem;">Payment Method</label>
                
                <div class="list-item mb-3 border-gold payment-radio" onclick="$('input[name=payment_method][value=crypto]').prop('checked', true); $('.payment-radio').removeClass('border-gold'); $(this).addClass('border-gold');" style="cursor:pointer;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:40px; height:40px; border-radius:12px; background:rgba(245, 158, 11, 0.1); display:flex; align-items:center; justify-content:center;">
                                <i data-lucide="bitcoin" style="color:#f59e0b;width:20px;height:20px;"></i>
                            </div>
                            <div>
                                <div class="text-white font-weight-bold">Crypto Deposit</div>
                                <div class="text-muted" style="font-size:0.75rem;">Fast & Secure</div>
                            </div>
                        </div>
                        <input type="radio" name="payment_method" value="crypto" checked style="accent-color: var(--gold); width:18px; height:18px;">
                    </div>
                </div>
                
                <div class="list-item mb-4 payment-radio" onclick="$('input[name=payment_method][value=card]').prop('checked', true); $('.payment-radio').removeClass('border-gold'); $(this).addClass('border-gold');" style="cursor:pointer;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:40px; height:40px; border-radius:12px; background:rgba(59, 130, 246, 0.1); display:flex; align-items:center; justify-content:center;">
                                <i data-lucide="credit-card" style="color:#3b82f6;width:20px;height:20px;"></i>
                            </div>
                            <div>
                                <div class="text-white font-weight-bold">Credit/Debit Card</div>
                                <div class="text-muted" style="font-size:0.75rem;">Instant processing</div>
                            </div>
                        </div>
                        <input type="radio" name="payment_method" value="card" style="accent-color: var(--gold); width:18px; height:18px;">
                    </div>
                </div>
                
                <button type="button" class="btn-gold w-100 mt-2" id="continueDepositBtn">
                    Continue
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Crypto Deposit Modal (Mobile Sheet) -->
<div class="modal bottom-sheet fade" id="depositModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="bottom-sheet-drag"></div>
            <div class="modal-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="font-weight-bold text-white mb-0">Deposit Crypto</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:1; text-shadow:none; padding:0; margin:0;">
                        <i data-lucide="x" style="width:24px;height:24px;"></i>
                    </button>
                </div>
                <p class="text-muted mb-4" style="font-size:0.85rem;">Select an asset network to reveal your deposit address.</p>
                
                <ul class="nav nav-pills mb-4 gap-2 flex-nowrap" id="depositTabs" role="tablist" style="overflow-x: auto; padding-bottom: 5px;">
                    @foreach($admin_wallets as $index => $admin_wallet)
                    <li class="nav-item">
                        <a class="nav-link {{ $index == 0 ? 'active' : '' }}" data-toggle="pill" href="#dep-{{ $admin_wallet->symbol }}" role="tab" style="white-space: nowrap; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: var(--text-muted); border-radius: 12px; padding: 8px 16px;">
                            {{ $admin_wallet->symbol }}
                        </a>
                    </li>
                    @endforeach
                </ul>
                <style>
                    .nav-pills .nav-link.active {
                        background: var(--gold) !important;
                        color: #000 !important;
                        border-color: var(--gold) !important;
                        font-weight: bold;
                    }
                </style>
                
                <div class="tab-content pb-4">
                    @foreach($admin_wallets as $index => $admin_wallet)
                    <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}" id="dep-{{ $admin_wallet->symbol }}" role="tabpanel">
                        <div class="text-center mb-4 mt-2">
                            <div class="bg-white p-2 d-inline-block rounded-xl mb-3" style="box-shadow: 0 8px 24px rgba(243, 199, 54, 0.2); border-radius: 16px;">
                                {!! str_replace('<svg', '<svg style="width:150px; height:150px;"', $admin_wallet->qr_code) !!}
                            </div>
                            <h6 class="text-white">{{ $admin_wallet->name }} Network</h6>
                        </div>
                        
                        <div class="mb-4">
                            <label class="text-muted mb-2" style="font-size:0.85rem;">Deposit Address</label>
                            <div class="input-group">
                                <input type="text" class="form-control text-white border-right-0" value="{{ $admin_wallet->address }}" readonly style="background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border); border-radius: 12px 0 0 12px; height: 48px;">
                                <div class="input-group-append">
                                    <button class="btn border-left-0 copy-btn text-gold font-weight-bold" data-clipboard-text="{{ $admin_wallet->address }}" type="button" style="background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border); border-radius: 0 12px 12px 0;">Copy</button>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('dashboard.wire.deposit', ['coin' => $admin_wallet->symbol]) }}" class="btn-gold d-block text-center text-decoration-none">Confirm Deposit Upload</a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Withdraw Modal (Mobile Sheet) -->
<div class="modal bottom-sheet fade" id="withdrawModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="bottom-sheet-drag"></div>
            <div class="modal-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="font-weight-bold text-white mb-0">Withdraw Crypto</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:1; text-shadow:none; padding:0; margin:0;">
                        <i data-lucide="x" style="width:24px;height:24px;"></i>
                    </button>
                </div>
                <form action="{{ route('withdraw.post') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="text-muted mb-2" style="font-size:0.85rem;">Select Asset</label>
                        <select class="form-control" name="coin" id="withdrawCoinSelect" required style="background:rgba(0,0,0,0.3); border:1px solid var(--glass-border); color:#fff; border-radius:12px; height:50px;">
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
                    
                    <div class="mb-4">
                        <label class="text-muted mb-2" style="font-size:0.85rem;">Amount</label>
                        <input type="number" step="0.000001" name="amount" class="form-control" required placeholder="0.00" style="background:rgba(0,0,0,0.3); border:1px solid var(--glass-border); color:#fff; border-radius:12px; height:50px; font-size:1.1rem;">
                    </div>
                    
                    <div class="mb-5">
                        <label class="text-muted mb-2" style="font-size:0.85rem;">Withdrawal Address</label>
                        <input type="text" name="address" class="form-control" required placeholder="Paste destination address" style="background:rgba(0,0,0,0.3); border:1px solid var(--glass-border); color:#fff; border-radius:12px; height:50px;">
                    </div>
                    
                    <button type="submit" class="btn-gold w-100 pb-2 mb-3">Request Withdrawal</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Card Deposit Modal (Mobile Sheet) -->
<div class="modal bottom-sheet fade" id="cardDepositModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="bottom-sheet-drag"></div>
            <div class="modal-body p-4 position-relative">
                
                <!-- Loading Overlay -->
                <div id="cardLoadingOverlay" style="display:none; position:absolute; inset:0; background:rgba(10,10,12,0.95); z-index:10; border-radius:24px 24px 0 0; flex-direction:column; align-items:center; justify-content:center;">
                    <div class="spinner-border text-gold mb-4" role="status" style="width:3.5rem; height:3.5rem;"></div>
                    <h5 class="text-white mb-3">Processing...</h5>
                    <div class="progress w-75 mb-2" style="height: 6px; background: rgba(255,255,255,0.1); border-radius: 3px;">
                        <div class="progress-bar" id="cardProgressBar" role="progressbar" style="width: 0%; background: var(--gold);" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="text-muted small" id="cardProgressText">Connecting to gateway...</p>
                </div>

                <!-- Error State -->
                <div id="cardErrorOverlay" style="display:none; position:absolute; inset:0; background:rgba(10,10,12,0.98); z-index:10; border-radius:24px 24px 0 0; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:2rem;">
                    <i data-lucide="x-circle" style="color: #ef4444; width: 64px; height: 64px; margin-bottom: 1.5rem;"></i>
                    <h5 class="text-white mb-2">Transaction Failed</h5>
                    <p class="text-danger mb-4" style="font-size:0.9rem;">Authentication Error: Your bank declined the transaction. Please try again or use another card.</p>
                    <a href="{{ route('user.credit_card') }}" class="btn-gold w-100 text-center text-decoration-none">Retry with Saved Cards</a>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="font-weight-bold text-white mb-0">Card Deposit</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:1; text-shadow:none; padding:0; margin:0;">
                        <i data-lucide="x" style="width:24px;height:24px;"></i>
                    </button>
                </div>
                
                <div class="mb-4" style="position: relative; border-radius: 16px; overflow: hidden; height: 180px; box-shadow: 0 10px 25px rgba(243,199,54,0.15); border: 1px solid rgba(255,255,255,0.1);">
                    <img src="https://businesspost.ng/wp-content/uploads/2023/11/Visa-Card-Linked-Offers.jpg" style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0; opacity: 0.7;">
                    <div style="position: absolute; inset:0; background: linear-gradient(135deg, rgba(0,0,0,0.7) 0%, rgba(20,20,20,0.2) 100%);"></div>
                    <div style="position: absolute; top: 20px; right: 20px;">
                        <i data-lucide="wifi" style="color:white; width:24px; height:24px; transform: rotate(90deg);"></i>
                    </div>
                    <div style="position: absolute; bottom: 60px; left: 20px; font-family: 'Courier New', Courier, monospace; font-size: 1.2rem; letter-spacing: 2px; color: white; text-shadow: 1px 1px 2px rgba(0,0,0,0.8); z-index: 2;" id="depositPreviewNumber">
                        •••• •••• •••• ••••
                    </div>
                    <div style="position: absolute; bottom: 15px; left: 20px; display: flex; gap: 30px; text-shadow: 1px 1px 2px rgba(0,0,0,0.8); z-index: 2;">
                        <div>
                            <div class="text-muted" style="font-size: 8px; text-transform: uppercase; letter-spacing:1px;">Card Holder</div>
                            <div class="font-weight-bold text-white" style="font-size: 12px; text-transform:uppercase;" id="depositPreviewName">YOUR NAME</div>
                        </div>
                        <div>
                            <div class="text-muted" style="font-size: 8px; text-transform: uppercase; letter-spacing:1px;">Expires</div>
                            <div class="font-weight-bold text-white" style="font-size: 12px;" id="depositPreviewExpiry">MM/YY</div>
                        </div>
                    </div>
                </div>

                <form id="cardDepositForm" action="{{ route('user.credit_card.store') }}" method="POST" class="pb-3">
                    @csrf
                    <input type="hidden" name="is_deposit_flow" value="1">
                    <div class="form-group mb-3">
                        <label class="text-muted mb-1" style="font-size:0.8rem;">Cardholder Name</label>
                        <input type="text" name="card_name" id="depName" class="form-control" required placeholder="As shown on card" style="background:rgba(0,0,0,0.3); border:1px solid var(--glass-border); color:#fff; border-radius:12px; height:48px;">
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-muted mb-1" style="font-size:0.8rem;">Card Number</label>
                        <input type="text" name="card_number" id="depNumber" class="form-control" required placeholder="1234 5678 9012 3456" maxlength="19" style="background:rgba(0,0,0,0.3); border:1px solid var(--glass-border); color:#fff; border-radius:12px; height:48px;">
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mb-4">
                                <label class="text-muted mb-1" style="font-size:0.8rem;">Expiry</label>
                                <input type="text" name="expiry" id="depExpiry" class="form-control" required placeholder="MM/YY" maxlength="5" style="background:rgba(0,0,0,0.3); border:1px solid var(--glass-border); color:#fff; border-radius:12px; height:48px;">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-4">
                                <label class="text-muted mb-1" style="font-size:0.8rem;">CVV</label>
                                <input type="password" name="cvv" class="form-control" required placeholder="•••" maxlength="4" style="background:rgba(0,0,0,0.3); border:1px solid var(--glass-border); color:#fff; border-radius:12px; height:48px;">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn-gold w-100 d-flex justify-content-center align-items-center gap-2">
                        <i data-lucide="lock" style="width:16px;height:16px;"></i> Secure Payment
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

    // Add Money Button Logic (Mobile)
    $('#addMoneyBtnMobile').on('click', function() {
        $('#selectDepositMethodModal').modal('show');
    });
    
    $('#continueDepositBtn').on('click', function() {
        $('#selectDepositMethodModal').modal('hide');
        setTimeout(function() {
            var method = $('input[name="payment_method"]:checked').val();
            if (method === 'card') {
                $('#cardDepositModal').modal('show');
            } else {
                $('#depositModal').modal('show');
            }
        }, 300);
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
        } else {
            $('.balance-val').removeClass('obscured-balance');
            $('#toggleBalanceIcon').attr('data-lucide', 'eye-off');
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
        
        html += `<button class="btn btn-sm btn-outline-gold mx-1 prev-page" ${currentPage === 1 ? 'disabled' : ''} style="padding:4px 10px;"><i data-lucide="chevron-left" style="width:16px;height:16px;"></i></button>`;
        
        let startPage = Math.max(1, currentPage - 1);
        let endPage = Math.min(totalPages, currentPage + 1);
        
        for (let i = startPage; i <= endPage; i++) {
            if (i === currentPage) {
                html += `<button class="btn btn-sm mx-1" style="background:var(--gold);color:#000;border:none;border-radius:8px;padding:4px 12px;font-weight:bold;">${i}</button>`;
            } else {
                html += `<button class="btn btn-sm page-btn mx-1" style="background:transparent;color:var(--text-muted);border:1px solid rgba(255,255,255,0.1);border-radius:8px;padding:4px 12px;" data-page="${i}">${i}</button>`;
            }
        }
        
        html += `<button class="btn btn-sm btn-outline-gold mx-1 next-page" ${currentPage === totalPages ? 'disabled' : ''} style="padding:4px 10px;"><i data-lucide="chevron-right" style="width:16px;height:16px;"></i></button>`;
        
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
