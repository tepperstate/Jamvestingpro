@extends('layouts.admin.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-white mb-1">Crypto Asset Management</h4>
            <p class="text-secondary mb-0">Manage cryptocurrencies available for purchase on the platform.</p>
        </div>
        <a onclick="history.back()" href="javascript:void(0)" class="btn btn-outline-light btn-sm rounded-pill px-3">
            <i class="ri-arrow-left-line me-1"></i> Back
        </a>
    </div>

    <div class="row">
        <!-- Add Crypto Form -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="glass-card p-4 h-100">
                <h5 class="outfit font-weight-bold mb-4 text-white">List New Crypto</h5>
                <form method="POST" action="{{ route('store_crypto') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="small text-secondary font-weight-bold text-uppercase mb-2">Asset Name</label>
                        <input type="text" name="name" class="form-control bg-dark-soft border-glass text-white py-2" placeholder="e.g. Bitcoin" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="small text-secondary font-weight-bold text-uppercase mb-2">External URL</label>
                        <input type="text" name="url" class="form-control bg-dark-soft border-glass text-white py-2" placeholder="https://coingecko.com/..." required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="small text-secondary font-weight-bold text-uppercase mb-2">Asset Icon</label>
                        <input type="file" name="image" class="form-control bg-dark-soft border-glass text-secondary py-1" required>
                        @error('image') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="row mb-4">
                        <div class="col-6">
                            <label class="small text-secondary font-weight-bold text-uppercase mb-2">Min Buy ($)</label>
                            <input type="number" name="min" class="form-control bg-dark-soft border-glass text-white py-2" placeholder="10" required>
                        </div>
                        <div class="col-6">
                            <label class="small text-secondary font-weight-bold text-uppercase mb-2">Max Buy ($)</label>
                            <input type="number" name="max" class="form-control bg-dark-soft border-glass text-white py-2" placeholder="10000" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 font-weight-bold rounded-3 shadow-lg">
                        <i class="ri-save-line me-2"></i> SAVE ASSET
                    </button>
                </form>
            </div>
        </div>

        <!-- Crypto List -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="glass-card p-0 h-100 overflow-hidden">
                <div class="p-4 border-bottom border-glass d-flex justify-content-between align-items-center bg-black-soft">
                    <h5 class="outfit font-weight-bold mb-0 text-white">Active Cryptos</h5>
                    <span class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill">{{ count($data) }} Assets</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-black-soft text-secondary small text-uppercase">
                            <tr>
                                <th class="border-0 px-4 py-3">Asset</th>
                                <th class="border-0 py-3">Limits (Min/Max)</th>
                                <th class="border-0 px-4 py-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-white border-glass font-text">
                            @foreach ($data as $val)
                            <tr class="border-glass">
                                <td class="px-4">
                                    <div class="d-flex align-items-center">
                                        <div class="asset-icon-box me-3">
                                            <img src="{{ asset('storage/image/'.$val->image) }}" class="rounded shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">
                                        </div>
                                        <div>
                                            <div class="font-weight-bold text-white mb-0">{{ $val->name }}</div>
                                            <a href="{{ $val->url }}" target="_blank" class="text-primary small text-decoration-none">View Source <i class="ri-external-link-line smaller"></i></a>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small text-secondary">Min: <span class="text-white">${{ number_format($val->min) }}</span></div>
                                    <div class="small text-secondary">Max: <span class="text-white">${{ number_format($val->max) }}</span></div>
                                </td>
                                <td class="px-4 text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('show_crypto', $val->id) }}" class="btn btn-outline-primary border-glass" title="Edit">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                        <a href="{{ route('delete_crypto', $val->id) }}" class="btn btn-outline-danger border-glass" onclick="return confirm('Delete this asset?')" title="Delete">
                                            <i class="ri-delete-bin-line"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-dark-soft { background: rgba(0,0,0,0.2) !important; }
    .bg-black-soft { background: rgba(0,0,0,0.3) !important; }
    .border-glass { border-color: rgba(255,255,255,0.05) !important; }
    .bg-primary-soft { background: rgba(59, 130, 246, 0.1); }
    .smaller { font-size: 0.75rem; }
    .table-hover tbody tr:hover { background: rgba(255,255,255,0.02); }
    .asset-icon-box { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 4px; border-radius: 10px; }
</style>
@endsection
