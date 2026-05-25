# Student Creation & Login Flow - Complete Guide

## How It Works Now

When you create a new student through the admin panel, the system automatically:

1. **Creates a User Account** with login credentials
2. **Forces Password Change** on first login
3. **Redirects to Dashboard** after password change

---

## Step-by-Step: Creating a New Student

### Step 1: Go to Students Management
- Log in as Admin
- Navigate to **Students** menu
- Click **Create Student** button

### Step 2: Fill in Student Information

**Student Details (Always Required):**
- Student ID: `23-SC-4209` (unique identifier)
- First Name: `Juan`
- Middle Name: `Carlos`
- Last Name: `Dela Cruz`
- Address: `123 Main Street, Manila`
- Contact Number: `09123456789`
- Email: `juan.delacruz@example.com`
- Degree: Select from dropdown (e.g., BSIT, BSCS)

**Login Credentials (Always Required):**
- Login Name: `jdelacruz` (display name for login)
- Temporary Password: `TempPass123!` (temporary, must be changed on first login)

### Step 3: Submit Form
- Click **Save** button
- You'll be redirected to Students list with success message: "Student created successfully!"

---

## Step-by-Step: Student First Login

### Step 1: Student Receives Credentials
Student receives:
- Email: `juan.delacruz@example.com`
- Temporary Password: `TempPass123!`
- Login URL: `http://yoursite/login`

### Step 2: Student Logs In
1. Go to login page
2. Enter email and temporary password
3. Click **Login**

### Step 3: Forced Password Change
- Student is automatically redirected to `/change-password`
- Page displays: "You must change your password before continuing"

### Step 4: Change Password
Student fills in:
- **Current Password:** `TempPass123!` (the temporary password)
- **New Password:** `MySecurePass456!` (their own password)
- **Confirm Password:** `MySecurePass456!` (must match)

### Step 5: Access Dashboard
After submitting new password:
- System saves the new password
- Student is automatically redirected to `/dashboard/student`
- Student can now use the new password for future logins

---

## Important Details

### Password Requirements
- Minimum 6 characters
- Must not be the same as temporary password
- Case-sensitive

### First-Time Login Behavior
- ✅ Student logs in with temporary password
- ✅ Forced redirect to change-password page
- ✅ After password change → access dashboard
- ✅ Cannot access dashboard without changing password

### Future Logins
After changing password, student can:
- Login with email and new password
- Go directly to dashboard (no password change required)
- Access all student features

---

## Account Security Features

### Failed Login Protection
- 3 failed login attempts = 1-minute account lockout
- After lockout, account automatically unlocks
- Failed attempts reset on successful login

### Password History
- System tracks when password was last changed (`password_changed_at`)
- Admin can see this in user records

### Account Status
- `force_password_change` flag: Set to `true` on creation, becomes `false` after change
- `locked_until`: Shows when account will unlock after failed attempts

---

## Testing the Flow

### Use These Test Accounts

**Baseline Student:**
```
Email: student@example.com
Password: StudentPass123!
Student ID: 23-SC-4208
```

**Create New Test Student:**
```
Student ID: TST-NEW
Email: test.student@example.com
Login Name: testuser
Temporary Password: Test123!
Degree: BSIT
First Name: Test
Last Name: User
Address: 123 Test St
Contact: 09123456789
```

Then:
1. Login with `test.student@example.com` and `Test123!`
2. You'll be sent to change password page
3. Enter current: `Test123!`, new: `NewTest456!`
4. Confirm you're redirected to student dashboard

---

## Troubleshooting

### Issue: "Invalid email or password"
- ✓ Check email spelling
- ✓ Check password spelling (case-sensitive)
- ✓ Wait 1 minute if account is locked

### Issue: Redirected to login after changing password
- ✓ Make sure you're using the NEW password
- ✓ Check that password change was saved
- ✓ Browser might need to refresh session

### Issue: Can't create student - "Email already in use"
- ✓ Email must be unique across users and students tables
- ✓ Check if email was used for admin/teacher
- ✓ Use different email

### Issue: Password field is optional when editing student
- ✓ This is correct! Edit form allows optional password changes
- ✓ Leave blank to keep current password
- ✓ Fill in to reset student's password (they'll be forced to change again)

---

## Database Details

### users table columns related to login
```sql
- id: User ID
- name: Display name (Login Name)
- email: Login email
- password: Hashed password (Argon2ID)
- role: 'admin', 'teacher', or 'student'
- force_password_change: true/false
- password_changed_at: When password was last changed
- failed_login_attempts: Count of failed logins
- locked_until: Timestamp of when account unlocks
```

### students table columns
```sql
- id: Student record ID
- user_id: FOREIGN KEY → users.id (the link!)
- student_id: Unique student identifier
- email: Student email
- degree_id: FOREIGN KEY → degrees.id
- first_name, middle_name, last_name: Student name
- address, contact_number: Contact info
```

---

## Summary

✅ **Admin creates student** with temporary password  
✅ **Student receives credentials** (email + temp password)  
✅ **Student logs in** with email and temp password  
✅ **System forces password change** on first login  
✅ **Student changes password** to new password  
✅ **Student accesses dashboard** with new password  
✅ **Future logins** use email + new password  

**This is now the complete, tested, and working flow!**
