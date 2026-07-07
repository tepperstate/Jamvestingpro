@extends('mobile-admin.layouts.app')

@section('header')
<div class="flex items-center gap-3">
    <div style="font-size: 18px; font-weight: 700;">Users Directory</div>
</div>
<div class="flex items-center gap-3">
    <button onclick="openBottomSheet('searchSheet')" style="background:none;border:none;color:white;">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    </button>
</div>
@endsection

@section('content')

<div class="mobile-bezel-outer mb-3">
    <div class="mobile-bezel-inner" style="padding: 10px 14px;">
        <div class="flex gap-2" style="overflow-x: auto; white-space: nowrap; scrollbar-width: none;">
            <a href="?status=all" style="text-decoration:none; background: {{ request('status') == 'all' || !request('status') ? 'var(--accent-color)' : 'rgba(255,255,255,0.05)' }}; color: white; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600;">All Users</a>
            <a href="?status=active" style="text-decoration:none; background: {{ request('status') == 'active' ? 'var(--accent-color)' : 'rgba(255,255,255,0.05)' }}; color: white; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600;">Active</a>
            <a href="?status=pending" style="text-decoration:none; background: {{ request('status') == 'pending' ? 'var(--accent-color)' : 'rgba(255,255,255,0.05)' }}; color: white; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600;">Pending</a>
            <a href="?status=suspended" style="text-decoration:none; background: {{ request('status') == 'suspended' ? 'var(--accent-color)' : 'rgba(255,255,255,0.05)' }}; color: white; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600;">Suspended</a>
        </div>
    </div>
</div>

<div style="font-size: 13px; font-weight:600; color: var(--text-secondary); margin-bottom: 16px; padding-left: 8px; text-transform: uppercase; letter-spacing: 0.05em;">
    {{ number_format($total_users) }} Users • {{ number_format($active_users) }} Active
</div>

<x-mobile.data-table emptyMessage="No users found for this criteria.">
    @foreach($users as $user)
        <x-mobile.data-table-row 
            title="{{ $user->first_name }} {{ $user->last_name }}"
            subtitle="{{ $user->email }}"
            status="{{ ucfirst($user->status ?? 'Active') }}"
            statusColor="{{ ($user->status ?? 'active') == 'active' ? 'success' : 'warning' }}">
            
            <x-slot name="actions">
                <button onclick="openUserSheet({{ $user->id }}, '{{ $user->first_name }} {{ $user->last_name }}', '{{ $user->email }}', '{{ $user->status ?? 'active' }}')" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15); color: white; border-radius: 10px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                </button>
            </x-slot>

            <x-slot name="details">
                <div class="flex justify-between items-center" style="font-size: 12px; color: var(--text-secondary);">
                    <div>
                        <span style="opacity: 0.7;">Joined:</span>
                        <span style="color: white; font-weight: 500;">{{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}</span>
                    </div>
                    <div>
                        <span style="opacity: 0.7;">Bal:</span>
                        <span style="color: var(--success-color); font-weight: 700;">${{ number_format(optional($user->balance)->amount ?? 0, 2) }}</span>
                    </div>
                </div>
            </x-slot>
        </x-mobile.data-table-row>
    @endforeach
</x-mobile.data-table>

<div style="margin-top: 16px;">
    {{ $users->appends(request()->query())->links() }}
</div>

@endsection

@section('modals')
<!-- User Action Bottom Sheet -->
<x-mobile.bottom-sheet id="userActionSheet" title="Manage User">
    <div style="margin-bottom: 24px; text-align: center;">
        <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; font-size: 24px; font-weight: bold; color: white;" id="sheetUserInitial">
            A
        </div>
        <h5 id="sheetUserName" class="text-white mb-1" style="font-weight: 700;">User Name</h5>
        <div id="sheetUserEmail" style="color: var(--text-secondary); font-size: 14px;">email@example.com</div>
        <div class="mt-2"><span id="sheetUserStatus" class="eyebrow-tag eyebrow-success">Active</span></div>
    </div>
    
    <div class="flex flex-col gap-3">
        <a href="#" id="sheetBtnEdit" class="btn" style="text-decoration: none;">
            Edit Profile
            <div class="btn-icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </div>
        </a>
        <a href="#" id="sheetBtnFinance" class="btn" style="text-decoration: none;">
            Financials & Wallets
            <div class="btn-icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </div>
        </a>
        <a href="#" id="sheetBtnTickets" class="btn" style="text-decoration: none;">
            Support Tickets
            <div class="btn-icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </div>
        </a>
        
        <div style="height: 1px; background: rgba(255,255,255,0.1); margin: 8px 0;"></div>
        
        <a href="#" id="sheetBtnBan" class="btn" style="background: rgba(239, 68, 68, 0.1); color: #EF4444; text-decoration: none;">
            Suspend User
            <div class="btn-icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
            </div>
        </a>
    </div>
</x-mobile.bottom-sheet>

<script>
    function openUserSheet(id, name, email, status) {
        document.getElementById('sheetUserName').innerText = name;
        document.getElementById('sheetUserEmail').innerText = email;
        document.getElementById('sheetUserInitial').innerText = name.charAt(0).toUpperCase();
        
        const statusEl = document.getElementById('sheetUserStatus');
        statusEl.innerText = status.toUpperCase();
        if(status.toLowerCase() === 'active') {
            statusEl.className = 'eyebrow-tag eyebrow-success';
        } else {
            statusEl.className = 'eyebrow-tag eyebrow-warning';
        }
        
        // Setup links dynamically
        document.getElementById('sheetBtnEdit').href = "/admin/users/edit/" + id;
        document.getElementById('sheetBtnBan').href = "/admin/users/ban/" + id; // Replace with actual ban route
        
        openBottomSheet('userActionSheet');
    }
</script>
@endsection
