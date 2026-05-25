<?php

namespace App\Http\Controllers;

use App\Models\Student;

class PagesController extends Controller
{
    public function about()
    {
        return view('about-us');
    }

    public function userProfile(int $studentId = 1)
    {
        $student = Student::with('profile')->find($studentId);

        return view('pages.user-profile', [
            'student' => $student,
        ]);
    }

    public function userPosts(int $studentId = 1)
    {
        $student = Student::with('posts')->find($studentId);

        return view('pages.user-posts', [
            'student' => $student,
        ]);
    }

    public function studentCourses()
    {
        $enrollments = Student::with('courses')
            ->whereHas('courses')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('pages.student-courses', [
            'enrollments' => $enrollments,
        ]);
    }
    public function maintenance()
    {
        return view('maintenance');
    }
}
