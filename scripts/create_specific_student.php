<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Student;
use App\Models\Degree;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

$email = 'testnew@example.com';
$password = 'TestPass123!';
$student_id = 'TST-UI-001';

$degree = Degree::first();
if (! $degree) { echo "No degree\n"; exit(1); }

try {
    $student = DB::transaction(function () use ($email, $password, $student_id, $degree) {
        $user = User::create([
            'name' => 'Test UI Student',
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'student',
            'force_password_change' => true,
        ]);

        return Student::create([
            'user_id' => $user->id,
            'student_id' => $student_id,
            'email' => $email,
            'degree_id' => $degree->id,
            'first_name' => 'Test',
            'middle_name' => 'UI',
            'last_name' => 'Student',
            'address' => 'Nowhere',
            'contact_number' => '09123456789',
        ]);
    });
    echo "Created student {$student->student_id} with email {$email}\n";
} catch (Exception $e) {
    echo "Failed: {$e->getMessage()}\n";
}
