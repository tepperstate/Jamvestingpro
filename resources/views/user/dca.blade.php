@extends('layouts.user.app')
@section('content')
<div class="container-fluid mt-4">
    <h2>Auto-Invest (DCA)</h2>
    <p class="text-muted">Automate your crypto purchases with Dollar-Cost Averaging.</p>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="row">
        @forelse($plans ?? [] as $plan)
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <h5>{{ $plan->name }}</h5>
                    <div class="d-flex justify-content-between my-3">
                        <span class="text-muted">Asset</span>
                        <div>
                            <img src="{{ \App\Services\AssetLogoService::getLogoUrl($plan->asset, $plan->asset_type ?? 'crypto') }}" style="width: 20px; height: 20px; border-radius: 50%; margin-right: 5px;">
                            <strong>{{ $plan->asset }}</strong>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between my-2">
                        <span class="text-muted">Frequency</span>
                        <strong>{{ ucfirst($plan->frequency) }}</strong>
                    </div>
                    
                    <form action="{{ url('dca/subscribe') }}" method="POST" class="mt-4">
                        @csrf
                        <input type="hidden" name="dca_plan_id" value="{{ $plan->id }}">
                        <div class="input-group mb-3">
                            <input type="number" name="amount" class="form-control" placeholder="Amount per {{ $plan->frequency }}" required>
                            <button class="btn btn-primary" type="submit">Start Bot</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info">No active DCA plans available right now.</div>
        </div>
        @endforelse
    </div>
</div>
@endsection
