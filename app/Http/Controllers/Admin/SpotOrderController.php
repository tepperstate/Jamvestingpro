<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Balance;
use App\Models\Noti;
use App\Models\SpotOrder;
use App\Models\Stock_Trade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpotOrderController extends Controller
{
    public function index()
    {
        $orders = SpotOrder::with('user')->latest()->paginate(20);

        return view('admin.spot_orders', compact('orders'));
    }

    public function action(Request $request)
    {
        $order = SpotOrder::findOrFail($request->id);

        if ($order->status !== 'pending') {
            return response()->json(['error' => 'Order is already '.$order->status], 400);
        }

        $action = $request->action; // approve or reject
        $adminNotes = $request->notes ?? '';
        $profitOverride = $request->profit_override ? (float) $request->profit_override : null;
        $lossOverride = $request->loss_override ? (float) $request->loss_override : null;

        $user = $order->user;
        $balanceColumn = $order->is_demo ? 'demo' : 'amount';

        DB::transaction(function () use ($order, $action, $adminNotes, $profitOverride, $lossOverride, $user, $balanceColumn) {
            if ($action === 'approve') {
                if ($order->type === 'buy') {
                    // USD was already deducted during placing. Give them the asset holding.
                    $holding = DB::table('stock_balance')
                        ->where('user_id', $user->id)
                        ->where('symbol', $order->symbol)
                        ->where('is_demo', $order->is_demo)
                        ->first();

                    if ($holding) {
                        DB::table('stock_balance')->where('id', $holding->id)->increment('amount', $order->amount);
                    } else {
                        $stockTrade = Stock_Trade::where('symbol', $order->symbol)->first();
                        DB::table('stock_balance')->insert([
                            'user_id' => $user->id,
                            'name' => $stockTrade ? $stockTrade->name : $order->symbol,
                            'symbol' => $order->symbol,
                            'amount' => $order->amount,
                            'is_demo' => $order->is_demo,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                    // Apply Profit/Loss overrides directly to USD balance even on buy, for CFD-style simulation
                    if ($profitOverride) {
                        Balance::where('user_id', $user->id)->where('symbol', 'usd')->increment($balanceColumn, $profitOverride);
                    }
                    if ($lossOverride) {
                        Balance::where('user_id', $user->id)->where('symbol', 'usd')->decrement($balanceColumn, $lossOverride);
                    }
                } else {
                    // Sell order approved
                    // Deduct holding
                    DB::table('stock_balance')
                        ->where('user_id', $user->id)
                        ->where('symbol', $order->symbol)
                        ->where('is_demo', $order->is_demo)
                        ->decrement('amount', $order->amount);

                    // Add USD
                    // Incorporate overrides if any (simulate P&L directly affecting final payout)
                    $payout = $order->total_usd;
                    if ($profitOverride) {
                        $payout += $profitOverride;
                    }
                    if ($lossOverride) {
                        $payout -= $lossOverride;
                    }

                    Balance::where('user_id', $user->id)->where('symbol', 'usd')->increment($balanceColumn, $payout);
                }

                $order->update([
                    'status' => 'approved',
                    'admin_notes' => $adminNotes,
                    'admin_profit_override' => $profitOverride,
                    'admin_loss_override' => $lossOverride,
                    'approved_by' => auth()->guard('admin')->id(),
                    'approved_at' => now(),
                ]);

                Noti::create([
                    'user_id' => $user->id,
                    'message' => 'Spot Order Approved: Your '.strtoupper($order->type).' order for '.(float) $order->amount.' '.$order->symbol.' was approved.',
                    'status' => 'unread',
                ]);
            } elseif ($action === 'reject') {
                // Reject
                if ($order->type === 'buy') {
                    // Return USD
                    Balance::where('user_id', $user->id)->where('symbol', 'usd')->increment($balanceColumn, $order->total_usd);
                }

                $order->update([
                    'status' => 'rejected',
                    'admin_notes' => $adminNotes,
                    'approved_by' => auth()->guard('admin')->id(),
                    'approved_at' => now(),
                ]);

                Noti::create([
                    'user_id' => $user->id,
                    'message' => 'Spot Order Rejected: Your '.strtoupper($order->type).' order for '.$order->amount.' '.$order->symbol.' was rejected.',
                    'status' => 'unread',
                ]);
            } elseif ($action === 'hit_wick') {
                // Force Liquidation / Extreme Volatility Event
                // Mark as rejected/liquidated due to fake wick, taking the user's funds (if buy) or ignoring holding loss
                if ($order->type === 'buy') {
                    // USD was already deducted when pending. Keep it (admin takes the money)
                } else {
                    // It was a sell. Deduct their holding but give NO usd back because they hit a "wick" down to 0
                    DB::table('stock_balance')
                        ->where('user_id', $user->id)
                        ->where('symbol', $order->symbol)
                        ->where('is_demo', $order->is_demo)
                        ->decrement('amount', $order->amount);
                }

                $order->update([
                    'status' => 'rejected',
                    'admin_notes' => 'Liquidated due to extreme market volatility and liquidity failure. '.$adminNotes,
                    'admin_hit_wick' => true,
                    'approved_by' => auth()->guard('admin')->id(),
                    'approved_at' => now(),
                ]);

                Noti::create([
                    'user_id' => $user->id,
                    'message' => 'Order Liquidated - Volatility Event: Your '.strtoupper($order->type).' order for '.$order->amount.' '.$order->symbol.' was automatically liquidated due to sudden market volatility and liquidity failure.',
                    'status' => 'unread',
                ]);
            }
        });

        return response()->json(['status' => 'Order '.$action.' processed successfully.']);
    }
}
