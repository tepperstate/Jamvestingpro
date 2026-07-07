<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Route;

class PageScannerTest extends TestCase
{
    /**
     * Test all GET routes.
     */
    public function test_scan_all_get_routes(): void
    {
        $errors = [];

        $getRoutes = collect(Route::getRoutes())->filter(function ($route) {
            return in_array('GET', $route->methods()) && 
                   !str_starts_with($route->uri(), '_ignition') &&
                   !str_starts_with($route->uri(), 'api/') &&
                   !str_starts_with($route->uri(), 'sanctum/') &&
                   !str_starts_with($route->uri(), 'cron/') &&
                   !str_starts_with($route->uri(), 'system/') &&
                   $route->uri() !== 'dashboard/connect' &&
                   !preg_match('/\{[a-zA-Z0-9_]+\}/', $route->uri());
        })->map->uri();

        // Test each route
        foreach ($getRoutes as $uri) {
            file_put_contents(base_path('scan_progress.log'), "Testing: $uri\n", FILE_APPEND);
            // Skip logout or destructive routes
            if (str_contains($uri, 'logout') || str_contains($uri, 'delete')) {
                continue;
            }

            try {
                // Test as Guest
                if (!str_starts_with($uri, 'admin') && !str_starts_with($uri, 'dashboard')) {
                    $response = $this->get($uri);
                    if ($response->getStatusCode() >= 500) {
                        $msg = "[Guest] GET /{$uri} returned " . $response->getStatusCode();
                        if (isset($response->exception)) {
                            $msg .= " -> " . $response->exception->getMessage();
                        }
                        $errors[] = $msg;
                    }
                }

                // Test as User for dashboard/ routes
                if (str_starts_with($uri, 'dashboard')) {
                    // Re-fetch user to avoid detached models
                    $user = \App\Models\User::first();
                    if ($user) {
                        $response = $this->actingAs($user, 'web')->get($uri);
                        if ($response->getStatusCode() >= 500) {
                            $msg = "[User] GET /{$uri} returned " . $response->getStatusCode();
                            if (isset($response->exception)) {
                                $msg .= " -> " . $response->exception->getMessage();
                            }
                            $errors[] = $msg;
                        }
                    }
                }

                // Test as Admin for admin/ routes
                if (str_starts_with($uri, 'admin')) {
                    // Re-fetch admin to avoid detached models
                    $admin = \App\Models\Admin::first();
                    if ($admin) {
                        $response = $this->actingAs($admin, 'admin')->get($uri);
                        if ($response->getStatusCode() >= 500) {
                            $msg = "[Admin] GET /{$uri} returned " . $response->getStatusCode();
                            if (isset($response->exception)) {
                                $msg .= " -> " . $response->exception->getMessage();
                            }
                            $errors[] = $msg;
                        }
                    }
                }
            } catch (\Exception $e) {
                // Catch hard crashes
                $errors[] = sprintf("GET /%s crashed -> %s", $uri, $e->getMessage());
            }
        }

        file_put_contents(base_path('scan_results.txt'), implode("\n", $errors));
        $this->assertEmpty($errors, "Found " . count($errors) . " errors on pages.");
    }
}
