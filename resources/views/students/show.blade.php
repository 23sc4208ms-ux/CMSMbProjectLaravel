<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 24px; min-height: 100vh; display: flex; flex-direction: column; background: linear-gradient(135deg, #ffe4ef, #ffd1e3); color: #4a2130; }
        .container { width: 100%; max-width: 760px; margin: 0 auto; background: #fff6fa; border: 1px solid #f3b2ca; border-radius: 12px; padding: 22px; box-shadow: 0 8px 22px rgba(184, 82, 130, 0.18); }
        .card { border: 1px solid #efb7ce; border-radius: 10px; padding: 18px; background: #fff; }
        .row { margin-bottom: 10px; }
        .label { font-weight: 700; display: inline-block; width: 150px; }
        .btn { display: inline-block; margin-top: 14px; padding: 9px 14px; text-decoration: none; border: 1px solid #d83f86; background: #d83f86; color: #fff; border-radius: 999px; font-weight: 600; }
        .nav { display: flex; justify-content: flex-end; gap: 10px; margin-bottom: 16px; }
        .nav a { text-decoration: none; border: 1px solid #dd7fa5; color: #9b2e64; background: #ffeaf3; padding: 8px 12px; border-radius: 999px; font-weight: 600; }
        .nav a.active { background: #d83f86; color: #fff; border-color: #d83f86; }
        .footer { width: 100%; max-width: 760px; margin: auto auto 0; padding-top: 12px; text-align: center; color: #8f6074; font-size: 14px; }
    </style>
</head>
<body>

    <div class="container">
        <nav class="nav">
            <a href="{{ route('home', [], false) }}">Home</a>
            <a href="{{ route('students.index', [], false) }}" class="active">Students</a>
            <a href="{{ route('degrees.index', [], false) }}">Degrees</a>
            <a href="{{ route('dashboard', [], false) }}">Dashboard</a>
            <a href="{{ route('about-us', [], false) }}">About Us</a>
        </nav>

        <h1>Student Details</h1>

        <div class="card">
            <div class="row"><span class="label">Student ID:</span> {{ $student->student_id }}</div>
            <div class="row">
                <span class="label">Degree:</span>
                {{ $student->degree ? $student->degree->code : 'N/A' }}
            </div>
            <div class="row">
                <span class="label">Degree Title:</span>
                {{ $student->degree_title }}
            </div>
            <div class="row"><span class="label">First Name:</span> {{ $student->first_name }}</div>
            <div class="row"><span class="label">Middle Name:</span> {{ $student->middle_name ?: 'N/A' }}</div>
            <div class="row"><span class="label">Last Name:</span> {{ $student->last_name }}</div>
            <div class="row"><span class="label">Address:</span> {{ $student->address }}</div>
            <div class="row"><span class="label">Contact Number:</span> {{ $student->contact_number }}</div>
        </div>

        <a href="{{ route('students.index', [], false) }}" class="btn">Back to List</a>

    </div>

    <footer class="footer">
        Copyright &copy; {{ date('Y') }} Student Management System. All rights reserved.
    </footer>
</body>
</html>
