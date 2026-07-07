<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Google
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Only check admin guard if it's an admin route
        if ($request->is('admin*') || $request->is('admin')) {
            if (auth('admin')->check()) {
                $user = auth('admin')->user();

                // Bypass if exempt
                if ($user->is_exempt_from_2fa) {
                    return $next($request);
                }

                // If 2FA is enabled but not verified in session
                // Check 'data' as a persistent bypass for this implementation
                if ($user->google_aut && ! session('2fa_verified') && $user->data != 1) {
                    // If not already on the 2FA page or posting to it, redirect
                    $allowedPaths = ['admin/2fa', 'admin/sfa/google'];
                    $isAllowed = false;
                    foreach ($allowedPaths as $path) {
                        if ($request->is($path) || $request->is($path.'/*')) {
                            $isAllowed = true;
                            break;
                        }
                    }

                    if (! $isAllowed && ! $request->routeIs('google_loginslogin') && ! $request->routeIs('google_login_admin')) {
                        return redirect()->route('google_login_admin');
                    }
                }
            }
        } else {
            // Only check web guard if it's NOT an admin route
            if (auth('web')->check()) {
                $user = auth('web')->user();

                // Bypass if exempt
                if ($user->is_2fa_exempt) {
                    return $next($request);
                }

                // IMPORTANT: Check is_2fa_enabled, NOT just google_aut
                if ($user->is_2fa_enabled && ! session('2fa_verified')) {
                    // If not already on the 2FA page or posting to it, redirect
                    $allowedPaths = ['2fa/verify', 'google/2fa'];
                    $isAllowed = false;
                    foreach ($allowedPaths as $path) {
                        if ($request->is($path) || $request->is($path.'/*')) {
                            $isAllowed = true;
                            break;
                        }
                    }

                    if (! $isAllowed &&
                        ! $request->routeIs('verify2fa') &&
                        ! $request->routeIs('authfa') &&
                        ! $request->routeIs('google') &&
                        ! $request->routeIs('google2fa')) {

                        // If it's a login-time verification, prefer the 'google' route
                        if (session('2fa_pending')) {
                            return redirect()->route('google');
                        }

                        return redirect()->route('authfa');
                    }
                }
            }
        }

        return $next($request);
    }
}
