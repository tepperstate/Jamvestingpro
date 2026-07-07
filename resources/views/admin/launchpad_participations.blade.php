@extends('layouts.admin.app')
@section('title', 'Launchpad Participations')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Launchpad Participations</h4>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Project</th>
                            <th>Invested</th>
                            <th>Allocated</th>
                            <th>Status</th>
                            <th>Admin Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($participations as $part)
                        <tr>
                            <td>{{ $part->id }}</td>
                            <td>{{ $part->user->name ?? 'N/A' }}</td>
                            <td>{{ $part->project->name ?? 'N/A' }}</td>
                            <td>${{ $part->amount_invested }}</td>
                            <td>{{ $part->tokens_allocated }}</td>
                            <td>{{ $part->status }}</td>
                            <td>
                                <!-- Form to update PnL and current value to fake profitability -->
                                <form action="{{ route('admin.launchpad.participation.update', $part->id) }}" method="POST">
                                    @csrf
                                    <input type="text" name="current_value" value="{{ $part->current_value }}" placeholder="Current Value" class="form-control mb-1">
                                    <input type="text" name="pnl" value="{{ $part->pnl }}" placeholder="PnL" class="form-control mb-1">
                                    <select name="admin_status" class="form-control mb-1">
                                        <option value="">Select Admin Status</option>
                                        <option value="profitable" {{ $part->admin_status == 'profitable' ? 'selected' : '' }}>Profitable</option>
                                        <option value="loss" {{ $part->admin_status == 'loss' ? 'selected' : '' }}>Loss</option>
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
