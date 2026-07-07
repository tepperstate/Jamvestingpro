<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Balance;
use App\Models\WireRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WireManagementController extends Controller
{
    public function index()
    {
        $requests = WireRequest::with('user')->orderBy('created_at', 'desc')->get();

        return view('admin.wire.index', compact('requests'));
    }

    public function approve(Request $request, $id)
    {
        $wire = WireRequest::findOrFail($id);

        if ($wire->status !== 'pending') {
            return back()->with('error', 'Request is already processed.');
        }

        DB::transaction(function () use ($wire) {
            $wire->update(['status' => 'approved']);

            // Credit USD Balance
            $balance = Balance::where('user_id', $wire->user_id)->where('symbol', 'usd')->first();
            if ($balance) {
                $balanceColumn = $wire->user->is_demo ? 'demo' : 'amount';
                $balance->increment($balanceColumn, $wire->amount);
            } else {
                Balance::create([
                    'user_id' => $wire->user_id,
                    'symbol' => 'USD',
                    'name' => 'USD',
                    'amount' => $wire->user->is_demo ? 0 : $wire->amount,
                    'demo' => $wire->user->is_demo ? $wire->amount : 0,
                    'bitcoin' => 0,
                    'bonus' => 0,
                    'bonus_balance' => 0,
                    'referral' => 0,
                ]);
            }

            // Notify user would be here
        });

        return back()->with('success', 'Wire deposit approved and funds credited.');
    }

    public function reject(Request $request, $id)
    {
        $wire = WireRequest::findOrFail($id);

        if ($wire->status !== 'pending') {
            return back()->with('error', 'Request is already processed.');
        }

        $wire->update(['status' => 'rejected']);

        return back()->with('success', 'Wire deposit rejected.');
    }
}
