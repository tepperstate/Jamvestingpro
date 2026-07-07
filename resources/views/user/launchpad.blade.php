@extends('layouts.user.app')
@section('title', 'ICO & Token Launchpad')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Active Launchpad Projects</h2>
    
    <div class="row">
        @foreach($projects as $project)
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0">
                @if($project->image)
                    <img src="{{ asset($project->image) }}" class="card-img-top" alt="{{ $project->name }}">
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $project->name }} ({{ $project->symbol }})</h5>
                    <p class="card-text text-muted">{{ Str::limit($project->description, 100) }}</p>
                    
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Price
                            <span>${{ number_format($project->price_per_token, 4) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total Supply
                            <span>{{ number_format($project->total_supply) }} {{ $project->symbol }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Raised
                            <span>${{ number_format($project->raised_amount) }}</span>
                        </li>
                    </ul>
                    
                    <div class="progress mb-3" style="height: 10px;">
                        @php
                            $progress = ($project->hard_cap > 0) ? ($project->raised_amount / $project->hard_cap) * 100 : 0;
                        @endphp
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ min(100, $progress) }}%"></div>
                    </div>
                    
                    <button class="btn btn-primary w-100" data-toggle="modal" data-target="#participateModal{{ $project->id }}">
                        Participate
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Participate Modal -->
        <div class="modal fade" id="participateModal{{ $project->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('user.launchpad.participate', $project->id) }}" method="POST">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Participate in {{ $project->name }}</h5>
                            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Price per token: <strong>${{ number_format($project->price_per_token, 4) }}</strong></p>
                            <div class="form-group mb-3">
                                <label>Investment Amount (USD)</label>
                                <input type="number" name="amount" class="form-control" required min="10" step="0.01">
                            </div>
                            <p class="text-muted small">Tokens will be vested for {{ $project->vesting_days }} days after the sale ends.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success w-100">Confirm Purchase</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <h3 class="mt-5 mb-3">Your Participations</h3>
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Project</th>
                            <th>Invested</th>
                            <th>Tokens</th>
                            <th>Current Value</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($participations as $part)
                        <tr>
                            <td>{{ $part->project->name }} ({{ $part->project->symbol }})</td>
                            <td>${{ number_format($part->amount_invested, 2) }}</td>
                            <td>{{ number_format($part->tokens_allocated, 2) }}</td>
                            <td class="{{ $part->pnl < 0 ? 'text-danger' : 'text-success' }}">
                                ${{ number_format($part->current_value, 2) }}
                            </td>
                            <td>
                                @if($part->status == 'vesting')
                                    <span class="badge bg-warning">Vesting until {{ Carbon\Carbon::parse($part->vesting_end_date)->format('Y-m-d') }}</span>
                                @elseif($part->status == 'claimable')
                                    <span class="badge bg-success">Claimable</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($part->status) }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">No participations yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

