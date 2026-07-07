@extends('layouts.admin.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-white mb-1">Intelligence Execution Logs</h4>
            <p class="text-secondary mb-0">Market signal absorption metrics for <span class="text-white font-weight-bold">{{$user->first_name}}</span>.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
             <div class="badge bg-warning-soft text-warning px-4 py-2 rounded-pill border-glass">
                <span class="small font-weight-bold">SIGNAL TRACKER: ACTIVE</span>
            </div>
            <button onclick="history.back()" class="btn btn-outline-light btn-sm rounded-pill px-4 border-glass font-weight-bold">
                <i class="ri-arrow-left-line me-1"></i> Back
            </button>
        </div>
    </div>

    <!-- Signal Trade History Registry -->
    <div class="glass-card p-0 overflow-hidden shadow-2xl">
        <div class="p-4 border-bottom border-glass bg-black-soft d-flex justify-content-between align-items-center">
           <div>
               <h5 class="outfit font-weight-bold text-white mb-0">Signal Utilization Stack</h5>
               <p class="text-secondary small mb-0 opacity-75">Historical impact of broadcasted intelligence.</p>
           </div>
           <span class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill">{{ count($data) }} Signal Events</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="example">
                <thead class="bg-black-soft text-secondary small text-uppercase">
                    <tr>
                        <th class="border-0 px-4 py-3">Rank</th>
                        <th class="border-0 py-3">Asset Artifact</th>
                        <th class="border-0 py-3">Volume Traded</th>
                        <th class="border-0 py-3">P/L Yield</th>
                        <th class="border-0 py-3">Direction</th>
                        <th class="border-0 py-3">Outcome</th>
                        <th class="border-0 px-4 py-3 text-end">Timestamp</th>
                    </tr>
                </thead>
                <tbody class="text-white border-glass font-text">
                    @forelse ($data as $key => $value)
                        <tr class="border-glass">
                            <td class="px-4 text-secondary small">#{{++$key}}</td>
                            <td>
                                <div class="font-weight-bold text-white mb-0">{{$value->name}}</div>
                                <div class="x-small text-secondary tracking-wider">{{$value->symbol}}</div>
                            </td>
                            <td><span class="font-weight-bold text-white">${{number_format($value->amount)}}</span></td>
                            <td><span class="text-success font-weight-bold">+${{number_format($value->profit)}}</span></td>
                            <td>
                                @if($value->type == 'Buy')
                                    <span class="badge bg-success-soft text-success px-3 py-1 rounded-pill small">LONG / BUY</span>
                                @else
                                    <span class="badge bg-danger-soft text-danger px-3 py-1 rounded-pill small">SHORT / SELL</span>
                                @endif
                            </td>
                            <td>
                                @if($value->status == 'win')
                                    <span class="text-success small font-weight-bold"><i class="ri-checkbox-circle-fill me-1"></i> VALIDATED</span>
                                @else
                                    <span class="text-danger small font-weight-bold"><i class="ri-close-circle-fill me-1"></i> REJECTED</span>
                                @endif
                            </td>
                            <td class="px-4 text-end small text-secondary">
                                {{ \Carbon\Carbon::parse($value->created_at)->format('M d, Y H:i') }}
                                <button class="btn btn-sm text-white ms-2" data-toggle="modal" data-target="#editSignal{{$value->id}}"><i class="ri-edit-2-line"></i></button>
                            </td>
                        </tr>

                        <!-- Edit Signal Modal -->
                        <div class="modal fade" id="editSignal{{$value->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content bg-dark text-white border-glass">
                                    <div class="modal-header border-bottom border-glass">
                                        <h5 class="modal-title font-weight-bold">Edit Signal Result</h5>
                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('admin.user_signal.update') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="id" value="{{$value->id}}">
                                        <div class="modal-body">
                                            <div class="form-group mb-3">
                                                <label class="small text-secondary">Traded Volume ($)</label>
                                                <input type="number" step="any" name="amount" class="form-control bg-black-soft text-white border-glass" value="{{$value->amount}}" required>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label class="small text-secondary">P/L Yield ($)</label>
                                                <input type="number" step="any" name="profit" class="form-control bg-black-soft text-white border-glass" value="{{$value->profit}}" required>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label class="small text-secondary">Direction</label>
                                                <select name="type" class="form-control bg-black-soft text-white border-glass">
                                                    <option value="Buy" {{$value->type == 'Buy' ? 'selected' : ''}}>LONG / BUY</option>
                                                    <option value="Sell" {{$value->type == 'Sell' ? 'selected' : ''}}>SHORT / SELL</option>
                                                </select>
                                            </div>
                                            <div class="form-group mb-0">
                                                <label class="small text-secondary">Outcome</label>
                                                <select name="status" class="form-control bg-black-soft text-white border-glass">
                                                    <option value="win" {{$value->status == 'win' ? 'selected' : ''}}>VALIDATED (Win)</option>
                                                    <option value="loss" {{$value->status == 'loss' ? 'selected' : ''}}>REJECTED (Loss)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top border-glass">
                                            <button type="button" class="btn btn-outline-light rounded-pill px-4" data-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary rounded-pill px-4">Update Signal</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-secondary">
                                <i class="ri-radar-line h1 d-block mb-3 opacity-20"></i>
                                <span>No Intelligence Execution Detected for this Account</span>
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
    .bg-danger-soft { background: rgba(239, 68, 68, 0.1); }
    .table-hover tbody tr:hover { background: rgba(255,255,255,0.02); }
    .x-small { font-size: 10px; }
    .tracking-wider { letter-spacing: 0.05em; }
</style>

<script>
    $(document).ready(function(){
        $('#example').DataTable({
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

