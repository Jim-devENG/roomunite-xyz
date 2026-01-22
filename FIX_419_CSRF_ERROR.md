# Fix 419 PAGE EXPIRED (CSRF Token Error)

## Problem
Getting "419 PAGE EXPIRED" error when trying to log in to admin panel at `roomunite.local/admin/authenticate`.

## Root Cause
This is a CSRF (Cross-Site Request Forgery) token mismatch error. Common causes:
1. Session not persisting properly
2. Session cookie domain mismatch
3. Cache issues with compiled views
4. Session driver configuration

## Solutions Applied

### 1. Clear All Caches ✅
```powershell
php artisan view:clear
php artisan cache:clear
php artisan route:clear
php artisan config:clear
```

### 2. Check Session Configuration

Verify your `.env` has proper session settings:
```env
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_DOMAIN=null
```

For `.local` domains, `SESSION_DOMAIN` should be `null` or not set.

---

## Additional Fixes to Try

### Fix 1: Clear Browser Cookies
1. Open browser developer tools (F12)
2. Go to Application/Storage tab
3. Clear cookies for `roomunite.local`
4. Try logging in again

### Fix 2: Check Session Storage Permissions
Ensure `storage/framework/sessions` directory is writable:
```powershell
# Windows (PowerShell as Admin)
icacls "storage\framework\sessions" /grant Users:F /T
```

### Fix 3: Verify Session Driver
Check if sessions are being stored:
```powershell
php artisan tinker
```
Then:
```php
Session::put('test', 'value');
Session::get('test'); // Should return 'value'
```

### Fix 4: Update Login Form (If Needed)
The form already has `{{ csrf_field() }}` which is correct. If still having issues, try:
```blade
@csrf
```
instead of:
```blade
{{ csrf_field() }}
```

### Fix 5: Check APP_KEY
Ensure `.env` has a valid `APP_KEY`:
```env
APP_KEY=base64:your_key_here
```

If missing, generate one:
```powershell
php artisan key:generate
```

---

## Quick Test

1. **Clear browser cache and cookies** for `roomunite.local`
2. **Visit login page**: `http://roomunite.local/admin/login`
3. **Check browser console** (F12) for any JavaScript errors
4. **Try logging in** again

---

## If Still Not Working

### Option 1: Temporarily Disable CSRF for Admin Auth (NOT RECOMMENDED)
Edit `app/app/Http/Middleware/VerifyCsrfToken.php`:
```php
protected $except = [
    '/admin/ajax-icalender-import/*',
    'users/veriff-complete',
    'users/veriff-process',
    'admin/authenticate', // TEMPORARY - Remove after fixing
];
```

**⚠️ WARNING**: Only use this for testing. Remove it after fixing the root cause.

### Option 2: Check Session Files
Check if session files are being created:
```powershell
dir storage\framework\sessions
```

Should see `.php` files being created when you visit the login page.

---

## Status

✅ **Caches cleared** - View, application, route, and config caches cleared.

**Next steps:**
1. Clear browser cookies for `roomunite.local`
2. Try logging in again
3. If still failing, check session storage permissions




