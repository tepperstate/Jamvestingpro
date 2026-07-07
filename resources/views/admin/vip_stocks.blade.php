@extends('layouts.admin.app')
@section('content')

<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-5">
        <div>
            <h1 class="h2 mb-1 text-white gradient-text" style="font-weight: 800;">VIP Stock Whitelist</h1>
            <p class="text-muted mb-0">Manage high-value stocks for VIP tier clients. Only stocks over $5,000 with VIP flag are shown to qualified users.</p>
        </div>
        <div class="badge badge-success-glass px-4 py-2 h5 mb-0">{{ $vipCount }} VIP Securities</div>
    </div>

    <div class="glass-card satin-border overflow-hidden">
        <div class="table-responsive">
            <table id="vipTable" class="table text-white">
                <thead>
                    <tr>
                        <th>STOCK</th>
                        <th>SYMBOL</th>
                        <th>PRICE</th>
                        <th>VIP STATUS</th>
                        <th class="text-right">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stocks as $stock)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($stock->image && $stock->image != 'default_stock.png')
                                <img src="{{ asset('storage/image/'.$stock->image) }}" style="width:32px; height:32px; border-radius:8px; margin-right:10px;">
                                @else
                                <div class="glass-panel d-flex align-items-center justify-content-center mr-2" style="width:32px;height:32px;border-radius:8px;font-size:10px;font-weight:700;color:var(--accent-primary);">{{ substr($stock->symbol,0,2) }}</div>
                                @endif
                                <div class="font-weight-bold text-white">{{ $stock->name }}</div>
                            </div>
                        </td>
                        <td class="font-weight-bold">{{ $stock->symbol }}</td>
                        <td>
                            <form class="d-inline" method="POST" action="{{ route('admin.vip_stock.price') }}">
                                @csrf
                                <input type="hidden" name="id" value="{{ $stock->id }}">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-white">${{ number_format($stock->buy, 2) }}</span>
                                    <input type="number" name="price" class="form-control glass-panel text-white border-0 d-inline" style="width:100px;padding:4px 8px !important;font-size:12px;" value="{{ $stock->buy }}" step="0.01">
                                    <button type="submit" class="btn btn-sm glass-panel border-0 text-info"><i data-lucide="save" style="width:12px"></i></button>
                                </div>
                            </form>
                        </td>
                        <td>
                            <button class="btn btn-sm {{ $stock->is_vip ? 'btn-success' : 'btn-outline-secondary' }} toggle-vip" data-id="{{ $stock->id }}">
                                {{ $stock->is_vip ? '★ VIP' : 'Standard' }}
                            </button>
                        </td>
                        <td class="text-right">
                            <button class="btn btn-sm glass-panel border-0 text-white toggle-vip" data-id="{{ $stock->id }}">
                                <i data-lucide="{{ $stock->is_vip ? 'shield-off' : 'shield-check' }}" style="width:14px"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $stocks->links() }}</div>
    </div>
</div>

@if(session('status'))
<script>toastr.success("{{ session('status') }}");</script>
@endif

<script>
$(document).ready(function(){
    lucide.createIcons();
    $('#vipTable').DataTable({
        "paging": false, "lengthChange": false, "searching": true,
        "ordering": true, "info": false, "autoWidth": false,
        "language": { "search": "", "searchPlaceholder": "Search stocks..." }
    });
    $('.dataTables_filter input').addClass('glass-panel text-white border-0 px-4 py-2').css({'background': 'rgba(255,255,255,0.02)', 'width': '250px', 'border-radius': '12px'});

    $(document).on('click', '.toggle-vip', function(){
        var id = $(this).data('id');
        $.ajax({
            url: "{{ route('admin.vip_stock.toggle') }}",
            method: 'POST',
            data: { id: id, _token: '{{ csrf_token() }}' },
            success: function(res) {
                toastr.success(res.message);
                setTimeout(() => location.reload(), 800);
            }
        });
    });
});
</script>
@endsection
