@extends('layouts.admin.app')
@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-5">
        <div>
            <h1 class="h2 mb-1 text-white gradient-text" style="font-weight: 800;">Capital Inflow Proofs</h1>
            <p class="text-muted mb-0">Review and verify manual deposit receipts from user accounts.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
             <div class="badge badge-primary-glass px-4 py-2 border-0 satin-border" style="background: rgba(59, 130, 246, 0.05) !important;">
                <span class="text-white small font-weight-bold">PENDING VERIFICATION: {{ count($data) }}</span>
            </div>
        </div>
    </div>

    <!-- Proof Registry -->
    <div class="glass-card satin-border overflow-hidden shadow-2xl">
        <div class="p-4 d-flex justify-content-between align-items-center" style="background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--glass-border);">
            <div>
                <h3 class="h5 text-white mb-1 font-weight-bold">Receipt Pipeline</h3>
                <p class="text-muted x-small mb-0">Cryptographic evidence of external asset transfers.</p>
            </div>
        </div>
        <div class="table-responsive">
            <table id="example" class="table text-white">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>ENTITY</th>
                        <th>AMOUNT</th>
                        <th>METHOD</th>
                        <th>RECEIPT</th>
                        <th>TIMESTAMP</th>
                        <th class="text-right">COMMANDS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $key => $val )
                        <tr>
                            <td class="small text-muted">{{ ++$key }}</td>
                            <td>
                                <div class="font-weight-bold text-white">{{ $val->first_name ?? '' }} {{ $val->last_name ?? '' }}</div>
                            </td>
                            <td>
                                <div class="text-success font-weight-bold">${{ number_format($val->amount ?? 0, 2) }}</div>
                            </td>
                            <td>
                                <div class="badge badge-secondary-glass px-2 py-1 x-small">{{ strtoupper($val->method ?? 'N/A') }}</div>
                            </td>
                            <td>
                                <div class="glass-panel p-1 rounded" style="width: 60px; height: 40px; overflow: hidden; border: 1px solid var(--glass-border);">
                                    <img class='img-fluid' src="{{ asset('storage/image/'.$val->file) }}" style='width: 100%; height: 100%; object-fit: cover;'>
                                </div>
                            </td>  
                            <td class="small text-muted">{{ \Illuminate\Support\Carbon::parse($val->created_at)->format('M d, H:i') }}</td>
                            <td class="text-right">
                                <a class='btn btn-sm glass-panel border-0 text-primary px-3' href="{{ asset('storage/image/'.$val->file) }}" target="_blank">
                                    <i data-lucide="maximize-2" style="width:14px; display:inline-block;"></i> INSPECT
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No pending proofs in current cycle.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .x-small { font-size: 10px; }
    .gap-3 { gap: 1rem; }
    #wrapper #content-wrapper #content { background: transparent !important; }
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
            "language": {
                "search": "",
                "searchPlaceholder": "Filter receipts..."
            }
        });
        $('.dataTables_filter input').addClass('glass-panel text-white border-0 px-4 py-2').css({'background': 'rgba(255,255,255,0.02)', 'width': '250px', 'border-radius': '12px'});
        lucide.createIcons();
    })
</script>
@endsection
