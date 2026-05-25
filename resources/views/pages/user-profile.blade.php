@if (!$student)
Student not found.
@else
{{ $student->first_name }} - {{ $student->profile?->bio ?? 'No bio found.' }}
@endif
