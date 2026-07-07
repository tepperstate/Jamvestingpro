<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Throwable;

class ScanRoutesCommand extends Command
{
    protected $signature = 'scan:routes';

    protected $description = 'Scan all GET routes for 500 errors';

    public function handle()
    {
        $this->info('Scanning all GET routes...');

        $routes = Route::getRoutes();
        $getRoutes = [];

        foreach ($routes as $route) {
            if (in_array('GET', $route->methods()) && ! str_contains($route->uri(), '{')) {
                // Ignore API and vendor routes if desired, but we will test them all if possible
                if (str_contains($route->uri(), 'broadcasting') || str_contains($route->uri(), 'livewire')) {
                    continue;
                }
                $getRoutes[] = $route->uri() === '/' ? '/' : '/'.$route->uri();
            }
        }

        $getRoutes = array_unique($getRoutes);
        $total = count($getRoutes);
        $this->info("Found {$total} distinct GET routes without parameters.");

        // We will authenticate a test user if possible, to test auth routes
        $user = User::first();
        if ($user) {
            Auth::login($user);
            $this->info('Authenticated as User ID: '.$user->id);
        } else {
            $this->warn('No users found. Auth routes will likely redirect to login (302).');
        }

        $failed = [];
        $passed = 0;

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($getRoutes as $uri) {
            try {
                $request = Request::create($uri, 'GET');

                // We use handle() on the Http Kernel to simulate the request
                $kernel = app()->make(Kernel::class);
                $response = $kernel->handle($request);

                if ($response->getStatusCode() === 500) {
                    $failed[] = [
                        'uri' => $uri,
                        'status' => 500,
                        'exception' => $response->exception ? $response->exception->getMessage() : 'Unknown 500 Error',
                    ];
                } else {
                    $passed++;
                }

            } catch (Throwable $e) {
                $failed[] = [
                    'uri' => $uri,
                    'status' => 'Fatal/Exception',
                    'exception' => $e->getMessage(),
                ];
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if (count($failed) > 0) {
            $this->error('Found '.count($failed).' errors!');
            foreach ($failed as $fail) {
                $this->error("[{$fail['status']}] {$fail['uri']}");
                $this->line('   - '.$fail['exception']);
            }
        } else {
            $this->info("Success! All {$passed} routes returned non-500 status codes (200, 301, 302, 403, 404).");
        }
    }
}
