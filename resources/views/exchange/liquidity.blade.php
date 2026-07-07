@extends('layouts.user.app')

@section('content')
<div class="content-body" style="background: #0f111a; min-height: 100vh; position: relative;">
    <div class="container-fluid" style="position: relative; z-index: 2;">
        
        <!-- Header -->
        <div class="row mb-5 align-items-center">
            <div class="col-lg-8">
                <h2 class="text-white fw-bold mb-2" style="font-family: 'Inter', sans-serif;">Liquidity Farming</h2>
                <p class="text-white-50">Provide liquidity to the AMM pools and earn high-yield APY from trading fees.</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <div class="p-3 rounded" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px);">
                    <span class="text-white-50 d-block mb-1">Total TVL</span>
                    <h3 class="text-white mb-0" style="color: #990000 !important;">$ {{ number_format($pools->sum('tvl'), 2) }}</h3>
                </div>
            </div>
        </div>

        <!-- Active Pools -->
        <h4 class="text-white mb-4" style="border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">Active Pools</h4>
        <div class="row">
            @forelse($pools as $pool)
            <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                <div class="card h-100 border-0" style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(20px); border-radius: 16px; border: 1px solid rgba(255,255,255,0.05) !important; box-shadow: 0 8px 32px rgba(0,0,0,0.3);">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; border: 1px solid rgba(153, 0, 0, 0.3);">
                                    <i class="ri-water-flash-fill fs-24" style="color: #990000;"></i>
                                </div>
                                <div class="ms-3">
                                    <h5 class="text-white mb-0 fw-bold">{{ $pool->name }}</h5>
                                    <small class="text-white-50">Fee Tier: {{ $pool->fee_tier }}%</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge" style="background: rgba(153, 0, 0, 0.1); color: #990000; border: 1px solid rgba(153, 0, 0, 0.2); padding: 8px 12px; font-size: 14px;">{{ $pool->apy }}% APY</span>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-6">
                                <p class="text-white-50 mb-1" style="font-size: 13px;">TVL</p>
                                <h6 class="text-white">${{ number_format($pool->tvl, 2) }}</h6>
                            </div>
                            <div class="col-6 text-end">
                                <p class="text-white-50 mb-1" style="font-size: 13px;">24h Vol</p>
                                <h6 class="text-white">${{ number_format($pool->volume_24h, 2) }}</h6>
                            </div>
                        </div>

                        <form action="{{ route('user.liquidity.deposit') }}" method="POST">
                            @csrf
                            <input type="hidden" name="pool_id" value="{{ $pool->id }}">
                            <div class="input-group mb-3">
                                <input type="number" name="amount" class="form-control text-white" placeholder="Amount (USD)" style="background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-right: none;" required min="{{ $pool->min_deposit }}">
                                <button class="btn fw-bold" type="submit" style="background: #990000; color: #000; border: none; padding: 10px 20px;">Deposit</button>
                            </div>
                            <small class="text-white-50 text-center d-block">Min: ${{ number_format($pool->min_deposit, 2) }}</small>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-dark text-center text-white-50 p-5" style="background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.1);">
                    <i class="ri-inbox-line fs-1 mb-3 d-block" style="color: #990000;"></i>
                    No active liquidity pools available at the moment.
                </div>
            </div>
            @endforelse
        </div>

        <!-- My Positions -->
        <h4 class="text-white mt-5 mb-4" style="border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">My Active Positions</h4>
        
        <div class="card border-0" style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(20px); border-radius: 16px; border: 1px solid rgba(255,255,255,0.05) !important;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover text-white align-middle mb-0" style="background: transparent;">
                        <thead style="background: rgba(0,0,0,0.2);">
                            <tr>
                                <th class="border-0 py-3 ps-4 text-white-50">Pool</th>
                                <th class="border-0 py-3 text-white-50">Initial Deposit</th>
                                <th class="border-0 py-3 text-white-50">Fees Earned</th>
                                <th class="border-0 py-3 text-white-50">Impermanent Loss</th>
                                <th class="border-0 py-3 text-white-50">Current Value</th>
                                <th class="border-0 py-3 text-white-50">Status</th>
                                <th class="border-0 py-3 pe-4 text-end text-white-50">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($positions as $pos)
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <td class="py-3 ps-4">
                                    <div class="d-flex align-items-center">
                                        <i class="ri-copper-coin-line fs-20 me-2" style="color: #990000;"></i>
                                        <span class="fw-bold">{{ $pos->pool->name ?? 'Unknown Pool' }}</span>
                                    </div>
                                </td>
                                <td class="py-3">${{ number_format($pos->amount_deposited, 2) }}</td>
                                <td class="py-3 text-success">+${{ number_format($pos->earned_fees + $pos->earned_rewards, 2) }}</td>
                                <td class="py-3 text-danger">-${{ number_format($pos->impermanent_loss, 2) }}</td>
                                <td class="py-3">
                                    <strong style="color: #990000;">${{ number_format($pos->current_value, 2) }}</strong>
                                </td>
                                <td class="py-3">
                                    <span class="badge rounded-pill" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);">{{ ucfirst($pos->status) }}</span>
                                </td>
                                <td class="py-3 pe-4 text-end">
                                    @if($pos->status === 'active')
                                    <form action="{{ route('user.liquidity.withdraw') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="position_id" value="{{ $pos->id }}">
                                        <button class="btn btn-sm btn-outline-light" style="border-radius: 6px;">Withdraw</button>
                                    </form>
                                    @else
                                    <button class="btn btn-sm btn-outline-secondary disabled" style="border-radius: 6px;">Done</button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-white-50">
                                    <i class="ri-folder-open-line fs-1 mb-2 d-block text-muted"></i>
                                    You don't have any active liquidity positions.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
    </div>
</div>

<style>
    .form-control::placeholder { color: rgba(255,255,255,0.4) !important; }
    .form-control:focus { box-shadow: none !important; border-color: #990000 !important; background: rgba(0,0,0,0.4) !important; color: white !important; }
    .table-hover tbody tr:hover { background: rgba(255,255,255,0.02) !important; }
</style>
@endsection
