<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stock_Trade;
use Illuminate\Http\Request;

class VipStockController extends Controller
{
    public function index()
    {
        $stocks = Stock_Trade::orderBy('is_vip', 'desc')
            ->orderBy('buy', 'desc')
            ->paginate(20);

        $vipCount = Stock_Trade::where('is_vip', true)->count();

        return view('admin.vip_stocks', compact('stocks', 'vipCount'));
    }

    public function toggleVip(Request $request)
    {
        $stock = Stock_Trade::findOrFail($request->id);
        $stock->update(['is_vip' => ! $stock->is_vip]);

        return response()->json([
            'status' => true,
            'message' => $stock->name.($stock->is_vip ? ' added to' : ' removed from').' VIP whitelist',
            'is_vip' => $stock->is_vip,
        ]);
    }

    public function updatePrice(Request $request)
    {
        $stock = Stock_Trade::findOrFail($request->id);
        $stock->update(['buy' => $request->price]);

        return back()->with('status', $stock->name.' price updated to $'.number_format($request->price, 2));
    }
}
