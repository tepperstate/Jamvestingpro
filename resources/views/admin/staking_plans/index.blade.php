@extends('layouts.admin.app')
@section('content')

<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-5">
        <div>
            <h1 class="h2 mb-1 text-white gradient-text" style="font-weight: 800;">Staking Plans</h1>
            <p class="text-muted mb-0">Manage platform staking plans.</p>
        </div>
        <div class="d-flex gap-2">
            <form action="{{ route('admin.staking_plans.sync') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-info glass-panel border-0 px-4 py-2">
                    Auto-Populate (+10% APY)
                </button>
            </form>
            <button class="btn btn-primary glass-panel border-0 px-4 py-2" data-toggle="modal" data-target="#createPlan">
                Create Plan
            </button>
        </div>
    </div>

    <div class="bento-grid">
        @forelse($plans as $plan)
        <div class="glass-card bento-col-4 p-4">
            <h4 class="text-white">{{ $plan->name }} ({{ $plan->symbol }})</h4>
            <div class="mt-3">
                <div class="text-muted small">APY</div>
                <div class="text-success font-weight-bold">{{ $plan->apy_percentage }}%</div>
                
                <div class="text-muted small mt-2">Lock Days</div>
                <div class="text-white">{{ $plan->lock_days }} Days</div>
                
                <div class="text-muted small mt-2">Min - Max</div>
                <div class="text-white">${{ number_format($plan->min_amount) }} - ${{ number_format($plan->max_amount) }}</div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.staking_plans.delete', $plan->id) }}" class="btn btn-sm btn-danger glass-panel" onclick="return confirm('Delete?')">Delete</a>
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
            <h4 class="text-white mb-4">Create Staking Plan</h4>
            <form method="POST" action="{{ route('admin.staking_plans.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="text-white">Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="text-white">Symbol</label>
                    <input type="text" name="symbol" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="text-white">APY (%)</label>
                    <input type="number" name="apy_percentage" class="form-control" step="0.01" required>
                </div>
                <div class="mb-3">
                    <label class="text-white">Lock Days</label>
                    <input type="number" name="lock_days" class="form-control" required>
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

