<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TeacherAnnotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    // List all teachers
    public function index()
    {
        $teachers = User::query()->where('role', 'teacher')->get();
        return view('teacher.index', ['teachers' => $teachers]);
    }

    // Show form to create teacher
    public function create()
    {
        return view('teacher.create');
    }

    // Store teacher in database
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = 'teacher';
        $validated['force_password_change'] = true;

        User::create($validated);

        return redirect()->route('teacher.index')->with('success', 'Teacher created successfully! They can now login.');
    }

    // Show teacher details
    public function show(User $user)
    {
        if ($user->role !== 'teacher') {
            return redirect()->route('teacher.index')->with('error', 'Invalid teacher.');
        }

        return view('teacher.show', ['teacher' => $user]);
    }

    // Show edit form
    public function edit(User $user)
    {
        if ($user->role !== 'teacher') {
            return redirect()->route('teacher.index')->with('error', 'Invalid teacher.');
        }

        return view('teacher.edit', ['teacher' => $user]);
    }

    // Update teacher
    public function update(Request $request, User $user)
    {
        if ($user->role !== 'teacher') {
            return redirect()->route('teacher.index')->with('error', 'Invalid teacher.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return redirect()->route('teacher.index')->with('success', 'Teacher updated successfully!');
    }

    // Delete teacher
    public function destroy(User $user)
    {
        if ($user->role !== 'teacher') {
            return redirect()->route('teacher.index')->with('error', 'Invalid teacher.');
        }

        try {
            // First, clear teacher references from students
            \App\Models\Student::where('teacher_id', $user->id)->update(['teacher_id' => null]);
            
            // Then delete the user
            $user->delete();

            return redirect()->route('teacher.index')->with('success', 'Teacher deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('teacher.index')->with('error', 'Error deleting teacher: ' . $e->getMessage());
        }
    }

    // Show annotation form for a teacher
    public function annotate(User $user)
    {
        if ($user->role !== 'teacher') {
            return redirect()->route('teacher.index')->with('error', 'Invalid teacher.');
        }

        return view('teacher.annotate', ['teacher' => $user]);
    }

    // Store annotation
    public function storeAnnotation(Request $request, User $user)
    {
        if ($user->role !== 'teacher') {
            return redirect()->route('teacher.index')->with('error', 'Invalid teacher.');
        }

        $validated = $request->validate([
            'annotation' => 'required|string|min:5|max:1000',
        ]);

        TeacherAnnotation::create([
            'teacher_id' => $user->id,
            'admin_id' => session('user_id'),
            'annotation' => $validated['annotation'],
        ]);

        return redirect()->route('teachers.show', $user->id)->with('success', 'Note added successfully!');
    }
}
