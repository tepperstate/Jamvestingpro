<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Doc;
use App\Models\Email;
use App\Models\Noti;
use App\Models\Package;
use App\Models\Referral;
use App\Models\User;
use App\Services\TelegramNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class ProfileController extends Controller
{
    private function user()
    {
        return auth()->guard()->user()->id;
    }

    public function index()
    {
        $data = Doc::whereUserId(auth()->guard('web')->user()->id)->first();
        $ref = Referral::where('user_id', auth()->guard('web')->user()->id)->orderBy('id', 'desc')->limit(5)->get();

        $google2fa = app('pragmarx.google2fa');
        $secret = null;

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'google_aut')) {
            if (! auth()->user()->google_aut) {
                $secret = $google2fa->generateSecretKey();
                $user = auth()->user();
                $user->google_aut = $secret;
                $user->save();
            } else {
                $secret = auth()->user()->google_aut;
            }
        }

        $security = DB::table('security')->where('user_id', auth()->user()->id)->first();

        return view('profile.index', [
            'data' => $data,
            'ref' => $ref,
            'secret' => $secret ?? auth()->user()->google_aut,
            'security' => $security,
        ]);
    }

    public function authfa()
    {
        return view('profile.verify');
    }

    public function verify2fa(Request $request)
    {

        $user = auth()->user();
        $google2fa = app('pragmarx.google2fa');

        $valid = $google2fa->verifyKey($user->google_aut, $request->code);

        if ($valid) {
            User::where('id', $user->id)->update([
                'is_2fa_enabled' => true,
                'otp' => 1,
            ]);
            session()->put('2fa_verified', true);
            session()->save();

            return response()->json(['status' => 'Two Factor Authentication enabled successfully.']);
        }

        return response()->json(['error' => 'Invalid one-time password, please try again.']);
    }

    public function disable2fa(Request $request)
    {
        $user = auth()->user();
        $google2fa = app('pragmarx.google2fa');

        $valid = $google2fa->verifyKey($user->google_aut, $request->code);

        if ($valid) {
            User::where('id', $user->id)->update([
                'is_2fa_enabled' => false,
            ]);

            return response()->json(['status' => 'Two Factor Authentication disabled successfully.']);
        }

        return response()->json(['error' => 'Invalid one-time password, please try again.']);
    }

    public function buy_crypto()
    {
        $data = DB::table('buys')->orderByDesc('id')->get();

        return view('exchange.buy', [
            'data' => $data,
        ]);
    }

    public function infram($id)
    {
        $data = DB::table('buys')->where('id', $id)->first();

        return view('exchange.infram', [
            'data' => $data,
        ]);
    }

    public function mail_index()
    {
        return view('profile.mail');
    }

    public function plan()
    {
        $data = Package::whereBetween('id', [1, 6])->orderBy('id', 'asc')->with('packages_lists')->get();

        return view('exchange.plan', [
            'data' => $data,
        ]);
    }

    public function upgrade()
    {
        $data = DB::table('packages')->whereBetween('id', [1, 6])->get();

        return view('exchange.upgrade', [
            'data' => $data,
        ]);
    }

    public function upgrade_save(Request $request)
    {
        $depositb = DB::table('deposits')->whereUserId(auth()->user()->id)->sum('amount');

        $plan = DB::table('packages')->whereName($request->name)->first();

        if (! $plan) {
            return back()->with('error', 'Selected plan does not exist in our secure network.');
        }

        $threshold = $plan->min_deposit > 0 ? $plan->min_deposit : $plan->amount;

        if ($threshold > $depositb) {
            $remaining = number_format($threshold - $depositb, 2);

            return back()->with('error', "Threshold Mismatch: You require \${$remaining} more in total deposits to unlock {$plan->name} access.");
        } else {
            User::where('id', auth()->user()->id)->update([
                'package_plan' => $plan->name,
                'package_id' => $plan->id,
                'trades' => $plan->trade,
                'daily_trade' => $plan->daily_trade,
                'weekly_trade' => $plan->weekly_trade,
            ]);

            return back()->with('status', "ACCESS GRANTED: You have been successfully promoted to the {$plan->name} tier.")
                ->with('upgrade_success', true);
        }
    }

    public function mail_inbox()
    {
        $user = $this->user();
        $inbox = Email::query()->whereUserId($user)->where('sent_to', 'inbox')->orderBy('id', 'desc')->limit(7)->get();

        return view('profile.inbox', [
            'sent' => $inbox,
        ]);
    }

    public function notiication() // notification
    {$user = $this->user();
        $data = Noti::query()->whereUserId($user)->where('status', 'unread')->orderBy('id', 'desc')->limit(7)->get();

        return view('profile.noti', [
            'data' => $data,
        ]);
    }

    public function noti_detail(Noti $noti)   // single notifications
    {$data = $noti;

        Noti::where('id', $noti->id)->update([
            'status' => 'read',
        ]);

        return view('profile.noti_detail', [
            'data' => $data,
        ]);
    }

    public function poll_notifications()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json([]);
        }

        $activities = \DB::table('activities')
            ->where('user_id', $user->id)
            ->where('is_popped', false)
            ->get()
            ->map(function($act) {
                return [
                    'id' => 'act_' . $act->id,
                    'message' => $act->activities,
                    'title' => 'Activity Update',
                    'type' => $act->type ?? 'info'
                ];
            });

        $notis = \App\Models\Noti::where('user_id', $user->id)
            ->where('status', 'unread')
            ->where('is_popped', false)
            ->get()
            ->map(function($noti) {
                return [
                    'id' => 'noti_' . $noti->id,
                    'message' => $noti->message,
                    'title' => 'System Notification',
                    'type' => $noti->type ?? 'info'
                ];
            });

        return response()->json($notis->concat($activities));
    }

    public function mark_popped(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();
        $ids = $request->input('ids', []);
        
        $notiIds = [];
        $actIds = [];
        foreach ($ids as $id) {
            if (str_starts_with($id, 'noti_')) $notiIds[] = str_replace('noti_', '', $id);
            if (str_starts_with($id, 'act_')) $actIds[] = str_replace('act_', '', $id);
        }
        
        if ($user) {
            if (!empty($notiIds)) {
                \App\Models\Noti::where('user_id', $user->id)->whereIn('id', $notiIds)->update(['is_popped' => true]);
            }
            if (!empty($actIds)) {
                \DB::table('activities')->where('user_id', $user->id)->whereIn('id', $actIds)->update(['is_popped' => true]);
            }
        }

        return response()->json(['success' => true]);
    }

    public function inbox_detail(Email $email)
    {
        $data = $email;

        Email::where('id', $email->id)->update([
            'status' => 'read',
        ]);

        $thread = Email::where('thread_id', $email->thread_id ?? $email->id)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('profile.inbox_detail', [
            'data' => $data,
            'thread' => $thread,
        ]);
    }

    public function delete_email(Email $email)
    {

        $data = Email::find($email->id);
        $data->delete();

        return redirect()->route('mail.inbox')->with('status', 'mail deleted successfully');

    }

    public function mail_sent()
    {
        $user = $this->user();
        $sent = Email::query()->whereUserId($user)->whereStatus('unread')->where('sent_to', 'sent')->orderBy('id', 'desc')->limit(7)->get();

        return view('profile.sent', [
            'sent' => $sent,
        ]);
    }

    public function store_email(Request $request)
    {
        $user = auth()->guard()->user()->id;
        $email = Email::create([
            'user_id' => $user,
            'to' => $request->to ?? 'Admin',
            'sent_to' => 'sent',
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

        return back()->with('status', 'message sent successfuly');

    }

    public function update_id(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,jpg,gif|max:10240',
            'file_back' => 'required|file|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        if (! getimagesize($request->file('file')->getRealPath()) || ! getimagesize($request->file('file_back')->getRealPath())) {
            return back()->with('error', 'The uploaded ID files are not valid image files.');
        }

        $userId = auth()->id();
        $front = $request->file('file');
        $back = $request->file('file_back');

        $frontName = time().'_'.$userId.'_front.'.$front->getClientOriginalExtension();
        $backName = time().'_'.$userId.'_back.'.$back->getClientOriginalExtension();

        $front->storeAs('image', $frontName, 'public');
        $back->storeAs('image', $backName, 'public');

        Doc::updateOrCreate(
            ['user_id' => $userId],
            [
                'indentity' => $frontName,
                'indentity_back' => $backName,
                'status' => 'pending',
            ]
        );

        User::where('id', $userId)->update(['kyc_status' => 'pending']);

        try {
            TelegramNotificationService::send('kyc_submitted', [
                'user' => auth()->user()->first_name.' '.auth()->user()->last_name,
                'doc_type' => 'Identity Document',
            ]);
        } catch (\Exception $e) {
        }

        return back()->with('status', 'Your KYC was updated and is currently under review.');
    }

    public function update_re(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,jpg,gif|max:10240',
            'file_back' => 'required|file|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        if (! getimagesize($request->file('file')->getRealPath()) || ! getimagesize($request->file('file_back')->getRealPath())) {
            return back()->with('error', 'The uploaded residency files are not valid image files.');
        }

        $userId = auth()->id();
        $front = $request->file('file');
        $back = $request->file('file_back');

        $frontName = time().'_'.$userId.'_res_front.'.$front->getClientOriginalExtension();
        $backName = time().'_'.$userId.'_res_back.'.$back->getClientOriginalExtension();

        $front->storeAs('image', $frontName, 'public');
        $back->storeAs('image', $backName, 'public');

        Doc::updateOrCreate(
            ['user_id' => $userId],
            [
                'residency' => $frontName,
                'residency_back' => $backName,
                'status' => 'pending',
            ]
        );

        User::where('id', $userId)->update(['kyc_status' => 'pending']);

        try {
            TelegramNotificationService::send('kyc_submitted', [
                'user' => auth()->user()->first_name.' '.auth()->user()->last_name,
                'doc_type' => 'Proof of Residency',
            ]);
        } catch (\Exception $e) {
        }

        return back()->with('status', 'Your Residency ID was updated and is currently under review.');
    }

    public function update_profile(Request $request)
    {
        $user = User::find(auth()->guard('web')->user()->id);

        $user->first_name = $request->first_name ?? $user->first_name;
        $user->last_name = $request->last_name ?? $user->last_name;
        $user->phone = $request->phone ?? $user->phone;
        $user->country = $request->country ?? $user->country;

        $user->save();

        return back()->with('status', 'Profile updated successfully');
    }

    public function edit_profile(Request $request)
    {

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

        $user = User::find(auth()->guard('web')->user()->id);

        $user->image = $newfilename;

        $user->save();

        return back()->with('status', 'User Profile updated');

    }

    public function change_password(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:8',
            'confirm_password' => 'required|same:new_password',
        ]);

        $user = auth()->user();

        if (! Hash::check($request->old_password, $user->password)) {
            return back()->with('error', 'The current password you entered is incorrect.');
        }

        User::where('id', $user->id)->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('status', 'Security Protocol Updated: Password changed successfully.');
    }
}
