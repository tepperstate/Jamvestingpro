<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PlanFeatureMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  string  $feature
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $feature)
    {
        $user = auth()->user();

        if (! $user || ! $user->hasFeature($feature)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Your current plan does not support this feature. Please upgrade to unlock.'], 403);
            }

            return redirect()->route('user.upgrade')->with('error', 'Your current plan does not support this feature. Please upgrade to unlock.');
        }

        return $next($request);
    }
}
