@extends('layouts.admin.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-white mb-1">Trading Bot Architect</h4>
            <p class="text-secondary mb-0">Configure and deploy automated high-frequency trading agents.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
             <div class="badge bg-primary-soft text-primary px-4 py-2 rounded-pill border-glass">
                <span class="small font-weight-bold">ALGORITHMIC HUB ACTIVE</span>
            </div>
            <button onclick="history.back()" class="btn btn-outline-light btn-sm rounded-pill px-4 border-glass font-weight-bold">
                <i class="ri-arrow-left-line me-1"></i> Back
            </button>
        </div>
    </div>

    <!-- Bot Creation Form -->
    <div class="glass-card mb-4 shadow-2xl">
        <div class="p-4 border-bottom border-glass bg-black-soft d-flex align-items-center">
            <div class="icon-box bg-primary-soft rounded-pill p-3 me-3">
                <i class="ri-robot-2-line h4 mb-0 text-primary"></i>
            </div>
            <div>
                <h5 class="outfit font-weight-bold text-white mb-0">Start New Agent</h5>
                <p class="text-secondary small mb-0 opacity-75">Define operational parameters for the trading algorithm.</p>
            </div>
        </div>
        <div class="p-4">
            <form method="post" action="{{route('addBot')}}" id="submit" enctype="multipart/form-data">
                @csrf
                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="small text-secondary font-weight-bold text-uppercase mb-2 tracking-wider">Bot Identifier</label>
                        <input type="text" class="form-control bg-dark-soft border-glass text-white py-3 rounded-3" name="name" placeholder="e.g. ALPHA-V1" required>
                    </div>
                    <div class="col-md-4">
                        <label class="small text-secondary font-weight-bold text-uppercase mb-2 tracking-wider">Visual Avatar</label>
                        <input type="file" class="form-control bg-dark-soft border-glass text-white py-2 rounded-3" name="image">
                        @error('image')<small class="text-danger mt-1 d-block">{{$message}}</small>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="small text-secondary font-weight-bold text-uppercase mb-2 tracking-wider">Acquisition Cost ($)</label>
                        <input type="text" class="form-control bg-dark-soft border-glass text-white py-3 rounded-3" name="amount" placeholder="0.00" required>
                    </div>
                    <div class="col-md-4">
                        <label class="small text-secondary font-weight-bold text-uppercase mb-2 tracking-wider">Buffer Percentage (%)</label>
                        <input type="number" step="0.01" class="form-control bg-dark-soft border-glass text-white py-3 rounded-3" name="buffer_percent" placeholder="20.00" value="20" required>
                    </div>
                    <div class="col-md-4">
                        <label class="small text-secondary font-weight-bold text-uppercase mb-2 tracking-wider">Per Withdrawal (%)</label>
                        <input type="number" step="0.01" class="form-control bg-dark-soft border-glass text-white py-3 rounded-3" name="per_withdrawal_percent" placeholder="5.00" value="5" required>
                    </div>
                    <div class="col-md-4">
                        <label class="small text-secondary font-weight-bold text-uppercase mb-2 tracking-wider">Min Operation Limit</label>
                        <input type="text" class="form-control bg-dark-soft border-glass text-white py-3 rounded-3" name="min" placeholder="100" required>
                    </div>
                    <div class="col-md-4">
                        <label class="small text-secondary font-weight-bold text-uppercase mb-2 tracking-wider">Max Operation Limit</label>
                        <input type="text" class="form-control bg-dark-soft border-glass text-white py-3 rounded-3" name="max" placeholder="10000" required>
                    </div>
                    <div class="col-md-4">
                        <label class="small text-secondary font-weight-bold text-uppercase mb-2 tracking-wider">Daily Execution Cap</label>
                        <input type="text" class="form-control bg-dark-soft border-glass text-white py-3 rounded-3" name="daily" placeholder="5" required>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-secondary font-weight-bold text-uppercase mb-2 tracking-wider">Win Projection (Count)</label>
                        <input type="number" class="form-control bg-dark-soft border-glass text-white py-3 rounded-3" name="win" placeholder="10" required>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-secondary font-weight-bold text-uppercase mb-2 tracking-wider">Risk Threshold (Loss Count)</label>
                        <input type="number" class="form-control bg-dark-soft border-glass text-white py-3 rounded-3" name="loss" placeholder="2" required>
                    </div>
                    <div class="col-12 mt-4 text-end">
                        <button type="submit" class="btn btn-primary px-5 py-3 font-weight-bold rounded-3 shadow-lg">
                            <i class="ri-save-3-line me-1"></i> INITIALIZE AGENT
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bot Registry Table -->
    <div class="glass-card p-0 overflow-hidden shadow-2xl">
        <div class="p-4 border-bottom border-glass bg-black-soft d-flex justify-content-between align-items-center">
            <h5 class="outfit font-weight-bold text-white mb-0">Active Bot Fleet</h5>
            <span class="badge bg-info-soft text-info px-3 py-2 rounded-pill">{{ count($data) }} Agents Online</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="botTable">
                <thead class="bg-black-soft text-secondary small text-uppercase">
                    <tr>
                        <th class="border-0 px-4 py-3">Rank</th>
                        <th class="border-0 py-3">Agent</th>
                        <th class="border-0 py-3">Pricing</th>
                        <th class="border-0 py-3">Adoption</th>
                        <th class="border-0 py-3">Limits (Min/Max)</th>
                        <th class="border-0 py-3 text-center">Performance (W/L)</th>
                        <th class="border-0 px-4 py-3 text-end">Operation</th>
                    </tr>
                </thead>
                <tbody class="text-white border-glass font-text">
                    @forelse($data as $key => $value)
                        <tr class="border-glass">
                            <td class="px-4 text-secondary small">#{{++$key}}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-box p-1 glass-panel rounded me-3">
                                        <img src="{{asset('storage/image/'.$value->image)}}" class="rounded" style="width:40px; height:40px; object-fit: cover;">
                                    </div>
                                    <div>
                                        <div class="font-weight-bold text-white mb-0">{{$value->name}}</div>
                                        <div class="x-small text-secondary">ID: {{$value->id}}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="text-primary font-weight-bold">${{number_format($value->amount ?? 0)}}</span></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="ri-user-follow-line text-secondary me-2 x-small"></i>
                                    <span class="small">{{$value->used}} users</span>
                                </div>
                            </td>
                            <td>
                                <div class="small">${{number_format($value->min ?? 0)}} - ${{number_format($value->max ?? 0)}}</div>
                                <div class="x-small text-secondary text-uppercase">{{$value->day}} TRADES/DAY</div>
                            </td>
                            <td class="text-center">
                                <div class="badge bg-success-soft text-success px-2 py-1 me-1 x-small">{{$value->win}} W</div>
                                <div class="badge bg-danger-soft text-danger px-2 py-1 x-small">{{$value->loss}} L</div>
                            </td>
                            <td class="px-4 text-end">
                                <a href="{{route('single_bot',$value->id)}}" class="btn btn-sm btn-info-soft text-info border-glass rounded-pill px-3">
                                    <i class="ri-edit-2-line me-1"></i> Configure
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-secondary">
                                <i class="ri-robot-line h1 d-block mb-3 opacity-20"></i>
                                <span>No Trading Agents Found in the Fleet</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .bg-dark-soft { background: rgba(0,0,0,0.2) !important; }
    .bg-black-soft { background: rgba(0,0,0,0.3) !important; }
    .border-glass { border-color: rgba(255,255,255,0.05) !important; }
    .bg-primary-soft { background: rgba(59, 130, 246, 0.1); }
    .bg-success-soft { background: rgba(255, 51, 51, 0.1); }
    .bg-info-soft { background: rgba(6, 182, 212, 0.1); }
    .bg-danger-soft { background: rgba(239, 68, 68, 0.1); }
    .btn-info-soft:hover { background: rgba(6, 182, 212, 0.2); }
    .table-hover tbody tr:hover { background: rgba(255,255,255,0.02); }
    .x-small { font-size: 10px; }
    .tracking-wider { letter-spacing: 0.05em; }
    .avatar-box { border: 1px solid rgba(255,255,255,0.05); }
</style>

<script>
    $(document).ready(function() {
        $('#botTable').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": false,
            "autoWidth": false,
            "responsive": true,
            "pageLength": 10
        });
    });
</script>
@endsection
