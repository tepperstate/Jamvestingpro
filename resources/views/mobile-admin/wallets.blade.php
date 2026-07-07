@extends('mobile-admin.layouts.app')

@section('header')
<div class="flex items-center gap-3">
    <a href="{{ route('admin.mobile.menu') }}" style="color: white; text-decoration: none;">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
    </a>
    <div style="font-size: 18px; font-weight: 700;">Deposit Wallets</div>
</div>
<div class="flex items-center gap-3">
    <button onclick="openBottomSheet('addWalletSheet')" style="background:var(--accent-color);border:none;color:white;border-radius:20px;padding:6px 12px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:6px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        New
    </button>
</div>
@endsection

@section('content')

<div style="font-size: 13px; font-weight:600; color: var(--text-secondary); margin-bottom: 16px; padding-left: 8px; text-transform: uppercase; letter-spacing: 0.05em;">
    {{ count($wallets) }} Supported Assets
</div>

<x-mobile.data-table emptyMessage="No deposit wallets found.">
    @foreach($wallets as $wallet)
        <x-mobile.data-table-row 
            title="{{ $wallet->name }}"
            subtitle="Symbol: {{ $wallet->symbol }}"
            status="Active"
            statusColor="success">
            
            <x-slot name="actions">
                <button onclick="openEditWalletSheet({{ $wallet->id }}, '{{ addslashes($wallet->name) }}', '{{ addslashes($wallet->symbol) }}', '{{ addslashes($wallet->address) }}')" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15); color: white; border-radius: 10px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                </button>
            </x-slot>

            <x-slot name="details">
                <div class="flex flex-col gap-2" style="font-size: 12px; color: var(--text-secondary);">
                    <div>
                        <span style="opacity: 0.7; display:block; margin-bottom: 4px;">Wallet Address:</span>
                        <div style="color: white; font-weight: 500; word-break: break-all; background: rgba(0,0,0,0.5); padding: 8px 12px; border-radius: 8px;">
                            {{ $wallet->address }}
                        </div>
                    </div>
                </div>
            </x-slot>
        </x-mobile.data-table-row>
    @endforeach
</x-mobile.data-table>

@endsection

@section('modals')
<!-- Add Wallet Sheet -->
<x-mobile.bottom-sheet id="addWalletSheet" title="New Deposit Wallet">
    <form action="{{ route('admin.wallets.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="input-group">
            <label>Asset Name</label>
            <input type="text" name="name" required placeholder="e.g., Bitcoin" />
        </div>
        
        <div class="input-group">
            <label>Asset Symbol</label>
            <input type="text" name="symbol" required placeholder="e.g., BTC" />
        </div>
        
        <div class="input-group">
            <label>Wallet Address</label>
            <textarea name="address" required rows="3" placeholder="Enter full receiving address..." style="width:100%; background: var(--input-bg); border: 1px solid var(--card-border); color: white; border-radius: 12px; padding: 12px; font-size: 15px;"></textarea>
        </div>
        
        <button type="submit" class="btn">
            Add Wallet
            <div class="btn-icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </div>
        </button>
    </form>
</x-mobile.bottom-sheet>

<!-- Edit Wallet Sheet -->
<x-mobile.bottom-sheet id="editWalletSheet" title="Edit Deposit Wallet">
    <form action="{{ route('admin.wallets.update') }}" method="POST" id="editWalletForm">
        @csrf
        <input type="hidden" name="id" id="editWalletId" />
        <div class="input-group">
            <label>Asset Name</label>
            <input type="text" name="name" id="editWalletName" required />
        </div>
        
        <div class="input-group">
            <label>Asset Symbol</label>
            <input type="text" name="symbol" id="editWalletSymbol" required />
        </div>
        
        <div class="input-group">
            <label>Wallet Address</label>
            <textarea name="address" id="editWalletAddress" required rows="3" style="width:100%; background: var(--input-bg); border: 1px solid var(--card-border); color: white; border-radius: 12px; padding: 12px; font-size: 15px;"></textarea>
        </div>
        
        <div class="flex gap-3">
            <button type="submit" class="btn">
                Save
                <div class="btn-icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </div>
            </button>
            <button type="button" onclick="deleteWallet()" class="btn" style="background: rgba(239, 68, 68, 0.1); color: #EF4444; width: 64px; padding: 0;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path></svg>
            </button>
        </div>
    </form>
</x-mobile.bottom-sheet>

<script>
    function openEditWalletSheet(id, name, symbol, address) {
        document.getElementById('editWalletId').value = id;
        document.getElementById('editWalletName').value = name;
        document.getElementById('editWalletSymbol').value = symbol;
        document.getElementById('editWalletAddress').value = address;
        
        openBottomSheet('editWalletSheet');
    }
    
    function deleteWallet() {
        if(confirm('Are you sure you want to delete this deposit wallet?')) {
            const id = document.getElementById('editWalletId').value;
            window.location.href = "{{ url('admin/admin-wallets/delete') }}/" + id;
        }
    }
</script>
@endsection
