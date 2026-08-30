<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RestrictLan
{
    public function handle(Request $request, Closure $next)
    {
        // Allow public access when Telegram Mini App or tunnel mode is enabled
        if (env('ALLOW_PUBLIC_TMA', true)) {
            return $next($request);
        }

        $ip = (string) $request->ip();

        // Allow localhost for development
        if (in_array($ip, ['127.0.0.1', '::1', ''])) {
            return $next($request);
        }

        // Check if IP starts with 192.168. or 10. (Common LAN subnets)
        if (str_starts_with($ip, '192.168.') || str_starts_with($ip, '10.')) {
            return $next($request);
        }

        abort(403, 'Forbidden. Access restricted to internal company network only.');
    }
}
