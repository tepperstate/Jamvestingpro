<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\DepositNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function store(Request $request)
    {
        info('Login request for: '.$request->email);
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
        ]);

        $attempt = auth()->guard('web')->attempt(['email' => $request->email, 'password' => $request->password]);
        info('Attempt result: '.($attempt ? 'Success' : 'Fail'));
        if ($attempt) {
            $request->session()->regenerate();

            $code = random_int(1111, 9999);

            DB::table('email')->insert([
                'email' => $request->email,
                'code' => $code,
            ]);

            $user_id = auth()->user();
            $user_id->update(['is_demo' => 0]);

            if ($user_id->otp_enabled == 0) {
                User::where('id', auth()->user()->id)->update([
                    'otp' => 1,
                ]);
            } else {
                User::where('id', auth()->user()->id)->update([
                    'otp' => 0,
                ]);
            }

            $text = [
                'greeting' => 'Hello '.auth()->guard('web')->user()->first_name,
                'subject' => 'Otp Code',
                'body' => 'Your verification otp is '.$code,
                'data' => null,
                'url' => null,
                'thanks' => 'Thank you for choosing '.env('APP_NAME'),
            ];

            try {
                // Only send email OTP if: otp is enabled AND Google 2FA is NOT active
                if (auth()->user()->otp_enabled == 1 && ! auth()->user()->is_2fa_enabled) {
                    Notification::route('mail', auth()->guard('web')->user()->email)->notify(new DepositNotification($text));
                }
            } catch (\Throwable $th) {
                info($th);
            }

            if ($user_id->is_2fa_exempt) {
                User::where('id', $user_id->id)->update(['otp' => 1]);

                return response()->json(['status' => 'login']);
            }

            if ($user_id->otp_enabled == 0) {
                if (auth()->user()->is_2fa_enabled == true) {
                    session(['2fa_pending' => true]);

                    return response()->json(['status' => 'google']);
                } else {
                    return response()->json(['status' => 'login']);
                }
            } else {
                // If Google 2FA is enabled, redirect to Google 2FA regardless of otp_enabled flag
                if (auth()->user()->is_2fa_enabled == true) {
                    session(['2fa_pending' => true]);

                    return response()->json(['status' => 'google']);
                }

                return response()->json(['status' => 'otp']);
            }
        }

        return response()->json(['error' => 'The provided credentials do not match our records']);
    }

    public function resend_otp()  // resend otp
    {$code = random_int(1111, 9999);

        DB::table('email')->insert([
            'email' => auth()->guard('web')->user()->email,
            'code' => $code,
        ]);

        User::where('id', auth()->user()->id)->update([
            'otp' => 0,
        ]);

        try {

            $text = [
                'greeting' => 'Hello '.auth()->guard('web')->user()->first_name,
                'subject' => 'Otp Code',
                'body' => 'Your verification otp is '.$code,
                'data' => null,
                'url' => null,
                'thanks' => 'Thank you for choosing '.env('APP_NAME'),
            ];
            Notification::route('mail', auth()->guard('web')->user()->email)
                ->notify(new DepositNotification($text));
        } catch (\Throwable $th) {
            // throw $th;
            info($th);
        }

        return response()->json(['status' => 'Your otp has been resent']);

    }

    public function validateOtp(Request $request) // otp validation
    {$this->validate($request, [
            'code' => 'required',
        ]);

        $check = DB::table('email')->where('code', $request->code)->exists();

        if ($check) {
            $code_data = DB::table('email')->where('code', $request->code)->first();

            if ($request->code === $code_data->code) {
                DB::table('email')->where('code', $request->code)->delete();

                User::where('id', auth()->user()->id)->update([
                    'otp' => 1,
                ]);

                $email = DB::table('admin_email')->where('id', 1)->first();

                try {
                    $text = [
                        'greeting' => 'Hello Admin',
                        'subject' => 'User Notification',
                        'body' => 'User '.auth()->guard('web')->user()->first_name.' has successfully accessed the platform.',
                        'data' => null,
                        'url' => null,
                        'thanks' => 'Thank you for choosing '.env('APP_NAME'),
                    ];
                    Notification::route('mail', $email->email)
                        ->notify(new DepositNotification($text));
                } catch (\Throwable $th) {
                    // throw $th;
                }
                if (auth()->user()->is_2fa_enabled == true) {
                    // 2FA disabled - return success directly
                    return response()->json(['status' => 'true']);
                } else {
                    return response()->json(['status' => 'true']);
                }

            } else {
                return response()->json(['error' => 'code does not match']);
            }
        } else {
            return response()->json(['error' => 'code does not exist']);
        }
    }

    public function google2fa(Request $request)
    {
        $this->validate($request, [
            'code' => 'required|numeric',
        ]);

        $user = auth()->user();
        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $google2fa = app('pragmarx.google2fa');
        $valid = $google2fa->verifyKey($user->google_aut, $request->code);

        if ($valid) {
            $user->update(['otp' => 1]);
            session(['2fa_verified' => true]);
            session()->forget('2fa_pending');
            session()->save();

            return response()->json(['status' => 'success']);
        }

        return response()->json(['error' => 'Invalid authentication code']);
    }

    public function otp()
    {
        return view('otp');
    }

    public function google()
    {
        return view('google');
    }

    public function reset()
    {
        return view('auth.passwords.reset');
    }

    public function passwordReset(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|exists:users,email',
        ]);

        $token = Str::random(67);

        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        $action = route('reset.token', ['token' => $token, 'email' => $request->email]);
        $body = 'We received a request to reset your password for account associate with '.$request->email.
        ' You can reset your password by clicking the click below';

        Mail::send('auth.passwords.email_fogot', ['action' => $action, 'body' => $body], function ($message) use ($request) {
            $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            $message->to($request->email, 'account')->subject('Reset Your Password');
        });

        return back()->with('success', 'We have e-mailed your password rest link');
    }

    public function showPasswordResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|exists:users,email',
            'password' => 'required',
        ]);

        $check_token = DB::table('password_resets')->where([
            'email' => $request->email,
            'token' => $request->token,
        ])->first();

        if (! $check_token) {
            return back()->with('fail', 'Invalid Token or email address');
        } else {
            User::where('email', $request->email)->update([
                'password' => Hash::make($request->password),
            ]);

            DB::table('password_resets')->where([
                'email' => $request->email,
            ])->delete();

            return redirect()->route('login')->with('info', 'Your password has been change your can now login with your password');
        }
    }

    public function verify($email)
    {
        $email = User::whereEmail($email)->update([
            'status' => 1,
        ]);

        return view('verify');
    }

    public function logout()
    {

        // User::whereId(auth()->guard('web')->user()->id)->update([
        //     'login'=>Carbon::now()
        // ]);
        auth()->guard('web')->logout();

        return redirect()->route('login');
    }
}
