<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Otp
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            if (auth()->guard('web')->user()->is_exempt_from_2fa) {
                return $next($request);
            }
            if (auth()->guard('web')->user()->otp == 0) {
                return redirect()->route('otp');
            }
        }

        return $next($request);
    }
}
