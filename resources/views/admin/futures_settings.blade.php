@extends('layouts.admin.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-white mb-1">Futures Settings</h4>
            <p class="text-secondary mb-0">Configure symbol margin rates, fees, and max leverage.</p>
        </div>
        <a onclick="history.back()" href="javascript:void(0)" class="btn btn-outline-light btn-sm rounded-pill px-3">
            <i class="ri-arrow-left-line me-1"></i> Back
        </a>
    </div>

    <div class="row">
        <!-- Add Setting Form -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="glass-card p-4 h-100">
                <h5 class="outfit font-weight-bold mb-4 text-white">Add/Update Symbol</h5>
                <form method="POST" action="{{ route('admin.futures.settings.store') }}">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="small text-secondary font-weight-bold text-uppercase mb-2">Symbol (e.g., BTCUSDT)</label>
                        <input type="text" name="symbol" class="form-control bg-dark-soft border-glass text-white py-2" placeholder="BTCUSDT" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="small text-secondary font-weight-bold text-uppercase mb-2">Max Leverage</label>
                        <div class="input-group">
                            <input type="number" name="max_leverage" class="form-control bg-dark-soft border-glass text-white py-2" placeholder="100" required>
                            <span class="input-group-text bg-dark-soft border-glass text-secondary">x</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="small text-secondary font-weight-bold text-uppercase mb-2">Maker Fee (%)</label>
                            <input type="number" step="0.001" name="maker_fee" class="form-control bg-dark-soft border-glass text-white py-2" placeholder="0.02" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-secondary font-weight-bold text-uppercase mb-2">Taker Fee (%)</label>
                            <input type="number" step="0.001" name="taker_fee" class="form-control bg-dark-soft border-glass text-white py-2" placeholder="0.04" required>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="small text-secondary font-weight-bold text-uppercase mb-2">Maintenance Margin (%)</label>
                            <input type="number" step="0.01" name="maintenance_margin" class="form-control bg-dark-soft border-glass text-white py-2" placeholder="0.5" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-secondary font-weight-bold text-uppercase mb-2">Initial Margin (%)</label>
                            <input type="number" step="0.01" name="initial_margin" class="form-control bg-dark-soft border-glass text-white py-2" placeholder="1.0" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 font-weight-bold rounded-3 shadow-lg">
                        <i class="ri-save-line me-2"></i> SAVE SETTING
                    </button>
                </form>
            </div>
        </div>

        <!-- Symbol List -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="glass-card p-0 h-100 overflow-hidden">
                <div class="p-4 border-bottom border-glass d-flex justify-content-between align-items-center">
                    <h5 class="outfit font-weight-bold mb-0 text-white">Configured Symbols</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-black-soft text-secondary small text-uppercase">
                            <tr>
                                <th class="border-0 px-4 py-3">Symbol</th>
                                <th class="border-0 py-3">Max Leverage</th>
                                <th class="border-0 py-3">Fees (M/T)</th>
                                <th class="border-0 py-3">Margin (I/M)</th>
                                <th class="border-0 px-4 py-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-white border-glass">
                            <!-- Loop symbol settings -->
                            <tr>
                                <td colspan="5" class="text-center py-4 text-secondary">No settings configured yet.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-dark-soft { background: rgba(0,0,0,0.2) !important; }
    .border-glass { border-color: rgba(255,255,255,0.05) !important; }
    .table-hover tbody tr:hover { background: rgba(255,255,255,0.02); }
</style>
@endsection
