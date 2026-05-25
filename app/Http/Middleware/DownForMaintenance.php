<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DownForMaintenance
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Show maintenance page only in local environment
        if (app()->environment('local')) {
            // Allow access to maintenance route itself
            if ($request->routeIs('maintenance')) {
                return $next($request);
            }
            // Redirect all other routes to maintenance
            return redirect()->route('maintenance');
        }

        // In production and other environments, allow normal access
        return $next($request);
    }
}
