# Admin Login Credentials Guide

## 🔐 Understanding Admin Passwords

### Important: Passwords are HASHED
- Laravel stores passwords using **bcrypt** hashing
- You **CANNOT** see the actual password in the database
- The password field contains a hash like: `$2y$10$...` (60 characters)
- This is a security feature - passwords are never stored in plain text

---

## 📋 How to Find/Reset Admin Credentials

### Option 1: Use the Password Reset Script (RECOMMENDED)

**Run the script**:
```powershell
cd C:\Users\MIKENZY\Documents\Apps\roomunite
php reset_admin_password.php
```

**This script will**:
1. Show all existing admin accounts
2. Allow you to reset passwords
3. Allow you to create new admin accounts
4. Properly hash passwords using Laravel's Hash::make()

---

### Option 2: Check Database Directly

**View admin accounts**:
```sql
-- Run in MySQL/phpMyAdmin
SELECT id, username, email, status FROM admin;
```

**Check admin with roles**:
```sql
SELECT 
    a.id,
    a.username,
    a.email,
    a.status,
    ra.role_id
FROM admin a
LEFT JOIN role_admin ra ON a.id = ra.admin_id;
```

**See SQL file**: `check_admin_accounts.sql` for more queries

---

### Option 3: Reset Password via Artisan Tinker

```powershell
php artisan tinker
```

Then in tinker:
```php
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

// Find admin
$admin = Admin::find(1); // Replace 1 with admin ID

// Reset password
$admin->password = Hash::make('your_new_password');
$admin->save();

// Verify
echo "Password reset for: " . $admin->username;
```

---

### Option 4: Create New Admin via Artisan Tinker

```powershell
php artisan tinker
```

```php
use App\Models\Admin;
use App\Models\RoleAdmin;
use Illuminate\Support\Facades\Hash;

// Create admin
$admin = new Admin();
$admin->username = 'admin';
$admin->email = 'admin@example.com';
$admin->password = Hash::make('password123');
$admin->status = 'Active';
$admin->save();

// Assign role
RoleAdmin::insert([
    'admin_id' => $admin->id,
    'role_id' => 1  // Usually 1 is Super Admin
]);

echo "Admin created: ID=" . $admin->id;
```

---

## 🔍 Default Credentials (from Seeder)

The database seeder creates a default admin:
- **Username**: `admin`
- **Email**: `admin@techvill.net`
- **Password**: `123456` (but stored as hash)

**Note**: If the database was reset or modified, these might not work.

---

## 🛠️ Quick Password Reset (One-Liner)

**Reset password for admin ID 1**:
```powershell
php artisan tinker --execute="App\Models\Admin::find(1)->update(['password' => Hash::make('newpassword123')]);"
```

**Reset password by email**:
```powershell
php artisan tinker --execute="App\Models\Admin::where('email', 'admin@techvill.net')->update(['password' => Hash::make('newpassword123')]);"
```

---

## 📊 Database Structure

### Admin Table
- `id` - Primary key
- `username` - Unique username
- `email` - Unique email
- `password` - Bcrypt hash (60 chars)
- `status` - 'Active' or 'Inactive'
- `profile_image` - Optional
- `created_at` - Timestamp
- `updated_at` - Timestamp

### Role Admin Table (Links admins to roles)
- `admin_id` - Foreign key to admin.id
- `role_id` - Foreign key to roles.id

---

## ✅ Verification Steps

1. **Check if admin exists**:
   ```sql
   SELECT * FROM admin;
   ```

2. **Check admin status**:
   ```sql
   SELECT id, username, email, status FROM admin WHERE status = 'Active';
   ```

3. **Check admin roles**:
   ```sql
   SELECT a.username, ra.role_id 
   FROM admin a 
   JOIN role_admin ra ON a.id = ra.admin_id;
   ```

4. **Test login**:
   - Go to: `http://roomunite.local/admin/login`
   - Try username/email and password

---

## 🚨 Common Issues

### Issue 1: "Invalid credentials"
**Cause**: Password hash doesn't match
**Solution**: Reset password using one of the methods above

### Issue 2: "Account inactive"
**Cause**: Admin status is 'Inactive'
**Solution**: 
```sql
UPDATE admin SET status = 'Active' WHERE id = 1;
```

### Issue 3: "No admin account found"
**Cause**: Database was reset or admin table is empty
**Solution**: Create new admin using `reset_admin_password.php` script

### Issue 4: "Permission denied"
**Cause**: Admin doesn't have required role/permissions
**Solution**: Assign role:
```sql
INSERT INTO role_admin (admin_id, role_id) VALUES (1, 1);
```

---

## 📝 Quick Reference

**Login URL**: `http://roomunite.local/admin/login`

**Password Reset Script**: `reset_admin_password.php`

**SQL Queries**: `check_admin_accounts.sql`

**Default Seeder Credentials**:
- Username: `admin`
- Email: `admin@techvill.net`
- Password: `123456` (if seeder was run)

---

## 🔒 Security Note

**NEVER** store passwords in plain text. Always use Laravel's `Hash::make()` or `bcrypt()` function.

The `reset_admin_password.php` script handles this automatically.




