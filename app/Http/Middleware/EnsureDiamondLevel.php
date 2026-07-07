<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDiamondLevel
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // For demonstration, assume 'tier' or 'level' is stored in users table.
        // E.g. $request->user()->tier == 'Diamond'
        // Mocking behavior:
        if ($request->user() && $request->user()->plan_id != 4) { // Assuming 4 = Diamond
            return redirect()->route('user.upgrade')->with('error', 'Upgrade to Diamond to access HIP Pro Institutional Plans.');
        }

        return $next($request);
    }
}
