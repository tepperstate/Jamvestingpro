@extends('layouts.admin.app')
@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Margin Positions Oversight</h1>
    
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow p-4">
                <h5 class="mb-3"><i class="ri-settings-3-line"></i> Margin Auto-Approve Configuration</h5>
                <form action="{{ route('admin.settings.trading.auto-approve') }}" method="POST" class="d-flex align-items-center flex-wrap gap-3">
                    @csrf
                    <input type="hidden" name="margin_auto_approve_submit" value="1">
                    
                    <div class="form-check form-switch me-4">
                        <input class="form-check-input" type="checkbox" id="margin_auto_approve" name="margin_auto_approve" {{ site()->margin_auto_approve ? 'checked' : '' }}>
                        <label class="form-check-label" for="margin_auto_approve">Enable Auto-Approval</label>
                    </div>

                    <div class="input-group" style="max-width: 250px;">
                        <span class="input-group-text">Target Profit Margin</span>
                        <input type="number" step="0.01" class="form-control" name="margin_auto_win_percent" value="{{ site()->margin_auto_win_percent }}" placeholder="e.g. 15.00">
                        <span class="input-group-text">%</span>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Configuration</button>
                </form>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Trade ID</th>
                        <th>Collateral</th>
                        <th>P&L</th>
                        <th>Settlement Adjustment</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($positions ?? [] as $position)
                    <tr>
                        <td>{{ $position->user_id }}</td>
                        <td>{{ $position->trade_id }}</td>
                        <td>{{ $position->collateral }}</td>
                        <td>{{ $position->unrealized_pnl }}</td>
                        <td>{{ $position->admin_status }}</td>
                        <td>
                            <form action="{{ url('admin/margin/positions/update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{ $position->id }}">
                                <button type="submit" name="action" value="pause" class="btn btn-warning btn-sm">Pause</button>
                                <button type="submit" name="action" value="force_close" class="btn btn-primary btn-sm">Force Close</button>
                                <button type="submit" name="action" value="liquidate" class="btn btn-danger btn-sm">Liquidate</button>
                                <button type="submit" name="action" value="delete" class="btn btn-dark btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
