@extends('layouts.user.app')
@section('title', 'Crypto Loans')

@section('content')
<div class="container mt-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <h2 class="text-white">Crypto Loans</h2>
            <p class="text-muted">Use your crypto as collateral to borrow stablecoins without selling your assets.</p>
        </div>
    </div>

    <h4 class="mb-3 text-white">Available Loan Plans</h4>
    <div class="row">
        @foreach($plans as $plan)
        <div class="col-md-4 mb-4">
            <div class="card glass-card h-100 border-0">
                <div class="card-body p-4">
                    <h5 class="card-title text-primary mb-4">{{ $plan->name }}</h5>
                    <div class="d-flex justify-content-between mb-4">
                        <div>
                            <small class="text-muted d-block text-uppercase mb-1" style="font-size: 0.75rem;">Collateral</small>
                            <div class="d-flex align-items-center">
                                <img src="{{ \App\Services\AssetLogoService::getLogoUrl($plan->collateral_asset, $plan->asset_type ?? 'crypto') }}" style="width: 24px; height: 24px; border-radius: 50%; margin-right: 8px;">
                                <strong class="text-white fs-5">{{ $plan->collateral_asset }}</strong>
                            </div>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block text-uppercase mb-1" style="font-size: 0.75rem;">Borrow</small>
                            <strong class="text-white fs-5">{{ $plan->loan_asset }}</strong>
                        </div>
                    </div>
                    
                    <ul class="list-unstyled mb-4">
                        <li class="d-flex justify-content-between mb-3 border-bottom border-secondary pb-2">
                            <span class="text-muted">Max LTV</span>
                            <span class="text-success fw-bold">{{ $plan->max_ltv }}%</span>
                        </li>
                        <li class="d-flex justify-content-between mb-3 border-bottom border-secondary pb-2">
                            <span class="text-muted">Daily Interest</span>
                            <span class="text-danger fw-bold">{{ $plan->interest_rate_daily }}%</span>
                        </li>
                        <li class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Term</span>
                            <span class="text-white fw-bold">{{ $plan->duration_days }} days</span>
                        </li>
                    </ul>

                    <button class="btn btn-outline-primary w-100 py-2" data-toggle="modal" data-target="#borrowModal{{ $plan->id }}">
                        Borrow Now
                    </button>
                </div>
            </div>
        </div>

        <!-- Borrow Modal -->
        <div class="modal fade" id="borrowModal{{ $plan->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('user.loans.borrow', $plan->id) }}" method="POST" class="w-100">
                    @csrf
                    <div class="modal-content glass-card border-0 text-white">
                        <div class="modal-header border-bottom border-secondary px-4 py-3">
                            <h5 class="modal-title text-primary">Borrow {{ $plan->loan_asset }} against {{ $plan->collateral_asset }}</h5>
                            <button type="button" class="btn-close btn-close-white" data-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body px-4 py-4">
                            <div class="form-group mb-4">
                                <label class="text-muted mb-2">Collateral Amount ({{ $plan->collateral_asset }})</label>
                                <input type="number" name="collateral_amount" class="form-control bg-dark text-white border-secondary px-3 py-2" required step="0.00000001">
                            </div>
                            <div class="form-group mb-4">
                                <label class="text-muted mb-2">Loan Amount ({{ $plan->loan_asset }})</label>
                                <input type="number" name="loan_amount" class="form-control bg-dark text-white border-secondary px-3 py-2" required step="0.01">
                            </div>
                            <div class="alert alert-warning small border-0 bg-dark text-warning mb-0">
                                <i class="fas fa-info-circle me-1"></i> Ensure your requested loan amount does not exceed the maximum LTV of <strong>{{ $plan->max_ltv }}%</strong>. If your LTV reaches <strong>{{ $plan->liquidation_ltv }}%</strong>, your collateral will be liquidated.
                            </div>
                        </div>
                        <div class="modal-footer border-top border-secondary px-4 py-3">
                            <button type="submit" class="btn btn-primary w-100 py-2">Confirm Loan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <h4 class="mt-5 mb-4 text-white">Your Active Loans</h4>
    <div class="card glass-card border-0 mb-5">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-borderless text-white mb-0 align-middle">
                    <thead class="border-bottom border-secondary text-muted">
                        <tr>
                            <th class="fw-normal py-3 ps-4 text-uppercase" style="font-size: 0.8rem;">ID</th>
                            <th class="fw-normal py-3 text-uppercase" style="font-size: 0.8rem;">Collateral</th>
                            <th class="fw-normal py-3 text-uppercase" style="font-size: 0.8rem;">Borrowed</th>
                            <th class="fw-normal py-3 text-uppercase" style="font-size: 0.8rem;">LTV</th>
                            <th class="fw-normal py-3 text-uppercase" style="font-size: 0.8rem;">Interest</th>
                            <th class="fw-normal py-3 text-uppercase" style="font-size: 0.8rem;">Balance</th>
                            <th class="fw-normal py-3 pe-4 text-uppercase" style="font-size: 0.8rem;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($positions as $pos)
                        <tr class="border-bottom border-secondary">
                            <td class="py-3 ps-4 text-muted">#{{ $pos->loan_id }}</td>
                            <td class="py-3">{{ rtrim(rtrim(sprintf('%.8f', $pos->collateral_amount), '0'), '.') }} <span class="text-muted ms-1">{{ $pos->plan->collateral_asset }}</span></td>
                            <td class="py-3">${{ number_format($pos->loan_amount, 2) }}</td>
                            <td class="py-3">
                                <span class="{{ $pos->current_ltv >= $pos->plan->liquidation_ltv ? 'text-danger fw-bold' : 'text-success' }}">
                                    {{ $pos->current_ltv }}%
                                </span>
                            </td>
                            <td class="py-3 text-warning">${{ number_format($pos->interest_accrued, 2) }}</td>
                            <td class="py-3 text-white fw-bold">${{ number_format($pos->remaining_balance, 2) }}</td>
                            <td class="py-3 pe-4">
                                @if($pos->status == 'liquidated')
                                    <span class="badge bg-danger-subtle text-danger border border-danger px-2 py-1">Liquidated</span>
                                @elseif($pos->status == 'repaid')
                                    <span class="badge bg-success-subtle text-success border border-success px-2 py-1">Repaid</span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary border border-primary px-2 py-1">Active</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="mb-3"><i class="fas fa-wallet fa-3x opacity-25"></i></div>
                                No active loans found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection


