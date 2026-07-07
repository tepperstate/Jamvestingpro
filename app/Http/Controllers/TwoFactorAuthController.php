<?php

namespace App\Http\Controllers;

use App\Mail\Welcome;
use App\Models\Balance;
use App\Models\User;
use App\Notifications\DepositNotification;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Laravel\Socialite\Facades\Socialite;

class TwoFactorAuthController extends Controller
{
    public function generate2faSecret(Request $request)
    {
        $google2fa = app('pragmarx.google2fa');

        $secret = $google2fa->generateSecretKey();
        User::where('id', auth()->user()->id)->update([
            'google_aut' => $secret,
        ]);

        $google2faUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            auth()->user()->email,
            $secret
        );

        $writer = new Writer(
            new ImageRenderer(
                new RendererStyle(400),
                new ImagickImageBackEnd
            )
        );

        $QR_Image = base64_encode($writer->writeString($google2faUrl));

        return view('2fa.generate', ['QR_Image' => $QR_Image, 'secret' => $secret]);
    }

    public function redirectToGoogle()
    {
        $googleUser = Socialite::driver('google')->with(['redirect_uri' => env('GOOGLE_REDIRECT_URI')])->redirect();

        return $googleUser;
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->with(['redirect_uri' => env('GOOGLE_REDIRECT_URI')])->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Google authentication failed or expired. Please try again.');
        }
        $user = User::where('email', $googleUser->email)->first();

        if (! $user) {
            // Split name into first and last name
            $nameParts = explode(' ', $googleUser->name, 2);
            $firstName = $nameParts[0] ?? $googleUser->name;
            $lastName = $nameParts[1] ?? '';

            $user = User::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'type' => 'Individual account',
                'email' => $googleUser->email,
                'phone' => 'null',
                'country' => 'null',
                'password' => 'null',
                'currency' => 'USD',
                'email_verified' => '1',
                'user_id' => substr(sha1(uniqid(rand(), true)), 0, 10),
                'package_plan' => DB::table('default_package')->where('id', 1)->first()->plan,
                'package_id' => 1,
                'withdrawal' => 'on',
            ]);

            $text = [
                'greeting' => $googleUser->name,
                'subject' => 'Your Registration was successful',
                'body' => 'Your registration on '.env('APP_NAME').' was successful, click on the button below to verify your account',
                'data' => 'Click Here',
                'url' => url('/verify', ['data' => $googleUser->email]),
                'thanks' => 'Thank you for choosing '.env('APP_NAME'),
            ];

            try {
                Notification::route('mail', $googleUser->email)->notify(new DepositNotification($text));
                Mail::to($googleUser->email)->send(new Welcome);
                Log::info('message sent');
            } catch (\Throwable $th) {
                Log::info($th);
            }

            Balance::create([
                'user_id' => $user->id,
                'demo' => 10000,
                'amount' => 0,
                'bitcoin' => 0,
                'referral' => 0,
                'name' => 'Dollar',
                'symbol' => 'USD',
                'image' => null,
                'bonus' => 0,
            ]);

            Balance::create([
                'user_id' => $user->id,
                'demo' => 0,
                'amount' => 0,
                'bitcoin' => 0,
                'referral' => 0,
                'name' => 'Bitcoin',
                'symbol' => 'BTC',
                'image' => null,
                'bonus' => 0,
            ]);

            Balance::create([
                'user_id' => $user->id,
                'demo' => 0,
                'amount' => 0,
                'bitcoin' => 0,
                'referral' => 0,
                'name' => 'Stellar',
                'symbol' => 'XLM',
                'image' => null,
                'bonus' => 0,
            ]);

            Balance::create([
                'user_id' => $user->id,
                'demo' => 0,
                'amount' => 0,
                'bitcoin' => 0,
                'referral' => 0,
                'name' => 'Dogecoin',
                'symbol' => 'Doge',
                'image' => null,
                'bonus' => 0,
            ]);

            Balance::create([
                'user_id' => $user->id,
                'demo' => 0,
                'amount' => 0,
                'bitcoin' => 0,
                'referral' => 0,
                'name' => 'Ethereum',
                'symbol' => 'ETH',
                'image' => null,
                'bonus' => 0,
            ]);

            Balance::create([
                'user_id' => $user->id,
                'demo' => 0,
                'amount' => 0,
                'bitcoin' => 0,
                'referral' => 0,
                'name' => 'Solana',
                'symbol' => 'SOL',
                'image' => null,
                'bonus' => 0,
            ]);

            Balance::create([
                'user_id' => $user->id,
                'demo' => 0,
                'amount' => 0,
                'bitcoin' => 0,
                'referral' => 0,
                'name' => 'Litecoin',
                'symbol' => 'LTC',
                'image' => null,
                'bonus' => 0,
            ]);

            Balance::create([
                'user_id' => $user->id,
                'demo' => 0,
                'amount' => 0,
                'bitcoin' => 0,
                'referral' => 0,
                'name' => 'Cardano',
                'symbol' => 'ADA',
                'image' => null,
                'bonus' => 0,
            ]);

            Balance::create([
                'user_id' => $user->id,
                'demo' => 0,
                'amount' => 0,
                'bitcoin' => 0,
                'referral' => 0,
                'name' => 'Chainlink',
                'symbol' => 'LINK',
                'image' => null,
                'bonus' => 0,
            ]);

        }

        Auth::login($user);

        return redirect()->route('home');
    }
}
