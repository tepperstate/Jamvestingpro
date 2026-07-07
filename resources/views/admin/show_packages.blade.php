@extends('layouts.admin.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-white mb-1">Edit Investment Package</h4>
            <p class="text-secondary mb-0">Modify parameters for the <span class="text-primary font-weight-bold">{{ $data->name }}</span> tier.</p>
        </div>
        <a onclick="history.back()" href="javascript:void(0)" class="btn btn-outline-light btn-sm rounded-pill px-3">
            <i class="ri-arrow-left-line me-1"></i> Back
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 mb-4">
            <div class="glass-card shadow-lg p-0 overflow-hidden">
                <div class="p-4 border-bottom border-glass bg-black-soft">
                    <h5 class="outfit font-weight-bold mb-0 text-white">Package Configuration</h5>
                </div>
                <div class="p-4">
                    <form method="POST" action="{{ route('edit_package') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="{{ $data->id }}">
                        
                        <div class="row g-4">
                            <div class="col-md-12">
                                <label class="small text-secondary font-weight-bold text-uppercase mb-2">Package Name</label>
                                <input type="text" name="name" class="form-control bg-dark-soft border-glass text-white py-3 form-control-lg" value="{{ $data->name }}" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="small text-secondary font-weight-bold text-uppercase mb-2">Min Investment Amount ($)</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-dark-soft border-glass text-secondary">$</span>
                                    <input type="number" name="amount" class="form-control bg-dark-soft border-glass text-white" value="{{ $data->amount }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="small text-secondary font-weight-bold text-uppercase mb-2">Auto-Upgrade Threshold ($)</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-dark-soft border-glass text-secondary">$</span>
                                    <input type="number" name="min_deposit" class="form-control bg-dark-soft border-glass text-white" value="{{ $data->min_deposit ?? $data->amount }}">
                                </div>
                            </div>



                            <div class="col-md-6">
                                <label class="small text-secondary font-weight-bold text-uppercase mb-2">Return Rate (%)</label>
                                <div class="input-group input-group-lg">
                                    <input type="number" step="0.01" name="perc" class="form-control bg-dark-soft border-glass text-white" value="{{ $data->perc }}" required>
                                    <span class="input-group-text bg-dark-soft border-glass text-secondary">%</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="small text-secondary font-weight-bold text-uppercase mb-2">Duration (Days)</label>
                                <div class="input-group input-group-lg">
                                    <input type="number" name="day" class="form-control bg-dark-soft border-glass text-white" value="{{ $data->day }}" required>
                                    <span class="input-group-text bg-dark-soft border-glass text-secondary">Days</span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="small text-secondary font-weight-bold text-uppercase mb-2">Total Trades</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-dark-soft border-glass text-secondary">#</span>
                                    <input type="number" name="trade" class="form-control bg-dark-soft border-glass text-white" value="{{ $data->trade }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="small text-secondary font-weight-bold text-uppercase mb-2">Daily Limit</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-dark-soft border-glass text-secondary">#</span>
                                    <input type="number" name="daily_trade" class="form-control bg-dark-soft border-glass text-white" value="{{ $data->daily_trade }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="small text-secondary font-weight-bold text-uppercase mb-2">Weekly Limit</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-dark-soft border-glass text-secondary">#</span>
                                    <input type="number" name="weekly_trade" class="form-control bg-dark-soft border-glass text-white" value="{{ $data->weekly_trade }}">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="small text-secondary font-weight-bold text-uppercase mb-2">Tier Features</label>
                                <div class="row g-2">
                                    <div class="col-md-3 col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="features[]" value="basic_trading" id="f_basic" {{ in_array('basic_trading', $data->features ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label text-white small" for="f_basic">Basic Trading</label>
                                        </div>
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="features[]" value="bot" id="f_bot" {{ in_array('bot', $data->features ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label text-white small" for="f_bot">Bot Trading</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="features[]" value="high_leverage" id="f_leverage" {{ in_array('high_leverage', $data->features ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label text-white small" for="f_leverage">High Leverage</label>
                                        </div>
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="features[]" value="copy_trading" id="f_copy" {{ in_array('copy_trading', $data->features ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label text-white small" for="f_copy">CopyTrader™</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="features[]" value="vip_stocks" id="f_vip" {{ in_array('vip_stocks', $data->features ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label text-white small" for="f_vip">VIP Stocks</label>
                                        </div>
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="features[]" value="high_yield" id="f_yield" {{ in_array('high_yield', $data->features ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label text-white small" for="f_yield">High Yield Earn</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="features[]" value="mutual_funds" id="f_mutual" {{ in_array('mutual_funds', $data->features ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label text-white small" for="f_mutual">Mutual Funds</label>
                                        </div>
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="features[]" value="signal" id="f_signal" {{ in_array('signal', $data->features ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label text-white small" for="f_signal">Discover (Signals)</label>
                                        </div>
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="features[]" value="advanced_controls" id="f_advanced" {{ in_array('advanced_controls', $data->features ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label text-white small" for="f_advanced">Advanced Controls</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mt-4 pt-2">
                                <button type="submit" class="btn btn-primary btn-lg w-100 py-3 font-weight-bold shadow-lg text-uppercase tracking-wider">
                                    <i class="ri-check-double-line me-2"></i> Update Package Settings
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-dark-soft { background: rgba(0,0,0,0.2) !important; }
    .bg-black-soft { background: rgba(0,0,0,0.3) !important; }
    .border-glass { border-color: rgba(255,255,255,0.05) !important; }
    .tracking-wider { letter-spacing: 0.05em; }
</style>
@endsection
