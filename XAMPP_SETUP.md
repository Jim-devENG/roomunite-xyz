# XAMPP Configuration Guide for RoomUnite

## Problem
XAMPP is showing the frontend from a previous project instead of this RoomUnite project.

## Solution Options

### Option 1: Configure Virtual Host (Recommended)

1. **Edit XAMPP Virtual Hosts Configuration**
   - Open: `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
   - Add the following configuration:

```apache
<VirtualHost *:80>
    ServerName roomunite.local
    DocumentRoot "C:/Users/MIKENZY/Documents/Apps/roomunite/public"
    
    <Directory "C:/Users/MIKENZY/Documents/Apps/roomunite/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

2. **Edit Windows Hosts File**
   - Open as Administrator: `C:\Windows\System32\drivers\etc\hosts`
   - Add this line:
   ```
   127.0.0.1    roomunite.local
   ```

3. **Enable Virtual Hosts in Apache**
   - Open: `C:\xampp\apache\conf\httpd.conf`
   - Find and uncomment (remove the #) this line:
   ```apache
   Include conf/extra/httpd-vhosts.conf
   ```

4. **Restart Apache in XAMPP Control Panel**

5. **Access your site at**: `http://roomunite.local`

---

### Option 2: Change XAMPP Document Root

1. **Edit Apache Configuration**
   - Open: `C:\xampp\apache\conf\httpd.conf`
   - Find the line:
   ```apache
   DocumentRoot "C:/xampp/htdocs"
   ```
   - Change it to:
   ```apache
   DocumentRoot "C:/Users/MIKENZY/Documents/Apps/roomunite/public"
   ```
   
   - Also find and change:
   ```apache
   <Directory "C:/xampp/htdocs">
   ```
   - To:
   ```apache
   <Directory "C:/Users/MIKENZY/Documents/Apps/roomunite/public">
   ```

2. **Restart Apache in XAMPP Control Panel**

3. **Access your site at**: `http://localhost`

---

### Option 3: Use Laravel's Built-in Server (Current Setup)

The Laravel development server is already running. You can access it at:
- `http://127.0.0.1:8000`
- `http://localhost:8000`

**To stop the server**: Press `Ctrl+C` in the terminal where it's running.

**To start it again**:
```powershell
cd C:\Users\MIKENZY\Documents\Apps\roomunite
php artisan serve
```

---

## Important Notes

1. **Make sure `.htaccess` exists** in the `public` folder (already created)
2. **Make sure `mod_rewrite` is enabled** in Apache:
   - In `httpd.conf`, find and uncomment:
   ```apache
   LoadModule rewrite_module modules/mod_rewrite.so
   ```
3. **Check Apache Error Log** if issues persist:
   - Location: `C:\xampp\apache\logs\error.log`

---

## Verification

After configuration, you should see the RoomUnite application, not your previous project.

