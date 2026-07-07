<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BlockMaliciousRequests
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $blockedExtensions = ['php', 'phar', 'phtml', 'php3', 'php4', 'php5', 'php7', 'php8', 'asp', 'jsp', 'exe', 'sh', 'bat', 'cmd', 'js', 'pl', 'py'];

        $path = $request->path();

        foreach ($blockedExtensions as $ext) {
            if (preg_match("/\.{$ext}$/i", $path)) {
                abort(403, 'Access Denied (Dangerous File Detected)');
            }
        }

        // Optionally: Block direct requests to known sensitive folders
        $blockedFolders = [
            'storage',
            'uploads',
            'user/storage/app/public/image',
            'config',
            'vendor',
            'node_modules',
            'resources',
            'bootstrap/cache',
            'logs',
            'temp',
            'private',
            'backup',
            'secrets',
            'env',
            'system',
        ];

        foreach ($blockedFolders as $folder) {
            if (preg_match("/(^|\/)".preg_quote($folder, '/')."($|\/)/i", $path)) {
                abort(403, 'Access Denied (Blocked Folder)');
            }
        }

        return $next($request);
    }
}
