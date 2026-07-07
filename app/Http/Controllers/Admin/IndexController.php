<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Balance;
use App\Models\Bot;
use App\Models\Deposit;
use App\Models\Email;
use App\Models\Noti;
use App\Models\Order;
use App\Models\Package;
use App\Models\Referral;
use App\Models\Signal;
use App\Models\Site_setting;
use App\Models\User;
use App\Models\Withdrawal;
use App\Notifications\DepositNotification;
use App\Services\HistoryBalanceService;
use App\Services\TierService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class IndexController extends Controller
{
    public function index()
    {
        if (auth()->guard('admin')->check()) {
            return redirect('admin/dashboard');
        }
        return view('admin.index');
    }

    public function all_trades()
    {
        $data = Order::with(['user', 'exchanges'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($data);
    }

    public function update_trades(Request $request)
    {
        $order = Order::where('id', $request->id)->first();

        $id = $order->id;
        $user = $order->user_id;
        $amount = $order->amount;
        $win = $order->win;
        $loss = $order->loss;
        $symbol = $order->symbol;
        $mode = $order->types;
        $expire_date = $order->expire_date;

        $divided_amount = ($win / 100) * $amount;

        $win_loss = $amount + $divided_amount;

        $divided_amounts = ($loss / 100) * $amount;

        $loss_amount = $divided_amounts;

        $loss_data = $amount - $divided_amounts;

        if ($request->data === 'win') {
            Order::where('id', $id)->update(['admin_status' => 'win']);
        } elseif ($request->data === 'loss') {
            Order::where('id', $id)->update(['admin_status' => 'loss']);
        } elseif ($request->data === 'draw') {
            Order::where('id', $id)->update(['admin_status' => 'draw']);
        }

        if ($request->has('strike_rate')) {
            Order::where('id', $id)->update(['strike_rate' => $request->strike_rate]);
        }

        return response()->json(true);
    }
    public function override_trade(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:win,loss,draw,auto',
            'earningPercentage' => 'nullable|numeric|min:-100|max:500',
            'overrideAmount' => 'nullable|numeric',
            'reason' => 'required|string|min:10',
        ]);

        $order = Order::findOrFail($id);

        if ($order->status !== 'pending') {
            return response()->json(['error' => 'Trade is not active.'], 400);
        }

        $amount = $order->amount;
        $percentage = $request->earningPercentage ?? 0;
        $overrideAmount = $request->overrideAmount;
        $status = $request->status;

        if ($status === 'auto') {
            return response()->json(['success' => true, 'message' => 'Left in auto mode.']);
        }

        $p_l = 0;
        if (!is_null($overrideAmount)) {
            if ($status === 'win') {
                $p_l = abs($overrideAmount);
            } elseif ($status === 'loss') {
                $p_l = -abs($overrideAmount);
            } else {
                $p_l = 0;
                $status = 'draw';
            }
        } else {
            if ($status === 'win') {
                $p_l = ($percentage / 100) * $amount;
            } elseif ($status === 'loss') {
                $p_l = -abs(($percentage / 100) * $amount);
            } else {
                $p_l = 0;
                $status = 'draw';
            }
        }

        $order->status = $status;
        $order->p_l = $p_l;
        $order->is_overridden = true;
        $order->override_by = auth()->guard('admin')->id() ?? 1;
        $order->override_reason = $request->reason;
        $order->override_timestamp = now();
        $order->forced_outcome = $request->status;
        $order->forced_percentage = $percentage;
        $order->save();

        $return_amount = $amount + $p_l;
        if ($return_amount > 0) {
            Balance::where('user_id', $order->user_id)->increment('amount', $return_amount);
        }

        \DB::table('activities')->insert([
            'user_id' => $order->user_id,
            'activities' => "Trade {$order->trade_id} manually closed. Outcome: {$status}",
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \App\Models\AdminAuditLog::create([
            'admin_id' => auth()->guard('admin')->id() ?? 1,
            'action' => 'trade_override',
            'target_type' => 'order',
            'target_id' => $id,
            'details' => json_encode([
                'reason' => $request->reason,
                'status' => $status,
                'p_l' => $p_l
            ])
        ]);

        \App\Models\Noti::create([
            'user_id' => $order->user_id,
            'title' => 'Trade Settlement Adjusted',
            'message' => "Your trade on {$order->symbol} has been adjusted and finalized with settlement outcome {$status}.",
            'status' => 'unread'
        ]);

        return response()->json(['success' => true, 'trade' => $order]);
    }

    public function delete_trade($id)
    {

        $data = Order::where('id', $id)->first();

        Balance::where('user_id', $data->user_id)->increment('amount', $data->amount);

        $data = Order::where('id', $id)->delete();

        return response()->json(true);

    }

    public function all_trade()
    {
        return view('admin.trades');
    }

    public function levels()
    {
        $data = DB::table('level')->orderBy('id', 'asc')->get();

        return view('admin.levels', [
            'data' => $data,
        ]);
    }

    public function edit_level(Request $request)
    {
        DB::table('level')->where('id', $request->level_id)->update([
            'plan' => $request->plan,
            'min' => $request->min,
            'max' => $request->max,
        ]);

        return back()->with('status', 'withdrawal levels updated');
    }

    public function wallet()
    {
        $data = DB::table('wallet')->orderByDesc('id')->get();

        return view('admin.wallet', [
            'data' => $data,
        ]);
    }

    public function wallet_post(Request $request)
    {

        $filename = $request->file('image');
        $newfilename = time().'.'.$filename->getClientOriginalExtension();

        $request->file('image')->storeAs('image', $newfilename, 'public');

        $user = User::all();

        foreach ($user as $data) {
            DB::table('balances')->insert([
                'user_id' => $data->id,
                'name' => $request->name,
                'image' => $newfilename,
                'symbol' => $request->symbol,
                'amount' => 0,
                'demo' => 0,
                'bitcoin' => 0,
                // 'created_at'=>Carbon::now(),
            ]);
        }

        DB::table('wallet')->insert([
            'name' => $request->name,
            'image' => $newfilename,
            'symbol' => $request->symbol,
            'balance' => 0,
        ]);

        return back()->with('status', 'Wallet added');
    }

    public function edit_wallet($id)
    {
        $data = DB::table('wallet')->where('id', $id)->first();

        return view('admin.wallet_edit', [
            'data' => $data,
        ]);
    }

    public function edit_wallet_post(Request $request)
    {
        $wallet = DB::table('wallet')->where('id', $request->id)->first();
        $user = User::all();

        if ($request->hasFile('image')) {

            $filename = $request->file('image');
            $newfilename = time().'.'.$filename->getClientOriginalExtension();

            $request->file('image')->storeAs('image', $newfilename, 'public');

            foreach ($user as $data) {
                DB::table('balances')->where('name', $request->id)->update([
                    'name' => $request->name,
                    'image' => $newfilename,
                    'symbol' => $request->symbol,
                    // 'created_at'=>Carbon::now(),
                ]);
            }

            DB::table('wallet')->where('name', $request->id)->update([
                'name' => $request->name,
                'image' => $newfilename,
                'symbol' => $request->symbol,
            ]);
        } else {
            foreach ($user as $data) {
                DB::table('balances')->where('name', $request->id)->update([
                    'name' => $request->name,
                    'image' => $wallet->image,
                    'symbol' => $request->symbol,
                    // 'created_at'=>Carbon::now(),
                ]);
            }
            DB::table('wallet')->where('name', $request->id)->update([
                'name' => $request->name,
                'image' => $wallet->image,
                'symbol' => $request->symbol,
            ]);
        }

        return back()->with('status', 'wallet setting updated');
    }

    public function site_template(Request $request)
    {

        DB::table('templates')->where('id', 1)->update([
            'template' => $request->data,
        ]);

        return back()->with('status', 'site templates updated');
    }

    public function onOffGoogle(Request $request)
    {
        $user = User::whereId($request->user)->first();

        if ($user->is_2fa_enabled == '1') {
            User::whereId($request->user)->update([
                'is_2fa_enabled' => '0',
            ]);
        } else {
            User::whereId($request->user)->update([
                'is_2fa_enabled' => '1',
            ]);
        }

        return response()->json(['status' => true]);
    }

    public function onOffOtp(Request $request)
    {
        $user = User::whereId($request->user)->first();

        if ($user->otp_enabled == '1') {
            User::whereId($request->user)->update([
                'otp_enabled' => '0',
            ]);
        } else {
            User::whereId($request->user)->update([
                'otp_enabled' => '1',
            ]);
        }

        return response()->json(['status' => true]);
    }

    public function general(Request $request)
    {
        $user = User::whereId($request->user)->first();

        if ($user->general == '1') {
            User::whereId($request->user)->update([
                'general' => '0',
            ]);
        } else {
            User::whereId($request->user)->update([
                'general' => '1',
            ]);
        }

        return response()->json(['status' => true]);
    }

    public function onOffEmail(Request $request)
    {
        $user = User::whereId($request->user)->first();

        if ($user->email_verified == '1') {
            User::whereId($request->user)->update([
                'email_verified' => '0',
            ]);
        } else {
            User::whereId($request->user)->update([
                'email_verified' => '1',
            ]);

            $text = [
                'greeting' => 'Hello '.$user->first_name,
                'subject' => 'Account Verification',
                'body' => 'Your account has been verified you can now enjoy our seemless trading experience',
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

        return response()->json(['status' => true]);
    }

    public function update_engine_settings(Request $request)
    {
        DB::table('emergency')->where('id', 1)->update([
            'engine_mode' => $request->engine_mode,
            'win_rate' => $request->win_rate,
        ]);

        return back()->with('status', 'Trading engine settings updated successfully');
    }

    public function siteindex()
    {
        $data = Site_setting::all();
        $code = DB::table('chats')->where('id', 1)->first();
        $email = DB::table('admin_email')->where('id', 1)->first();
        $message = DB::table('system_message')->where('id', 1)->first();
        $template = DB::table('templates')->where('id', 1)->first();
        $tax = DB::table('tax')->where('id', 1)->first();
        $lock = DB::table('lock_message')->where('id', 1)->first();
        $engine = DB::table('emergency')->where('id', 1)->first();

        return view('admin.site_setting', [
            'data' => $data,
            'code' => $code,
            'email' => $email,
            'message' => $message,
            'template' => $template,
            'tax' => $tax,
            'lock' => $lock,
            'engine' => $engine,
        ]);
    }

    public function cronjobs()
    {
        return view('admin.cronjobs');
    }

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

    public function updateTradingAutoApprove(Request $request)
    {
        $fields = [];

        if ($request->has('spot_auto_approve_submit')) {
            $fields['spot_auto_approve'] = $request->has('spot_auto_approve') ? 1 : 0;
            $fields['spot_auto_win_percent'] = $request->spot_auto_win_percent ?? 0;
        }

        if ($request->has('margin_auto_approve_submit')) {
            $fields['margin_auto_approve'] = $request->has('margin_auto_approve') ? 1 : 0;
            $fields['margin_auto_win_percent'] = $request->margin_auto_win_percent ?? 0;
        }

        if ($request->has('futures_auto_approve_submit')) {
            $fields['futures_auto_approve'] = $request->has('futures_auto_approve') ? 1 : 0;
            $fields['futures_auto_win_percent'] = $request->futures_auto_win_percent ?? 0;
        }

        if ($request->has('trades_auto_approve_submit')) {
            $fields['trades_auto_approve'] = $request->has('trades_auto_approve') ? 1 : 0;
            $fields['trades_auto_win_percent'] = $request->trades_auto_win_percent ?? 0;
        }

        if (! empty($fields)) {
            Site_setting::where('id', 1)->update($fields);
        }

        return back()->with('status', 'Auto-Approve settings updated successfully.');
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

    public function bank_error_message(Request $request)
    {
        DB::table('site_settings')->where('id', 1)->update([
            'withdrawal_message' => $request->withdrawal_message,
        ]);

        return back()->with('status', 'bank error message updated');
    }

    public function user_lock(Request $request)
    {
        DB::table('lock_message')->where('id', 1)->update([
            'title' => $request->title,
            'message' => $request->message,
        ]);

        return back()->with('status', 'lock messages  updated');
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

    public function admin_email(Request $request)
    {
        DB::table('admin_email')->where('id', 1)->update([
            'email' => $request->email,
        ]);

        return back()->with('status', 'admin email updated');
    }

    public function chat_code(Request $request)
    {
        DB::table('chats')->where('id', 1)->update([
            'code' => $request->code,
        ]);

        return back()->with('status', 'chat widget updated');
    }

    public function system_message(Request $request)
    {
        DB::table('system_message')->where('id', 1)->update([
            'message' => $request->message,
        ]);

        return back()->with('status', 'system message updated');
    }

    public function store_site(Request $request)
    {
        $site = Site_setting::where('id', $request->id)->first();
        if (! $site) {
            return back()->with('error', 'Site setting not found');
        }

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'meta' => $request->meta,
            'smtp_host' => $request->smtp_host,
            'smtp_port' => $request->smtp_port,
            'smtp_user' => $request->smtp_user,
            'smtp_pass' => $request->smtp_pass,
            'smtp_encryption' => $request->smtp_encryption,
            'mail_from_address' => $request->mail_from_address,
            'app_debug' => $request->app_debug,
            'app_url' => $request->app_url,
            'pusher_app_id' => $request->pusher_app_id,
            'pusher_app_key' => $request->pusher_app_key,
            'pusher_app_secret' => $request->pusher_app_secret,
            'pusher_app_cluster' => $request->pusher_app_cluster,
            'alphavantage_api_key' => $request->alphavantage_api_key,
            'finnhub_api_key' => $request->finnhub_api_key,
            'coingecko_api_key' => $request->coingecko_api_key,
            'binance_api_key' => $request->binance_api_key,
            'binance_api_secret' => $request->binance_api_secret,
            'use_round_robin' => $request->has('use_round_robin') ? 1 : 0,
            'twelve_data_api_key' => $request->twelve_data_api_key,
            'polygon_api_key' => $request->polygon_api_key,
            'auto_sync_logos' => $request->has('auto_sync_logos') ? 1 : 0,
            'google_client_id' => $request->google_client_id,
            'google_client_secret' => $request->google_client_secret,
            'google_redirect_url' => $request->google_redirect_url,
            'screenshot_api_key' => $request->screenshot_api_key,
            'maker_fee' => $request->maker_fee ?? 0.0010,
            'taker_fee' => $request->taker_fee ?? 0.0020,
            'withdrawal_fee' => $request->withdrawal_fee ?? 5.0000,
        ];

        // Handle Logo
        if ($request->hasFile('logo')) {
            $request->validate([
                'logo' => ['required', 'file', 'mimes:jpeg,png,jpg,gif,ico,svg', 'max:2048'],
            ]);
            $filename = $request->file('logo');
            $newfilename = 'logo_'.time().'.'.$filename->getClientOriginalExtension();
            $request->file('logo')->storeAs('image', $newfilename, 'public');
            $updateData['logo'] = $newfilename;
        }

        // Handle Logo Dark
        if ($request->hasFile('logo_dark')) {
            $request->validate([
                'logo_dark' => ['required', 'file', 'mimes:jpeg,png,jpg,gif,ico,svg', 'max:2048'],
            ]);
            $filename = $request->file('logo_dark');
            $newfilename = 'logo_dark_'.time().'.'.$filename->getClientOriginalExtension();
            $request->file('logo_dark')->storeAs('image', $newfilename, 'public');
            $updateData['logo_dark'] = $newfilename;
        }

        // Handle Favicon (New)
        if ($request->hasFile('favicon')) {
            $request->validate([
                'favicon' => ['required', 'file', 'mimes:jpeg,png,jpg,gif,ico,svg', 'max:1024'],
            ]);
            $filename = $request->file('favicon');
            $newfilename = 'favicon_'.time().'.'.$filename->getClientOriginalExtension();
            $request->file('favicon')->storeAs('image', $newfilename, 'public');
            $updateData['favicon'] = $newfilename;
        }

        // Handle Auth Background/Branding
        if ($request->hasFile('login')) {
            $request->validate([
                'login' => ['required', 'file', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            ]);
            $filename = $request->file('login');
            $newfilename = 'login_'.time().'.'.$filename->getClientOriginalExtension();
            $request->file('login')->storeAs('image', $newfilename, 'public');
            $updateData['login'] = $newfilename;
        }

        Site_setting::where('id', $request->id)->update($updateData);
        cache()->forget('site_setting');

        return back()->with('status', 'Platform identity matrix updated successfully');
    }

    public function store_video(Request $request)
    {
        $request->validate([
            'video' => [
                'required',
                'file',
                'mimes:mp4,mov,ogg,qt',
                'max:2048',
                function ($attribute, $value, $fail) {
                    // Real MIME from file content
                    $realMime = $value->getMimeType();
                    $allowedMimes = ['video/mp4', 'video/mov', 'video/ogg', 'video/quicktime'];

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

        $filename = $request->file('video');
        $newfilename = time().'.'.$filename->getClientOriginalExtension();

        $request->file('video')->storeAs('image', $newfilename, 'public');

        Site_setting::where('id', 1)->update([
            'video' => $newfilename,
        ]);

        return back()->with('status', 'video setting updated');
    }

    public function dashboard()
    {
        // Fetch only recent users for the summary feed (last 50)
        // Note: The 'View All' button links to a separate paginated user directory.
        $user = User::orderBy('id', 'Desc')->limit(50)->get();
        $admin = Admin::count();
        $line = DB::table('site_settings')->where('id', 1)->first();

        // REMOVED: $data = Order::orderBy('id', 'DESC')->get();
        // This query was fetching the entire orders table but was not used in the dashboard view,
        // causing significant performance overhead on large datasets.

        $balance = Balance::where('symbol', 'USD')->sum('amount');
        $default_package = Package::where('is_default', 1)->first();

        if (! $default_package) {
            $default_package = (object) ['plan' => 'N/A'];
        }

        return view('admin.dashboard', [
            'data' => $user,
            'line' => $line,
            'admin' => $admin,
            'balance' => $balance,
            'default_package' => $default_package,
        ]);
    }

    public function loginUsernow($id)
    {
        $user = User::find($id);

        $user->otp = 1;
        $user->is_demo = 0;
        $user->save();

        auth()->guard('web')->login($user);
        session(['2fa_verified' => true]);

        // Check if the user is authenticated
        if (auth()->guard('web')->check()) {
            return redirect()->route('home')->with('status', 'User logged in successfully.');
        } else {
            return 'Failed to log in user';
        }
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

        } elseif ($request->action == 'Reject') {
            if (isset($data->trx_id)) {
                DB::table('proof')->where('trx_id', $data->trx_id)->update(['status' => 'failed']);
            }
            Deposit::where('id', $data->id)->update(['status' => 'failed']);
        } elseif ($request->action == 'Delete') {
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

    // public function updatewithdrawal(Request $request){
    //     Withdrawal::where(['id'=>$request->id,'user_id'=>$request->user_id])->update([
    //         'status'=> 'confirmed',
    //         'hash'=>$request->hash
    //      ]);

    //     return back()->with('status','withdrawal state updated');
    // }

    public function updatewithdrawal_bank($id)
    {

        DB::table('transfer_payment')->where(['id' => $id])->update([
            'status' => 'confirmed',
        ]);

        return back()->with('status', 'withdrawal state updated');
    }

    public function bots()
    {
        $data = Bot::orderBy('id', 'desc')->get();

        return view('admin.bot', [
            'data' => $data,
        ]);
    }

    public function addBot(Request $request)
    {
        $this->validate($request, [
            'image' => 'required|max:2000',
        ]);
        $filename = $request->file('image');
        $newfilename = time().'.'.$filename->getClientOriginalExtension();

        $request->file('image')->storeAs('image', $newfilename, 'public');

        DB::table('bots')->insert([
            'name' => $request->name,
            'image' => $newfilename,
            'amount' => $request->amount,
            'buffer_percent' => $request->buffer_percent ?? 20.00,
            'per_withdrawal_percent' => $request->per_withdrawal_percent ?? 5.00,
            'min' => $request->min,
            'max' => $request->max,
            'day' => $request->daily,
            'total' => 0,
            'win' => $request->win,
            'loss' => $request->loss,
            'used' => rand(10000, 50000),
        ]);

        return back()->with('status', 'bot updated');
    }

    public function single_bots(Bot $bot) // single bot
    {return view('admin.single_bot', [
            'data' => $bot,
        ]);
    }

    public function edit_bot(Request $request)  // edit bot
    {$site = Bot::where('id', $request->id)->first();

        if ($request->hasFile('image')) {
            $this->validate($request, [
                'image' => 'required|mimes:png|max:2000',
            ]);
            $filename = $request->file('image');
            $newfilename = time().'.'.$filename->getClientOriginalExtension();

            $request->file('image')->storeAs('image', $newfilename, 'public');

            Bot::where('id', $request->id)->update([
                'name' => $request->name,
                'image' => $newfilename,
                'amount' => $request->amount,
                'buffer_percent' => $request->buffer_percent ?? 20.00,
                'per_withdrawal_percent' => $request->per_withdrawal_percent ?? 5.00,
                'min' => $request->min,
                'max' => $request->max,
                'day' => $request->daily,
                'total' => 0,
                'win' => $request->win,
                'loss' => $request->loss,
            ]);
        } else {
            Bot::where('id', $request->id)->update([
                'name' => $request->name,
                'image' => $site->image,
                'amount' => $request->amount,
                'buffer_percent' => $request->buffer_percent ?? 20.00,
                'per_withdrawal_percent' => $request->per_withdrawal_percent ?? 5.00,
                'min' => $request->min,
                'max' => $request->max,
                'day' => $request->daily,
                // 'total'=>$request->total,
                'win' => $request->win,
                'loss' => $request->loss,
            ]);
        }

        return redirect()->route('bots')->with('status', 'Bot updated');
    }

    public function userBots($id)  // user bot trades
    {$user = User::whereId($id)->first();

        return view('admin.user_bot', [
            'data' => DB::table('bot_generated_result')->whereUserId($user->id)->orderBy('id', 'desc')->get(),
            'user' => $user,
        ]);
    }

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

        $divided_amount = ($percentage / 100) * $amount;
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

            // Optionally update user balance here if you want historical edits to affect balance
            // For now, we just update the log itself to fix display values

            return back()->with('success', 'User signal updated successfully.');
        }

        return back()->with('error', 'Signal not found.');
    }

    public function deposit_message(Request $request) // depposit message
    {$data = DB::table('deposit_message')->where('user_id', $request->user_id)->get();

        if (count($data) < 1) {
            DB::table('deposit_message')->insert([
                'user_id' => $request->user_id,
                'message' => $request->message,
            ]);
        } else {
            DB::table('deposit_message')->where('user_id', $request->user_id)->update([
                'message' => $request->message,
            ]);
        }

        return back()->with('status', 'deposit message updated');
    }

    public function withdrawal_message(Request $request) // withdrawal message
    {$data = DB::table('withdrawal_message')->where('user_id', $request->user_id)->get();

        if (count($data) < 1) {
            DB::table('withdrawal_message')->insert([
                'user_id' => $request->user_id,
                'message' => $request->message,
            ]);
        } else {
            DB::table('withdrawal_message')->where('user_id', $request->user_id)->update([
                'message' => $request->message,
            ]);
        }

        return back()->with('status', 'withdrawal message updated');
    }

    public function delete_withdrawal_message($id) // / delete withdrawal message
    {DB::table('withdrawal_message')->where('id', $id)->delete();

        return back()->with('status', 'withdrawal message deleted');

    }

    public function delete_deposit_message($id) // delete deposit message
    {DB::table('deposit_message')->where('id', $id)->delete();

        return back()->with('status', 'deposit message deleted');
    }

    public function send_message(Request $request) // send message
    {$email = Email::create([
           'user_id' => $request->user_id,
           'to' => 'Admin',
           'sent_to' => 'inbox',
           'subject' => $request->subject,
           'message' => $request->message,
           'status' => 'unread',
           'thread_id' => $request->thread_id,
           'reply_to_id' => $request->reply_to_id,
       ]);

        if (empty($request->thread_id)) {
            $email->thread_id = $email->id;
            $email->save();
        }

        // Notify the user so they see the message in their notification center
        Noti::create([
            'user_id' => $request->user_id,
            'message' => 'You have a new message from Admin: "'.$request->subject.'". Check your Inbox.',
            'status' => 'unread',
        ]);

        return back()->with('status', 'Message Sent');
    }

    public function send_notification(Request $request) // send message
    {Noti::create([
            'user_id' => $request->user_id,
            'message' => $request->message,
            'status' => 'unread',
        ]);

        return back()->with('status', 'Message Sent');
    }

    public function single_message($id, $user_id) // getsingle message
    {$user = User::whereId($user_id)->first();
        $email = Email::whereId($id)->first();

        $thread = Email::where('thread_id', $email->thread_id ?? $email->id)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.user_message', [
            'user' => $user,
            'email' => $email,
            'thread' => $thread,
        ]);
    }

    public function update_wecome_message(Request $request) // update welome message
    {DB::table('messages')->where('id', 1)->update([
            'message' => $request->message,
        ]);

        return back()->with('status', 'welcome message updated');
    }

    public function google_login() // google login
    {return view('admin.google_login');
    }

    public function verify2faAdmin(Request $request)
    {

        $user = 'WITLFYNTCIPJAKH2';

        $google2fa = app('pragmarx.google2fa');

        $valid = $google2fa->verifyKey($user, $request->code);

        if ($valid) {
            Admin::where('id', auth()->guard('admin')->user()->id)->update([
                'data' => 1,
            ]);

            return redirect('/admin');
        }

        return back()->with('error', 'Code Incorrect  please try again.');
    }

    public function adminLogin(Request $request)  // admin login
    {$this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        $email = trim($request->email);
        $password = trim($request->password);

        if (auth()->guard('admin')->attempt(['email' => $email, 'password' => $password])) {
            $admin = auth()->guard('admin')->user();

            // Bypass 2FA for all admins as requested
            Admin::where('id', $admin->id)->update(['data' => 1]);
            return redirect('admin/dashboard');
        }

        return back()->with('not', 'The provided credentials do not match our records');
    }

    // --- Admin History CRUD ---

    public function edit_trade($id)
    {
        $trade = Order::findOrFail($id);

        return view('admin.history.trade_edit', compact('trade'));
    }

    public function update_trade(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:win,loss,draw,pending',
            'amount' => 'required|numeric|min:0',
            'created_at' => 'required|date',
        ]);

        $trade = Order::findOrFail($id);
        $oldStatus = $trade->status;
        $oldAmount = $trade->amount;

        $trade->status = $request->status;
        $trade->amount = $request->amount;
        $trade->symbol = $request->symbol ?? $trade->symbol;
        $trade->strike_rate = $request->strike_rate ?? $trade->strike_rate;
        $trade->created_at = $request->created_at;

        if ($oldStatus !== $trade->status || $oldAmount != $trade->amount) {
            HistoryBalanceService::recalculateTrade($trade, $oldStatus, $trade->status, $oldAmount, $trade->amount);
        }

        $trade->save();

        return redirect()->back()->with('success', 'Trade updated and balance recalculated successfully.');
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

    public function approveTrade($id)
    {
        $order = Order::findOrFail($id);
        $order->approval_status = 'approved';
        $order->status = 'pending'; // Now it enters the market
        $order->traded_date = \Carbon\Carbon::now(); // Start the countdown now
        $order->expire_date = \Carbon\Carbon::now()->addMinutes($order->time);
        
        // Update strike rate to current market price if possible
        try {
            $data = \App\Services\BinancePriceService::fetchAll();
            $priceMap = $data['priceMap'];
            if (isset($priceMap[$order->symbol])) {
                $order->strike_rate = $priceMap[$order->symbol];
            }
        } catch (\Exception $e) {}
        
        $order->save();
        return redirect()->back()->with('success', 'Trade approved and entered market successfully.');
    }

    public function rejectTrade($id)
    {
        $order = Order::findOrFail($id);
        $order->approval_status = 'rejected';
        $order->status = 'closed';
        $order->save();

        // Refund amount
        $balanceColumn = $order->is_demo ? 'demo' : 'amount';
        \Illuminate\Support\Facades\DB::table('balances')
            ->where('user_id', $order->user_id)
            ->where('symbol', 'usd')
            ->increment($balanceColumn, $order->amount);

        return redirect()->back()->with('success', 'Trade rejected and amount refunded.');
    }
}
