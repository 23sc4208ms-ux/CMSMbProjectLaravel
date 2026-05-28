<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teachers List</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #ADD8E6 0%, #87CEEB 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .header h1 {
            color: #333;
        }

        .header a {
            padding: 10px 20px;
            background: #6BB6D6;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .header a:hover {
            background: #87CEEB;
        }

        .logout-btn {
            background: #e74c3c;
        }

        .logout-btn:hover {
            background: #c0392b;
        }

        .table-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th {
            background: #6BB6D6;
            color: white;
            padding: 12px;
            text-align: left;
        }

        table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        table tr:hover {
            background: #f5f5f5;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .action-buttons a {
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 3px;
            font-size: 12px;
        }

        .view-btn {
            background: #3498db;
            color: white;
        }

        .edit-btn {
            background: #f39c12;
            color: white;
        }

        .delete-btn {
            background: #e74c3c;
            color: white;
        }

        .message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }

        .empty-message {
            text-align: center;
            color: #666;
            padding: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Teachers List</h1>
            <div>
                <a href="{{ route('teacher.create') }}">➕ Add Teacher</a>
                <a href="{{ route('logout') }}" class="logout-btn">Logout</a>
            </div>
        </div>

        @if ($message = Session::get('success'))
            <div class="message">
                {{ $message }}
            </div>
        @endif

        <div class="table-container">
            @if($teachers->count())
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($teachers as $teacher)
                            <tr>
                                <td>{{ $teacher->id }}</td>
                                <td>{{ $teacher->name }}</td>
                                <td>{{ $teacher->email }}</td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('teacher.show', $teacher->id) }}" class="view-btn">View</a>
                                        <form method="POST" action="{{ route('teacher.destroy', $teacher->id) }}" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="delete-btn" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-message">
                    <p>No teachers found. <a href="{{ route('teacher.create') }}">Add a new teacher</a></p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
