@extends('layouts.admin.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-white mb-1">Edit Closed Futures Trade</h4>
            <p class="text-secondary mb-0">Modify details of a historical futures position.</p>
        </div>
        <a onclick="history.back()" href="javascript:void(0)" class="btn btn-outline-light btn-sm rounded-pill px-3">
            <i class="ri-arrow-left-line me-1"></i> Back
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="glass-card p-4">
                <form method="POST" action="{{ route('admin.futures.history.update', ['id' => $trade->id ?? 0]) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group mb-3">
                        <label class="small text-secondary font-weight-bold text-uppercase mb-2">Realized PNL</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark-soft border-glass text-secondary">$</span>
                            <input type="number" step="0.01" name="realized_pnl" class="form-control bg-dark-soft border-glass text-white py-2" value="{{ $trade->realized_pnl ?? '' }}" required>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="small text-secondary font-weight-bold text-uppercase mb-2">Close Price</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark-soft border-glass text-secondary">$</span>
                            <input type="number" step="0.00001" name="close_price" class="form-control bg-dark-soft border-glass text-white py-2" value="{{ $trade->close_price ?? '' }}" required>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="small text-secondary font-weight-bold text-uppercase mb-2">Status</label>
                        <select name="status" class="form-select bg-dark-soft border-glass text-white py-2" required>
                            <option value="closed" {{ isset($trade) && $trade->status == 'closed' ? 'selected' : '' }}>Closed</option>
                            <option value="liquidated" {{ isset($trade) && $trade->status == 'liquidated' ? 'selected' : '' }}>Liquidated</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 font-weight-bold rounded-3 shadow-lg">
                        <i class="ri-save-line me-2"></i> UPDATE TRADE
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-dark-soft { background: rgba(0,0,0,0.2) !important; }
    .border-glass { border-color: rgba(255,255,255,0.05) !important; }
    select option { background: #1a1a1a; color: #fff; }
</style>
@endsection
