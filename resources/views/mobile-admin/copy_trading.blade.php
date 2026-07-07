@extends('mobile-admin.layouts.app')

@section('header')
<div class="flex items-center gap-3">
    <a href="{{ route('admin.mobile.menu') }}" style="color: white; text-decoration: none;">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
    </a>
    <div style="font-size: 18px; font-weight: 700;">Copy Traders</div>
</div>
<div class="flex items-center gap-3">
    <button onclick="openBottomSheet('addTraderSheet')" style="background:var(--accent-color);border:none;color:white;border-radius:20px;padding:6px 12px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:6px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        New Trader
    </button>
</div>
@endsection

@section('content')

<div style="font-size: 13px; font-weight:600; color: var(--text-secondary); margin-bottom: 16px; padding-left: 8px; text-transform: uppercase; letter-spacing: 0.05em;">
    {{ number_format($active_traders) }} Master Traders
</div>

<x-mobile.data-table emptyMessage="No traders found.">
    @foreach($traders as $trader)
        <x-mobile.data-table-row 
            title="{{ $trader->name }}"
            subtitle="Win Rate: {{ $trader->win }}% • Copiers: {{ $trader->total_copier }}"
            status="Active"
            statusColor="success">
            
            <x-slot name="actions">
                <button onclick="openTraderSheet({{ $trader->id }}, '{{ addslashes($trader->name) }}', '{{ $trader->percentage }}', '{{ $trader->win }}', '{{ $trader->total_copier }}', '{{ $trader->amount }}')" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15); color: white; border-radius: 10px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                </button>
            </x-slot>

            <x-slot name="details">
                <div class="flex justify-between items-center" style="font-size: 12px; color: var(--text-secondary);">
                    <div>
                        <span style="opacity: 0.7;">ROI:</span>
                        <span style="color: var(--success-color); font-weight: 700;">+{{ $trader->percentage }}%</span>
                    </div>
                    <div>
                        <span style="opacity: 0.7;">Min Copy:</span>
                        <span style="color: white; font-weight: 500;">${{ number_format($trader->amount, 2) }}</span>
                    </div>
                </div>
            </x-slot>
        </x-mobile.data-table-row>
    @endforeach
</x-mobile.data-table>

<div style="margin-top: 16px;">
    {{ $traders->links() }}
</div>

@endsection

@section('modals')
<!-- Add Trader Sheet -->
<x-mobile.bottom-sheet id="addTraderSheet" title="New Master Trader">
    <form action="{{ route('add.store_trader') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="input-group" style="margin-bottom: 12px;">
            <label style="color: var(--text-secondary); font-size: 12px; font-weight: 600; margin-bottom: 4px; display: block;">Trader Name</label>
            <input type="text" name="name" required placeholder="e.g., CryptoKing" style="width:100%; background: var(--input-bg); border: 1px solid var(--card-border); color: white; border-radius: 12px; padding: 12px; font-size: 15px;" />
        </div>
        
        <div class="flex gap-2" style="margin-bottom: 12px;">
            <div class="input-group" style="flex: 1;">
                <label style="color: var(--text-secondary); font-size: 12px; font-weight: 600; margin-bottom: 4px; display: block;">ROI (%)</label>
                <input type="number" name="percentage" required placeholder="150" style="width:100%; background: var(--input-bg); border: 1px solid var(--card-border); color: white; border-radius: 12px; padding: 12px; font-size: 15px;" />
            </div>
            <div class="input-group" style="flex: 1;">
                <label style="color: var(--text-secondary); font-size: 12px; font-weight: 600; margin-bottom: 4px; display: block;">Win Rate (%)</label>
                <input type="number" name="win" required placeholder="85" style="width:100%; background: var(--input-bg); border: 1px solid var(--card-border); color: white; border-radius: 12px; padding: 12px; font-size: 15px;" />
            </div>
        </div>
        
        <div class="input-group" style="margin-bottom: 12px;">
            <label style="color: var(--text-secondary); font-size: 12px; font-weight: 600; margin-bottom: 4px; display: block;">Min Copy Amount ($)</label>
            <input type="number" name="amount" required placeholder="500" style="width:100%; background: var(--input-bg); border: 1px solid var(--card-border); color: white; border-radius: 12px; padding: 12px; font-size: 15px;" />
        </div>
        
        <button type="submit" class="btn">
            Create Trader Profile
            <div class="btn-icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </div>
        </button>
    </form>
</x-mobile.bottom-sheet>

<!-- View/Edit Trader Sheet -->
<x-mobile.bottom-sheet id="viewTraderSheet" title="Trader Management">
    <div style="margin-bottom: 24px; text-align: center;">
        <h5 id="sheetTraderName" class="text-white mb-1" style="font-weight: 700; font-size: 20px;">Trader Name</h5>
        <div id="sheetTraderStats" style="color: var(--text-secondary); font-size: 14px;">ROI: +150% • Win: 85%</div>
    </div>
    
    <div class="flex flex-col gap-3">
        <form action="{{ route('add.copy_delete') }}" method="POST" id="traderDeleteForm" style="display: block; width: 100%;">
            @csrf
            <input type="hidden" name="id" id="traderId" value="">
            <button type="submit" class="btn" style="background: rgba(239, 68, 68, 0.1); color: #EF4444;">
                Delete Trader Profile
                <div class="btn-icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                </div>
            </button>
        </form>
    </div>
</x-mobile.bottom-sheet>

<script>
    function openTraderSheet(id, name, roi, win, copiers, amount) {
        document.getElementById('sheetTraderName').innerText = name;
        document.getElementById('sheetTraderStats').innerText = "ROI: +" + roi + "% • Win Rate: " + win + "%";
        
        document.getElementById('traderId').value = id;
        
        openBottomSheet('viewTraderSheet');
    }
</script>
@endsection
