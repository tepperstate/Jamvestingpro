@extends('layouts.admin.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-white mb-1">Futures Management</h4>
            <p class="text-secondary mb-0">View all open positions, liquidation heatmap, mark price controls, and mass liquidation.</p>
        </div>
        <a onclick="history.back()" href="javascript:void(0)" class="btn btn-outline-light btn-sm rounded-pill px-3">
            <i class="ri-arrow-left-line me-1"></i> Back
        </a>
    </div>
    
    <div class="row">
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="glass-card p-4 h-100">
                <h6 class="text-secondary text-uppercase font-weight-bold mb-2">Total Open Positions</h6>
                <h3 class="text-white font-weight-bold mb-0">0</h3>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="glass-card p-4 h-100">
                <h6 class="text-secondary text-uppercase font-weight-bold mb-2">Total Margin Used</h6>
                <h3 class="text-white font-weight-bold mb-0">$0.00</h3>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="glass-card p-4 h-100">
                <h6 class="text-secondary text-uppercase font-weight-bold mb-2">Unrealized PNL</h6>
                <h3 class="text-success font-weight-bold mb-0">+$0.00</h3>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="glass-card p-4 h-100">
                <h6 class="text-secondary text-uppercase font-weight-bold mb-2">Liquidation Risk</h6>
                <h3 class="text-danger font-weight-bold mb-0">Low</h3>
            </div>
        </div>
    </div>

    <!-- Mass Liquidation / Heatmap -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-4 mb-lg-0">
            <div class="glass-card p-4 h-100">
                <h5 class="outfit font-weight-bold mb-4 text-white">Liquidation Heatmap</h5>
                <div class="d-flex justify-content-center align-items-center h-75">
                    <p class="text-secondary">Heatmap visualization will be integrated here.</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="glass-card p-4 h-100">
                <h5 class="outfit font-weight-bold mb-4 text-white">Mass Liquidation</h5>
                <p class="text-secondary small mb-4">Force liquidate all positions below maintenance margin across the platform.</p>
                <form action="{{ route('admin.futures.mass_liquidate') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger w-100 py-3 font-weight-bold rounded-3 shadow-lg" onclick="return confirm('Are you sure you want to run mass liquidation? This action cannot be undone.')">
                        <i class="ri-alert-line me-2"></i> RUN MASS LIQUIDATION
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Open Positions -->
    <div class="glass-card p-0 overflow-hidden mb-4">
        <div class="p-4 border-bottom border-glass d-flex justify-content-between align-items-center">
            <h5 class="outfit font-weight-bold mb-0 text-white">All Open Positions</h5>
            <a href="{{ route('admin.futures.settings') }}" class="btn btn-sm btn-primary rounded-pill px-3">
                <i class="ri-settings-3-line me-1"></i> Settings
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-black-soft text-secondary small text-uppercase">
                    <tr>
                        <th class="border-0 px-4 py-3">User</th>
                        <th class="border-0 py-3">Symbol / Side</th>
                        <th class="border-0 py-3">Leverage / Margin</th>
                        <th class="border-0 py-3">Entry / Mark Price</th>
                        <th class="border-0 py-3">Liq. Price</th>
                        <th class="border-0 py-3 text-end px-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-white border-glass">
                    <!-- Positions Data Loop -->
                    <tr>
                        <td colspan="6" class="text-center py-4 text-secondary">No open positions found.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .bg-dark-soft { background: rgba(0,0,0,0.2) !important; }
    .border-glass { border-color: rgba(255,255,255,0.05) !important; }
    .bg-primary-soft { background: rgba(59, 130, 246, 0.1); }
    .table-hover tbody tr:hover { background: rgba(255,255,255,0.02); }
</style>
@endsection
