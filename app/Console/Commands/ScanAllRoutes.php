<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\User;

class ScanAllRoutes extends Command
{
    protected $signature = 'scan:all';
    protected $description = 'Scan all GET routes without parameters';

    public function handle()
    {
        $admin = Admin::first();
        $user = User::first();
        
        if (!$admin || !$user) {
            $this->error('Need admin and user to test.');
            return;
        }

        $routes = Route::getRoutes()->getRoutesByMethod()['GET'];

        foreach ($routes as $route) {
            $uri = $route->uri();
            
            if (strpos($uri, '{') !== false || strpos($uri, 'logout') !== false || strpos($uri, 'delete') !== false || strpos($uri, 'biscolab') !== false) {
                continue;
            }

            $middlewares = $route->gatherMiddleware();
            $isAdmin = false;
            $isUser = false;
            foreach($middlewares as $m) {
                if (is_string($m)) {
                    if (strpos($m, 'auth:admin') !== false) $isAdmin = true;
                    if (strpos($m, 'auth:web') !== false || $m === 'auth') $isUser = true;
                }
            }
            if (strpos($uri, 'admin') === 0 && !$isUser) $isAdmin = true;
            if (strpos($uri, 'dashboard') === 0 && !$isAdmin) $isUser = true;

            Auth::guard('admin')->logout();
            Auth::guard('web')->logout();

            if ($isAdmin) {
                Auth::guard('admin')->login($admin);
            } elseif ($isUser) {
                Auth::guard('web')->login($user);
            }

            $request = Request::create('/' . ltrim($uri, '/'), 'GET');
            
            try {
                $response = app()->handle($request);
                $status = $response->getStatusCode();
                
                if ($status == 500) {
                    $this->error("FAIL (500) - $uri");
                    if (isset($response->exception) && $response->exception) {
                        $this->line("    " . $response->exception->getMessage());
                    }
                }
            } catch (\Throwable $t) {
                $this->error("FAIL (500 EXCEPTION) - $uri");
                $this->line("    " . $t->getMessage() . ' in ' . $t->getFile() . ':' . $t->getLine());
            }
        }
        $this->info("Scan complete.");
    }
}
