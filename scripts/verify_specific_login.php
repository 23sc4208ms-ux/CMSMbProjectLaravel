<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$email = 'testnew@example.com';
$password = 'TestPass123!';

$user = User::where('email', $email)->first();
if (! $user) { echo "User not found\n"; exit(1); }

$ok = Hash::check($password, $user->password);
echo $ok ? "Password OK\n" : "Password mismatch\n";
