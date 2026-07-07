<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Balance;
use App\Models\Deposit;
use App\Models\Noti;
use App\Models\Payment;
use App\Models\Referral;
use App\Models\Site_setting;
use App\Models\User;
use App\Models\Withdrawal;
use App\Notifications\DepositNotification;
use App\Services\HistoryBalanceService;
use App\Services\TierService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function withdrawal_settings_index()
    {
        $tax = DB::table('tax')->where('id', 1)->first();
        $users = User::select('id', 'first_name', 'last_name', 'email', 'upgrade_code', 'tax_code', 'demorage', 'upgrade_code_check', 'tax_code_check', 'demorage_check')->orderBy('id', 'desc')->get();

        return view('admin.withdrawal_settings', [
            'tax' => $tax,
            'users' => $users,
        ]);
    }

    public function generate_user_codes(Request $request)
    {
        $userId = $request->user_id;
        $codes = [];

        if ($request->has('generate_clearance') || $request->has('generate_all')) {
            $codes['upgrade_code'] = Str::random(6);
        }
        if ($request->has('generate_tax') || $request->has('generate_all')) {
            $codes['tax_code'] = Str::random(6);
        }
        if ($request->has('generate_liquidation') || $request->has('generate_all')) {
            $codes['demorage'] = Str::random(6);
        }

        if (! empty($codes)) {
            User::where('id', $userId)->update($codes);
        }

        $user = User::select('id', 'upgrade_code', 'tax_code', 'demorage', 'upgrade_code_check', 'tax_code_check', 'demorage_check')->find($userId);

        return response()->json(['status' => true, 'user' => $user, 'message' => 'Codes generated successfully']);
    }

    public function toggle_user_code_check(Request $request)
    {
        $userId = $request->user_id;
        $field = $request->field; // upgrade_code_check, tax_code_check, demorage_check

        $allowed = ['upgrade_code_check', 'tax_code_check', 'demorage_check'];
        if (! in_array($field, $allowed)) {
            return response()->json(['status' => false, 'message' => 'Invalid field']);
        }

        $user = User::find($userId);
        $newVal = $user->$field == 'on' ? 'off' : 'on';
        User::where('id', $userId)->update([$field => $newVal]);

        return response()->json(['status' => true, 'value' => $newVal, 'message' => 'Toggle updated']);
    }

    public function withdrawal_settings_update(Request $request)
    {
        Site_setting::where('id', 1)->update([
            'withdrawal_flow_enabled' => $request->has('withdrawal_flow_enabled') ? 1 : 0,
            'default_withdrawal_security' => $request->default_withdrawal_security,
            'clearance_pin_name' => $request->clearance_pin_name,
            'tax_pin_name' => $request->tax_pin_name,
            'liquidation_pin_name' => $request->liquidation_pin_name,
        ]);

        DB::table('tax')->where('id', 1)->update([
            'percentage' => $request->tax_percentage,
            'amount' => $request->tax_amount,
        ]);

        return back()->with('status', 'Withdrawal configurations updated successfully');
    }

    public function bank_content(Request $request)
    {
        DB::table('site_settings')->where('id', 1)->update([
            'bank' => $request->summernote3,
        ]);

        return back()->with('status', 'bank messages  updated');
    }

    public function bank_limit(Request $request)
    {
        DB::table('site_settings')->where('id', 1)->update([
            'bank_limit' => $request->bank_limit,
        ]);

        return back()->with('status', 'bank minimum limited updated');
    }

    public function tax_settings(Request $request)
    {
        DB::table('tax')->where('id', 1)->update([
            'amount' => $request->amount,
            'percentage' => $request->percentage,
        ]);

        return back()->with('status', 'tax setting updated');
    }

    public function chineses_deposit()
    {
        $data = DB::table('chineses_deposit')->where('id', 1)->first();
        $deposit = DB::table('chineses')->orderBy('id', 'desc')->get();

        return view('admin.chiness', [
            'data' => $data,
            'deposit' => $deposit,
        ]);
    }

    public function update_chiness_deposit(Request $request)
    {
        DB::table('chineses_deposit')->where('id', 1)->update([
            'data' => $request->data,
        ]);

        return back()->with('status', 'deposit updated');
    }

    public function withdrawal(Request $request)
    {
        // 1. Fetch main withdrawals (excluding internal transfers)
        $withdrawals = Withdrawal::with('user')
            ->where('type', '!=', 'Transfer')
            ->orderBy('created_at', 'DESC')
            ->get();

        // 2. Fetch legacy bank transfers and map to unified structure
        $bank = DB::table('transfer_payment')
            ->join('users', 'transfer_payment.user_id', '=', 'users.id')
            ->select('transfer_payment.*', 'users.first_name', 'users.last_name', 'users.email')
            ->orderBy('transfer_payment.created_at', 'DESC')
            ->get()
            ->map(function ($item) {
                return (object) [
                    'id' => $item->id,
                    'user_id' => $item->user_id,
                    'amount' => $item->amount,
                    'address' => 'BANK_DETAILS: '.$item->bank_name,
                    'status' => $item->status,
                    'type' => 'Bank (L)',
                    'source' => 'transfer_payment',
                    'created_at' => Carbon::parse($item->created_at),
                    'user' => (object) [
                        'first_name' => $item->first_name,
                        'last_name' => $item->last_name,
                        'email' => $item->email,
                    ],
                ];
            });

        // 3. Fetch legacy bonus withdrawals and map to unified structure
        $bonus = DB::table('bonus_withdrawal')
            ->join('users', 'bonus_withdrawal.user_id', '=', 'users.id')
            ->select('bonus_withdrawal.*', 'users.first_name', 'users.last_name', 'users.email')
            ->orderBy('bonus_withdrawal.created_at', 'DESC')
            ->get()
            ->map(function ($item) {
                return (object) [
                    'id' => $item->id,
                    'user_id' => $item->user_id,
                    'amount' => $item->amount,
                    'address' => 'BONUS_WALLET',
                    'status' => $item->status,
                    'type' => 'Bonus (L)',
                    'source' => 'bonus_withdrawal',
                    'created_at' => Carbon::parse($item->created_at),
                    'user' => (object) [
                        'first_name' => $item->first_name,
                        'last_name' => $item->last_name,
                        'email' => $item->email,
                    ],
                ];
            });

        // 4. Unify and Sort by Timestamp
        $allData = $withdrawals->map(function ($item) {
            $item->source = 'withdrawals';

            return $item;
        })->concat($bank)->concat($bonus)->sortByDesc('created_at');

        // 5. Manual Pagination for the combined collection
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 25;
        $currentPageItems = $allData->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $data = new LengthAwarePaginator($currentPageItems, $allData->count(), $perPage);
        $data->setPath($request->url());

        return view('admin.withdrawal', [
            'data' => $data,
        ]);
    }

    public function withdrawal_transfer()
    {
        $data = Withdrawal::with('user')->where('type', '=', 'Transfer')->orderBy('id', 'desc')->get();

        return view('admin.withdrawal_transfer', [
            'data' => $data,
        ]);
    }

    public function deposit()
    {
        $data = Deposit::orderBy('id', 'desc')->paginate(25);

        return view('admin.deposit', [
            'data' => $data,
        ]);
    }

    public function updatedeposit(Request $request)
    {
        $data = Deposit::where('id', $request->id)->first();
        $user_id = $data->user_id;
        $symbol = $data->pay_currency;

        if ($request->action == 'Approved') {
            // Handle custom amount override
            if ($request->has('custom_amount') && is_numeric($request->custom_amount) && $request->custom_amount > 0) {
                $data->amount = $request->custom_amount;
                
                Deposit::where('id', $data->id)->update([
                    'status' => 'success',
                    'amount' => $request->custom_amount,
                ]);
            } else {
                Deposit::where('id', $data->id)->update([
                    'status' => 'success',
                ]);
            }

            // Sync status with proof table
            if (isset($data->trx_id)) {
                $proofUpdate = ['status' => 'success'];
                if ($request->has('custom_amount') && is_numeric($request->custom_amount) && $request->custom_amount > 0) {
                    $proofUpdate['amount'] = $request->custom_amount;
                }
                DB::table('proof')->where('trx_id', $data->trx_id)->update($proofUpdate);
            }
            // Credit the balance — always credit USD since amounts are requested and recorded in USD
            $creditSymbol = 'USD';

            // Ensure USD balance exists before incrementing
            $balanceExists = Balance::where('user_id', $data->user_id)->where('symbol', $creditSymbol)->exists();
            if (! $balanceExists) {
                Balance::create(['user_id' => $data->user_id, 'symbol' => 'USD', 'amount' => 0, 'demo' => 100000]);
            }
            Balance::where('user_id', $data->user_id)->where('symbol', $creditSymbol)->increment('amount', $data->amount);

            // Synchronize User Metrics
            $user = User::find($data->user_id);
            if ($user) {
                $newTraded = $user->traded + $data->amount;
                $updateData = [
                    'traded' => $newTraded,
                    'trades' => $user->trades + 1,
                ];
                if ($newTraded > $user->highest_investment) {
                    $updateData['highest_investment'] = $newTraded;
                }
                $user->update($updateData);
            }

            // Create in-platform notification for user
            Noti::create([
                'user_id' => $data->user_id,
                'message' => 'Your deposit of $'.number_format($data->amount, 2).' has been approved and credited to your account.',
                'status' => 'unread',
            ]);

            $refer = Referral::where('referral_id', $user_id)->get();

            if ($refer) {
                foreach ($refer as $value) {
                    $referral_id = $value->referral_id;
                    $user = $value->user_id;

                    $divided_amount = (10 / 100) * $request->amount;

                    Referral::where('referral_id', $referral_id)->increment('balance', $divided_amount);

                    Balance::where('user_id', $user)->where('symbol', $creditSymbol)->increment('amount', $divided_amount);
                    Balance::where('user_id', $user)->where('symbol', $creditSymbol)->increment('referral', $divided_amount);
                }
            }

            $user = User::find($user_id);
            $text = [
                'greeting' => 'Hello '.($user->first_name ?? 'User'),
                'subject' => 'Deposit Approved',
                'body' => 'Your deposit of $'.number_format($data->amount, 2).' has been approved and credited to your account.',
                'data' => null,
                'url' => null,
                'thanks' => 'Thank you for choosing '.env('APP_NAME'),
            ];
            try {
                if ($user && $user->email) {
                    Notification::route('mail', $user->email)->notify(new DepositNotification($text));
                }
            } catch (\Throwable $th) {
            }

            // Trigger Tier Upgrade Check
            $user = User::find($user_id);
            if ($user) {
                TierService::checkAndUpgrade($user);
            }

        } else {
            if (isset($data->trx_id)) {
                DB::table('proof')->where('trx_id', $data->trx_id)->delete();
            }
            Deposit::where('id', $data->id)->delete();
        }

        return response()->json(['status' => 'Deposit state updated']);
    }

    public function updatewithdrawals(Request $request)
    {
        $source = $request->source ?? 'withdrawals';

        if ($source == 'transfer_payment') {
            $data = DB::table('transfer_payment')->where('id', $request->id)->first();
        } elseif ($source == 'bonus_withdrawal') {
            $data = DB::table('bonus_withdrawal')->where('id', $request->id)->first();
        } else {
            $data = Withdrawal::where('id', $request->id)->first();
        }

        if (! $data) {
            return response()->json(['error' => 'Record not found'], 404);
        }

        $user = User::where('id', $data->user_id)->first();

        if ($request->action == 'Approved') {
            try {
                DB::beginTransaction();

                // Re-fetch the record within the transaction for absolute safety
                if ($source == 'transfer_payment') {
                    $dbRecord = DB::table('transfer_payment')->where('id', $data->id)->lockForUpdate()->first();
                } elseif ($source == 'bonus_withdrawal') {
                    $dbRecord = DB::table('bonus_withdrawal')->where('id', $data->id)->lockForUpdate()->first();
                } else {
                    $dbRecord = Withdrawal::where('id', $data->id)->lockForUpdate()->first();
                }

                if (! $dbRecord) {
                    DB::rollBack();

                    return response()->json(['error' => 'Record not found during transaction'], 404);
                }

                // Prevent double-deduction if already confirmed
                if (strtolower($dbRecord->status) == 'confirmed') {
                    DB::rollBack();

                    return response()->json(['status' => 'Withdrawal already confirmed']);
                }

                // Deduct from USD wallet as the platform uses USD as the base currency for all withdrawals
                $assetSymbol = 'USD';
                $balanceRecord = Balance::where('user_id', $data->user_id)
                    ->where('symbol', $assetSymbol)
                    ->lockForUpdate()
                    ->first();

                if (! $balanceRecord || $balanceRecord->amount < $data->amount) {
                    DB::rollBack();

                    return response()->json(['error' => 'Insufficient user balance to approve this withdrawal'], 400);
                }

                // Deduct the amount
                $balanceRecord->decrement('amount', $data->amount);

                // Update the withdrawal status INSIDE the transaction
                if ($source == 'transfer_payment') {
                    DB::table('transfer_payment')->where('id', $data->id)->update(['status' => 'confirmed']);
                } elseif ($source == 'bonus_withdrawal') {
                    DB::table('bonus_withdrawal')->where('id', $data->id)->update(['status' => 'confirmed']);
                } else {
                    Withdrawal::where('id', $data->id)->update(['status' => 'confirmed']);
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Withdrawal approval failed', ['error' => $e->getMessage()]);

                return response()->json(['error' => 'An error occurred during balance reconciliation: '.$e->getMessage()], 500);
            }

            $text = [
                'greeting' => 'Hello '.($user->first_name ?? 'User'),
                'subject' => 'Withdrawal Confirmation',
                'body' => 'Your withdrawal of $'.number_format($data->amount, 2).' has been processed and approved. The funds have been sent to your provided address.',
                'data' => null,
                'url' => null,
                'thanks' => 'Thank you for choosing '.env('APP_NAME'),
            ];

            try {
                if ($user && $user->email) {
                    Notification::route('mail', $user->email)->notify(new DepositNotification($text));
                }
            } catch (\Throwable $th) {
            }

        } else {
            if ($source == 'transfer_payment') {
                DB::table('transfer_payment')->where('id', $data->id)->update(['status' => 'reversed']);
            } elseif ($source == 'bonus_withdrawal') {
                DB::table('bonus_withdrawal')->where('id', $data->id)->update(['status' => 'reversed']);
            } else {
                Withdrawal::where('id', $data->id)->update(['status' => 'reversed']);
            }
        }

        return response()->json(['status' => 'Withdrawal state updated']);
    }

    public function updateTransfer(Request $request)
    {

        $data = Withdrawal::where('id', $request->id)->first();

        $email = User::where('email', $data->address)->first();

        $user = User::where('id', $data->user_id)->first();

        if ($request->action == 'Approved') {
            Balance::where('user_id', $email->id)->where('symbol', 'USD')->increment('amount', $data->amount);

            Withdrawal::where('id', $data->id)->update([
                'status' => 'confirmed',
            ]);

            Withdrawal::create([
                'user_id' => $email->id,
                'trx_id' => Str::random(6),
                'type' => 'Received',
                'address' => $email->email,
                'amount' => $data->amount,
                'status' => 'confirmed',
            ]);

            $text = [
                'greeting' => 'Hello '.($user->first_name ?? 'User'),
                'subject' => 'Transfer Confirmation',
                'body' => 'Your transfer of $'.$data->amount.' has been processed and approved, the transfer amount has now reflect in recepient account',
                'data' => null,
                'url' => null,
                'thanks' => 'Thank you for choosing '.env('APP_NAME'),
            ];
            try {
                Notification::route('mail', $user->email)->notify(new DepositNotification($text));

            } catch (\Throwable $th) {
                // throw $th;
            }
        } else {
            Withdrawal::where('id', $data->id)->update([
                'status' => 'reversed',
            ]);

            Balance::where('user_id', $data->user_id)->where('symbol', 'USD')->increment('amount', $data->amount);

            $text = [
                'greeting' => 'Hello '.($user->first_name ?? 'User'),
                'subject' => 'Transfer reversed',
                'body' => 'Your transfer of $'.$data->amount.' has been return to your account',
                'data' => null,
                'url' => null,
                'thanks' => 'Thank you for choosing '.env('APP_NAME'),
            ];

            try {
                Notification::route('mail', $user->email)->notify(new DepositNotification($text));

            } catch (\Throwable $th) {
                // throw $th;
            }
        }

        return response()->json(['status' => 'transfer state updated']);
    }

    public function updatewithdrawal_bank($id)
    {

        DB::table('transfer_payment')->where(['id' => $id])->update([
            'status' => 'confirmed',
        ]);

        return back()->with('status', 'withdrawal state updated');
    }

    public function edit_deposit($id)
    {
        $deposit = Deposit::findOrFail($id);

        return view('admin.history.deposit_edit', compact('deposit'));
    }

    public function update_deposit(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:success,pending,failed',
            'amount' => 'required|numeric|min:0',
            'created_at' => 'required|date',
        ]);

        $deposit = Deposit::findOrFail($id);
        $oldStatus = $deposit->status;
        $oldAmount = $deposit->amount;

        $deposit->status = $request->status;
        $deposit->amount = $request->amount;
        $deposit->created_at = $request->created_at;

        if ($oldStatus !== $deposit->status || $oldAmount != $deposit->amount) {
            HistoryBalanceService::recalculateDeposit($deposit->user_id, $oldStatus, $deposit->status, $oldAmount, $deposit->amount);
        }

        $deposit->save();

        return redirect()->back()->with('success', 'Deposit updated and balance recalculated successfully.');
    }

    public function edit_withdrawal($id)
    {
        $withdrawal = Withdrawal::findOrFail($id);

        return view('admin.history.withdrawal_edit', compact('withdrawal'));
    }

    public function update_withdrawal(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:success,pending,reversed,confirmed',
            'amount' => 'required|numeric|min:0',
            'created_at' => 'required|date',
        ]);

        $withdrawal = Withdrawal::findOrFail($id);
        $oldStatus = $withdrawal->status;
        $oldAmount = $withdrawal->amount;

        $withdrawal->status = $request->status;
        $withdrawal->amount = $request->amount;
        $withdrawal->created_at = $request->created_at;

        if ($oldStatus !== $withdrawal->status || $oldAmount != $withdrawal->amount) {
            HistoryBalanceService::recalculateWithdrawal($withdrawal->user_id, $oldStatus, $withdrawal->status, $oldAmount, $withdrawal->amount);
        }

        $withdrawal->save();

        return redirect()->back()->with('success', 'Withdrawal updated and balance recalculated successfully.');
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

    public function delete_wallet($id)
    {
        Payment::where('id', $id)->delete();

        return back()->with('status', 'wallet address deleted deleted');
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

        return back()->with('status','withdrawal access updated');
    }
}
