<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AuthController extends Controller
{
    const MAX_LOGIN_ATTEMPTS = 3;
    const LOCKOUT_TIME_MINUTES = 1;

    public function login(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'username' => 'required|string|min:2',
            'password' => 'required|min:6',
        ], [
            'username.required' => 'Username is required.',
            'username.string' => 'Username must be valid.',
            'username.min' => 'Username must be at least 2 characters.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters.',
        ]);

        // Find user by name OR email (allow entering username or email)
        $user = User::query()
            ->where(function ($q) use ($validated) {
                $q->where('name', $validated['username'])
                  ->orWhere('email', $validated['username']);
            })->first();

        // Check if account is locked
        if ($user && $user->locked_until && now() < $user->locked_until) {
            $lockoutTime = $user->locked_until;
            $remainingSeconds = now()->diffInSeconds($lockoutTime);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'locked' => true,
                    'message' => "Account is temporarily locked. Try again in {$remainingSeconds} seconds.",
                    'remaining_seconds' => $remainingSeconds,
                ], 422);
            }

            return back()
                ->withErrors(['username' => "Account is temporarily locked. Try again in {$remainingSeconds} seconds."])
                ->with('locked', true)
                ->with('locked_username', $user->name)
                ->with('remaining_seconds', $remainingSeconds)
                ->withInput($request->only('username'));
        }

        // Unlock account if lockout time has passed
        if ($user && $user->locked_until && now() >= $user->locked_until) {
            $user->update([
                'failed_login_attempts' => 0,
                'locked_until' => null,
            ]);
        }

        // Check if user exists and password is correct
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            // Increment failed attempts if user exists
            if ($user) {
                $user->update([
                    'failed_login_attempts' => $user->failed_login_attempts + 1,
                ]);

                // Lock account if max attempts reached
                if ($user->failed_login_attempts >= self::MAX_LOGIN_ATTEMPTS) {
                    $user->update([
                        'locked_until' => now()->addMinutes(self::LOCKOUT_TIME_MINUTES),
                    ]);

                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'locked' => true,
                            'message' => "Too many failed login attempts. Account locked for 1 minute.",
                            'remaining_seconds' => 60,
                        ], 422);
                    }

                    return back()
                        ->withErrors(['username' => "Too many failed login attempts. Account locked for 1 minute."])
                        ->with('locked', true)
                        ->with('locked_username', $user->name)
                        ->with('remaining_seconds', 60)
                        ->withInput($request->only('username'));
                }

                $attemptsLeft = self::MAX_LOGIN_ATTEMPTS - $user->failed_login_attempts;
                $message = "Invalid username or password. {$attemptsLeft} attempt(s) remaining.";

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'locked' => false,
                        'message' => $message,
                    ], 422);
                }

                return back()
                    ->withErrors(['username' => $message])
                    ->withInput($request->only('username'));
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'locked' => false,
                    'message' => 'Invalid username or password.',
                ], 422);
            }

            return back()
                ->withErrors(['username' => 'Invalid username or password.'])
                ->withInput($request->only('username'));
        }

        // Reset failed attempts on successful login
        $user->update([
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);

        // Login successful - store user in session
        session(['user_id' => $user->id, 'user_email' => $user->email, 'user_name' => $user->name, 'user_role' => $user->role]);

        // Check if user needs to change password
        if ($user->force_password_change) {
            $redirectUrl = route('password.change');
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'redirect_url' => $redirectUrl,
                ], 200);
            }
            return redirect()->route('password.change');
        }

        // Determine redirect URL based on user role
        if ($user->role === 'admin') {
            $redirectUrl = route('dashboard.admin');
        } elseif ($user->role === 'teacher') {
            $redirectUrl = route('dashboard.teacher');
        } else {
            $redirectUrl = route('dashboard.student');
        }

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'redirect_url' => $redirectUrl,
            ], 200);
        }

        // Redirect for traditional form submission
        if ($user->role === 'admin') {
            return redirect()->route('dashboard.admin')->with('success', 'Welcome Admin!');
        } elseif ($user->role === 'teacher') {
            return redirect()->route('dashboard.teacher')->with('success', 'Welcome Teacher!');
        } else {
            return redirect()->route('dashboard.student')->with('success', 'Welcome Student!');
        }
    }

    public function logout(Request $request)
    {
        session()->flush();
        return redirect()->route('login');
    }
}
