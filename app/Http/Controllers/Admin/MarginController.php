<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarginPair;
use App\Models\MarginPosition;
use App\Models\Stock_Trade;
use App\Services\BinancePriceService;
use Illuminate\Http\Request;

class MarginController extends Controller
{
    public function pairsIndex()
    {
        $pairs = MarginPair::all();

        return view('admin.margin_pairs', compact('pairs'));
    }

    public function storePair(Request $request)
    {
        $request->validate([
            'symbol' => 'required|string',
        ]);

        MarginPair::create($request->all());

        return redirect()->back()->with('success', 'Margin pair created successfully.');
    }

    public function syncFromBinance(Request $request)
    {
        // Margin uses Spot exchange info from Binance
        $info = BinancePriceService::getSpotExchangeInfo();
        if (! $info || ! isset($info['symbols'])) {
            return response()->json(['status' => false, 'message' => 'Failed to fetch exchange info from Binance.']);
        }

        $symbols = $info['symbols'];
        $count = 0;

        foreach ($symbols as $sym) {
            // Check status TRADING and margin allowed
            if ($sym['status'] === 'TRADING' && (isset($sym['isMarginTradingAllowed']) && $sym['isMarginTradingAllowed'])) {
                $symbolName = $sym['symbol'];

                $exists = MarginPair::where('symbol', $symbolName)->exists();
                if (! $exists) {
                    MarginPair::create([
                        'symbol' => $symbolName,
                        'status' => 'active',
                        'max_leverage' => 10,
                        'borrow_rate_hourly' => 0.001,
                        'maintenance_margin' => 0.05,
                        'max_borrow' => 0,
                        'collateral_factor' => 0,
                        'mark_price' => 0,
                        'buffer_percent' => 0,
                        'per_withdrawal_percent' => 0,
                    ]);
                    $count++;
                }

                $stockExists = Stock_Trade::where('symbol', $symbolName)->exists();
                if (! $stockExists) {
                    $baseAsset = $sym['baseAsset'] ?? str_replace('USDT', '', $symbolName);
                    Stock_Trade::create([
                        'name' => $baseAsset,
                        'symbol' => $symbolName,
                        'is_vip' => false,
                        'image' => strtolower($baseAsset).'.png',
                    ]);
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => "Successfully synchronized {$count} new margin pairs from Binance.",
            'count' => $count,
        ]);
    }

    public function updatePair(Request $request)
    {
        $pair = MarginPair::findOrFail($request->id);
        $pair->update($request->all());

        return redirect()->back()->with('success', 'Margin pair updated successfully.');
    }

    public function destroyPair($id)
    {
        $pair = MarginPair::findOrFail($id);
        $pair->delete();

        return redirect()->back()->with('success', 'Margin pair deleted.');
    }

    public function massDelete(Request $request)
    {
        $ids = $request->ids;
        if (! is_array($ids) || empty($ids)) {
            return response()->json(['status' => false, 'message' => 'No pairs selected.']);
        }

        try {
            MarginPair::whereIn('id', $ids)->delete();

            return response()->json(['status' => true, 'message' => count($ids).' pairs deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error deleting pairs: '.$e->getMessage()]);
        }
    }

    public function positionsIndex()
    {
        $positions = MarginPosition::with(['user', 'pair'])->get();

        return view('admin.margin_positions', compact('positions'));
    }

    public function updatePosition(Request $request)
    {
        $position = MarginPosition::findOrFail($request->id);

        if ($request->action === 'delete') {
            $position->delete();

            return redirect()->back()->with('success', 'Position deleted.');
        }

        $position->update($request->all());

        return redirect()->back()->with('success', 'Position updated.');
    }

    public function injectPosition(Request $request)
    {
        MarginPosition::create($request->all());

        return redirect()->back()->with('success', 'Position injected successfully.');
    }

    public function updateMarkPrice(Request $request)
    {
        $pair = MarginPair::findOrFail($request->pair_id);
        $pair->update(['mark_price' => $request->mark_price]);

        return redirect()->back()->with('success', 'Mark price updated.');
    }

    public function approvePosition($id)
    {
        $position = MarginPosition::findOrFail($id);
        $position->approval_status = 'approved';
        $position->status = 'open';
        
        // Update entry price to current market price
        try {
            $data = \App\Services\BinancePriceService::fetchAll();
            $priceMap = $data['priceMap'];
            $pair = $position->marginPair;
            if ($pair && isset($priceMap[$pair->symbol])) {
                $position->entry_price = $priceMap[$pair->symbol];
                
                // Recalculate liquidation price based on new entry price
                $position->liquidation_price = $position->direction === 'long'
                    ? $position->entry_price * (1 - (1 / $position->leverage) + ($pair->maintenance_margin / 100))
                    : $position->entry_price * (1 + (1 / $position->leverage) - ($pair->maintenance_margin / 100));
            }
        } catch (\Exception $e) {}
        
        $position->save();
        return redirect()->back()->with('success', 'Position approved and opened successfully.');
    }

    public function rejectPosition($id)
    {
        $position = MarginPosition::findOrFail($id);
        $position->approval_status = 'rejected';
        $position->status = 'closed';
        $position->save();

        // Refund collateral
        $balanceColumn = $position->is_demo ? 'demo' : 'amount';
        \Illuminate\Support\Facades\DB::table('balances')
            ->where('user_id', $position->user_id)
            ->where('symbol', 'usd')
            ->increment($balanceColumn, $position->collateral);

        return redirect()->back()->with('success', 'Position rejected and collateral refunded.');
    }
}
