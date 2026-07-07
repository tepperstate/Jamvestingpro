@extends('layouts.user.app')
@section('title', 'ICO & Token Launchpad')

@section('content')
<div class="mobile-container pb-5 px-3 mt-3">
    <!-- Header -->
    <div class="mb-4 text-center">
        <h1 class="h3 font-weight-bold text-white mb-2" style="font-family: 'Outfit', sans-serif;">Launchpad</h1>
        <p class="text-secondary small mb-0">Invest in exclusive early-stage token offerings.</p>
    </div>

    <!-- Active Projects -->
    <h4 class="font-weight-bold text-white mb-3" style="font-family: 'Outfit', sans-serif;">Active Projects</h4>
    <div class="d-flex flex-column gap-4 mb-5">
        @forelse($projects as $project)
        <div class="glass-card-gold overflow-hidden" style="border-radius: 20px;">
            @if($project->image)
                <div style="height: 140px; width: 100%; position: relative;">
                    <img src="{{ asset($project->image) }}" alt="{{ $project->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to bottom, transparent, #0a0b0e);"></div>
                </div>
            @else
                <div class="d-flex align-items-center justify-content-center" style="height: 140px; background: rgba(153,0,0,0.1);">
                    <i class="ri-rocket-2-fill text-gold" style="font-size: 4rem;"></i>
                </div>
            @endif
            
            <div class="p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="text-white fw-bold mb-0">{{ $project->name }}</h5>
                    <span class="badge" style="background: rgba(153, 0, 0, 0.1); color: #990000;">{{ $project->symbol }}</span>
                </div>
                
                <p class="text-secondary mb-4" style="font-size: 0.75rem; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                    {{ $project->description }}
                </p>
                
                <div class="p-3 mb-4 rounded" style="background: rgba(0,0,0,0.3); border: 1px solid rgba(153,0,0,0.1);">
                    <div class="d-flex justify-content-between mb-2 pb-2 border-bottom" style="border-color: rgba(255,255,255,0.05) !important;">
                        <span class="small text-secondary">Price</span>
                        <strong class="text-gold small">${{ number_format($project->price_per_token, 4) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2 pb-2 border-bottom" style="border-color: rgba(255,255,255,0.05) !important;">
                        <span class="small text-secondary">Total Supply</span>
                        <strong class="text-white small">{{ number_format($project->total_supply) }} {{ $project->symbol }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small text-secondary">Raised</span>
                        <strong class="text-white small">${{ number_format($project->raised_amount) }}</strong>
                    </div>
                    
                    <div class="progress mt-2" style="height: 4px; background: rgba(255,255,255,0.05); border-radius: 2px;">
                        @php $progress = ($project->hard_cap > 0) ? ($project->raised_amount / $project->hard_cap) * 100 : 0; @endphp
                        <div class="progress-bar bg-gold" role="progressbar" style="width: {{ min(100, $progress) }}%;"></div>
                    </div>
                </div>
                
                <button class="btn btn-gold w-100 py-3 rounded-pill fw-bold shadow" data-toggle="modal" data-target="#participateModal{{ $project->id }}">
                    Participate
                </button>
            </div>
        </div>
        @empty
        <div class="glass-card-gold p-4 text-center" style="border-radius: 20px;">
            <i class="ri-rocket-line text-white-50 fs-1 d-block mb-3"></i>
            <p class="text-secondary small mb-0">No active launchpad projects available.</p>
        </div>
        @endforelse
    </div>

    <!-- Your Participations -->
    <h4 class="font-weight-bold text-white mb-3" style="font-family: 'Outfit', sans-serif;">Your Participations</h4>
    <div class="d-flex flex-column gap-3">
        @forelse($participations as $part)
        <div class="glass-card-gold p-3" style="border-radius: 16px;">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom" style="border-color: rgba(255,255,255,0.05) !important;">
                <div>
                    <h6 class="text-white font-weight-bold mb-0">{{ $part->project->name }}</h6>
                    <small class="text-gold" style="font-size: 0.7rem;">{{ $part->project->symbol }}</small>
                </div>
                <div>
                    @if($part->status == 'vesting')
                        <span class="badge" style="background: rgba(255, 193, 7, 0.1); color: #ffc107;">Vesting until {{ Carbon\Carbon::parse($part->vesting_end_date)->format('Y-m-d') }}</span>
                    @elseif($part->status == 'claimable')
                        <span class="badge" style="background: rgba(40, 167, 69, 0.1); color: #28a745;">Claimable</span>
                    @else
                        <span class="badge" style="background: rgba(255,255,255,0.1); color: #fff;">{{ ucfirst($part->status) }}</span>
                    @endif
                </div>
            </div>

            <div class="row g-2 mb-2">
                <div class="col-6">
                    <div class="small text-secondary" style="font-size: 0.65rem;">Invested</div>
                    <div class="text-white font-weight-bold" style="font-size: 0.85rem;">${{ number_format($part->amount_invested, 2) }}</div>
                </div>
                <div class="col-6 text-right">
                    <div class="small text-secondary" style="font-size: 0.65rem;">Current Value</div>
                    <div class="font-weight-bold {{ $part->pnl < 0 ? 'text-danger' : 'text-success' }}" style="font-size: 0.85rem;">
                        ${{ number_format($part->current_value, 2) }}
                    </div>
                </div>
            </div>

            <div class="pt-2">
                <div class="small text-secondary" style="font-size: 0.65rem;">Tokens Allocated</div>
                <div class="text-gold font-weight-bold" style="font-size: 0.85rem;">{{ number_format($part->tokens_allocated, 2) }}</div>
            </div>
        </div>
        @empty
        <div class="glass-card-gold p-4 text-center" style="border-radius: 16px;">
            <p class="text-secondary small mb-0">No participations yet.</p>
        </div>
        @endforelse
    </div>
</div>

@push('modals')
@foreach($projects as $project)
<div class="modal fade" id="participateModal{{ $project->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('user.launchpad.participate', $project->id) }}" method="POST">
            @csrf
            <div class="modal-content glass-card-gold" style="border: 1px solid rgba(153,0,0,0.2);">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-gold fw-bold">Participate in {{ $project->name }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body py-4">
                    <div class="d-flex justify-content-between align-items-center mb-4 p-3 rounded" style="background: rgba(0,0,0,0.4); border: 1px solid rgba(153,0,0,0.1);">
                        <span class="text-white-50">Token Price</span>
                        <strong class="text-gold" style="font-size: 18px;">${{ number_format($project->price_per_token, 4) }}</strong>
                    </div>

                    <div class="form-group mb-4">
                        <label class="text-white-50 mb-2 small">Investment Amount (USD)</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 text-gold" style="background: rgba(0,0,0,0.5); border-radius: 12px 0 0 12px;">$</span>
                            <input type="number" name="amount" class="form-control text-white border-0 shadow-none" required min="10" step="0.01" style="background: rgba(0,0,0,0.5); border-radius: 0 12px 12px 0;">
                        </div>
                    </div>

                    <div class="alert border-0 d-flex align-items-center p-3" style="background: rgba(153, 0, 0, 0.1); color: #990000; border-radius: 12px;">
                        <i class="ri-information-line fs-4 me-3"></i>
                        <div style="font-size: 0.75rem;">Tokens will be vested for <strong>{{ $project->vesting_days }} days</strong> after the sale ends.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-gold w-100 fw-bold py-3 rounded-pill">Confirm Purchase</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach
@endpush

<style>
    body { background-color: #0a0b0e; color: #fff; }
    .text-gold { color: #990000 !important; }
    .bg-gold { background-color: #990000 !important; }
    .glass-card-gold {
        background: rgba(16, 18, 27, 0.6);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(153, 0, 0, 0.15);
    }
    .btn-gold {
        background: linear-gradient(45deg, #990000, #f3e5ab);
        color: #0a0b0e;
        border: none;
    }
</style>
@endsection
