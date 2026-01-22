# Laravel 500 Error Fix Checklist - XAMPP + Apache

## ⚠️ BEFORE YOU START
1. **Backup these files**:
   - `C:\xampp\apache\conf\httpd.conf`
   - `C:\xampp\php\php.ini`
   - `C:\Windows\System32\drivers\etc\hosts`

2. **Run PowerShell as Administrator** for all commands

---

## ✅ CHECKLIST (Follow in Order)

### [ ] STEP 1: Run Quick Fix Script
```powershell
cd C:\Users\MIKENZY\Documents\Apps\roomunite
.\QUICK_FIX_COMMANDS.ps1
```

**Verification**: Script completes without errors

---

### [ ] STEP 2: Check Apache PHP Version
1. Open browser: `http://localhost/diagnostic.php` (or your Apache URL)
2. Check PHP version shown
3. **If PHP < 8.0**: Fix Apache PHP module (see XAMPP_APACHE_FIX.md Step 1)

**Verification**: PHP version matches CLI (8.2.29)

---

### [ ] STEP 3: Configure Apache Virtual Host
1. Open: `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
2. Copy contents from `APACHE_VHOST_CONFIG.conf`
3. Open: `C:\xampp\apache\conf\httpd.conf`
4. Find and uncomment: `Include conf/extra/httpd-vhosts.conf`
5. Restart Apache

**Verification**:
```powershell
Invoke-WebRequest -Uri http://roomunite.local/diagnostic.php
```

---

### [ ] STEP 4: Enable mod_rewrite
1. Open: `C:\xampp\apache\conf\httpd.conf`
2. Find and uncomment: `LoadModule rewrite_module modules/mod_rewrite.so`
3. Restart Apache

**Verification**: Check Apache error log for "mod_rewrite" on startup

---

### [ ] STEP 5: Configure PHP Error Logging
1. Open: `C:\xampp\php\php.ini` (NOT php.ini-development)
2. Find and set values from `PHP_INI_SETTINGS.ini`
3. Restart Apache

**Verification**:
```powershell
php -i | Select-String -Pattern "error_log|log_errors"
```

---

### [ ] STEP 6: Add Hosts File Entry
1. Open Notepad as Administrator
2. Open: `C:\Windows\System32\drivers\etc\hosts`
3. Add line from `HOSTS_FILE_ENTRY.txt`
4. Save file
5. Run: `ipconfig /flushdns`

**Verification**:
```powershell
ping roomunite.local
# Should resolve to 127.0.0.1
```

---

### [ ] STEP 7: Clear All Laravel Caches
```powershell
cd C:\Users\MIKENZY\Documents\Apps\roomunite
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
Remove-Item bootstrap\cache\*.php -Force -ErrorAction SilentlyContinue
```

**Verification**: No errors from commands

---

### [ ] STEP 8: Test Basic Route
```powershell
Invoke-WebRequest -Uri http://roomunite.local/test-basic -UseBasicParsing
```

**Expected**: JSON response with status "ok"

**If fails**: Check `storage\logs\php-errors.log` and `C:\xampp\apache\logs\error.log`

---

### [ ] STEP 9: Test Homepage
```powershell
Invoke-WebRequest -Uri http://roomunite.local/ -UseBasicParsing
```

**Expected**: HTML response (may be slow, but should return 200)

**If fails**: Check logs and see error messages

---

### [ ] STEP 10: Monitor Error Logs
```powershell
# Watch Apache errors
Get-Content C:\xampp\apache\logs\error.log -Wait -Tail 20

# Watch PHP errors (in another terminal)
Get-Content storage\logs\php-errors.log -Wait -Tail 20

# Watch Laravel errors (in another terminal)
Get-Content storage\logs\laravel.log -Wait -Tail 20
```

**Then make a request** and watch for errors

---

## 🎯 SUCCESS CRITERIA

- [ ] `http://roomunite.local/test-basic` returns JSON with status "ok"
- [ ] `http://roomunite.local/` loads homepage (may be slow)
- [ ] Error logs show actual errors (not empty)
- [ ] PHP version matches CLI version
- [ ] mod_rewrite is enabled
- [ ] Virtual host points to `/public` directory

---

## 🚨 IF STILL FAILING

1. **Check Apache Error Log**: `C:\xampp\apache\logs\error.log`
2. **Check PHP Error Log**: `storage\logs\php-errors.log`
3. **Check Laravel Log**: `storage\logs\laravel.log`
4. **Verify Document Root**: Should be `/public`, not project root
5. **Test with PHP CLI Server** (bypasses Apache):
   ```powershell
   cd public
   php -S 127.0.0.1:8000
   # Access: http://127.0.0.1:8000
   ```

---

## 📋 QUICK REFERENCE

**Access URLs**:
- Virtual host: `http://roomunite.local/`
- Diagnostic: `http://roomunite.local/diagnostic.php`
- Test route: `http://roomunite.local/test-basic`

**Key Files**:
- Apache config: `C:\xampp\apache\conf\httpd.conf`
- Virtual hosts: `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
- PHP config: `C:\xampp\php\php.ini`
- Hosts file: `C:\Windows\System32\drivers\etc\hosts`

**Log Files**:
- Apache: `C:\xampp\apache\logs\error.log`
- PHP: `storage\logs\php-errors.log`
- Laravel: `storage\logs\laravel.log`




