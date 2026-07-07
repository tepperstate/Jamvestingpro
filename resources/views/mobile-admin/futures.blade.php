@extends('mobile-admin.layouts.app')

@section('header')
<div class="flex items-center gap-3">
    <a href="{{ route('admin.mobile.menu') }}" style="color: white; text-decoration: none;">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
    </a>
    <div style="font-size: 18px; font-weight: 700;">Futures Trades</div>
</div>
@endsection

@section('content')

<div class="mobile-bezel-outer mb-3">
    <div class="mobile-bezel-inner" style="padding: 10px 14px;">
        <div class="flex gap-2" style="overflow-x: auto; white-space: nowrap; scrollbar-width: none;">
            <a href="?status=all" style="text-decoration:none; background: {{ request('status') == 'all' || !request('status') ? 'var(--accent-color)' : 'rgba(255,255,255,0.05)' }}; color: white; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600;">All Trades</a>
            <a href="?status=open" style="text-decoration:none; background: {{ request('status') == 'open' ? 'var(--accent-color)' : 'rgba(255,255,255,0.05)' }}; color: white; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600;">Active</a>
            <a href="?status=closed" style="text-decoration:none; background: {{ request('status') == 'closed' ? 'var(--accent-color)' : 'rgba(255,255,255,0.05)' }}; color: white; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600;">Closed</a>
        </div>
    </div>
</div>

<div style="font-size: 13px; font-weight:600; color: var(--text-secondary); margin-bottom: 16px; padding-left: 8px; text-transform: uppercase; letter-spacing: 0.05em;">
    {{ number_format($active_positions) }} Active Positions
</div>

<x-mobile.data-table emptyMessage="No futures trades found.">
    @foreach($positions as $position)
        <x-mobile.data-table-row 
            title="{{ $position->pair->name ?? 'Unknown' }} ({{ $position->leverage ?? 1 }}x)"
            subtitle="{{ optional($position->user)->first_name }} {{ optional($position->user)->last_name }}"
            status="{{ ucfirst($position->status ?? 'open') }}"
            statusColor="{{ ($position->status ?? 'open') == 'open' ? 'warning' : 'secondary' }}">
            
            <x-slot name="actions">
                <button onclick="openFuturesSheet({{ $position->id }}, '{{ $position->pair->name ?? 'Unknown' }}', '{{ optional($position->user)->first_name }} {{ optional($position->user)->last_name }}', '{{ $position->margin ?? 0 }}', '{{ $position->status ?? 'open' }}', '{{ $position->type ?? 'long' }}')" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15); color: white; border-radius: 10px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </button>
            </x-slot>

            <x-slot name="details">
                <div class="flex justify-between items-center" style="font-size: 12px; color: var(--text-secondary);">
                    <div>
                        <span style="opacity: 0.7;">Type:</span>
                        <span style="color: {{ ($position->type ?? 'long') == 'long' ? 'var(--success-color)' : 'var(--danger-color)' }}; font-weight: 500; text-transform: uppercase;">{{ $position->type ?? 'LONG' }}</span>
                    </div>
                    <div>
                        <span style="opacity: 0.7;">Margin:</span>
                        <span style="color: white; font-weight: 700;">${{ number_format($position->margin ?? 0, 2) }}</span>
                    </div>
                </div>
            </x-slot>
        </x-mobile.data-table-row>
    @endforeach
</x-mobile.data-table>

<div style="margin-top: 16px;">
    {{ $positions->links() }}
</div>

@endsection

@section('modals')
<!-- Futures Override Sheet -->
<x-mobile.bottom-sheet id="futuresActionSheet" title="Futures Override">
    <div style="margin-bottom: 24px; text-align: center;">
        <h5 id="sheetFuturesSymbol" class="text-white mb-1" style="font-weight: 800; font-size: 24px;">BTC/USD</h5>
        <div id="sheetFuturesUser" style="color: var(--text-secondary); font-size: 14px;">User Name</div>
        <div class="mt-2"><span id="sheetFuturesStatus" class="eyebrow-tag eyebrow-warning">Open</span></div>
    </div>
    
    <div class="mobile-bezel-outer mb-3">
        <div class="mobile-bezel-inner" style="padding: 12px; font-size: 13px;">
            <div class="flex justify-between mb-2">
                <span style="color: var(--text-secondary);">Margin Amount</span>
                <span id="sheetFuturesMargin" style="color: white; font-weight: 600;">$0.00</span>
            </div>
            <div class="flex justify-between">
                <span style="color: var(--text-secondary);">Position Type</span>
                <span id="sheetFuturesType" style="color: white; font-weight: 600;">Long</span>
            </div>
        </div>
    </div>
    
    <form action="" method="POST" id="futuresOverrideForm" style="display: block; width: 100%;">
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
            <input type="number" name="profit_percent" value="100" style="width:100%; background: var(--input-bg); border: 1px solid var(--card-border); color: white; border-radius: 12px; padding: 12px; font-size: 15px;" />
        </div>
        
        <button type="submit" class="btn" style="background: rgba(239, 68, 68, 0.1); color: #EF4444;">
            Apply Override
            <div class="btn-icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </div>
        </button>
    </form>
</x-mobile.bottom-sheet>

<script>
    function openFuturesSheet(id, symbol, user, margin, status, type) {
        document.getElementById('sheetFuturesSymbol').innerText = symbol;
        document.getElementById('sheetFuturesUser').innerText = user;
        document.getElementById('sheetFuturesMargin').innerText = '$' + parseFloat(margin).toLocaleString('en-US', {minimumFractionDigits: 2});
        
        const typeEl = document.getElementById('sheetFuturesType');
        typeEl.innerText = type.toUpperCase();
        if(type.toLowerCase() === 'long') {
            typeEl.style.color = 'var(--success-color)';
        } else {
            typeEl.style.color = 'var(--danger-color)';
        }
        
        document.getElementById('futuresOverrideForm').action = "/admin/override-outcome/" + id;
        
        const statusEl = document.getElementById('sheetFuturesStatus');
        statusEl.innerText = status.toUpperCase();
        if(status.toLowerCase() === 'open') {
            statusEl.className = 'eyebrow-tag eyebrow-warning';
        } else {
            statusEl.className = 'eyebrow-tag eyebrow-secondary';
        }
        
        openBottomSheet('futuresActionSheet');
    }
</script>
@endsection
