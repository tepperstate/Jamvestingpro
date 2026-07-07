@extends('layouts.admin.app')
@section('title', 'KYC Verification')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-white mb-1">Identity Verification Hub</h4>
            <p class="text-secondary mb-0">Review and verify user-submitted identity documentation.</p>
        </div>
        <div class="glass-panel px-4 py-2 rounded-pill border-glass">
            <span class="text-white small font-weight-bold"><i class="ri-shield-check-line me-2 text-primary"></i> COMPLIANCE MONITOR ACTIVE</span>
        </div>
    </div>

    <!-- KYC Registry -->
    <div class="glass-card p-0 overflow-hidden shadow-2xl">
        <div class="p-4 border-bottom border-glass d-flex justify-content-between align-items-center bg-black-soft">
           <div>
               <h5 class="outfit font-weight-bold text-white mb-0">Compliance Validation Stack</h5>
               <p class="text-secondary small mb-0 opacity-75">Documentation review pending verification.</p>
           </div>
           <span class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill">{{ count($kyc) }} Pending IDVs</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="kyc-table">
                <thead class="bg-black-soft text-secondary small text-uppercase">
                    <tr>
                        <th class="border-0 px-4 py-3">Rank</th>
                        <th class="border-0 py-3">User Context</th>
                        <th class="border-0 py-3 text-center">Identity Artifacts</th>
                        <th class="border-0 py-3 text-center">Residency Proof</th>
                        <th class="border-0 py-3 text-center">Status</th>
                        <th class="border-0 px-4 py-3 text-end">Operation</th>
                    </tr>
                </thead>
                @php use \App\Models\User; @endphp
                <tbody class="text-white border-glass font-text">
                    @foreach ($kyc as $key => $datas)
                        <tr class="border-glass">
                            <td class="px-4 text-secondary small">IDV-{{++$key}}</td>
                            <td>
                                <div class="font-weight-bold text-white mb-0">{{User::where('id',$datas->user_id)->first()->first_name ?? 'NOT_FOUND'}}</div>
                                <div class="x-small text-secondary tracking-wider">UID: {{ $datas->user_id }}</div>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <div class="doc-preview">
                                        @if(!is_null($datas->indentity))
                                            <a href="{{asset('storage/image/'.$datas->indentity)}}" target="_blank" class="glass-panel d-block rounded p-1">
                                                <img src="{{asset('storage/image/'.$datas->indentity)}}" class="rounded" style="width:40px; height:28px; object-fit: cover;">
                                            </a>
                                        @else
                                            <span class="x-small text-secondary p-1 border-glass rounded font-weight-bold">EMPTY_F</span>
                                        @endif
                                    </div>
                                    <div class="doc-preview">
                                        @if(!is_null($datas->indentity_back))
                                            <a href="{{asset('storage/image/'.$datas->indentity_back)}}" target="_blank" class="glass-panel d-block rounded p-1">
                                                <img src="{{asset('storage/image/'.$datas->indentity_back)}}" class="rounded" style="width:40px; height:28px; object-fit: cover;">
                                            </a>
                                        @else
                                            <span class="x-small text-secondary p-1 border-glass rounded font-weight-bold">EMPTY_B</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <div class="doc-preview">
                                        @if(!is_null($datas->residency))
                                            <a href="{{asset('storage/image/'.$datas->residency)}}" target="_blank" class="glass-panel d-block rounded p-1">
                                                <img src="{{asset('storage/image/'.$datas->residency)}}" class="rounded" style="width:40px; height:28px; object-fit: cover;">
                                            </a>
                                        @else
                                            <span class="x-small text-secondary p-1 border-glass rounded font-weight-bold">EMPTY_P</span>
                                        @endif
                                    </div>
                                    <div class="doc-preview">
                                        @if(!is_null($datas->residency_back))
                                            <a href="{{asset('storage/image/'.$datas->residency_back)}}" target="_blank" class="glass-panel d-block rounded p-1">
                                                <img src="{{asset('storage/image/'.$datas->residency_back)}}" class="rounded" style="width:40px; height:28px; object-fit: cover;">
                                            </a>
                                        @else
                                            <span class="x-small text-secondary p-1 border-glass rounded font-weight-bold">EMPTY_P</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                @if($datas->status == 'approved')
                                    <span class="badge bg-success text-white px-3 py-2" style="border-radius:8px; font-size:0.7rem; font-weight:800; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">VERIFIED</span>
                                @elseif($datas->status == 'pending')
                                    <span class="badge bg-warning text-dark px-3 py-2" style="border-radius:8px; font-size:0.7rem; font-weight:800; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">EVALUATING</span>
                                @else
                                    <span class="badge bg-danger text-white px-3 py-2" style="border-radius:8px; font-size:0.7rem; font-weight:800; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">REJECTED</span>
                                @endif
                            </td>
                            <td class="px-4 text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-sm btn-success-soft text-success border-glass rounded-pill px-3 cli" did="{{$datas->id}}" user="{{$datas->user_id}}">
                                        <i class="ri-check-line me-1"></i> Verify
                                    </button>
                                    <button class="btn btn-sm btn-danger-soft text-danger border-glass rounded-pill px-3 clix" did="{{$datas->id}}" user="{{$datas->user_id}}">
                                        <i class="ri-close-line me-1"></i> Reject
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .bg-black-soft { background: rgba(0,0,0,0.3) !important; }
    .border-glass { border-color: rgba(255,255,255,0.05) !important; }
    .bg-primary-soft { background: rgba(59, 130, 246, 0.1); }
    .bg-success-soft { background: rgba(255, 51, 51, 0.1); }
    .bg-warning-soft { background: rgba(245, 158, 11, 0.1); }
    .bg-danger-soft { background: rgba(239, 68, 68, 0.1); }
    .btn-success-soft:hover { background: rgba(255, 51, 51, 0.2); }
    .btn-danger-soft:hover { background: rgba(239, 68, 68, 0.2); }
    .doc-preview a:hover { background: rgba(255,255,255,0.1) !important; transform: translateY(-2px); transition: all 0.2s; }
    
    .table-hover tbody tr { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
    .table-hover tbody tr:hover { 
        background: rgba(255,255,255,0.04) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        position: relative;
        z-index: 10;
    }
    .x-small { font-size: 10px; }
    .tracking-wider { letter-spacing: 0.05em; }
</style>

<script>
    $(document).ready(function(){
        $('#kyc-table').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": false,
            "autoWidth": false,
            "responsive": true,
            "pageLength": 10
        });

        $(document).on('click','.clix',function(){
            let id = $(this).attr('did');
            let user_id = $(this).attr('user');
            if(confirm('Are you sure you want to reject this verification?')){
                $.post("{{route('admin.kyc.delete')}}",{
                    _token: "{{ csrf_token() }}",
                    id: id,
                    user_id: user_id
                },function(data){
                    toastr.success("ID rejected successfully","Success")
                    setTimeout(() => location.reload(), 800);
                });
            } 
        })

        $(document).on('click','.cli',function(){
            let id = $(this).attr('did');
            let user_id = $(this).attr('user');
            if(confirm('Are you sure you want to approve this verification?')){
                $.post("{{route('admin.kyc.approved')}}",{
                    _token: "{{ csrf_token() }}",
                    id: id,
                    user_id: user_id
                },function(data){
                    toastr.success("ID verified successfully","Success")
                    setTimeout(() => location.reload(), 800);
                });
            } 
        })
        
        // Anime.js Entrance Animation
        if (typeof anime !== 'undefined') {
            anime({
                targets: '#kyc-table tbody tr',
                translateY: [30, 0],
                opacity: [0, 1],
                delay: anime.stagger(80, {start: 100}),
                easing: 'easeOutQuint',
                duration: 800
            });
        }
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
@endsection
