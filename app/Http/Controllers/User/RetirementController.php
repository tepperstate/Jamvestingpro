<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Balance;
use App\Models\Noti;
use App\Models\RetirementAccount;
use App\Models\RetirementPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RetirementController extends Controller
{
    public function index()
    {
        $plans = RetirementPlan::where('status', 'active')->orderBy('tier', 'asc')->get();
        $accounts = RetirementAccount::where('user_id', auth()->id())
            ->where('is_demo', auth()->user()->is_demo)
            ->with('plan')
            ->latest()
            ->get();

        $accounts->transform(function ($acc) {
            $buffer = $acc->plan->buffer_percent ?? 20.0;
            $multiplier = 1 + ($buffer / 100);
            $acc->balance = $acc->balance * $multiplier;
            $acc->employee_contributions = $acc->employee_contributions * $multiplier;
            $acc->employer_contributions = $acc->employer_contributions * $multiplier;
            $acc->vested_amount = $acc->vested_amount * $multiplier;

            return $acc;
        });

        return view('exchange.retirement', compact('plans', 'accounts'));
    }

    public function contribute(Request $request)
    {
        $plan = RetirementPlan::findOrFail($request->id);
        $amount = (float) $request->amount;

        if ($amount < $plan->min_contribution || $amount > $plan->max_contribution) {
            return response()->json(['error' => 'Contribution must be between $'.number_format($plan->min_contribution).' and $'.number_format($plan->max_contribution)], 400);
        }

        $user = auth()->user();
        $isDemo = $user->is_demo;
        $balanceColumn = $isDemo ? 'demo' : 'amount';
        $balance = Balance::where('user_id', $user->id)->where('symbol', 'usd')->first();

        if (! $balance || $amount > $balance->$balanceColumn) {
            return response()->json(['error' => 'Insufficient funds'], 400);
        }

        $employerMatch = $amount * ($plan->employer_match_pct / 100);

        DB::transaction(function () use ($user, $balance, $plan, $amount, $balanceColumn, $isDemo, $employerMatch) {
            $balance->decrement($balanceColumn, $amount);

            // Check for existing account
            $existing = RetirementAccount::where('user_id', $user->id)
                ->where('retirement_plan_id', $plan->id)
                ->where('is_demo', $isDemo)
                ->first();

            if ($existing) {
                $existing->increment('employee_contributions', $amount);
                $existing->increment('employer_contributions', $employerMatch);
                $existing->increment('balance', $amount + $employerMatch);
            } else {
                RetirementAccount::create([
                    'user_id' => $user->id,
                    'retirement_plan_id' => $plan->id,
                    'balance' => $amount + $employerMatch,
                    'employer_contributions' => $employerMatch,
                    'employee_contributions' => $amount,
                    'vested_amount' => $plan->vesting_schedule === 'immediate' ? ($amount + $employerMatch) : $amount,
                    'start_date' => now()->toDateString(),
                    'status' => 'active',
                    'is_demo' => $isDemo,
                ]);
            }

            Noti::create([
                'user_id' => $user->id,
                'title' => '401(k) Contribution Recorded',
                'message' => 'You contributed $'.number_format($amount, 2).' to '.$plan->name.'. Employer matched $'.number_format($employerMatch, 2).'.',
                'status' => 'unread',
            ]);
        });

        return response()->json(['status' => 'Contribution of $'.number_format($amount, 2).' recorded. Employer match: $'.number_format($employerMatch, 2).'.']);
    }
}
