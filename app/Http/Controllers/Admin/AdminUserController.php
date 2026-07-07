<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Corder;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function executeTrade(Request $request)
    {
        try {
            // Validate required numeric inputs to prevent rand() failure
            $minAmount = intval($request->min_amount ?? 0);
            $maxAmount = intval($request->max_amount ?? 0);

            if ($maxAmount < $minAmount) {
                return response()->json([
                    'status' => false,
                    'message' => 'Max amount cannot be less than min amount.',
                ], 400);
            }

            $asset = Asset::where('symbols', $request->asset)->first();

            if (! $asset) {
                return response()->json([
                    'status' => false,
                    'message' => 'Asset security ['.$request->asset.'] not found in system database. Please verify asset symbol.',
                ], 404);
            }

            if (! $request->user_id || ! is_array($request->user_id)) {
                return response()->json([
                    'status' => false,
                    'message' => 'No valid users selected for trade execution.',
                ], 400);
            }

            foreach ($request->user_id as $user_id) {
                $amount = rand($minAmount, $maxAmount);
                $corder = Corder::where('user_id', $user_id)->where('trader_name', $request->trader)->first();
                $auto_renew = $corder ? $corder->is_auto_renew : 0;

                Copy_trade_order::create([
                    'user_id' => $user_id,
                    'exchange' => $asset->exchanges_id,
                    'asset_id' => $asset->id,
                    'trader_name' => $request->trader,
                    'symbol' => $request->asset,
                    'amount' => $amount,
                    'win' => $this->asset($request->asset),
                    'loss' => $this->asset_loss($request->asset),
                    'expire_time' => $formattedValue,
                    'time' => $request->expiretime,
                    'expire_date' => Carbon::now()->addMinutes(intval($request->expiretime)),
                    'status' => 'pending',
                    'type' => $request->type,
                    'types' => 'live',
                    'traded_date' => Carbon::now(),
                    'admin_status' => $request->result,
                    'is_auto_renew' => $auto_renew,
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Trade successfully executed for '.count($request->user_id).' user(s).',
            ]);

        } catch (\Exception $e) {
            \Log::error('Admin Trade Execution Failed: '.$e->getMessage());

            return response()->json([
                'message' => 'Execution failed: '.$e->getMessage(),
            ], 500);
        }
    }

    public function copy_trades_index()
    {
        return view('admin.copy_trades');
    }

    public function all_copy_trades()
    {
        $data = Copy_trade_order::with(['user', 'exchanges', 'asset'])->orderBy('id', 'desc')->limit(20)->get();

        return response()->json($data);
    }

    public function bot_trades_index()
    {
        return view('admin.bot_trades');
    }

    public function all_bot_trades() {}
}
