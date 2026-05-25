<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "\n========== TESTING ALL STUDENT ACCOUNTS ==========\n\n";

$students = [
    ['email' => 'student@example.com', 'password' => 'StudentPass123!'],
    ['email' => 'jake@gmail.com', 'password' => 'JakePass123!'],
    ['email' => 'james@gmail.com', 'password' => 'JamesPass123!'],
    ['email' => 'jamvis@gmail.com', 'password' => 'JamvisPass123!'],
    ['email' => 'tin@gmail.com', 'password' => 'TinPass123!'],
];

foreach ($students as $cred) {
    $user = User::where('email', $cred['email'])->first();
    
    if (!$user) {
        echo "❌ {$cred['email']}: NOT FOUND in database\n";
        continue;
    }
    
    $passwordOk = Hash::check($cred['password'], $user->password);
    $status = $passwordOk ? "✅ LOGIN OK" : "❌ INVALID PASSWORD";
    
    echo "{$status} | {$cred['email']} / {$cred['password']}\n";
    
    if (!$passwordOk) {
        echo "   Hashed in DB: " . substr($user->password, 0, 40) . "...\n";
    }
}

echo "\n";
