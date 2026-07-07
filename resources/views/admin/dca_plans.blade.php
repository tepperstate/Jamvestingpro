@extends('layouts.admin.app')
@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">DCA Plans</h1>
        <div class="d-flex gap-2">
            <form action="{{ route('admin.dca.sync_binance') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-warning">Auto-Populate (Binance)</button>
            </form>
            <button class="btn btn-primary" data-toggle="modal" data-target="#createModal">Create New Plan</button>
        </div>
    </div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Asset</th>
                            <th>Frequency</th>
                            <th>Spread Markup</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($plans as $plan)
                        <tr>
                            <td>{{ $plan->name }}</td>
                            <td>{{ $plan->asset }}</td>
                            <td>{{ ucfirst($plan->frequency) }}</td>
                            <td>{{ $plan->spread_markup }}</td>
                            <td>{{ ucfirst($plan->status) }}</td>
                            <td>
                                <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#editModal{{$plan->id}}">Edit</button>
                                <a href="{{ route('admin.dca.plans.delete', $plan->id) }}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this DCA plan?');">Delete</a>
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
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form action="{{ route('admin.dca.plans.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-content text-dark">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">Create DCA Plan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. BTC Auto-Invest">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Asset</label>
                        <input type="text" name="asset" class="form-control" required placeholder="e.g. BTC">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Frequency</label>
                        <select name="frequency" class="form-control" required>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="biweekly">Bi-weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Execution Hour (0-23)</label>
                        <input type="number" name="execution_hour" class="form-control" min="0" max="23" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Min Amount</label>
                        <input type="number" step="any" name="min_amount" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Max Amount</label>
                        <input type="number" step="any" name="max_amount" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Spread Markup (%)</label>
                        <input type="number" step="any" name="spread_markup" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Buffer % (Optional)</label>
                        <input type="number" step="any" name="buffer_percent" class="form-control" value="20">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Per Withdrawal % (Optional)</label>
                        <input type="number" step="any" name="per_withdrawal_percent" class="form-control" value="5">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Create Plan</button>
            </div>
        </div>
    </form>
  </div>
</div>

@foreach($plans as $plan)
<!-- Edit Modal -->
<div class="modal fade" id="editModal{{$plan->id}}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form action="{{ route('admin.dca.plans.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" value="{{ $plan->id }}">
        <div class="modal-content text-dark">
            <div class="modal-header">
                <h5 class="modal-title">Edit DCA Plan: {{ $plan->name }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $plan->name }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Asset</label>
                        <input type="text" name="asset" class="form-control" value="{{ $plan->asset }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Frequency</label>
                        <select name="frequency" class="form-control" required>
                            <option value="daily" {{ $plan->frequency == 'daily' ? 'selected' : '' }}>Daily</option>
                            <option value="weekly" {{ $plan->frequency == 'weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="biweekly" {{ $plan->frequency == 'biweekly' ? 'selected' : '' }}>Bi-weekly</option>
                            <option value="monthly" {{ $plan->frequency == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Execution Hour (0-23)</label>
                        <input type="number" name="execution_hour" class="form-control" value="{{ $plan->execution_hour }}" min="0" max="23" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Min Amount</label>
                        <input type="number" step="any" name="min_amount" class="form-control" value="{{ $plan->min_amount }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Max Amount</label>
                        <input type="number" step="any" name="max_amount" class="form-control" value="{{ $plan->max_amount }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Spread Markup (%)</label>
                        <input type="number" step="any" name="spread_markup" class="form-control" value="{{ $plan->spread_markup }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            <option value="active" {{ $plan->status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $plan->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </form>
  </div>
</div>
@endforeach
@endsection

