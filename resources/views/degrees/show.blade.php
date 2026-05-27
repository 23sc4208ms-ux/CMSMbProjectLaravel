<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Degree</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 24px; min-height: 100vh; display: flex; flex-direction: column; background: linear-gradient(135deg, #eef7ff 0%, #dceeff 55%, #c8e2ff 100%); color: #17324d; }
        .container { width: 100%; max-width: 760px; margin: 0 auto; background: rgba(255, 255, 255, 0.9); border: 1px solid #b9d7f5; border-radius: 16px; padding: 22px; box-shadow: 0 12px 30px rgba(52, 103, 160, 0.16); backdrop-filter: blur(8px); }
        .nav { display: flex; justify-content: flex-end; gap: 10px; margin-bottom: 16px; flex-wrap: wrap; }
        .nav a { text-decoration: none; border: 1px solid #b8d3ee; color: #1f4f86; background: #f5faff; padding: 8px 12px; border-radius: 999px; font-weight: 600; }
        .nav a.active { background: linear-gradient(180deg, #4f8fd1 0%, #2f6fae 100%); color: #fff; border-color: #2f6fae; box-shadow: 0 6px 16px rgba(47, 111, 174, 0.28); }
        .card { border: 1px solid #c6dbf2; border-radius: 10px; overflow: hidden; }
        .row { display: grid; grid-template-columns: 180px 1fr; border-bottom: 1px solid #c6dbf2; }
        .row:last-child { border-bottom: none; }
        .label { background: #eaf4ff; padding: 12px; font-weight: 700; color: #1f4f86; }
        .value { padding: 12px; }
        .actions { margin-top: 16px; display: flex; gap: 8px; }
        .btn { display: inline-block; padding: 9px 14px; text-decoration: none; border: 1px solid #2f6fae; background: linear-gradient(180deg, #4f8fd1 0%, #2f6fae 100%); color: #fff; border-radius: 999px; cursor: pointer; font-weight: 600; }
        .btn.secondary { background: #f5faff; color: #1f4f86; border-color: #b8d3ee; }
        .btn.danger { background: linear-gradient(180deg, #5d8fc4 0%, #2b5d91 100%); border-color: #2b5d91; }
        .alert { background: #eaf4ff; border: 1px solid #b9d7f5; color: #1f4f86; padding: 10px; margin-bottom: 14px; }
        .footer { width: 100%; max-width: 760px; margin: auto auto 0; padding-top: 12px; text-align: center; color: #5f7590; font-size: 14px; }

        @media (max-width: 640px) {
            .row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <div class="container">
        <nav class="nav">
            <a href="{{ route('home', [], false) }}">Home</a>
            <a href="{{ route('students.index', [], false) }}">Students</a>
            <a href="{{ route('degrees.index', [], false) }}" class="active">Degrees</a>
            <a href="{{ route('dashboard', [], false) }}">Dashboard</a>
            <a href="{{ route('about-us', [], false) }}">About Us</a>
            <a href="{{ route('logout', [], false) }}">Logout</a>
        </nav>

        <h1>View Degree</h1>

        @if (session('success'))
            <div class="alert">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="row">
                <div class="label">Degree Name</div>
                <div class="value">{{ $degree->code }}</div>
            </div>
        </div>

        <div class="actions">
            <form action="{{ route('degrees.destroy', [$degree], false) }}" method="POST" onsubmit="return confirm('Delete this degree?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn danger">Delete</button>
            </form>
            <a href="{{ route('degrees.index') }}" class="btn secondary">Back</a>
        </div>
    </div>

    <footer class="footer">
        Copyright &copy; {{ date('Y') }} Student Management System. All rights reserved.
    </footer>
</body>
</html>
