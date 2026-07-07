@extends('layouts.admin.app')
@section('title', 'Signal Monitoring')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-white mb-1">Signal Intelligence Hub</h4>
            <p class="text-secondary mb-0">Broadcast high-precision trading signals to the platform network.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
             <div class="badge bg-warning-soft text-warning px-4 py-2 rounded-pill border-glass">
                <span class="small font-weight-bold">SIGNAL EMITTER: ONLINE</span>
            </div>
            <button onclick="history.back()" class="btn btn-outline-light btn-sm rounded-pill px-4 border-glass font-weight-bold">
                <i class="ri-arrow-left-line me-1"></i> Back
            </button>
        </div>
    </div>

    <!-- Signal Generation Form -->
    <div class="glass-card mb-4 shadow-2xl">
        <div class="p-4 border-bottom border-glass bg-black-soft d-flex align-items-center">
            <div class="icon-box bg-warning-soft rounded-pill p-3 me-3">
                <i class="ri-radar-line h4 mb-0 text-warning"></i>
            </div>
            <div>
                <h5 class="outfit font-weight-bold text-white mb-0">Broadcast New Intelligence</h5>
                <p class="text-secondary small mb-0 opacity-75">Define technical parameters for the next market move.</p>
            </div>
        </div>
        <div class="p-4">
            @if($errors->any())
                <div class="alert alert-danger mb-4" style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: #fca5a5; border-radius: 12px; padding: 12px 16px;">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="post" action="{{route('addSignal')}}" enctype="multipart/form-data">
                @csrf
                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="small text-secondary font-weight-bold text-uppercase mb-2 tracking-wider">Signal Identifier</label>
                        <input type="text" class="form-control bg-dark-soft border-glass text-white py-3 rounded-3" name="name" placeholder="e.g. ALPHA QUANT" required>
                    </div>
                    <div class="col-md-4">
                        <label class="small text-secondary font-weight-bold text-uppercase mb-2 tracking-wider">Signal Image (PNG/JPG/GIF)</label>
                        <input type="file" class="form-control bg-dark-soft border-glass text-white py-2 rounded-3" name="image" accept="image/*">
                    </div>
                    <div class="col-md-4">
                        <label class="small text-secondary font-weight-bold text-uppercase mb-2 tracking-wider">Service Fee ($)</label>
                        <input type="number" class="form-control bg-dark-soft border-glass text-white py-3 rounded-3" name="amount" placeholder="499" required>
                    </div>
                    <div class="col-md-3">
                        <label class="small text-secondary font-weight-bold text-uppercase mb-2 tracking-wider">Buffer Percentage (%)</label>
                        <input type="number" step="0.01" class="form-control bg-dark-soft border-glass text-white py-3 rounded-3" name="buffer_percent" placeholder="20.00" value="20" required>
                    </div>
                    <div class="col-md-3">
                        <label class="small text-secondary font-weight-bold text-uppercase mb-2 tracking-wider">Per Withdrawal (%)</label>
                        <input type="number" step="0.01" class="form-control bg-dark-soft border-glass text-white py-3 rounded-3" name="per_withdrawal_percent" placeholder="5.00" value="5" required>
                    </div>
                    <div class="col-md-3">
                        <label class="small text-secondary font-weight-bold text-uppercase mb-2 tracking-wider">Min Entry</label>
                        <input type="number" class="form-control bg-dark-soft border-glass text-white py-3 rounded-3" name="min" placeholder="50" required>
                    </div>
                    <div class="col-md-3">
                        <label class="small text-secondary font-weight-bold text-uppercase mb-2 tracking-wider">Max Limit</label>
                        <input type="number" class="form-control bg-dark-soft border-glass text-white py-3 rounded-3" name="max" placeholder="5000" required>
                    </div>
                    <div class="col-md-4">
                        <label class="small text-secondary font-weight-bold text-uppercase mb-2 tracking-wider">Daily Execution Frequency</label>
                        <input type="text" class="form-control bg-dark-soft border-glass text-white py-3 rounded-3" name="daily" placeholder="12 Trades / Cycle" required>
                    </div>
                    <div class="col-md-4">
                        <label class="small text-secondary font-weight-bold text-uppercase mb-2 tracking-wider">Min Profit (%)</label>
                        <input type="number" step="0.01" class="form-control bg-dark-soft border-glass text-white py-3 rounded-3" name="profit_min_percent" placeholder="5.00" value="5" required>
                    </div>
                    <div class="col-md-4">
                        <label class="small text-secondary font-weight-bold text-uppercase mb-2 tracking-wider">Max Profit (%)</label>
                        <input type="number" step="0.01" class="form-control bg-dark-soft border-glass text-white py-3 rounded-3" name="profit_max_percent" placeholder="15.00" value="15" required>
                    </div>
                    <div class="col-12 mt-4 text-end">
                        <button type="submit" class="btn btn-primary px-5 py-3 font-weight-bold rounded-3 shadow-lg">
                            <i class="ri-broadcast-line me-1"></i> PUBLISH SIGNAL
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Signal Registry Table -->
    <div class="glass-card p-0 overflow-hidden shadow-2xl">
        <div class="p-4 border-bottom border-glass bg-black-soft d-flex justify-content-between align-items-center">
            <h5 class="outfit font-weight-bold text-white mb-0">Signal Transmission Stack</h5>
            <span class="badge bg-warning-soft text-warning px-3 py-2 rounded-pill">{{ count($data) }} Active Streams</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="signalTable">
                <thead class="bg-black-soft text-secondary small text-uppercase">
                    <tr>
                        <th class="border-0 px-4 py-3">Rank</th>
                        <th class="border-0 py-3">Signal Stream</th>
                        <th class="border-0 py-3">Commercials</th>
                        <th class="border-0 py-3">Usage</th>
                        <th class="border-0 py-3">Daily Output</th>
                        <th class="border-0 py-3">Bounds</th>
                        <th class="border-0 py-3">Profit % Range</th>
                        <th class="border-0 px-4 py-3 text-end">Operation</th>
                    </tr>
                </thead>
                <tbody class="text-white border-glass font-text">
                    @forelse ($data as $key => $value)
                        <tr class="border-glass">
                            <td class="px-4 text-secondary small">#{{++$key}}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-box p-1 glass-panel rounded me-3">
                                        <img src="{{asset('storage/image/'.$value->image)}}" 
                                             onerror="this.src='https://ui-avatars.com/api/?name={{ $value->name }}&background=000&color=fff&rounded=true';"
                                             class="rounded" style="width:36px; height:36px; object-fit: cover;">
                                    </div>
                                    <div class="font-weight-bold text-white">{{$value->name}}</div>
                                </div>
                            </td>
                            <td><span class="text-primary font-weight-bold">${{number_format($value->amount)}}</span></td>
                            <td><div class="badge bg-success-soft text-success px-2 py-1 small rounded-pill">{{$value->used}} users</div></td>
                            <td><span class="small text-secondary">{{$value->day}}</span></td>
                            <td>
                                <div class="small text-white-50">${{number_format($value->min)}} - ${{number_format($value->max)}}</div>
                            </td>
                            <td>
                                <div class="small text-success font-weight-bold">{{$value->profit_min_percent ?? 5}}% - {{$value->profit_max_percent ?? 15}}%</div>
                            </td>
                            <td class="px-4 text-end">
                                <a href="{{route('single_signal',$value->id)}}" class="btn btn-sm btn-info-soft text-info border-glass rounded-pill px-3">
                                    <i class="ri-edit-line me-1"></i> Tune
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-secondary">
                                <i class="ri-radar-line h1 d-block mb-3 opacity-20"></i>
                                <span>No Trading Intelligence Found in History</span>
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
    .bg-warning-soft { background: rgba(245, 158, 11, 0.1); }
    .bg-success-soft { background: rgba(255, 51, 51, 0.1); }
    .bg-info-soft { background: rgba(6, 182, 212, 0.1); }
    .table-hover tbody tr:hover { background: rgba(255,255,255,0.02); }
    .x-small { font-size: 10px; }
    .tracking-wider { letter-spacing: 0.05em; }
    .avatar-box { border: 1px solid rgba(255,255,255,0.05); }
</style>

<script>
    $(document).ready(function() {
        $('#signalTable').DataTable({
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
