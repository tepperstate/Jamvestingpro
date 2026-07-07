@extends('mobile-admin.layouts.app')

@section('header')
<div class="flex items-center gap-3">
    <a href="{{ route('admin.mobile.menu') }}" style="color: white; text-decoration: none;">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
    </a>
    <div style="font-size: 18px; font-weight: 700;">Signal Center</div>
</div>
<div class="flex items-center gap-3">
    <button onclick="openBottomSheet('addSignalSheet')" style="background:var(--accent-color);border:none;color:white;border-radius:20px;padding:6px 12px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:6px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        New Signal
    </button>
</div>
@endsection

@section('content')

<div style="font-size: 13px; font-weight:600; color: var(--text-secondary); margin-bottom: 16px; padding-left: 8px; text-transform: uppercase; letter-spacing: 0.05em;">
    {{ count($signals) }} Active Signals
</div>

<x-mobile.data-table emptyMessage="No signals found.">
    @foreach($signals as $signal)
        <x-mobile.data-table-row 
            title="{{ $signal->name }}"
            subtitle="Profit Range: {{ $signal->min }}% - {{ $signal->max }}%"
            status="{{ $signal->daily ?? 'Active' }}"
            statusColor="primary">
            
            <x-slot name="actions">
                <button onclick="openEditSignalSheet({{ $signal->id }}, '{{ addslashes($signal->name) }}', '{{ $signal->amount }}', '{{ $signal->min }}', '{{ $signal->max }}', '{{ $signal->daily }}')" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15); color: white; border-radius: 10px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                </button>
            </x-slot>

            <x-slot name="details">
                <div class="flex justify-between items-center" style="font-size: 12px; color: var(--text-secondary);">
                    <div>
                        <span style="opacity: 0.7;">Amount:</span>
                        <span style="color: white; font-weight: 500;">${{ number_format($signal->amount, 2) }}</span>
                    </div>
                </div>
            </x-slot>
        </x-mobile.data-table-row>
    @endforeach
</x-mobile.data-table>

@endsection

@section('modals')
<!-- Add Signal Sheet -->
<x-mobile.bottom-sheet id="addSignalSheet" title="New Signal">
    <form action="{{ route('addSignal') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="input-group" style="margin-bottom: 12px;">
            <label style="color: var(--text-secondary); font-size: 12px; font-weight: 600; margin-bottom: 4px; display: block;">Asset Pair / Name</label>
            <input type="text" name="name" required placeholder="e.g., BTC/USD" style="width:100%; background: var(--input-bg); border: 1px solid var(--card-border); color: white; border-radius: 12px; padding: 12px; font-size: 15px;" />
        </div>
        
        <div class="input-group" style="margin-bottom: 12px;">
            <label style="color: var(--text-secondary); font-size: 12px; font-weight: 600; margin-bottom: 4px; display: block;">Base Amount ($)</label>
            <input type="number" name="amount" required placeholder="1000" style="width:100%; background: var(--input-bg); border: 1px solid var(--card-border); color: white; border-radius: 12px; padding: 12px; font-size: 15px;" />
        </div>
        
        <div class="flex gap-2" style="margin-bottom: 12px;">
            <div class="input-group" style="flex: 1;">
                <label style="color: var(--text-secondary); font-size: 12px; font-weight: 600; margin-bottom: 4px; display: block;">Min Profit %</label>
                <input type="number" name="min" required placeholder="5" style="width:100%; background: var(--input-bg); border: 1px solid var(--card-border); color: white; border-radius: 12px; padding: 12px; font-size: 15px;" />
            </div>
            <div class="input-group" style="flex: 1;">
                <label style="color: var(--text-secondary); font-size: 12px; font-weight: 600; margin-bottom: 4px; display: block;">Max Profit %</label>
                <input type="number" name="max" required placeholder="15" style="width:100%; background: var(--input-bg); border: 1px solid var(--card-border); color: white; border-radius: 12px; padding: 12px; font-size: 15px;" />
            </div>
        </div>
        
        <div class="input-group" style="margin-bottom: 16px;">
            <label style="color: var(--text-secondary); font-size: 12px; font-weight: 600; margin-bottom: 4px; display: block;">Duration</label>
            <input type="text" name="daily" required placeholder="e.g., 24 Hours" style="width:100%; background: var(--input-bg); border: 1px solid var(--card-border); color: white; border-radius: 12px; padding: 12px; font-size: 15px;" />
        </div>
        
        <button type="submit" class="btn">
            Broadcast Signal
            <div class="btn-icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </div>
        </button>
    </form>
</x-mobile.bottom-sheet>

<!-- Edit Signal Sheet -->
<x-mobile.bottom-sheet id="editSignalSheet" title="Edit Signal">
    <form action="{{ route('edit_signal') }}" method="POST" id="editSignalForm">
        @csrf
        <input type="hidden" name="id" id="editSignalId" />
        <div class="input-group" style="margin-bottom: 12px;">
            <label style="color: var(--text-secondary); font-size: 12px; font-weight: 600; margin-bottom: 4px; display: block;">Asset Pair / Name</label>
            <input type="text" name="name" id="editSignalName" required style="width:100%; background: var(--input-bg); border: 1px solid var(--card-border); color: white; border-radius: 12px; padding: 12px; font-size: 15px;" />
        </div>
        
        <div class="input-group" style="margin-bottom: 12px;">
            <label style="color: var(--text-secondary); font-size: 12px; font-weight: 600; margin-bottom: 4px; display: block;">Base Amount ($)</label>
            <input type="number" name="amount" id="editSignalAmount" required style="width:100%; background: var(--input-bg); border: 1px solid var(--card-border); color: white; border-radius: 12px; padding: 12px; font-size: 15px;" />
        </div>
        
        <div class="flex gap-2" style="margin-bottom: 12px;">
            <div class="input-group" style="flex: 1;">
                <label style="color: var(--text-secondary); font-size: 12px; font-weight: 600; margin-bottom: 4px; display: block;">Min Profit %</label>
                <input type="number" name="min" id="editSignalMin" required style="width:100%; background: var(--input-bg); border: 1px solid var(--card-border); color: white; border-radius: 12px; padding: 12px; font-size: 15px;" />
            </div>
            <div class="input-group" style="flex: 1;">
                <label style="color: var(--text-secondary); font-size: 12px; font-weight: 600; margin-bottom: 4px; display: block;">Max Profit %</label>
                <input type="number" name="max" id="editSignalMax" required style="width:100%; background: var(--input-bg); border: 1px solid var(--card-border); color: white; border-radius: 12px; padding: 12px; font-size: 15px;" />
            </div>
        </div>
        
        <div class="input-group" style="margin-bottom: 16px;">
            <label style="color: var(--text-secondary); font-size: 12px; font-weight: 600; margin-bottom: 4px; display: block;">Duration</label>
            <input type="text" name="daily" id="editSignalDaily" required style="width:100%; background: var(--input-bg); border: 1px solid var(--card-border); color: white; border-radius: 12px; padding: 12px; font-size: 15px;" />
        </div>
        
        <button type="submit" class="btn">
            Save Changes
            <div class="btn-icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </div>
        </button>
    </form>
</x-mobile.bottom-sheet>

<script>
    function openEditSignalSheet(id, name, amount, min, max, daily) {
        document.getElementById('editSignalId').value = id;
        document.getElementById('editSignalName').value = name;
        document.getElementById('editSignalAmount').value = amount;
        document.getElementById('editSignalMin').value = min;
        document.getElementById('editSignalMax').value = max;
        document.getElementById('editSignalDaily').value = daily;
        
        openBottomSheet('editSignalSheet');
    }
</script>
@endsection
