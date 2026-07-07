<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\BankWirePaymentRequest as BankWireRequestValidation;
use App\Models\BankWirePaymentRequest;
use App\Enums\BankWireStatus;
use App\Enums\FinanceReviewStatus;
use App\Enums\BankWirePaymentStatus;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Services\FinanceNotificationService;

class BankWirePaymentController extends Controller
{
    protected $financeNotificationService;

    public function __construct(
        FinanceNotificationService $financeNotificationService
    ) {
        $this->financeNotificationService = $financeNotificationService;
    }

    public function create()
    {
        return view('payments.bank-wire-form');
    }

    public function store(BankWireRequestValidation $request)
    {
        $validated = $request->validated();
        
        $paymentReference = 'BW-' . strtoupper(Str::random(10));
        
        $wireRequest = BankWirePaymentRequest::create(array_merge($validated, [
            'uuid' => (string) Str::uuid(),
            'user_id' => auth()->id(),
            'payment_reference' => $paymentReference,
            'status' => BankWireStatus::SUBMITTED_TO_FINANCE->value,
            'finance_status' => FinanceReviewStatus::PENDING->value,
            'payment_status' => BankWirePaymentStatus::PENDING->value,
            'initiated_at' => Carbon::now(),
            'submitted_to_finance_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addDays(7), // Wire requests valid for 7 days
        ]));

        // Notify Finance Department
        $this->financeNotificationService->notifyNewWireRequest($wireRequest);

        return redirect()->route('dashboard.index')->with('success', 'Bank Wire transfer request submitted successfully. Our finance team will review it shortly. Reference: ' . $paymentReference);
    }
}
