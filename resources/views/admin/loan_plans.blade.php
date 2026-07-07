@extends('layouts.admin.app')
@section('title', 'Loan Plans')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Loan Plans</h4>
                <form action="{{ route('admin.loan.sync_binance') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-warning">Auto-Populate (Binance)</button>
                </form>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Collateral Asset</th>
                            <th>Loan Asset</th>
                            <th>Max LTV</th>
                            <th>Liq. LTV</th>
                            <th>Col. Price (Oracle)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($plans as $plan)
                        <tr>
                            <td>{{ $plan->id }}</td>
                            <td>{{ $plan->name }}</td>
                            <td>{{ $plan->collateral_asset }}</td>
                            <td>{{ $plan->loan_asset }}</td>
                            <td>{{ $plan->max_ltv }}%</td>
                            <td>{{ $plan->liquidation_ltv }}%</td>
                            <td>${{ $plan->collateral_price }}</td>
                            <td>
                                <!-- Form to rig the collateral price or liquidation LTV -->
                                <form action="{{ route('admin.loans.plans.update', $plan->id) }}" method="POST">
                                    @csrf
                                    <input type="text" name="collateral_price" value="{{ $plan->collateral_price }}" placeholder="Rig Price" class="form-control mb-1">
                                    <input type="text" name="liquidation_ltv" value="{{ $plan->liquidation_ltv }}" placeholder="Liq LTV" class="form-control mb-1">
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
