<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth('admin')->check() && !auth('admin')->user()->is_super_admin) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Unauthorized. Super Admin access required.'], 403);
            }

            return abort(403, 'Unauthorized. Super Admin access required.');
        }

        return $next($request);
    }
}
