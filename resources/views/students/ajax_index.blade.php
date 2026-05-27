<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students - AJAX Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/modern-css-reset/dist/reset.min.css">
    <style>
        body { font-family: Arial, sans-serif; padding: 24px; }
        .container { max-width: 1000px; margin: 0 auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #f5f5f5; }
        .btn { padding: 8px 12px; background: #007bff; color: #fff; border-radius: 4px; text-decoration: none; display: inline-block; }
        .btn.danger { background: #dc3545; }
        .modal { display: none; position: fixed; left: 0; top: 0; right:0; bottom:0; background: rgba(0,0,0,0.4); align-items: center; justify-content:center; }
        .modal .panel { background: #fff; padding: 16px; border-radius: 8px; width: 600px; max-width: 95%; }
        .form-row { margin-bottom: 8px; }
        label { display:block; margin-bottom:4px; }
        input, select { width:100%; padding:8px; box-sizing:border-box; }
        .flex { display:flex; gap:8px; }
        .message { margin-top: 12px; padding: 10px; border-radius: 6px; }
        .message.success { background: #e6ffed; border: 1px solid #b8f5c8; color: #155724; }
        .message.error { background: #ffe6e6; border: 1px solid #f5b8b8; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Student Management (AJAX)</h1>
        <div class="controls">
            <button id="btnAdd" class="btn">Add Student</button>
            <a href="{{ route('students.index', [], false) }}" class="btn">Non-AJAX List</a>
        </div>

        <div id="messages"></div>

        <table id="studentsTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Degree</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <div id="modal" class="modal">
        <div class="panel">
            <h3 id="modalTitle">Add Student</h3>
            <form id="studentForm">
                <input type="hidden" name="id" id="student_id_field">
                <div class="form-row">
                    <label>Student ID</label>
                    <input type="text" name="student_id" id="student_id" required>
                </div>
                <div class="form-row">
                    <label>First Name</label>
                    <input type="text" name="first_name" id="first_name" required>
                </div>
                <div class="form-row">
                    <label>Middle Name</label>
                    <input type="text" name="middle_name" id="middle_name">
                </div>
                <div class="form-row">
                    <label>Last Name</label>
                    <input type="text" name="last_name" id="last_name" required>
                </div>
                <div class="form-row">
                    <label>Email</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div class="form-row">
                    <label>Degree</label>
                    <select name="degree_id" id="degree_id" required>
                        <option value="">Loading...</option>
                    </select>
                </div>
                <div class="form-row">
                    <label>Address</label>
                    <input type="text" name="address" id="address">
                </div>
                <div class="form-row">
                    <label>Contact Number</label>
                    <input type="text" name="contact_number" id="contact_number">
                </div>
                <div class="form-row">
                    <label>Password</label>
                    <input type="password" name="password" id="password">
                </div>
                <div class="flex">
                    <button type="submit" class="btn">Save</button>
                    <button type="button" id="btnCancel" class="btn secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/js/app.js"></script>
</body>
</html>
