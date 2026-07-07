<?php

namespace App\Services;

use App\Models\BankWirePaymentRequest;
use Illuminate\Support\Facades\Mail;

class FinanceNotificationService
{
    protected string $financeEmail;

    public function __construct()
    {
        $this->financeEmail = config('mail.finance_department', 'finance@jamvestingpro.com');
    }

    public function notifyNewWireRequest(BankWirePaymentRequest $wireRequest)
    {
        // Ideally this would be queued or sent via a Mailable class
        // Mail::to($this->financeEmail)->send(new \App\Mail\NewBankWireRequest($wireRequest));
        
        // For the sake of the prompt's simplicity, we'll just log it or dispatch a job.
        \Log::info("Finance Notification: New Bank Wire Request {$wireRequest->payment_reference} from user {$wireRequest->user_id}. Amount: {$wireRequest->amount} {$wireRequest->currency}");
    }

    public function notifyReviewUpdate(BankWirePaymentRequest $wireRequest)
    {
        \Log::info("Finance Notification: Bank Wire Request {$wireRequest->payment_reference} status updated to {$wireRequest->finance_status}.");
    }
}
