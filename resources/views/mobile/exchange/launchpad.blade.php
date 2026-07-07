@extends('layouts.user.app')
@section('content')

<div class="mobile-container pb-5" style="background: #0f111a; min-height: 100vh;">
    <div class="px-3 pt-4">
        <!-- Header -->
        <div class="mb-4 text-center">
            <h2 class="text-white fw-bold mb-2" style="font-family: 'Outfit', sans-serif;">IEO Launchpad</h2>
            <p class="text-white-50 small mb-3">Invest in exclusive early-stage token offerings.</p>
            <div class="glass-card-gold p-3 rounded" style="border-radius: 16px;">
                <span class="text-gold d-block mb-1" style="font-size: 0.7rem;">My Total Allocation</span>
                <h3 class="text-white mb-0 font-weight-bold">$ {{ number_format($participations->sum('current_value'), 2) }}</h3>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <ul class="nav nav-pills nav-fill mb-4 p-1" id="launchpadTabs" role="tablist" style="background: rgba(255,255,255,0.05); border-radius: 12px;">
            <li class="nav-item" role="presentation">
                <a class="nav-link active small py-2" id="active-tab" data-toggle="pill" href="#active" role="tab" style="border-radius: 8px;">Active</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link small py-2" id="completed-tab" data-toggle="pill" href="#completed" role="tab" style="border-radius: 8px;">Finished</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link small py-2" id="allocations-tab" data-toggle="pill" href="#allocations" role="tab" style="border-radius: 8px;">Mine</a>
            </li>
        </ul>
        
        <div class="tab-content" id="launchpadTabsContent">
            
            <!-- ACTIVE TAB -->
            <div class="tab-pane fade show active" id="active" role="tabpanel">
                <div class="d-flex flex-column gap-4">
                    @forelse($projects as $project)
                    <div class="glass-card-gold overflow-hidden" style="border-radius: 20px;">
                        @if($project->image)
                            <div style="height: 120px; width: 100%; position: relative;">
                                <img src="{{ asset($project->image) }}" alt="{{ $project->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to bottom, transparent, #0a0b0e);"></div>
                            </div>
                        @else
                            <div class="d-flex align-items-center justify-content-center" style="height: 120px; background: rgba(153,0,0,0.1);">
                                <i class="ri-rocket-2-fill text-gold" style="font-size: 3rem;"></i>
                            </div>
                        @endif
                        
                        <div class="p-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="text-white fw-bold mb-0">{{ $project->name }}</h5>
                                    <span class="badge" style="background: rgba(153, 0, 0, 0.1); color: #990000; border: 1px solid rgba(153, 0, 0, 0.2);">{{ $project->symbol }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="badge bg-gold text-dark border-0">Price: ${{ number_format($project->price_per_token, 4) }}</span>
                                </div>
                            </div>
                            
                            <p class="text-white-50 mb-3" style="font-size: 0.75rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $project->description }}
                            </p>
                            
                            <div class="mb-3 p-2 rounded" style="background: rgba(0,0,0,0.3);">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-white-50" style="font-size: 0.7rem;">Raised: ${{ number_format($project->raised_amount, 0) }}</span>
                                    <span class="text-white-50" style="font-size: 0.7rem;">Cap: ${{ number_format($project->hard_cap, 0) }}</span>
                                </div>
                                <div class="progress" style="height: 4px; background: rgba(255,255,255,0.05); border-radius: 2px;">
                                    @php $progress = ($project->hard_cap > 0) ? ($project->raised_amount / $project->hard_cap) * 100 : 0; @endphp
                                    <div class="progress-bar bg-gold" role="progressbar" style="width: {{ min(100, $progress) }}%;"></div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <small class="text-white-50 d-block" style="font-size: 0.65rem;">Tokens for Sale</small>
                                    <span class="text-white fw-bold" style="font-size: 0.8rem;">{{ number_format($project->tokens_for_sale) }}</span>
                                </div>
                                <div class="text-right">
                                    <small class="text-white-50 d-block" style="font-size: 0.65rem;">Listing Date</small>
                                    <span class="text-white fw-bold" style="font-size: 0.8rem;">{{ $project->listing_date ? $project->listing_date->format('M d, Y') : 'TBA' }}</span>
                                </div>
                            </div>
                            
                            <button class="btn btn-gold w-100 py-2 rounded-pill fw-bold" data-toggle="modal" data-target="#participateModal{{ $project->id }}">
                                Participate
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="glass-card-gold p-4 text-center">
                        <i class="ri-rocket-2-line text-gold fs-1 mb-2 d-block"></i>
                        <p class="text-white-50 small mb-0">No active launchpad projects available.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- COMPLETED TAB -->
            <div class="tab-pane fade" id="completed" role="tabpanel">
                <div class="d-flex flex-column gap-4">
                    @forelse($completedProjects as $project)
                    <div class="glass-card-gold overflow-hidden" style="border-radius: 20px; opacity: 0.8;">
                        @if($project->image)
                            <div style="height: 100px; width: 100%; position: relative;">
                                <img src="{{ asset($project->image) }}" alt="{{ $project->name }}" style="width: 100%; height: 100%; object-fit: cover; filter: grayscale(100%);">
                                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to bottom, transparent, #0a0b0e);"></div>
                            </div>
                        @else
                            <div class="d-flex align-items-center justify-content-center" style="height: 100px; background: rgba(255,255,255,0.05);">
                                <i class="ri-rocket-2-fill text-white-50" style="font-size: 3rem;"></i>
                            </div>
                        @endif
                        
                        <div class="p-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="text-white fw-bold mb-0">{{ $project->name }}</h5>
                                    <span class="badge" style="background: rgba(255, 255, 255, 0.1); color: #fff;">{{ $project->symbol }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="badge bg-secondary text-white">Finished</span>
                                </div>
                            </div>
                            
                            <div class="mb-3 p-2 rounded" style="background: rgba(0,0,0,0.3);">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-white-50" style="font-size: 0.7rem;">Raised: ${{ number_format($project->raised_amount, 0) }}</span>
                                    <span class="text-white-50" style="font-size: 0.7rem;">Cap: ${{ number_format($project->hard_cap, 0) }}</span>
                                </div>
                                <div class="progress" style="height: 4px; background: rgba(255,255,255,0.05); border-radius: 2px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 100%;"></div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <div>
                                    <small class="text-white-50 d-block" style="font-size: 0.65rem;">Sale Price</small>
                                    <span class="text-white fw-bold" style="font-size: 0.8rem;">${{ number_format($project->price_per_token, 4) }}</span>
                                </div>
                                <div class="text-right">
                                    <small class="text-white-50 d-block" style="font-size: 0.65rem;">Tokens Sold</small>
                                    <span class="text-white fw-bold" style="font-size: 0.8rem;">{{ number_format($project->tokens_sold) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="glass-card-gold p-4 text-center">
                        <p class="text-white-50 small mb-0">No completed launchpad projects.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- ALLOCATIONS TAB -->
            <div class="tab-pane fade" id="allocations" role="tabpanel">
                <div class="d-flex flex-column gap-3">
                    @forelse($participations as $part)
                    <div class="glass-card-gold p-3" style="border-radius: 16px;">
                        <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom" style="border-color: rgba(255,255,255,0.05) !important;">
                            <div class="bg-dark rounded-circle" style="width: 40px; height: 40px; overflow: hidden; border: 1px solid rgba(153,0,0,0.3);">
                                @if($part->project->image)
                                    <img src="{{ asset($part->project->image) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @endif
                            </div>
                            <div>
                                <div class="fw-bold text-white">{{ $part->project->name }}</div>
                                <div class="small text-gold">{{ $part->project->symbol }}</div>
                            </div>
                            <div class="ms-auto text-end">
                                @if($part->status == 'vesting')
                                    <span class="badge" style="background: rgba(255, 193, 7, 0.1); color: #ffc107; font-size: 0.6rem;">Vested</span>
                                @elseif($part->status == 'claimable')
                                    <span class="badge" style="background: rgba(40, 167, 69, 0.1); color: #28a745; font-size: 0.6rem;">Claimable</span>
                                @else
                                    <span class="badge" style="background: rgba(255,255,255,0.1); font-size: 0.6rem;">{{ ucfirst($part->status) }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <div class="small text-secondary" style="font-size: 0.65rem;">Invested (USD)</div>
                                <div class="text-white font-weight-bold" style="font-size: 0.85rem;">${{ number_format($part->amount_invested, 2) }}</div>
                            </div>
                            <div class="col-6 text-end">
                                <div class="small text-secondary" style="font-size: 0.65rem;">Current Value</div>
                                <div class="font-weight-bold {{ $part->pnl < 0 ? 'text-danger' : 'text-success' }}" style="font-size: 0.85rem;">
                                    ${{ number_format($part->current_value, 2) }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="small text-secondary" style="font-size: 0.65rem;">Tokens</div>
                                <div class="text-gold font-weight-bold" style="font-size: 0.85rem;">{{ number_format($part->tokens_allocated, 2) }}</div>
                            </div>
                            @if($part->status == 'vesting')
                            <div class="text-end">
                                <div class="small text-secondary" style="font-size: 0.65rem;">Unlocks</div>
                                <div class="text-white-50" style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse($part->vesting_end_date)->format('M d, Y') }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="glass-card-gold p-4 text-center">
                        <i class="ri-wallet-3-line text-white-50 fs-1 mb-2 d-block"></i>
                        <p class="text-white-50 small mb-0">No launchpad allocations.</p>
                    </div>
                    @endforelse
                </div>
            </div>
            
        </div>
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
                    <h5 class="modal-title text-white fw-bold">Commit to {{ $project->name }}</h5>
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
                        <small class="text-muted mt-2 d-block text-center" style="font-size: 0.7rem;">Minimum allocation is $10.00.</small>
                    </div>
                    
                    <div class="alert border-0 d-flex align-items-center p-3" style="background: rgba(153, 0, 0, 0.1); color: #990000; border-radius: 12px;">
                        <i class="ri-information-line fs-4 me-3"></i>
                        <div style="font-size: 0.75rem;">Tokens are vested for <strong>{{ $project->vesting_days }} days</strong> post-sale.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-gold w-100 fw-bold py-3 rounded-pill">Commit Investment</button>
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
    .nav-pills .nav-link { color: rgba(255,255,255,0.5); }
    .nav-pills .nav-link.active { background: rgba(153, 0, 0, 0.1) !important; color: #990000 !important; }
</style>
@endsection
