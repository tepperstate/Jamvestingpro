@extends('layouts.admin.app')
@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Liquidity Pools</h1>
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Manage Pools</h6>
            <div>
                <form action="{{ route('admin.liquidity.pools.sync_binance') }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-success btn-sm">Auto-Populate from Binance</button>
                </form>
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createModal">Create Pool</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Pair</th>
                            <th>Displayed TVL</th>
                            <th>Displayed APY</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pools as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->token_a }} / {{ $item->token_b }}</td>
                            <td>
                                <form action="{{ route('admin.liquidity.pools.update') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $item->id }}">
                                    $<input type="text" name="tvl" value="{{ $item->tvl }}" class="form-control form-control-sm" style="width:100px; display:inline">
                                    <button class="btn btn-sm btn-success"><i class="fa fa-save"></i></button>
                                </form>
                            </td>
                            <td>
                                <form action="{{ route('admin.liquidity.simulate_apy') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $item->id }}">
                                    <input type="text" name="apy" value="{{ $item->apy }}" class="form-control form-control-sm" style="width:70px; display:inline">%
                                    <button class="btn btn-sm btn-success"><i class="fa fa-save"></i></button>
                                </form>
                            </td>
                            <td>{{ ucfirst($item->status) }}</td>
                            <td>
                                <a href="{{ route('admin.liquidity.pools.delete', $item->id) }}" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.liquidity.pools.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create Pool</h5>
                </div>
                <div class="modal-body">
                    <input type="text" name="name" class="form-control mb-2" placeholder="Pool Name (e.g. BTC/USDT Vault)" required>
                    <input type="text" name="token_a" class="form-control mb-2" placeholder="Token A" required>
                    <input type="text" name="token_b" class="form-control mb-2" placeholder="Token B" required>
                    <input type="text" name="tvl" class="form-control mb-2" placeholder="Initial TVL (Fake)" required>
                    <input type="text" name="apy" class="form-control mb-2" placeholder="Displayed APY %" required>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

