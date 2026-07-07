@extends('layouts.admin.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-white mb-1">Agent Execution Logs</h4>
            <p class="text-secondary mb-0">Detailed algorithmic trade outcomes for <span class="text-white font-weight-bold">{{$user->first_name}}</span>.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
             <div class="badge bg-success-soft text-success px-4 py-2 rounded-pill border-glass">
                <span class="small font-weight-bold">VALIDATOR STATUS: NOMINAL</span>
            </div>
            <button onclick="history.back()" class="btn btn-outline-light btn-sm rounded-pill px-4 border-glass font-weight-bold">
                <i class="ri-arrow-left-line me-1"></i> Back
            </button>
        </div>
    </div>

    <!-- Trade History Registry -->
    <div class="glass-card p-0 overflow-hidden shadow-2xl">
        <div class="p-4 border-bottom border-glass bg-black-soft d-flex justify-content-between align-items-center">
           <div>
               <h5 class="outfit font-weight-bold text-white mb-0">Algorithmic Trade Stack</h5>
               <p class="text-secondary small mb-0 opacity-75">Historical performance of deployed agents.</p>
           </div>
           <span class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill">{{ count($data) }} Total Trades</span>
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
                        <th class="border-0 py-3">Status</th>
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
                                    <span class="text-success small font-weight-bold"><i class="ri-checkbox-circle-fill me-1"></i> PROFIT</span>
                                @else
                                    <span class="text-danger small font-weight-bold"><i class="ri-close-circle-fill me-1"></i> LOSS</span>
                                @endif
                            </td>
                            <td class="px-4 text-end small text-secondary">
                                {{ $value->created_at->format('M d, Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-secondary">
                                <i class="ri-history-line h1 d-block mb-3 opacity-20"></i>
                                <span>No Algorithmic Activity Detected for this Account</span>
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
