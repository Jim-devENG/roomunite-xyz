# Manual Steps Required - Complete These

## ⚠️ CRITICAL: These steps require manual intervention

Some steps cannot be automated and require you to manually edit configuration files.

---

## STEP 1: Enable Virtual Hosts in Apache

**File**: `C:\xampp\apache\conf\httpd.conf`

**Action**: Find this line (around line 500-600):
```apache
# Virtual hosts
#Include conf/extra/httpd-vhosts.conf
```

**Change to**:
```apache
# Virtual hosts
Include conf/extra/httpd-vhosts.conf
```

**Save** and restart Apache.

---

## STEP 2: Enable mod_rewrite

**File**: `C:\xampp\apache\conf\httpd.conf`

**Action**: Find this line (around line 150-200):
```apache
#LoadModule rewrite_module modules/mod_rewrite.so
```

**Change to**:
```apache
LoadModule rewrite_module modules/mod_rewrite.so
```

**Save** and restart Apache.

---

## STEP 3: Configure PHP Error Logging

**File**: `C:\xampp\php\php.ini`

**Find and change these lines**:

```ini
; Error logging
log_errors = On
error_log = "C:/Users/MIKENZY/Documents/Apps/roomunite/storage/logs/php-errors.log"
display_errors = On
display_startup_errors = On
error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT
```

**Save** and restart Apache.

---

## STEP 4: Add Hosts File Entry

**Run PowerShell as Administrator**, then execute:

```powershell
Add-Content -Path "C:\Windows\System32\drivers\etc\hosts" -Value "127.0.0.1    roomunite.local"
ipconfig /flushdns
```

**Or manually edit**: `C:\Windows\System32\drivers\etc\hosts`

Add this line:
```
127.0.0.1    roomunite.local
```

Then run: `ipconfig /flushdns`

---

## STEP 5: Restart Apache

**After making all changes**:

1. Open XAMPP Control Panel
2. Stop Apache
3. Start Apache
4. Check for errors in: `C:\xampp\apache\logs\error.log`

---

## STEP 6: Verify Configuration

**Test virtual host**:
```powershell
Invoke-WebRequest -Uri http://roomunite.local/diagnostic.php
```

**Test basic route**:
```powershell
Invoke-WebRequest -Uri http://roomunite.local/test-basic
```

**Expected**: JSON response with status "ok"

---

## ✅ Checklist

- [ ] Virtual hosts enabled in httpd.conf
- [ ] mod_rewrite enabled in httpd.conf
- [ ] PHP error logging configured in php.ini
- [ ] Hosts file entry added
- [ ] Apache restarted
- [ ] Test routes working

---

## 🚨 If Apache Won't Start

1. Check Apache error log: `C:\xampp\apache\logs\error.log`
2. Verify syntax: `C:\xampp\apache\bin\httpd.exe -t`
3. Check if port 80 is in use: `netstat -ano | findstr :80`




