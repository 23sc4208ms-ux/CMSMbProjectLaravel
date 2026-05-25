@php
    $student = $student ?? null;
    $method = $method ?? 'POST';
    $degrees = $degrees ?? collect();
@endphp

<form action="{{ $action }}" method="POST" novalidate @if ($method !== 'POST') onsubmit="return confirm('Save changes to this student?');" @endif>
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <style>
        .row { margin-bottom: 14px; }
        label { display: block; margin-bottom: 6px; font-weight: 600; }
        input, select { width: 100%; padding: 8px 10px; border: 1px solid #b8d3ee; border-radius: 8px; background: #fff; box-sizing: border-box; }
        .btn { display: inline-block; padding: 9px 14px; text-decoration: none; border: 1px solid #2f6fae; background: linear-gradient(180deg, #4f8fd1 0%, #2f6fae 100%); color: #fff; border-radius: 999px; cursor: pointer; font-weight: 600; }
        .btn.secondary { background: #f5faff; color: #1f4f86; border-color: #b8d3ee; }
        .error { color: #d13b3b; font-size: 14px; margin-top: 4px; }
        .actions { display: flex; gap: 8px; }
    </style>

    <div class="row">
        <label for="student_id">Student ID</label>
        <input type="text" id="student_id" name="student_id" value="{{ old('student_id', optional($student)->student_id) }}">
        @error('student_id') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div class="row">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email', optional($student)->email) }}">
        @error('email') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div class="row">
        <label for="first_name">First Name</label>
        <input type="text" id="first_name" name="first_name" value="{{ old('first_name', optional($student)->first_name) }}">
        @error('first_name') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div class="row">
        <label for="middle_name">Middle Name</label>
        <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name', optional($student)->middle_name) }}" required>
        @error('middle_name') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div class="row">
        <label for="last_name">Last Name</label>
        <input type="text" id="last_name" name="last_name" value="{{ old('last_name', optional($student)->last_name) }}">
        @error('last_name') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div class="row">
        <label for="address">Address</label>
        <input type="text" id="address" name="address" value="{{ old('address', optional($student)->address) }}">
        @error('address') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div class="row">
        <label for="contact_number">Contact Number</label>
        <input type="text" id="contact_number" name="contact_number" value="{{ old('contact_number', optional($student)->contact_number) }}" inputmode="numeric">
        @error('contact_number') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div class="row">
        <label for="degree_id">Degree</label>
        <select id="degree_id" name="degree_id" @disabled($degrees->isEmpty())>
            <option value="">Select Degree</option>
            @foreach ($degrees as $degree)
                <option value="{{ $degree->id }}" @selected(old('degree_id', optional($student)->degree_id) == $degree->id)>
                    {{ $degree->code }}
                </option>
            @endforeach
        </select>
        @if ($degrees->isEmpty())
            <div class="error">No degree records found in the database. Please add degrees first.</div>
        @endif
        @error('degree_id') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div class="row">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="{{ old('username', $student?->user?->name) }}" @if(! $student) required @endif>
        <small style="display:block; margin-top:4px; color:#5f7590;">Display name for the student account.</small>
        @error('username') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div class="row">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" @if($student) placeholder="Leave blank to keep current password" @else required @endif>
        <small style="display:block; margin-top:4px; color:#5f7590;">Initial password for student login.</small>
        @error('password') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div class="actions">
        <button type="submit" class="btn" formnovalidate>{{ $submitLabel }}</button>
        <a href="{{ route('students.index') }}" class="btn secondary">Back</a>
    </div>
</form>
