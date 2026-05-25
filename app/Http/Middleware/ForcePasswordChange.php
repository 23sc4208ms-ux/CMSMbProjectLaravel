<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next)
    {
        $userId = session('user_id');

        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::query()->find($userId);

        if (!$user) {
            return redirect()->route('login')->with('error', 'User not found.');
        }

        // If user needs to change password and is not already on the change password page
        if ($user->force_password_change && !$request->routeIs('password.change')) {
            return redirect()->route('password.change')
                ->with('warning', 'You must change your password before continuing.');
        }

        return $next($request);
    }
}
