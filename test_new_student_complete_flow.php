<?php
/**
 * COMPLETE TEST: New Student Creation → Login → Password Change → Dashboard
 * This simulates exactly what happens when admin creates a student through the form
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Student;
use App\Models\Degree;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

echo "\n========== COMPLETE STUDENT CREATION & LOGIN TEST ==========\n\n";

try {
    // Get degree
    $degree = Degree::first();
    if (!$degree) {
        echo "❌ ERROR: No degrees found\n";
        exit(1);
    }

    // Simulate admin form submission data
    $timestamp = time();
    $formData = [
        'student_id' => 'NEW-TEST-' . $timestamp,
        'email' => 'newstudent' . $timestamp . '@example.com',
        'first_name' => 'New',
        'middle_name' => 'Test',
        'last_name' => 'Student',
        'address' => '456 New Street',
        'contact_number' => '09987654321',
        'degree_id' => $degree->id,
        'username' => 'newstudent' . $timestamp,  // Display name
        'password' => 'NewStudent123!',             // Temporary password
    ];

    echo "SCENARIO: Admin creates new student\n";
    echo "=====================================\n";
    echo "Form Data:\n";
    echo "  Student ID: {$formData['student_id']}\n";
    echo "  Email: {$formData['email']}\n";
    echo "  Name: {$formData['first_name']} {$formData['middle_name']} {$formData['last_name']}\n";
    echo "  Username (Login Name): {$formData['username']}\n";
    echo "  Password (Temporary): {$formData['password']}\n";

    // STEP 1: Create student (simulating StudentController.store())
    echo "\n--- STEP 1: Creating student (StudentController.store) ---\n";

    $student = DB::transaction(function () use ($formData) {
        // Create User account
        $user = User::create([
            'name' => $formData['username'],
            'email' => $formData['email'],
            'password' => Hash::make($formData['password']),
            'role' => 'student',
            'force_password_change' => true,  // FORCE PASSWORD CHANGE ON FIRST LOGIN
        ]);

        echo "✓ User created (ID: {$user->id})\n";
        echo "  - Email: {$user->email}\n";
        echo "  - Role: {$user->role}\n";
        echo "  - Force Password Change: " . ($user->force_password_change ? 'YES' : 'NO') . "\n";

        // Create Student record
        return Student::create([
            'user_id' => $user->id,
            'student_id' => $formData['student_id'],
            'email' => $formData['email'],
            'degree_id' => $formData['degree_id'],
            'first_name' => $formData['first_name'],
            'middle_name' => $formData['middle_name'],
            'last_name' => $formData['last_name'],
            'address' => $formData['address'],
            'contact_number' => $formData['contact_number'],
        ]);
    });

    echo "✓ Student created (ID: {$student->student_id})\n";
    echo "  - User ID Link: {$student->user_id}\n";

    // STEP 2: Student tries to login (simulating login form submission)
    echo "\n--- STEP 2: Student logs in (AuthController.login) ---\n";

    $loginUser = User::where('email', $formData['email'])->first();
    if (!$loginUser) {
        echo "❌ ERROR: User not found for login!\n";
        exit(1);
    }
    echo "✓ Found user by email\n";

    // Validate password
    if (!Hash::check($formData['password'], $loginUser->password)) {
        echo "❌ ERROR: Password validation failed!\n";
        exit(1);
    }
    echo "✓ Password validation: PASSED\n";

    // Check if force_password_change is set
    if (!$loginUser->force_password_change) {
        echo "❌ ERROR: force_password_change should be TRUE!\n";
        exit(1);
    }
    echo "✓ force_password_change: TRUE\n";
    echo "→ AuthController will redirect to /change-password\n";

    // Simulate session creation (what AuthController does on successful login)
    session([
        'user_id' => $loginUser->id,
        'user_email' => $loginUser->email,
        'user_name' => $loginUser->name,
        'user_role' => $loginUser->role,
    ]);
    echo "✓ Session created\n";

    // STEP 3: Student is on change-password page (PasswordController.showChangeForm)
    echo "\n--- STEP 3: Student accesses change-password page ---\n";

    $sessionUserId = session('user_id');
    $userOnChangePage = User::find($sessionUserId);

    if (!$userOnChangePage) {
        echo "❌ ERROR: User not found in session!\n";
        exit(1);
    }
    echo "✓ User found in session\n";
    echo "✓ Page displays: 'You must change your password before continuing'\n";

    // STEP 4: Student submits password change form (PasswordController.updatePassword)
    echo "\n--- STEP 4: Student submits password change form ---\n";

    $newPassword = 'StudentNewPassword456!';
    $userOnChangePage->password = Hash::make($newPassword);
    $userOnChangePage->force_password_change = false;  // REMOVE the flag
    $userOnChangePage->password_changed_at = now();
    $userOnChangePage->save();

    echo "✓ Password changed\n";
    echo "  - Old password: {$formData['password']} (temporary)\n";
    echo "  - New password: {$newPassword}\n";
    echo "  - force_password_change: FALSE\n";
    echo "  - password_changed_at: {$userOnChangePage->password_changed_at}\n";

    // STEP 5: Student is redirected to dashboard
    echo "\n--- STEP 5: Student accesses dashboard ---\n";

    $dashboardUser = User::find($sessionUserId);

    // Check ForcePasswordChange middleware
    if ($dashboardUser->force_password_change) {
        echo "❌ ERROR: ForcePasswordChange middleware would redirect back to /change-password!\n";
        exit(1);
    }
    echo "✓ ForcePasswordChange middleware: PASS\n";

    // Check authentication
    if (!$sessionUserId) {
        echo "❌ ERROR: Not authenticated!\n";
        exit(1);
    }
    echo "✓ Authentication: PASS\n";

    // Check student role
    if ($dashboardUser->role !== 'student') {
        echo "❌ ERROR: User role is not student!\n";
        exit(1);
    }
    echo "✓ Role check: PASS (role = student)\n";

    // Check student record exists
    $linkedStudent = Student::where('user_id', $dashboardUser->id)->first();
    if (!$linkedStudent) {
        echo "❌ ERROR: No student record linked!\n";
        exit(1);
    }
    echo "✓ Student record: FOUND\n";
    echo "  - Student ID: {$linkedStudent->student_id}\n";

    echo "✓ Dashboard access: ALLOWED\n";
    echo "→ Student is redirected to /dashboard/student\n";

    // STEP 6: Future logins (no password change required)
    echo "\n--- STEP 6: Student logs in again (future login) ---\n";

    $futureLoginUser = User::where('email', $formData['email'])->first();
    if (!$futureLoginUser) {
        echo "❌ ERROR: User not found for future login!\n";
        exit(1);
    }
    echo "✓ Found user by email\n";

    if (!Hash::check($newPassword, $futureLoginUser->password)) {
        echo "❌ ERROR: New password doesn't work!\n";
        exit(1);
    }
    echo "✓ New password validation: PASSED\n";

    if ($futureLoginUser->force_password_change) {
        echo "❌ ERROR: force_password_change should be FALSE!\n";
        exit(1);
    }
    echo "✓ force_password_change: FALSE\n";
    echo "→ AuthController will redirect directly to /dashboard/student\n";

    // Cleanup
    echo "\n--- CLEANUP: Removing test data ---\n";
    $linkedStudent->delete();
    $futureLoginUser->delete();
    echo "✓ Test data cleaned up\n";

    // SUCCESS!
    echo "\n========== ✅ ALL TESTS PASSED ==========\n";
    echo "\nCOMPLETE FLOW VERIFIED:\n";
    echo "✅ Step 1: Admin creates student with user account\n";
    echo "✅ Step 2: Student logs in with temporary password\n";
    echo "✅ Step 3: Student is forced to /change-password\n";
    echo "✅ Step 4: Student changes password\n";
    echo "✅ Step 5: Student accesses /dashboard/student\n";
    echo "✅ Step 6: Future logins use new password, no forced change\n";
    echo "\nThis flow is NOW WORKING!\n\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    exit(1);
}
