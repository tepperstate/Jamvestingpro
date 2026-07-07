@extends('layouts.user.app')
@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 mb-4">
                <h4 class="card-title">Liquidity Pools</h4>
                <p class="text-muted">Provide liquidity to earn trading fees and yield farm rewards.</p>
            </div>
            
            @foreach($pools as $pool)
            <div class="col-xl-4 col-md-6">
                <div class="card bg-dark text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 text-white">{{ $pool->name }}</h5>
                            <span class="badge badge-success">{{ $pool->apy }}% APY</span>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <span class="text-muted d-block">Total Value Locked</span>
                                <strong>${{ number_format($pool->tvl, 2) }}</strong>
                            </div>
                            <div class="col-6 text-right">
                                <span class="text-muted d-block">24h Volume</span>
                                <strong>${{ number_format($pool->volume_24h, 2) }}</strong>
                            </div>
                        </div>
                        <form action="{{ route('user.liquidity.deposit') }}" method="POST">
                            @csrf
                            <input type="hidden" name="pool_id" value="{{ $pool->id }}">
                            <div class="input-group mb-3">
                                <input type="number" name="amount" class="form-control" placeholder="Amount (USD)">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">Add Liquidity</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <h4 class="mt-4 mb-3">Your Liquidity Positions</h4>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Pool</th>
                                <th>Deposited</th>
                                <th>Earned</th>
                                <th>Current Value</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($positions as $pos)
                            <tr>
                                <td>{{ $pos->pool->name }}</td>
                                <td>${{ number_format($pos->amount_deposited, 2) }}</td>
                                <td class="text-success">+${{ number_format($pos->earned_fees + $pos->earned_rewards, 2) }}</td>
                                <td><strong>${{ number_format($pos->current_value, 2) }}</strong></td>
                                <td><span class="badge badge-light">{{ ucfirst($pos->status) }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No active positions.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
