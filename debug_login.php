<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "\n========== CHECKING LOGIN ISSUE ==========\n\n";

// Check for testnew@example.com
$testEmail = 'testnew@example.com';
echo "Looking for: {$testEmail}\n";

$user = User::where('email', $testEmail)->first();

if (!$user) {
    echo "❌ EMAIL NOT FOUND in users table\n";
    echo "\nAvailable users:\n";
    $allUsers = User::all();
    foreach ($allUsers as $u) {
        echo "  - {$u->email}\n";
    }
    exit(1);
}

echo "✓ Found user: {$user->email}\n";
echo "  - ID: {$user->id}\n";
echo "  - Role: {$user->role}\n";
echo "  - Password hash: " . substr($user->password, 0, 40) . "...\n\n";

// Test password
echo "Testing password: TestPass123!\n";
$passwordOk = Hash::check('TestPass123!', $user->password);
echo "Result: " . ($passwordOk ? "✅ CORRECT" : "❌ WRONG") . "\n";

if (!$passwordOk) {
    echo "\nTrying other common passwords:\n";
    $testPasswords = ['TestPass123', 'testpass123', 'Test123', 'password', 'test'];
    foreach ($testPasswords as $testPass) {
        $result = Hash::check($testPass, $user->password);
        echo "  - {$testPass}: " . ($result ? "✅ MATCH" : "❌") . "\n";
    }
}

echo "\n";
