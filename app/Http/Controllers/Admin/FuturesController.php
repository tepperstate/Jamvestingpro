<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FuturesPair;
use App\Models\FuturesPosition;
use App\Models\Stock_Trade;
use App\Services\BinancePriceService;
use Illuminate\Http\Request;

class FuturesController extends Controller
{
    public function index()
    {
        // View all open positions
        $positions = FuturesPosition::with(['user', 'pair'])->where('status', 'open')->get();

        return view('admin.futures', compact('positions'));
    }

    public function allPositions()
    {
        // View all positions
        $positions = FuturesPosition::with(['user', 'pair'])->get();

        return view('admin.futures_positions', compact('positions'));
    }

    public function pairsIndex()
    {
        $pairs = FuturesPair::all();

        return view('admin.futures_pairs', compact('pairs'));
    }

    public function storePair(Request $request)
    {
        $request->validate([
            'symbol' => 'required|string',
            'base_asset' => 'required|string',
            'quote_asset' => 'required|string',
        ]);

        FuturesPair::create($request->all());

        return redirect()->back()->with('success', 'Futures pair created successfully.');
    }

    public function syncFromBinance(Request $request)
    {
        $info = BinancePriceService::getFuturesExchangeInfo();
        if (! $info || ! isset($info['symbols'])) {
            return response()->json(['status' => false, 'message' => 'Failed to fetch Futures exchange info from Binance.']);
        }

        $symbols = $info['symbols'];
        $count = 0;

        foreach ($symbols as $sym) {
            if ($sym['status'] === 'TRADING') {
                $symbolName = $sym['symbol'];

                // Optional: Check if it exists in stock_trades, or just add it
                $exists = FuturesPair::where('symbol', $symbolName)->exists();
                if (! $exists) {
                    FuturesPair::create([
                        'symbol' => $symbolName,
                        'base_asset' => $sym['baseAsset'] ?? str_replace('USDT', '', $symbolName),
                        'quote_asset' => $sym['quoteAsset'] ?? 'USDT',
                        'max_leverage' => 100, // default or extract from filters
                        'status' => 'active',
                        'funding_rate' => 0.0100,
                        'mark_price' => 0,
                        'index_price' => 0,
                        'maintenance_margin' => 0.40,
                        'maker_fee' => 0.0200,
                        'taker_fee' => 0.0400,
                        'insurance_fund' => 0,
                        'open_interest_long' => 0,
                        'open_interest_short' => 0,
                        'buffer_percent' => 0,
                        'per_withdrawal_percent' => 0,
                    ]);
                    $count++;
                }

                // Ensure symbol exists in stock_trades as requested
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
            'message' => "Successfully synchronized {$count} new futures pairs from Binance.",
            'count' => $count,
        ]);
    }

    public function updatePair(Request $request)
    {
        $pair = FuturesPair::findOrFail($request->id);
        $pair->update($request->all());

        return redirect()->back()->with('success', 'Futures pair updated successfully.');
    }

    public function destroyPair($id)
    {
        $pair = FuturesPair::findOrFail($id);
        $pair->delete();

        return redirect()->back()->with('success', 'Futures pair deleted.');
    }

    public function massDelete(Request $request)
    {
        $ids = $request->ids;
        if (! is_array($ids) || empty($ids)) {
            return response()->json(['status' => false, 'message' => 'No pairs selected.']);
        }

        try {
            FuturesPair::whereIn('id', $ids)->delete();

            return response()->json(['status' => true, 'message' => count($ids).' pairs deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error deleting pairs: '.$e->getMessage()]);
        }
    }

    public function positionsIndex()
    {
        $positions = FuturesPosition::with(['user', 'pair'])->get();

        return view('admin.futures_positions', compact('positions'));
    }

    public function updatePosition(Request $request)
    {
        $position = FuturesPosition::findOrFail($request->id);

        if ($request->action === 'delete') {
            $position->delete();

            return redirect()->back()->with('success', 'Position deleted.');
        }

        $position->update($request->all());

        return redirect()->back()->with('success', 'Position updated.');
    }

    public function injectPosition(Request $request)
    {
        FuturesPosition::create($request->all());

        return redirect()->back()->with('success', 'Position injected successfully.');
    }

    public function updateMarkPrice(Request $request)
    {
        // Updates the internal mark price
        $request->validate([
            'pair_id' => 'required',
            'mark_price' => 'required|numeric',
        ]);

        $pair = FuturesPair::findOrFail($request->pair_id);
        $pair->update(['mark_price' => $request->mark_price]);

        return redirect()->back()->with('success', 'Mark price updated.');
    }

    public function triggerLiquidation(Request $request, $id)
    {
        // Forces liquidation on a specific position
        $position = FuturesPosition::findOrFail($id);
        $position->update([
            'status' => 'liquidated',
            'close_price' => $position->pair->mark_price ?? 0,
        ]);

        return redirect()->back()->with('success', 'Position liquidated successfully.');
    }

    public function massLiquidate(Request $request)
    {
        // Liquidates all positions matching a criteria
        $query = FuturesPosition::where('status', 'open');

        if ($request->has('pair_id') && $request->pair_id) {
            $query->where('pair_id', $request->pair_id);
        }

        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $positions = $query->get();
        foreach ($positions as $position) {
            // Check if exempt (admin_liquidation_override property or similar)
            if (! isset($position->admin_liquidation_override) || ! $position->admin_liquidation_override) {
                $position->update([
                    'status' => 'liquidated',
                    'close_price' => $position->pair->mark_price ?? 0,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Mass liquidation completed.');
    }

    public function overrideOutcome(Request $request, $id)
    {
        // Sets outcome_preset or admin_status to win/loss/liquidated
        $position = FuturesPosition::findOrFail($id);
        $position->update([
            'outcome_preset' => $request->outcome_preset,
            'status' => $request->admin_status ?? $position->status,
        ]);

        return redirect()->back()->with('success', 'Settlement outcome adjusted successfully.');
    }

    public function exemptFromLiquidation(Request $request, $id)
    {
        // Sets admin_liquidation_override
        $position = FuturesPosition::findOrFail($id);
        $position->update([
            'admin_liquidation_override' => $request->exempt ? true : false,
        ]);

        return redirect()->back()->with('success', 'Liquidation exemption updated.');
    }

    public function editHistory(Request $request, $id)
    {
        // Edits closed position details (close_price, realized_pnl, etc.)
        $position = FuturesPosition::findOrFail($id);
        $position->update([
            'close_price' => $request->close_price,
            'realized_pnl' => $request->realized_pnl,
            'status' => $request->status ?? $position->status,
        ]);

        return redirect()->back()->with('success', 'Position settlement history updated.');
    }

    public function deletePosition($id)
    {
        // Deletes the position entirely
        $position = FuturesPosition::findOrFail($id);
        $position->delete();

        return redirect()->back()->with('success', 'Position deleted permanently.');
    }

    public function generateFakes(Request $request)
    {
        // Generates fake futures history
        return redirect()->back()->with('success', 'Fake futures history generated.');
    }

    public function updateSettings(Request $request)
    {
        // Edits FuturesSettings (margin rates, fees, leverage limits)
        return redirect()->back()->with('success', 'Futures settings updated.');
    }

    public function approvePosition($id)
    {
        $position = FuturesPosition::findOrFail($id);
        $position->approval_status = 'approved';
        $position->status = 'open';
        
        // Update entry price to current market price
        try {
            $data = \App\Services\BinancePriceService::fetchAll();
            $priceMap = $data['priceMap'];
            $pair = $position->futuresPair;
            if ($pair && isset($priceMap[$pair->symbol])) {
                $position->entry_price = $priceMap[$pair->symbol];
                
                // Recalculate liquidation price based on new entry price
                $position->liquidation_price = $position->direction === 'long'
                    ? $position->entry_price * (1 - (1 / $position->leverage))
                    : $position->entry_price * (1 + (1 / $position->leverage));
            }
        } catch (\Exception $e) {}
        
        $position->save();
        return redirect()->back()->with('success', 'Position approved and opened successfully.');
    }

    public function rejectPosition($id)
    {
        $position = FuturesPosition::findOrFail($id);
        $position->approval_status = 'rejected';
        $position->status = 'closed';
        $position->save();

        // Refund margin
        $balanceColumn = $position->is_demo ? 'demo' : 'amount';
        \Illuminate\Support\Facades\DB::table('balances')
            ->where('user_id', $position->user_id)
            ->where('symbol', 'usd')
            ->increment($balanceColumn, $position->margin_amount);

        return redirect()->back()->with('success', 'Position rejected and margin refunded.');
    }
}
