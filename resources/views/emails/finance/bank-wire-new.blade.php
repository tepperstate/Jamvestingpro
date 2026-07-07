@component('mail::message')
# New Bank Wire Payment Request

A new bank wire payment request has been submitted and requires financial review.

**Reference:** {{ $wireRequest->payment_reference }}
**Amount:** {{ $wireRequest->amount }} {{ $wireRequest->currency }}
**User ID:** {{ $wireRequest->user_id }}
**Bank Name:** {{ $wireRequest->bank_name }}
**Account Holder:** {{ $wireRequest->account_holder_name }}

@component('mail::button', ['url' => route('admin.finance.bank-wire-review', $wireRequest->id)])
Review Request
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
