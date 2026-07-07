<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Balance;
use App\Models\Noti;
use App\Models\StakingPlan;
use App\Models\StakingPosition;
use App\Models\UserWallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StakingController extends Controller
{
    public function index()
    {
        $plans = StakingPlan::where('status', 'active')->orderBy('apy_percentage', 'asc')->paginate(12);
        $positions = StakingPosition::where('user_id', auth()->id())
            ->where('is_demo', auth()->user()->is_demo)
            ->with('plan')
            ->latest()
            ->get();

        $positions->transform(function ($pos) {
            $buffer = $pos->plan->buffer_percent ?? 20.0;
            $multiplier = 1 + ($buffer / 100);
            $pos->amount = $pos->amount * $multiplier;
            $pos->earned = $pos->earned * $multiplier;

            return $pos;
        });

        return view('exchange.staking', compact('plans', 'positions'));
    }

    public function stake(Request $request)
    {
        $plan = StakingPlan::findOrFail($request->id);
        $amount = (float) $request->amount;

        if ($amount < $plan->min_amount) {
            return response()->json(['error' => 'Minimum stake for '.$plan->name.' is $'.number_format($plan->min_amount)], 400);
        }

        if ($amount > $plan->max_amount) {
            return response()->json(['error' => 'Maximum stake for '.$plan->name.' is $'.number_format($plan->max_amount)], 400);
        }

        $user = auth()->user();
        $isDemo = $user->is_demo;
        $balanceColumn = $isDemo ? 'demo' : 'amount';

        $symbol = strtoupper($plan->symbol ?? 'USD');

        if ($symbol === 'USD') {
            $balance = Balance::where('user_id', $user->id)->where('symbol', 'usd')->first();
            if (! $balance || $amount > $balance->$balanceColumn) {
                return response()->json(['error' => 'Insufficient USD funds'], 400);
            }
        } else {
            $balance = UserWallet::where('user_id', $user->id)
                ->where('coin_symbol', $symbol)
                ->first();
            if (! $balance || $balance->balance < $amount) {
                return response()->json(['error' => 'Insufficient '.$symbol.' funds'], 400);
            }
        }

        DB::transaction(function () use ($user, $balance, $plan, $amount, $balanceColumn, $isDemo, $symbol) {
            if ($symbol === 'USD') {
                $balance->decrement($balanceColumn, $amount);
            } else {
                $balance->decrement('balance', $amount);
            }

            StakingPosition::create([
                'user_id' => $user->id,
                'staking_plan_id' => $plan->id,
                'amount' => $amount,
                'earned' => 0,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addDays($plan->lock_days)->toDateString(),
                'status' => 'active',
                'is_demo' => $isDemo,
            ]);

            Noti::create([
                'user_id' => $user->id,
                'title' => 'Staking Position Opened',
                'message' => 'You staked $'.number_format($amount, 2).' in '.$plan->name.' at '.$plan->apy_percentage.'% APY for '.$plan->lock_days.' days.',
                'status' => 'unread',
            ]);
        });

        return response()->json(['status' => 'Staking position opened successfully! '.$plan->apy_percentage.'% APY locked for '.$plan->lock_days.' days.']);
    }

    public function unstake(Request $request)
    {
        $position = StakingPosition::where('id', $request->id)
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->firstOrFail();

        // Enforce lock period — block unstaking before end_date
        $endDate = Carbon::parse($position->end_date);
        if (! now()->gte($endDate)) {
            $daysLeft = now()->diffInDays($endDate, false);

            return response()->json([
                'error' => 'This position is locked for '.ceil($daysLeft).' more days. Unstaking is available after '.$endDate->format('M d, Y').'.',
            ], 400);
        }

        $user = auth()->user();
        $isDemo = $user->is_demo;
        $balanceColumn = $isDemo ? 'demo' : 'amount';

        $refundAmount = $position->amount + $position->earned;
        $plan = $position->plan;
        $symbol = strtoupper($plan->symbol ?? 'USD');

        DB::transaction(function () use ($position, $refundAmount, $balanceColumn, $user, $symbol) {
            if ($symbol === 'USD') {
                Balance::where('user_id', $user->id)->where('symbol', 'usd')->increment($balanceColumn, $refundAmount);
            } else {
                $wallet = UserWallet::firstOrCreate(
                    ['user_id' => $user->id, 'coin_symbol' => $symbol],
                    ['balance' => 0, 'is_enabled' => true]
                );
                $wallet->increment('balance', $refundAmount);
            }
            $position->update(['status' => 'completed']);
        });

        $msg = 'Staking completed! $'.number_format($refundAmount, 2).' (capital + yield) returned to your wallet.';

        return response()->json(['status' => $msg]);
    }
}
