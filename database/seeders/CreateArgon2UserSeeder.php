<?php

namespace Database\Seeders;

use App\Models\Degree;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CreateArgon2UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'role' => 'admin',
                'password' => Hash::make('AdminPass123!'),
                'force_password_change' => false,
                'failed_login_attempts' => 0,
                'locked_until' => null,
            ]
        );

        $teacher = User::updateOrCreate(
            ['email' => 'teacher@example.com'],
            [
                'name' => 'Teacher User',
                'role' => 'teacher',
                'password' => Hash::make('TeacherPass123!'),
                'force_password_change' => false,
                'failed_login_attempts' => 0,
                'locked_until' => null,
            ]
        );

        $studentUser = User::updateOrCreate(
            ['email' => 'student@example.com'],
            [
                'name' => 'Student User',
                'role' => 'student',
                'password' => Hash::make('StudentPass123!'),
                'force_password_change' => false,
                'failed_login_attempts' => 0,
                'locked_until' => null,
            ]
        );

        $degreeId = Degree::query()->value('id');

        if ($degreeId) {
            Student::updateOrCreate(
                ['user_id' => $studentUser->id],
                [
                    'teacher_id' => $teacher->id,
                    'student_id' => '23-SC-4208',
                    'email' => 'student@example.com',
                    'degree_id' => $degreeId,
                    'first_name' => 'Student',
                    'middle_name' => 'Demo',
                    'last_name' => 'User',
                    'address' => 'Demo Address',
                    'contact_number' => '09123456789',
                ]
            );
        }

        echo "Default admin, teacher, and student accounts restored.\n";
    }
}
