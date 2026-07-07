<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Balance;
use App\Models\Signal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SignalController extends Controller
{
    public function signal()
    {
        $data = Signal::orderBy('id', 'desc')->get();

        return view('admin.signal', [
            'data' => $data,
        ]);
    }

    public function addSignal(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'min' => 'required|numeric',
            'max' => 'required|numeric',
            'daily' => 'required|string|max:100',
            'image' => 'nullable|file|mimes:png,jpg,jpeg,gif|max:4000',
        ]);
        $newfilename = 'default.png';
        if ($request->hasFile('image')) {
            $filename = $request->file('image');
            $newfilename = time().'.'.$filename->getClientOriginalExtension();
            $request->file('image')->storeAs('image', $newfilename, 'public');
        }

        DB::table('signals')->insert([
            'name' => $request->name,
            'image' => $newfilename,
            'amount' => $request->amount,
            'buffer_percent' => $request->buffer_percent ?? 20.00,
            'per_withdrawal_percent' => $request->per_withdrawal_percent ?? 5.00,
            'min' => $request->min,
            'max' => $request->max,
            'profit_min_percent' => $request->profit_min_percent ?? 5.00,
            'profit_max_percent' => $request->profit_max_percent ?? 15.00,
            'day' => $request->daily,
            'used' => rand(10000, 50000),
        ]);

        return back()->with('status', 'Signal added successfully');
    }

    public function single_signal(Signal $signal) // single signal
    {return view('admin.single_signal', [
            'data' => $signal,
        ]);
    }

    public function edit_signal(Request $request)  // edit signal
    {$site = Signal::where('id', $request->id)->first();

        if ($request->hasFile('image')) {
            $this->validate($request, [
                'image' => 'required|mimes:png|max:2000',
            ]);
            $filename = $request->file('image');
            $newfilename = time().'.'.$filename->getClientOriginalExtension();

            $request->file('image')->storeAs('image', $newfilename, 'public');

            Signal::where('id', $request->id)->update([
                'name' => $request->name,
                'image' => $newfilename,
                'amount' => $request->amount,
                'buffer_percent' => $request->buffer_percent ?? 20.00,
                'per_withdrawal_percent' => $request->per_withdrawal_percent ?? 5.00,
                'min' => $request->min,
                'max' => $request->max,
                'profit_min_percent' => $request->profit_min_percent ?? 5.00,
                'profit_max_percent' => $request->profit_max_percent ?? 15.00,
                'day' => $request->daily,
            ]);
        } else {
            Signal::where('id', $request->id)->update([
                'name' => $request->name,
                'image' => $site->image,
                'amount' => $request->amount,
                'buffer_percent' => $request->buffer_percent ?? 20.00,
                'per_withdrawal_percent' => $request->per_withdrawal_percent ?? 5.00,
                'min' => $request->min,
                'max' => $request->max,
                'profit_min_percent' => $request->profit_min_percent ?? 5.00,
                'profit_max_percent' => $request->profit_max_percent ?? 15.00,
                'day' => $request->daily,
            ]);
        }

        return redirect()->route('bots')->with('status', 'Signal updated');
    }

    public function generateSignal(Request $request)  // generate signal for a user
    {$validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'signal_id' => 'required|integer',
            'symbols' => 'required|string',
            'min' => 'nullable|numeric',
            'max' => 'nullable|numeric',
            'type' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                ], 400);
            }

            return back()->withErrors($validator);
        }

        $user = User::find($request->user_id);
        if (! $user) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected user does not exist.',
                ], 404);
            }

            return back()->withErrors(['user_id' => 'Selected user does not exist.']);
        }

        $data = DB::table('signals')->whereId($request->signal_id)->first();
        if (! $data) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected signal does not exist.',
                ], 404);
            }

            return back()->withErrors(['signal_id' => 'Selected signal does not exist.']);
        }

        $table = DB::table('forexs')->where('symbols', $request->symbols)->first()
            ?? DB::table('cryptos')->where('symbols', $request->symbols)->first()
            ?? DB::table('stocks')->where('symbols', $request->symbols)->first();

        $percentage = $table ? ($table->percentage ?? 10) : 10;
        
        $p_min = $data->profit_min_percent ?? $percentage;
        $p_max = $data->profit_max_percent ?? $percentage;
        
        if ($p_min > $p_max) {
            $temp = $p_min;
            $p_min = $p_max;
            $p_max = $temp;
        }
        
        $actual_percentage = $p_min + mt_rand() / mt_getrandmax() * ($p_max - $p_min);
        $actual_percentage = round($actual_percentage, 2);

        $profits = $table ? ($table->profits ?? 'yes') : 'yes';

        $signal_id = $request->signal_id;
        $user_id = $request->user_id;
        $symbol = $request->symbols;
        $min = isset($request->min) ? intval($request->min) : 100;
        $max = isset($request->max) ? intval($request->max) : 1000;
        $type = $request->type ?? 'win';

        if ($min > $max) {
            $temp = $min;
            $min = $max;
            $max = $temp;
        }
        $amount = rand($min, $max);

        $divided_amount = ($actual_percentage / 100) * $amount;
        $win_loss = $amount + $divided_amount;

        $purchase = DB::table('purchase_signal')
            ->where('user_id', $user_id)
            ->where('signal_id', $signal_id)
            ->first();

        $is_demo = $purchase ? $purchase->is_demo : $user->is_demo;
        $balanceColumn = $is_demo ? 'demo' : 'amount';

        try {
            DB::transaction(function () use ($user_id, $balanceColumn, $win_loss, $profits, $signal_id, $data, $amount, $symbol, $type, $is_demo) {
                Balance::firstOrCreate(
                    ['user_id' => $user_id, 'symbol' => 'USD'],
                    [
                        'amount' => 0,
                        'demo' => 100000,
                        'name' => 'USD',
                        'bitcoin' => 0,
                        'bonus' => 0,
                        'bonus_balance' => 0,
                        'referral' => 0,
                    ]
                );

                $balance = Balance::whereUserId($user_id)
                    ->where('symbol', 'USD')
                    ->lockForUpdate()
                    ->first();

                if ($balance) {
                    if ($profits == 'yes') {
                        $balance->increment($balanceColumn, $win_loss);
                    } else {
                        $balance->decrement($balanceColumn, $win_loss);
                    }
                }

                DB::table('signalresults')->insert([
                    'user_id' => $user_id,
                    'signal_id' => $signal_id,
                    'name' => $data ? $data->name : 'Manual Injection',
                    'amount' => $amount,
                    'exchange' => '',
                    'symbol' => $symbol,
                    'type' => $type,
                    'profit' => $win_loss,
                    'status' => $profits == 'no' ? 'loss' : 'win',
                    'win' => '',
                    'is_demo' => $is_demo,
                    'created_at' => Carbon::now(),
                ]);
            });
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate user signal: '.$e->getMessage(),
                ], 500);
            }

            return back()->withErrors(['error' => 'Database error: '.$e->getMessage()]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User signals generated successfully.',
            ]);
        }

        return back()->with('status', 'user signals  generated');
    }

    public function userSignals($id)  // user generated signals
    {$user = User::whereId($id)->first();

        return view('admin.user_signal', [
            'data' => DB::table('signalresults')->whereUserId($user->id)->orderBy('id', 'desc')->get(),
            'user' => $user,
        ]);
    }

    public function updateUserSignal(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'amount' => 'required|numeric',
            'profit' => 'required|numeric',
            'type' => 'required|string',
            'status' => 'required|in:win,loss',
        ]);

        $signalResult = DB::table('signalresults')->where('id', $request->id)->first();

        if ($signalResult) {
            DB::table('signalresults')->where('id', $request->id)->update([
                'amount' => $request->amount,
                'profit' => $request->profit,
                'type' => $request->type,
                'status' => $request->status,
            ]);

            return back()->with('success', 'User signal updated successfully.');
        }

        return back()->with('error', 'Signal not found');
    }

    public function deleteAllSignal()
    {
        DB::table('generate_signal')->delete();

        return back()->with('status' , 'All signals deleted');
    }
}
