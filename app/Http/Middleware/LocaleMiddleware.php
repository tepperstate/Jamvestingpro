<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LocaleMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $locale = session('locale', config('app.locale', 'en'));

        if (in_array($locale, ['en', 'es', 'fr', 'zh', 'ar'])) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
