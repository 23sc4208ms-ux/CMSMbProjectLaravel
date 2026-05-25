<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "=== Checking Users in Database ===\n\n";

$user = User::where('email', 'student@example.com')->first();

if ($user) {
    echo "✓ User Found!\n";
    echo "  ID: " . $user->id . "\n";
    echo "  Name: " . $user->name . "\n";
    echo "  Email: " . $user->email . "\n";
    echo "  Role: " . $user->role . "\n";
    echo "  Password Hash: " . substr($user->password, 0, 50) . "...\n";
    echo "  Force Password Change: " . ($user->force_password_change ? 'Yes' : 'No') . "\n";
} else {
    echo "✗ User not found!\n";
    echo "  Available users:\n";
    $allUsers = User::all();
    foreach ($allUsers as $u) {
        echo "  - {$u->email} (Role: {$u->role})\n";
    }
}
