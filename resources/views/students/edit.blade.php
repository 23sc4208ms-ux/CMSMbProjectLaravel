<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 24px; min-height: 100vh; display: flex; flex-direction: column; background: linear-gradient(135deg, #eef7ff 0%, #dceeff 55%, #c8e2ff 100%); color: #17324d; }
        .container { width: 100%; max-width: 760px; margin: 0 auto; background: rgba(255, 255, 255, 0.9); border: 1px solid #b9d7f5; border-radius: 16px; padding: 22px; box-shadow: 0 12px 30px rgba(52, 103, 160, 0.16); backdrop-filter: blur(8px); }
        .row { margin-bottom: 14px; }
        label { display: block; margin-bottom: 6px; font-weight: 600; }
        input, select { width: 100%; padding: 8px 10px; border: 1px solid #b8d3ee; border-radius: 8px; background: #fff; box-sizing: border-box; }
        .btn { display: inline-block; padding: 9px 14px; text-decoration: none; border: 1px solid #2f6fae; background: linear-gradient(180deg, #4f8fd1 0%, #2f6fae 100%); color: #fff; border-radius: 999px; cursor: pointer; font-weight: 600; }
        .btn.secondary { background: #f5faff; color: #1f4f86; border-color: #b8d3ee; }
        .error { color: #d13b3b; font-size: 14px; margin-top: 4px; }
        .actions { display: flex; gap: 8px; }
        .nav { display: flex; justify-content: flex-end; gap: 10px; margin-bottom: 16px; }
        .nav a { text-decoration: none; border: 1px solid #b8d3ee; color: #1f4f86; background: #f5faff; padding: 8px 12px; border-radius: 999px; font-weight: 600; }
        .nav a.active { background: linear-gradient(180deg, #4f8fd1 0%, #2f6fae 100%); color: #fff; border-color: #2f6fae; box-shadow: 0 6px 16px rgba(47, 111, 174, 0.28); }
        .footer { width: 100%; max-width: 760px; margin: auto auto 0; padding-top: 12px; text-align: center; color: #5f7590; font-size: 14px; }
    </style>
</head>
<body>

    <div class="container">
        <nav class="nav">
            <a href="{{ route('home', [], false) }}">Home</a>
            <a href="{{ route('students.index', [], false) }}" class="active">Students</a>
            <a href="{{ route('degrees.index', [], false) }}">Degrees</a>
            <a href="{{ route('dashboard', [], false) }}">Dashboard</a>
            <a href="{{ route('about-us', [], false) }}">About Us</a>            <a href="{{ route('logout', [], false) }}">Logout</a>        </nav>

        <h1>Edit Student</h1>

        @include('students._form', [
            'action' => route('students.update', [$student], false),
            'method' => 'PUT',
            'submitLabel' => 'Update Student',
            'student' => $student,
        ])

    </div>

    <footer class="footer">
        Copyright &copy; {{ date('Y') }} Student Management System. All rights reserved.
    </footer>
</body>
</html>
