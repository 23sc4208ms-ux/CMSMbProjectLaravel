<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;

class DashboardController extends Controller
{
    // Admin Dashboard
    public function adminDashboard()
    {
        $totalTeachers = User::query()->where('role', 'teacher')->count();
        $totalStudents = User::query()->where('role', 'student')->count();

        return view('dashboard.admin', [
            'totalTeachers' => $totalTeachers,
            'totalStudents' => $totalStudents,
        ]);
    }

    // Teacher Dashboard
    public function teacherDashboard()
    {
        $userId = session('user_id');
        $user = User::query()->find($userId);

        return view('dashboard.teacher', [
            'user' => $user,
        ]);
    }

    // Student Dashboard
    public function studentDashboard()
    {
        $userId = session('user_id');
        $user = User::query()->find($userId);
        $student = Student::query()->with(['teacher', 'degree'])->where('user_id', $userId)->first();

        return view('dashboard.student', [
            'user' => $user,
            'student' => $student,
        ]);
    }
}
