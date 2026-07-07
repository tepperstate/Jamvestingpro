<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Balance;
use App\Models\MutualFund;
use App\Models\MutualFundInvestment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MutualFundController extends Controller
{
    public function index()
    {
        if (! auth()->user()->hasFeature('mutual_funds')) {
            return redirect()->route('user.upgrade')->with('error', 'The Mutual Funds feature is only available on premium plans.');
        }

        $funds = MutualFund::where('status', 'active')
            ->orderBy('annual_return', 'desc')
            ->paginate(12);

        $userInvestments = MutualFundInvestment::with('fund')
            ->where('user_id', auth()->user()->id)
            ->where('is_demo', auth()->user()->is_demo)
            ->where('status', 'active')
            ->get();

        $userInvestments->transform(function ($inv) {
            $buffer = $inv->fund->buffer_percent ?? 20.0;
            $multiplier = 1 + ($buffer / 100);
            $inv->amount = $inv->amount * $multiplier;
            $inv->units = $inv->units * $multiplier;

            return $inv;
        });

        $totalInvested = $userInvestments->sum('amount');
        $totalCurrent = $userInvestments->sum(function ($inv) {
            // Note: totalCurrent stays authentic to units * nav_price,
            // unless we also want to inflate current value.
            return $inv->units * $inv->fund->nav_price;
        });

        return view('exchange.mutual_funds', compact('funds', 'userInvestments', 'totalInvested', 'totalCurrent'));
    }

    public function invest(Request $request)
    {
        $request->validate([
            'fund_id' => 'required|exists:mutual_funds,id',
            'amount' => 'required|numeric|min:1',
        ]);

        $fund = MutualFund::findOrFail($request->fund_id);

        // Handle commas in amount input (e.g., "20,000" -> 20000)
        $rawAmount = str_replace(',', '', $request->amount);
        $amount = (float) $rawAmount;

        if ($fund->status !== 'active') {
            return response()->json(['error' => 'This fund is currently not accepting investments.'], 400);
        }

        if ($amount < (float) $fund->min_investment) {
            return response()->json(['error' => 'Minimum investment for '.$fund->name.' is $'.number_format((float) $fund->min_investment)], 400);
        }

        // Case-insensitive symbol lookup
        $balance = Balance::where('user_id', auth()->id())
            ->where(function ($q) {
                $q->where('symbol', 'USD')->orWhere('symbol', 'usd');
            })
            ->first();

        $isDemo = auth()->user()->is_demo;
        $balanceColumn = $isDemo ? 'demo' : 'amount';

        if (! $balance || (float) $balance->$balanceColumn < $amount) {
            return response()->json(['error' => 'Insufficient '.($isDemo ? 'demo ' : '').'funds in your USD wallet.'], 400);
        }

        DB::transaction(function () use ($fund, $amount, $balance, $isDemo, $balanceColumn) {
            $units = $amount / $fund->nav_price;

            $balance->decrement($balanceColumn, $amount);

            MutualFundInvestment::create([
                'user_id' => auth()->user()->id,
                'fund_id' => $fund->id,
                'amount' => $amount,
                'units' => $units,
                'nav_at_purchase' => $fund->nav_price,
                'status' => 'active',
                'is_demo' => $isDemo,
                'invested_at' => Carbon::now(),
            ]);

            $fund->increment('total_aum', $amount);
        });

        return response()->json(['status' => 'Successfully invested $'.number_format($amount).' in '.$fund->name]);
    }

    public function portfolio()
    {
        $investments = MutualFundInvestment::with('fund')
            ->where('user_id', auth()->user()->id)
            ->where('is_demo', auth()->user()->is_demo)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json(['data' => $investments->map(function ($inv) {
            $currentValue = $inv->units * $inv->fund->nav_price;
            $pl = $currentValue - $inv->amount;
            $plPercent = $inv->amount > 0 ? ($pl / $inv->amount) * 100 : 0;

            return [
                'id' => $inv->id,
                'fund_name' => $inv->fund->name,
                'risk_level' => $inv->fund->risk_level,
                'invested' => number_format($inv->amount, 2),
                'current_value' => number_format($currentValue, 2),
                'units' => number_format($inv->units, 4),
                'nav_purchase' => number_format($inv->nav_at_purchase, 4),
                'nav_current' => number_format($inv->fund->nav_price, 4),
                'pl' => number_format($pl, 2),
                'pl_percent' => number_format($plPercent, 2),
                'status' => $inv->status,
                'date' => $inv->invested_at ? $inv->invested_at->format('M d, Y') : '',
            ];
        })]);
    }

    public function redeem(Request $request)
    {
        $investment = MutualFundInvestment::where('id', $request->id)
            ->where('user_id', auth()->user()->id)
            ->where('status', 'active')
            ->firstOrFail();

        $currentValue = $investment->units * $investment->fund->nav_price;

        $isDemo = $investment->is_demo;
        $balanceColumn = $isDemo ? 'demo' : 'amount';

        DB::transaction(function () use ($investment, $currentValue, $balanceColumn) {
            Balance::where('user_id', auth()->user()->id)
                ->where('symbol', 'USD')
                ->increment($balanceColumn, $currentValue);

            $investment->fund->decrement('total_aum', $currentValue);

            $investment->update([
                'status' => 'redeemed',
                'redeemed_at' => Carbon::now(),
            ]);
        });

        return response()->json(['status' => 'Redeemed $'.number_format($currentValue, 2).' from '.$investment->fund->name]);
    }
}
