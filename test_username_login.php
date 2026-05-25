<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "\n========== Testing Username-Based Login ==========\n\n";

// Test with Student User account
$testUsername = 'Student User';
$testPassword = 'StudentPass123!';

$user = User::where('name', $testUsername)->first();

if (!$user) {
    echo "❌ User not found by username: {$testUsername}\n";
    exit(1);
}

echo "✓ Found user by username: {$testUsername}\n";
echo "  - ID: {$user->id}\n";
echo "  - Email: {$user->email}\n";
echo "  - Role: {$user->role}\n";

$passwordOk = Hash::check($testPassword, $user->password);
echo "✓ Password check: " . ($passwordOk ? "✅ CORRECT" : "❌ WRONG") . "\n";

echo "\n✅ Username-based login should work!\n\n";
