@component('mail::message')
# Bank Wire Payment Status Update

Your bank wire payment request status has been updated to: **{{ $wireRequest->finance_status }}**.

**Reference:** {{ $wireRequest->payment_reference }}
**Amount:** {{ $wireRequest->amount }} {{ $wireRequest->currency }}

@if($wireRequest->finance_status === 'rejected')
**Reason:** {{ $wireRequest->finance_notes }}
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent
