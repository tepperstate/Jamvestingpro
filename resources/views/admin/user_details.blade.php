@extends('layouts.admin.app')
@section('title', 'User Profiles')

@section('content')

@if(session()->has('message'))
    <script>toastr.success("{{ session()->get('message') }}","success")</script>
@endif
@if(session('status'))
    <script>toastr.success("{{session('status')}}","success")</script>
@endif

@push('styles')
<style>
/* Premium Bento Styling */
.bento-item {
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    background: linear-gradient(145deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.01) 100%);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.08);
    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2);
    border-radius: 24px;
    position: relative;
    overflow: hidden;
}
.bento-item::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    opacity: 0.5;
}
.bento-item:hover {
    transform: translateY(-5px) scale(1.02);
    box-shadow: 0 15px 45px rgba(0, 0, 0, 0.3);
    background: linear-gradient(145deg, rgba(255,255,255,0.08) 0%, rgba(255,255,255,0.02) 100%);
    border: 1px solid rgba(255, 255, 255, 0.15);
}
.bento-icon-wrapper {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.05);
    transition: all 0.3s ease;
}
.bento-item:hover .bento-icon-wrapper {
    background: rgba(255, 255, 255, 0.15);
    transform: scale(1.1) rotate(5deg);
}
.btn-glass-premium {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(4px);
    transition: all 0.3s ease;
    border-radius: 10px;
}
.btn-glass-premium:hover {
    background: rgba(255, 255, 255, 0.15);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}
.premium-glass-card {
    transition: all 0.4s ease;
    background: linear-gradient(145deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.01) 100%);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
    border: 1px solid rgba(255,255,255,0.1);
    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2);
}
.premium-glass-card:hover {
    box-shadow: 0 15px 45px rgba(0,0,0,0.3);
    border: 1px solid rgba(255,255,255,0.2);
}
</style>
@endpush

<div class="container-fluid">
    <!-- 1. IDENTITY HEADER: Modern Glassmorphism Identity Card -->
    <div class="glass-card premium-glass-card mb-5 overflow-hidden shadow-2xl satin-border" style="border-radius: 24px;">
        <div class="p-4 p-md-5 d-flex flex-column flex-md-row align-items-center gap-4" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(139, 92, 246, 0.05) 50%, rgba(0,0,0,0) 100%);">
            <div class="position-relative">
                <img src="{{ $user->image ? asset('storage/image/'.$user->image) : 'https://ui-avatars.com/api/?name='.$user->first_name.'&size=128&background=0D8ABC&color=fff' }}" 
                     class="shadow-xl" 
                     style="width: 100px; height: 100px; border-radius: 25px; border: 4px solid var(--glass-border); object-fit: cover;">
                <div class="position-absolute" style="bottom: -5px; right: -5px;">
                    <div class="badge {{ $user->status == 'active' ? 'badge-success' : 'badge-danger' }} rounded-circle p-2 border-4 border-dark" style="width: 20px; height: 20px;"></div>
                </div>
            </div>
            
            <div class="text-center text-md-left flex-grow-1">
                <div class="d-flex align-items-center gap-2 mb-1 justify-content-center justify-content-md-start">
                    <h1 class="h3 text-white font-weight-bold mb-0">{{$user->first_name}} {{$user->last_name}}</h1>
                    @if($user->is_demo)
                    <span class="badge badge-warning-glass px-2 py-1 text-uppercase x-small">DEMO ACCOUNT</span>
                    @endif
                    <span class="badge badge-primary-glass px-2 py-1 text-uppercase x-small d-none d-sm-inline">{{$user->package_plan ?? 'UNASSIGNED Plan'}}</span>
                </div>
                <div class="badge badge-primary-glass px-2 py-1 text-uppercase x-small d-inline-block d-sm-none mb-2">{{$user->package_plan ?? 'UNASSIGNED Plan'}}</div>
                <p class="text-muted mb-3 d-flex flex-wrap align-items-center justify-content-center justify-content-md-start small">
                    <span class="d-flex align-items-center"><i data-lucide="mail" class="mr-2" style="width:14px"></i> {{$user->email}}</span>
                    <span class="mx-2 opacity-25 d-none d-sm-inline">|</span>
                    <span class="d-flex align-items-center"><i data-lucide="calendar" class="mr-2" style="width:14px"></i> Joined: {{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}</span>
                </p>
                
                <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-md-start">
                    <div class="badge {{ $user->email_verified ? 'badge-success-glass' : 'badge-danger-glass' }} px-2 py-1">
                        <i data-lucide="{{ $user->email_verified ? 'check-circle' : 'x-circle' }}" style="width:10px" class="mr-1"></i> EMAIL
                    </div>
                    <div class="badge {{ $user->indentify_verified ? 'badge-success-glass' : 'badge-danger-glass' }} px-2 py-1">
                        <i data-lucide="{{ $user->indentify_verified ? 'shield-check' : 'shield-alert' }}" style="width:10px" class="mr-1"></i> IDENTITY
                    </div>
                    <div class="badge {{ $user->status == 'active' ? 'badge-success-glass' : 'badge-danger-glass' }} px-2 py-1">
                        <i data-lucide="power" style="width:10px" class="mr-1"></i> {{ strtoupper($user->status) }}
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column gap-2 w-100-mobile" style="min-width: 200px;">
                <a href="{{route('loginUsernow',$user->id)}}" class="btn btn-primary glass-panel border-0 satin-border font-weight-bold py-2 shadow-lg btn-sm">
                    <i data-lucide="user-plus" class="mr-2" style="width:14px; display:inline-block; vertical-align:middle;"></i> Infiltrate Session
                </a>
                <button class="btn btn-danger glass-panel border-0 satin-border font-weight-bold py-2 btn-sm" data-toggle="modal" data-target="#delete">
                    <i data-lucide="trash-2" class="mr-2" style="width:14px; display:inline-block; vertical-align:middle;"></i> Purge Account
                </button>
            </div>
        </div>
    </div>

    <!-- Moved Security Hub to Tabs -->

    <!-- 3. PREMIUM BENTO GRID STATS -->
    <div class="row g-4 mb-5">
        <div class="col-lg-3">
            <div class="bento-item p-4 h-100 d-flex flex-column" style="border-left: 4px solid #10b981;">
                <div class="d-flex justify-content-between align-items-center mb-3 text-white-50">
                    <span class="x-small font-weight-bold tracking-wider">LIVE LEDGER</span>
                    <div class="bento-icon-wrapper text-success"><i data-lucide="wallet"></i></div>
                </div>
                <h3 class="text-white mb-1 font-weight-bold" style="font-size: 2rem;">${{number_format($c->amount)}}</h3>
                <p class="text-muted small mb-4 flex-grow-1">Verified Equity</p>
                <div class="d-flex gap-2 mt-auto">
                    <a href="{{route('fund', ['user' => $c->user_id, 'symbol' => $c->symbol]) }}" class="btn btn-sm btn-glass-premium text-success flex-grow-1 font-weight-bold"><i data-lucide="arrow-down-left" class="mr-1" style="width:14px; display:inline-block; vertical-align:middle;"></i> Credit</a>
                    <a href="{{route('debit', ['user' => $c->user_id, 'symbol' => $c->symbol]) }}" class="btn btn-sm btn-glass-premium text-danger flex-grow-1 font-weight-bold"><i data-lucide="arrow-up-right" class="mr-1" style="width:14px; display:inline-block; vertical-align:middle;"></i> Debit</a>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="bento-item p-4 h-100 d-flex flex-column" style="border-left: 4px solid #6366f1;">
                <div class="d-flex justify-content-between align-items-center mb-3 text-white-50">
                    <span class="x-small font-weight-bold tracking-wider">SIMULATOR</span>
                    <div class="bento-icon-wrapper text-primary"><i data-lucide="cpu"></i></div>
                </div>
                <h3 class="text-white mb-1 font-weight-bold" style="font-size: 2rem;">${{number_format($c->demo)}}</h3>
                <p class="text-muted small mb-4 flex-grow-1">Training Capital</p>
                <div class="d-flex gap-2 mt-auto">
                    <button data-toggle="modal" data-target="#edit_demo_balance" class="btn btn-sm btn-glass-premium text-primary flex-grow-1 font-weight-bold w-100"><i data-lucide="refresh-cw" class="mr-1" style="width:14px; display:inline-block; vertical-align:middle;"></i> Update Balance</button>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="bento-item p-4 h-100 d-flex flex-column" style="border-left: 4px solid #8b5cf6;">
                <div class="d-flex justify-content-between align-items-center mb-3 text-white-50">
                    <span class="x-small font-weight-bold tracking-wider">ENGAGEMENT</span>
                    <div class="bento-icon-wrapper text-info"><i data-lucide="activity"></i></div>
                </div>
                <h3 class="text-white mb-1 font-weight-bold" style="font-size: 2rem;">${{number_format($total_trade)}}</h3>
                <p class="text-muted small mb-4 flex-grow-1">Total Life Volume</p>
                <div class="progress mt-auto bg-white-05 rounded-pill overflow-hidden" style="height:6px">
                    <div class="progress-bar bg-info progress-bar-striped progress-bar-animated" style="width: 70%"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="bento-item p-4 h-100 d-flex flex-column" style="border-left: 4px solid #f59e0b;">
                <div class="d-flex justify-content-between align-items-center mb-3 text-white-50">
                    <span class="x-small font-weight-bold tracking-wider">CASHFLOW</span>
                    <div class="bento-icon-wrapper text-warning"><i data-lucide="landmark"></i></div>
                </div>
                <div class="d-flex flex-column gap-2 mb-3 flex-grow-1 mt-2">
                    <div class="d-flex justify-content-between align-items-center p-2 rounded" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2);">
                        <span class="small text-white-50">Inbound</span>
                        <span class="text-success font-weight-bold" style="color: #34d399 !important;">${{number_format($total_deposit)}}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center p-2 rounded" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2);">
                        <span class="small text-white-50">Pending</span>
                        <span class="text-danger font-weight-bold" style="color: #f87171 !important;">${{number_format($total_withdrawal)}}</span>
                    </div>
                </div>
                <div class="mt-auto pt-2 text-right border-top border-white-05">
                    <a href="{{route('delete_deposit',$user->id)}}" class="x-small font-weight-bold text-danger text-decoration-none d-inline-flex align-items-center" style="transition: opacity 0.2s;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">
                        <i data-lucide="trash" class="mr-1" style="width:12px;"></i> PURGE CACHE
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. TABBED ACTION HUB -->
    <div class="glass-card satin-border overflow-hidden">
        <div class="nav-scroll-wrapper">
            <ul class="nav nav-tabs nav-justified border-white-05 bg-white-02 flex-nowrap" id="commandTabs" role="tablist">
                <li class="nav-item"><a class="nav-link active py-3 py-md-4 font-weight-bold text-uppercase x-small" id="financial-tab" data-toggle="tab" href="#financial" role="tab">Financials</a></li>
                <li class="nav-item"><a class="nav-link py-3 py-md-4 font-weight-bold text-uppercase x-small" id="settings-tab" data-toggle="tab" href="#settings" role="tab">Process</a></li>
                <li class="nav-item"><a class="nav-link py-3 py-md-4 font-weight-bold text-uppercase x-small" id="market-tab" data-toggle="tab" href="#market" role="tab">Holdings</a></li>
                <li class="nav-item"><a class="nav-link py-3 py-md-4 font-weight-bold text-uppercase x-small" id="comm-tab" data-toggle="tab" href="#comm" role="tab">Comms</a></li>
                <li class="nav-item"><a class="nav-link py-3 py-md-4 font-weight-bold text-uppercase x-small" id="identity-tab" data-toggle="tab" href="#identity" role="tab">KYC</a></li>
            </ul>
        </div>
        <style>
            .nav-scroll-wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; scrollbar-width: none; }
            .nav-scroll-wrapper::-webkit-scrollbar { display: none; }
            .nav-tabs { width: max-content; min-width: 100%; border-bottom: 2px solid rgba(255,255,255,0.05); }
            .nav-tabs .nav-link { border: none !important; color: rgba(255,255,255,0.5); position: relative; transition: all 0.3s ease; }
            .nav-tabs .nav-link:hover { color: rgba(255,255,255,0.8); background: rgba(255,255,255,0.03); }
            .nav-tabs .nav-link.active { 
                color: #fff !important; 
                background: linear-gradient(0deg, rgba(99, 102, 241, 0.1) 0%, transparent 100%) !important; 
                text-shadow: 0 0 10px rgba(99, 102, 241, 0.5);
            }
            .nav-tabs .nav-link.active::after {
                content: '';
                position: absolute;
                bottom: 0; left: 0; right: 0; height: 3px;
                background: #6366f1;
                box-shadow: 0 -2px 10px rgba(99, 102, 241, 0.8);
            }
            .custom-switch-premium {
                padding: 10px 15px;
                border-radius: 12px;
                background: rgba(255,255,255,0.02);
                border: 1px solid rgba(255,255,255,0.05);
                transition: all 0.3s ease;
            }
            .custom-switch-premium:hover {
                background: rgba(255,255,255,0.05);
                border-color: rgba(255,255,255,0.1);
                transform: translateX(2px);
            }
            @media (max-width: 768px) {
                .w-100-mobile { width: 100% !important; }
                .tab-pane { padding: 1.5rem !important; }
            }
        </style>
        <div class="tab-content">
            <!-- Financial Portfolio Tab -->
            <div class="tab-pane fade show active p-5" id="financial" role="tabpanel">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="glass-panel p-4 h-100 d-flex flex-column">
                            <h6 class="text-muted x-small font-weight-bold uppercase mb-4">Direct Link Management</h6>
                            <p class="text-white-50 small mb-4">Manage withdrawal network addresses and payment gateway configurations.</p>
                            <div class="mt-auto d-flex flex-column gap-2">
                                <button class="btn btn-primary btn-sm btn-block" data-toggle="modal" data-target="#wallets">Register New Gateway</button>
                                <a href="{{route('wallet_index',$user->id)}}" class="btn btn-outline-info btn-sm btn-block">Advanced Logic</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="glass-panel p-4 h-100 mb-4">
                            <h6 class="text-muted x-small font-weight-bold uppercase mb-4">Ledger Activity</h6>
                            <div class="d-flex flex-wrap gap-2 mb-4">
                                <button class="btn btn-xs glass-panel text-white font-weight-bold border-0 px-3" onclick="showView('financial_content_area', 'view_history_deposit')"><i data-lucide="arrow-down-left" class="mr-1" style="width:12px"></i> Deposit Logs</button>
                                <button class="btn btn-xs glass-panel text-white font-weight-bold border-0 px-3" onclick="showView('financial_content_area', 'view_history_withdrawal')"><i data-lucide="arrow-up-right" class="mr-1" style="width:12px"></i> Withdrawal Logs</button>
                                <button class="btn btn-xs glass-panel text-success font-weight-bold border-0 px-3" data-toggle="modal" data-target="#deposit_history"><i data-lucide="plus" class="mr-1" style="width:12px"></i> Force Deposit</button>
                                <button class="btn btn-xs glass-panel text-danger font-weight-bold border-0 px-3" data-toggle="modal" data-target="#deposit_withdrawal"><i data-lucide="minus" class="mr-1" style="width:12px"></i> Force Withdrawal</button>
                            </div>
                            <!-- Dynamic Content Area -->
                            <div id="financial_content_area" class="border-top border-white-05 pt-4" style="max-height: 400px; overflow-y: auto;">
                                <div id="view_history_deposit" class="content-view" style="display:none;">
                                    <div class="table-responsive">
                                        <table class="table text-white mb-0">
                                            <thead>
                                                <tr><th>AMOUNT</th><th>METHOD</th><th>STATUS</th><th>DATE</th></tr>
                                            </thead>
                                            <tbody>
                                                @forelse($deposit_history as $dep)
                                                <tr>
                                                    <td>{{$user->currency}}{{number_format($dep->amount, 2)}}</td>
                                                    <td>{{$dep->coin->name ?? $dep->method ?? 'Crypto'}}</td>
                                                    <td>
                                                        @if($dep->status == 'success') <span class="badge badge-success">SUCCESS</span>
                                                        @elseif($dep->status == 'pending') <span class="badge badge-warning text-dark">PENDING</span>
                                                        @else <span class="badge badge-danger">FAILED</span>
                                                        @endif
                                                    </td>
                                                    <td class="small text-muted">{{ \Carbon\Carbon::parse($dep->created_at)->format('M d, Y') }}</td>
                                                </tr>
                                                @empty
                                                <tr><td colspan="4" class="text-center text-muted py-3">No deposit history.</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div id="view_history_withdrawal" class="content-view" style="display:none;">
                                    <div class="table-responsive">
                                        <table class="table text-white mb-0">
                                            <thead>
                                                <tr><th>AMOUNT</th><th>METHOD</th><th>STATUS</th><th>DATE</th></tr>
                                            </thead>
                                            <tbody>
                                                @forelse($withdrawal_history as $wit)
                                                <tr>
                                                    <td>{{$user->currency}}{{number_format($wit->amount, 2)}}</td>
                                                    <td>{{$wit->method ?? 'Crypto'}}</td>
                                                    <td>
                                                        @if($wit->status == 'success') <span class="badge badge-success">SUCCESS</span>
                                                        @elseif($wit->status == 'pending') <span class="badge badge-warning text-dark">PENDING</span>
                                                        @else <span class="badge badge-danger">FAILED</span>
                                                        @endif
                                                    </td>
                                                    <td class="small text-muted">{{ \Carbon\Carbon::parse($wit->created_at)->format('M d, Y') }}</td>
                                                </tr>
                                                @empty
                                                <tr><td colspan="4" class="text-center text-muted py-3">No withdrawal history.</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            <h6 class="text-muted x-small font-weight-bold uppercase mt-5 mb-3">Asset Interfaces</h6>
                            <div id="tab_wallets_content" class="table-responsive">
                                @forelse($payment as $pay)
                                <div class="glass-panel p-3 mb-2 d-flex justify-content-between align-items-center" style="border-radius:12px;">
                                    <div>
                                        <div class="font-weight-bold text-white small">{{$pay->name}}</div>
                                        <div class="x-small text-muted" style="word-break: break-all;">{{$pay->address}}</div>
                                    </div>
                                    <button class="btn btn-xs btn-outline-info" onclick="editAddress({{$pay->id}}, '{{$pay->name}}', '{{$pay->address}}')">Edit</button>
                                </div>
                                @empty
                                <div class="text-center text-muted py-3">No wallets found.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Settings Tab -->
            <div class="tab-pane fade p-5" id="settings" role="tabpanel">
                <div class="row g-4">
                    <!-- Control Permissions Block Moved from Header -->
                    <div class="col-12 mb-4">
                        <div class="glass-card premium-glass-card satin-border overflow-hidden" style="border-radius: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                            <div class="p-3 p-md-4 d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: linear-gradient(135deg, rgba(255,255,255,0.08) 0%, rgba(255,255,255,0.02) 100%); border-bottom: 1px solid var(--glass-border);">
                                <h5 class="m-0 font-weight-bold text-white d-flex align-items-center h6">
                                    <i data-lucide="shield" class="mr-2 mr-md-3 text-primary" style="width:18px"></i> Security & Permissions Control
                                </h5>
                                <div class="badge badge-primary-glass px-3 py-1 x-small shadow-sm">MASTER ACCESS</div>
                            </div>
                            <div class="p-4" style="background: rgba(0,0,0,0.15);">
                                <div class="row g-4">
                                    <div class="col-lg-3 col-md-6 border-right border-white-05">
                                        <div class="p-3">
                                            <h6 class="text-muted x-small font-weight-bold text-uppercase mb-3" style="color: #6366f1 !important; text-shadow: 0 0 10px rgba(99,102,241,0.3);">Core Transfers</h6>
                                            <div class="d-flex flex-column gap-2">
                                                <div class="custom-control custom-switch custom-switch-premium d-flex align-items-center justify-content-between">
                                                    <label class="text-white small mb-0" for="transfer" style="cursor: pointer;">Internal Ledger</label>
                                                    <div><input type="checkbox" {{ $user->transfer == 'on' ? 'checked' : '' }} class="custom-control-input" id="transfer" onchange="toggleSecurity('transfer')"><label class="custom-control-label" for="transfer"></label></div>
                                                </div>
                                                <div class="custom-control custom-switch custom-switch-premium d-flex align-items-center justify-content-between">
                                                    <label class="text-white small mb-0" for="withdrawal" style="cursor: pointer;">Outbound Gateway</label>
                                                    <div><input type="checkbox" {{ $user->withdrawal == 'on' ? 'checked' : '' }} class="custom-control-input" id="withdrawal" onchange="toggleSecurity('withdrawal')"><label class="custom-control-label" for="withdrawal"></label></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-3 col-md-6 border-right border-white-05">
                                        <div class="p-3">
                                            <h6 class="text-muted x-small font-weight-bold text-uppercase mb-3" style="color: #10b981 !important; text-shadow: 0 0 10px rgba(16,185,129,0.3);">Multi-Factor Auth</h6>
                                            <div class="d-flex flex-column gap-2">
                                                <div class="custom-control custom-switch custom-switch-premium d-flex align-items-center justify-content-between">
                                                    <label class="text-white small mb-0" for="otp_enabled" style="cursor: pointer;">Email OTP Layer</label>
                                                    <div><input type="checkbox" {{ $user->otp_enabled == '1' ? 'checked' : '' }} class="custom-control-input" id="otp_enabled" onchange="toggleSecurity('otp_enabled')"><label class="custom-control-label" for="otp_enabled"></label></div>
                                                </div>
                                                <div class="custom-control custom-switch custom-switch-premium d-flex align-items-center justify-content-between">
                                                    <label class="text-white small mb-0" for="is_2fa_enabled" style="cursor: pointer;">Bio-Auth/2FA</label>
                                                    <div><input type="checkbox" {{ $user->is_2fa_enabled == '1' ? 'checked' : '' }} class="custom-control-input" id="is_2fa_enabled" onchange="toggleSecurity('is_2fa_enabled')"><label class="custom-control-label" for="is_2fa_enabled"></label></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                    
                                    <div class="col-lg-3 col-md-6 border-right border-white-05">
                                        <div class="p-3">
                                            <h6 class="text-muted x-small font-weight-bold text-uppercase mb-3" style="color: #f59e0b !important; text-shadow: 0 0 10px rgba(245,158,11,0.3);">Account Status</h6>
                                            <div class="d-flex flex-column gap-2">
                                                <div class="custom-control custom-switch custom-switch-premium d-flex align-items-center justify-content-between">
                                                    <label class="text-white small mb-0" for="email_verified" style="cursor: pointer;">Email Trust</label>
                                                    <div><input type="checkbox" {{ $user->email_verified == '1' ? 'checked' : '' }} class="custom-control-input" id="email_verified" onchange="toggleSecurity('email_verified')"><label class="custom-control-label" for="email_verified"></label></div>
                                                </div>
                                                <div class="custom-control custom-switch custom-switch-premium d-flex align-items-center justify-content-between">
                                                    <label class="text-white small mb-0" for="general" style="cursor: pointer;">Asset Access</label>
                                                    <div><input type="checkbox" {{ $user->general == '1' ? 'checked' : '' }} class="custom-control-input" id="general" onchange="toggleSecurity('general')"><label class="custom-control-label" for="general"></label></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                    
                                    <div class="col-lg-3 col-md-6">
                                        <div class="p-3">
                                            <h6 class="text-muted x-small font-weight-bold text-uppercase mb-3" style="color: #ec4899 !important; text-shadow: 0 0 10px rgba(236,72,153,0.3);">Special Ops</h6>
                                            <div class="d-flex flex-column gap-2">
                                                <div class="custom-control custom-switch custom-switch-premium d-flex align-items-center justify-content-between">
                                                    <label class="text-white small mb-0" for="custom" style="cursor: pointer;">Custom UI Hub</label>
                                                    <div><input type="checkbox" {{ $user->custom == 'on' ? 'checked' : '' }} class="custom-control-input" id="custom" onchange="toggleSecurity('custom')"><label class="custom-control-label" for="custom"></label></div>
                                                </div>
                                                <div class="custom-control custom-switch custom-switch-premium d-flex align-items-center justify-content-between">
                                                    <label class="text-white small mb-0" for="exit_trade" style="cursor: pointer;">Smart Liquidation</label>
                                                    <div><input type="checkbox" {{ $user->exit_trade == 'on' ? 'checked' : '' }} class="custom-control-input" id="exit_trade" onchange="toggleSecurity('exit_trade')"><label class="custom-control-label" for="exit_trade"></label></div>
                                                </div>
                                                <div class="custom-control custom-switch custom-switch-premium d-flex align-items-center justify-content-between">
                                                    <label class="text-white small mb-0" for="is_demo" style="cursor: pointer;">Demo Mode</label>
                                                    <div><input type="checkbox" {{ $user->is_demo ? 'checked' : '' }} class="custom-control-input" id="is_demo" onchange="toggleDemo()"><label class="custom-control-label" for="is_demo"></label></div>
                                                </div>
                                                <div class="custom-control custom-switch custom-switch-premium d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <label class="text-white small mb-0" for="basic_plan_access" style="cursor: pointer;">Basic Plan</label>
                                                        @if($user->basic_plan_approved == 2)
                                                        <span class="badge badge-warning-glass x-small px-2 py-1" style="animation: pulse 2s infinite;">PENDING</span>
                                                        @elseif($user->basic_plan_approved == 1)
                                                        <span class="badge badge-success-glass x-small px-2 py-1">APPROVED</span>
                                                        @endif
                                                    </div>
                                                    <div><input type="checkbox" {{ $user->basic_plan_approved == 1 ? 'checked' : '' }} class="custom-control-input" id="basic_plan_access" onchange="toggleBasicPlan()"><label class="custom-control-label" for="basic_plan_access"></label></div>
                                                </div>
                                                <div class="custom-control custom-switch custom-switch-premium d-flex align-items-center justify-content-between">
                                                    <label class="text-white small mb-0" for="hip_pro_access" style="cursor: pointer;">HIP Pro Access</label>
                                                    <div><input type="checkbox" {{ $user->hasHipProAccess() ? 'checked' : '' }} class="custom-control-input" id="hip_pro_access" onchange="toggleHipPro()"><label class="custom-control-label" for="hip_pro_access"></label></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="glass-panel p-4 h-100">
                            <h6 class="text-muted x-small font-weight-bold uppercase mb-4">System Tiering</h6>
                            <form method="post" action="{{route('package_plan')}}">
                                @csrf
                                <input type="hidden" name="id" value="{{$user->id}}">
                                <div class="form-group mb-4">
                                    <label class="text-white small">Current Startment Package</label>
                                    <select class="form-control glass-panel border-0 text-white" name="package">
                                        @foreach($package as $v) <option value="{{$v->id}}" {{$user->package_plan == $v->name ? 'selected' : ''}}>{{$v->name}}</option> @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-4">
                                      <label class="text-white small">Total Trade Limit</label>
                                      <input type="number" class="form-control glass-panel border-0 text-white" name="trade" value="{{$user->trades}}">
                                  </div>
                                  <div class="form-group mb-4">
                                      <label class="text-white small">22-Hour Trade Limit (0 to remove limit)</label>
                                      <input type="number" class="form-control glass-panel border-0 text-white" name="daily_trade" value="{{$user->daily_trade}}">
                                  </div>
                                <button type="submit" class="btn btn-primary btn-block">Synchronize Tier</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="glass-panel p-4 h-100">
                            <h6 class="text-muted x-small font-weight-bold uppercase mb-4">Security Access Codes</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <div class="p-3 bg-white-02 rounded">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted x-small font-weight-bold">ACCOUNT TIER VERIFICATION</span>
                                            <div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id="upgrade_check" {{ $user->upgrade_code_check == 'on' ? 'checked' : '' }} onchange="toggleCode('upgrade_code_check')"><label class="custom-control-label" for="upgrade_check"></label></div>
                                        </div>
                                        <div class="h5 text-white font-weight-bold mb-0">{{$user->upgrade_code ?? '---'}}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 bg-white-02 rounded">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted x-small font-weight-bold">COMPLIANCE CLEARANCE</span>
                                            <div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id="tax_check" {{ $user->tax_code_check == 'on' ? 'checked' : '' }} onchange="toggleCode('tax_code_check')"><label class="custom-control-label" for="tax_check"></label></div>
                                        </div>
                                        <div class="h5 text-white font-weight-bold mb-0">{{$user->tax_code ?? '---'}}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 bg-white-02 rounded">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted x-small font-weight-bold">PROCESSING PRIORITY</span>
                                            <div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id="dem_check" {{ $user->demorage_check == 'on' ? 'checked' : '' }} onchange="toggleCode('demorage_check')"><label class="custom-control-label" for="dem_check"></label></div>
                                        </div>
                                        <div class="h5 text-white font-weight-bold mb-0">{{$user->demorage ?? '---'}}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <form method="post" action="{{route('update_code_generate')}}" class="flex-grow-1"><@csrf><input type="hidden" name="user_id" value="{{$user->id}}"><button class="btn btn-outline-primary btn-block">Generate New Security Codes</button></form>
                                <button class="btn btn-outline-info" data-toggle="modal" data-target="#code">Manage Aliases</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Market Holdings Tab -->
            <div class="tab-pane fade p-5" id="market" role="tabpanel">
                <div class="row g-4">
                    <div class="col-md-3">
                        <div class="glass-panel p-4 h-100">
                            <h6 class="text-muted x-small font-weight-bold uppercase mb-4">Operations Interface</h6>
                            <div class="d-flex flex-column gap-3">
                                <button class="btn glass-panel border-0 text-left py-3 text-white" onclick="showView('market_content_area', 'view_trades')"><i data-lucide="bar-chart-2" class="mr-2" style="width:16px"></i> Asset Positions</button>
                                <button class="btn glass-panel border-0 text-left py-3 text-white" onclick="showView('market_content_area', 'view_stocks')"><i data-lucide="pie-chart" class="mr-2" style="width:16px"></i> Equity Portfolio</button>
                                <button class="btn glass-panel border-0 text-left py-3 text-white" onclick="showView('market_content_area', 'view_bots')"><i data-lucide="bot" class="mr-2" style="width:16px"></i> Autonomy Accounts</button>
                                <hr class="border-white-05 mx-n2">
                                <button class="btn btn-warning-glass btn-sm" data-toggle="modal" data-target="#generate">Inject History Account</button>
                                <button class="btn btn-info-glass btn-sm" data-toggle="modal" data-target="#bot">Inject AI Account</button>
                                <button class="btn btn-primary-glass btn-sm mt-2" data-toggle="modal" data-target="#injectSignal">Inject Signal Account</button>
                                <button class="btn btn-secondary-glass btn-sm mt-2" data-toggle="modal" data-target="#injectCopy">Inject Copy Trade</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="glass-panel p-4" style="min-height: 400px; max-height: 500px; overflow-y: auto;">
                            <div id="market_content_area" class="h-100">
                                <div id="view_default" class="text-center py-5 text-muted opacity-50 content-view"><i data-lucide="layout" class="mb-3" style="width:48px"></i><br>Select a domain to view its active matrices.</div>
                                <div id="view_trades" class="content-view" style="display:none;">
                                    <div class="table-responsive">
                                        <table class="table text-white mb-0">
                                            <thead>
                                                <tr><th>ASSET</th><th>VOLUME</th><th>RATE</th><th>STATUS</th><th>DATE</th></tr>
                                            </thead>
                                            <tbody>
                                                @forelse($data as $trade)
                                                <tr>
                                                    <td><span class="badge glass-panel text-white border-0">{{$trade->symbol}}</span></td>
                                                    <td>{{$user->currency}}{{number_format($trade->amount, 2)}}</td>
                                                    <td>{{$trade->strike_rate ?? 'N/A'}}</td>
                                                    <td>
                                                        @if($trade->status == 'win') <span class="badge badge-success">WIN</span>
                                                        @elseif($trade->status == 'loss') <span class="badge badge-danger">LOSS</span>
                                                        @elseif($trade->status == 'draw') <span class="badge badge-info">DRAW</span>
                                                        @else <span class="badge badge-warning text-dark">PENDING</span>
                                                        @endif
                                                    </td>
                                                    <td class="small text-muted">{{ \Carbon\Carbon::parse($trade->created_at)->format('M d, Y') }}</td>
                                                </tr>
                                                @empty
                                                <tr><td colspan="5" class="text-center text-muted py-3">No asset positions.</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div id="view_stocks" class="content-view" style="display:none;">
                                    <div class="table-responsive">
                                        <table class="table text-white mb-0">
                                            <thead>
                                                <tr><th>NAME</th><th>SYMBOL</th><th>UNITS / VALUE</th><th class="text-right">ACTION</th></tr>
                                            </thead>
                                            <tbody>
                                                @forelse($stock as $s)
                                                <tr>
                                                    <td>{{$s->name}}</td>
                                                    <td><span class="badge glass-panel text-white border-0">{{$s->symbol}}</span></td>
                                                    <td>{{number_format($s->amount, 4)}} Units</td>
                                                    <td class="text-right">
                                                        <button class="btn btn-sm btn-outline-light" data-toggle="modal" data-target="#editStock{{$s->id}}"><i class="ri-edit-2-line"></i> Edit</button>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr><td colspan="3" class="text-center text-muted py-3">No equity portfolio.</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div id="view_bots" class="content-view" style="display:none;">
                                    <div class="table-responsive">
                                        <table class="table text-white mb-0">
                                            <thead>
                                                <tr><th>BOT</th><th>DURATION</th><th>AMOUNT</th></tr>
                                            </thead>
                                            <tbody>
                                                @forelse($bot as $b)
                                                <tr>
                                                    <td>{{$b->name}}</td>
                                                    <td>{{$b->day}} Days</td>
                                                    <td>{{$user->currency}}{{number_format($b->amount, 2)}}</td>
                                                </tr>
                                                @empty
                                                <tr><td colspan="3" class="text-center text-muted py-3">No autonomy accounts.</td></tr>
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

            <!-- Communications Center Tab -->
            <div class="tab-pane fade p-5" id="comm" role="tabpanel">
                <div class="row g-4">
                    <div class="col-lg-5">
                        <div class="glass-panel p-4 mb-4">
                            <h6 class="text-muted x-small font-weight-bold uppercase mb-4">Direct Terminal</h6>
                            <form method="post" action="{{route('send_message')}}">
                                @csrf
                                <input type="hidden" name="user_id" value="{{$user->id}}">
                                <input type="text" name="subject" class="form-control glass-panel border-0 text-white mb-3" placeholder="Subject Vector Establishment" required>
                                <textarea name="message" id="summernote" class="form-control glass-panel border-0 text-white" required></textarea>
                                <button class="btn btn-primary btn-block mt-4">Broadcast Transmission</button>
                            </form>
                        </div>
                        <div class="glass-panel p-4">
                            <h6 class="text-muted x-small font-weight-bold uppercase mb-4">Interface Customizer</h6>
                            <button class="btn btn-outline-primary btn-block py-3 mb-2" data-toggle="modal" data-target="#customs">Set Global UI Message</button>
                            <p class="x-small text-muted text-center">Controls localized frosted-glass feedback loops for the user account.</p>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="glass-panel p-4 h-100">
                            <h6 class="text-muted x-small font-weight-bold uppercase mb-4">Signal Logs</h6>
                            <div id="tab_messages_content" style="max-height: 500px; overflow-y: auto;">
                                @forelse($message as $msg)
                                <div class="glass-panel p-3 mb-3" style="border-radius:12px;">
                                    <div class="font-weight-bold text-white small">{{$msg->title ?? $msg->subject ?? 'Message'}}</div>
                                    <div class="x-small text-muted mb-2">{{ \Carbon\Carbon::parse($msg->created_at)->format('M d, Y h:i A') }}</div>
                                    <div class="small text-white-50">{!! $msg->message !!}</div>
                                </div>
                                @empty
                                <div class="text-center text-muted py-3">No messages found.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Identity Tab -->
            <div class="tab-pane fade p-5" id="identity" role="tabpanel">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="glass-panel p-4 h-100 border-left border-warning" style="border-width: 4px !important;">
                            <div class="d-flex justify-content-between mb-4">
                                <h6 class="text-muted x-small font-weight-bold uppercase">KYC Questionnaire Matrix</h6>
                                <span class="badge badge-warning-glass">REVIEW PENDING</span>
                            </div>
                            <p class="text-white-50 small mb-4">Analyzing demographic vectors, risk appetite, and source of capital declarations.</p>
                            <button class="btn btn-warning btn-block font-weight-bold py-3" data-toggle="modal" data-target="#questions">ACCESS RAW MATRIX DATA</button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="glass-panel p-4 h-100 border-left border-info" style="border-width: 4px !important;">
                            <div class="d-flex justify-content-between mb-4">
                                <h6 class="text-muted x-small font-weight-bold uppercase">Security Intelligence</h6>
                                <i data-lucide="key" class="text-info" style="width:16px"></i>
                            </div>
                            <p class="text-white-50 small mb-4">Active verification of passphrase challenges and secret recovery phrase integrity.</p>
                            <button class="btn btn-info btn-block font-weight-bold py-3" data-toggle="modal" data-target="#security">SECURITY QUESTIONS</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals pushed from loops -->
@push('modals')
    @foreach($stock as $s)
    <!-- Edit Stock Modal -->
    <div class="modal fade" id="editStock{{$s->id}}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content glass-modal satin-border shadow-2xl border-0">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title font-weight-bold text-white">Edit Asset Performance</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.user_stock.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{$s->id}}">
                    <div class="modal-body text-left p-4 pt-0">
                        <div class="form-group mb-3 mt-3">
                            <label class="text-muted small uppercase mb-1">Asset Units / Volume</label>
                            <input type="number" step="any" name="amount" class="form-control glass-panel border-0 text-white" value="{{$s->amount}}" required>
                            <small class="form-text text-muted mt-2">Adjusting units will directly impact the total equity value of this asset.</small>
                        </div>
                    </div>
                    <div class="modal-footer border-top border-white-05 pt-3">
                        <button type="button" class="btn glass-panel text-white px-4" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-premium px-4 font-weight-bold">Update Asset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
@endpush

@push('modals')
<!-- Inject Signal Account Modal -->
<div class="modal fade" id="injectSignal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content glass-modal satin-border shadow-2xl border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-white font-weight-bold">Inject Signal Account</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <form action="{{ route('generateSignal') }}" method="POST" class="modal-async-form">
                @csrf
                <input type="hidden" name="user_id" value="{{$user->id}}">
                <div class="modal-body p-4 pt-0 text-left">
                    <div class="form-group mb-3">
                        <label class="text-muted small uppercase mb-1">Signal Type</label>
                        <select name="signal_id" class="form-control glass-panel border-0 text-white" required>
                            @foreach(\DB::table('signals')->get() as $s)
                            <option value="{{$s->id}}" class="text-dark">{{$s->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-muted small uppercase mb-1">Asset (Symbols)</label>
                        <select name="symbols" class="form-control glass-panel border-0 text-white" required>
                            @foreach(\App\Models\Asset::all() as $asset)
                            <option value="{{$asset->symbols}}" class="text-dark">{{$asset->symbols}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-muted small uppercase mb-1">Trade Direction</label>
                        <select name="type" class="form-control glass-panel border-0 text-white" required>
                            <option value="buy" class="text-dark">Buy</option>
                            <option value="sell" class="text-dark">Sell</option>
                        </select>
                    </div>
                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <label class="text-muted small uppercase mb-1">Min Capital</label>
                            <input type="number" step="any" name="min" class="form-control glass-panel border-0 text-white font-weight-bold" placeholder="100" required>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small uppercase mb-1">Max Capital</label>
                            <input type="number" step="any" name="max" class="form-control glass-panel border-0 text-white font-weight-bold" placeholder="1000" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-premium btn-block py-3 font-weight-bold">
                        <span class="btn-text">Inject Signal Account</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Inject Copy Trade Modal -->
<div class="modal fade" id="injectCopy" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content glass-modal satin-border shadow-2xl border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-white font-weight-bold">Inject Copy Trade</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <form action="{{ route('admin.generate.generate_copy') }}" method="POST" class="modal-async-form">
                @csrf
                <input type="hidden" name="user_id" value="{{$user->id}}">
                <div class="modal-body p-4 pt-0 text-left">
                    <div class="form-group mb-3">
                        <label class="text-muted small uppercase mb-1">Target Trader</label>
                        <select name="trader_id" class="form-control glass-panel border-0 text-white" required>
                            @foreach(\App\Models\Trader::all() as $trader)
                            <option value="{{$trader->id}}" class="text-dark">{{$trader->name}} ({{$trader->percentage}}%)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="text-muted x-small uppercase mb-1">Start Date</label>
                            <input type="date" name="start_date" class="form-control glass-panel border-0 text-white" required>
                        </div>
                        <div class="col-6">
                            <label class="text-muted x-small uppercase mb-1">End Date</label>
                            <input type="date" name="end_date" class="form-control glass-panel border-0 text-white" required>
                        </div>
                    </div>
                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <label class="text-muted small uppercase mb-1">Min Capital</label>
                            <input type="number" step="any" name="min" class="form-control glass-panel border-0 text-white font-weight-bold" placeholder="100" required>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small uppercase mb-1">Max Capital</label>
                            <input type="number" step="any" name="max" class="form-control glass-panel border-0 text-white font-weight-bold" placeholder="1000" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-premium btn-block py-3 font-weight-bold">
                        <span class="btn-text">Inject Copy Trade</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush


@include('admin.user_details_modals_logic')

<script>
    $(document).ready(function() {
        // Initializing UI Mounts
        $('#tab_wallets_content').append($('#source_wallets').children());
        $('#tab_messages_content').append($('#source_messages').children());

        // Security Field Toggle Sync
        window.toggleSecurity = function(field) {
            const status = $('#' + field).is(':checked') ? 'on' : 'off';
            const routes = {
                'transfer': "{{route('transfer')}}",
                'withdrawal': "{{route('withdrawal.onOffWithdrawal')}}",
                'custom': "{{route('custom')}}",
                'exit_trade': "{{route('exit_trade')}}",
                'general': "{{route('general')}}",
                'otp_enabled': "{{route('admin_onOffOtp')}}",
                'is_2fa_enabled': "{{route('admin_onOffGoogle')}}",
                'email_verified': "{{route('admin_onOffEmail')}}"
            };
            
            $.ajax({
                url: routes[field],
                method: "POST",
                data: { _token: "{{csrf_token()}}", user: "{{$user->id}}" },
                success: function() { toastr.success('Process ' + field.toUpperCase() + ' Synchronized', 'HUB SECURE'); },
                error: function() { 
                    toastr.error('Link Failure', 'SYSTEM ERROR');
                    $('#' + field).prop('checked', !$('#' + field).is(':checked'));
                }
            });
        };

        // Code Access Toggle Sync
        window.toggleCode = function(field) {
            const routes = {
                'upgrade_code_check': "{{route('upgrade_code_check')}}",
                'tax_code_check': "{{route('tax_code_check')}}",
                'demorage_check': "{{route('demorage_check')}}"
            };
            $.ajax({
                url: routes[field],
                method: "POST",
                data: { _token: "{{csrf_token()}}", user: "{{$user->id}}" },
                success: function() { toastr.success('Access Vector Updated', 'ENCRYPTION MODIFIED'); },
                error: function() { toastr.error('Sync Mismatch', 'ERROR'); }
            });
        };

        // Demo Mode Toggle
        window.toggleDemo = function() {
            $.ajax({
                url: "{{route('admin.user.toggle_demo')}}",
                method: "POST",
                data: { _token: "{{csrf_token()}}", user: "{{$user->id}}" },
                success: function(data) { 
                    if(data.status) {
                        toastr.success('User Mode Switched', 'SYSTEM MODIFIED');
                        location.reload(); 
                    }
                },
                error: function() { 
                    toastr.error('Link Failure', 'SYSTEM ERROR');
                    $('#is_demo').prop('checked', !$('#is_demo').is(':checked'));
                }
            });
        };

        // Basic Plan Access Toggle
        window.toggleBasicPlan = function() {
            $.ajax({
                url: "{{route('admin.user.toggle_basic_plan')}}",
                method: "POST",
                data: { _token: "{{csrf_token()}}", user: "{{$user->id}}" },
                success: function(data) { 
                    if(data.status) {
                        toastr.success('Basic Plan Access ' + data.state.toUpperCase(), 'PLAN ACCESS');
                        location.reload(); 
                    }
                },
                error: function() { 
                    toastr.error('Link Failure', 'SYSTEM ERROR');
                    $('#basic_plan_access').prop('checked', !$('#basic_plan_access').is(':checked'));
                }
            });
        };

        // HIP Pro Access Toggle
        window.toggleHipPro = function() {
            $.ajax({
                url: "{{route('admin.user.toggle_hip_pro')}}",
                method: "POST",
                data: { _token: "{{csrf_token()}}", user: "{{$user->id}}" },
                success: function(data) { 
                    if(data.status) {
                        toastr.success('HIP Pro Access Updated', 'PLAN ACCESS');
                        location.reload(); 
                    }
                },
                error: function() { 
                    toastr.error('Link Failure', 'SYSTEM ERROR');
                    $('#hip_pro_access').prop('checked', !$('#hip_pro_access').is(':checked'));
                }
            });
        };

        // Async Modal Form Submission Handler
        $('.modal-async-form').on('submit', function(e) {
            e.preventDefault();
            const $form = $(this);
            const $submitBtn = $form.find('button[type="submit"], button:not([type]), button.btn-premium, input[type="submit"]');
            const $btnText = $submitBtn.find('.btn-text');
            const $spinner = $submitBtn.find('.spinner-border');

            // Sync summernote if present
            if (typeof $.fn.summernote !== 'undefined') {
                $form.find('textarea').each(function() {
                    if ($(this).data('summernote') || $(this).hasClass('summernote') || $(this).next().hasClass('note-editor')) {
                        $(this).val($(this).summernote('code'));
                    }
                });
            }

            const formData = $form.serialize();

            // Disable button, show spinner, and disable form inputs
            $submitBtn.prop('disabled', true);
            if ($btnText.length) $btnText.addClass('opacity-50');
            if ($spinner.length) $spinner.removeClass('d-none');
            $form.find('input, select, textarea').prop('disabled', true);

            $.ajax({
                url: $form.attr('action'),
                method: $form.attr('method') || 'POST',
                data: formData,
                success: function(response) {
                    toastr.success(response.message || 'Operation executed successfully.', 'SUCCESS');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                },
                error: function(xhr) {
                    let errMsg = 'Operation failed. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errMsg = xhr.responseJSON.message;
                    }
                    toastr.error(errMsg, 'ERROR');
                    
                    // Re-enable form
                    $submitBtn.prop('disabled', false);
                    if ($btnText.length) $btnText.removeClass('opacity-50');
                    if ($spinner.length) $spinner.addClass('d-none');
                    $form.find('input, select, textarea').prop('disabled', false);
                }
            });
        });
    });
</script>

@endsection

