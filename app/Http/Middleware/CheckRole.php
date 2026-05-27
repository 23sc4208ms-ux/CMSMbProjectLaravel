<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // If user is not logged in, redirect to login page with message
        if (!session()->has('user_id')) {
            return redirect('/login')->with('error', 'Please login first.');
        }

        // If roles were provided, ensure the user has one of them
        if (!empty($roles)) {
            $userRole = strtolower((string) session('user_role'));
            $allowed = array_map(fn($r) => strtolower((string) $r), $roles);

            if (!in_array($userRole, $allowed, true)) {
                return redirect('/login')->with('error', 'Unauthorized access.');
            }
        }

        return $next($request);
    }
}
