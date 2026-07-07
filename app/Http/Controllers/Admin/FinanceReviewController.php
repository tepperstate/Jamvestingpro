<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BankWirePaymentRequest;
use App\Enums\FinanceReviewStatus;
use App\Enums\BankWireStatus;
use App\Enums\BankWirePaymentStatus;
use Carbon\Carbon;
use App\Services\FinanceNotificationService;

class FinanceReviewController extends Controller
{
    protected $financeNotificationService;

    public function __construct(FinanceNotificationService $financeNotificationService)
    {
        $this->financeNotificationService = $financeNotificationService;
    }

    public function index()
    {
        $requests = BankWirePaymentRequest::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.finance.bank-wire-index', compact('requests'));
    }

    public function show($id)
    {
        $wireRequest = BankWirePaymentRequest::findOrFail($id);
        return view('admin.finance.bank-wire-review', compact('wireRequest'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'finance_status' => ['required', 'in:approved,rejected'],
            'finance_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $wireRequest = BankWirePaymentRequest::findOrFail($id);
        
        $wireRequest->finance_status = $request->finance_status;
        $wireRequest->finance_notes = $request->finance_notes;
        $wireRequest->finance_reviewed_at = Carbon::now();

        if ($request->finance_status === 'approved') {
            $wireRequest->status = BankWireStatus::FINANCE_APPROVED->value;
            $wireRequest->payment_status = BankWirePaymentStatus::SENT->value; // Or CONFIRMED depending on flow
            // Here we would typically add logic to credit the user's wallet
        } else {
            $wireRequest->status = BankWireStatus::FINANCE_REJECTED->value;
            $wireRequest->payment_status = BankWirePaymentStatus::FAILED->value;
        }

        $wireRequest->save();

        // Notify User
        $this->financeNotificationService->notifyReviewUpdate($wireRequest);

        return redirect()->route('admin.finance.bank-wire.index')->with('success', 'Bank Wire request updated successfully.');
    }
}
