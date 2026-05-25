@if($enrollments->isEmpty())
No student-course records found yet in the pivot table.
@else
@foreach ($enrollments as $student)
{{ $student->first_name }} {{ $student->last_name }}:
@foreach ($student->courses as $course)
{{ $course->title }} - {{ $course->code ?? 'N/A' }}
@endforeach
@endforeach
@endif
