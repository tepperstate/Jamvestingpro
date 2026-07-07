@extends('layouts.admin.app')
@section('content')

<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-5">
        <div>
            <h1 class="h2 mb-1 text-white gradient-text" style="font-weight: 800;">Student Plans</h1>
            <p class="text-muted mb-0">Manage platform student plans.</p>
        </div>
        <button class="btn btn-primary glass-panel border-0 px-4 py-2" data-toggle="modal" data-target="#createPlan">
            Create Plan
        </button>
    </div>

    <div class="bento-grid">
        @forelse($plans as $plan)
        <div class="glass-card bento-col-4 p-4">
            <h4 class="text-white">{{ $plan->name }}</h4>
            <div class="mt-3">
                <div class="text-muted small">Interest Rate (%)</div>
                <div class="text-success font-weight-bold">{{ $plan->interest_rate }}%</div>
                
                <div class="text-muted small mt-2">Duration (Months)</div>
                <div class="text-white">{{ $plan->duration_months }} Months</div>
                
                <div class="text-muted small mt-2">Min - Max Amount</div>
                <div class="text-white">${{ number_format($plan->min_amount) }} - ${{ number_format($plan->max_amount) }}</div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.student_plans.delete', $plan->id) }}" class="btn btn-sm btn-danger glass-panel" onclick="return confirm('Delete?')">Delete</a>
            </div>
        </div>
        @empty
        <div class="glass-card bento-col-12 p-5 text-center text-white">No plans created.</div>
        @endforelse
    </div>
</div>

<div class="modal fade" id="createPlan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-modal p-4">
            <h4 class="text-white mb-4">Create Student Plan</h4>
            <form method="POST" action="{{ route('admin.student_plans.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="text-white">Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="text-white">Interest Rate (%)</label>
                    <input type="number" name="interest_rate" class="form-control" step="0.01" required>
                </div>
                <div class="mb-3">
                    <label class="text-white">Duration (Months)</label>
                    <input type="number" name="duration_months" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="text-white">Min Amount</label>
                    <input type="number" name="min_amount" class="form-control" step="0.01" required>
                </div>
                <div class="mb-3">
                    <label class="text-white">Max Amount</label>
                    <input type="number" name="max_amount" class="form-control" step="0.01" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 mt-3">Create</button>
            </form>
        </div>
    </div>
</div>
@endsection

