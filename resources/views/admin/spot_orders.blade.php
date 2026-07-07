@extends('layouts.admin.app')
@section('title', 'Spot Orders Approval')

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-5">
        <div>
            <h1 class="h2 mb-1 text-white gradient-text" style="font-weight: 800;">Spot Orders Approval</h1>
            <p class="text-secondary">Approve or reject spot market orders submitted by users.</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="glass-card satin-border p-4" style="border-radius: 20px;">
                <h5 class="text-white mb-3"><i class="ri-settings-3-line"></i> Spot Auto-Approve Configuration</h5>
                <form action="{{ route('admin.settings.trading.auto-approve') }}" method="POST" class="d-flex align-items-center flex-wrap gap-3">
                    @csrf
                    <input type="hidden" name="spot_auto_approve_submit" value="1">
                    
                    <div class="form-check form-switch me-4">
                        <input class="form-check-input" type="checkbox" id="spot_auto_approve" name="spot_auto_approve" {{ site()->spot_auto_approve ? 'checked' : '' }}>
                        <label class="form-check-label text-white" for="spot_auto_approve">Enable Auto-Approval</label>
                    </div>

                    <div class="input-group" style="max-width: 250px;">
                        <span class="input-group-text bg-dark border-secondary text-white">Target Profit Margin</span>
                        <input type="number" step="0.01" class="form-control bg-dark border-secondary text-white" name="spot_auto_win_percent" value="{{ site()->spot_auto_win_percent }}" placeholder="e.g. 10.00">
                        <span class="input-group-text bg-dark border-secondary text-white">%</span>
                    </div>

                    <button type="submit" class="btn btn-primary" style="border-radius: 10px;">Save Configuration</button>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="glass-card satin-border p-4" style="border-radius: 20px;">
                <div class="table-responsive">
                    <table class="table text-white mb-0 align-middle">
                        <thead style="background: rgba(255,255,255,0.02);">
                            <tr>
                                <th class="border-0 small text-secondary py-3">USER</th>
                                <th class="border-0 small text-secondary py-3">ASSET</th>
                                <th class="border-0 small text-secondary py-3">TYPE</th>
                                <th class="border-0 small text-secondary py-3">AMOUNT</th>
                                <th class="border-0 small text-secondary py-3">PRICE / TOTAL</th>
                                <th class="border-0 small text-secondary py-3">STATUS</th>
                                <th class="border-0 small text-secondary py-3">DATE</th>
                                <th class="border-0 small text-secondary py-3 text-end">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $o)
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);">
                                <td class="py-3">
                                    <div class="font-weight-bold">{{ ($o->user->first_name || $o->user->last_name) ? ($o->user->first_name . ' ' . $o->user->last_name) : 'Unknown' }}</div>
                                    <div class="small text-secondary">{{ $o->user->email ?? '' }}</div>
                                    @if($o->is_demo) <span class="badge bg-warning text-dark mt-1" style="font-size:0.6rem;">DEMO</span> @endif
                                </td>
                                <td class="py-3 font-weight-bold">{{ $o->symbol }}</td>
                                <td class="py-3">
                                    <span class="badge {{ $o->type == 'buy' ? 'bg-success' : 'bg-danger' }} px-2">
                                        {{ strtoupper($o->type) }}
                                    </span>
                                    @if($o->order_type !== 'market')
                                        <div class="small text-warning mt-1">{{ strtoupper(str_replace('_', ' ', $o->order_type)) }}</div>
                                        @if($o->stop_price) <div class="small text-secondary">Stop: ${{ number_format($o->stop_price, 2) }}</div> @endif
                                        @if($o->limit_price) <div class="small text-secondary">Limit: ${{ number_format($o->limit_price, 2) }}</div> @endif
                                        @if($o->trigger_price) <div class="small text-secondary">Trigger: ${{ number_format($o->trigger_price, 2) }}</div> @endif
                                        @if($o->trailing_delta) <div class="small text-secondary">Trailing: {{ number_format($o->trailing_delta, 2) }}%</div> @endif
                                    @endif
                                </td>
                                <td class="py-3">{{ number_format($o->amount, 4) }}</td>
                                <td class="py-3">
                                    <div class="small text-secondary">@ ${{ number_format($o->price, 2) }}</div>
                                    <div class="font-weight-bold text-white">${{ number_format($o->total_usd, 2) }}</div>
                                </td>
                                <td class="py-3">
                                    @php $sc = ['pending'=>'warning','approved'=>'success','rejected'=>'danger','filled'=>'info'][$o->status] ?? 'secondary'; @endphp
                                    <span class="badge bg-{{ $sc }} px-2" style="font-size:0.65rem;">{{ strtoupper($o->status) }}</span>
                                    @if($o->admin_hit_wick) <div class="small text-danger mt-1">LIQUIDATED</div> @endif
                                </td>
                                <td class="py-3 small text-secondary">{{ $o->created_at->format('M d, Y H:i') }}</td>
                                <td class="py-3 text-end">
                                    @if($o->status === 'pending')
                                    <button class="btn btn-sm btn-success px-3 me-1 mb-1" onclick="handleOrder({{ $o->id }}, 'approve', '{{ $o->type }}', {{ $o->total_usd }})" style="border-radius:10px;">Approve</button>
                                    <button class="btn btn-sm btn-danger px-3 mb-1" onclick="handleOrder({{ $o->id }}, 'reject', '{{ $o->type }}', {{ $o->total_usd }})" style="border-radius:10px;">Reject</button>
                                    <button class="btn btn-sm btn-warning text-dark px-3 mt-1" onclick="handleOrder({{ $o->id }}, 'hit_wick', '{{ $o->type }}', {{ $o->total_usd }})" style="border-radius:10px; font-weight: bold;"><i class="ri-flashlight-line"></i> Force Liquidation</button>
                                    @else
                                    <span class="small text-secondary"><i class="ri-check-line"></i> Handled</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="text-center text-secondary py-5">No spot orders found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $orders->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function handleOrder(id, action, type, totalUsd) {
    if (!confirm(`Are you sure you want to ${action} this ${type} order?`)) return;
    
    let profit = null, loss = null;
    if (action === 'approve') {
        let wantSimulate = confirm("Do you want to adjust the final settlement with a Slippage Bonus or Liquidity Fee PERCENTAGE? (Click Cancel to pay standard amount)");
        if (wantSimulate) {
            let p = prompt("Enter EXTRA Slippage Bonus PERCENTAGE to add (e.g. 10 for 10%):", "0");
            if(p && parseFloat(p) > 0) profit = (parseFloat(p) / 100) * totalUsd;
            else {
                let l = prompt("Enter Liquidity Fee PERCENTAGE to deduct (e.g. 5 for 5%):", "0");
                if(l && parseFloat(l) > 0) loss = (parseFloat(l) / 100) * totalUsd;
            }
        }
    }
    
    let notes = prompt("Enter admin notes (optional):", "");

    fetch("{{ route('admin.spot_orders.action') }}", {
        method: 'POST',
        headers: {'Content-Type':'application/json', 'X-CSRF-TOKEN':"{{ csrf_token() }}"},
        body: JSON.stringify({id, action, notes, profit_override: profit, loss_override: loss})
    })
    .then(r => r.json().then(d => ({ok: r.ok, data: d})))
    .then(({ok, data}) => {
        if(ok && data.status) {
            toastr.success(data.status);
            setTimeout(() => window.location.reload(), 1500);
        } else {
            toastr.error(data.error || 'Failed');
        }
    })
    .catch(() => toastr.error('Network error'));
}
</script>
@endpush
