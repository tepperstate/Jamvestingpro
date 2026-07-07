<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

class Scan500Fast extends Command
{
    protected $signature = 'scan:500';

    protected $description = 'Scan routes for 500 errors';

    public function handle()
    {
        $this->info('Scanning routes...');
        $routes = Route::getRoutes()->get('GET');

        $user = User::first();
        $admin = Admin::first();

        $errors = [];
        $scanned = 0;

        foreach ($routes as $route) {
            $uri = ltrim($route->uri(), '/');

            // Skip api routes, ignition, telescope, etc
            if (str_starts_with($uri, '_ignition') || str_starts_with($uri, 'api/') || str_starts_with($uri, 'sanctum/')) {
                continue;
            }

            // Provide dummy params for simple common parameters
            $uri = str_replace(['{id}', '{user}', '{admin}', '{page}', '{token}', '{plan}', '{crypto}'], ['1', '1', '1', '1', 'dummy-token', '1', 'BTC'], $uri);

            if (strpos($uri, '{') !== false) {
                continue;
            }

            $url = url($uri);
            $this->line('Testing: '.$url);

            try {
                $response = Http::timeout(2)->get($url);
                if ($response->status() >= 500) {
                    $errors[] = ['uri' => $uri, 'role' => 'guest'];
                    $this->error("500 on $uri (Guest)");
                }
            } catch (\Exception $e) {
                $this->error("Failed on $uri (Guest): ".$e->getMessage());
            }

            $scanned++;
        }

        $reportPath = storage_path('logs/500_scan_report.json');
        file_put_contents($reportPath, json_encode([
            'scanned_routes' => $scanned,
            'errors_found' => count($errors),
            'errors' => $errors,
        ], JSON_PRETTY_PRINT));

        $this->info('Done! Found '.count($errors).' errors.');
    }
}
