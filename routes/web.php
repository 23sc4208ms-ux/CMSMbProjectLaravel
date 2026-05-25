<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DegreeController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

// Root redirect to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Login Routes (No Auth Required)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Change Routes (Auth Required - excluded from force password change middleware)
Route::middleware('auth')->group(function () {
    Route::get('/change-password', [PasswordController::class, 'showChangeForm'])->name('password.change');
    Route::post('/change-password', [PasswordController::class, 'updatePassword'])->name('password.update');
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
});

// Dashboard Routes (Auth Required)
Route::middleware(['auth', 'force.password.change'])->group(function () {
    // Admin Dashboard
    Route::get('/dashboard/admin', [DashboardController::class, 'adminDashboard'])->middleware('checkrole:admin')->name('dashboard.admin');

    // Teacher Dashboard
    Route::get('/dashboard/teacher', [DashboardController::class, 'teacherDashboard'])->middleware('checkrole:teacher')->name('dashboard.teacher');

    // Student Dashboard
    Route::get('/dashboard/student', [DashboardController::class, 'studentDashboard'])->middleware('checkrole:student')->name('dashboard.student');
});

Route::view('/home', 'home')->name('home');
Route::view('/maintenance', 'maintenance')->name('maintenance');
Route::view('/dashboard', 'dashboard')->name('dashboard');
Route::view('/about-us', 'maintenance')->name('about-us');

Route::get('/about', [PagesController::class, 'about'])->name('pages.about');
Route::get('/user-profile', [PagesController::class, 'userProfile'])->name('pages.user-profile');
Route::get('/user_profile', [PagesController::class, 'userProfile'])->name('pages.user_profile');
Route::get('/user-posts', [PagesController::class, 'userPosts'])->name('pages.user-posts');
Route::get('/user_posts', [PagesController::class, 'userPosts'])->name('pages.user_posts');
Route::get('/student-courses', [PagesController::class, 'studentCourses'])->name('pages.student-courses');
Route::get('/student_courses', [PagesController::class, 'studentCourses'])->name('pages.student_courses');
Route::get('/user-courses', [PagesController::class, 'studentCourses'])->name('pages.user-courses');
Route::get('/user_courses', [PagesController::class, 'studentCourses'])->name('pages.user_courses');

Route::get('/degrees', [DegreeController::class, 'index'])->name('degrees.index');
Route::get('/degrees.json', [DegreeController::class, 'ajaxIndex'])->name('degrees.ajax');
Route::post('/degrees', [DegreeController::class, 'store'])->name('degrees.store');
Route::get('/degrees/{degree}/edit', [DegreeController::class, 'edit'])->name('degrees.edit');
Route::put('/degrees/{degree}', [DegreeController::class, 'update'])->name('degrees.update');
Route::get('/degrees/{degree}', [DegreeController::class, 'show'])->name('degrees.show');
Route::delete('/degrees/{degree}', [DegreeController::class, 'destroy'])->name('degrees.destroy');

// Teacher Routes (Admin Only)
Route::middleware('auth')->group(function () {
    Route::get('/teachers', [TeacherController::class, 'index'])->middleware('checkrole:admin')->name('teacher.index');
    Route::get('/teachers/create', [TeacherController::class, 'create'])->middleware('checkrole:admin')->name('teacher.create');
    Route::post('/teachers', [TeacherController::class, 'store'])->middleware('checkrole:admin')->name('teacher.store');
    Route::get('/teachers/{user}', [TeacherController::class, 'show'])->middleware('checkrole:admin')->name('teacher.show');
    Route::get('/teachers/{user}/edit', [TeacherController::class, 'edit'])->middleware('checkrole:admin')->name('teacher.edit');
    Route::put('/teachers/{user}', [TeacherController::class, 'update'])->middleware('checkrole:admin')->name('teacher.update');
    Route::delete('/teachers/{user}', [TeacherController::class, 'destroy'])->middleware('checkrole:admin')->name('teacher.destroy');
    Route::get('/teachers/{user}/annotate', [TeacherController::class, 'annotate'])->middleware('checkrole:admin')->name('teacher.annotate');
    Route::post('/teachers/{user}/annotate', [TeacherController::class, 'storeAnnotation'])->middleware('checkrole:admin')->name('teacher.store-annotation');
});

// Student Routes (Auth Required)
Route::middleware('auth')->group(function () {
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    // AJAX CRUD endpoints and page
    Route::get('/students/ajax', function () {
        return view('students.ajax_index');
    })->name('students.ajax')->middleware('checkrole:admin');

    Route::get('/ajax/students', [StudentController::class, 'ajaxIndex'])->name('students.ajax.index')->middleware('checkrole:admin');
    Route::get('/ajax/students/{student}', [StudentController::class, 'ajaxShow'])->name('students.ajax.show')->middleware('checkrole:admin');
    Route::post('/ajax/students', [StudentController::class, 'ajaxStore'])->name('students.ajax.store')->middleware('checkrole:admin');
    Route::put('/ajax/students/{student}', [StudentController::class, 'ajaxUpdate'])->name('students.ajax.update')->middleware('checkrole:admin');
    Route::delete('/ajax/students/{student}', [StudentController::class, 'ajaxDestroy'])->name('students.ajax.destroy')->middleware('checkrole:admin');
    Route::get('/students/create', [StudentController::class, 'create'])->middleware('checkrole:admin')->name('students.create');
    Route::post('/students', [StudentController::class, 'store'])->middleware('checkrole:admin')->name('students.store');
    Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->middleware('checkrole:admin')->name('students.edit');
    Route::put('/students/{student}', [StudentController::class, 'update'])->middleware('checkrole:admin')->name('students.update');
    Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])->middleware('checkrole:admin')->name('students.destroy');
    Route::get('/student_courses/{student}', [StudentController::class, 'studentCourses'])->name('students.courses');
});

Route::get('/try', [RedirectController::class, 'index'])->name('RedirectIndex');
Route::get('/try/{message}', [RedirectController::class, 'showMessage'])->name('RedirectMessage');

Route::get('/redirectme', function () {
    return redirect()->route('RedirectMessage', ['message' => 'This is my message']);
});

Route::get('/showSomething/{message?}', [RedirectController::class, 'showSomething'])->name('showSomething');

Route::get('/redirect-action', function () {
    return redirect()->action([RedirectController::class, 'showSomething'], ['message' => 'This is my message']);
});
