<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class CheckAdminRole
{
    public function handle(Request $request, Closure $next)
    {
        $userId = session('user_id');

        if (!$userId) {
            return redirect('/login')->with('error', 'Please login first.');
        }

        $user = User::query()->find($userId);

        if (!$user || $user->role !== 'admin') {
            return redirect('/login')->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }
}
