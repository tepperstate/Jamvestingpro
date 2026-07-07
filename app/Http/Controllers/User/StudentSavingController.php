<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Balance;
use App\Models\Noti;
use App\Models\StudentPlan;
use App\Models\StudentSaving;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentSavingController extends Controller
{
    public function index()
    {
        $plans = StudentPlan::where('status', 'active')->orderBy('tier', 'asc')->get();
        $savings = StudentSaving::where('user_id', auth()->id())
            ->where('is_demo', auth()->user()->is_demo)
            ->with('plan')
            ->latest()
            ->get();

        $savings->transform(function ($save) {
            $buffer = $save->plan->buffer_percent ?? 20.0;
            $multiplier = 1 + ($buffer / 100);
            $save->amount = $save->amount * $multiplier;
            $save->earned = $save->earned * $multiplier;

            return $save;
        });

        return view('exchange.student_savings', compact('plans', 'savings'));
    }

    public function store(Request $request)
    {
        $plan = StudentPlan::findOrFail($request->id);
        $amount = (float) $request->amount;

        if ($amount < $plan->min_amount || $amount > $plan->max_amount) {
            return response()->json(['error' => 'Amount must be between $'.number_format($plan->min_amount).' and $'.number_format($plan->max_amount)], 400);
        }

        $user = auth()->user();
        $isDemo = $user->is_demo;
        $balanceColumn = $isDemo ? 'demo' : 'amount';
        $balance = Balance::where('user_id', $user->id)->where('symbol', 'usd')->first();

        if (! $balance || $amount > $balance->$balanceColumn) {
            return response()->json(['error' => 'Insufficient funds'], 400);
        }

        DB::transaction(function () use ($user, $balance, $plan, $amount, $balanceColumn, $isDemo) {
            $balance->decrement($balanceColumn, $amount);

            StudentSaving::create([
                'user_id' => $user->id,
                'student_plan_id' => $plan->id,
                'amount' => $amount,
                'earned' => 0,
                'start_date' => now()->toDateString(),
                'maturity_date' => now()->addMonths($plan->duration_months)->toDateString(),
                'status' => 'active',
                'is_demo' => $isDemo,
            ]);

            Noti::create([
                'user_id' => $user->id,
                'title' => 'Student Savings Activated',
                'message' => 'You deposited $'.number_format($amount, 2).' into the '.$plan->name.' plan at '.$plan->interest_rate.'% APY.',
                'status' => 'unread',
            ]);
        });

        return response()->json(['status' => $plan->name.' savings account opened! '.$plan->interest_rate.'% APY for '.$plan->duration_months.' months.']);
    }
}
