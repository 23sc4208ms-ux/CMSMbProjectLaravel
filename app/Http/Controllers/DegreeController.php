<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Degree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class DegreeController extends Controller
{
    private function logActivity(string $action, string $description, ?Degree $degree = null, ?string $ipAddress = null): void
    {
        try {
            ActivityLog::create([
                'action' => $action,
                'subject_type' => $degree ? Degree::class : null,
                'subject_id' => $degree?->id,
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
        $degrees = Degree::orderByDesc('id')->get();

        return view('degrees.index', compact('degrees'));
    }

    // Simple JSON endpoint for AJAX clients
    public function ajaxIndex()
    {
        $degrees = Degree::orderBy('code')->get();
        return response()->json($degrees, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|min:2|max:120|unique:degrees,code',
            'name' => 'nullable|string|max:120',
        ]);

        $degree = Degree::create([
            'code' => trim($validated['code']),
            'name' => isset($validated['name']) ? trim($validated['name']) : null,
        ]);

        $this->logActivity('created', 'Created degree '.$degree->code.'.', $degree, $request->ip());

        return redirect()->route('degrees.index')->with('success', 'Degree added successfully.');
    }

    public function edit(Degree $degree)
    {
        return view('degrees.edit', compact('degree'));
    }

    public function show(Degree $degree)
    {
        $degree->loadCount('students');

        return view('degrees.show', compact('degree'));
    }

    public function update(Request $request, Degree $degree)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'min:2',
                'max:120',
                Rule::unique('degrees', 'code')->ignore($degree->id),
            ],
        ]);

        $degree->update([
            'code' => trim($validated['code']),
        ]);

        $this->logActivity('updated', 'Updated degree '.$degree->code.'.', $degree, $request->ip());

        return redirect()->route('degrees.show', $degree)->with('success', 'Degree updated successfully.');
    }

    public function destroy(Request $request, Degree $degree)
    {
        if ($degree->students()->exists()) {
            return redirect()->route('degrees.index')->with('error', 'Cannot delete degree with assigned students.');
        }

        $this->logActivity('deleted', 'Deleted degree '.$degree->code.'.', $degree, $request->ip());

        $degree->delete();

        return redirect()->route('degrees.index')->with('success', 'Degree deleted successfully.');
    }
}
