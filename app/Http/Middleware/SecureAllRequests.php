<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SecureAllRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Block non-authenticated or unwanted patterns if necessary
        if (! auth()->check()) {
            abort(403, 'Unauthorized access');
        }

        return $next($request);
    }
}
