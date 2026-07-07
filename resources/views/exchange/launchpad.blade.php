@extends('layouts.user.app')
@section('content')

<div class="content-body" style="background: #0f111a; min-height: 100vh; position: relative;">
    <div class="container-fluid" style="position: relative; z-index: 2;">
        
        <!-- Header -->
        <div class="row mb-5 align-items-center">
            <div class="col-lg-8">
                <h2 class="text-white fw-bold mb-2" style="font-family: 'Inter', sans-serif;">IEO Launchpad</h2>
                <p class="text-white-50">Discover and invest in exclusive early-stage token offerings before they list on public exchanges.</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <div class="p-3 rounded" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px);">
                    <span class="text-white-50 d-block mb-1">My Total IEO Allocation</span>
                    <h3 class="text-white mb-0" style="color: #990000 !important;">$ {{ number_format($participations->sum('current_value'), 2) }}</h3>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <ul class="nav nav-pills mb-4" id="launchpadTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active fw-bold px-4 py-2 me-2" id="active-tab" data-toggle="pill" href="#active" role="tab" style="border-radius: 8px;">Active Sales</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link fw-bold px-4 py-2 me-2" id="completed-tab" data-toggle="pill" href="#completed" role="tab" style="border-radius: 8px;">Completed Sales</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link fw-bold px-4 py-2" id="allocations-tab" data-toggle="pill" href="#allocations" role="tab" style="border-radius: 8px;">My Allocations</a>
            </li>
        </ul>
        
        <div class="tab-content" id="launchpadTabsContent">
            
            <!-- ACTIVE TAB -->
            <div class="tab-pane fade show active" id="active" role="tabpanel">
                
                <!-- Desktop Table (d-none d-lg-block) -->
                <div class="d-none d-lg-block mb-4">
                    <div class="card border-0" style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(20px); border-radius: 16px; border: 1px solid rgba(255,255,255,0.05) !important;">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover text-white align-middle mb-0" style="background: transparent;">
                                    <thead style="background: rgba(0,0,0,0.2);">
                                        <tr>
                                            <th class="border-0 py-3 ps-4 text-white-50">Project</th>
                                            <th class="border-0 py-3 text-white-50">Price</th>
                                            <th class="border-0 py-3 text-white-50">Tokens for Sale</th>
                                            <th class="border-0 py-3 text-white-50" style="width: 250px;">Progress</th>
                                            <th class="border-0 py-3 text-white-50">Listing Date</th>
                                            <th class="border-0 py-3 pe-4 text-end text-white-50">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($projects as $project)
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                            <td class="py-3 ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-dark rounded me-3" style="width: 44px; height: 44px; border: 1px solid rgba(255,255,255,0.1); overflow: hidden;">
                                                        @if($project->image)
                                                            <img src="{{ asset($project->image) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                                        @else
                                                            <div class="d-flex h-100 align-items-center justify-content-center bg-secondary"><i class="ri-rocket-2-fill text-white-50"></i></div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <span class="fw-bold d-block">{{ $project->name }}</span>
                                                        <small class="badge mt-1" style="background: rgba(153, 0, 0, 0.1); border: 1px solid rgba(153, 0, 0, 0.3); color: #990000;">{{ $project->symbol }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3 fw-bold">${{ number_format($project->price_per_token, 4) }}</td>
                                            <td class="py-3">{{ number_format($project->tokens_for_sale) }}</td>
                                            <td class="py-3">
                                                <div class="d-flex justify-content-between mb-1" style="font-size: 11px;">
                                                    <span class="text-white-50">${{ number_format($project->raised_amount, 0) }} Raised</span>
                                                    <span class="text-white-50">${{ number_format($project->hard_cap, 0) }}</span>
                                                </div>
                                                <div class="progress" style="height: 6px; background: rgba(255,255,255,0.05); border-radius: 10px;">
                                                    @php $progress = ($project->hard_cap > 0) ? ($project->raised_amount / $project->hard_cap) * 100 : 0; @endphp
                                                    <div class="progress-bar" role="progressbar" style="width: {{ min(100, $progress) }}%; background: #990000; border-radius: 10px;"></div>
                                                </div>
                                            </td>
                                            <td class="py-3">{{ $project->listing_date ? $project->listing_date->format('M d, Y') : 'TBA' }}</td>
                                            <td class="py-3 pe-4 text-end">
                                                <button class="btn fw-bold px-4" data-toggle="modal" data-target="#participateModal{{ $project->id }}" style="background: rgba(153, 0, 0, 0.1); border: 1px solid rgba(153, 0, 0, 0.4); color: #990000; transition: all 0.2s;" onmouseover="this.style.background='#990000'; this.style.color='#000';" onmouseout="this.style.background='rgba(153, 0, 0, 0.1)'; this.style.color='#990000';">Participate</button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="6" class="text-center py-5 text-white-50">No active projects available.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mobile Cards (d-block d-lg-none) -->
                <div class="d-block d-lg-none">
                    <div class="row">
                        @forelse($projects as $project)
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0" style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(20px); border-radius: 16px; border: 1px solid rgba(255,255,255,0.05) !important; box-shadow: 0 8px 32px rgba(0,0,0,0.3); overflow: hidden;">
                                @if($project->image)
                                    <div style="height: 140px; width: 100%; overflow: hidden; position: relative; border-bottom: 1px solid rgba(255,255,255,0.05);">
                                        <img src="{{ asset($project->image) }}" alt="{{ $project->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to bottom, transparent, rgba(15, 17, 26, 1));"></div>
                                    </div>
                                @else
                                    <div class="d-flex align-items-center justify-content-center" style="height: 140px; background: rgba(0,0,0,0.2); border-bottom: 1px solid rgba(255,255,255,0.05);">
                                        <i class="ri-rocket-2-fill text-white-50" style="font-size: 4rem;"></i>
                                    </div>
                                @endif
                                <div class="card-body p-4 pt-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h4 class="text-white fw-bold mb-0">{{ $project->name }}</h4>
                                            <span class="badge" style="background: rgba(153, 0, 0, 0.1); border: 1px solid rgba(153, 0, 0, 0.3); color: #990000;">{{ $project->symbol }}</span>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge" style="background: rgba(255,255,255,0.1); color: white;">Price: ${{ number_format($project->price_per_token, 4) }}</span>
                                        </div>
                                    </div>
                                    <p class="text-white-50 mb-4" style="font-size: 14px; line-height: 1.5; height: 63px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                                        {{ $project->description }}
                                    </p>
                                    <div class="mb-4">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-white-50" style="font-size: 13px;">Raised: ${{ number_format($project->raised_amount, 0) }}</span>
                                            <span class="text-white-50" style="font-size: 13px;">Hard Cap: ${{ number_format($project->hard_cap, 0) }}</span>
                                        </div>
                                        <div class="progress" style="height: 6px; background: rgba(255,255,255,0.05); border-radius: 10px;">
                                            @php $progress = ($project->hard_cap > 0) ? ($project->raised_amount / $project->hard_cap) * 100 : 0; @endphp
                                            <div class="progress-bar" role="progressbar" style="width: {{ min(100, $progress) }}%; background: #990000;"></div>
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-6">
                                            <small class="text-white-50 d-block mb-1">Tokens for Sale</small>
                                            <span class="text-white fw-bold">{{ number_format($project->tokens_for_sale) }}</span>
                                        </div>
                                        <div class="col-6 text-end">
                                            <small class="text-white-50 d-block mb-1">Listing Date</small>
                                            <span class="text-white fw-bold">{{ $project->listing_date ? $project->listing_date->format('M d, Y') : 'TBA' }}</span>
                                        </div>
                                    </div>
                                    <button class="btn w-100 fw-bold" data-toggle="modal" data-target="#participateModal{{ $project->id }}" style="background: rgba(153, 0, 0, 0.1); border: 1px solid rgba(153, 0, 0, 0.4); color: #990000; padding: 12px; transition: all 0.3s ease;" onmouseover="this.style.background='#990000'; this.style.color='#000';" onmouseout="this.style.background='rgba(153, 0, 0, 0.1)'; this.style.color='#990000';">
                                        Participate Now
                                    </button>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="alert alert-dark text-center text-white-50 p-5" style="background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.1);">
                                <i class="ri-rocket-2-line fs-1 mb-3 d-block" style="color: #990000;"></i>
                                No upcoming or active launchpad projects available at the moment.
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- COMPLETED TAB -->
            <div class="tab-pane fade" id="completed" role="tabpanel">
                
                <!-- Desktop Table (d-none d-lg-block) -->
                <div class="d-none d-lg-block mb-4">
                    <div class="card border-0" style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(20px); border-radius: 16px; border: 1px solid rgba(255,255,255,0.05) !important;">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover text-white align-middle mb-0" style="background: transparent;">
                                    <thead style="background: rgba(0,0,0,0.2);">
                                        <tr>
                                            <th class="border-0 py-3 ps-4 text-white-50">Project</th>
                                            <th class="border-0 py-3 text-white-50">Sale Price</th>
                                            <th class="border-0 py-3 text-white-50">Tokens Sold</th>
                                            <th class="border-0 py-3 text-white-50" style="width: 250px;">Raised / Hard Cap</th>
                                            <th class="border-0 py-3 pe-4 text-end text-white-50">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($completedProjects as $project)
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                            <td class="py-3 ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-dark rounded me-3" style="width: 44px; height: 44px; border: 1px solid rgba(255,255,255,0.1); overflow: hidden; filter: grayscale(50%);">
                                                        @if($project->image)
                                                            <img src="{{ asset($project->image) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                                        @else
                                                            <div class="d-flex h-100 align-items-center justify-content-center bg-secondary"><i class="ri-rocket-2-fill text-white-50"></i></div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <span class="fw-bold d-block text-white-50">{{ $project->name }}</span>
                                                        <small class="badge mt-1 text-white-50" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1);">{{ $project->symbol }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3 fw-bold text-white-50">${{ number_format($project->price_per_token, 4) }}</td>
                                            <td class="py-3 text-white-50">{{ number_format($project->tokens_sold) }}</td>
                                            <td class="py-3">
                                                <div class="d-flex justify-content-between mb-1" style="font-size: 11px;">
                                                    <span class="text-white-50">${{ number_format($project->raised_amount, 0) }}</span>
                                                    <span class="text-white-50">${{ number_format($project->hard_cap, 0) }}</span>
                                                </div>
                                                <div class="progress" style="height: 6px; background: rgba(255,255,255,0.05); border-radius: 10px;">
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: 100%; border-radius: 10px;"></div>
                                                </div>
                                            </td>
                                            <td class="py-3 pe-4 text-end">
                                                <span class="badge bg-secondary text-white px-3 py-2">Finished</span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="5" class="text-center py-5 text-white-50">No completed projects.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mobile Cards (d-block d-lg-none) -->
                <div class="d-block d-lg-none">
                    <div class="row">
                        @forelse($completedProjects as $project)
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0" style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(20px); border-radius: 16px; border: 1px solid rgba(255,255,255,0.05) !important; box-shadow: 0 8px 32px rgba(0,0,0,0.3); overflow: hidden; opacity: 0.8;">
                                @if($project->image)
                                    <div style="height: 140px; width: 100%; overflow: hidden; position: relative; border-bottom: 1px solid rgba(255,255,255,0.05);">
                                        <img src="{{ $project->image }}" alt="{{ $project->name }}" style="width: 100%; height: 100%; object-fit: cover; filter: grayscale(50%);">
                                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to bottom, transparent, rgba(15, 17, 26, 1));"></div>
                                    </div>
                                @else
                                    <div class="d-flex align-items-center justify-content-center" style="height: 140px; background: rgba(0,0,0,0.2); border-bottom: 1px solid rgba(255,255,255,0.05);">
                                        <i class="ri-rocket-2-fill text-white-50" style="font-size: 4rem;"></i>
                                    </div>
                                @endif
                                <div class="card-body p-4 pt-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h4 class="text-white fw-bold mb-0">{{ $project->name }}</h4>
                                            <span class="badge" style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.3); color: #fff;">{{ $project->symbol }}</span>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-secondary text-white">Finished</span>
                                        </div>
                                    </div>
                                    <p class="text-white-50 mb-4" style="font-size: 14px; line-height: 1.5; height: 63px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                                        {{ $project->description }}
                                    </p>
                                    <div class="mb-4">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-white-50" style="font-size: 13px;">Raised: ${{ number_format($project->raised_amount, 0) }}</span>
                                            <span class="text-white-50" style="font-size: 13px;">Hard Cap: ${{ number_format($project->hard_cap, 0) }}</span>
                                        </div>
                                        <div class="progress" style="height: 6px; background: rgba(255,255,255,0.05); border-radius: 10px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: 100%;"></div>
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-6">
                                            <small class="text-white-50 d-block mb-1">Sale Price</small>
                                            <span class="text-white fw-bold">${{ number_format($project->price_per_token, 4) }}</span>
                                        </div>
                                        <div class="col-6 text-end">
                                            <small class="text-white-50 d-block mb-1">Tokens Sold</small>
                                            <span class="text-white fw-bold">{{ number_format($project->tokens_sold) }}</span>
                                        </div>
                                    </div>
                                    <button class="btn w-100 fw-bold" disabled style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: #fff; padding: 12px;">
                                        Sale Completed
                                    </button>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="alert alert-dark text-center text-white-50 p-4" style="background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.1);">
                                No completed launchpad projects.
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- ALLOCATIONS TAB -->
            <div class="tab-pane fade" id="allocations" role="tabpanel">
                <div class="card border-0 mb-4" style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(20px); border-radius: 16px; border: 1px solid rgba(255,255,255,0.05) !important;">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover text-white align-middle mb-0" style="background: transparent;">
                                <thead style="background: rgba(0,0,0,0.2);">
                                    <tr>
                                        <th class="border-0 py-3 ps-4 text-white-50">Project</th>
                                        <th class="border-0 py-3 text-white-50">Invested (USD)</th>
                                        <th class="border-0 py-3 text-white-50">Tokens Allocated</th>
                                        <th class="border-0 py-3 text-white-50">Current Value</th>
                                        <th class="border-0 py-3 text-white-50">Vesting Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($participations as $part)
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                        <td class="py-3 ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-dark rounded-circle me-3" style="width: 32px; height: 32px; border: 1px solid rgba(255,255,255,0.1); overflow: hidden;">
                                                    @if($part->project->image)
                                                        <img src="{{ asset($part->project->image) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                                    @endif
                                                </div>
                                                <div>
                                                    <span class="fw-bold d-block">{{ $part->project->name }}</span>
                                                    <small class="text-muted">{{ $part->project->symbol }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3">${{ number_format($part->amount_invested, 2) }}</td>
                                        <td class="py-3 fw-bold" style="color: #990000;">{{ number_format($part->tokens_allocated, 2) }}</td>
                                        <td class="py-3">
                                            <strong class="{{ $part->pnl < 0 ? 'text-danger' : 'text-success' }}">
                                                ${{ number_format($part->current_value, 2) }}
                                            </strong>
                                        </td>
                                        <td class="py-3">
                                            @if($part->status == 'vesting')
                                                <span class="badge" style="background: rgba(255, 193, 7, 0.1); border: 1px solid rgba(255, 193, 7, 0.3); color: #ffc107;">Vested until {{ \Carbon\Carbon::parse($part->vesting_end_date)->format('M d, Y') }}</span>
                                            @elseif($part->status == 'claimable')
                                                <span class="badge" style="background: rgba(40, 167, 69, 0.1); border: 1px solid rgba(40, 167, 69, 0.3); color: #28a745;">Claimable</span>
                                            @else
                                                <span class="badge" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);">{{ ucfirst($part->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-white-50">
                                            <i class="ri-wallet-3-line fs-1 mb-2 d-block text-muted"></i>
                                            You have not participated in any token launches.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>

    </div>
</div>

@push('modals')
<!-- Modals for Participation (placed outside to avoid duplication issues across responsive views) -->
@foreach($projects as $project)
<div class="modal fade" id="participateModal{{ $project->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('user.launchpad.participate', $project->id) }}" method="POST">
            @csrf
            <div class="modal-content" style="background: #151823; border: 1px solid rgba(255,255,255,0.1); border-radius: 16px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-white fw-bold">Commit Funds to {{ $project->name }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body py-4">
                    <div class="d-flex justify-content-between align-items-center mb-4 p-3 rounded" style="background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.05);">
                        <span class="text-white-50">Token Price</span>
                        <strong class="text-white" style="font-size: 18px;">${{ number_format($project->price_per_token, 4) }}</strong>
                    </div>
                    
                    <div class="form-group mb-4">
                        <label class="text-white-50 mb-2">Investment Amount (USD)</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 text-white-50" style="background: rgba(0,0,0,0.3); border-radius: 8px 0 0 8px;">$</span>
                            <input type="number" name="amount" class="form-control text-white border-0" required min="10" step="0.01" style="background: rgba(0,0,0,0.3); box-shadow: none;">
                        </div>
                        <small class="text-muted mt-2 d-block text-center">Minimum allocation is $10.00.</small>
                    </div>
                    
                    <div class="alert alert-warning border-0 d-flex align-items-center" style="background: rgba(153, 0, 0, 0.1); color: #990000;">
                        <i class="ri-information-line fs-20 me-2"></i>
                        <div style="font-size: 13px;">Tokens are vested for <strong>{{ $project->vesting_days }} days</strong> post-sale before they can be sold or withdrawn.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn w-100 fw-bold" style="background: #990000; color: #000; padding: 12px; border-radius: 8px;">Commit Investment</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach
@endpush

<style>
    .form-control:focus { box-shadow: none !important; border-color: #990000 !important; }
    .table-hover tbody tr:hover { background: rgba(255,255,255,0.02) !important; }
    
    .nav-pills .nav-link {
        background: rgba(255,255,255,0.03);
        color: rgba(255,255,255,0.5);
        border: 1px solid rgba(255,255,255,0.05);
        transition: all 0.2s;
    }
    .nav-pills .nav-link:hover {
        background: rgba(255,255,255,0.06);
        color: rgba(255,255,255,0.8);
    }
    .nav-pills .nav-link.active {
        background: rgba(153, 0, 0, 0.1) !important;
        color: #990000 !important;
        border: 1px solid rgba(153, 0, 0, 0.4);
    }
</style>
@endsection

