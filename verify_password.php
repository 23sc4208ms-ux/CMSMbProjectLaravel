<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::where('email', 'student@example.com')->first();

if ($user) {
    $testPassword = 'StudentPass123!';
    $isMatch = Hash::check($testPassword, $user->password);

    echo "Password Verification Test:\n";
    echo "Test Password: " . $testPassword . "\n";
    echo "Hash Match: " . ($isMatch ? 'YES ✓' : 'NO ✗') . "\n";

    if (!$isMatch) {
        echo "\nTrying to re-hash the password for the user...\n";
        $hashedPassword = Hash::make($testPassword);
        $user->update(['password' => $hashedPassword]);
        echo "Password updated! Try logging in again.\n";
    }
} else {
    echo "User not found!\n";
}
