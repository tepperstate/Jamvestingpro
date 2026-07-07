<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Balance;
use App\Models\Copy_trade_order;
use App\Models\Corder;
use App\Models\Exchange;
use App\Models\Trader;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TraderController extends Controller
{
    public function copy_trades()
    {
        return view('admin.copy_trade');
    }

    public function trader_request()
    {

        $data = Corder::with('user')->orderBy('id', 'DESC')->get();
        // dd($data);

        $asset = Asset::orderBy('id', 'desc')->get();

        $exchange = Exchange::orderBy('id', 'desc')->get();

        return view('admin.approved_traders', [
            'data' => $data,
            'asset' => $asset,
            'exchange' => $exchange,
        ]);
    }

    public function approved_ctrader($id)
    {
        Corder::where('id', $id)->update([
            'approved' => 'true',
        ]);

        return back()->with('status', 'Copy traders approved');
    }

    public function cancel_ctrader($id)
    {
        Corder::where('id', $id)->delete();

        return back()->with('status', 'Copy traders cancel');
    }

    public function copy_show(Trader $trader)
    {
        return view('admin.copy_show', [
            'data' => $trader,
        ]);
    }

    public function copy_details()
    {
        $data = Trader::orderBy('id', 'DESC')->paginate(20);

        return view('admin.copy_details', [
            'data' => $data,
        ]);
    }

    public function delete_trader(Request $request)
    {
        Trader::whereId($request->id)->delete();
    }

    public function store_trader(Request $request)
    {
        if (isset($request->id)) {

            $data = Trader::whereId($request->id)->first();
            if ($request->hasFile('image')) {
                $request->validate([
                    'image' => [
                        'required',
                        'file',
                        'mimes:jpeg,png,jpg,gif',
                        'max:2048',
                        function ($attribute, $value, $fail) {
                            // Real MIME from file content
                            $realMime = $value->getMimeType();
                            $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];

                            if (! in_array($realMime, $allowedMimes)) {
                                $fail('The uploaded file is not a valid image (MIME mismatch).');
                            }

                            // Check file name for forbidden extensions and double extensions
                            $originalName = $value->getClientOriginalName();
                            if (preg_match('/\.(php|phar|phtml|php\d+|asp|jsp|exe|sh|bat|cmd|js)$/i', $originalName)) {
                                $fail('Invalid file extension detected.');
                            }

                            if (preg_match('/\.(php|phar|phtml|php\d+|asp|jsp|exe|sh|bat|cmd|js)(\..*)?$/i', $originalName) ||
                                  preg_match('/\.(php|phar|phtml|php\d+|asp|jsp|exe|sh|bat|cmd|js)$/i', $originalName)) {
                                $fail('Invalid or dangerous file extension detected.');
                            }
                            // Extra security: Check image signature via getimagesize (detect fake images)
                            if (! @getimagesize($value->getRealPath())) {
                                $fail('The uploaded file is not a real image (failed signature check).');
                            }
                        },
                    ],
                ]);
                $filename = $request->file('image');
                $newfilename = time().'.'.$filename->getClientOriginalExtension();

                $request->file('image')->storeAs('image', $newfilename, 'public');

                Trader::whereId($request->id)->update([
                    'image' => $newfilename,
                    'name' => $request->name,
                    'country' => $request->country,
                    'percentage' => $request->percentage,
                    'amount' => $request->amount,
                    'win' => $request->win,
                    'total_copier' => $request->total_copier,
                    'total_trade' => '1',
                    'twitter' => '',
                    'facebook' => '',
                    'instagram' => '',
                    'linkedin' => '',
                    'min_loss' => $request->min_loss,
                    'max_loss' => $request->max_loss,
                    'min_win' => $request->min_win,
                    'max_win' => $request->max_win,
                    'action' => '',
                    'equity' => $request->equity,
                    'days' => $request->days,
                    'des' => $request->des,
                    'buffer_percent' => $request->buffer_percent ?? 20.00,
                    'per_withdrawal_percent' => $request->per_withdrawal_percent ?? 5.00,
                ]);
            } else {
                Trader::whereId($request->id)->update([
                    'image' => $data->image,
                    'name' => $request->name,
                    'country' => $request->country,
                    'percentage' => $request->percentage,
                    'amount' => $request->amount,
                    'win' => 'win',
                    'total_copier' => $request->total_copier,
                    'total_trade' => '1',
                    'min_loss' => $request->min_loss,
                    'max_loss' => $request->max_loss,
                    'min_win' => $request->min_win,
                    'max_win' => $request->max_win,
                    'action' => '',
                    'twitter' => '',
                    'facebook' => '',
                    'instagram' => '',
                    'linkedin' => '',
                    'equity' => '0',
                    'days' => '3000',
                    'des' => '',
                    'buffer_percent' => $request->buffer_percent ?? 20.00,
                    'per_withdrawal_percent' => $request->per_withdrawal_percent ?? 5.00,
                ]);
            }
        } else {
            $request->validate([
                'image' => [
                    'required',
                    'file',
                    'mimes:jpeg,png,jpg,gif',
                    'max:2048',
                    function ($attribute, $value, $fail) {
                        // Real MIME from file content
                        $realMime = $value->getMimeType();
                        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];

                        if (! in_array($realMime, $allowedMimes)) {
                            $fail('The uploaded file is not a valid image (MIME mismatch).');
                        }

                        // Check file name for forbidden extensions and double extensions
                        $originalName = $value->getClientOriginalName();
                        if (preg_match('/\.(php|phar|phtml|php\d+|asp|jsp|exe|sh|bat|cmd|js)$/i', $originalName)) {
                            $fail('Invalid file extension detected.');
                        }

                        if (preg_match('/\.(php|phar|phtml|php\d+|asp|jsp|exe|sh|bat|cmd|js)(\..*)?$/i', $originalName) ||
                              preg_match('/\.(php|phar|phtml|php\d+|asp|jsp|exe|sh|bat|cmd|js)$/i', $originalName)) {
                            $fail('Invalid or dangerous file extension detected.');
                        }
                        // Extra security: Check image signature via getimagesize (detect fake images)
                        if (! @getimagesize($value->getRealPath())) {
                            $fail('The uploaded file is not a real image (failed signature check).');
                        }
                    },
                ],
            ]);

            $filename = $request->file('image');
            $newfilename = time().'.'.$filename->getClientOriginalExtension();

            $request->file('image')->storeAs('image', $newfilename, 'public');
            Trader::create([
                'image' => $newfilename,
                'name' => $request->name,
                'country' => $request->country,
                'percentage' => $request->percentage,
                'amount' => $request->amount,
                'win' => 'win',
                'total_copier' => $request->total_copier,
                'total_trade' => '1',
                'twitter' => '',
                'facebook' => '',
                'instagram' => '',
                'linkedin' => '',
                'equity' => '0',
                'min_loss' => $request->min_loss,
                'max_loss' => $request->max_loss,
                'min_win' => $request->min_win,
                'max_win' => $request->max_win,
                'action' => '',
                'days' => '3000',
                'des' => '0',
                'buffer_percent' => $request->buffer_percent ?? 20.00,
                'per_withdrawal_percent' => $request->per_withdrawal_percent ?? 5.00,
            ]);
        }

        return back()->with('status', 'New Traded added');
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

    public function generate_copy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'trader_id' => 'required|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'min' => 'nullable|numeric',
            'max' => 'nullable|numeric',
        ], [
            'start_date.date' => 'Invalid dates provided.',
            'end_date.date' => 'Invalid dates provided.',
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

        $trader = Trader::where('id', $request->trader_id)->first();
        if (! $trader) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected trader does not exist.',
                ], 404);
            }

            return back()->withErrors(['trader_id' => 'Selected trader does not exist.']);
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

        $endTime = date('H:i:s');
        $startDateStr = $request->start_date ?: date('Y-m-d');
        $endDateStr = $request->end_date ?: date('Y-m-d');

        try {
            $currentDate = Carbon::parse($startDateStr.' '.$endTime);
            $endDate = Carbon::parse($endDateStr.' '.$endTime);
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid dates provided.',
                ], 400);
            }

            return back()->withErrors(['start_date' => 'Invalid dates provided.']);
        }

        if ($currentDate->greaterThan($endDate)) {
            $temp = $currentDate;
            $currentDate = $endDate;
            $endDate = $temp;
        }

        if ($currentDate->diffInDays($endDate) > 90) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Date range cannot exceed 90 days.',
                ], 400);
            }

            return back()->withErrors(['start_date' => 'Date range cannot exceed 90 days.']);
        }

        $dates = [];

        while ($currentDate->lte($endDate)) {
            $dates[] = $currentDate->format('Y-m-d H:i:s');
            $currentDate->addDay(); // Increment the date by one day
        }

        $is_demo = $user->is_demo;
        $balanceColumn = $is_demo ? 'demo' : 'amount';

        try {
            DB::transaction(function () use ($dates, $request, $trader, $user, $is_demo, $balanceColumn) {
                Balance::firstOrCreate(
                    ['user_id' => $user->id, 'symbol' => 'USD'],
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

                $balance = Balance::whereUserId($user->id)
                    ->where('symbol', 'USD')
                    ->lockForUpdate()
                    ->first();

                $totalProfit = 0;

                $min = isset($request->min) ? intval($request->min) : 100;
                $max = isset($request->max) ? intval($request->max) : 1000;
                if ($min > $max) {
                    $temp = $min;
                    $min = $max;
                    $max = $temp;
                }

                foreach ($dates as $date) {
                    $amount = mt_rand($min, $max);

                    $percentage = $trader->percentage ?: 10;
                    $divided_amount = ($percentage / 100) * $amount;

                    $win_loss = $amount + $divided_amount;
                    $totalProfit += $divided_amount;

                    $order = new Corder([
                        'user_id' => $user->id,
                        'trades_id' => $trader->id,
                        'trader_name' => $trader->name,
                        'country' => $trader->country,
                        'amount' => $amount,
                        'commission' => $percentage,
                        'win' => 'yes',
                        'profit' => $win_loss,
                        'status' => 'win',
                        'types' => 'live',
                        'is_demo' => $is_demo,
                    ]);
                    $order->created_at = $date;
                    $order->updated_at = $date;
                    $order->save();
                }

                if ($balance && $totalProfit > 0) {
                    $balance->increment($balanceColumn, $totalProfit);
                }
            });
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate copy trade history: '.$e->getMessage(),
                ], 500);
            }

            return back()->withErrors(['error' => 'Database error: '.$e->getMessage()]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Traders histories generated successfully.',
            ]);
        }

        return back()->with('status','Traders histories generated successfully');
    }
}
