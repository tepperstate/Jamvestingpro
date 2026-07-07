@extends('layouts.admin.app')
@section('title', 'Loan Positions')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Loan Positions (User Loans)</h4>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Plan</th>
                            <th>Collateral</th>
                            <th>Loan Amount</th>
                            <th>Current LTV</th>
                            <th>Status</th>
                            <th>Admin Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($positions as $pos)
                        <tr>
                            <td>{{ $pos->id }}</td>
                            <td>{{ $pos->user->name ?? 'N/A' }}</td>
                            <td>{{ $pos->plan->name ?? 'N/A' }}</td>
                            <td>{{ $pos->collateral_amount }} (${{ $pos->collateral_value }})</td>
                            <td>${{ $pos->loan_amount }}</td>
                            <td>{{ $pos->current_ltv }}%</td>
                            <td>{{ $pos->status }} ({{ $pos->admin_status }})</td>
                            <td>
                                <!-- Form to fake liquidations or adjust LTV manually -->
                                <form action="{{ route('admin.loan.position.update', $pos->id) }}" method="POST">
                                    @csrf
                                    <input type="text" name="current_ltv" value="{{ $pos->current_ltv }}" placeholder="LTV %" class="form-control mb-1">
                                    <select name="admin_status" class="form-control mb-1">
                                        <option value="">Select Status</option>
                                        <option value="healthy" {{ $pos->admin_status == 'healthy' ? 'selected' : '' }}>Healthy</option>
                                        <option value="margin_call" {{ $pos->admin_status == 'margin_call' ? 'selected' : '' }}>Margin Call</option>
                                        <option value="liquidated" {{ $pos->admin_status == 'liquidated' ? 'selected' : '' }}>Liquidated</option>
                                    </select>
                                    <select name="status" class="form-control mb-1">
                                        <option value="active" {{ $pos->status == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="repaid" {{ $pos->status == 'repaid' ? 'selected' : '' }}>Repaid</option>
                                        <option value="liquidated" {{ $pos->status == 'liquidated' ? 'selected' : '' }}>Liquidated</option>
                                        <option value="defaulted" {{ $pos->status == 'defaulted' ? 'selected' : '' }}>Defaulted</option>
                                    </select>
                                    <button class="btn btn-sm btn-primary" type="submit">Update</button>
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
