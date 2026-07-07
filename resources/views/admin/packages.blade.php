@extends('layouts.admin.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-white mb-1">Investment Packages</h4>
            <p class="text-secondary mb-0">Configure and manage investment tiers for users.</p>
        </div>
        <a onclick="history.back()" href="javascript:void(0)" class="btn btn-outline-light btn-sm rounded-pill px-3">
            <i class="ri-arrow-left-line me-1"></i> Back
        </a>
    </div>

    <div class="row">
        <!-- Add Package Form -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="glass-card p-4 h-100">
                <h5 class="outfit font-weight-bold mb-4 text-white">Create New Package</h5>
                <form method="POST" action="{{ route('store_package') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="small text-secondary font-weight-bold text-uppercase mb-2">Package Name</label>
                        <input type="text" name="name" class="form-control bg-dark-soft border-glass text-white py-2" placeholder="e.g. Starter Package" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="small text-secondary font-weight-bold text-uppercase mb-2">Min Investment Amount ($)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark-soft border-glass text-secondary">$</span>
                            <input type="number" name="amount" class="form-control bg-dark-soft border-glass text-white py-2" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="small text-secondary font-weight-bold text-uppercase mb-2">Auto-Upgrade Threshold ($)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark-soft border-glass text-secondary">$</span>
                            <input type="number" name="min_deposit" class="form-control bg-dark-soft border-glass text-white py-2" placeholder="e.g. 5000">
                        </div>
                        <small class="text-muted" style="font-size: 0.7rem;">Cumulative deposit required to reach this tier.</small>
                    </div>

                    <div class="form-group mb-3">
                        <label class="small text-secondary font-weight-bold text-uppercase mb-2">Tier Features</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="checkbox" name="features[]" value="basic_trading" id="f_basic" checked>
                                    <label class="form-check-label text-white small" for="f_basic">Basic Trading</label>
                                </div>
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="checkbox" name="features[]" value="high_leverage" id="f_leverage">
                                    <label class="form-check-label text-white small" for="f_leverage">High Leverage</label>
                                </div>
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="checkbox" name="features[]" value="vip_stocks" id="f_vip">
                                    <label class="form-check-label text-white small" for="f_vip">VIP Stocks</label>
                                </div>
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="checkbox" name="features[]" value="mutual_funds" id="f_mutual">
                                    <label class="form-check-label text-white small" for="f_mutual">Trading Signals</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="checkbox" name="features[]" value="bot" id="f_bot">
                                    <label class="form-check-label text-white small" for="f_bot">Bot Trading</label>
                                </div>
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="checkbox" name="features[]" value="signal" id="f_signal">
                                    <label class="form-check-label text-white small" for="f_signal">Discover</label>
                                </div>
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="checkbox" name="features[]" value="copy_trading" id="f_copy">
                                    <label class="form-check-label text-white small" for="f_copy">CopyTrader™</label>
                                </div>
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="checkbox" name="features[]" value="high_yield" id="f_yield">
                                    <label class="form-check-label text-white small" for="f_yield">High Yield Earn</label>
                                </div>
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="checkbox" name="features[]" value="advanced_controls" id="f_advanced">
                                    <label class="form-check-label text-white small" for="f_advanced">Advanced Controls</label>
                                </div>
                            </div>
                        </div>
                    </div>



                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="small text-secondary font-weight-bold text-uppercase mb-2">Return Rate (%)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="perc" class="form-control bg-dark-soft border-glass text-white py-2" placeholder="e.g. 5.5" required>
                                <span class="input-group-text bg-dark-soft border-glass text-secondary">%</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-secondary font-weight-bold text-uppercase mb-2">Duration (Days)</label>
                            <input type="number" name="day" class="form-control bg-dark-soft border-glass text-white py-2" placeholder="e.g. 30" required>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="small text-secondary font-weight-bold text-uppercase mb-2">Total Trades</label>
                            <input type="number" name="trade" class="form-control bg-dark-soft border-glass text-white py-2" placeholder="e.g. 10" required>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-secondary font-weight-bold text-uppercase mb-2">Daily Limit</label>
                            <input type="number" name="daily_trade" class="form-control bg-dark-soft border-glass text-white py-2" placeholder="e.g. 5">
                        </div>
                        <div class="col-md-4">
                            <label class="small text-secondary font-weight-bold text-uppercase mb-2">Weekly Limit</label>
                            <input type="number" name="weekly_trade" class="form-control bg-dark-soft border-glass text-white py-2" placeholder="e.g. 20">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 font-weight-bold rounded-3 shadow-lg">
                        <i class="ri-save-line me-2"></i> SAVE PACKAGE
                    </button>
                </form>
            </div>
        </div>

        <!-- Packages List -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="glass-card p-0 h-100 overflow-hidden">
                <div class="p-4 border-bottom border-glass d-flex justify-content-between align-items-center">
                    <h5 class="outfit font-weight-bold mb-0 text-white">Active Packages</h5>
                    <span class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill">{{ count($data) }} Total</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-black-soft text-secondary small text-uppercase">
                            <tr>
                                <th class="border-0 px-4 py-3">ID</th>
                                <th class="border-0 py-3">Package Details</th>
                                <th class="border-0 py-3 text-center">ROI / Terms</th>
                                <th class="border-0 py-3 text-center">Trades</th>
                                <th class="border-0 px-4 py-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-white border-glass">
                            @foreach ($data as $key => $c)
                            <tr class="border-glass">
                                <td class="px-4 text-secondary">{{ $key + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="p-2 rounded bg-primary-soft text-primary me-3">
                                            <i class="ri-package-line h5 mb-0"></i>
                                        </div>
                                        <div>
                                            <div class="font-weight-bold text-white mb-0">{{ $c->name }}</div>
                                            <div class="small text-secondary">
                                                Min Inv: <span class="text-success font-weight-bold">${{ number_format($c->amount) }}</span> | 
                                                Threshold: <span class="text-info font-weight-bold">${{ number_format($c->min_deposit ?? $c->amount) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="h5 font-weight-bold text-success mb-0">{{ number_format($c->perc, 2) }}%</div>
                                    <div class="small text-secondary text-uppercase">{{ $c->day }} Days</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info-soft text-info mb-1">{{ $c->trade }} Total</span>
                                    <div class="x-small text-secondary text-uppercase font-weight-bold">
                                        {{ $c->daily_trade }} Daily / {{ $c->weekly_trade }} Weekly
                                    </div>
                                </td>
                                <td class="px-4 text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('show_package_list', $c->id) }}" class="btn btn-outline-info border-glass" title="Manage Features">
                                            <i class="ri-list-settings-line"></i>
                                        </a>
                                        <a href="{{ route('show_package', $c->id) }}" class="btn btn-outline-primary border-glass" title="Edit">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                        <a href="{{ route('delete_package', $c->id) }}" class="btn btn-outline-danger border-glass" onclick="return confirm('Delete this package?')" title="Delete">
                                            <i class="ri-delete-bin-line"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($data->hasPages())
                <div class="p-4 border-top border-glass">
                    {{ $data->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .bg-dark-soft { background: rgba(0,0,0,0.2) !important; }
    .border-glass { border-color: rgba(255,255,255,0.05) !important; }
    .bg-primary-soft { background: rgba(59, 130, 246, 0.1); }
    .bg-info-soft { background: rgba(6, 182, 212, 0.1); }
    .table-hover tbody tr:hover { background: rgba(255,255,255,0.02); }
</style>
@endsection
