<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Degree;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    private function studentRules(?Student $student = null): array
    {
        $userId = $student?->user_id;

        return [
            'student_id' => [
                'required',
                'string',
                'min:4',
                'max:50',
                Rule::unique('students', 'student_id')->ignore($student?->id),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('students', 'email')->ignore($student?->id),
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'degree_id' => 'required|exists:degrees,id',
            'first_name' => 'required|string|min:2|max:100',
            'middle_name' => 'nullable|string|min:2|max:100',
            'last_name' => 'required|string|min:2|max:100',
            'address' => 'required|string|min:5|max:255',
            'contact_number' => 'required|regex:/^[0-9]+$/|min:11|max:30',
            'username' => $student ? 'nullable|string|min:2|max:255' : 'required|string|min:2|max:255',
            'password' => $student && $student->user_id ? 'nullable|string|min:6' : 'required|string|min:6',
        ];
    }

    private function studentMessages(): array
    {
        return [
            '*.required' => 'The :attribute field is required.',
            '*.min' => 'The :attribute must be at least :min characters.',
            '*.max' => 'The :attribute must not exceed :max characters.',
            'student_id.unique' => 'This student ID is already in use.',
            'email.unique' => 'This email is already in use.',
            'degree_id.exists' => 'Please select a valid degree.',
            'contact_number.regex' => 'Contact number must contain digits only.',
            'username.required' => 'The username field is required.',
            'password.required' => 'The password field is required.',
        ];
    }

    private function studentAttributes(): array
    {
        return [
            'student_id' => 'student ID',
            'email' => 'email',
            'degree_id' => 'degree',
            'first_name' => 'first name',
            'middle_name' => 'middle name',
            'last_name' => 'last name',
            'address' => 'address',
            'contact_number' => 'contact number',
            'username' => 'username',
            'password' => 'password',
        ];
    }

    private function studentDisplayName(array $validated): string
    {
        $username = trim((string) ($validated['username'] ?? ''));

        if ($username !== '') {
            return $username;
        }

        return trim($validated['first_name'].' '.trim(($validated['middle_name'] ?? '').' '.$validated['last_name']));
    }

    private function logActivity(string $action, string $description, ?Student $student = null, ?string $ipAddress = null): void
    {
        try {
            ActivityLog::create([
                'action' => $action,
                'subject_type' => $student ? Student::class : null,
                'subject_id' => $student?->id,
                'description' => $description,
                'ip_address' => $ipAddress,
            ]);
        } catch (\Throwable $exception) {
            Log::warning('Activity log write failed', [
                'action' => $action,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    public function index()
    {
        $students = Student::latest()->paginate(10);

        return view('students.index', compact('students'));
    }

    public function create()
    {
        $degrees = Degree::orderBy('code')->get();

        return view('students.create', compact('degrees'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            $this->studentRules(),
            $this->studentMessages(),
            $this->studentAttributes()
        );

        if ($validator->fails()) {
            Log::warning('Student store validation failed', ['errors' => $validator->errors()->toArray()]);

            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        // Log validated payload for diagnostics (helps trace why some students appear missing)
        Log::info('Student store validated data', ['validated' => $validated]);

        try {
            $student = DB::transaction(function () use ($validated) {
                $user = User::create([
                    'name' => $this->studentDisplayName($validated),
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'role' => 'student',
                    'force_password_change' => true,
                ]);

                return Student::create([
                    'user_id' => $user->id,
                    'student_id' => $validated['student_id'],
                    'email' => $validated['email'],
                    'degree_id' => $validated['degree_id'],
                    'first_name' => $validated['first_name'],
                    'middle_name' => $validated['middle_name'] ?? null,
                    'last_name' => $validated['last_name'],
                    'address' => $validated['address'],
                    'contact_number' => $validated['contact_number'],
                ]);
            });

            Log::info('Student is added: '.$validated['student_id'], ['student_id' => $student->student_id, 'user_id' => $student->user_id]);
            $this->logActivity('created', 'Created student '.$student->student_id.'.', $student, $request->ip());

            // Redirect admin back to students list (stay in admin area) with success message
            return redirect()->route('students.index')->with('success', 'Student created successfully! They can now login and change their password.');
        } catch (\Exception $e) {
            Log::error('Failed to create student', ['error' => $e->getMessage(), 'payload' => $validated]);
            return back()->with('error', 'Failed to create student: '.$e->getMessage())->withInput();
        }
    }

    public function show(Student $student)
    {
        return view('students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $degrees = Degree::orderBy('code')->get();

        return view('students.edit', compact('student', 'degrees'));
    }

    public function update(Request $request, Student $student)
    {
        $validator = Validator::make(
            $request->all(),
            $this->studentRules($student),
            $this->studentMessages(),
            $this->studentAttributes()
        );

        if ($validator->fails()) {
            Log::warning('Student update validation failed', ['student_id' => $student->student_id, 'errors' => $validator->errors()->toArray()]);

            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        DB::transaction(function () use ($student, $validated): void {
            $student->update([
                'student_id' => $validated['student_id'],
                'email' => $validated['email'],
                'degree_id' => $validated['degree_id'],
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name' => $validated['last_name'],
                'address' => $validated['address'],
                'contact_number' => $validated['contact_number'],
            ]);

            $user = $student->user ?: new User();
            $user->name = $this->studentDisplayName($validated);
            $user->email = $validated['email'];
            $user->role = 'student';

            if (! empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
                $user->force_password_change = true;
            }

            $user->save();

            if (! $student->user_id) {
                $student->user_id = $user->id;
                $student->save();
            }
        });

        Log::info('Student updated: '.$student->student_id);
        $this->logActivity('updated', 'Updated student '.$student->student_id.'.', $student, $request->ip());

        return redirect()->route('students.index')->with('success', 'Student successfully updated!');
    }

    public function destroy(Request $request, Student $student)
    {
        Log::warning('Student deleted: '.$student->student_id);

        $this->logActivity('deleted', 'Deleted student '.$student->student_id.'.', $student, $request->ip());

        if ($student->user) {
            $student->user->delete();
        }

        $student->delete();

        return redirect()->route('students.index')->with('success', 'Student successfully deleted!');
    }

    public function studentCourses(Student $student)
    {
        $student->load('courses');

        if ($student->courses->isEmpty()) {
            return $student->first_name.' '.$student->last_name.' has no enrolled courses.';
        }

        $lines = $student->courses->map(function ($course) use ($student) {
            return $student->first_name.' '.$student->last_name.' is enrolled in: '.$course->title;
        })->implode("\n");

        return response($lines, 200)->header('Content-Type', 'text/plain');
    }

    // --- AJAX endpoints for jQuery CRUD ---
    public function ajaxIndex()
    {
        $students = Student::with('degree')->latest()->get();

        return response()->json(['students' => $students], 200);
    }

    public function ajaxShow(Student $student)
    {
        $student->load('degree');
        return response()->json(['student' => $student], 200);
    }

    public function ajaxStore(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            $this->studentRules(),
            $this->studentMessages(),
            $this->studentAttributes()
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        try {
            $student = DB::transaction(function () use ($validated) {
                $user = User::create([
                    'name' => $this->studentDisplayName($validated),
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'role' => 'student',
                    'force_password_change' => true,
                ]);

                return Student::create([
                    'user_id' => $user->id,
                    'student_id' => $validated['student_id'],
                    'email' => $validated['email'],
                    'degree_id' => $validated['degree_id'],
                    'first_name' => $validated['first_name'],
                    'middle_name' => $validated['middle_name'] ?? null,
                    'last_name' => $validated['last_name'],
                    'address' => $validated['address'],
                    'contact_number' => $validated['contact_number'],
                ]);
            });

            $this->logActivity('created', 'Created student '.$student->student_id.'.', $student, request()->ip());

            return response()->json(['student' => $student, 'message' => 'Student created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create student: '.$e->getMessage()], 500);
        }
    }

    public function ajaxUpdate(Request $request, Student $student)
    {
        $validator = Validator::make(
            $request->all(),
            $this->studentRules($student),
            $this->studentMessages(),
            $this->studentAttributes()
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        try {
            DB::transaction(function () use ($student, $validated) {
                $student->update([
                    'student_id' => $validated['student_id'],
                    'email' => $validated['email'],
                    'degree_id' => $validated['degree_id'],
                    'first_name' => $validated['first_name'],
                    'middle_name' => $validated['middle_name'] ?? null,
                    'last_name' => $validated['last_name'],
                    'address' => $validated['address'],
                    'contact_number' => $validated['contact_number'],
                ]);

                $user = $student->user ?: new User();
                $user->name = $this->studentDisplayName($validated);
                $user->email = $validated['email'];
                $user->role = 'student';

                if (! empty($validated['password'])) {
                    $user->password = Hash::make($validated['password']);
                    $user->force_password_change = true;
                }

                $user->save();

                if (! $student->user_id) {
                    $student->user_id = $user->id;
                    $student->save();
                }
            });

            $this->logActivity('updated', 'Updated student '.$student->student_id.'.', $student, request()->ip());

            return response()->json(['student' => $student, 'message' => 'Student updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update student: '.$e->getMessage()], 500);
        }
    }

    public function ajaxDestroy(Request $request, Student $student)
    {
        try {
            if ($student->user) {
                $student->user->delete();
            }

            $student->delete();

            $this->logActivity('deleted', 'Deleted student '.$student->student_id.'.', $student, $request->ip());

            return response()->json(['message' => 'Student deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete student: '.$e->getMessage()], 500);
        }
    }
}
