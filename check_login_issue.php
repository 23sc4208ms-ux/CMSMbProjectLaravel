<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Student;

echo "\n========== DATABASE CHECK ==========\n\n";

echo "--- USERS TABLE ---\n";
$users = User::all();
foreach ($users as $user) {
    echo "ID: {$user->id} | Email: {$user->email} | Role: {$user->role} | Force PW Change: " . ($user->force_password_change ? 'YES' : 'NO') . "\n";
}

echo "\n--- STUDENTS TABLE ---\n";
$students = Student::all();
foreach ($students as $student) {
    $user = $student->user;
    echo "Student ID: {$student->student_id} | User ID: {$student->user_id} | Email: {$student->email} | Has User: " . ($user ? 'YES' : 'NO') . "\n";
    if (!$user) {
        echo "  ⚠️  WARNING: Student has no linked user account!\n";
    }
}

echo "\n--- PASSWORD TEST ---\n";
$testUser = User::where('email', 'student@example.com')->first();
if ($testUser) {
    echo "Testing student@example.com with password: StudentPass123!\n";
    $correct = \Illuminate\Support\Facades\Hash::check('StudentPass123!', $testUser->password);
    echo "Password check result: " . ($correct ? "✅ CORRECT" : "❌ WRONG") . "\n";
    echo "Hashed password in DB: " . substr($testUser->password, 0, 30) . "...\n";
} else {
    echo "❌ student@example.com not found in users table!\n";
}

echo "\n";
