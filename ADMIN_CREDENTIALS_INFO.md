# Admin Login Credentials - Current Status

## ✅ Admin Account Found in Database

### Current Admin Account:
- **ID**: 1
- **Username**: `admin`
- **Email**: `admin@techvill.net`
- **Status**: Active ✅
- **Role**: 1 (Super Admin)

---

## 🔐 Password Information

### Important Facts:
1. **Passwords are HASHED** - Laravel uses bcrypt encryption
2. **You CANNOT see the actual password** in the database
3. **The password field** contains a hash like: `$2y$10$...` (60 characters)
4. **This is normal and secure** - passwords are never stored in plain text

---

## 🚨 Why You Can't Login

Possible reasons:
1. **Password was changed** - Someone reset it
2. **Database was updated** - New password hash was set
3. **Wrong credentials** - Using incorrect email/username
4. **Account is inactive** - Status changed to 'Inactive' (but yours is Active ✅)

---

## ✅ Solution: Reset the Password

### Option 1: Quick Reset (Recommended)

**Run this command**:
```powershell
php quick_reset_admin.php
```

This will reset the password to: `admin123`

**Then login with**:
- Email: `admin@techvill.net`
- Password: `admin123`

---

### Option 2: Interactive Reset

**Run this command**:
```powershell
php reset_admin_password.php
```

**Choose option 1** to reset password for admin ID 1, then enter your new password.

---

### Option 3: Reset via Artisan Tinker

```powershell
php artisan tinker
```

Then:
```php
$admin = App\Models\Admin::find(1);
$admin->password = Hash::make('your_new_password');
$admin->save();
```

---

### Option 4: Direct SQL (NOT RECOMMENDED)

**⚠️ WARNING**: This uses MD5 which Laravel doesn't use. Use PHP script instead!

```sql
-- This WON'T work with Laravel's bcrypt!
UPDATE admin SET password = MD5('password123') WHERE id = 1;
```

**Use the PHP script instead** - it uses proper bcrypt hashing.

---

## 📋 Default Credentials (from Seeder)

The database seeder creates:
- **Username**: `admin`
- **Email**: `admin@techvill.net`
- **Password**: `123456` (but stored as bcrypt hash)

**If database was reset**, these might work. Otherwise, reset using the script.

---

## 🧪 Test Login

After resetting password:

1. **Go to**: `http://roomunite.local/admin/login`
2. **Enter**:
   - Email: `admin@techvill.net`
   - Password: (the password you set)
3. **Click Login**

---

## 🔍 Verify Admin Account Status

**Check in database**:
```sql
SELECT id, username, email, status FROM admin WHERE id = 1;
```

**Should show**:
- Status: `Active` ✅
- Email: `admin@techvill.net`
- Username: `admin`

---

## 📝 Quick Commands

**View all admins**:
```powershell
php artisan tinker --execute="App\Models\Admin::all(['id', 'username', 'email', 'status']);"
```

**Reset password**:
```powershell
php quick_reset_admin.php
```

**Check roles**:
```sql
SELECT a.username, ra.role_id FROM admin a JOIN role_admin ra ON a.id = ra.admin_id;
```

---

## ✅ Summary

**Your admin account EXISTS and is ACTIVE** ✅

**To login**, you need to:
1. Reset the password (it's hashed, you can't see it)
2. Use the reset script: `php quick_reset_admin.php`
3. Login with the new password

**Login URL**: `http://roomunite.local/admin/login`




