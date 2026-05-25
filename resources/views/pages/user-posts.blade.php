@if (!$student)
Student not found.
@elseif($student->posts->isEmpty())
No posts found for {{ $student->first_name }}.
@else
Posts by {{ $student->first_name }} {{ $student->last_name }}:
@foreach ($student->posts as $post)
{{ $post->title }} - {{ $post->content ?? 'No content.' }}
@endforeach
@endif
