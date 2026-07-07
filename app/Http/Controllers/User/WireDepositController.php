<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\WireRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WireDepositController extends Controller
{
    public function index()
    {
        $requests = WireRequest::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get();

        return view('user.deposit.wire', compact('requests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100',
            'bank_name' => 'required|string',
            'account_name' => 'required|string',
            'account_number' => 'required|string',
            'routing_number' => 'nullable|string',
            'swift_code' => 'nullable|string',
            'message' => 'nullable|string',
        ]);

        WireRequest::create([
            'user_id' => Auth::id(),
            'amount' => $request->amount,
            'bank_name' => $request->bank_name,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'routing_number' => $request->routing_number,
            'swift_code' => $request->swift_code,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Wire deposit request submitted successfully. We will review and provide instructions.');
    }
}
