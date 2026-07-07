@extends('mobile-admin.layouts.app')

@section('header')
<div class="flex items-center gap-3">
    <a href="{{ route('admin.mobile.menu') }}" style="color: white; text-decoration: none;">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
    </a>
    <div style="font-size: 18px; font-weight: 700;">Withdrawal Management</div>
</div>
@endsection

@section('content')
<div style="font-size: 13px; font-weight:600; color: var(--text-secondary); margin-bottom: 16px; padding-left: 8px; text-transform: uppercase; letter-spacing: 0.05em;">
    {{ number_format($total_pending) }} Pending Withdrawals
</div>

<x-mobile.data-table emptyMessage="No withdrawals found.">
    @foreach($withdrawals as $withdrawal)
        <x-mobile.data-table-row 
            title="${{ number_format($withdrawal->amount, 2) }} {{ $withdrawal->symbol ?? 'USD' }}"
            subtitle="{{ $withdrawal->user->first_name ?? 'Unknown' }} {{ $withdrawal->user->last_name ?? '' }}"
            status="{{ ucfirst($withdrawal->status ?? 'pending') }}"
            statusColor="{{ ($withdrawal->status ?? 'pending') == 'success' || ($withdrawal->status ?? '') == 'approved' ? 'success' : (($withdrawal->status ?? 'pending') == 'pending' ? 'warning' : 'danger') }}">
            
            <x-slot name="actions">
                <button onclick="openWithSheet({{ $withdrawal->id }}, '{{ $withdrawal->user->first_name ?? 'Unknown' }}', '{{ $withdrawal->amount }}', '{{ $withdrawal->payment_method ?? 'Transfer' }}', '{{ $withdrawal->status ?? 'pending' }}')" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15); color: white; border-radius: 10px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                </button>
            </x-slot>

            <x-slot name="details">
                <div class="flex justify-between items-center" style="font-size: 12px; color: var(--text-secondary);">
                    <div>
                        <span style="opacity: 0.7;">Method:</span>
                        <span style="color: white; font-weight: 500;">{{ $withdrawal->payment_method ?? 'Crypto' }}</span>
                    </div>
                    <div>
                        <span style="opacity: 0.7;">Date:</span>
                        <span style="color: white; font-weight: 500;">{{ $withdrawal->created_at ? $withdrawal->created_at->format('M d, H:i') : 'Unknown' }}</span>
                    </div>
                </div>
            </x-slot>
        </x-mobile.data-table-row>
    @endforeach
</x-mobile.data-table>

<div style="margin-top: 16px;">
    {{ $withdrawals->links() }}
</div>
@endsection

@section('modals')
<x-mobile.bottom-sheet id="withActionSheet" title="Process Withdrawal">
    <div style="margin-bottom: 24px; text-align: center;">
        <h5 id="sheetWithAmount" class="text-white mb-1" style="font-weight: 800; font-size: 28px;">$0.00</h5>
        <div id="sheetWithUser" style="color: var(--text-secondary); font-size: 14px;">User Name</div>
        <div class="mt-2"><span id="sheetWithStatus" class="eyebrow-tag eyebrow-warning">Pending</span></div>
    </div>
    
    <div class="flex flex-col gap-3">
        <div class="mobile-bezel-outer mb-2">
            <div class="mobile-bezel-inner" style="padding: 12px; font-size: 13px;">
                <div class="flex justify-between">
                    <span style="color: var(--text-secondary);">Method</span>
                    <span id="sheetWithMethod" style="color: white; font-weight: 600;">Crypto</span>
                </div>
            </div>
        </div>
        
        <form action="#" method="POST" id="withApproveForm" style="display: block; width: 100%;">
            @csrf
            <input type="hidden" name="status" value="success">
            <button type="submit" class="btn">
                Approve Withdrawal
                <div class="btn-icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </div>
            </button>
        </form>
        
        <form action="#" method="POST" id="withRejectForm" style="display: block; width: 100%;">
            @csrf
            <input type="hidden" name="status" value="failed">
            <button type="submit" class="btn" style="background: rgba(239, 68, 68, 0.1); color: #EF4444;">
                Reject Withdrawal
                <div class="btn-icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </div>
            </button>
        </form>
    </div>
</x-mobile.bottom-sheet>

<script>
    function openWithSheet(id, user, amount, method, status) {
        document.getElementById('sheetWithAmount').innerText = '$' + parseFloat(amount).toLocaleString('en-US', {minimumFractionDigits: 2});
        document.getElementById('sheetWithUser').innerText = user;
        document.getElementById('sheetWithMethod').innerText = method;
        
        // Dynamically set action URL to the update route with the correct ID
        document.getElementById('withApproveForm').action = "/admin/history/withdrawal/" + id + "/update";
        document.getElementById('withRejectForm').action = "/admin/history/withdrawal/" + id + "/update";
        
        const statusEl = document.getElementById('sheetWithStatus');
        statusEl.innerText = status.toUpperCase();
        if(status.toLowerCase() === 'success' || status.toLowerCase() === 'approved') {
            statusEl.className = 'eyebrow-tag eyebrow-success';
            document.getElementById('withApproveForm').style.display = 'none';
            document.getElementById('withRejectForm').style.display = 'none';
        } else if (status.toLowerCase() === 'failed' || status.toLowerCase() === 'rejected') {
            statusEl.className = 'eyebrow-tag eyebrow-danger';
            document.getElementById('withApproveForm').style.display = 'none';
            document.getElementById('withRejectForm').style.display = 'none';
        } else {
            statusEl.className = 'eyebrow-tag eyebrow-warning';
            document.getElementById('withApproveForm').style.display = 'block';
            document.getElementById('withRejectForm').style.display = 'block';
        }
        
        openBottomSheet('withActionSheet');
    }
</script>
@endsection
