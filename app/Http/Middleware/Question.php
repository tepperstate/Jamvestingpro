<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Question
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            if (auth()->user()->question != 'off') {
                return redirect()->route('question');
            }
        }

        return $next($request);
    }
}
