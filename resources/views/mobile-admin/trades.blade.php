@extends('mobile-admin.layouts.app')

@section('header')
<div class="flex items-center gap-3">
    <a href="{{ route('admin.mobile.menu') }}" style="color: white; text-decoration: none;">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
    </a>
    <div style="font-size: 18px; font-weight: 700;">Spot Trades</div>
</div>
@endsection

@section('content')

<div class="mobile-bezel-outer mb-3">
    <div class="mobile-bezel-inner" style="padding: 10px 14px;">
        <div class="flex gap-2" style="overflow-x: auto; white-space: nowrap; scrollbar-width: none;">
            <a href="?status=all" style="text-decoration:none; background: {{ request('status') == 'all' || !request('status') ? 'var(--accent-color)' : 'rgba(255,255,255,0.05)' }}; color: white; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600;">All Trades</a>
            <a href="?status=pending" style="text-decoration:none; background: {{ request('status') == 'pending' ? 'var(--accent-color)' : 'rgba(255,255,255,0.05)' }}; color: white; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600;">Active</a>
            <a href="?status=win" style="text-decoration:none; background: {{ request('status') == 'win' ? 'var(--accent-color)' : 'rgba(255,255,255,0.05)' }}; color: white; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600;">Won</a>
            <a href="?status=loss" style="text-decoration:none; background: {{ request('status') == 'loss' ? 'var(--accent-color)' : 'rgba(255,255,255,0.05)' }}; color: white; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600;">Lost</a>
        </div>
    </div>
</div>

<div style="font-size: 13px; font-weight:600; color: var(--text-secondary); margin-bottom: 16px; padding-left: 8px; text-transform: uppercase; letter-spacing: 0.05em;">
    ${{ number_format($total_volume, 2) }} Vol • {{ number_format($active_trades) }} Active
</div>

<x-mobile.data-table emptyMessage="No spot trades found.">
    @foreach($orders as $order)
        <x-mobile.data-table-row 
            title="{{ $order->symbol ?? 'Trade' }} ({{ ucfirst($order->type ?? 'Buy') }})"
            subtitle="{{ optional($order->user)->first_name }} {{ optional($order->user)->last_name }}"
            status="{{ ucfirst($order->status ?? 'pending') }}"
            statusColor="{{ ($order->status ?? 'pending') == 'pending' ? 'warning' : (($order->status ?? 'win') == 'win' ? 'success' : 'danger') }}">
            
            <x-slot name="actions">
                <button onclick="openTradeSheet({{ $order->id }}, '{{ $order->symbol ?? 'Trade' }}', '{{ optional($order->user)->first_name }} {{ optional($order->user)->last_name }}', '{{ $order->amount ?? 0 }}', '{{ $order->status ?? 'pending' }}', '{{ $order->type ?? 'buy' }}')" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15); color: white; border-radius: 10px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </button>
            </x-slot>

            <x-slot name="details">
                <div class="flex justify-between items-center" style="font-size: 12px; color: var(--text-secondary);">
                    <div>
                        <span style="opacity: 0.7;">Time:</span>
                        <span style="color: white; font-weight: 500;">{{ $order->created_at ? $order->created_at->format('H:i') : 'Unknown' }}</span>
                    </div>
                    <div>
                        <span style="opacity: 0.7;">Amount:</span>
                        <span style="color: white; font-weight: 700;">${{ number_format($order->amount ?? 0, 2) }}</span>
                    </div>
                </div>
            </x-slot>
        </x-mobile.data-table-row>
    @endforeach
</x-mobile.data-table>

<div style="margin-top: 16px;">
    {{ $orders->links() }}
</div>

@endsection

@section('modals')
<!-- Trade Override Bottom Sheet -->
<x-mobile.bottom-sheet id="tradeActionSheet" title="Trade Override">
    <div style="margin-bottom: 24px; text-align: center;">
        <h5 id="sheetTradeSymbol" class="text-white mb-1" style="font-weight: 800; font-size: 24px;">BTC/USD</h5>
        <div id="sheetTradeUser" style="color: var(--text-secondary); font-size: 14px;">User Name</div>
        <div class="mt-2"><span id="sheetTradeStatus" class="badge badge-warning">Pending</span></div>
    </div>
    
    <div class="mobile-bezel-outer mb-3">
        <div class="mobile-bezel-inner" style="padding: 12px; font-size: 13px;">
            <div class="flex justify-between mb-2">
                <span style="color: var(--text-secondary);">Amount</span>
                <span id="sheetTradeAmount" style="color: white; font-weight: 600;">$0.00</span>
            </div>
            <div class="flex justify-between">
                <span style="color: var(--text-secondary);">Type</span>
                <span id="sheetTradeType" style="color: white; font-weight: 600;">Buy</span>
            </div>
        </div>
    </div>
    
    <form action="" method="POST" id="tradeOverrideForm" style="display: block; width: 100%;">
        @csrf
        <div class="input-group" style="margin-bottom: 12px;">
            <label style="color: var(--text-secondary); font-size: 12px; font-weight: 600; margin-bottom: 4px; display: block;">Force Outcome</label>
            <select name="status" style="width:100%; background: var(--input-bg); border: 1px solid var(--card-border); color: white; border-radius: 12px; padding: 12px; font-size: 15px;">
                <option value="win">Win (Profit)</option>
                <option value="loss">Loss</option>
                <option value="draw">Draw (Refund)</option>
            </select>
        </div>
        
        <div class="input-group" style="margin-bottom: 16px;">
            <label style="color: var(--text-secondary); font-size: 12px; font-weight: 600; margin-bottom: 4px; display: block;">Profit % (If Win)</label>
            <input type="number" name="profit_percent" value="80" style="width:100%; background: var(--input-bg); border: 1px solid var(--card-border); color: white; border-radius: 12px; padding: 12px; font-size: 15px;" />
        </div>
        
        <button type="submit" class="btn">
            Apply Override
            <div class="btn-icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </div>
        </button>
    </form>
</x-mobile.bottom-sheet>

<script>
    function openTradeSheet(id, symbol, user, amount, status, type) {
        document.getElementById('sheetTradeSymbol').innerText = symbol;
        document.getElementById('sheetTradeUser').innerText = user;
        document.getElementById('sheetTradeAmount').innerText = '$' + parseFloat(amount).toLocaleString('en-US', {minimumFractionDigits: 2});
        document.getElementById('sheetTradeType').innerText = type.toUpperCase();
        
        document.getElementById('tradeOverrideForm').action = "/admin/trades/" + id + "/override";
        
        const statusEl = document.getElementById('sheetTradeStatus');
        statusEl.innerText = status.toUpperCase();
        if(status.toLowerCase() === 'win') {
            statusEl.className = 'badge badge-success';
        } else if (status.toLowerCase() === 'loss') {
            statusEl.className = 'badge badge-danger';
        } else {
            statusEl.className = 'badge badge-warning';
        }
        
        openBottomSheet('tradeActionSheet');
    }
</script>
@endsection
