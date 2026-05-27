<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 24px; min-height: 100vh; display: flex; flex-direction: column; background: linear-gradient(135deg, #eef7ff 0%, #dceeff 55%, #c8e2ff 100%); color: #17324d; }
        .container { width: 100%; max-width: 1000px; margin: 0 auto; background: rgba(255, 255, 255, 0.9); border: 1px solid #b9d7f5; border-radius: 16px; padding: 22px; box-shadow: 0 12px 30px rgba(52, 103, 160, 0.16); backdrop-filter: blur(8px); }
        table { border-collapse: collapse; width: 100%; margin-top: 16px; }
        th, td { border: 1px solid #c6dbf2; padding: 10px; text-align: left; }
        th { background: linear-gradient(180deg, #1f4f86 0%, #163c68 100%); color: #f7fbff; }
        .actions { display: flex; gap: 8px; align-items: center; }
        .btn { display: inline-block; padding: 8px 12px; text-decoration: none; border: 1px solid #2f6fae; background: linear-gradient(180deg, #4f8fd1 0%, #2f6fae 100%); color: #fff; border-radius: 999px; cursor: pointer; font-weight: 600; }
        .btn.secondary { background: #f5faff; color: #1f4f86; border-color: #b8d3ee; }
        .alert { background: #eaf4ff; border: 1px solid #b9d7f5; color: #1f4f86; padding: 10px; margin-bottom: 14px; }
        .muted { color: #5f7590; }
        form { margin: 0; }
        .nav { display: flex; justify-content: flex-end; gap: 10px; margin-bottom: 16px; }
        .nav a { text-decoration: none; border: 1px solid #b8d3ee; color: #1f4f86; background: #f5faff; padding: 8px 12px; border-radius: 999px; font-weight: 600; }
        .nav a.active { background: linear-gradient(180deg, #4f8fd1 0%, #2f6fae 100%); color: #fff; border-color: #2f6fae; box-shadow: 0 6px 16px rgba(47, 111, 174, 0.28); }
        .footer { width: 100%; max-width: 1000px; margin: auto auto 0; padding-top: 12px; text-align: center; color: #5f7590; font-size: 14px; }
    </style>
</head>
<body>

    <div class="container">
        <nav class="nav">
            <a href="{{ route('home', [], false) }}">Home</a>
            <a href="{{ route('students.index', [], false) }}" class="active">Students</a>
            <a href="{{ route('degrees.index', [], false) }}">Degrees</a>
            <a href="{{ route('activity-logs.index', [], false) }}">Activity Logs</a>
            <a href="{{ route('dashboard', [], false) }}">Dashboard</a>
            <a href="{{ route('about-us', [], false) }}">About Us</a>
        </nav>

        <h1>Student Management</h1>

        @if (session('success'))
            <div class="alert">{{ session('success') }}</div>
        @endif

        <a href="{{ route('students.create', [], false) }}" class="btn">Add New Student</a>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Degree Title</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($students as $student)
                    <tr>
                        <td>{{ $students->firstItem() + $loop->index }}</td>
                        <td>{{ $student->student_id }}</td>
                        <td>{{ $student->last_name }}, {{ $student->first_name }} {{ $student->middle_name }}</td>
                        <td>{{ $student->degree_title }}</td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('students.show', [$student], false) }}" class="btn secondary">View</a>
                                <a href="{{ route('students.edit', [$student], false) }}" class="btn secondary">Edit</a>

                                <form action="{{ route('students.destroy', [$student], false) }}" method="POST" onsubmit="return confirm('Delete this student?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="muted">No students found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 16px;">
            {{ $students->links() }}
        </div>

    </div>

    <footer class="footer">
        Copyright &copy; {{ date('Y') }} Student Management System. All rights reserved.
    </footer>
</body>
</html>
