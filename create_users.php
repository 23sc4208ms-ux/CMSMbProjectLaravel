<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== Creating Admin and Teacher Users ===\n\n";

// Create Admin User
$adminPassword = 'AdminPass123!';
$adminHash = Hash::make($adminPassword);

$admin = User::updateOrCreate(
    ['email' => 'admin@example.com'],
    [
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => $adminHash,
        'role' => 'admin',
        'force_password_change' => false,
        'failed_login_attempts' => 0,
    ]
);

echo "✓ Admin User Created/Updated\n";
echo "  Email: admin@example.com\n";
echo "  Password: AdminPass123!\n";
echo "  Role: admin\n\n";

// Create Teacher User
$teacherPassword = 'TeacherPass123!';
$teacherHash = Hash::make($teacherPassword);

$teacher = User::updateOrCreate(
    ['email' => 'teacher@example.com'],
    [
        'name' => 'Teacher User',
        'email' => 'teacher@example.com',
        'password' => $teacherHash,
        'role' => 'teacher',
        'force_password_change' => false,
        'failed_login_attempts' => 0,
    ]
);

echo "✓ Teacher User Created/Updated\n";
echo "  Email: teacher@example.com\n";
echo "  Password: TeacherPass123!\n";
echo "  Role: teacher\n\n";

// List all users
echo "=== All Users in System ===\n\n";
$users = User::all();
foreach ($users as $u) {
    echo "👤 {$u->name}\n";
    echo "   Email: {$u->email}\n";
    echo "   Role: " . strtoupper($u->role) . "\n";
    echo "   ID: {$u->id}\n\n";
}
