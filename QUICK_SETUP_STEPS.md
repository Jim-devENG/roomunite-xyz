# Quick XAMPP Setup Steps for RoomUnite

## ✅ What's Already Done:
1. ✓ Virtual host configuration added to XAMPP
2. ✓ mod_rewrite is enabled
3. ✓ Virtual hosts are enabled in Apache

## 🔧 What You Need to Do:

### Step 1: Add to Windows Hosts File (REQUIRES ADMIN)

**Option A - Using Notepad (Easiest):**
1. Press `Windows Key + R`
2. Type: `notepad C:\Windows\System32\drivers\etc\hosts`
3. Press `Ctrl+Shift+Enter` (this runs as Administrator)
4. Click "Yes" when prompted
5. Add this line at the end of the file:
   ```
   127.0.0.1    roomunite.local
   ```
6. Save (Ctrl+S) and close

**Option B - Using PowerShell (As Administrator):**
1. Right-click PowerShell → "Run as Administrator"
2. Run this command:
   ```powershell
   Add-Content -Path "C:\Windows\System32\drivers\etc\hosts" -Value "`n127.0.0.1    roomunite.local"
   ```

### Step 2: Restart Apache in XAMPP
1. Open XAMPP Control Panel
2. Click "Stop" next to Apache (if it's running)
3. Wait a few seconds
4. Click "Start" next to Apache
5. Make sure it shows "Running" in green

### Step 3: Test Your Site
1. Open your browser
2. Go to: `http://roomunite.local`
3. You should now see your RoomUnite application!

## 🐛 Troubleshooting:

**If you get "This site can't be reached":**
- Make sure Apache is running in XAMPP
- Check that you added the hosts file entry correctly
- Try: `http://127.0.0.1` (should show XAMPP dashboard or your old project)

**If you still see your old project:**
- Make sure you're accessing `http://roomunite.local` (not localhost)
- Clear your browser cache (Ctrl+Shift+Delete)
- Check Apache error log: `C:\xampp\apache\logs\error.log`

**If Apache won't start:**
- Check the error log: `C:\xampp\apache\logs\error.log`
- Make sure port 80 is not in use by another application
- Try changing the port in XAMPP if needed

## 📝 Notes:
- The virtual host points to: `C:/Users/MIKENZY/Documents/Apps/roomunite/public`
- Your old project will still be accessible at `http://localhost` (if it's in htdocs)
- This setup allows you to run multiple projects simultaneously

