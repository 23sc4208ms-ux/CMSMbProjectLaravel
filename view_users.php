<?php
// Simple script to view users with hashed passwords

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "\n=== User Accounts with Hashed Passwords (Argon2id) ===\n\n";

$users = User::all();

if ($users->count() === 0) {
    echo "No users found.\n";
} else {
    foreach ($users as $user) {
        echo "ID: {$user->id}\n";
        echo "Name: {$user->name}\n";
        echo "Email: {$user->email}\n";
        echo "Password Hash: {$user->password}\n";
        echo "Created: {$user->created_at}\n";
        echo str_repeat("-", 80) . "\n\n";
    }
}
?>
