<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\SystemCoin;
use App\Models\UserWallet;
use Illuminate\Http\Request;

class WalletManagerController extends Controller
{
    public function index()
    {
        $coins = SystemCoin::orderBy('symbol', 'asc')->get();

        return view('admin.wallets.index', compact('coins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'symbol' => 'required|string|unique:system_coins,symbol',
            'name' => 'required|string',
            'network' => 'nullable|string',
            'min_swap_amount' => 'required|numeric|min:0',
            'fee_percentage' => 'required|numeric|min:0',
        ]);

        SystemCoin::create($request->all());

        return back()->with('success', 'Coin added successfully.');
    }

    public function update(Request $request, $id)
    {
        $coin = SystemCoin::findOrFail($id);

        $request->validate([
            'symbol' => 'required|string|unique:system_coins,symbol,'.$coin->id,
            'name' => 'required|string',
            'network' => 'nullable|string',
            'min_swap_amount' => 'required|numeric|min:0',
            'fee_percentage' => 'required|numeric|min:0',
        ]);

        $coin->update($request->all());

        return back()->with('success', 'Coin updated successfully.');
    }

    public function toggleWallet($id)
    {
        $wallet = UserWallet::findOrFail($id);

        $wallet->is_enabled = ! $wallet->is_enabled;
        $wallet->save();

        AdminAuditLog::create([
            'admin_id' => auth()->id(),
            'action' => $wallet->is_enabled ? 'enabled_wallet' : 'disabled_wallet',
            'target_type' => 'UserWallet',
            'target_id' => $wallet->id,
            'details' => 'Toggled wallet for user '.$wallet->user_id.' coin '.$wallet->coin_symbol,
        ]);

        return back()->with('success', 'Wallet status toggled successfully.');
    }

    public function toggleStatus(Request $request)
    {
        $coin = SystemCoin::findOrFail($request->id);
        $coin->is_active = ! $coin->is_active;
        $coin->save();

        return response()->json(['success' => true, 'message' => 'Status updated.']);
    }
}
