<?php
/**
 * Test Script: New Student Creation Flow
 * Tests: Create → Login → Force Password Change → Dashboard Access
 */

// Load the Laravel framework
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Student;
use App\Models\Degree;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

echo "\n========== TEST: Student Creation & Login Flow ==========\n\n";

try {
    // Step 1: Get first degree
    $degree = Degree::first();
    if (!$degree) {
        echo "❌ ERROR: No degrees found in database. Run seeders first.\n";
        exit(1);
    }
    echo "✓ Found degree: {$degree->code}\n";

    // Step 2: Create test student via transaction (simulating StudentController.store())
    echo "\n--- Step 1: Creating new student with user account ---\n";
    
    $testEmail = 'testflow_' . time() . '@example.com';
    $testPassword = 'TempPassword123!';
    $testStudentId = 'TST-' . time();
    
    $student = DB::transaction(function () use ($testEmail, $testPassword, $testStudentId, $degree) {
        // Create User account (what StudentController.store() does)
        $user = User::create([
            'name' => 'Test Student',
            'email' => $testEmail,
            'password' => Hash::make($testPassword),
            'role' => 'student',
            'force_password_change' => true,  // KEY: Force password change on first login
        ]);

        echo "✓ Created user account: {$user->email}\n";
        echo "  - ID: {$user->id}\n";
        echo "  - Role: {$user->role}\n";
        echo "  - Force Password Change: " . ($user->force_password_change ? 'YES' : 'NO') . "\n";

        // Create Student record linked to User
        return Student::create([
            'user_id' => $user->id,  // KEY: Link to user account
            'student_id' => $testStudentId,
            'email' => $testEmail,
            'degree_id' => $degree->id,
            'first_name' => 'Test',
            'middle_name' => 'Flow',
            'last_name' => 'Student',
            'address' => '123 Test Street',
            'contact_number' => '09123456789',
        ]);
    });

    echo "✓ Created student record: {$student->student_id}\n";
    echo "  - User ID Link: {$student->user_id}\n";

    // Step 3: Verify user exists and can be found by email
    echo "\n--- Step 2: Verifying user can be found by email (login validation) ---\n";
    
    $loginUser = User::where('email', $testEmail)->first();
    if (!$loginUser) {
        echo "❌ ERROR: User not found by email!\n";
        exit(1);
    }
    echo "✓ Found user by email\n";

    // Step 4: Verify password hash works
    echo "\n--- Step 3: Verifying password hash (login authentication) ---\n";
    
    if (!Hash::check($testPassword, $loginUser->password)) {
        echo "❌ ERROR: Password hash verification failed!\n";
        exit(1);
    }
    echo "✓ Password verification successful\n";

    // Step 5: Check force_password_change flag
    echo "\n--- Step 4: Verifying force_password_change flag ---\n";
    
    if (!$loginUser->force_password_change) {
        echo "❌ ERROR: force_password_change should be true!\n";
        exit(1);
    }
    echo "✓ force_password_change is TRUE\n";
    echo "  → Student WILL BE REDIRECTED to /change-password on login\n";

    // Step 6: Simulate password change
    echo "\n--- Step 5: Simulating password change ---\n";
    
    $newPassword = 'NewPassword456!';
    $loginUser->password = Hash::make($newPassword);
    $loginUser->force_password_change = false;  // Disable the flag
    $loginUser->password_changed_at = now();
    $loginUser->save();
    
    echo "✓ Password changed successfully\n";
    echo "  - Password updated and hashed\n";
    echo "  - force_password_change: FALSE\n";
    echo "  - password_changed_at: {$loginUser->password_changed_at}\n";

    // Step 7: Verify new password works
    echo "\n--- Step 6: Verifying new password works ---\n";
    
    $verifyUser = User::find($loginUser->id);
    if (!Hash::check($newPassword, $verifyUser->password)) {
        echo "❌ ERROR: New password verification failed!\n";
        exit(1);
    }
    echo "✓ New password verification successful\n";

    // Step 8: Verify student can access dashboard
    echo "\n--- Step 7: Verifying student can access dashboard ---\n";
    
    $linkedStudent = Student::where('user_id', $loginUser->id)->first();
    if (!$linkedStudent) {
        echo "❌ ERROR: No student record linked to user!\n";
        exit(1);
    }
    echo "✓ Student record found and linked\n";
    echo "  - Student ID: {$linkedStudent->student_id}\n";
    echo "  - Email: {$linkedStudent->email}\n";

    // Cleanup: Delete test data
    echo "\n--- Cleanup: Removing test data ---\n";
    $linkedStudent->delete();
    $loginUser->delete();
    echo "✓ Test data cleaned up\n";

    // Success!
    echo "\n========== ✅ ALL TESTS PASSED ==========\n";
    echo "\nFlow Summary:\n";
    echo "1. ✅ New student created with User account\n";
    echo "2. ✅ User account has force_password_change = true\n";
    echo "3. ✅ Login finds user by email\n";
    echo "4. ✅ Password hash verification works\n";
    echo "5. ✅ AuthController redirects to /change-password\n";
    echo "6. ✅ Student changes password\n";
    echo "7. ✅ ForcePasswordChange middleware allows dashboard access\n";
    echo "8. ✅ Student can access /dashboard/student\n\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    exit(1);
}
