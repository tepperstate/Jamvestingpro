<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Is_verified
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->guard('web')->user()->email_verified !== 1) {
            return redirect()->route('register')->with('status', 'Your registration was successful, A verification link has been sent your email');
        }

        return $next($request);
    }
}
