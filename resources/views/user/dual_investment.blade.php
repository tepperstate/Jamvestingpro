@extends('layouts.user.app')
@section('content')
<style>
    /* Dark mode Glassmorphism core */
    body { background-color: #000000; color: #f8fafc; }
    .glass-card {
        background: rgba(30, 41, 59, 0.7);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 16px;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        color: #f8fafc;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .glass-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
    }
    .text-muted { color: #94a3b8 !important; }
    .text-primary { color: #3b82f6 !important; }
    .text-success { color: #ff3333 !important; }
    .bg-darker { background-color: rgba(0, 0, 0, 0.5); }
    .form-control-glass {
        background: rgba(0, 0, 0, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #f8fafc;
        border-radius: 8px;
    }
    .form-control-glass:focus {
        background: rgba(0, 0, 0, 0.8);
        border-color: #3b82f6;
        color: #ffffff;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.25);
    }
    .btn-primary { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border: none; color: #fff; }
    .btn-primary:hover { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: #fff; }
    
    .table-glass { color: #f8fafc; }
    .table-glass th { border-bottom: 1px solid rgba(255,255,255,0.1); color: #94a3b8; font-weight: 600; padding: 1rem; }
    .table-glass td { border-bottom: 1px solid rgba(255,255,255,0.05); vertical-align: middle; padding: 1rem; }
    .table-borderless tbody+tbody, .table-borderless td, .table-borderless th, .table-borderless thead th {
        border: 0;
    }

    .modern-badge {
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-block;
    }
    .badge-win { background: rgba(255, 51, 51, 0.15); color: #ff3333; }
    .badge-loss { background: rgba(239, 68, 68, 0.15); color: #ef4444; }
    .badge-active { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
    .badge-settled { background: rgba(148, 163, 184, 0.15); color: #94a3b8; }
</style>

<div class="container-fluid py-5">
    <div class="mb-5 text-center">
        <h2 class="fw-bold mb-2">Dual Investment</h2>
        <p class="text-muted">High-yield structured products. Predict price direction and earn high APY.</p>
    </div>
    
    @if(session('status'))
        <div class="alert alert-success glass-card border-0 text-success mb-4" style="background: rgba(255, 51, 51, 0.1);">
            {{ session('status') }}
        </div>
    @endif

    <div class="row g-4 mb-5">
        @forelse($products ?? [] as $product)
        <div class="col-xl-4 col-md-6">
            <div class="glass-card h-100 p-4 d-flex flex-column">
                <div class="text-center mb-4">
                    <h5 class="fw-bold mb-1">{{ $product->name }}</h5>
                    <span class="badge badge-active modern-badge mt-2">{{ $product->type ?? 'Dual Product' }}</span>
                </div>
                
                <div class="bg-darker p-3 rounded-3 mb-4">
                    <div class="d-flex justify-content-between my-2">
                        <span class="text-muted">Underlying</span>
                        <div class="d-flex align-items-center">
                            <img src="{{ \App\Services\AssetLogoService::getLogoUrl($product->underlying_asset, $product->asset_type ?? 'crypto') }}" style="width: 24px; height: 24px; border-radius: 50%; margin-right: 8px;">
                            <strong class="text-white">{{ $product->underlying_asset }}</strong>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between my-2">
                        <span class="text-muted">Strike Price</span>
                        <strong class="text-white">${{ number_format($product->strike_price, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between my-2">
                        <span class="text-muted">APY</span>
                        <strong class="text-success">{{ $product->apy }}%</strong>
                    </div>
                    <div class="d-flex justify-content-between my-2">
                        <span class="text-muted">Duration</span>
                        <strong class="text-white">{{ $product->duration_days }} Days</strong>
                    </div>
                </div>
                
                <div class="mt-auto">
                    <form action="{{ route('user.dual.buy') }}" method="POST">
                        @csrf
                        <input type="hidden" name="dual_product_id" value="{{ $product->id }}">
                        <div class="input-group">
                            <input type="number" step="any" name="amount" class="form-control form-control-glass" placeholder="Amount ({{ $product->deposit_asset ?? 'USD' }})" required>
                            <button class="btn btn-primary px-4 fw-bold" type="submit">Subscribe</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="glass-card p-5 text-center text-muted">
                <h5 class="mb-0">No active Dual Investment products available right now.</h5>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Active Subscriptions Table -->
    <h4 class="fw-bold mb-4 mt-5">My Subscriptions</h4>
    <div class="glass-card p-4">
        <div class="table-responsive">
            <table class="table table-borderless table-glass mb-0">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Amount</th>
                        <th>Expected Return</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscriptions ?? [] as $sub)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ optional($sub->dualInvestmentProduct)->name ?? 'Dual Investment' }}</div>
                            <small class="text-muted">{{ optional($sub->dualInvestmentProduct)->underlying_asset }}</small>
                        </td>
                        <td>
                            ${{ number_format($sub->amount, 2) }}
                        </td>
                        <td class="text-success">
                            ${{ number_format($sub->expected_return, 2) }}
                        </td>
                        <td>
                            @if($sub->status === 'active' || $sub->status === 'pending')
                                <span class="modern-badge badge-active">Active</span>
                            @elseif($sub->status === 'win')
                                <span class="modern-badge badge-win">Won</span>
                            @elseif($sub->status === 'loss')
                                <span class="modern-badge badge-loss">Lost</span>
                            @else
                                <span class="modern-badge badge-settled">{{ ucfirst($sub->status) }}</span>
                            @endif
                        </td>
                        <td>
                            {{ $sub->created_at->format('M d, Y H:i') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">No subscriptions found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
