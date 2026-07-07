@extends('mobile-admin.layouts.app')

@section('header')
<div class="flex items-center gap-3">
    <a href="{{ route('admin.mobile.menu') }}" style="color: white; text-decoration: none;">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
    </a>
    <div style="font-size: 18px; font-weight: 700;">KYC Approvals</div>
</div>
@endsection

@section('content')
<div style="font-size: 13px; font-weight:600; color: var(--text-secondary); margin-bottom: 16px; padding-left: 8px; text-transform: uppercase; letter-spacing: 0.05em;">
    {{ number_format($total_pending) }} Pending Verifications
</div>

<x-mobile.data-table emptyMessage="No KYC applications found.">
    @foreach($kyc as $doc)
        <x-mobile.data-table-row 
            title="{{ $doc->user->first_name ?? 'Unknown' }} {{ $doc->user->last_name ?? '' }}"
            subtitle="Submitted {{ $doc->created_at ? $doc->created_at->diffForHumans() : 'Unknown' }}"
            status="{{ ucfirst($doc->status ?? 'pending') }}"
            statusColor="{{ ($doc->status ?? 'pending') == 'approved' ? 'success' : (($doc->status ?? 'pending') == 'pending' ? 'warning' : 'danger') }}">
            
            <x-slot name="actions">
                <button onclick="openKycSheet({{ $doc->id }}, '{{ $doc->user->first_name ?? 'Unknown' }} {{ $doc->user->last_name ?? '' }}', '{{ $doc->front ?? '' }}', '{{ $doc->back ?? '' }}', '{{ $doc->status ?? 'pending' }}')" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15); color: white; border-radius: 10px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                </button>
            </x-slot>

            <x-slot name="details">
                <div class="flex justify-between items-center" style="font-size: 12px; color: var(--text-secondary);">
                    <div>
                        <span style="opacity: 0.7;">Type:</span>
                        <span style="color: white; font-weight: 500;">{{ $doc->type ?? 'ID Card' }}</span>
                    </div>
                </div>
            </x-slot>
        </x-mobile.data-table-row>
    @endforeach
</x-mobile.data-table>

<div style="margin-top: 16px;">
    {{ $kyc->links() }}
</div>
@endsection

@section('modals')
<x-mobile.bottom-sheet id="kycActionSheet" title="Review KYC">
    <div style="margin-bottom: 24px; text-align: center;">
        <h5 id="sheetKycName" class="text-white mb-1" style="font-weight: 700;">User Name</h5>
        <div class="mt-2"><span id="sheetKycStatus" class="eyebrow-tag eyebrow-warning">Pending</span></div>
    </div>
    
    <div class="flex flex-col gap-3">
        <a href="#" id="sheetKycFront" target="_blank" class="btn" style="text-decoration: none;">
            View Front Document
            <div class="btn-icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </div>
        </a>
        <a href="#" id="sheetKycBack" target="_blank" class="btn" style="text-decoration: none;">
            View Back Document
            <div class="btn-icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </div>
        </a>
        
        <div style="height: 1px; background: rgba(255,255,255,0.1); margin: 8px 0;"></div>
        
        <!-- Assuming POST form for approval -->
        <form action="{{ route('admin.kyc.approved') }}" method="POST" id="kycApproveForm" style="display: block; width: 100%;">
            @csrf
            <input type="hidden" name="id" id="kycId" value="">
            <input type="hidden" name="user_id" id="kycUserId" value="">
            <button type="submit" class="btn">
                Approve KYC
                <div class="btn-icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </div>
            </button>
        </form>
    </div>
</x-mobile.bottom-sheet>

<script>
    function openKycSheet(id, name, front, back, status) {
        document.getElementById('sheetKycName').innerText = name;
        document.getElementById('kycId').value = id;
        
        const statusEl = document.getElementById('sheetKycStatus');
        statusEl.innerText = status.toUpperCase();
        if(status.toLowerCase() === 'approved') {
            statusEl.className = 'eyebrow-tag eyebrow-success';
            document.getElementById('kycApproveForm').style.display = 'none';
        } else {
            statusEl.className = 'eyebrow-tag eyebrow-warning';
            document.getElementById('kycApproveForm').style.display = 'block';
        }
        
        document.getElementById('sheetKycFront').href = "/storage/image/" + front; // Adjust path if needed
        document.getElementById('sheetKycBack').href = "/storage/image/" + back;
        
        openBottomSheet('kycActionSheet');
    }
</script>
@endsection
