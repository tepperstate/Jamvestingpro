@extends('layouts.admin.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-white mb-1">Internal Wallet Inventory</h4>
            <p class="text-secondary mb-0">System-wide wallet types and asset definitions.</p>
        </div>
        <a onclick="history.back()" href="javascript:void(0)" class="btn btn-outline-light btn-sm rounded-pill px-3">
            <i class="ri-arrow-left-line me-1"></i> Back
        </a>
    </div>

    <div class="row">
        <!-- Add Wallet Form -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="glass-card p-4 h-100">
                <h5 class="outfit font-weight-bold mb-4 text-white">Define New Wallet</h5>
                <form method="POST" action="{{ route('wallet_post') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="">
                    
                    <div class="form-group mb-3">
                        <label class="small text-secondary font-weight-bold text-uppercase mb-2">Wallet Display Name</label>
                        <input type="text" name="name" class="form-control bg-dark-soft border-glass text-white py-2" placeholder="e.g. Ethereum Network" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="small text-secondary font-weight-bold text-uppercase mb-2">Ticker / Symbol</label>
                        <input type="text" name="symbol" class="form-control bg-dark-soft border-glass text-white py-2" placeholder="ETH" required>
                    </div>

                    <div class="form-group mb-4">
                        <label class="small text-secondary font-weight-bold text-uppercase mb-2">Visual Branding (Icon)</label>
                        <input type="file" name="image" class="form-control bg-dark-soft border-glass text-secondary py-1" required>
                        @error('logo') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 font-weight-bold rounded-3 shadow-lg">
                        <i class="ri-save-line me-2"></i> SAVE WALLET
                    </button>
                </form>
            </div>
        </div>

        <!-- Wallets List -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="glass-card p-0 h-100 overflow-hidden">
                <div class="p-4 border-bottom border-glass d-flex justify-content-between align-items-center bg-black-soft">
                    <h5 class="outfit font-weight-bold mb-0 text-white">Configured Wallets</h5>
                    <span class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill">{{ count($data) }} Wallets</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-black-soft text-secondary small text-uppercase">
                            <tr>
                                <th class="border-0 px-4 py-3">ID</th>
                                <th class="border-0 py-3">Wallet Spec</th>
                                <th class="border-0 py-3 text-center">Symbol</th>
                                <th class="border-0 px-4 py-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-white border-glass font-text">
                            @foreach ($data as $key => $value)
                            <tr class="border-glass">
                                <td class="px-4 text-secondary">{{ $key + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="wallet-icon-box me-3">
                                            <img src="{{ asset('storage/image/'.$value->image) }}" class="rounded shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">
                                        </div>
                                        <div class="font-weight-bold text-white">{{ $value->name }}</div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info-soft text-info">{{ $value->symbol }}</span>
                                </td>
                                <td class="px-4 text-end">
                                    <a href="{{ route('edit_wallet', $value->id) }}" class="btn btn-outline-primary btn-sm border-glass rounded-pill px-3">
                                        <i class="ri-edit-line me-1"></i> Edit
                                    </a>
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
    .bg-info-soft { background: rgba(6, 182, 212, 0.1); }
    .table-hover tbody tr:hover { background: rgba(255,255,255,0.02); }
    .wallet-icon-box { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 4px; border-radius: 10px; }
</style>
@endsection
