<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    // Show the change password form
    public function showChangeForm()
    {
        if (!session('user_id')) {
            return redirect()->route('login');
        }

        return view('auth.change-password');
    }

    // Update the password
    public function updatePassword(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'current_password' => 'required|min:6',
            'new_password' => 'required|min:6|confirmed',
            'new_password_confirmation' => 'required|min:6',
        ], [
            'current_password.required' => 'Current password is required.',
            'current_password.min' => 'Current password must be at least 6 characters.',
            'new_password.required' => 'New password is required.',
            'new_password.min' => 'New password must be at least 6 characters.',
            'new_password.confirmed' => 'The passwords do not match.',
            'new_password_confirmation.required' => 'Password confirmation is required.',
            'new_password_confirmation.min' => 'Password confirmation must be at least 6 characters.',
        ]);

        // Get the user
        $user = User::query()->find(session('user_id'));

        if (!$user) {
            return redirect()->route('login')->with('error', 'User not found.');
        }

        // Check if current password is correct
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Current password is incorrect.'])
                ->withInput($request->only('new_password', 'new_password_confirmation'));
        }

        // Check if new password is different from current password
        if (Hash::check($validated['new_password'], $user->password)) {
            return back()
                ->withErrors(['new_password' => 'New password must be different from current password.'])
                ->withInput($request->only('new_password', 'new_password_confirmation'));
        }

        // Update the password
        $user->password = Hash::make($validated['new_password']);
        $user->force_password_change = false;
        $user->password_changed_at = now();
        $user->save();

        // Redirect based on user role
        if ($user->role === 'admin') {
            return redirect()->route('dashboard.admin')->with('success', 'Password updated successfully!');
        } elseif ($user->role === 'teacher') {
            return redirect()->route('dashboard.teacher')->with('success', 'Password updated successfully!');
        } else {
            return redirect()->route('dashboard.student')->with('success', 'Password updated successfully!');
        }
    }
}
