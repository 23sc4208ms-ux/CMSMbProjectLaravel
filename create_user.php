<?php
// Load Laravel app
$app = require __DIR__ . '/bootstrap/app.php';

// Make kernel and bootstrap
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Use password_hash with Argon2id
    $hashedPassword = password_hash('StudentPass123!', PASSWORD_ARGON2ID);

    DB::table('users')->insertOrIgnore([
        'name' => 'Student User',
        'email' => 'student@example.com',
        'password' => $hashedPassword,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $user = DB::table('users')->where('email', 'student@example.com')->first();

    echo "✓ User created successfully!\n";
    echo "  ID: " . $user->id . "\n";
    echo "  Name: " . $user->name . "\n";
    echo "  Email: " . $user->email . "\n";
    echo "  Password: hashed with Argon2id\n";
    echo "  Hash Prefix: " . substr($user->password, 0, 15) . "...\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
