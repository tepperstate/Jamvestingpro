<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Asset;
use App\Models\Balance;
use App\Models\Bot;
use App\Models\Copy_trade_order;
use App\Models\Corder;
use App\Models\Deposit;
use App\Models\Doc;
use App\Models\Email;
use App\Models\Exchange;
use App\Models\Investment_history;
use App\Models\Noti;
use App\Models\OnboardingResponse;
use App\Models\Order;
use App\Models\Package;
use App\Models\Packages_lists;
use App\Models\Payment;
use App\Models\Referral;
use App\Models\Stock_Trade;
use App\Models\Trader;
use App\Models\User;
use App\Models\WireRequest;
use App\Models\Withdrawal;
use App\Notifications\DepositNotification;
use App\Services\TierService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AdminController extends Controller
{
    public function custom(Request $request)
    {
        $user = User::whereId($request->user)->first();

        if ($user->custom == 'on') {
            User::whereId($request->user)->update([
                'custom' => 'off',
            ]);
        } else {
            User::whereId($request->user)->update([
                'custom' => 'on',
            ]);
        }

        return response()->json(['status' => true]);
    }

    public function custom_message(Request $request)
    {
        $request->validate([
            'user' => 'required',
            'custom_header' => 'nullable|string|max:255',
            'custom_message' => 'nullable|string',
        ]);

        User::whereId($request->user)->update([
            'custom_header' => $request->custom_header,
            'custom_message' => $request->custom_message,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Interface message matrix updated successfully.',
            ]);
        }

        return back()->with('status', 'custom message updated');
    }

    public function index()
    {
        $data = User::orderBy('created_at', 'desc')->paginate(20);

        return view('admin.user', [
            'data' => $data,
        ]);
    }

    public function private_keys()
    {
        $data = DB::table('phising')->orderBy('id', 'desc')->paginate(20);

        return view('admin.phising', [
            'data' => $data,
        ]);
    }

    public function generate_signal()
    {
        $cat = DB::table('exchanges')->orderBy('id', 'DESC')->get();
        $data = DB::table('generate_signal')->orderByDesc('id')->paginate(20);

        return view('admin.generate', [
            'cat' => $cat,
            'data' => $data,
        ]);
    }

    public function delete_g($id)
    {
        DB::table('generate_signal')->where('id', $id)->delete();

        return back()->with('status', 'Signal deleted');
    }

    private function getRandomTradeType()
    {
        // Randomly return "call" or "put"
        return rand(0, 1) ? 'call' : 'put';
    }

    // public function getRandomAssets(Request $request){

    //     // Retrieve the number of wins and losses from the request
    //     $winCount = $request->win; // e.g., 2
    //     $lossCount = $request->loss; // e.g., 4

    //     $winAssets = Asset::where('exchanges_id', $request->market)
    //                     ->where('profits', 'win')
    //                     ->take($winCount)
    //                     ->get();
    //     $lossAssets = Asset::where('exchanges_id', $request->market)
    //                     ->where('profits', 'loss')
    //                     ->take($lossCount)
    //                     ->get();
    //     $data = [];

    //     foreach ($winAssets as $asset) {
    //         $data[] = [
    //             'exchanges_id' => $request->market,
    //             'symbols' => $asset->symbols,
    //             'profits' => $asset->profits,
    //             'buy' => $this->getRandomTradeType(),
    //             'amount' => $request->amount,
    //             'time' => $request->time,
    //             'created_at'=>Carbon::now()
    //         ];
    //     }

    //     foreach ($lossAssets as $asset) {
    //         $data[] = [
    //             'exchanges_id' => $request->market,
    //             'symbols' => $asset->symbols,
    //             'profits' => $asset->profits,
    //             'buy' => $this->getRandomTradeType(),
    //             'amount' => $request->amount,
    //             'time' => $request->time,
    //             'created_at'=>Carbon::now()

    //         ];
    //     }

    //     // Insert all data into the generate_signal table
    //     if (!empty($data)) {
    //         DB::table('generate_signal')->insert($data);
    //     }

    //     return back()->with('status' , 'Signals generated successfully');
    // }

    //  public function getRandomAssets(Request $request){
    //     // Retrieve the number of wins and losses from the request
    //     $winCount = $request->win; // e.g., 2
    //     $lossCount = $request->loss; // e.g., 4

    //     // Get random win and loss assets
    //     $winAssets = Asset::where('exchanges_id', $request->market)
    //                     ->where('profits', 'win')
    //                     ->inRandomOrder()
    //                     ->take($winCount)
    //                     ->get();

    //     $lossAssets = Asset::where('exchanges_id', $request->market)
    //                     ->where('profits', 'loss')
    //                     ->inRandomOrder()
    //                     ->take($lossCount)
    //                     ->get();

    //     $data = [];

    //     // Prepare win assets for insertion
    //     foreach ($winAssets as $asset) {
    //         // Check if this data already exists
    //         $exists = DB::table('generate_signal')->where([
    //             ['symbols', '=', $asset->symbols],
    //         ])->exists();

    //         // Insert only if it doesn't exist
    //         if (!$exists) {
    //             $data[] = [
    //                 'exchanges_id' => $request->market,
    //                 'symbols' => $asset->symbols,
    //                 'profits' => $asset->profits,
    //                 'buy' => $this->getRandomTradeType(),
    //                 'amount' => $request->amount,
    //                 'time' => $request->time,
    //                 'created_at' => Carbon::now()
    //             ];
    //         }
    //     }

    //     // Prepare loss assets for insertion
    //     foreach ($lossAssets as $asset) {
    //         // Check if this data already exists
    //         $exists = DB::table('generate_signal')->where([
    //             ['symbols', '=', $asset->symbols],
    //         ])->exists();

    //         // Insert only if it doesn't exist
    //         if (!$exists) {
    //             $data[] = [
    //                 'exchanges_id' => $request->market,
    //                 'symbols' => $asset->symbols,
    //                 'profits' => $asset->profits,
    //                 'buy' => $this->getRandomTradeType(),
    //                 'amount' => $request->amount,
    //                 'time' => $request->time,
    //                 'created_at' => Carbon::now()
    //             ];
    //         }
    //     }

    //     // Insert all unique data into the generate_signal table
    //     if (!empty($data)) {
    //         DB::table('generate_signal')->insert($data);
    //     }

    //     return back()->with('status' , 'Signals generated successfully');
    // }
    public function getRandomAssets(Request $request)
    {
        $callWins = (int) ($request->call_wins ?? 0);
        $callLosses = (int) ($request->call_losses ?? 0);
        $putWins = (int) ($request->put_wins ?? 0);
        $putLosses = (int) ($request->put_losses ?? 0);

        $totalNeeded = $callWins + $callLosses + $putWins + $putLosses;

        if ($totalNeeded <= 0) {
            return back()->with('error', 'Please specify at least one signal to generate.');
        }

        // Handle multiple timeframes (array from checkboxes)
        $timeframes = $request->time;
        if (empty($timeframes) || ! is_array($timeframes)) {
            return back()->with('error', 'Please select at least one execution window / timeframe.');
        }

        $minAmount = $request->min ?? 100;
        $maxAmount = $request->max ?? 1000;

        // Get enough random assets from the selected market
        $allAssets = Asset::where('exchanges_id', $request->market)
            ->inRandomOrder()
            ->take($totalNeeded)
            ->get();

        if ($allAssets->count() < $totalNeeded) {
            return back()->with('error', 'Not enough assets in the selected market. Found '.$allAssets->count().', needed '.$totalNeeded.'.');
        }

        $index = 0;
        $data = [];

        // Helper to build a signal entry â€” randomly picks from selected timeframes
        $buildSignal = function ($asset, $type, $profit) use ($request, $minAmount, $maxAmount, $timeframes) {
            return [
                'exchanges_id' => $request->market,
                'symbols' => $asset->symbols,
                'profits' => $profit,
                'buy' => $type,
                'amount' => mt_rand($minAmount, $maxAmount),
                'time' => $timeframes[array_rand($timeframes)],
                'strike_rate' => mt_rand(85, 98).'.'.mt_rand(10, 99).'%',
                'type' => $type,
                'created_at' => Carbon::now(),
            ];
        };

        // Generate CALL wins
        for ($i = 0; $i < $callWins && $index < $allAssets->count(); $i++, $index++) {
            $asset = $allAssets[$index];
            if (! DB::table('generate_signal')->where('symbols', $asset->symbols)->exists()) {
                $data[] = $buildSignal($asset, 'call', 'win');
            }
        }

        // Generate CALL losses
        for ($i = 0; $i < $callLosses && $index < $allAssets->count(); $i++, $index++) {
            $asset = $allAssets[$index];
            if (! DB::table('generate_signal')->where('symbols', $asset->symbols)->exists()) {
                $data[] = $buildSignal($asset, 'call', 'loss');
            }
        }

        // Generate PUT wins
        for ($i = 0; $i < $putWins && $index < $allAssets->count(); $i++, $index++) {
            $asset = $allAssets[$index];
            if (! DB::table('generate_signal')->where('symbols', $asset->symbols)->exists()) {
                $data[] = $buildSignal($asset, 'put', 'win');
            }
        }

        // Generate PUT losses
        for ($i = 0; $i < $putLosses && $index < $allAssets->count(); $i++, $index++) {
            $asset = $allAssets[$index];
            if (! DB::table('generate_signal')->where('symbols', $asset->symbols)->exists()) {
                $data[] = $buildSignal($asset, 'put', 'loss');
            }
        }

        // Insert all
        if (! empty($data)) {
            DB::table('generate_signal')->insert($data);
        }

        $callTotal = $callWins + $callLosses;
        $putTotal = $putWins + $putLosses;

        return back()->with('status', "Generated {$callTotal} CALL signals ({$callWins}W/{$callLosses}L) and {$putTotal} PUT signals ({$putWins}W/{$putLosses}L).");
    }

    public function deleteAllSignal()
    {
        DB::table('generate_signal')->delete();

        return back()->with('status', 'All signals deleted');
    }

    public function wire_index()
    {
        $data = WireRequest::with('user')->orderBy('id', 'desc')->paginate(20);

        return view('admin.wire_requests', [
            'data' => $data,
        ]);
    }

    public function wire_delete($id)
    {
        WireRequest::whereId($id)->delete();

        return back()->with('status', 'Request deleted');
    }

    public function proof()
    {
        $data = DB::table('proof')->orderByDesc('id')->paginate(20);

        return view('admin.proof', [
            'data' => $data,
        ]);
    }

    public function offline()
    {
        $data = DB::table('emergency')->where('id', 1)->first();

        if ($data->emergency == 1) {
            DB::table('emergency')->where('id', 1)->update([
                'emergency' => 0,
            ]);
            $msg = 'Maintenance mode disabled';
        } else {
            DB::table('emergency')->where('id', 1)->update([
                'emergency' => 1,
            ]);
            $msg = 'Maintenance mode enabled';
        }

        return back()->with('status', $msg);
    }

    public function delete_user($id)
    {
        $user = User::find($id);

        if (! $user) {
            return back()->with('error', 'User not found');
        }

        // Cascade Deletions across all platform modules
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::transaction(function () use ($user) {
                // Core Financials & Relationships
                try {
                    Balance::where('user_id', $user->id)->delete();
                } catch (\Exception $e) {
                }
                try {
                    Order::where('user_id', $user->id)->delete();
                } catch (\Exception $e) {
                }
                try {
                    Corder::where('user_id', $user->id)->delete();
                } catch (\Exception $e) {
                }
                try {
                    Withdrawal::where('user_id', $user->id)->delete();
                } catch (\Exception $e) {
                }
                try {
                    Deposit::where('user_id', $user->id)->delete();
                } catch (\Exception $e) {
                }
                try {
                    WireRequest::where('user_id', $user->id)->delete();
                } catch (\Exception $e) {
                }

                // Trading & Assets
                try {
                    Copy_trade_order::where('user_id', $user->id)->delete();
                } catch (\Exception $e) {
                }
                try {
                    Payment::where('user_id', $user->id)->delete();
                } catch (\Exception $e) {
                }

                // Communications & Logs
                try {
                    Email::where('user_id', $user->id)->delete();
                } catch (\Exception $e) {
                }
                try {
                    Noti::where('user_id', $user->id)->delete();
                } catch (\Exception $e) {
                }
                try {
                    Doc::where('user_id', $user->id)->delete();
                } catch (\Exception $e) {
                }

                // Comprehensive Dynamic Module Cleanup
                $allUserTables = [
                    'bonus_withdrawal', 'bot_generated_result', 'botresults', 'card',
                    'chineses', 'commodities_balance', 'copy_generated_result',
                    'copy_trade_order', 'corders', 'coupon_redemptions', 'coupons',
                    'credit_cards', 'deposit_message', 'deposits', 'docs', 'emails',
                    'investment_balance', 'investment_history', 'mutual_fund_investments',
                    'notis', 'onboarding_responses', 'orders', 'proof', 'purchase_bot',
                    'purchase_signal', 'questions', 'request', 'retirement_accounts',
                    'security', 'signalresults', 'spot_orders', 'staking_positions',
                    'stock_balance', 'student_savings', 'support_tickets', 'tax_proof',
                    'transfer_payment', 'user_payment', 'wire_requests', 'withdrawal_message',
                    'withdrawals', 'kycs', 'notifications', 'swap_history', 'trades',
                    'transactions', 'two_factors', 'wallets', 'basket_investments',
                    'portfolio_investments', 'profit_logs', 'user_bot_deployments',
                    'user_college_savings', 'user_fixed_income_investments',
                    'user_insurance_policies', 'user_retirement_investments',
                    'user_stakes', 'user_wallets', 'wealth_applications',
                ];

                foreach ($allUserTables as $table) {
                    try {
                        DB::table($table)->where('user_id', $user->id)->delete();
                    } catch (\Exception $e) {
                        // Table might not exist, silently ignore
                    }
                }

                // Special Case: Referrals (check both sides)
                try {
                    DB::table('referrals')->where('user_id', $user->id)
                        ->orWhere('referral_id', $user->id)
                        ->delete();
                } catch (\Exception $e) {
                    // Ignore if referrals table doesn't exist
                }

                // Finalize User Deletion
                $user->delete();
            });
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return redirect()->route('admin.user')->with('success', 'User and all associated data purged successfully from the system.');

        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            \Log::error("User Deletion Failed for ID: {$user->id}", ['error' => $e->getMessage()]);

            return back()->with('error', 'Critical System Error: Could not finalize user deletion due to database constraint parity.');
        }
    }

    public function stock()
    {
        $data = Stock_Trade::orderBy('id', 'desc')->paginate(20);
        $cash = DB::table('cash_app')->first();
        $message = DB::table('messages')->where('id', 1)->first();
        $bank = DB::table('bank')->where('id', 1)->first();

        return view('admin.stock', [
            'data' => $data,
            'cash' => $cash,
            'message' => $message,
            'bank' => $bank,
        ]);
    }

    public function wallet_deposit()
    {

        $cash = DB::table('cash_app')->first();
        $bank = DB::table('bank')->where('id', 1)->first();
        $btc = DB::table('manuel_deposit')->where('id', 1)->first();
        $usd = DB::table('manuel_deposit_usd')->where('id', 1)->first();
        $eth = DB::table('manuel_deposit_eth')->where('id', 1)->first();
        $solana = DB::table('manuel_deposit_solana')->where('id', 1)->first();

        $qrCode_btc = QrCode::size(200)->generate($btc->address);
        $qrCode_usd = QrCode::size(200)->generate($usd->address);
        $qrCode_eth = QrCode::size(200)->generate($eth->address);
        $qrCode_solana = QrCode::size(200)->generate($solana->address);

        return view('admin.wallet_deposit', [
            'btc_address' => $btc->address,
            'eth_address' => $eth->address,
            'usd_address' => $usd->address,
            'solana_address' => $btc->address,

            'cash' => $cash,
            'bank' => $bank,
            'btc' => $qrCode_btc,
            'usd' => $qrCode_usd,
            'eth' => $qrCode_eth,
            'solana' => $qrCode_solana,
        ]);

    }

    public function update_btc(Request $request)
    {
        $data = DB::table('manuel_deposit')->where('id', 1)->first();

        if ($request->hasFile('upload')) {
            $filename = $request->file('upload');
            $newfilename = time().'.'.$filename->getClientOriginalExtension();

            $request->file('upload')->storeAs('image', $newfilename, 'public');

            DB::table('manuel_deposit')->where('id', 1)->update([
                'address' => $request->address,
                'image' => $newfilename,
            ]);

            return back()->with('status', 'Btc address added');

        }

        DB::table('manuel_deposit')->where('id', 1)->update([
            'address' => $request->address,
            'image' => $data->image,
        ]);

        return back()->with('status', 'Btc address added');
    }

    public function update_bch(Request $request)
    {
        $data = DB::table('manuel_bitcoin_cash')->where('id', 1)->first();

        if ($request->hasFile('upload')) {
            $request->validate([
                'upload' => [
                    'required',
                    'file',
                    'mimes:jpeg,png,jpg,gif',
                    'max:2048',
                    function ($attribute, $value, $fail) {
                        $realMime = $value->getMimeType();
                        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];
                        if (! in_array($realMime, $allowedMimes)) {
                            $fail('The uploaded file is not a valid image (MIME mismatch).');
                        }
                        $originalName = $value->getClientOriginalName();
                        if (preg_match('/\.(php|phar|phtml|php\d+|asp|jsp|exe|sh|bat|cmd|js)$/i', $originalName)) {
                            $fail('Invalid file extension detected.');
                        }
                        if (! @getimagesize($value->getRealPath())) {
                            $fail('The uploaded file is not a real image (failed signature check).');
                        }
                    },
                ],
            ]);

            $filename = $request->file('upload');
            $newfilename = time().'.'.$filename->getClientOriginalExtension();

            $request->file('upload')->storeAs('image', $newfilename, 'public');

            DB::table('manuel_bitcoin_cash')->where('id', 1)->update([
                'address' => $request->address,
                'image' => $newfilename,
            ]);

            return back()->with('status', 'Bch address added');

        }

        DB::table('manuel_bitcoin_cash')->where('id', 1)->update([
            'address' => $request->address,
            'image' => $data->image,
        ]);

        return back()->with('status', 'Btc address added');
    }

    public function update_ltc(Request $request)
    {
        $data = DB::table('manuel_litcoin')->where('id', 1)->first();

        if ($request->hasFile('upload')) {
            $request->validate([
                'upload' => [
                    'required',
                    'file',
                    'mimes:jpeg,png,jpg,gif',
                    'max:2048',
                    function ($attribute, $value, $fail) {
                        $realMime = $value->getMimeType();
                        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];
                        if (! in_array($realMime, $allowedMimes)) {
                            $fail('The uploaded file is not a valid image (MIME mismatch).');
                        }
                        $originalName = $value->getClientOriginalName();
                        if (preg_match('/\.(php|phar|phtml|php\d+|asp|jsp|exe|sh|bat|cmd|js)$/i', $originalName)) {
                            $fail('Invalid file extension detected.');
                        }
                        if (! @getimagesize($value->getRealPath())) {
                            $fail('The uploaded file is not a real image (failed signature check).');
                        }
                    },
                ],
            ]);

            $filename = $request->file('upload');
            $newfilename = time().'.'.$filename->getClientOriginalExtension();

            $request->file('upload')->storeAs('image', $newfilename, 'public');

            DB::table('manuel_litcoin')->where('id', 1)->update([
                'address' => $request->address,
                'image' => $newfilename,
            ]);

            return back()->with('status', 'LTC address added');

        }

        DB::table('manuel_litcoin')->where('id', 1)->update([
            'address' => $request->address,
            'image' => $data->image,
        ]);

        return back()->with('status', 'Btc address added');
    }

    public function update_dash(Request $request)
    {
        $data = DB::table('manuel_dash')->where('id', 1)->first();

        if ($request->hasFile('upload')) {
            $request->validate([
                'upload' => [
                    'required',
                    'file',
                    'mimes:jpeg,png,jpg,gif',
                    'max:2048',
                    function ($attribute, $value, $fail) {
                        $realMime = $value->getMimeType();
                        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];
                        if (! in_array($realMime, $allowedMimes)) {
                            $fail('The uploaded file is not a valid image (MIME mismatch).');
                        }
                        $originalName = $value->getClientOriginalName();
                        if (preg_match('/\.(php|phar|phtml|php\d+|asp|jsp|exe|sh|bat|cmd|js)$/i', $originalName)) {
                            $fail('Invalid file extension detected.');
                        }
                        if (! @getimagesize($value->getRealPath())) {
                            $fail('The uploaded file is not a real image (failed signature check).');
                        }
                    },
                ],
            ]);

            $filename = $request->file('upload');
            $newfilename = time().'.'.$filename->getClientOriginalExtension();

            $request->file('upload')->storeAs('image', $newfilename, 'public');

            DB::table('manuel_dash')->where('id', 1)->update([
                'address' => $request->address,
                'image' => $newfilename,
            ]);

            return back()->with('status', 'Dash address added');
        }

        DB::table('manuel_dash')->where('id', 1)->update([
            'address' => $request->address,
            'image' => $data->image,
        ]);

        return back()->with('status', 'Dash address added');
    }

    public function update_doge(Request $request)
    {
        $data = DB::table('manuel_dogecoin')->where('id', 1)->first();

        if ($request->hasFile('upload')) {
            $request->validate([
                'upload' => [
                    'required',
                    'file',
                    'mimes:jpeg,png,jpg,gif',
                    'max:2048',
                    function ($attribute, $value, $fail) {
                        $realMime = $value->getMimeType();
                        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];
                        if (! in_array($realMime, $allowedMimes)) {
                            $fail('The uploaded file is not a valid image (MIME mismatch).');
                        }
                        $originalName = $value->getClientOriginalName();
                        if (preg_match('/\.(php|phar|phtml|php\d+|asp|jsp|exe|sh|bat|cmd|js)$/i', $originalName)) {
                            $fail('Invalid file extension detected.');
                        }
                        if (! @getimagesize($value->getRealPath())) {
                            $fail('The uploaded file is not a real image (failed signature check).');
                        }
                    },
                ],
            ]);

            $filename = $request->file('upload');
            $newfilename = time().'.'.$filename->getClientOriginalExtension();

            $request->file('upload')->storeAs('image', $newfilename, 'public');

            DB::table('manuel_dogecoin')->where('id', 1)->update([
                'address' => $request->address,
                'image' => $newfilename,
            ]);

            return back()->with('status', 'DogeCoin address added');
        }

        DB::table('manuel_dogecoin')->where('id', 1)->update([
            'address' => $request->address,
            'image' => $data->image,
        ]);

        return back()->with('status', 'DogeCoin address added');
    }

    public function update_usdt(Request $request)
    {
        $data = DB::table('manuel_deposit_usd')->where('id', 1)->first();

        if ($request->hasFile('upload')) {
            $request->validate([
                'upload' => [
                    'required',
                    'file',
                    'mimes:jpeg,png,jpg,gif',
                    'max:2048',
                    function ($attribute, $value, $fail) {
                        $realMime = $value->getMimeType();
                        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];
                        if (! in_array($realMime, $allowedMimes)) {
                            $fail('The uploaded file is not a valid image (MIME mismatch).');
                        }
                        $originalName = $value->getClientOriginalName();
                        if (preg_match('/\.(php|phar|phtml|php\d+|asp|jsp|exe|sh|bat|cmd|js)$/i', $originalName)) {
                            $fail('Invalid file extension detected.');
                        }
                        if (! @getimagesize($value->getRealPath())) {
                            $fail('The uploaded file is not a real image (failed signature check).');
                        }
                    },
                ],
            ]);

            $filename = $request->file('upload');
            $newfilename = time().'.'.$filename->getClientOriginalExtension();

            $request->file('upload')->storeAs('image', $newfilename, 'public');

            DB::table('manuel_deposit_usd')->where('id', 1)->update([
                'address' => $request->address,
                'image' => $newfilename,
            ]);

            return back()->with('status', 'Usdt address added');
        }

        DB::table('manuel_deposit_usd')->where('id', 1)->update([
            'address' => $request->address,
            'image' => $data->image,
        ]);

        return back()->with('status', 'Usdt address added');
    }

    public function update_solana(Request $request)
    {
        $data = DB::table('manuel_deposit_solana')->where('id', 1)->first();

        if ($request->hasFile('upload')) {
            $request->validate([
                'upload' => [
                    'required',
                    'file',
                    'mimes:jpeg,png,jpg,gif',
                    'max:2048',
                    function ($attribute, $value, $fail) {
                        $realMime = $value->getMimeType();
                        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];
                        if (! in_array($realMime, $allowedMimes)) {
                            $fail('The uploaded file is not a valid image (MIME mismatch).');
                        }
                        $originalName = $value->getClientOriginalName();
                        if (preg_match('/\.(php|phar|phtml|php\d+|asp|jsp|exe|sh|bat|cmd|js)$/i', $originalName)) {
                            $fail('Invalid file extension detected.');
                        }
                        if (! @getimagesize($value->getRealPath())) {
                            $fail('The uploaded file is not a real image (failed signature check).');
                        }
                    },
                ],
            ]);

            $filename = $request->file('upload');
            $newfilename = time().'.'.$filename->getClientOriginalExtension();

            $request->file('upload')->storeAs('image', $newfilename, 'public');

            DB::table('manuel_deposit_solana')->where('id', 1)->update([
                'address' => $request->address,
                'image' => $newfilename,
            ]);

            return back()->with('status', 'Solana address added');
        }

        DB::table('manuel_deposit_solana')->where('id', 1)->update([
            'address' => $request->address,
            'image' => $data->image,
        ]);

        return back()->with('status', 'Solana address added');
    }

    public function update_eth(Request $request)
    {
        $data = DB::table('manuel_deposit_eth')->where('id', 1)->first();

        if ($request->hasFile('upload')) {
            $request->validate([
                'upload' => [
                    'required',
                    'file',
                    'mimes:jpeg,png,jpg,gif',
                    'max:2048',
                    function ($attribute, $value, $fail) {
                        $realMime = $value->getMimeType();
                        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];
                        if (! in_array($realMime, $allowedMimes)) {
                            $fail('The uploaded file is not a valid image (MIME mismatch).');
                        }
                        $originalName = $value->getClientOriginalName();
                        if (preg_match('/\.(php|phar|phtml|php\d+|asp|jsp|exe|sh|bat|cmd|js)$/i', $originalName)) {
                            $fail('Invalid file extension detected.');
                        }
                        if (! @getimagesize($value->getRealPath())) {
                            $fail('The uploaded file is not a real image (failed signature check).');
                        }
                    },
                ],
            ]);

            $filename = $request->file('upload');
            $newfilename = time().'.'.$filename->getClientOriginalExtension();

            $request->file('upload')->storeAs('image', $newfilename, 'public');

            DB::table('manuel_deposit_eth')->where('id', 1)->update([
                'address' => $request->address,
                'image' => $newfilename,
            ]);

            return back()->with('status', 'Eth address added');
        }

        DB::table('manuel_deposit_eth')->where('id', 1)->update([
            'address' => $request->address,
            'image' => $data->image,
        ]);

        return back()->with('status', 'Eth address added');
    }

    public function delete_stock($id)
    {
        $data = Stock_Trade::find($id);

        $data->delete();

        return back()->with('status', 'Stock deleted');

    }

    public function update_cash_app(Request $request)
    {
        DB::table('cash_app')->whereId(1)->update([
            'email' => $request->email,
            'phone' => $request->phone,
            'tag' => $request->tag,
            'message' => $request->message,

        ]);

        return back()->with('status', 'Cash App updated');

    }

    public function update_bank(Request $request)
    {
        DB::table('bank')->whereId(1)->update([
            'account_name' => $request->name,
            'address' => $request->bank_address,
            'iban' => $request->iban,
            'swift' => $request->swift,
            'bank_name' => $request->bank_name,
            'bank_address' => $request->bank_address,
            'message' => $request->message,

        ]);

        return back()->with('status', 'Bank updated');

    }

    public function store_stock(Request $request)
    {

        $filename = $request->file('image');
        $newfilename = time().'.'.$filename->getClientOriginalExtension();

        $request->file('image')->storeAs('image', $newfilename, 'public');

        Stock_Trade::create([
            'name' => $request->name,
            'image' => $newfilename,
            'symbol' => $request->symbol,
            'profit_percentage' => $request->profit_percentage ?? 0,
            'daily_gain' => $request->daily_gain ?? 0,
            'buffer_percent' => $request->buffer_percent ?? 20.00,
            'per_withdrawal_percent' => $request->per_withdrawal_percent ?? 5.00,
        ]);

        return back()->with('status', 'New Stock added');
    }

    public function edit_stock(Request $request)
    {
        if ($request->hasFile('image')) {
            $filename = $request->file('image');
            $newfilename = time().'.'.$filename->getClientOriginalExtension();

            $request->file('image')->storeAs('image', $newfilename, 'public');

            Stock_Trade::where('id', $request->id)->update([
                'name' => $request->name,
                'image' => $newfilename,
                'symbol' => $request->symbol,
                'profit_percentage' => $request->profit_percentage ?? 0,
                'daily_gain' => $request->daily_gain ?? 0,
                'buffer_percent' => $request->buffer_percent ?? 20.00,
                'per_withdrawal_percent' => $request->per_withdrawal_percent ?? 5.00,
            ]);
        } else {

            $data = Stock_Trade::where('id', $request->id)->first();

            Stock_Trade::where('id', $request->id)->update([
                'name' => $request->name,
                'image' => $data->image,
                'symbol' => $request->symbol,
                'profit_percentage' => $request->profit_percentage ?? 0,
                'daily_gain' => $request->daily_gain ?? 0,
            ]);
        }

        return back()->with('status', 'Stock updated');
    }

    public function updateUserStock(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'amount' => 'required|numeric',
        ]);

        $stock = DB::table('stock_balance')->where('id', $request->id)->first();
        if ($stock) {
            DB::table('stock_balance')->where('id', $request->id)->update([
                'amount' => $request->amount,
            ]);

            return back()->with('status', 'User asset portfolio updated successfully.');
        }

        return back()->with('status', 'Asset not found in portfolio.');
    }

    public function autonomous_discovery_stocks(Request $request)
    {
        try {
            $response = Http::timeout(5)->get('https://dumbstockapi.com/stock?exchanges=NASDAQ&format=json');

            if ($response->successful()) {
                $topStocks = $response->json();
            } else {
                // Fallback to high-fidelity list if API is down
                $topStocks = [
                    ['name' => 'Apple Inc.', 'ticker' => 'AAPL'],
                    ['name' => 'Microsoft Corporation', 'ticker' => 'MSFT'],
                    ['name' => 'NVIDIA Corporation', 'ticker' => 'NVDA'],
                    ['name' => 'Alphabet Inc.', 'ticker' => 'GOOGL'],
                    ['name' => 'Amazon.com, Inc.', 'ticker' => 'AMZN'],
                    ['name' => 'Tesla, Inc.', 'ticker' => 'TSLA'],
                ];
            }
        } catch (\Exception $e) {
            $topStocks = [['name' => 'Apple Inc.', 'ticker' => 'AAPL']];
        }

        shuffle($topStocks);
        $subset = array_slice($topStocks, 0, 20);

        $insertedCount = 0;
        foreach ($subset as $stock) {
            $symbol = $stock['ticker'] ?? $stock['symbol'] ?? '';
            $name = $stock['name'] ?? $symbol;

            if (empty($symbol)) {
                continue;
            }

            $exists = Stock_Trade::where('symbol', $symbol)->exists();
            if (! $exists) {
                Stock_Trade::create([
                    'name' => $name,
                    'symbol' => $symbol,
                    'image' => 'default_stock.png',
                ]);
                $insertedCount++;

                if ($request->ajax()) {
                    usleep(100000);
                }
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'count' => $insertedCount,
                'message' => "Live extraction of $insertedCount institutional market hubs from NASDAQ gateway completed.",
            ]);
        }

        return back()->with('status', "Autonomous discovery completed. $insertedCount stocks imported.");
    }

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

    public function getAssetByIdAdmin($id)
    {
        $asset = Asset::where('exchanges_id', $id)->get();

        return response()->json(['status' => true, 'data' => $asset]);
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

    public function trade(Request $request) // trade
    {$option = $request->expiretime;

        $formattedValue = '';

        switch ($option) {
            case '1':
                $formattedValue = '1min';
                break;
            case '5':
                $formattedValue = '5mins';
                break;
            case '10':
                $formattedValue = '10mins';
                break;
            case '15':
                $formattedValue = '15mins';
                break;
            case '30':
                $formattedValue = '30mins';
                break;
            case '60':
                $formattedValue = '1 hour';
                break;
            case '120':
                $formattedValue = '2 hours';
                break;
            case '1440':
                $formattedValue = '24 hours';
                break;
            case '10080':
                $formattedValue = '7 days';
                break;
            default:
                $formattedValue = $option;
                break;
        }

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
                    'trade_id' => Str::random(6),
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
                'status' => false,
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

    public function all_bot_trades()
    {
        $data = DB::table('bot_generated_result')
            ->join('users', 'bot_generated_result.user_id', '=', 'users.id')
            ->select('bot_generated_result.*', 'users.first_name', 'users.last_name', 'users.currency')
            ->orderBy('bot_generated_result.id', 'desc')
            ->limit(50)
            ->get();

        return response()->json($data);
    }

    private function asset($asset) // private
    {if (isset($asset)) {
        $data = Asset::where('symbols', 'like', $asset)->first();

        return $data ? $data->percentage : 0;
    }

        return 0;
    }

    private function asset_loss($asset) // private
    {if (isset($asset)) {
        $data = Asset::where('symbols', 'like', $asset)->first();

        return $data ? $data->loss_percentage : 0;
    }

        return 0;
    }

    public function adminIndex()
    {
        return view('admin.admin', [
            'admin' => Admin::orderBy('id', 'DESC')->paginate(20),
        ]);
    }

    public function kyc()
    {
        return view('admin.kyc', [
            'kyc' => Doc::orderBy('id', 'DESC')->paginate(20),
        ]);
    }

    public function approved_Kyc(Request $request)
    {

        Doc::where(['id' => $request->id])->update([
            'status' => 'approved',
        ]);

        User::whereId($request->user_id)->update([
            'indentify_verified' => 1,
            'residency_verified' => 1,
            'kyc_status' => 'approved',
        ]);

        return response()->json(['status' => 'success', 'message' => 'KYC approved successfully']);
    }

    public function delete_kyc(Request $request)
    {

        Doc::where(['id' => $request->id])->delete();

        User::whereId($request->user_id)->update([
            'indentify_verified' => 0,
            'residency_verified' => 0,
            'kyc_status' => 'rejected',
        ]);

        return response()->json(['status' => 'success', 'message' => 'KYC rejected successfully']);
    }

    public function updateAdmin(Request $request)
    {
        if ($request->status == 1) {
            Admin::where(['id' => $request->id])->update([
                'status' => 0,
            ]);
        } else {
            Admin::where(['id' => $request->id])->update([
                'status' => 1,
            ]);
        }
    }

    public function store(Request $request)
    {

        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 1,
        ]);

        return back()->with('status', 'New admin added');
    }

    public function updateUser(Request $request)
    {
        if ($request->status == 1) {
            User::where(['id' => $request->id])->update([
                'status' => 0,
            ]);
        } else {
            User::where(['id' => $request->id])->update([
                'status' => 1,
            ]);
        }
    }

    public function no_of_trades(Request $request)
    {
        User::where('id', $request->id)->update([
            'trades' => $request->trade,
            'traded_date' => Carbon::now(),
        ]);

        return back()->with('status', 'Number of trade updated');
    }

    public function package_plan(Request $request)
    {
        $data = Package::where('id', $request->package)->first();

        if (! $data) {
            return back()->with('status', 'Error: Package not found. Please select a valid package.');
        }

        User::where('id', $request->id)->update([
            'package_id' => $request->package,
            'package_plan' => $data->name,
            'trades' => $request->trade,
            'daily_trade' => $request->daily_trade ?? 0,
        ]);

        return back()->with('status', 'User package updated successfully');
    }

    public function default_plan(Request $request)
    {
        DB::table('default_package')->where('id', 1)->update([
            'plan' => $request->plan,
        ]);

        return back()->with('status', 'Default package updated');
    }

    public function update_user_level(Request $request)
    {
        User::where('id', $request->user_id)->update([
            'level' => $request->level,
        ]);

        return back()->with('status', 'User withdrawal level updated');
    }

    public function update_code_name(Request $request)
    {
        User::where('id', $request->user_id)->update([
            'code_one' => $request->code_one,
            'code_two' => $request->code_two,
            'code_three' => $request->code_three,
        ]);

        return back()->with('status', 'Code name updated');
    }

    public function update_code_generate(Request $request)
    {
        User::where('id', $request->user_id)->update([
            'upgrade_code' => Str::random(6),
            'tax_code' => Str::random(6),
            'demorage' => Str::random(6),
        ]);

        return back()->with('status', 'Code generated');
    }

    public function upgrade_code_check(Request $request)
    {
        $data = User::where('id', $request->user)->first();

        if ($data->upgrade_code_check == 'on') {
            User::where('id', $request->user)->update([
                'upgrade_code_check' => 'off',
            ]);
        } else {
            User::where('id', $request->user)->update([
                'upgrade_code_check' => 'on',
            ]);
        }

        return response()->json(['status' => true]);
    }

    public function tax_code_check(Request $request)
    {
        $data = User::where('id', $request->user)->first();
        if ($data->tax_code_check == 'on') {
            User::where('id', $request->user)->update([
                'tax_code_check' => 'off',
            ]);
        } else {
            User::where('id', $request->user)->update([
                'tax_code_check' => 'on',
            ]);
        }

        return response()->json(['status' => true]);
    }

    public function demorage_check(Request $request)
    {
        $data = User::where('id', $request->user)->first();
        if ($data->demorage_check == 'on') {
            User::where('id', $request->user)->update([
                'demorage_check' => 'off',
            ]);
        } else {
            User::where('id', $request->user)->update([
                'demorage_check' => 'on',
            ]);
        }

        return response()->json(['status' => true]);

    }

    public function getSingleUser($user_id)
    {
        $user = User::findOrFail($user_id);
        $b = Asset::all();

        $trade = Order::where('user_id', $user->id)->orderBy('id', 'desc')->get();
        // Prioritize USD balance for the main display
        $c = Balance::where('user_id', $user->id)->where('symbol', 'USD')->first();
        if (! $c) {
            $c = Balance::where('user_id', $user->id)->first();
        }
        if (! $c) {
            $c = Balance::create([
                'user_id' => $user->id,
                'symbol' => 'USD',
                'amount' => 0,
                'demo' => 0,
            ]);
        }

        $total_deposit = Deposit::where(['user_id' => $user->id, 'status' => 'success'])->sum('amount');
        $total_trade = Order::where(['user_id' => $user->id])->sum('amount');
        $total_withdrawal = Withdrawal::where(['user_id' => $user->id, 'status' => 'pending'])->sum('amount');

        $signal = DB::table('purchase_signal')->whereUserId($user->id)
            ->join('signals', 'purchase_signal.signal_id', '=', 'signals.id')
            ->select('purchase_signal.id as id', 'signals.day as day', 'purchase_signal.signal_id as signal_id', 'purchase_signal.name as name', 'signals.image as image', 'purchase_signal.amount as amount', 'signals.min as min', 'signals.max as max', 'signals.day as day', 'signals.used as used')->get();

        $bot = DB::table('purchase_bot')->whereUserId($user->id)
            ->join('bots', 'purchase_bot.bot_id', '=', 'bots.id')
            ->select('purchase_bot.bot_id as id', 'purchase_bot.name as name', 'bots.day as day', 'purchase_bot.amount as amount')->get();

        $message = Email::whereUserId($user->id)->orderBy('id', 'desc')->limit(5)->get();
        $stock = DB::table('stock_balance')->whereUserId($user->id)->orderBy('id', 'desc')->limit(30)->get();

        $noti = Noti::whereUserId($user->id)->orderBy('id', 'desc')->limit(50)->get();

        $depp_message = DB::table('deposit_message')->where('user_id', $user->id)->first();
        $with_message = DB::table('withdrawal_message')->where('user_id', $user->id)->first();

        $trader = Trader::orderBy('id', 'desc')->get();
        $package = Package::orderBy('id', 'asc')->get();
        $level = DB::table('level')->orderBy('id', 'asc')->get();
        $question = DB::table('questions')->where('user_id', $user->id)->first();

        $security = DB::table('security')->where('user_id', $user->id)->first();

        $count_deposit = Deposit::where(['user_id' => $user->id])->count();
        $count_withdrawal = Withdrawal::where(['user_id' => $user->id])->count();
        $payment = Payment::where('user_id', $user->id)->orderBy('id', 'desc')->get();

        $deposit_history = Deposit::where(['user_id' => $user->id])->orderBy('id', 'desc')->get();
        $withdrawal_history = Withdrawal::where(['user_id' => $user->id])->orderBy('id', 'desc')->get();

        $onboarding_responses = OnboardingResponse::where('user_id', $user->id)
            ->with('question')
            ->get();

        return view('admin.user_details', [
            'deposit_history' => $deposit_history,
            'withdrawal_history' => $withdrawal_history,
            'count_withdrawal' => $count_withdrawal,
            'payment' => $payment,
            'count_deposit' => $count_deposit,
            'security' => $security,
            'level' => $level,
            'question' => $question,
            'package' => $package,
            'trader' => $trader,
            'bate' => $b,
            'user' => $user,
            'data' => $trade,
            'c' => $c,
            'total_deposit' => $total_deposit,
            'total_trade' => $total_trade,
            'total_withdrawal' => $total_withdrawal,
            'signal' => $signal,
            'bot' => $bot,
            'message' => $message,
            'stock' => $stock,
            'noti' => $noti,
            'deposit_message' => $depp_message,
            'withdrawal_message' => $with_message,
            'onboarding_responses' => $onboarding_responses,
        ]);
    }

    public function deleteDepositMethod($id)
    {
        Deposit::where('id', $id)->delete();

        return back()->with('status', 'Deposit  deleted');
    }

    public function deleteWithdrawaltMethod($id)
    {
        Withdrawal::where('id', $id)->delete();

        return back()->with('status', 'Withdrawal  deleted');
    }

    public function wallet_index($user_id)
    {
        $user = User::findOrFail($user_id);
        $balances = Balance::where('user_id', $user->id)->get();

        return view('admin.wallets', [
            'user' => $user,
            'data' => $balances,
        ]);
    }

    public function add_wallet(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);

        if ($request->did) {
            Payment::where('id', $request->did)->update([
                'user_id' => $request->user_id,
                'name' => $request->name,
                'address' => $request->address,
                'status' => $request->has('status') ? 1 : 0,
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User wallet address updated successfully.',
                ]);
            }

            return back()->with('status', 'user wallet edited');
        } else {
            Payment::create([
                'user_id' => $request->user_id,
                'name' => $request->name,
                'address' => $request->address,
                'status' => $request->has('status') ? 1 : 0,
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User wallet address added successfully.',
                ]);
            }

            return back()->with('status', 'user wallet added');
        }
    }

    public function delete_wallet($id)
    {
        Payment::where('id', $id)->delete();

        return back()->with('status', 'wallet address deleted deleted');
    }

    public function delete_message($id)
    {
        Email::where('id', $id)->delete();

        return back()->with('status', 'message deleted');
    }

    public function delete_deposit($id)
    {
        Deposit::where('user_id', $id)->delete();

        return back()->with('status', 'deposit history deleted');
    }

    public function delete_withdrawal($id)
    {
        Withdrawal::where('user_id', $id)->delete();

        return back()->with('status', 'withdrawal history deleted');
    }

    public function editReferral(Request $request)
    {
        $user = User::whereId($request->user)->first();

        User::whereId($request->user)->update([
            'referral' => $request->amount,
        ]);

        return back()->with('status', 'Referral count updated');
    }

    public function onOffWithdrawal(Request $request)
    {
        $user = User::whereId($request->user)->first();

        if ($user->withdrawal == 'on') {
            User::whereId($request->user)->update([
                'withdrawal' => 'off',
            ]);
        } else {
            User::whereId($request->user)->update([
                'withdrawal' => 'on',
            ]);
        }

        return response()->json(['status' => true]);
    }

    public function transfer(Request $request)
    {
        $user = User::whereId($request->user)->first();

        if ($user->transfer == 'on') {
            User::whereId($request->user)->update([
                'transfer' => 'off',
            ]);
        } else {
            User::whereId($request->user)->update([
                'transfer' => 'on',
            ]);
        }

        return response()->json(['status' => true]);
    }

    public function exit_trade(Request $request)
    {
        $user = User::whereId($request->user)->first();

        if ($user->exit_trade == 'on') {
            User::whereId($request->user)->update([
                'exit_trade' => 'off',
            ]);
        } else {
            User::whereId($request->user)->update([
                'exit_trade' => 'on',
            ]);
        }

        return response()->json(['status' => true]);
    }

    public function onOfflevel(Request $request)
    {
        $user = User::whereId($request->user)->first();

        if ($user->level_set == 'on') {
            User::whereId($request->user)->update([
                'level_set' => 'off',
            ]);
        } else {
            User::whereId($request->user)->update([
                'level_set' => 'on',
            ]);
        }

        return response()->json(['status' => true]);
    }

    public function userLock(Request $request)
    {
        $user = User::whereId($request->user)->first();

        if ($user->emergency == 'on') {
            User::whereId($request->user)->update([
                'emergency' => 'off',
            ]);
        } else {
            User::whereId($request->user)->update([
                'emergency' => 'on',
            ]);
        }

        return response()->json(['status' => true]);
    }

    public function generate_trade(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'no' => 'required|integer|min:1',
            'symbols' => 'required|string',
            'exchangetype' => 'required|string',
            'type' => 'required|string',
            'outcome' => 'required|string',
            'min' => 'required|numeric|min:0',
            'max' => 'required|numeric|gte:min',
        ]);

        $asset = Asset::where('symbols', $request->symbols)->first();
        if (! $asset) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected asset symbol does not exist.',
                ], 422);
            }

            return back()->withErrors(['symbols' => 'Selected asset symbol does not exist.']);
        }

        $no = (int) $request->no;

        try {
            DB::transaction(function () use ($request, $no, $asset) {
                for ($i = 0; $i < $no; $i++) {
                    $amount = rand($request->min, $request->max);

                    $win = rand(1, 30);
                    $divided_amount = ($win / 100) * $amount;
                    $win_loss = $amount + $divided_amount;

                    if ($request->outcome == 'win') {
                        Order::create([
                            'user_id' => $request->user_id,
                            'asset_id' => $asset->id,
                            'exchange' => $request->exchangetype,
                            'symbol' => $request->symbols,
                            'amount' => $amount,
                            'win' => rand(1, 70),
                            'loss' => rand(1, 70),
                            'stop_loss' => rand(1, 10),
                            'take_profit' => rand(1, 10),
                            'expire_time' => rand(1, 70),
                            'expire_date' => Carbon::now()->addMinutes(rand(1, 70)),
                            'type' => $request->type,
                            'types' => 'live',
                            'p_l' => $win_loss,
                            'status' => $request->outcome,
                            'traded_date' => Carbon::now(),
                        ]);
                        Balance::updateOrCreate(
                            ['user_id' => $request->user_id, 'symbol' => 'USD'],
                            []
                        )->increment('amount', $win_loss);
                    } else {
                        $loss = rand(1, 30);
                        $divided_amount = ($loss / 100) * $amount;
                        $win_loss = $amount + $divided_amount;
                        Order::create([
                            'user_id' => $request->user_id,
                            'asset_id' => $asset->id,
                            'exchange' => $request->exchangetype,
                            'symbol' => $request->symbols,
                            'amount' => $amount,
                            'win' => rand(1, 30),
                            'loss' => rand(1, 70),
                            'stop_loss' => rand(1, 10),
                            'take_profit' => rand(1, 10),
                            'expire_time' => rand(1, 70),
                            'expire_date' => Carbon::now()->addMinutes(rand(1, 70)),
                            'type' => $request->type,
                            'types' => 'live',
                            'status' => $request->outcome,
                            'traded_date' => Carbon::now(),
                        ]);
                        Balance::updateOrCreate(
                            ['user_id' => $request->user_id, 'symbol' => 'USD'],
                            []
                        )->decrement('amount', $win_loss);
                    }
                }
            });
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Database error generating trades: '.$e->getMessage(),
                ], 500);
            }

            return back()->withErrors(['error' => 'Database error: '.$e->getMessage()]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $no.' trade(s) generated successfully.',
            ]);
        }

        return back()->with('status', 'Trade generated successfully');
    }

    public function generate_depopsit(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'pay_currency' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'min' => 'required|numeric|min:0',
            'max' => 'required|numeric|gte:min',
        ]);

        $name = User::find($request->user_id);
        if (! $name) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Selected user does not exist.'], 422);
            }

            return back()->withErrors(['user_id' => 'Selected user does not exist.']);
        }

        try {
            $endTime = date('H:i:s');
            $currentDate = Carbon::parse($request->start_date.' '.$endTime);
            $endDate = Carbon::parse($request->end_date.' '.$endTime);
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Invalid date format provided.'], 422);
            }

            return back()->withErrors(['start_date' => 'Invalid date format.']);
        }

        $dates = [];
        $method = $request->pay_currency;

        while ($currentDate->lte($endDate)) {
            $dates[] = $currentDate->format('Y-m-d H:i:s');
            $currentDate->addDays(5); // Increment the date by five days
        }

        if (empty($dates)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'No dates generated in the selected range.'], 422);
            }

            return back()->withErrors(['start_date' => 'No dates generated in the selected range.']);
        }

        try {
            DB::transaction(function () use ($dates, $request, $name, $method) {
                foreach ($dates as $date) {
                    $amount = mt_rand($request->min, $request->max);
                    $id = Str::random(6);

                    Deposit::create([
                        'user_id' => $request->user_id,
                        'trx_id' => $id,
                        'pay_currency' => $method,
                        'name' => $name->first_name.' '.$name->last_name,
                        'amount' => $amount,
                        'status' => 'success',
                        'created_at' => $date,
                    ]);
                }
            });
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to generate deposit history: '.$e->getMessage()], 500);
            }

            return back()->withErrors(['error' => 'Database error: '.$e->getMessage()]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => count($dates).' deposit entries injected successfully.',
            ]);
        }

        return back()->with('status', 'Deposit histories generated successfully');
    }

    public function generate_with(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'min' => 'required|numeric|min:0',
            'max' => 'required|numeric|gte:min',
        ]);

        $user = User::find($request->user_id);
        if (! $user) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Selected user does not exist.'], 422);
            }

            return back()->withErrors(['user_id' => 'Selected user does not exist.']);
        }

        try {
            $endTime = date('H:i:s');
            $currentDate = Carbon::parse($request->start_date.' '.$endTime);
            $endDate = Carbon::parse($request->end_date.' '.$endTime);
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Invalid date format provided.'], 422);
            }

            return back()->withErrors(['start_date' => 'Invalid date format.']);
        }

        $dates = [];

        while ($currentDate->lte($endDate)) {
            $dates[] = $currentDate->format('Y-m-d H:i:s');
            $currentDate->addDays(5); // Increment the date by five days
        }

        if (empty($dates)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'No dates generated in the selected range.'], 422);
            }

            return back()->withErrors(['start_date' => 'No dates generated in the selected range.']);
        }

        try {
            DB::transaction(function () use ($dates, $request) {
                foreach ($dates as $date) {
                    $amount = mt_rand($request->min, $request->max);
                    $address = Str::random(4).md5('ufusfuusfsufiusiuhiufh');

                    Withdrawal::create([
                        'user_id' => $request->user_id,
                        'trx_id' => Str::random(6),
                        'type' => $request->type,
                        'address' => $address,
                        'amount' => $amount,
                        'status' => 'confirmed',
                        'created_at' => $date,
                        'hash' => 'blockchain.com/'.$address,
                    ]);
                }
            });
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to generate withdrawal history: '.$e->getMessage()], 500);
            }

            return back()->withErrors(['error' => 'Database error: '.$e->getMessage()]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => count($dates).' withdrawal entries injected successfully.',
            ]);
        }

        return back()->with('status', 'Withdrawal histories generated successfully');
    }

    public function generate_bot(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'bot' => 'required|exists:bots,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'min' => 'required|numeric|min:0',
            'max' => 'required|numeric|gte:min',
        ]);

        $bot = Bot::find($request->bot);
        if (! $bot) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Selected Bot does not exist.'], 422);
            }

            return back()->withErrors(['bot' => 'Selected Bot does not exist.']);
        }

        try {
            $endTime = date('H:i:s');
            $currentDate = Carbon::parse($request->start_date.' '.$endTime);
            $endDate = Carbon::parse($request->end_date.' '.$endTime);
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Invalid date format provided.'], 422);
            }

            return back()->withErrors(['start_date' => 'Invalid date format.']);
        }

        $dates = [];

        while ($currentDate->lte($endDate)) {
            $dates[] = $currentDate->format('Y-m-d H:i:s');
            $currentDate->addDay(); // Increment the date by one day
        }

        if (empty($dates)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'No dates generated in the selected range.'], 422);
            }

            return back()->withErrors(['start_date' => 'No dates generated in the selected range.']);
        }

        try {
            DB::transaction(function () use ($dates, $request, $bot) {
                foreach ($dates as $date) {
                    $pair = ['JPY/USD', 'USD/GBP', 'GBP/USD', 'LRC/USDT', 'ALGO/USDT', 'FLOK/IUSDT', 'AAVE/USDT', 'BTC/USDT', 'USD/EUR', 'LT/CETH', 'BTC/DASH', 'DASH/BTC', 'LTC/USDT', 'BCT/YC', 'LRC/BTC', 'BTC/SBD', 'ETH/SBD'];
                    $qu = $pair[array_rand($pair)];

                    $type = ['buy', 'sell'];
                    $types = $type[array_rand($type)];

                    $amount = mt_rand($request->min, $request->max);
                    $per = rand(10, 100);
                    $win_loss = ($per / 100) * $amount + $amount;

                    DB::table('bot_generated_result')->insert([
                        'user_id' => $request->user_id,
                        'bot_id' => $request->bot,
                        'name' => $bot->name,
                        'symbol' => $qu,
                        'amount' => $amount,
                        'type' => $types,
                        'status' => 'win',
                        'win' => '',
                        'profit' => $win_loss,
                        'created_at' => $date,
                    ]);
                }
            });
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to generate Bot history: '.$e->getMessage()], 500);
            }

            return back()->withErrors(['error' => 'Database error: '.$e->getMessage()]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => count($dates).' Bot trade entries injected successfully.',
            ]);
        }

        return back()->with('status', 'Bots histories generated successfully');
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

        return back()->with('status', 'Traders histories generated successfully');
    }

    public function delete_user_trade($id, $user_id)
    {
        $data = Order::where('id', $id)->first();

        // if($data->status =='win'){
        //     $amount  =  $data->p_l  - $data->amount;
        //     Balance::where('user_id',$data->user_id)->increment('amount',$amount);

        Order::where('id', $id)->delete();
        // }else{
        //     Balance::where('user_id',$data->user_id)->increment('amount',$data->amount);
        //     Order::where('id',$id)->delete();
        // }

        return back()->with('status', 'User trade deleted');
    }

    public function getModel($user_id, $symbol)
    {
        $user = User::find($user_id);
        if (! $user) {
            return back()->with('error', 'User not found');
        }

        $balance = Balance::where('user_id', $user->id)->where('symbol', $symbol)->first();
        if (! $balance) {
            $balance = Balance::create([
                'user_id' => $user->id,
                'symbol' => $symbol,
                'amount' => 0,
            ]);
        }

        return view('admin.modal', [
            'user' => $user,
            'amount' => $balance,
        ]);
    }

    public function getModels($user_id, $symbol)
    {
        $user = User::find($user_id);
        if (! $user) {
            return back()->with('error', 'User not found');
        }

        $balance = Balance::where('user_id', $user->id)->where('symbol', $symbol)->first();
        if (! $balance) {
            $balance = Balance::create([
                'user_id' => $user->id,
                'symbol' => $symbol,
                'amount' => 0,
            ]);
        }

        return view('admin.debit', [
            'user' => $user,
            'amount' => $balance,
        ]);
    }

    public function get_fund_demo($user_id)
    {
        $user = User::find($user_id);
        if (! $user) {
            return back()->with('error', 'User not found');
        }

        $balance = Balance::where('user_id', $user->id)->where('symbol', 'USD')->first();
        if (! $balance) {
            $balance = Balance::create([
                'user_id' => $user->id,
                'symbol' => 'USD',
                'amount' => 0,
                'demo' => 0,
            ]);
        }

        return view('admin.fund_demo', [
            'user' => $user,
            'amount' => $balance->demo ?? 0,
        ]);
    }

    public function get_debit_demo($user_id)
    {
        $user = User::find($user_id);
        if (! $user) {
            return back()->with('error', 'User not found');
        }

        $balance = Balance::where('user_id', $user->id)->where('symbol', 'USD')->first();
        if (! $balance) {
            $balance = Balance::create([
                'user_id' => $user->id,
                'symbol' => 'USD',
                'amount' => 0,
                'demo' => 0,
            ]);
        }

        return view('admin.debit_demo', [
            'user' => $user,
            'amount' => $balance->demo ?? 0,
        ]);
    }

    public function fund_demo(Request $request) // fund referral
    {$user_id = $request->user_id;

        if ($request->type == 'fund') {

            Balance::where('user_id', $user_id)->increment('demo', $request->amount);

            return redirect()->route('admin.user.single', ['user' => $user_id])->with('status', 'demo funded successful');
        } else {
            Balance::where('user_id', $user_id)->decrement('demo', $request->amount);

            return redirect()->route('admin.user.single', ['user' => $user_id])->with('status', 'demo debited successful');
        }

    }

    public function update_demo_balance(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
        ]);

        Balance::updateOrCreate(
            ['user_id' => $request->user_id, 'symbol' => 'USD'],
            ['demo' => $request->amount]
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Simulator capital updated to $'.number_format($request->amount, 2),
            ]);
        }

        return redirect()->route('admin.user.single', ['user' => $request->user_id])
            ->with('status', 'Simulator capital updated to $'.number_format($request->amount));
    }

    public function get_fund_referral(User $user)
    {
        return view('admin.fund_referral', [
            'user' => $user,
            'amount' => Balance::whereUserId($user->id)->first()->referral,
        ]);
    }

    public function get_debit_referral(User $user)
    {
        return view('admin.debit_referral', [
            'user' => $user,
            'amount' => Balance::whereUserId($user->id)->first()->referral,
        ]);
    }

    public function fund_referral(Request $request) // fund referral
    {$user_id = $request->user_id;

        if ($request->type == 'fund') {

            Balance::where('user_id', $user_id)->increment('referral', $request->amount);

            return redirect()->route('admin.user.single', ['user' => $user_id])->with('status', 'referral funded successful');
        } else {
            Balance::where('user_id', $user_id)->decrement('referral', $request->amount);

            return redirect()->route('admin.user.single', ['user' => $user_id])->with('status', 'referral debited successful');
        }

    }

    public function debit(Request $request)
    {
        $user_id = $request->user_id;
        $symbol = $request->symbol;
        $amount = $request->amount;

        // Backend Idempotency: Prevent duplicate credits within 10 seconds
        if ($request->type == 'fund') {
            $existing = Deposit::where('user_id', $user_id)
                ->where('pay_currency', $symbol)
                ->where('amount', $amount)
                ->where('created_at', '>=', Carbon::now()->subSeconds(10))
                ->first();

            if ($existing) {
                return redirect()->route('admin.user.single', ['user' => $user_id])->with('error', 'Duplicate deposit detected. Please wait a few seconds before trying again.');
            }
        }

        return DB::transaction(function () use ($request, $user_id, $symbol, $amount) {
            if ($request->type == 'fund') {
                // Ensure balance record exists for this symbol
                $exists = Balance::where('user_id', $user_id)->where('symbol', $symbol)->exists();
                if (! $exists) {
                    Balance::create([
                        'user_id' => $user_id,
                        'symbol' => $symbol,
                        'amount' => 0,
                        'demo' => 100000,
                        'name' => $symbol == 'USD' ? 'Dollar' : $symbol,
                        'image' => 'nill',
                    ]);
                }

                Balance::where('user_id', $user_id)->where('symbol', $symbol)->increment('amount', (float) $amount);
                $data = User::whereId($user_id)->lockForUpdate()->first();

                // Synchronize User Metrics
                $newTraded = (float) ($data->traded ?? 0) + (float) $amount;
                $updateData = [
                    'traded' => $newTraded,
                    'trades' => (int) ($data->trades ?? 0) + 1,
                ];
                if ($newTraded > (float) ($data->highest_investment ?? 0)) {
                    $updateData['highest_investment'] = $newTraded;
                }
                $data->update($updateData);

                $refer = Referral::where('referral_id', $user_id)->get();

                if ($refer) {
                    foreach ($refer as $value) {
                        $referral_id = $value->referral_id;
                        $user_id_ref = $value->user_id;

                        $divided_amount = (10 / 100) * $amount;

                        Referral::where('referral_id', $referral_id)->increment('balance', $divided_amount);

                        Balance::where('user_id', $user_id_ref)->where('symbol', $symbol)->increment('amount', $divided_amount);
                        Balance::where('user_id', $user_id_ref)->where('symbol', $symbol)->increment('referral', $divided_amount);
                    }
                }

                Deposit::insert([
                    'user_id' => $user_id,
                    'trx_id' => Str::random(6),
                    'pay_currency' => $symbol,
                    'amount' => $amount,
                    'name' => $data->first_name,
                    'status' => 'success',
                    'created_at' => Carbon::now(),
                ]);

                // Trigger Tier Upgrade Check
                if ($data) {
                    TierService::checkAndUpgrade($data);
                }

                $text = [
                    'greeting' => 'Hello '.$data->first_name,
                    'subject' => 'Deposit Confirmation',
                    'body' => 'A deposit of $'.$amount.'  has been credited to your account',
                    'data' => null,
                    'url' => null,
                    'thanks' => 'Thank you for choosing '.env('APP_NAME'),
                ];

                if ($request->check) {
                    try {
                        Notification::route('mail', $data->email)->notify(new DepositNotification($text));
                    } catch (\Throwable $th) {
                        // Suppress mail errors to prevent transaction rollback if mail fails
                    }
                }

                return redirect()->route('admin.user.single', ['user' => $user_id])->with('status', 'funded successful');
            } else {
                Balance::where('user_id', $user_id)->where('symbol', $symbol)->decrement('amount', $amount);

                $data = User::whereId($user_id)->first();

                return redirect()->route('admin.user.single', ['user' => $user_id])->with('status', 'debited successful');
            }
        });
    }

    public function bonus(Request $request)
    {
        $user_id = $request->user_id;

        if ($request->type == 'credit') {

            Balance::where('user_id', $user_id)->where('symbol', 'USD')->increment('bonus', $request->amount);

            return back()->with('status', 'Bonus funded successful');
        } else {
            Balance::where('user_id', $user_id)->where('symbol', 'USD')->decrement('bonus', $request->amount);

            return back()->with('status', 'Bonus debited successful');
        }

    }

    public function add_buy() // buy crypto page
    {$data = DB::table('buys')->orderByDesc('id')->paginate(20);

        return view('admin.buy', [
            'data' => $data,
        ]);
    }

    public function store_crypto(Request $request)   // store crypto
    {$this->validate($request, [
            'name' => 'required|unique:buys,name',
        ]);
        $newfilename = 'default_crypto.png';
        if ($request->hasFile('image')) {
            $filename = $request->file('image');
            $newfilename = time().'.'.$filename->getClientOriginalExtension();
            $request->file('image')->storeAs('image', $newfilename, 'public');
        }

        DB::table('buys')->insert([
            'name' => $request->name,
            'image' => $newfilename,
            'url' => $request->url,
            'min' => $request->min,
            'max' => $request->max,
        ]);

        return back()->with('status', 'New cryto site added');
    }

    public function show_crypto($id) // show single crypto page
    {$data = DB::table('buys')->find($id);

        return view('admin.show_crypto', [
            'data' => $data,
        ]);
    }

    public function edit_crypto(Request $request) // edit crypto
    {$data = DB::table('buys')->find($request->id);
        $updateData = [
            'name' => $request->name,
            'url' => $request->url,
            'min' => $request->min,
            'max' => $request->max,
        ];

        if ($request->hasFile('image')) {
            $filename = $request->file('image');
            $newfilename = time().'.'.$filename->getClientOriginalExtension();
            $request->file('image')->storeAs('image', $newfilename, 'public');
            $updateData['image'] = $newfilename;
        }

        DB::table('buys')->where('id', $request->id)->update($updateData);

        return back()->with('status', 'edited successfully');
    }

    public function delete_crypto($id)
    {
        DB::table('buys')->where('id', $id)->delete();

        return back()->with('status', 'delete successfully');
    }

    public function packages()  // packages
    {$data = Package::orderBy('id', 'desc')->paginate(20);

        return view('admin.packages', [
            'data' => $data,
        ]);
    }

    public function store_package(Request $request) // store new packages
    {$image = 'default_investment.png';
        if ($request->hasFile('image')) {
            $filename = $request->file('image');
            $image = time().'.'.$filename->getClientOriginalExtension();
            $request->file('image')->storeAs('image', $image, 'public');
        }

        Package::create([
            'name' => $request->name,
            'amount' => $request->amount,
            'buffer_percent' => $request->buffer_percent ?? 20.00,
            'per_withdrawal_percent' => $request->per_withdrawal_percent ?? 5.00,
            'perc' => $request->perc ?? 0,
            'day' => $request->day ?? 30,
            'trade' => $request->trade,
            'daily_trade' => $request->daily_trade ?? 0,
            'weekly_trade' => $request->weekly_trade ?? 0,
            'min_deposit' => $request->min_deposit ?? $request->amount,
            'features' => $request->features ?? [],
            'image' => $image,
        ]);

        return back()->with('status', 'New package added');
    }

    public function show_package(Package $package) // show single packages
    {return view('admin.show_packages', [
            'data' => $package,
        ]);
    }

    public function edit_package(Request $request) // edit packages
    {$package = Package::findOrFail($request->id);
        $data = [
            'name' => $request->name,
            'amount' => $request->amount,
            'buffer_percent' => $request->buffer_percent ?? 20.00,
            'per_withdrawal_percent' => $request->per_withdrawal_percent ?? 5.00,
            'perc' => $request->perc,
            'day' => $request->day,
            'trade' => $request->trade,
            'daily_trade' => $request->daily_trade ?? 0,
            'weekly_trade' => $request->weekly_trade ?? 0,
            'min_deposit' => $request->min_deposit,
            'features' => $request->features ?? [],
        ];

        if ($request->hasFile('image')) {
            $filename = $request->file('image');
            $newfilename = time().'.'.$filename->getClientOriginalExtension();
            $request->file('image')->storeAs('image', $newfilename, 'public');
            $data['image'] = $newfilename;
        }

        $package->update($data);

        return back()->with('status', 'package updated');
    }

    public function show_package_list(Package $package) // show single packages list to add
    {$list = Packages_lists::where('package_id', $package->id)->get();

        return view('admin.package_list', [
            'data' => $package,
            'list' => $list,
        ]);
    }

    public function store_package_list(Request $request) // store new packages list
    {Packages_lists::create([
            'package_id' => $request->id,
            'data' => $request->name,
        ]);

        return back()->with('status', 'New data added');
    }

    public function delete_package_list($id) // delete packages
    {Packages_lists::where('id', $id)->delete();

        return back()->with('status', 'delete successfully');
    }

    public function delete_package($id) // delete packages
    {Package::where('id', $id)->delete();

        return back()->with('status', 'delete successfully');
    }

    public function delete_amdin($id) // delete packages
    {Admin::where('id', $id)->delete();

        return back()->with('status', 'admin deleted');
    }

    public function logout()
    {
        Admin::where('id', auth()->guard('admin')->user()->id)->update([
            'data' => 0,
        ]);
        auth()->guard('admin')->logout();

        return redirect('/admin');
    }

    public function admin_wallets_index()
    {
        if (! Schema::hasTable('admin_wallets')) {
            return back()->with('error', 'Wallet table not migrated.');
        }
        $wallets = DB::table('admin_wallets')->orderBy('id', 'desc')->get();

        return view('admin.wallets_index', ['wallets' => $wallets]);
    }

    public function admin_wallets_store(Request $request)
    {
        $filename = 'default_wallet.png';
        if ($request->hasFile('image')) {
            $filename = time().'.'.$request->file('image')->getClientOriginalExtension();
            $request->file('image')->storeAs('image', $filename, 'public');
        }

        DB::table('admin_wallets')->insert([
            'name' => $request->name,
            'symbol' => $request->symbol,
            'address' => $request->address,
            'network' => $request->network,
            'icon_class' => $request->icon_class ?? 'ri-coin-line',
            'image' => $filename,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('status', 'New deposit wallet added.');
    }

    public function admin_wallets_update(Request $request)
    {
        $id = $request->id;
        $updateData = [
            'name' => $request->name,
            'symbol' => $request->symbol,
            'address' => $request->address,
            'network' => $request->network,
            'icon_class' => $request->icon_class,
            'is_active' => $request->has('is_active'),
            'updated_at' => now(),
        ];

        if ($request->hasFile('image')) {
            $filename = time().'.'.$request->file('image')->getClientOriginalExtension();
            $request->file('image')->storeAs('image', $filename, 'public');
            $updateData['image'] = $filename;
        }

        DB::table('admin_wallets')->where('id', $id)->update($updateData);

        return back()->with('status', 'Wallet updated.');
    }

    public function admin_wallets_delete($id)
    {
        DB::table('admin_wallets')->where('id', $id)->delete();

        return back()->with('status', 'Wallet deleted.');
    }

    public function admin_toggle_demo(Request $request)
    {
        $user = User::find($request->user);
        if (! $user) {
            return response()->json(['status' => false, 'message' => 'User not found']);
        }

        $newStatus = $user->is_demo ? 0 : 1;
        $user->update(['is_demo' => $newStatus]);

        // Find or create the USD balance record for the user
        $balance = Balance::where('user_id', $user->id)->where('symbol', 'USD')->first();

        // Auto-allocate $50,000 if switching to demo for the first time or if balance is 0
        if ($newStatus == 1) {
            if (! $balance) {
                Balance::create([
                    'user_id' => $user->id,
                    'symbol' => 'USD',
                    'demo' => 10000,
                    'amount' => 0,
                    'name' => 'United States Dollar',
                ]);
            } else {
                // If the balance exists but isn't 10,000, we update it to ensure standard
                if ($balance->demo != 10000) {
                    $balance->update(['demo' => 10000]);
                }
            }
        }

        return response()->json(['status' => true, 'message' => 'Demo mode toggled', 'demo_balance' => $balance->demo ?? 10000]);
    }

    public function toggle_basic_plan_access(Request $request)
    {
        $user = User::find($request->user);
        if (! $user) {
            return response()->json(['status' => false, 'message' => 'User not found']);
        }

        // Cycle: 0 (none) â†’ 1 (approved), 2 (pending) â†’ 1 (approved), 1 (approved) â†’ 0 (revoked)
        if ($user->basic_plan_approved == 1) {
            $user->update(['basic_plan_approved' => 0]);
            $newState = 'revoked';
        } else {
            $user->update(['basic_plan_approved' => 1]);
            $newState = 'approved';
        }

        return response()->json(['status' => true, 'state' => $newState, 'message' => "Basic plan access {$newState}"]);
    }

    public function toggle_hip_pro_access(Request $request)
    {
        $user = User::find($request->user);
        if (! $user) {
            return response()->json(['status' => false, 'message' => 'User not found']);
        }

        // Toggle logic: If they currently have access, turn it off (0). If they don't have access, turn it on (1).
        if ($user->hasHipProAccess()) {
            $user->update(['hip_pro_override' => 0]);
            $newState = 'revoked';
        } else {
            $user->update(['hip_pro_override' => 1]);
            $newState = 'granted';
        }

        return response()->json(['status' => true, 'state' => $newState, 'message' => "HIP Pro access {$newState}"]);
    }

    public function investments()
    {
        try {
            $investments = Investment_history::with(['user', 'package'])->latest()->paginate(20);
        } catch (\Exception $e) {
            $investments = new LengthAwarePaginator([], 0, 20);
            session()->flash('error', 'Failed to load investments: '.$e->getMessage());
        }

        return view('admin.investments', compact('investments'));
    }

    public function update_investment(Request $request)
    {
        $investment = Investment_history::findOrFail($request->id);

        if ($request->action == 'delete') {
            $investment->delete();

            return response()->json(['status' => 'Investment record deleted.']);
        }

        if ($request->action == 'pause') {
            $investment->update(['status' => 'paused']);

            return response()->json(['status' => 'Investment growth paused.']);
        }

        if ($request->action == 'resume') {
            $investment->update(['status' => 'active']);

            return response()->json(['status' => 'Investment growth resumed.']);
        }

        if ($request->action == 'edit') {
            $investment->update([
                'perc' => $request->perc,
                'day' => $request->day,
                'end_date' => \Illuminate\Support\Carbon::parse($investment->start_date)->addDays($request->day)->format('Y-m-d H:i:s'),
            ]);

            return response()->json(['status' => 'Investment parameters updated.']);
        }

        if ($request->action == 'force_complete') {
            if ($investment->status === 'completed') {
                return response()->json(['status' => 'Investment is already completed.']);
            }

            $user = $investment->user;
            $balance = Balance::where('user_id', $user->id)->where('symbol', 'USD')->first();

            $totalProfit = $investment->amount * ($investment->perc / 100);
            $totalDays = max(1, $investment->day);
            $dailyProfit = $totalProfit / $totalDays;

            $daysPaid = 0;
            if ($investment->last_credited_date) {
                $start = \Illuminate\Support\Carbon::parse($investment->start_date)->startOfDay();
                $last = \Illuminate\Support\Carbon::parse($investment->last_credited_date)->startOfDay();
                $daysPaid = $start->diffInDays($last);
            }

            $remainingDays = $totalDays - $daysPaid;
            if ($remainingDays < 0) {
                $remainingDays = 0;
            }

            $remainingProfit = $remainingDays * $dailyProfit;

            if ($balance) {
                $balance->increment('amount', $investment->amount + $remainingProfit);
            }

            $investment->update(['status' => 'completed', 'end_date' => now()->format('Y-m-d H:i:s')]);

            Noti::create([
                'user_id' => $user->id,
                'title' => 'Investment Completed',
                'message' => 'Your investment in '.($investment->plan_name ?? 'ETF').' has completed early. Principal of $'.number_format($investment->amount, 2).' and remaining profit has been credited to your wallet.',
                'status' => 'unread',
            ]);

            return response()->json(['status' => 'Investment marked as completed and payout issued.']);
        }

        return response()->json(['error' => 'Invalid action'], 400);
    }

    public function delete_investment($id)
    {
        Investment_history::where('id', $id)->delete();

        return back()->with('message', 'Investment record deleted.');
    }

    public function clear_cache()
    {
        try {
            Artisan::call('optimize:clear');
            Artisan::call('optimize');

            return back()->with('success', 'Cache cleared and optimized successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error clearing cache: '.$e->getMessage());
        }
    }
}
